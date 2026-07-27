# Fitur: Platform Fee Pass-Through — Biaya Aplikasi ke Customer

> Referensi: [Store Setting UI](../store-setting-ui/plan.md) · [Operating Hours](../operating-hours/plan.md) · [Project Map](../../project-map.md)

---

## Masalah Saat Ini

1. **Service Charge (persen) ambigu** — Tenant bisa set "Biaya Layanan" (persen). Tapi ada juga "Biaya Aplikasi" (flat `trx_fee` yang ditarik platform dari wallet tenant).
2. **Platform fee membebani resto** — `BillingService::chargeTransactionFee()` memotong `trx_fee` (flat Rp300, default) dari wallet tenant per order.
3. **Kebutuhan Bisnis** — Resto butuh kemampuan untuk **membebankan biaya aplikasi (flat) ke pelanggan**, TANPA menghilangkan fitur Biaya Layanan Resto (persen) jika resto tetap mau pakai keduanya.

---

## Keputusan Arsitektur

### Pertahankan Keduanya, Buat Kolom Baru

Kita **TIDAK BISA** me-repurpose kolom `service_charge` yang ada, karena:
- Resto bisa butuh dua-duanya jalan bareng: Biaya Layanan Resto 5% + Biaya Aplikasi Rp300.
- Uang "Biaya Layanan Resto" adalah hak resto.
- Uang "Biaya Aplikasi" adalah hak platform (PakaiApp), yang akan ditarik dari wallet tenant. Jika order dibayar customer, tenant "tidak nombok".

### Entitas Baru: Biaya Aplikasi (Application Fee)

1. **Database & Model**
   - Tabel `store_settings`: tambah `is_application_fee_passed` (boolean, default false)
   - Tabel `orders`: tambah `application_fee` (decimal/int, default 0)

2. **Nominal Biaya Aplikasi**
   - BUKAN diinput oleh tenant.
   - Diambil dari Central DB via `SettingService::get('default_trx_fee')`.
   - Nominal flat (contoh Rp300).

3. **Setting Tenant (UI)**
   - Ada 2 toggle terpisah di bagian "Pajak & Layanan":
     1. **Biaya Layanan Resto**: Toggle ON/OFF + Input Persentase (Existing)
     2. **Biaya Aplikasi**: Toggle ON/OFF saja. Wording: "Bebankan biaya aplikasi PakaiApp ke pelanggan." (Baru)

4. **Kalkulasi & Checkout**
   - Subtotal
   - + Diskon
   - + Biaya Layanan Resto (X%)
   - + Biaya Aplikasi (Flat Rp300)
   - + Pajak PB1 (Y% dari [Subtotal - Diskon + Biaya Layanan + Biaya Aplikasi])
   - = Total

---

## File yang Diubah

### Phase 1: Database & Model (Migration)
- `database/migrations/tenant/..._add_application_fee_to_store_settings_and_orders.php` (BARU)
  - `store_settings`: `boolean('is_application_fee_passed')->default(false)`
  - `orders`: `decimal('application_fee', 10, 2)->default(0)`
- `app/Tenant/Models/Core/StoreSetting.php` — tambah ke `$fillable`, default cast boolean.
- `app/Tenant/Models/Core/Order.php` — tambah ke `$fillable`.

### Phase 2: Backend Logic
- `app/Tenant/Services/OrderService.php` 
  - `calculateTaxesAndTotal()` — tambah parameter `$appFeeAmount`, tambahkan ke dasar pengenaan pajak dan total.
  - `processOrder()` — ambil `default_trx_fee` jika `is_application_fee_passed` aktif. Pass ke kalkulator.
- `app/Tenant/Controllers/Api/RestaurantApiController.php`
  - Kirim 2 variabel ke FE: `is_application_fee_passed` dan `application_fee_amount` (ambil dari central).

### Phase 3: Frontend Settings
- `app/Livewire/Pages/Tenant/Setting/StoreSetting.php` — tambah property `is_application_fee_passed`.
- `resources/views/pages/tenant/setting/⚡store-setting/store-setting.blade.php` — tambah toggle baru di card "Pajak & Layanan".

### Phase 4: Storefront & Customer View (Alpine & UI)
- `resources/js/store.js`
  - Tambah getter `appFeeAmount()`
  - Update `totalOrderPrice` dan dasar pengenaan `taxAmount`
  - Update `fetchLatestSettings()` untuk ambil state fee baru
- `resources/views/layouts/store.blade.php` + `product.blade.php` — pass dataset variable baru.
- `checkout-modal.blade.php` — tampilkan baris baru "Biaya Aplikasi: Rp 300" jika aktif.
- `receipt/⚡show/show.blade.php`, `order/⚡show/show.blade.php`, `order/⚡order-modal/order-modal.blade.php` — tampilkan breakdown Biaya Aplikasi.

### Phase 5: Payment Gateways
- `app/Central/Services/MidtransService.php` — tambah item detail `APP_FEE` (Biaya Aplikasi).
- `app/Central/Services/DuitkuService.php` — pastikan perhitungan total akurat dengan tambahan app fee.

---

## Detail Teknis (OrderService)

```php
private function calculateTaxesAndTotal(float $subtotal, float $discount, float $taxRate, float $serviceRate, float $appFeeAmount): array
{
    $subtotalAfterDiscount = max(0, $subtotal - $discount);
    $serviceCharge = round(($serviceRate / 100) * $subtotalAfterDiscount);
    
    // Dasar pengenaan pajak (DPP) = Subtotal + Service Charge + App Fee
    $dpp = $subtotalAfterDiscount + $serviceCharge + $appFeeAmount;
    $taxAmount = round(($taxRate / 100) * $dpp);
    
    $totalPrice = $dpp + $taxAmount;

    return [
        'subtotal' => $subtotal,
        'service_charge_amount' => $serviceCharge,
        'application_fee' => $appFeeAmount, // NEW
        'tax_amount' => $taxAmount,
        'total_price' => $totalPrice,
        'discount' => $discount,
    ];
}
```

---

## Open Questions

- [x] Siapa pemilik "Biaya Layanan"? Resto.
- [x] Siapa pemilik "Biaya Aplikasi"? PakaiApp.
- [ ] BillingService: `chargeTransactionFee()` tetap jalan memotong wallet tenant secara background. Karena tenant menerima uang kas dari customer yang include Biaya Aplikasi, saat wallet tenant dipotong, hitungannya impas. **Tidak ada perubahan di BillingService.**

---

*Dibuat: 2026-07-27. Revisi: Mempertahankan Service Charge lama dan menambah Biaya Aplikasi sebagai entitas baru.*
