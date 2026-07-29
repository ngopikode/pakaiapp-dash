# Wallet Refactor — Phase 2 Service Refactor Plan

> Referensi: [Plan Utama](./plan.md) · [Laravel 13 Patterns](../../references/laravel13/PATTERNS.md)

Dokumen ini merinci implementasi Phase 2 untuk refactor service wallet dari single wallet hardcoded (`id = 1`) menjadi multi-wallet berbasis `Wallet::TYPE_*`.

---

## Scope Phase 2

Phase 2 hanya mengubah backend service dan caller yang terkait langsung dengan wallet/billing.

Tidak termasuk:
- UI tab wallet
- Shift/Z-Report
- Pencatatan cash/bank/gateway sales ke wallet toko
- Inventory/opname

---

## Tujuan

1. Hilangkan hardcode `Wallet::firstOrCreate(['id' => 1])`.
2. Jadikan wallet `billing` sebagai default agar backward compatible.
3. Semua transaksi biaya Pakaiapp tetap memakai wallet `billing`.
4. Cegah salah skip charge untuk app fee pass-through:
   - cash/bank bypass tetap potong `billing`
   - gateway hanya skip potong `billing` jika fee bisa diambil dari settlement gateway
5. Siapkan jalur wallet `cash`, `bank`, dan `gateway` untuk phase berikutnya.

---

## File Terdampak

### Modify
- `app/Tenant/Services/TenantWalletService.php`
- `app/Central/Services/BillingService.php`
- `app/Tenant/Models/Core/Wallet.php` jika constant belum ada

### Tidak Diubah di Phase Ini
- `resources/views/pages/tenant/payment/⚡wallet/*`
- `resources/views/components/layouts/⚡sidebar/sidebar.php`
- `OrderService.php` untuk cash/bank/gateway ledger toko

---

## Keputusan Teknis

### 1. Wallet Type Constant

Semua pemanggilan wallet type wajib memakai constant model:

```php
Wallet::TYPE_BILLING
Wallet::TYPE_CASH
Wallet::TYPE_BANK
Wallet::TYPE_GATEWAY
```

Tidak boleh pakai raw string:

```php
'billing'
'cash'
'bank'
'gateway'
```

### 2. Default Wallet Tetap Billing

`TenantWalletService::getWallet()` default ke billing agar code lama tetap aman:

```php
public function getWallet(string $type = Wallet::TYPE_BILLING): Wallet
```

### 3. Nama Default Wallet

Gunakan mapping kecil di service, bukan concat mentah, agar label rapi:

```php
private function defaultWalletName(string $type): string
{
    return match ($type) {
        Wallet::TYPE_BILLING => 'Deposit Pakaiapp',
        Wallet::TYPE_CASH => 'Kas Tunai',
        Wallet::TYPE_BANK => 'Kas Bank',
        Wallet::TYPE_GATEWAY => 'Kas Gateway',
    };
}
```

---

## Detail Implementasi

### 1. Refactor `TenantWalletService::getWallet()`

Sebelum:

```php
return Wallet::firstOrCreate(
    ['id' => 1],
    ['balance' => 0]
);
```

Sesudah:

```php
return Wallet::firstOrCreate(
    ['type' => $type],
    [
        'name' => $this->defaultWalletName($type),
        'balance' => 0,
    ]
);
```

### 2. Refactor `addBalance()`

Signature baru:

```php
public function addBalance(
    float|int $amount,
    Model $reference,
    ?string $description = null,
    string $walletType = Wallet::TYPE_BILLING,
): WalletTransaction
```

Behavior:
- default tetap billing
- caller cash/bank/gateway bisa eksplisit override nanti

### 3. Refactor `deductBalance()`

Signature baru:

```php
public function deductBalance(
    float|int $amount,
    Model $reference,
    ?string $description = null,
    string $walletType = Wallet::TYPE_BILLING,
): WalletTransaction
```

Behavior:
- default tetap billing
- biaya Pakaiapp eksplisit kirim `Wallet::TYPE_BILLING`

### 4. Refactor `processTransaction()`

Tambahkan parameter wallet type:

```php
private function processTransaction(
    string $type,
    float|int $amount,
    Model $reference,
    ?string $description = null,
    string $walletType = Wallet::TYPE_BILLING,
): WalletTransaction
```

Ambil wallet:

```php
$expectedWalletId = $this->getWallet($walletType)->id;
$wallet = Wallet::where('id', $expectedWalletId)->lockForUpdate()->firstOrFail();
```

### 5. Refactor `BillingService::lockAndPrepareWallet()`

Sebelum:

```php
Wallet::lockForUpdate()->firstOrCreate(
    ['id' => 1],
    ['balance' => 0]
);
```

Sesudah:

```php
Wallet::where('type', Wallet::TYPE_BILLING)
    ->lockForUpdate()
    ->firstOrCreate(
        ['type' => Wallet::TYPE_BILLING],
        [
            'name' => 'Deposit Pakaiapp',
            'balance' => 0,
        ]
    );
```

### 6. Guard Application Fee Pass-Through

Jika `orders.application_fee > 0`, biaya aplikasi sudah dibebankan ke pembeli. Tapi sumber uangnya menentukan apakah wallet `billing` tetap dipotong.

Rules:
- `cash`: tetap potong `billing`, karena fee diterima merchant di laci kasir
- `qris` / `transfer`: tetap potong `billing`, karena fee masuk rekening tenant (bypass Pakaiapp)
- `duitku` / `midtrans` / `digital`: skip potong `billing` hanya jika fee bisa diambil dari settlement gateway

MVP rule saat Phase 2:
- Treat gateway methods as settlement-managed: `duitku`, `digital`, `midtrans`
- Treat tenant bypass methods as merchant-held: `cash`, `qris`, `transfer`

Pseudocode:

```php
$isPassedToCustomer = (float) $order->application_fee > 0;
$isGatewaySettlement = in_array($order->payment_method, ['duitku', 'digital', 'midtrans'], true);
$shouldDeductBilling = $feeToCharge > 0 && (!$isPassedToCustomer || !$isGatewaySettlement);

if ($feeToCharge > 0) {
    $updateData['monthly_fee_paid'] = $wallet->monthly_fee_paid + $feeToCharge;
}

$wallet->update($updateData);

if ($shouldDeductBilling) {
    $this->walletService->deductBalance(
        amount: $feeToCharge,
        reference: $order,
        description: "Biaya layanan pakaiapp untuk pesanan $order->invoice_code",
        walletType: Wallet::TYPE_BILLING,
    );
}
```

Catatan: `monthly_fee_paid` tetap naik karena fee tetap menjadi pendapatan Pakaiapp; yang berubah hanya sumber pembayarannya.

### 7. Refactor `processVoidPenalty()`

Penalty void tetap biaya merchant, bukan pembeli.

Rules:
- selalu pakai wallet `billing`
- tidak terkait `orders.application_fee`

```php
$this->walletService->deductBalance(
    amount: $voidPenaltyFee,
    reference: $order,
    description: "Penalti void berlebih untuk pesanan $order->invoice_code",
    walletType: Wallet::TYPE_BILLING,
);
```

---

## Backward Compatibility

Phase 2 wajib aman untuk flow lama:

- Caller lama `addBalance($amount, $reference, $description)` tetap masuk ke billing.
- Caller lama `deductBalance($amount, $reference, $description)` tetap potong billing.
- Halaman wallet lama tetap menampilkan billing karena `getWallet()` default billing.
- `wallet_transactions` lama tetap valid karena `wallet_id` tidak berubah.

---

## Risiko

1. **Double charge application fee**
   - Dicegah dengan skip khusus gateway settlement.

2. **Salah skip billing untuk cash/bank bypass**
   - Dicegah dengan mapping `payment_method`.

3. **Typo wallet type**
   - Dicegah dengan `Wallet::TYPE_*` constant.

4. **Deadlock / stale read**
   - Tetap pakai manual `DB::beginTransaction()` + `lockForUpdate()` sesuai standar project.

5. **Unique type belum ada**
   - Phase 2 baru aman dijalankan setelah Phase 1 migration wallet selesai.

---

## Acceptance Criteria

- Tidak ada lagi pemanggilan wallet hardcoded `['id' => 1]` di `TenantWalletService` dan `BillingService`.
- Semua biaya layanan Pakaiapp memakai `Wallet::TYPE_BILLING`.
- Order cash/bank bypass dengan `application_fee > 0` tetap memotong saldo billing.
- Order gateway settlement dengan `application_fee > 0` tidak memotong saldo billing.
- Order tanpa `application_fee` tetap memotong saldo billing seperti sekarang.
- Void penalty tetap memotong saldo billing.
- Tidak ada perubahan UI.
