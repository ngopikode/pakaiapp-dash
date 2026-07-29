# Fitur: Wallet Refactor — Multi-Wallet untuk Billing, Cash, Bank, Gateway

> Referensi: [Project Map](../../project-map.md) · [Architecture Decisions](../../decisions/) · [Laravel 13 Patterns](../../references/laravel13/PATTERNS.md)

Dokumen ini mencatat rencana implementasi fase 1 untuk upgrade tabel `wallets` agar mendukung pemisahan uang billing Pakaiapp, kas tunai toko, uang rekening tenant, dan uang payment gateway.

---

## Konteks & Masalah Saat Ini

1. **Wallet saat ini single-purpose** — `TenantWalletService::getWallet()` memakai `Wallet::firstOrCreate(['id' => 1])`, sehingga wallet dianggap satu-satunya saldo tenant.
2. **Wallet existing adalah billing/deposit Pakaiapp** — saldo dipotong oleh `BillingService` untuk biaya transaksi dan penalti void.
3. **Buku Kas Toko butuh ledger yang sama** — struktur `wallets` dan `wallet_transactions` sudah cocok untuk arus kas, jadi tidak perlu bikin tabel `cashbooks` baru.
4. **QRIS/transfer tenant bypass bukan gateway Pakaiapp** — uang langsung masuk rekening tenant, bukan dikelola Pakaiapp.
5. **Fee aplikasi bisa ditanggung merchant atau pembeli** — jika pembeli bayar cash/bank bypass, fee ada di laci/rekening tenant sehingga wallet `billing` tetap harus dipotong. Jika fee bisa diambil dari settlement gateway, wallet `billing` tidak dipotong.

---

## Keputusan Arsitektur

### 1. Empat Tipe Wallet

- `billing`
  - Saldo deposit merchant untuk membayar biaya Pakaiapp.
  - Ini adalah wallet lama yang sekarang memakai `id = 1`.
  - Dipakai oleh `BillingService`.

- `cash`
  - Uang tunai toko.
  - Sumber: setoran shift/Z-Report atau transaksi tunai yang sudah disahkan.
  - Dipakai untuk buku kas fisik.

- `bank`
  - Uang non-tunai yang langsung masuk rekening tenant.
  - Contoh: QRIS statis tenant, transfer manual ke rekening tenant.
  - Pakaiapp tidak memegang uang ini.

- `gateway`
  - Uang yang lewat payment gateway terintegrasi.
  - Contoh: Duitku/Midtrans jika settlement dapat dikelola/dipantau sistem.
  - Jika gateway settlement bisa split fee Pakaiapp, wallet `billing` tidak perlu dipotong untuk fee tersebut.

### 2. Migration Phase 1

Tambah kolom ke tabel `wallets`:

```php
Schema::table('wallets', function (Blueprint $table) {
    $table->string('name')->default('Deposit Pakaiapp')->after('id');
    $table->enum('type', [
        Wallet::TYPE_BILLING,
        Wallet::TYPE_CASH,
        Wallet::TYPE_BANK,
        Wallet::TYPE_GATEWAY,
    ])->default(Wallet::TYPE_BILLING)->after('name');
    $table->unique('type');
});
```

Backfill wallet lama:

```php
DB::table('wallets')
    ->where('id', 1)
    ->update([
        'name' => 'Deposit Pakaiapp',
        'type' => Wallet::TYPE_BILLING,
    ]);
```

### 3. Type-Safe Constants

Gunakan constant di Model `Wallet` agar terhindar dari typo:

```php
class Wallet extends Model
{
    public const TYPE_BILLING = 'billing';
    public const TYPE_CASH = 'cash';
    public const TYPE_BANK = 'bank';
    public const TYPE_GATEWAY = 'gateway';
}
```

### 4. Kenapa `unique('type')`

MVP hanya butuh 1 wallet per tipe per tenant:
- 1 deposit billing
- 1 kas tunai
- 1 rekening/bank tenant
- 1 gateway settlement

Skipped: multi rekening bank, multi laci kasir, multi QRIS account. Add when merchant butuh pemisahan BCA/Mandiri/QRIS/Brankas.

### 5. Compatibility

Migration tidak boleh merusak data lama:
- wallet existing `id = 1` tetap ada
- transaksi lama di `wallet_transactions` tetap menunjuk ke wallet yang sama
- default `type = billing`, karena wallet lama memang deposit Pakaiapp

---

## Fase Implementasi

### Fase 1: Migration Wallet

File:
- `database/migrations/tenant/core/YYYY_MM_DD_HHMMSS_add_type_and_name_to_wallets_table.php`
- `app/Tenant/Models/Core/Wallet.php`

Task:
- Tambah constant type di Model `Wallet`
- Tambah `name` dan `type` via migration
- Backfill wallet lama jadi `billing`
- Tambah unique index pada `type`

### Fase 2: Service Refactor

File terdampak:
- `app/Tenant/Services/TenantWalletService.php`
- `app/Central/Services/BillingService.php`

Task:
- Ubah `getWallet()` jadi `getWallet(string $type = Wallet::TYPE_BILLING): Wallet`
- `addBalance()` dan `deductBalance()` menerima `$walletType = Wallet::TYPE_BILLING`
- `BillingService` eksplisit pakai wallet `billing`
- Guard application fee berdasarkan jalur pembayaran:
  - `cash`, `qris`, `transfer` tenant bypass: tetap potong wallet `billing`
  - gateway yang bisa split fee: jangan potong wallet `billing`

### Fase 3: Ledger Integration & UI Wallet

File terdampak:
- `app/Tenant/Services/OrderService.php`
- callback payment gateway terkait
- `resources/views/pages/tenant/payment/⚡wallet/wallet.php`
- `resources/views/pages/tenant/payment/⚡wallet/wallet.blade.php`
- `resources/views/components/layouts/⚡sidebar/sidebar.php`

Task:
- Mapping payment method ke wallet: cash → cash, qris/transfer → bank, duitku/midtrans/digital → gateway
- Tambah tab/filter wallet: Billing, Tunai, Bank, Gateway
- Tetap default ke Billing agar flow lama aman

---

## Risiko & Guardrail

1. **Jangan campur billing dengan kas toko**
   - Semua biaya Pakaiapp harus pakai wallet `billing`.

2. **Jangan salah skip billing charge saat fee dibebankan ke pembeli**
   - Fee cash/bank bypass tetap harus potong `billing`, karena uang fee diterima tenant.
   - Fee gateway hanya boleh skip potong `billing` jika fee bisa diambil dari settlement gateway.

3. **Jangan bikin tabel cashbook baru**
   - `wallet_transactions` sudah cukup sebagai ledger.

4. **Ikuti standar project**
   - Service pakai lazy getter atau constructor injection sesuai `.docs/references/laravel13/PATTERNS.md`.
   - Business logic tetap di Service, Livewire hanya UI/event.
   - Migration masuk `database/migrations/tenant/core/`.
