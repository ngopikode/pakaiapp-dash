# Fitur: Pengaturan Area & Slot Waktu (Master Data)

> Lanjutan dari fitur Pre-Order (Fase 5)
> Referensi: [Project Map](../../project-map.md)

Dokumen ini merencanakan pembuatan antarmuka (UI) untuk Master Data Pengiriman Terjadwal, yaitu **Slot Waktu Pengiriman** (DeliverySlot) dan **Zona Ongkos Kirim** (DeliveryZone).

Saat ini tabelnya sudah ada dan digunakan oleh engine Pre-Order, namun merchant masih belum bisa menambahkan/menghapus Slot & Zona melalui antarmuka web.

---

## Cakupan Fitur

1. **Modal Manajemen Zona/Area Ongkir**
   - Livewire Component untuk CRUD `DeliveryZone`.
   - Form field: Nama Area, Ongkir, Minimal Belanja Gratis Ongkir, dan Status Aktif.
2. **Modal Manajemen Slot Waktu Pengiriman**
   - Livewire Component untuk CRUD `DeliverySlot`.
   - Form field: Nama Slot (Pagi/Siang), Jam Mulai, Jam Selesai, Kuota Maksimal, dan Status Aktif.
3. **Integrasi UI di Pengaturan Toko**
   - Menambahkan tombol pemicu modal di dalam `store-setting.blade.php`.
   - Menyediakan counter dinamis untuk "Zona Aktif" dan "Slot Aktif" di halaman pengaturan.
4. **Pembaruan UX Checkout Mode WA**
   - Menyempurnakan Tampilan Sukses Checkout WA di `checkout-modal.blade.php`.
   - Menampilkan tombol "Lanjut ke WhatsApp" beserta ikon yang elegan alih-alih menutup modal secara mendadak.
   - Menyelesaikan *glitch* karakter (ikon keranjang yang ter-encode) pada format URL WhatsApp.

---

## Langkah Implementasi

### 1. DTO & Service (Validasi)

- Buat `DeliveryZoneData` dan `DeliverySlotData` di `app/Tenant/Data`.
- Buat `DeliverySettingService` (atau ekspansi `PreOrderService`) dengan method untuk menyimpan dan menghapus model (Soft Delete/Deactivate).

### 2. Livewire Modals (UI)

- Buat `delivery-zone-modal.php` (dengan view `delivery-zone-modal.blade.php`).
- Buat `delivery-slot-modal.php` (dengan view `delivery-slot-modal.blade.php`).
- Pola UI: Daftar data di tabel/list, di bawahnya terdapat Form untuk Tambah Baru / Edit (Bisa melihat `category-modal` sebagai referensi).

### 3. Store Settings Integration

Di file `store-setting.blade.php`, pada section "PENGIRIMAN TERJADWAL (PRE-ORDER)":

```html
<div class="flex gap-4">
    <button wire:click="$dispatch('openModal', 'delivery-zone-modal')" class="btn">
        <i class="ph ph-map-pin"></i> Atur Zona Ongkir (3 Aktif)
    </button>
    <button wire:click="$dispatch('openModal', 'delivery-slot-modal')" class="btn">
        <i class="ph ph-clock"></i> Atur Slot Waktu (2 Aktif)
    </button>
</div>
```

### 4. Perbaikan `waUrl` dan Success State

Di `store.js` & `checkout-modal.blade.php`, alih-alih melakukan `window.open(waUrl)` langsung dan menutup form:
- Simpan `waUrl` ke dalam object `orderSuccess` (bersama dengan `invoiceCode`).
- Tampilkan blok `<template x-if="orderSuccess">` yang khusus untuk WA Checkout (bisa digabung/dibedakan dari sukses POS Manual).
- Berikan tombol besar dengan icon WA: **"Kirim Pesanan ke WhatsApp"** yang memiliki `<a :href="orderSuccess.waUrl" target="_blank">`. 
- Ubah format payload di PHP: pastikan `rawurlencode()` digunakan dengan benar, dan jangan me-*rawurlencode* karakter spesial emoji secara ganda jika frontend tidak bisa meloadnya.

---

**Status:** ✅ Shipped
**Estimasi Pengerjaan:** 1 Sesi
