# Laravel 13 Patterns & Architecture Decisions

Dokumen ini berisi keputusan arsitektur dan pola penulisan yang telah disepakati di codebase ini. Wajib diikuti untuk semua fitur baru.

---

## 1. Service Injection Pattern

**Masalah:** Pemanggilan `app(Service::class)` inline berulang kali menyulitkan testing, boros instansiasi, dan tidak konsisten.

**Pola:** Gunakan salah satu dari dua pola berikut:

### 1a. Lazy Getter (Default)

Untuk service yang dipakai oleh **satu atau beberapa** method saja.

```php
// ✅ BENAR — lazy getter
protected ?OrderService $orderService = null;

protected function orderService(): OrderService
{
    return $this->orderService ??= app(OrderService::class);
}

// Usage
$this->orderService()->processOrder($orderData, $items);
```

```php
// ❌ SALAH — inline app() call
app(OrderService::class)->processOrder($orderData, $items);
```

### 1b. Constructor Property Promotion

Untuk service yang dipakai oleh **hampir semua** method di kelas tersebut.

```php
// ✅ BENAR — constructor injection untuk service yang dipakai luas
public function __construct(
    protected readonly TenantWalletService $walletService
) {}

// Usage
$this->walletService->deductBalance(...);
```

### Aturan Blade

**JANGAN** pernah resolve service di Blade template:

```blade
{{-- ❌ SALAH — Blade tidak boleh resolve service --}}
{{ app(\App\Tenant\Services\SettingService::class)->get('trx_fee') }}

{{-- ✅ BENAR — data disiapkan oleh controller/layout/component --}}
{{ $applicationFeeAmount }}
```

Data yang dibutuhkan Blade harus disiapkan di layer atas (Controller, Livewire Component, `@php` block di layout).

---

## 2. Thin Controller Pattern

Controller/Livewire hanya bertanggung jawab untuk:
- Handling request / event
- Authorization
- Mapping input ke DTO
- Memanggil Service
- Mengembalikan response / dispatch event

**BUKAN** untuk business logic:

```php
// ✅ BENAR — controller tipis, logic di service
public function store(OrderRequest $request)
{
    $order = $this->orderService()->processOrder($request->validated(), $request->items);
    return redirect()->route('order.show', $order->invoice_code);
}
```

---

## 3. DTO Pattern

Jika method service membutuhkan **>3 parameter**, gunakan Data Transfer Object (`Spatie\LaravelData\Data`):

```php
use Spatie\LaravelData\Data;

readonly class ProductFilterData extends Data
{
    public function __construct(
        public string $search = '',
        public string $filterCategory = '',
        public string $sortField = 'newest',
        public int $perPage = 20,
    ) {}
}
```

---

## 4. Caching Pattern

**Jangan pernah** menyimpan instance Eloquent Model langsung ke cache. Driver `database` / `redis` di Laravel 13 menggunakan `allowed_classes: false` untuk mencegah deserialization attack.

```php
// ❌ SALAH — menyimpan object, TypeError saat unserialize
Cache::rememberForever('key', fn () => Model::first());

// ✅ BENAR — simpan array, reconstruct setelah load
$attrs = Cache::rememberForever('key', fn () => Model::first()?->getAttributes());
return $attrs ? (new Model)->forceFill($attrs)->syncOriginal() : null;
```

---

## 5. Service Method Return Types

Service methods harus return **typed result** (Model, void, collection). Response handling (toasts, session flash, dispatch event) milik Controller/Livewire layer.

```php
// ✅ BENAR
public function toggleAvailability(Product $product): void
{
    $product->update(['is_active' => !$product->is_active]);
}

// ❌ SALAH — controller concern di service
public function toggleAvailability(Product $product): void
{
    $product->update(...);
    $this->dispatch('notify', ...);
}
```

---

## 6. Service Location

| Layer | Path | Contoh |
|-------|------|--------|
| Central | `app/Central/Services/` | `BillingService.php` |
| Tenant | `app/Tenant/Services/` | `OrderService.php` |
| Central DTO | `app/Central/Data/` | `RegisterTenantInputData.php` |
| Tenant DTO | `app/Tenant/Data/` | `CategoryData.php` |

---

## Referensi

- [ADR Service Pattern](../../decisions/001-service-dto-pattern.md) — Detail DTO & service standard
- [ADR Service Injection](../../decisions/0002-service-injection-guidelines.md) — Constructor vs lazy getter
- [Standards Livewire](../livewire4/PATTERNS.md) — Zero-roundtrip, dirty indicator, dll
