# Fix: Service Injection — Ganti Inline `app()` ke Pola Standar

> Referensi: [Laravel 13 PATTERNS](../../references/laravel13/PATTERNS.md) · [ADR-0002](../../decisions/0002-service-injection-guidelines.md)

## Masalah

Fitur Application Fee Pass-Through menggunakan `app(SettingService::class)` inline di beberapa tempat, melanggar ADR-0002 dan PATTERNS.md yang melarang keras inline `app()` di method maupun blade.

## File Bermasalah & Fix

### 1. `app/Tenant/Services/OrderService.php`

**Masalah:** `calculateTaxesAndTotal()` memanggil `app(SettingService::class)->get('default_trx_fee', tenant())` di dalam method private.

**Fix:** Tambah lazy getter `settingService()` + panggil dari `processOrder()`, bukan dari `calculateTaxesAndTotal()`.

### 2. `app/Tenant/Controllers/Api/RestaurantApiController.php`

**Masalah:** `app(SettingService::class)` inline di response method.

**Fix:** Constructor inject `SettingService`, panggil `$this->settingService->get()`.

### 3. `resources/views/layouts/store.blade.php`

**Masalah:** `app(\App\Tenant\Services\SettingService::class)->get(...)` inline di template.

**Fix:** Hitung `$applicationFeeAmount` di `@php` block atau di controller yang pass `$setting`. Letakkan di scope yang sama dengan tempat `$setting` di-load.

### 4. `resources/views/pages/tenant/store/resto/product.blade.php`

**Masalah:** Sama — `app(...)` inline di template.

**Fix:** Sama — pass via controller atau `@php` block.
