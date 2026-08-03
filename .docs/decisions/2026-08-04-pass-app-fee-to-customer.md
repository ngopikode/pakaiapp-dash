# ADR: Pass Application Transaction Fee to Customer (Frontend & Backend Sync)

**Tanggal:** 2026-08-04
**Status:** Done
**Target Files:**
- `resources/views/pages/tenant/pos/⚡resto-cashier/resto-cashier.php`
- `resources/views/pages/tenant/pos/⚡resto-cashier/resto-cashier.blade.php`
- `resources/views/pages/tenant/pos/partials/_modal-payment.blade.php`
- `resources/views/pages/tenant/pos/partials/_cart-resto.blade.php`

---

## Masalah

Sistem memiliki fitur `is_application_fee_passed` di `StoreSetting` yang menentukan apakah biaya transaksi aplikasi (PakaiApp fee, default Rp 300) ditanggung pelanggan atau ditanggung toko.

**Masalah Kritis:**
1. **Backend (OrderService)** sudah menghitung biaya ini (`$applicationFeeAmount = $isAppFeeActive ? $settingService->get('default_trx_fee', ...) : 0`) dan menambahkannya ke `total_price` order.
2. **Frontend (AlpineJS `restoPos`)** **TIDAK MENGETAHUI** adanya biaya ini.
3. Hasilnya: UI menampilkan total tagihan (misal Rp 50.000), tapi server menyimpan order dengan total + fee (misal Rp 50.300).
4. Pelanggan dibayar Rp 50.000, kasir tercatat "kurang bayar" Rp 300, atau kasir harus menutupi selisihnya.

---

## Solusi

Sinkronkan konfigurasi biaya aplikasi dari server ke frontend (Alpine `restoPos` state), lalu hitung `applicationFee` secara real-time di client-side sehingga:
1. UI menampilkan rincian "Biaya Aplikasi Rp 300" di Cart & Payment Modal.
2. Total tagihan (`payTotal`) di frontend = `subTotal + tax + serviceCharge + appFee`.
3. Pelanggan membayar sesuai angka yang ditampilkan UI.
4. `ProcessOrderData` dikirim ke server sudah termasuk `globalDiscount` & biaya, jadi server tidak perlu menganggap ini "selisih".

---

## Implementasi Plan

### 1. Backend (`resto-cashier.php`): Expose config ke Frontend
- Tambahkan `isAppFeePassed` & `appFeeAmount` ke return `with()` method.
- Gunakan `SettingService` yang sudah ada di `OrderService` via container.

### 2. Frontend State (Alpine `restoPos`):
- Tambah `isAppFeePassed` & `appFeeAmount` ke `x-data`.
- Tambah getter `applicationFee` & update `subTotalWithCharges` & `payTotal`.

### 3. UI Cart (`_cart-resto.blade.php`):
- Tambah baris "Biaya Aplikasi" jika `applicationFee > 0`.

### 4. Payment Modal (`_modal-payment.blade.php`):
- Tambah rincian biaya aplikasi di rincian total tagihan.
- Pastikan total yang ditampilkan = `payTotal` (sudah include app fee).

---

## Keputusan Implementasi

- **State di Alpine:** `isAppFeePassed` (boolean) + `appFeeAmount` (float, default 300).
- **Getter Alpine:** `applicationFee = isAppFeePassed ? appFeeAmount : 0`.
- **Hitungan:** `subTotalWithCharges = subTotal + serviceCharge + tax + applicationFee`.
- **`payTotal`** sudah pakai `subTotalWithCharges`, jadi otomatis naik.
- **DTO `ProcessOrderData`:** Tidak perlu field baru, karena `globalDiscount` sudah dipakai untuk diskon, biaya aplikasi ditambahkan di sisi server (OrderService) berdasarkan store setting.

---

## Catatan Ulang Tahun (Future)

Jika nanti `default_trx_fee` diubah oleh admin via UI Setting, cukup refresh halaman kasir dan `appFeeAmount` akan ter-update otomatis dari server (via `with()` method Livewire).