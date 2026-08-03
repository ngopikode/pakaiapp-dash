# ADR: Implementasi Shift Summary (Z-Report) Post-Close

**Tanggal:** 2026-08-04  
**Status:** Planned  
**Komponen:** `RestoCashier` (Livewire) & `ShiftService`

---

## Konteks & Keputusan Bisnis

Pada sistem kasir (POS), terdapat skenario di mana uang fisik di laci kasir tidak sesuai dengan catatan penjualan sistem (bisa kurang/tekor atau lebih). 

**Keputusan:**
1. **Sistem TIDAK memblokir** penutupan shift jika terjadi selisih (minus/plus). Shift harus bisa ditutup agar kasir shift berikutnya bisa masuk.
2. **Sistem mempertahankan *Blind Count***. Kasir tidak diberitahu "Expected Cash" (uang yang seharusnya ada) *sebelum* shift ditutup. Ini mencegah manipulasi uang laci oleh kasir.
3. Selisih (Difference) baru akan ditampilkan **SETELAH** shift resmi ditutup dan dikunci di database.

---

## Rencana Implementasi: Post-Close Z-Report Modal

Untuk memfasilitasi keputusan di atas, kita perlu membangun alur akhir penutupan shift:

### 1. Backend (`resto-cashier.php`)
- `submitCloseShift()` akan menerima instance `Shift` yang dikembalikan oleh `ShiftService::closeShift()`.
- Properti baru `$closedShiftSummary` akan dibuat untuk menampung data shift yang baru saja ditutup.
- Setelah sukses, bukan sekadar memunculkan toast, komponen akan memicu event JS `show-shift-summary` untuk membuka modal baru.

### 2. UI / Frontend (`_modal-shift-summary.blade.php`)
- Buat partial modal baru menggunakan panduan desain `ui-ux-pro-max`.
- **Informasi yang ditampilkan:**
  - Identitas: Nama Kasir, Waktu Buka, Waktu Tutup.
  - Aliran Kas: Modal Awal, Penjualan Tunai, Pengeluaran.
  - Hasil Hitung: Uang Diharapkan (Sistem) vs Uang Aktual (Fisik).
  - **Selisih (Difference):** Merah mencolok jika minus (Tekor), Hijau jika pas/lebih.
- **Aksi:** Tombol "Selesai & Kunci Kasir" (Opsional: Tombol "Print Struk Z-Report" jika printer terhubung).

### 3. Alur Kerja (Flow)
1. Kasir input stok & uang aktual (Blind Count).
2. Klik "Konfirmasi Tutup Shift".
3. Data dikirim ke server → Shift dikunci, selisih dihitung, masuk DB.
4. Server return data → Modal Z-Report Summary muncul.
5. Kasir melihat hasilnya (termasuk tekor/tidaknya).
6. Kasir klik "Selesai" → Modal tutup → Halaman kasir terkunci (gembok).

---

## Catatan Masa Depan
Modal Z-Report ini akan menjadi titik integrasi untuk fitur cetak struk rekap (Thermal Printer) di pengembangan fase berikutnya.
