# ADR: Peningkatan Keamanan Billing & Halaman Cashbook

**Tanggal:** 2026-08-04  
**Status:** In Progress (Revisi Strategi Lock & Standardisasi Architecture)  
**Target Fitur:** Modul Keuangan (Wallet) & Keamanan Billing Sistem

---

## Konteks & Latar Belakang

Berdasarkan dokumen `wallet-refactor/plan.md`, tabel `wallets` telah di-upgrade dari *single-purpose* menjadi **4 tipe dompet**: `billing`, `cash`, `bank`, dan `gateway`.

Namun, saat ini ada dua celah besar:
1. **Kebocoran App Fee:** Bisnis PakaiApp memotong saldo dari dompet `billing`. Jika saldo `billing` habis (Rp 0), tenant masih bisa menerima order, yang menyebabkan kerugian sistem (hutang akumulatif). Sistem harus menghentikan kemampuan bertransaksi jika saldo billing kosong.
2. **Ketiadaan Fitur Pencatatan Operasional:** Tenant butuh sarana mencatat pemasukan/pengeluaran manual (seperti uang kasbon karyawan, beli beras, listrik) untuk arus kas di `cash` (laci) dan `bank` (rekening).

**Catatan Khusus tentang Halaman Wallet Eksisting:**
Halaman Wallet saat ini (`/wallet`) yang memiliki *Virtual Premium Wallet Card* UI dibiarkan persis seperti aslinya. Tidak ada perombakan UI "4-Cards" yang dipaksakan. Halaman itu tetap berfungsi sebagai UI utama. 

---

## Keputusan & Rencana Implementasi

### 1. Pembatasan Transaksi (Pengganti Global Lock)
*Revisi Arsitektur (Standar `laravel13/PATTERNS.md` section 1): Pengecekan saldo (DB Query) secara global di Blade (`layouts/app.blade.php`) melanggar pola "Blade tidak boleh resolve service/model" dan boros.*
- **Frontend (Tingkat Kasir/POS):**
  Pengecekan saldo `billing` dipindahkan ke komponen spesifik (Livewire layer). Jika saldonya `<= 0`:
  - Kasir ditolak saat mencoba **Open Shift** (Validasi Backend di Livewire + Modal UI).
  - Tampilkan modal/overlay pemblokiran hanya dari dalam komponen Kasir.
- **Backend Security Guard (Sudah Diterapkan):**
  Pencegatan di `OrderService::processOrder()`. Jika `wallet(TYPE_BILLING)->balance <= 0`, exception dilempar sehingga transaksi mutlak ditolak dari server.

### 2. Halaman Cashbook (Pencatatan Operasional)
Sesuai standar penamaan profesional, modul baru akan menggunakan *English nomenclature* di tingkat *codebase*.
- **Lokasi Komponen MFC:** `resources/views/pages/tenant/finance/⚡cashbook/` (Berisi `cashbook.php` dan `cashbook.blade.php`).
- **Route:** `Route::livewire('cashbook', 'pages::tenant.finance.cashbook')->name('cashbook');`
- **Data Boundary (Sesuai `laravel13/PATTERNS.md` section 3):**
  Komponen Cashbook **HARUS** mengirim form data via DTO (misal: `CashbookTransactionData`), bukan array mentah ke `TenantWalletService`.
- **UI & UX (Zero-Roundtrip Policy):**
  - Halaman ini memuat tabel riwayat dari model `WalletTransaction` khusus tipe `cash` dan `bank`.
  - Dilengkapi tombol "Add Transaction" yang membuka modal input.
  - Modal form menggunakan *Jumbo Input* dengan fitur *Real-time Rupiah Masking* AlpineJS. **Tidak boleh** memakai `wire:model.live` untuk format angka (mematuhi Zero-Roundtrip UI `livewire4/PATTERNS.md`), format ditangani murni JS (`x-model`).

---

## Task Breakdown (Tahapan Eksekusi)

**Fase 1: Keamanan Transaksi (Backend) & UI Cashbook Awal**
- [x] Implementasikan blokir transaksi (Throw Exception jika <= 0) pada `OrderService`.
- [x] Buat struktur MFC `cashbook` (`cashbook.php` & `cashbook.blade.php`).
- [x] Rancang UI form *Add Transaction* dengan *Jumbo Input Rupiah Masking* AlpineJS (Client-side).
- [x] Buat tabel riwayat `WalletTransaction` (tipe `cash` dan `bank`).
- [x] Daftarkan route `cashbook` & tautan menu navigasi sidebar.

**Fase 2: Pemindahan Logika Global Lock (Revisi Standar Blade)**
- [ ] Hapus raw Eloquent Query `$billingWallet` dari `layouts/app.blade.php`.
- [ ] Pindahkan validasi pengecekan saldo `billing` ke aksi `openShift()` di komponen Livewire `resto-cashier` & `retail-cashier` atau pada global event pendukung.
- [ ] Render komponen visual peringatan (atau panggil event modal) dari komponen POS, bukan dari `app.blade.php`.

**Fase 3: Standardisasi DTO & Service Cashbook**
- [ ] Buat file DTO `app/Tenant/Data/CashbookTransactionData.php`.
- [ ] Update `cashbook.php` untuk mem-parsing array form input menjadi DTO sebelum memanggil `addBalance`/`deductBalance` di `TenantWalletService`.
- [ ] Validasi DTO bekerja harmonis dengan layer Service dan *Pessimistic Locking* wallet.
