# ADR: Bugfix Keranjang Belanja Toko Online (Extra IDs)

**Tanggal:** 2026-08-04  
**Status:** Planned  
**Target Files:**
- `resources/views/pages/tenant/store/resto/store.blade.php` (Core Script)
- `resources/views/pages/tenant/store/resto/modals/option-modal.blade.php`

---

## Masalah

Bug "Kembalian Bodoh" / "Discrepancy Tagihan Frontend dan Backend karena Extra Tidak Masuk Payload" ternyata tidak hanya terjadi di halaman POS Kasir, tetapi juga terjadi di halaman **Toko Online (Customer Facing App)**.

Pelanggan yang memesan produk dengan Topping/Ekstra via QR/Link Toko Online akan mendapati tagihan mereka lebih besar di layar HP mereka dibandingkan nilai yang tercatat di sistem backend (Order API Controller).

### Akar Masalah
Sama persis dengan POS Kasir:
1. Fungsi AlpineJS `toggleExtra` dan `isExtraSelected` menggunakan `.name` alih-alih `.id`.
2. Saat menekan "Tambah ke Keranjang", objek yang dikirim ke `this.cart.push` tidak memiliki array `extra_ids`.

---

## Solusi (Fix Plan)

1. **Ganti Referensi Alpine dari Name ke ID:**
   Ubah semua pemanggilan `isExtraSelected` dan `toggleExtra` di `option-modal.blade.php` dari parameter `extra.name` menjadi `extra.id`.
2. **Perbaiki Alpine JS Store Script:**
   Buka script utama AlpineJS untuk toko online dan ubah metode:
   - `toggleExtra()` menggunakan ID.
   - `extrasTotal()` melakukan filter berbasis ID.
   - `confirmOption()` menambahkan array `extra_ids: [...this.extrasSelected]` ke *cart object*.
3. Pastikan `addToCart()` (untuk produk tanpa varian/opsi) juga mem-*push* `extra_ids: []` sebagai nilai *default*.