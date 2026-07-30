# Inventory Shift — Phase 3 UI/UX Flow Plan

> Referensi: [Plan Utama](./plan.md) · [Livewire 4 Features](../../references/livewire4/PATTERNS.md) · [UI Standards](../../features/ui-standards/sidebar-visual-standards.md)

Dokumen ini merinci implementasi Phase 3 untuk alur *frontend* (Kasir) terkait dengan Sesi Shift, Stock Opname, dan Z-Report.

---

## Scope Phase 3

Phase 3 menangani antarmuka pengguna pada sistem Point of Sale (POS) agar selaras dengan status `shifts` di *backend*. 

**Catatan Khusus:** Pada fase ini, implementasi HANYA difokuskan pada Kasir F&B (`resto-cashier`). Integrasi untuk `retail-cashier` ditunda ke iterasi berikutnya.

Termasuk:
- Komponen Livewire modal/halaman "Buka Shift" (Resto)
- Modifikasi komponen `resto-cashier` untuk menerapkan *System Lock* jika kasir belum membuka shift
- Pembuatan fitur pencatatan Pengeluaran Laci (Petty Cash / `shift_expenses`) di antarmuka resto
- Pembuatan alur "Tutup Shift & Opname" menggunakan 2-Step Form
- Integrasi *toggle* `is_shift_active` pada halaman *Store Setting*

Tidak termasuk:
- Integrasi *System Lock* dan Sesi Shift pada `retail-cashier`.
- Refactor Buku Kas Utama / Laporan Dompet Digital (Bagian dari Modul Arus Kas).

---

## Keputusan Arsitektur & Livewire 4 Standard

### 1. Pola System Lock (Buka Shift)

**Masalah:** Bagaimana mencegah kasir memasukkan pesanan jika belum "Buka Shift"?
**Keputusan:** 
Menggunakan *Computed Property* di komponen `resto-cashier`.
- Tambahkan `#[Computed]` untuk `activeShift()` yang melakukan *query* ke tabel `shifts`.
- Di level Blade, gunakan `@if(!$this->activeShift)` untuk menyembunyikan *Cart* dan Daftar Produk, lalu menampilkan komponen penutup layar penuh (Modal Buka Shift).
- Penggunaan `wire:submit` untuk mengirim `starting_cash` (Modal Laci) ke *Service* melalui DTO.

### 2. Form Opname & Tutup Shift (Z-Report)

**Pola Formulir (Livewire 4):**
- Pisahkan logika *Tutup Shift* ke dalam satu form object terpisah atau Livewire Component mandiri agar komponen POS utama tidak membengkak.
- Terapkan *Blind Close* pada Step 2 (Uang Kasir): Nominal *expected_cash* (jumlah yang seharusnya) tidak ditampilkan ke kasir. Kasir murni mengisi form berapa lembar/uang fisik yang ada. *Backend* yang bertugas mengalkulasi `difference`.

**Query Item Kritis (Stock Opname):**
- Jangan me-*load* seluruh produk. Query hanya memanggil bahan baku (`RawMaterial`) di mana `is_critical = true`. (Varian produk retail diabaikan di fase ini).

### 3. Penggunaan UI Standards (Tailwind)
- Tampilan Modal Buka/Tutup Shift akan mengikuti gaya *sliding panel* / modal tengah yang sudah ada di aplikasi.
- Indikator Shift Aktif: Jika Shift aktif, tampilkan tombol kecil di area *header/navbar* bertuliskan "Shift Aktif: [Nama] (Mulai: [Jam])".

---

## Fase Implementasi & Modifikasi Berkas

### A. Pengaturan Tenant (Settings)
- **Target File:** `resources/views/pages/tenant/setting/⚡store-setting/store-setting.php` & `blade.php`
- **Tindakan:** Tambahkan *toggle* (checkbox) untuk properti `$isShiftActive`. Sinkronisasikan penyimpanannya ke dalam DTO `StoreSettingFormData`.

### B. Pembuatan DTO & Service (Shift)
- **Target File:** `app/Tenant/Data/ShiftClosingData.php`, `app/Tenant/Services/ShiftService.php`
- **Tindakan:** Membangun lapisan *Service* agar logika pembuatan *StockOpname*, pembuatan *ShiftExpense*, dan pembaruan `Shift` terpusat. Metode *Controller*/*Livewire* akan sangat tipis sesuai pola *Thin Controller*.
- **Penting:** Pastikan saat Z-Report/Tutup Shift sukses (Status `closed`), panggil `TenantWalletService::addBalance(..., Wallet::TYPE_CASH)` untuk mentransfer jumlah `actual_cash` ke Buku Kas.

### C. Proteksi POS Resto (Cashier)
- **Target File:** `resources/views/pages/tenant/pos/⚡resto-cashier/resto-cashier.php` & `blade.php`
- **Tindakan:**
  - Injeksi `$storeSetting->is_shift_active`.
  - Tambahkan properti/computed `$activeShift`.
  - Jika `is_shift_active && !$activeShift`, sembunyikan layar produk dan panggil blok/komponen `<livewire:pages::tenant.pos.shift-opener />`.
  
### D. Modul Buka, Pengeluaran & Tutup Shift (UI)
- **Pembuatan File:** `resources/views/pages/tenant/pos/partials/_modal-open-shift.blade.php`
- **Pembuatan File:** `resources/views/pages/tenant/pos/partials/_modal-close-shift.blade.php` (dengan tab Step 1 Opname & Step 2 Kas Fisik).
- **Pembuatan File:** `resources/views/pages/tenant/pos/partials/_modal-shift-expense.blade.php`
- **Tindakan (Livewire):** Menyiapkan aksi PHP di `resto-cashier.php` (atau komponen child) untuk menangani input dari modal-modal tersebut, dikirim langsung ke `ShiftService`.

---

## Guardrails (Penjaga Kualitas)
- **No Over-fetching:** Saat opname, jangan panggil relasi/produk yang tidak relevan. Cukup id, nama, satuan, dan stok sistem saat ini dari `RawMaterial`.
- **Islands/Lazy:** Komponen Tutup Shift cukup kompleks, hindari *heavy query* saat inisialisasi awal. Ambil data opname hanya saat modal akan dibuka.
- **Named Arguments:** Semua metode di `ShiftService` wajib menggunakan argumen bernama jika parameternya banyak.
