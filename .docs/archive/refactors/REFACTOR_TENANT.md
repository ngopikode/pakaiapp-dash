# Clean Architecture Refactoring Plan: `app/Tenant`

> **Prinsip**: Controller hanya boleh: Parse Input → Validate → Delegate ke Service → Return Response.
> Controller DILARANG berisi: query DB kompleks, kalkulasi bisnis, loop panjang, `new SomeService()` langsung.

---

## Status Audit

| File | Severity | Action |
|---|---|---|
| `Api/OrderApiController.php` | 🟡 Minor | Pindah logic ke Service, fix instantiation |
| `Api/DuitkuCallbackController.php` | 🟠 Major | Buat Middleware, lazy init, hapus duplikasi |
| `Api/DuitkuApiController.php` | ✅ Clean | Tidak perlu diubah |
| `Api/CategoryApiController.php` | ✅ Clean | Tidak perlu diubah |
| `Api/ProductApiController.php` | ✅ Clean | Tidak perlu diubah |
| `Api/OrderHistoryApiController.php` | ✅ Clean | Tidak perlu diubah |
| `Api/RestaurantApiController.php` | ✅ Clean | Tidak perlu diubah |
| `Web/HomeController.php` | ✅ Clean | Tidak perlu diubah |
| `Web/MenuController.php` | ✅ Clean | Tidak perlu diubah |

---

## Prioritas 1 — `DuitkuCallbackController.php` (🟠 Major)

### Masalah

| Baris | Pelanggaran | Jenis |
|---|---|---|
| 45–58 | Logika IP whitelist ada di dalam method `callback()` | ❌ Harusnya Middleware |
| 61, 204, 242 | `new DuitkuService()` tiga kali | ❌ Anti-pattern, harusnya lazy init |
| 263–273 | `mapPaymentMethod()` — mapping kode Duitku ke enum DB | ⚠️ Harusnya di `DuitkuService` |
| 235–258 | Method `paymentMethods()` — duplikasi dari `DuitkuApiController` | ❌ Duplikasi |

### Perubahan

#### [NEW] `app/Tenant/Http/Middleware/DuitkuIpWhitelist.php`
Pindah logika IP whitelist dari controller ke Middleware yang proper.
```php
// Contoh struktur
class DuitkuIpWhitelist
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (!config('duitku.ip_whitelist_enabled', false)) {
            return $next($request);
        }
        // cek IP, return 403 jika tidak terdaftar
    }
}
```

#### [MODIFY] `DuitkuCallbackController.php`
- Tambah lazy init property untuk `DuitkuService`
- Hapus method `paymentMethods()` (sudah ada di `DuitkuApiController`)
- Pindah `mapPaymentMethod()` ke `DuitkuService::mapPaymentMethodCode(string $code): string`
- Hapus import-import yang tidak lagi dipakai

#### [MODIFY] `app/Central/Services/DuitkuService.php`
- Tambah method `mapPaymentMethodCode(string $paymentCode): string`

#### [MODIFY] `routes/tenant.php`
- Register middleware `DuitkuIpWhitelist` pada route callback Duitku

---

## Prioritas 2 — `OrderApiController.php` (🟡 Minor)

### Masalah

| Baris | Pelanggaran | Jenis |
|---|---|---|
| 116–131 | `checkProductAvailability()` — query DB langsung di controller | ⚠️ Harusnya di `OrderService` |
| 193–210 | `buildCustomerDetail()` — query `TenantUser::first()` di controller | ⚠️ Harusnya di `OrderService` |
| 217 | `new MidtransService()` | ❌ Anti-pattern, harusnya lazy init |
| 234 | `new DuitkuService()` | ❌ Anti-pattern, harusnya lazy init |

### Perubahan

#### [MODIFY] `OrderApiController.php`
- Ganti `new MidtransService()` dan `new DuitkuService()` dengan lazy init sesuai AGENTS.md
- `checkProductAvailability()` dipanggil via `OrderService::checkAvailability(array $items)`
- `buildCustomerDetail()` dipanggil via `OrderService::buildCustomerDetail(Request $request)`

#### [MODIFY] `app/Tenant/Services/OrderService.php`
- Tambah method `checkAvailability(array $items): ?array` — pindahkan logic dari controller
- Tambah method `buildCustomerDetail(string $name, ?string $email, ?string $phone): array` — pindahkan logic dari controller

---

## Verifikasi

```bash
php -l app/Tenant/Controllers/Api/DuitkuCallbackController.php
php -l app/Tenant/Controllers/Api/OrderApiController.php
php artisan route:list | grep tenant
```
