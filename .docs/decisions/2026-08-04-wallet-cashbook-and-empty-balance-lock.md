# ADR: Peningkatan Dompet, Buku Kas Baru, dan Global Lock Saldo

**Tanggal:** 2026-08-04  
**Status:** Planned  
**Target Fitur:** Modul Keuangan (Wallet) & Keamanan Billing Sistem

---

## Konteks & Latar Belakang

Seiring dengan meningkatnya aktivitas transaksi penyewa (tenant), pencatatan arus kas (cashflow) menjadi fitur krusial. 
Saat ini sistem telah memiliki pondasi 4 tipe dompet (`billing`, `cash`, `bank`, `gateway`), namun antarmuka pengelolaannya masih belum memadai. 

Selain itu, model bisnis aplikasi membebankan biaya transaksi (App Fee) yang dipotong langsung dari saldo *Billing Wallet* tenant. Masalahnya, jika saldo billing habis, tenant masih bisa terus menerima pesanan dan menggunakan fitur POS, yang dapat berujung pada akumulasi hutang sistem (minus balance) yang berisiko.

Tiga kebutuhan utama yang akan diselesaikan:
1. **Perbaikan Halaman Wallet:** Memperjelas informasi saldo dari keempat tipe dompet dan riwayat transaksinya.
2. **Halaman Baru Buku Kas:** Menyediakan fitur pencatatan kas manual (pemasukan/pengeluaran operasional di luar order POS) seperti bayar tagihan listrik, kasbon karyawan, dll.
3. **Global Lock Modal:** Mencegat penggunaan fitur inti aplikasi jika saldo Wallet Billing habis, memaksa tenant melakukan Top Up.

---

## Rencana Implementasi

### 1. Global Lock Modal (Saldo Habis) di `app.js`
- **Frontend (AlpineJS Global):**
  Akan dibuat sebuah state global (misalnya di layout utama) yang mengecek status saldo billing. Jika saldo habis (<= 0), sebuah modal *Full Screen Overlay* ber-z-index tertinggi (`z-[9999]`) akan muncul dan memblokir layar. Modal ini tidak memiliki tombol *close/batal*.
  Isi modal: *"Saldo Tagihan (Billing) Anda Habis. Aplikasi terkunci sementara. Silakan isi ulang saldo (Top Up) untuk melanjutkan."* beserta tombol **Top Up**.
- **Backend Security:** 
  Pengecekan di layer Service (`OrderService`) dan Controller untuk memblokir penambahan pesanan (`processOrder`) jika saldo billing kurang dari fee yang ditentukan, agar tenant tidak bisa melakukan *bypass* lewat manipulasi DOM atau API.

### 2. Pembuatan Halaman Buku Kas (Cash Book)
- Buat modul baru `pages/tenant/finance/buku-kas`.
- Halaman ini akan menjadi pusat pencatatan mutasi di luar penjualan (seperti pembelian bahan baku non-sistem, biaya sampah, gaji, dsb).
- Form input manual dengan tipe: **Pemasukan** dan **Pengeluaran**.
- Pengguna wajib memilih *sumber/tujuan* dompet (`cash` atau `bank`).
- Data yang diinput akan memanggil `TenantWalletService` untuk secara otomatis menyesuaikan (`addBalance` atau `deductBalance`) dompet yang dipilih, agar rekonsiliasi keuangan akurat.

### 3. Peningkatan Halaman Dompet (Wallet Page)
- Redesain halaman Wallet (yang sudah ada) menggunakan prinsip `ui-ux-pro-max`.
- Tampilkan 4 kartu (cards) informatif di bagian atas untuk saldo: Billing, Cash (Laci), Bank (Transfer), dan Gateway (Midtrans/Duitku).
- Sediakan tabel riwayat transaksi tersentralisasi yang difilter dengan tabs per tipe dompet.

---

## Task Breakdown (Tahapan Eksekusi)

**Fase 1: Security & Global Lock Modal**
- [ ] Modifikasi `layouts/app.blade.php` untuk membaca saldo *Billing Wallet* tenant saat ini.
- [ ] Buat komponen modal overlay di `_partials/_wallet-lock-modal.blade.php` yang terintegrasi dengan AlpineJS.
- [ ] Pindahkan trigger atau validasi ke `app.js` / Alpine global state agar dapat muncul dari halaman manapun.
- [ ] Tambah proteksi `BillingService` atau `OrderService` di sisi PHP agar menolak transaksi baru jika saldo <= 0.

**Fase 2: Modul Buku Kas**
- [ ] Generate Livewire component `CashBook` (Controller & Blade).
- [ ] Bangun UI Buku Kas dengan tabel riwayat pencatatan manual.
- [ ] Terapkan DTO `CashBookEntryData` untuk memproses inputan.

**Fase 3: Redesain Halaman Wallet**
- [ ] Refactor tampilan `Wallet` komponen saat ini.
- [ ] Hubungkan riwayat transaksi dari tabel `wallet_histories`.