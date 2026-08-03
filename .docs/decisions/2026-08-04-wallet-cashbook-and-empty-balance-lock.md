# ADR: Peningkatan Dompet, Buku Kas Baru, dan Global Lock Saldo

**Tanggal:** 2026-08-04  
**Status:** Planned  
**Target Fitur:** Modul Keuangan (Wallet) & Keamanan Billing Sistem

---

## Konteks & Latar Belakang

Berdasarkan dokumen `wallet-refactor/plan.md`, tabel `wallets` telah di-upgrade dari *single-purpose* (hanya deposit) menjadi **4 tipe dompet**:
1. `billing` (Deposit Pakaiapp untuk potongan fee transaksi)
2. `cash` (Uang fisik di laci/toko hasil setoran Z-Report)
3. `bank` (Uang non-tunai yang bypass langsung masuk rekening tenant)
4. `gateway` (Uang di *payment gateway* seperti Midtrans/Duitku)

Walaupun backend/Service sudah mendukung ke-empat tipe ini, antarmuka pengguna (UI) saat ini:
1. Halaman Wallet (`pages/tenant/finance/wallet`) masih berfokus hanya pada saldo lama (Billing), membingungkan pengguna yang ingin melihat Buku Kas (Laci & Bank).
2. Tidak ada sarana (form UI) bagi tenant untuk mencatat pemasukan/pengeluaran manual (seperti uang kasbon karyawan, beli beras, dll) yang mana ini esensial dalam operasional *Buku Kas*.

Selain itu, karena bisnis PakaiApp bergantung pada pemotongan saldo dari dompet `billing` (melalui `BillingService`), muncul risiko *minus balance*. Jika saldo `billing` tenant habis (Rp 0), tenant masih bisa menikmati aplikasi dan berjualan, yang menyebabkan akumulasi hutang biaya transaksi.

---

## Keputusan & Rencana Implementasi

### 1. Global Lock Modal (Saldo Billing Habis)
Untuk menghentikan kebocoran biaya aplikasi, sistem harus dikunci ketika dompet tipe `billing` habis.

- **Frontend (AlpineJS Global & Layout):**
  Pengecekan saldo `billing` dilakukan di Layout utama aplikasi (`layouts/app.blade.php`). Jika saldonya `<= 0`, render komponen *Full Screen Overlay* ber-z-index ekstrem (`z-[99999]`) yang menutupi seluruh layar tanpa tombol *close*.
  Isi modal: *"Saldo Deposit Pakaiapp (Billing) Anda Habis. Aplikasi terkunci sementara. Silakan isi ulang saldo (Top Up) untuk melanjutkan berjualan."*
  Modal ini dirancang interaktif di Alpine (bisa di-refresh) agar saat proses Top Up selesai, modal bisa hilang tanpa *refresh* page penuh.
  
- **Backend Security:**
  Mencegat proses *Order Creation* di `OrderService` (atau middleware API). Jika `wallet(TYPE_BILLING)->balance <= 0`, lempar *Exception* yang akan membatalkan seluruh operasi. Fitur ini memastikan tenant yang mahir *Inspect Element* pun tetap tidak bisa mem-*bypass* sistem penguncian.

### 2. Fitur Halaman Buku Kas (Cash Book)
- **Modul Baru:** Buat komponen Livewire baru `pages/tenant/finance/buku-kas/buku-kas.php`.
- **Fungsi Utama:** Melakukan pencatatan operasional (Income / Expense).
- **Aliran Dana:** Saat form disimpan, sistem akan memanggil `TenantWalletService` dan melakukan operasi `addBalance` atau `deductBalance` pada tipe dompet yang dipilih (pilihan dibatasi hanya pada `cash` atau `bank` — karena `billing` dikontrol admin/top up, dan `gateway` dikontrol API bank).
- UI menggunakan standar F&B dengan tabel mutasi (`wallet_histories`) khusus tipe `cash` dan `bank`.

### 3. Redesain Halaman Wallet & Saldo (Overview)
- Refactor halaman `wallet` saat ini (jika sudah ada) menjadi *Dashboard Keuangan*.
- Tampilkan 4 Kartu Saldo (Cards) untuk:
  - Saldo Deposit (`billing`)
  - Saldo Laci Kasir (`cash`)
  - Saldo Rekening/QRIS (`bank`)
  - Saldo Pending/Gateway (`gateway`)
- Tabel riwayat (histories) akan dimodifikasi dengan *filter tab* untuk melihat mutasi secara terpisah per jenis dompet.

---

## Task Breakdown (Tahapan Eksekusi)

**Fase 1: Security & Global Lock Modal**
- [ ] Ubah `layouts/app.blade.php` atau `Layout Controller` untuk menyuplai boolean `$isBillingEmpty` ke Alpine `app.js`.
- [ ] Buat file `resources/views/layouts/_partials/_billing-lock-modal.blade.php`.
- [ ] Implementasikan blokir transaksi (Throw Exception jika <= 0) pada `OrderService`.

**Fase 2: Pembuatan Halaman Buku Kas**
- [ ] Buat folder dan file Livewire komponen untuk `BukuKas`.
- [ ] Rancang UI Buku Kas sesuai panduan `ui-ux-pro-max` (Jumbo input nominal, masking *real-time*).
- [ ] Panggil `TenantWalletService` dari metode simpan komponen.

**Fase 3: Pembaruan Wallet Dashboard**
- [ ] Tambahkan query pemanggilan semua jenis saldo di halaman *Wallet*.
- [ ] Tata letak UI menjadi 4 kartu metrik utama.
- [ ] Implementasikan *filter histories* berdasarkan tipe dompet.