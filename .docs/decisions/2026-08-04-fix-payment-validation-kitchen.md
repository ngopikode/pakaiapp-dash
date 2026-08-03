# ADR: Bugfix Pembayaran Kasir & Sinkronisasi Amount FE/BE

**Tanggal:** 2026-08-04  
**Status:** Planned  
**Target Files:**
- `app/Tenant/Services/OrderService.php`
- `resources/views/pages/tenant/pos/⚡resto-cashier/resto-cashier.php`

---

## Analisis Masalah

### 1. Pesanan Langsung Masuk Dapur Saat Uang Kurang (Arsitektur Checkout)
Pada fungsi `processDirectCheckout()` di Livewire `resto-cashier.php`:
1. Kode memanggil `$order = $this->orderService()->processOrder($dto, $cart);`. (Pesanan langsung disimpan ke DB, stok dikurangi, dan disiarkan ke Dapur/Kitchen).
2. *Kemudian* mengecek: `if ($paid < $totalPrice) throw Exception(...)`
3. Jika kasir salah input uang (kurang bayar), akan muncul `Exception`, tapi **ordernya sudah terlanjur dibuat dan koki mulai memasak!** 

**Solusi:**
Validasi pembayaran manual untuk transaksi *Direct Checkout* harus dipindah langsung ke dalam servis `OrderService::processOrder()`, satu paket dengan `DB::beginTransaction()`. Jika uang kurang, batalkan transaksi dan lempar exception *sebelum* `DB::commit()`. Dengan demikian order tidak terbuat dan stok aman.

### 2. Discrepancy Amount antara FE dan BE
Perbedaan nilai tagihan (`$paid < $totalPrice`) mengindikasikan adanya selisih perhitungan akibat *rounding* (pembulatan).

Javascript (Alpine) dan PHP sering kali memiliki hasil pembulatan yang berbeda 1 rupiah saat menghitung PPN dan Service Charge yang berbentuk persentase. Ketika sistem Livewire mengecek `if ($paid < $totalPrice)` secara eksak, selisih 1 rupiah saja akan dianggap "kurang bayar", lalu pembayaran ditolak.

**Solusi Toleransi Pembulatan (Rounding Tolerance):**
Ketika kita membandingkan `$paid` dengan `$totalPrice`, kita harus memberikan toleransi wajar (misal: 10 rupiah) untuk mengakomodir perbedaan algoritma pembulatan (IEEE 754 vs standar PHP).
```php
if ($paid < ($totalPrice - 10)) {
    throw new Exception('Nominal pembayaran kurang.');
}
```
Atau jika `$paid` dimasukkan (kasir mengisi nominal di modal), dan selisihnya kurang dari 10 rupiah dari total tagihan sistem, kita anggap itu **uang pas**, sehingga tagihannya disesuaikan mengikuti uang fisik yang diterima.

---

## Action Plan

1. **Pindahkan Logika Validasi Pembayaran ke `OrderService`:**
   - Di akhir `processOrder()`, tambahkan pengecekan jika metode pembayaran adalah 'cash' atau 'transfer' dan status dibayar ('paid'/'completed').
   - Jika `amountPaid > 0` tapi kurang dari `$totalPrice` dengan margin toleransi, lempar Exception (akan me-trigger `DB::rollBack()`).

2. **Perbaiki Livewire Callers:**
   - Hapus pengecekan manual `if ($paid < $totalPrice)` dari `processDirectCheckout` di Livewire.
   - Hapus query update order `change_amount` dan `amount_paid` di Livewire, biarkan `OrderService` yang mengerjakannya saat `processOrder()`.
