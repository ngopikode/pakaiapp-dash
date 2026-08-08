# Rencana Perbaikan: Master Data Pre-Order (V2 UI/UX)

> **Tujuan:** Membuat halaman pengaturan Delivery Zones & Delivery Slots terasa seperti *Native App* dengan responsivitas super tinggi menggunakan Livewire 4 Islands dan Bottom Sheet UI.

---

## 1. UI Responsif (Bottom Sheet di Mobile)

**Masalah saat ini:** Di mobile, panel list dan panel form tampil berjejer/menumpuk, yang bisa terlihat berantakan jika data banyak.

**Solusi:**
- Di **Desktop (`md:`)**: Tetap menggunakan layout dua kolom (`flex-row`). Kiri: Daftar Item, Kanan: Form Tambah/Edit.
- Di **Mobile**: 
  - Tampilan utama hanya menunjukkan *Daftar Item*.
  - Ketika item diklik (Edit) atau tombol "Tambah Baru" ditekan, Form akan muncul dari bawah layar menggunakan efek **Bottom Sheet** (Slide-Up) via Alpine.js.
  - Tambahkan overlay blur di latar belakang untuk menyoroti Bottom Sheet form.
  - Tambahkan tombol "X" atau area tarik (drag handle) di atas Bottom Sheet agar mudah ditutup.

---

## 2. Livewire Islands (Isolasi Render)

**Masalah saat ini:** Halaman yang panjang bisa mengalami performa lambat jika terus-menerus di-*re-render* secara penuh.

**Solusi (Sesuai `PATTERNS.md` & `islands.md`):**
- **Daftar Zona/Slot (`@island(name: 'list', defer: true)`)**: Area daftar di kiri akan dibungkus dengan Island. Akan memuat setelah halaman selesai *paint*, menampilkan `@placeholder` skeleton (animasi pulse) sementara data dimuat dari database.
- Hal ini membuat form bisa tetap berjalan murni di Alpine.js (zero-roundtrip), sementara list di-render ulang oleh Livewire **hanya jika** ada perubahan data (seperti berhasil Save atau Hapus) dengan trigger dari `$wire.$island('list').$refresh()`.

---

## 3. Custom Alert Deletion (Anti Browser-Native)

**Masalah saat ini:** Penggunaan `wire:confirm` bawaan Livewire memicu `window.confirm()` yang tampilannya kaku dan berbeda-beda tiap OS (iOS, Android, Windows).

**Solusi:**
- Membuang `wire:confirm="Hapus zona ini?"`.
- Menggunakan **SweetAlert2** (`Swal.fire`) yang sudah terinstal secara global di `app.js` (`window.Swal`).
- Event Alpine.js: 
```javascript
window.Swal.fire({
    title: 'Hapus Area?',
    text: 'Aksi ini tidak bisa dikembalikan!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#64748b',
    confirmButtonText: 'Ya, Hapus!',
    cancelButtonText: 'Batal',
    background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#ffffff',
    color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#0f172a'
}).then((result) => {
    if (result.isConfirmed) {
        $wire.delete(id);
    }
});
```
- Hal ini menjamin konfirmasi hapus terlihat modern, premium, dan bisa menyesuaikan dengan *Dark Mode*.

---

## Rangkuman File yang Akan Diubah

| File | Ubahan Utama |
|---|---|
| `delivery-zones.blade.php` | Konversi Island, Bottom Sheet UI, Swal Delete |
| `delivery-zones.php` | Pemisahan scope Island jika perlu, refaktor method save/delete |
| `delivery-slots.blade.php` | Konversi Island, Bottom Sheet UI, Swal Delete |
| `delivery-slots.php` | Pemisahan scope Island jika perlu, refaktor method save/delete |

---

**Status:** ⏳ Planned
**Estimasi Pengerjaan:** 1 Sesi