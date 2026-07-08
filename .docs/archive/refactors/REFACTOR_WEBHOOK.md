# Refactor Webhook Payment Gateway — Domain Central

Memindahkan seluruh *business logic* dari `MidtransController` dan `DuitkuController`
ke dalam Service layer, sehingga Controller murni menjadi HTTP adapter.

---

## Analisis Kondisi Saat Ini

### MidtransController (264 baris)
Masalah yang ditemukan:
- **Business logic di Controller**: Signature verification, status mapping (`capture/settlement` → `paid`), fraud check (underpaid), stock restore, fee deduction — semua ada di Controller.
- **Duplikasi kode**: Blok `INV-REG-` handling (baris 68–97) dan blok `REG` backward-compat (baris 108–139) punya alur yang hampir identik — bedanya hanya cara lookup `TenantRegistration`.
- **God switch via if-elseif**: Status mapping `transaction_status` → internal status diulang manual dua kali (di reg block dan order block).
- **Config::$serverKey di Controller**: Konfigurasi Midtrans diset ulang di Controller, padahal sudah ada di `MidtransService::__construct()`.

### DuitkuController (418 baris)
Masalah yang ditemukan:
- **`processCallback()` tetap ada di Controller**: Method private 100+ baris yang memuat full business logic (DB transaction, fraud check, wallet credit, fee deduction) seharusnya ada di Service.
- **Model diakses langsung dari Controller**: `TenantRegistration::find()`, `Order::where()` langsung di Controller.
- **`getPaymentMethods` punya inline validation**: `$request->validate(['amount' => ...])` masih di Controller — candidate untuk Input Data Object.

---

## Keputusan Desain

| Keputusan | Pilihan | Alasan |
|---|---|---|
| Duplikasi `INV-REG-` vs `REG` block (Midtrans) | Gabung ke satu method Service `handleRegistrationWebhook()` | Kedua blok identik logikanya — hanya lookup berbeda |
| `processCallback()` (Duitku) | Pindah ke `DuitkuService::processOrderCallback()` | Controller tidak boleh pegang business logic & DB transaction |
| Input Data Object untuk `getPaymentMethods` | `GetPaymentMethodsInputData` | Konsisten dengan pola baru AuthController |
| Status mapping Midtrans | Ekstrak ke `MidtransService::resolveTransactionStatus()` | Reusable, testable, dan hilangkan if-elseif chain dari Controller |

---

## Proposed Changes

### Step 1 — [NEW] Input Data Object

#### [NEW] `app/Central/Data/GetPaymentMethodsInputData.php`
Menggantikan `$request->validate(['amount' => 'required|numeric|min:1'])` di `DuitkuController::getPaymentMethods()`

---

### Step 2 — [MODIFY] `MidtransService.php`

#### [NEW] `handleRegistrationWebhook(TenantRegistration $reg, string $transactionStatus): void`
Memindahkan dan menggabungkan logika dari dua blok duplikat di Controller:
- Block `INV-REG-` (L68–97)
- Block `REG` backward-compat (L108–139)
Keduanya punya alur yang sama: cek idempotency, mapping status, update DB, dan panggil `completeRegistration()`.

#### [NEW] `resolveRegistrationStatus(string $transactionStatus): string`
Mengekstrak if-elseif status mapping (`capture/settlement → paid`, `deny/expire/cancel → failed`) ke method yang reusable dan testable.

#### [NEW] `resolveOrderStatus(string $transactionStatus, string $paymentType, string $fraudStatus): string`
Mengekstrak blok status mapping yang lebih kompleks (termasuk credit card + fraud check) dari Controller ke Service.

---

### Step 3 — [MODIFY] `DuitkuService.php`

#### [NEW] `processOrderCallback(string $invoiceCode, array $notif): void`
Memindahkan seluruh isi `DuitkuController::processCallback()` ke Service:
- Validasi format `invoiceCode`
- `DB::transaction()`
- Idempotency guard
- Fraud check (underpaid)
- `$order->update()`
- Kredit wallet (`TenantWalletService`)
- Potong fee (`BillingService`)

---

### Step 4 — [MODIFY] `MidtransController.php`

Setelah logic dipindah ke Service, Controller menjadi:
```php
public function notification(Request $request)
{
    Log::info('[Midtrans] Webhook received', $request->all());

    try {
        $this->midtransService()->handleWebhook();
        return response()->json(['message' => 'OK']);
    } catch (Throwable $e) {
        Log::error('[Midtrans] Webhook error', ['error' => $e->getMessage()]);
        return response()->json(['message' => 'ERROR'], 500);
    }
}
```

Import yang bisa dihapus dari Controller:
- `use App\Central\Models\Tenant;`
- `use App\Central\Models\TenantRegistration;`
- `use App\Tenant\Models\Core\Order;`
- `use Midtrans\Config;`
- `use Midtrans\Notification;`

---

### Step 5 — [MODIFY] `DuitkuController.php`

Setelah logic dipindah ke Service, `callback()` menjadi:
```php
public function callback(Request $request): Response
{
    Log::info('[Duitku Central] Callback diterima', [
        'merchantOrderId' => $request->input('merchantOrderId'),
    ]);

    try {
        $this->duitkuService()->handleWebhook($request->input('merchantOrderId', ''));
        return response('OK', 200);
    } catch (Throwable $e) {
        Log::error('[Duitku Central] Callback error', ['error' => $e->getMessage()]);
        return response('ERROR', 400);
    }
}
```

Import yang bisa dihapus dari Controller:
- `use App\Central\Models\Tenant;`
- `use App\Central\Models\TenantRegistration;`
- `use App\Tenant\Models\Core\Order;`
- `use App\Tenant\Services\TenantWalletService;`
- `use Illuminate\Support\Facades\DB;`

---

## Verification Plan

```bash
php -l app/Central/Controllers/MidtransController.php
php -l app/Central/Controllers/DuitkuController.php
php -l app/Central/Services/MidtransService.php
php -l app/Central/Services/DuitkuService.php
```

Manual:
- Simulasi webhook Midtrans `INV-REG-` → tenant harus terbuat
- Simulasi webhook Midtrans `{tenantId}__{invoiceCode}` → order harus update ke paid
- Simulasi webhook Duitku callback → wallet harus dikreditkan
- Request `getPaymentMethods` tanpa `amount` → harus return 422
- Simulasi underpaid → order harus ter-cancel dan stok restore
