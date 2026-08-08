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

Gunakan Data Transfer Object (`Spatie\LaravelData\Data`) di dua skenario:

### 3a. Service method dengan parameter >3

```php
// ❌ SALAH — parameter >3 tanpa DTO
public function processOrder($customerName, $tableNumber, $orderType, $items, $isTaxActive) {}

// ✅ BENAR — pake DTO
public function processOrder(CreateOrderData $data): Order
```

### 3b. Livewire → Service boundary

Setiap data yang dikirim dari Livewire component ke Service layer HARUS pake DTO, tidak peduli jumlah parameter.

```php
// ✅ BENAR — 1 parameter pun pake DTO karena ini boundary layer
$dto = ProductFormData::from($this->all());
$this->productService()->saveFromForm($existingProduct, $dto);
```

Aturan ini menjaga **layer isolation**: Service tidak tahu dari mana data berasal (Livewire, API, CLI). DTO adalah kontrak eksplisit antar layer.

### Lokasi DTO

| Domain | Path | Contoh |
|--------|------|--------|
| Tenant | `app/Tenant/Data/` | `ProductFormData.php`, `ProductFilterData.php` |
| Central | `app/Central/Data/` | `RegisterTenantInputData.php` |

### Contoh DTO

```php
namespace App\Tenant\Data;

use Spatie\LaravelData\Data;

class ProductFilterData extends Data
{
    public function __construct(
        public string $search = '',
        public string $filterCategory = '',
        public string $filterStatus = '',
        public string $filterPrice = '',
        public string $sortField = 'newest',
        public int $perPage = 20,
    ) {}
}
```

> Untuk DTO kompleks dengan nested data (seperti array of objects), lihat [Spatie Laravel Data docs](https://spatie.be/docs/laravel-data/v4/working-with-nested-data).

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

## 7. DB Transaction Pattern

Jangan gunakan closure `DB::transaction(fn() => ...)`. Gunakan `try/catch` dengan manual `beginTransaction/commit/rollBack`.

**Alasan:** Closure `DB::transaction()` tidak memberikan kontrol eksplisit — error handling, conditional rollback, atau lock management (`lockForUpdate()`) jadi tidak bisa diatur dengan baik.

```php
// ❌ SALAH — closure, kontrol terbatas
DB::transaction(function () use ($data) {
    $product = Product::create($data);
    $this->syncVariants($product, $data);
});

// ✅ BENAR — try/catch manual, reusable, lock bisa dipasang
try {
    DB::beginTransaction();

    $order = Order::lockForUpdate()->find($orderId);
    if (!$order) throw new Exception('Pesanan tidak ditemukan');

    $order->update(['status' => $toStatus]);

    DB::commit();
} catch (Throwable $e) {
    DB::rollBack();
    throw $e;
}
```

Referensi implementasi: `app/Tenant/Services/KitchenService.php`.

---

## 8. Single-line If / Loop Statement

Jika blok `if`, `foreach`, `for` hanya memiliki **satu baris eksekusi**, jangan gunakan kurung kurawal `{}`.

```php
// ❌ SALAH — braces tidak perlu untuk satu baris
if ($product->is_active) {
    $product->update(['is_active' => false]);
}

// ✅ BENAR — satu baris, no braces
if (!$order) throw new Exception('Pesanan tidak ditemukan.');
```

Aturan ini berlaku untuk semua kode PHP di project ini.

---

## 9. Array Validation Syntax

Selalu gunakan array syntax `['required', 'string']` untuk validasi Laravel, bukan pipe syntax string `'required|string'`.

```php
// ❌ SALAH — pakai pipe (|) string
$request->validate([
    'name' => 'required|string|max:255',
    'email' => 'required|email|unique:users',
]);

// ✅ BENAR — pakai array
$request->validate([
    'name' => ['required', 'string', 'max:255'],
    'email' => ['required', 'email', 'unique:users'],
]);
```
Ini mempermudah penambahan rule object custom (seperti `['required', new CustomRule()]`) ke depannya dan menghindari kesalahan parsing saat rule berisi regex yang menggunakan karakter `|`.

---

## 10. Domain Constants Pattern

Jangan pakai string literal berulang untuk nilai domain yang terbatas seperti tipe, status, dan kategori internal. Definisikan constant di model yang memiliki field tersebut, lalu pakai constant itu di migration, service, controller, dan Livewire.

```php
// ✅ BENAR — single source of truth di model pemilik field
class Wallet extends Model
{
    public const TYPE_BILLING = 'billing';
    public const TYPE_CASH = 'cash';
    public const TYPE_DIGITAL = 'digital';
}

TenantWalletService::getWallet(Wallet::TYPE_BILLING);
```

```php
// ❌ SALAH — raw string rawan typo dan susah refactor
TenantWalletService::getWallet('billing');
```

Jika constant dipakai di migration, import model terkait agar enum/default/backfill tetap konsisten:

```php
use App\Tenant\Models\Core\Wallet;

$table->enum('type', [Wallet::TYPE_BILLING, Wallet::TYPE_CASH, Wallet::TYPE_DIGITAL])
    ->default(Wallet::TYPE_BILLING);
```

## 11. Named Arguments Pattern

Gunakan Named Arguments (fitur bawaan PHP 8) secara eksplisit untuk fungsi internal atau public yang memiliki lebih dari dua parameter untuk meningkatkan keterbacaan, terutama pada argumen bertipe `boolean`, `array`, atau `null`.

```php
// ✅ BENAR — jelas parameter mana yang dikirim
$this->aggregateStockAdjustments(
    variant: $variant,
    quantity: $recalculatedItem['quantity'],
    variantAdjustments: $variantAdjustments,
    rawMaterialAdjustments: $rawMaterialAdjustments
);

$this->executeStockAdjustments(
    variantAdjustments: $variantAdjustments,
    rawMaterialAdjustments: $rawMaterialAdjustments,
    operation: self::OPERATION_INCREMENT
);
```

```php
// ❌ SALAH — susah ditebak jika melihat pemanggilannya dari luar
$this->aggregateStockAdjustments($variant, $recalculatedItem['quantity'], $variantAdjustments, $rawMaterialAdjustments);
```

---

## 12. Asset URL Tenant — Selalu Gunakan `Storage::url()`

**JANGAN PERNAH** hardcode path asset tenant di Blade menggunakan format `/tenant_{{ tenant('id') }}/...` atau apapun yang bergantung pada `suffix_base` konfigurasi Tenancy secara manual.

**Alasan:** Konfigurasi `suffix_base` di `config/tenancy.php` bisa berubah (contoh: dari `tenant_` menjadi `tenants/`). Jika path di-hardcode di Blade, seluruh gambar akan langsung 404 tanpa ada error yang jelas.

```blade
{{-- ❌ SALAH — hardcode suffix_base, langsung 404 jika config berubah --}}
<img src="/tenant_{{ tenant('id') }}/{{ $logo }}">

{{-- ✅ BENAR — dinamis mengikuti konfigurasi Storage yang aktif --}}
<img src="{{ Storage::url($logo) }}">
```

---

## Referensi

- [ADR Service Pattern](../../decisions/001-service-dto-pattern.md) — Detail DTO & service standard
- [ADR Service Injection](../../decisions/0002-service-injection-guidelines.md) — Constructor vs lazy getter
- [Standards Livewire](../livewire4/PATTERNS.md) — Zero-roundtrip, dirty indicator, dll
