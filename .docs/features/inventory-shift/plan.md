# Fitur: Modul Inventory & Sesi Kasir (Z-Report)

> Referensi: [Project Map](../../project-map.md)

Dokumen ini mencatat rencana implementasi, keputusan arsitektur, dan konteks untuk fitur Modul Inventory dan Sesi Shift Kasir (Z-Report).

---

## Konteks & Masalah Saat Ini

1. **Uang Fisik vs Uang Sistem** — Saat ini uang tagihan SaaS Pakaiapp (Deposit/Billing) menumpang di tabel `wallets`. Belum ada wadah pencatatan untuk uang fisik operasional di laci mesin kasir (Z-Report).
2. **Tidak ada Opname / Loss Tracking** — Stok otomatis terpotong saat transaksi, tapi tidak ada sistem wajib/harian untuk memantau selisih fisik barang (kebocoran/waste) yang biasa dilakukan saat tutup toko.
3. **Resep Ekstra Terbatas** — Fitur Topping/Ekstra (seperti "Ekstra Keju") belum terhubung secara dinamis untuk memotong stok bahan baku mentah (`raw_materials`).
4. **Buka/Tutup Toko vs Buka/Tutup Mesin Kasir** — Toko online (katalog/API) otomatis buka/tutup via `StoreSetting::operating_hours`. Namun, mesin kasir fisik memerlukan *logic* pengunci manual (Buka Shift) sebagai bentuk validasi tanggung jawab atas uang fisik yang ada di laci saat itu.
5. **Fleksibilitas Operasional UMKM** — Memaksa rutinitas "Buka Shift" dan "Tutup Shift + Opname" untuk UMKM kecil (misal: dijaga owner sendiri) akan terasa memberatkan. Sistem harus bisa di-toggle on/off.

---

## Keputusan Arsitektur

### 1. Struktur Data (Migration & Model)
- Buat tabel `shifts` (Sesi kasir) untuk mencatat sesi kasir laci (uang modal awal, penjualan tunai, selisih).
- Buat tabel `shift_expenses` untuk mencatat pengeluaran kasir (contoh: beli es batu) dari uang laci.
- Buat tabel `stock_opnames` dan `stock_opname_items` (polymorphic) untuk merekap hasil opname tutup shift.
- Tambah field `is_critical` (boolean) di `raw_materials` dan `product_variants` agar opname harian hanya difokuskan pada barang bernilai tinggi (Kategori A).
- Tambah field `is_shift_active` (boolean, default: `false`) di `store_settings` untuk menyalakan/mematikan paksaan rutinitas shift (Toggle ON/OFF).
- Refactor `variant_recipes` menjadi `recipes` (polymorphic via `recipeable_type` dan `recipeable_id`) agar `ProductVariant` dan `ProductExtra` bisa terhubung ke `RawMaterial`.

### 2. Strategi Logika Bisnis (Backend)
- **Toggle OFF (`is_shift_active` = false):** Kasir beroperasi normal. Penjualan `cash` hanya terekam di tabel `orders` tanpa sistem laci.
- **Toggle ON (`is_shift_active` = true):**
  - Kasir di-lock dari layar order jika belum melakukan aksi "Buka Shift" (menyatakan modal uang awal hari itu).
  - Transaksi dengan metode pembayaran `cash` di tabel `orders` akan **juga meng-increment** nilai `cash_sales` di tabel `shifts` yang sedang aktif milik *auth user*.
  - Transaksi metode `digital` tidak dihitung dalam sesi laci kasir, sehingga selisih uang fisik laci tetap akurat.

---

## Fase Implementasi

### Fase 1: Persiapan Database (Core Shift & Stock Control)
- Penambahan kolom `is_shift_active` pada `store_settings`.
- Pembuatan 4 file migrasi baru (`shifts`, `shift_expenses`, `stock_opnames`, `stock_opname_items`).
- Penambahan kolom `is_critical` pada `raw_materials` dan `product_variants`.
- Refactor relasi tabel resep menjadi polymorphic (`recipes`).
- Pembaruan Seeder & Factory (jika ada) untuk menyesuaikan dengan struktur relasi baru.

### Fase 2: Business Logic & Auto-Deduct (Backend)
- Refactor `OrderService::processOrder()`:
  - Eksekusi potongan stok bahan baku untuk `ProductExtra` (Topping) berdasarkan tabel resep polymorphic.
  - Saat order dibayar *cash*, update `cash_sales` pada *shift* yang aktif milik user auth (jika setting shift ON).
- Refactor `OrderService::cancelOrder()` / `voidItem()`: 
  - Pastikan membalikkan logika pemotongan (retur stok ekstra/bahan baku).
  - Kurangi nilai `cash_sales` pada shift terkait (jika yang dibatalkan adalah transaksi tunai).
- Implementasi middleware atau proteksi controller: Blokir pembuatan order (HTTP 403/422) jika setting shift ON tapi kasir belum Buka Shift.

### Fase 3: UI/UX Flow (Storefront Kasir Frontend)
- **Pengaturan Tenant:** Tambah toggle "Wajibkan Shift Kasir & Opname" di halaman *Store Settings*.
- **UI Buka Shift**: Modal/Halaman perantara sederhana yang menanyakan "Berapa Modal Laci (Starting Cash) anda sekarang?" saat kasir membuka halaman POS.
- **UI Tutup Shift (Z-Report)**:
  - **Step 1 (Stock Opname)**: Menampilkan list barang dengan flag `is_critical = true`. Input pre-fill stok sistem vs stok fisik aktual. Auto hitung *difference* (Waste).
  - **Step 2 (Cash Count)**: Menampilkan estimasi uang sistem di laci (`expected_cash`). Kasir mengetik total uang fisik yang dihitungnya. Hitung selisih laci.
  - **Post-Tutup Aksi**: Saat dikonfirmasi, *shift* ditutup. (Di iterasi berikutnya, bisa diintegrasikan ke tabel arus kas/buku kas utama toko).
