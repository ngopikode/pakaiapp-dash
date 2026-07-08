# Clean Architecture Refactoring Plan: `app/Central`

> **Prinsip**: Controller hanya boleh: Parse Input → Validate → Delegate ke Service → Return Response.
> Controller DILARANG berisi: query DB kompleks, kalkulasi bisnis, pengiriman email, `Artisan::call()`, hash password, loop panjang.

---

## Status Audit

| File | Severity | Action |
|---|---|---|
| `AuthController.php` | 🔴 Critical | Refactor besar — ekstrak ke Service baru |
| `DuitkuController.php` | 🟡 Minor | Pindah utility method ke Service, bersihkan dead import |
| `MidtransController.php` | 🟡 Minor | Hapus dead imports |
| `ArticleController.php` | ✅ Clean | Tidak perlu diubah |

---

## Prioritas 1 — `AuthController.php` (🔴 Critical)

### Masalah

| Baris | Pelanggaran | Jenis |
|---|---|---|
| 215–265 | 5 lapis validasi anti-abuse free trial (IP, cookie, disposable email, Gmail normalization, WA uniqueness) — semua di controller | ❌ Business Logic |
| 304–365 | `Artisan::call('tenant:create')`, buat `User`, hash password, kirim email sukses/gagal | ❌ Workflow/Orchestration — duplikasi dari `TenantRegistrationService` |
| 396–417 | Buat Midtrans snap token + kirim email invoice | ❌ Business Logic |
| 421–446 | Buat Duitku invoice + kirim email invoice | ❌ Business Logic |
| 368–391 | Bangun URL WhatsApp + kirim email manual | ❌ Business Logic |
| 449–459 | `normalizeEmail()` — utility function langsung di controller | ❌ Harusnya di Service/Helper |

**Root cause**: Tidak ada `RegistrationService` — semua flow dari free plan, Midtrans, Duitku, dan manual dijejalkan ke satu controller 461 baris.

### Perubahan

#### [NEW] `app/Central/Services/RegistrationAbuseGuard.php`
Merangkum 5 lapis validasi anti-abuse menjadi satu service yang fokus.
```php
class RegistrationAbuseGuard
{
    // Mengembalikan pesan error (string) jika abuse terdeteksi, atau null jika aman
    public function check(Request $request, string $email, string $whatsapp): ?string
    {
        // Layer 1: IP Rate Limiting
        // Layer 2: Cookie Fingerprint
        // Layer 3: Disposable Email
        // Layer 4: Gmail Alias Normalization
        // Layer 5: WhatsApp Uniqueness
    }

    private function normalizeEmail(string $email): string { ... }
}
```

#### [MODIFY] `app/Central/Services/TenantRegistrationService.php`
Tambah method untuk menangani free plan registration (saat ini hanya handle paid via webhook):
```php
// Method baru — free plan langsung aktif tanpa webhook
public function completeFreePlanRegistration(TenantRegistration $registration): array
{
    // Artisan::call, buat User, hash password, kirim email — logic yang sekarang ada di controller
    // Return: ['redirect_url' => ..., 'auto_login_token' => ...]
}
```
Tambah juga method untuk alur billing email:
```php
public function sendBillingEmail(TenantRegistration $registration, string $paymentMethod, mixed $paymentData): void
{
    // Kirim email invoice untuk manual, midtrans, atau duitku
}
```

#### [MODIFY] `AuthController.php`
Setelah ekstraksi, controller `registerTenant()` cukup menjadi:
```php
public function registerTenant(Request $request): JsonResponse
{
    $validated = $request->validate([...]);

    // 1. Guard: cek email verified
    // 2. Guard: cek free plan abuse (jika paket free) → delegasi ke RegistrationAbuseGuard
    // 3. Guard: cek slug sudah terpakai
    // 4. Buat TenantRegistration
    // 5. Proses sesuai plan & payment method → delegasi ke TenantRegistrationService
    // 6. Return response
}
```

---

## Prioritas 2 — `DuitkuController.php` (🟡 Minor)

### Masalah

| Baris | Pelanggaran | Jenis |
|---|---|---|
| 69–74 | `abort(403)` di `__construct()` — abort di constructor itu tidak idiomatis | ⚠️ Harusnya Middleware |
| `parseMerchantOrderId()` | Method parsing format string merchant order ID | ⚠️ Utility logic — harusnya di `DuitkuService` |
| Import `Artisan`, `Hash`, `Mail`, dll. | Import yang sudah tidak terpakai setelah refactoring sebelumnya | ❌ Dead imports |

### Perubahan

#### [NEW] `app/Central/Http/Middleware/DuitkuEnabled.php`
```php
class DuitkuEnabled
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (!config('duitku.enabled')) {
            abort(ResponseAlias::HTTP_FORBIDDEN, 'Duitku payment gateway is disabled.');
        }
        return $next($request);
    }
}
```

#### [MODIFY] `DuitkuController.php`
- Hapus `__construct()` yang hanya berisi `abort()`
- Pindah `parseMerchantOrderId()` ke `DuitkuService::parseMerchantOrderId(string $raw): array`
- Bersihkan dead imports: `Artisan`, `Hash`, `Mail`, `SystemEmail`, `User`, `Tenant`

#### [MODIFY] `app/Central/Services/DuitkuService.php`
- Tambah method `parseMerchantOrderId(string $merchantOrderId): array`

#### [MODIFY] `routes/central.php` / `routes/web.php`
- Register middleware `DuitkuEnabled` pada route group Duitku

---

## Prioritas 3 — `MidtransController.php` (🟡 Minor)

### Masalah

| Pelanggaran | Jenis |
|---|---|
| Import `Artisan`, `Hash`, `Mail`, `SystemEmail`, `User`, `Tenant` masih ada setelah refactoring | ❌ Dead imports |

### Perubahan

#### [MODIFY] `MidtransController.php`
- Hapus semua import yang sudah tidak terpakai

---

## Verifikasi

```bash
php -l app/Central/Controllers/AuthController.php
php -l app/Central/Controllers/DuitkuController.php
php -l app/Central/Controllers/MidtransController.php
php artisan route:list
```
