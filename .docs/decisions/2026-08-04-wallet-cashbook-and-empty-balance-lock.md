# ADR: Peningkatan Keamanan Billing & Halaman Cashbook

**Tanggal:** 2026-08-04  
**Status:** Planned  
**Target Fitur:** Modul Keuangan (Wallet) & Keamanan Billing Sistem

---

## Konteks & Latar Belakang

Berdasarkan dokumen `wallet-refactor/plan.md`, tabel `wallets` telah di-upgrade dari *single-purpose* menjadi **4 tipe dompet**: `billing`, `cash`, `bank`, dan `gateway`.

Namun, saat ini ada dua celah besar:
1. **Kebocoran App Fee:** Bisnis PakaiApp memotong saldo dari dompet `billing`. Jika saldo `billing` habis (Rp 0), tenant masih bisa menerima order, yang menyebabkan kerugian sistem (hutang akumulatif). Sistem harus mengunci aplikasi jika saldo billing kosong.
2. **Ketiadaan Fitur Pencatatan Operasional:** Tenant butuh sarana mencatat pemasukan/pengeluaran manual (seperti uang kasbon karyawan, beli beras, listrik) untuk arus kas di `cash` (laci) dan `bank` (rekening).

**Catatan Khusus tentang Halaman Wallet Eksisting:**
Halaman Wallet saat ini (`/wallet`) yang memiliki *Virtual Premium Wallet Card* UI dibiarkan persis seperti aslinya. Tidak ada perombakan UI "4-Cards" yang dipaksakan. Halaman itu tetap berfungsi sebagai UI utama. 

---

## Keputusan & Rencana Implementasi

### 1. Global Lock Modal (Saldo Billing Habis)
Sistem harus dikunci ketika dompet tipe `billing` habis.
- **Frontend (Global Layout):**
  Pengecekan *read-only* saldo `billing` di `layouts/app.blade.php`. Jika saldonya `<= 0`, render *Full Screen Overlay Modal* tanpa tombol *close*.
  Isi modal: *"Aplikasi Terkunci. Saldo Deposit Pakaiapp Anda Habis."* beserta tombol *Top Up*. Modal ini tidak akan muncul jika pengguna sedang berada di halaman `/wallet`, `/login`, `/logout`, atau `/profile`.
- **Backend Security Guard:**
  Pencegatan di `OrderService::processOrder()`. Jika `wallet(TYPE_BILLING)->balance <= 0`, lempar *Exception* sehingga transaksi ditolak dari sisi server.

### 2. Halaman Cashbook (Pencatatan Operasional)
Sesuai standar penamaan profesional, modul baru akan menggunakan *English nomenclature* di tingkat *codebase*.
- **Lokasi Komponen MFC:** `resources/views/pages/tenant/finance/⚡cashbook/` (Berisi `cashbook.php` dan `cashbook.blade.php`).
- **Route:** `Route::livewire('cashbook', 'pages::tenant.finance.cashbook')->name('cashbook');`
- **Aliran Dana:** Saat form disimpan, sistem memanggil `TenantWalletService` (`addBalance` atau `deductBalance`) untuk tipe dompet `cash` atau `bank`.
- **UI & UX:**
  - Halaman ini memuat tabel riwayat dari model `WalletTransaction` khusus tipe `cash` dan `bank`.
  - Dilengkapi tombol "Add Transaction" yang membuka modal input.
  - Modal form menggunakan *Jumbo Input* dengan fitur *Real-time Rupiah Masking* AlpineJS untuk menghindari salah ketik angka, mirip dengan modal POS Shift.

---

## Task Breakdown (Tahapan Eksekusi)

**Fase 1: Security & Global Lock Modal (SELESAI)**
- [x] Ubah `layouts/app.blade.php` untuk pengecekan read-only `$isBillingEmpty`.
- [x] Buat file modal overlay `resources/views/layouts/_partials/_billing-lock-modal.blade.php`.
- [x] Implementasikan blokir transaksi (Throw Exception jika <= 0) pada `OrderService`.

**Fase 2: Pembuatan Halaman Cashbook**
- [ ] Buat file `cashbook.php` dan `cashbook.blade.php` di dalam `resources/views/pages/tenant/finance/⚡cashbook/`.
- [ ] Rancang UI form *Add Transaction* dengan *Jumbo Input Rupiah Masking* AlpineJS.
- [ ] Buat tabel riwayat `WalletTransaction` (difilter hanya tipe `cash` dan `bank`).
- [ ] Daftarkan route `cashbook` ke `routes/tenant.php`.
- [ ] Tambahkan tautan menu `cashbook` ke `sidebar.php` dan `mobile-menu.php`.
