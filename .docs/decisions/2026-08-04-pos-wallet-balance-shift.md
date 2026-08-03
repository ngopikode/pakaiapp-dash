# ADR: Wallet Balance Fix — Deduct Starting Cash on Shift Open

**Tanggal:** 2026-08-04
**Status:** Done
**File:** `app/Tenant/Services/ShiftService.php`

---

## Masalah

`ShiftService::closeShift()` sudah benar — ia menyetorkan **seluruh uang aktual laci** (`actual_cash`) ke `Wallet::TYPE_CASH` saat shift ditutup.

Namun `ShiftService::openShift()` **tidak memotong** (`deductBalance`) saldo `Wallet::TYPE_CASH` ketika kasir memasukkan nilai Modal Awal (`starting_cash`).

### Efek Double Counting

```
Shift 1: Buka Rp 100.000 → Wallet TIDAK berkurang
         Jual Rp 200.000 → Wallet TIDAK bertambah (masuk ke shift.cash_sales)
         Tutup Rp 300.000 → Wallet BERTAMBAH Rp 300.000 ✓

Shift 2: Buka Rp 100.000 → Wallet TIDAK berkurang ← CELAH
         Jual Rp 0       →
         Tutup Rp 100.000 → Wallet BERTAMBAH Rp 100.000 ← DOUBLE COUNT

Hasil: Wallet Rp 400.000 padahal seharusnya Rp 200.000 (penjualan bersih)
```

Setelah beberapa shift, saldo `Wallet::TYPE_CASH` akan menggelembung secara artifisial — uang yang tidak pernah ada.

---

## Solusi

Saat `openShift()` dipanggil dan `starting_cash > 0`, lakukan `deductBalance` dari `Wallet::TYPE_CASH` sejumlah `starting_cash`.

Ini merepresentasikan bahwa uang modal laci **"diambil dari kas utama/brankas toko"**.

Arus kas yang benar:
```
Buka Shift Rp 100.000 → Wallet KAS berkurang Rp 100.000 (uang masuk ke laci)
...penjualan berjalan, masuk shift.cash_sales...
Tutup Shift Rp 300.000 → Wallet KAS bertambah Rp 300.000 (laci disetor ke brankas)

Net effect: +Rp 200.000 (penjualan bersih yang benar)
```

---

## Keputusan

- Wallet `TYPE_CASH` **diperbolehkan menjadi minus** jika saldo tidak cukup. Ini adalah perilaku normal di F&B kecil/menengah di mana owner sering nombokin laci dari kantong pribadi.
- `openShift()` dibungkus `DB::beginTransaction()` karena sekarang melibatkan dua operasi database: INSERT ke tabel `shifts` dan INSERT ke tabel `wallet_histories`.
- `starting_cash = 0` (kasir tidak membawa modal awal) tidak akan memotong wallet.
