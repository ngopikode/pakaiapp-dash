# ADR: Sinkronisasi Presisi Rumus Akuntansi POS (FE & BE)

**Tanggal:** 2026-08-04  
**Status:** Planned  
**Target Files:**
- `resources/views/pages/tenant/pos/⚡resto-cashier/resto-cashier.blade.php`
- `app/Tenant/Services/OrderService.php`

---

## Masalah: Diskrepansi Tagihan & Kembalian "Gaib"

Terdapat laporan bahwa saat kasir memasukkan uang dengan jumlah "Uang Pas", sistem memunculkan Exception `"Nominal pembayaran kurang"`, atau mencetak struk dengan kembalian aneh yang berbeda dari layar kasir.

### Root Cause (Akar Masalah)

Akar masalahnya bukanlah sebatas pembulatan (rounding) antar bahasa pemrograman, melainkan **Perbedaan Fundamental pada Rumus Akuntansi** antara Frontend (AlpineJS) dan Backend (`OrderService.php`).

**Perbedaan 1: Pengenaan Pajak (DPP)**
- **Backend:** Pajak dikenakan pada Dasar Pengenaan Pajak (DPP) yang mencakup: `Subtotal + Service Charge + Biaya Aplikasi`.
- **Frontend:** Pajak dikenakan HANYA pada: `Subtotal + Service Charge`. Frontend lupa memasukkan Biaya Aplikasi ke dalam objek pajak.

**Perbedaan 2: Urutan Potong Diskon Global**
- **Backend:** Diskon memotong harga barang (Subtotal) *terlebih dahulu*. Hasil pengurangan ini baru dipakai untuk menghitung Pajak dan Service Charge.
- **Frontend:** Frontend menghitung Pajak dan Service Charge secara *bruto* (dari subtotal sebelum didiskon), lalu diskon memotong hasil penjumlahan total (Grand Total) di akhir pada variabel `payTotal`.

Akibat dari dua perbedaan rumus ini, perhitungan Frontend (layar yang dilihat kasir) dan Backend (yang disimpan di database) akan **SELALU** menghasilkan angka yang berbeda jika ada Diskon atau Biaya Aplikasi.

### *Hack* Sementara yang Digunakan Sebelumnya
Pada iterasi sebelumnya, `OrderService.php` menggunakan *hack* dengan memberikan toleransi selisih hingga 10 Rupiah (`if ($paid < $finalTotalPrice - 10)`). Ini sangat berbahaya untuk sistem akuntansi POS karena bisa dieksploitasi dan menyebabkan kebocoran kas (shortage) sebesar 10 rupiah per transaksi, yang mana totalnya akan terbaca membengkak di laporan akhir bulan.

---

## Solusi / Action Plan

1. **Refactor Rumus Frontend (AlpineJS) agar 100% Sama dengan Backend**
   Kita akan mendefinisikan ulang *getter* (computed properties) di objek `restoPos` AlpineJS:
   - Terapkan pemotongan `globalDiscount` di awal (menciptakan getter `subtotalAfterDiscount`).
   - Ubah `serviceChargeAmount` agar dihitung dari `subtotalAfterDiscount`.
   - Ubah `taxAmount` agar dihitung dari DPP utuh (`subtotalAfterDiscount + serviceChargeAmount + applicationFeeAmount`).

2. **Hapus Toleransi di Backend (`OrderService.php`)**
   Setelah Frontend dan Backend memakai rumus yang identik, hasil perhitungan akan 100% presisi dan sama. Kita harus menghapus toleransi `- 10` rupiah yang bodoh itu dan mengembalikannya pada tingkat keakuratan mutlak (`if ($paid < $finalTotalPrice)`).

---

## Hasil Akhir yang Diharapkan
- Kasir akan melihat total yang sama persis dengan yang dilihat server.
- Uang "pas" tidak akan pernah ditolak.
- Tidak ada kebocoran kas (loss) karena celah toleransi pembulatan.
- Struk yang dicetak akan selalu presisi dengan UI.