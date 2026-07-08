# Migrasi `spatie/laravel-data` — Domain Central

Menggantikan inline `$request->validate()` di Controller dan `array` return dari Service
dengan typed **Data Objects** yang IDE-friendly, type-safe, dan konsisten.

---

## Keputusan Desain (Industry Standard)

| Keputusan | Pilihan | Alasan |
|---|---|---|
| Struktur folder | `app/Central/Data/` (flat) | Konvensi resmi Spatie & ekosistem Laravel |
| Cookie placement | Tetap di Controller | Service layer tidak boleh tahu soal HTTP — itu urusan HTTP layer |

---

## Kondisi Saat Ini

- Tidak ada FormRequest. Semua validasi inline di dalam method Controller.
- Service return `array` polosan. Controller harus tahu magic key-nya (`$result['type']`, `$result['data']`).
- `spatie/laravel-data` belum terinstall.

---

## Proposed Changes

### Step 1 — Install Dependency

```bash
composer require spatie/laravel-data
```

---

### Step 2 — [NEW] Input Data Objects

**Lokasi:** `app/Central/Data/`

Menggantikan `$request->validate([...])` di setiap method Controller.
Spatie otomatis resolve dari Request + validasi + throw 422 jika gagal.

#### [NEW] `CentralLoginInputData.php`
Menggantikan `validate(['login_input' => 'required|string|max:255'])` di `AuthController::centralLogin()`

#### [NEW] `RequestOtpInputData.php`
Menggantikan `validate(['email' => 'required|email'])` di `AuthController::requestOtp()`

#### [NEW] `VerifyOtpInputData.php`
Menggantikan `validate(['email' => 'required|email', 'otp' => 'required|digits:6'])` di `AuthController::verifyOtp()`

#### [NEW] `RegisterTenantInputData.php`
Menggantikan `validate([jenisBisnis, namaToko, namaOwner, noWa, email, paket, payment_method])` di `AuthController::registerTenant()`

---

### Step 3 — [NEW] Output Data Objects

**Lokasi:** `app/Central/Data/`

Menggantikan `array` return dari Service.

#### [NEW] `RegisterStatusData.php`
Fields: paymentStatus, redirectUrl, paymentUrl

#### [NEW] `CentralLoginResultData.php`
Fields: type ('email' | 'subdomain'), stores (Collection|null), redirectUrl (string|null)

#### [NEW] `RegistrationResultData.php`
Fields: type ('free'|'manual'|'midtrans'|'duitku'), message, redirectUrl, paymentUrl, snapToken, invoiceCode
Note: cookie TIDAK masuk sini — dihandle Controller

---

### Step 4 — [MODIFY] `TenantRegistrationService.php`

| Method | Return Sebelum | Return Sesudah |
|---|---|---|
| `getRegisterStatus()` | `array` | `RegisterStatusData` |
| `processCentralLogin()` | `array` | `CentralLoginResultData` |
| `initiateRegistration()` | `array` | `RegistrationResultData` |

---

### Step 5 — [MODIFY] `AuthController.php`

Hapus semua `$request->validate([...])` — validasi pindah ke parameter Input Data di method signature.

```php
// SEBELUM
public function centralLogin(Request $request): JsonResponse
{
    $request->validate(['login_input' => 'required|string|max:255']);
    $data = $this->service()->processCentralLogin(trim($request->login_input));
    return $this->successResponse(data: $data);
}

// SESUDAH
public function centralLogin(CentralLoginInputData $input): JsonResponse
{
    $result = $this->tenantRegistrationService()->processCentralLogin(trim($input->login_input));
    return $this->successResponse(data: $result);
}
```

Cookie free trial tetap diset di Controller:
```php
$result = $this->tenantRegistrationService()->initiateRegistration($input, $request->ip(), $hasCookie);
$response = $this->successResponse(data: $result, message: $result->message);
if ($result->type === 'free')
    $response->withCookie(cookie()->forever('pakaiapp_free_trial_claimed', '1'));
return $response;
```

---

## Verification Plan

```bash
php -l app/Central/Controllers/AuthController.php
php -l app/Central/Services/TenantRegistrationService.php
```

Manual:
- Flow register free → cookie + redirect_url
- Flow register midtrans → snap_token
- Flow register duitku → payment_url
- Request tanpa field wajib → 422 dari Spatie
- IDE auto-complete `$result->redirectUrl` harus jalan
