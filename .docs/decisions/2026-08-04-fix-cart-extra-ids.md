# ADR: Bugfix Keranjang Belanja "Extra" (Kembalian Gaib)

**Tanggal:** 2026-08-04
**Status:** Done
**Target Files:**
- `resources/views/pages/tenant/pos/⚡resto-cashier/resto-cashier.blade.php`
- `resources/views/pages/tenant/pos/partials/_modal-option.blade.php`

---

## Masalah

Ketika kasir menambahkan produk dengan opsi **"Extra"** (seperti topping tambahan berbayar), total pembayaran (Amount Paid) di layar kasir menjadi lebih besar daripada tagihan aktual yang tersimpan di sistem. Akibatnya, pada struk atau history transaksi, muncul kembalian bodong (Change Amount).

### Akar Masalah

Terdapat _mismatch_ (ketidakcocokan) komunikasi data antara Frontend AlpineJS dan Backend PHP:

1. **Frontend Menyimpan Nama, Bukan ID:**
   Ketika "Extra" diklik di modal opsi, AlpineJS menyimpan `extra.name` ke dalam *array* `this.extrasSelected`. 
2. **Payload Cart Tidak Lengkap:**
   Saat produk dimasukkan ke keranjang via `confirmOption()`, AlpineJS hanya menggabungkan nama Extra menjadi label `variant_name` secara visual, namun **LUPA** melampirkan array ID extra tersebut (`extra_ids`) ke objek `$cart` yang dikirim ke server.
3. **Backend Mengabaikan Extra:**
   Server (`OrderService.php`) menerima pesanan tanpa `extra_ids`. Alhasil, server menghitung ulang harga secara sepihak dan membuang harga ekstra karena sistem menganggap ekstra tidak pernah dipilih. Karena total tagihan server menjadi lebih kecil dari uang yang disetor kasir, sistem menganggap sisa uangnya adalah "kembalian" (Change Amount).

---

## Solusi (Fix Plan)

1. **Ganti Referensi Alpine dari Name ke ID:**
   Ubah semua referensi fungsi di `resto-cashier.blade.php` (`toggleExtra`, `isExtraSelected`) agar menyimpan dan mencari berdasarkan `extra.id` bukan `extra.name`.
2. **Kirim Array ID ke Cart:**
   Pada fungsi `confirmOption()`, tambahkan *property* baru pada objek `cart.push` yaitu `extra_ids: [...this.extrasSelected]`.
3. **Penyempurnaan Label Keranjang:**
   Karena `extrasSelected` sekarang berisi ID, kita harus melakukan *lookup* ke objek produk untuk mendapatkan kembali nama-namanya agar bisa digabungkan di label keranjang.
4. **Update Modal Option:**
   Ganti parameter fungsi `isExtraSelected(extra.name)` menjadi `isExtraSelected(extra.id)` di `_modal-option.blade.php`.