# ADR: UX Redesign Modal Buka/Tutup Shift Kasir

**Tanggal:** 2026-08-04  
**Status:** Planned  
**Target File:** 
- `resources/views/pages/tenant/pos/partials/_modal-open-shift.blade.php`
- `resources/views/pages/tenant/pos/partials/_modal-close-shift.blade.php`

---

## Konteks & Masalah UX Saat Ini

Berdasarkan analisis `ui-ux-pro-max`, modal pembukaan dan penutupan shift saat ini memiliki dua masalah utama:

1. **Input Format yang Rawan Error:** Penggunaan `<input type="number">` untuk memasukkan nominal uang adalah *anti-pattern* pada software kasir (POS). Kasir melihat angka raw seperti "1000000" yang sangat membingungkan secara visual dibandingkan "1.000.000". Ini memicu kesalahan *fat-finger* (kurang/lebih angka nol).
2. **Visual Hierarchy yang Lemah:** Modal terasa *generic*. Nominal uang yang merupakan data krusial justru ditampilkan dengan ukuran font kecil (`text-sm`) seperti input form biasa. 

---

## Solusi Redesain & Format Rupiah

### 1. Alpine.js Currency Masking (Real-Time Formatting)
Kita akan meninggalkan `type="number"` dan `wire:model` langsung. Sebagai gantinya, kita gunakan `type="text" inputmode="numeric"` yang dikontrol penuh oleh AlpineJS:

```js
// Konsep Alpine component untuk input uang
x-data="{ 
    displayValue: '', 
    format(val) { 
        let num = val.toString().replace(/\D/g, ''); 
        this.displayValue = num ? new Intl.NumberFormat('id-ID').format(num) : '';
        $wire.set('startingCash', num); // Sync ke Livewire
    } 
}"
```

### 2. UI Typography & Layout (UI Pro Max Standard)
- **Input Uang Jumbo:** Input field akan dibuat mencolok: teks di-center, ukuran besar (`text-3xl`), font tebal (`font-black`), dengan padding lega.
- **Prefix Statis:** Menampilkan lambang `Rp` secara statis dan terpisah secara desain di dalam input field agar user langsung tahu konteksnya.
- **Quick-fill Buttons (Buka Shift):** Menambahkan tombol nominal cepat di bawah input (misal: [0], [100rb], [500k], [1Juta]) untuk 1-click fill.
- **Step Visual (Tutup Shift):** Mempertegas visualisasi form tutup shift (Step 1 dan Step 2).

---

## Task Breakdown (Tahapan Eksekusi)

**Phase 1: Redesain Modal Open Shift**
- [ ] Ganti `<input type="number" wire:model="startingCash">` menjadi `<input type="text" x-model="displayValue" @input="...">`.
- [ ] Tambahkan wrapper Alpine `x-data` untuk logika masking dan sync dengan `$wire`.
- [ ] Perbesar ukuran text input ke `text-3xl font-black text-center`.
- [ ] Tambahkan barisan *chip buttons* (Quick Fill) untuk mengisi nominal Rp 0, 100k, 500k, 1Juta.

**Phase 2: Redesain Modal Close Shift (Z-Report)**
- [ ] Terapkan teknik masking Alpine yang sama pada input `actualCash` di Step 2.
- [ ] Perbesar ukuran text input.
- [ ] (Opsional) Terapkan format yang sama di `_modal-shift-expense.blade.php`.
