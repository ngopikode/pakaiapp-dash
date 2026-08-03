# ADR: Refactor RestoCashier Component

**Tanggal:** 2026-08-04
**Status:** Planned
**File:** `resources/views/pages/tenant/pos/⚡resto-cashier/resto-cashier.php` + `resto-cashier.blade.php`

---

## Konteks

Selama sesi pengembangan fitur Shift Kasir (buka/tutup shift, sistem kunci kasir, shift controls di navbar), ditemukan 3 masalah di komponen `RestoCashier` yang perlu ditangani secara terstruktur.

---

## Masalah yang Ditemukan

### Bug 1 — Duplikat Kode Validasi Cart (Code Quality)

**Lokasi:** Baris 152–157 dan 195–200

Loop validasi cart dari JS diulang secara identik di dua method berbeda (`createOrder` dan `processDirectCheckout`):

```php
foreach ($cart as $item) {
    if (!isset($item['variant_id'], $item['quantity'])
        || !is_int($item['variant_id'])
        || !is_numeric($item['quantity'])
        || $item['quantity'] < 1
    ) {
        return ['success' => false, 'error' => 'Data keranjang tidak valid.'];
    }
}
```

**Dampak:** Jika validasi perlu diperketat (misal: tambah cek `price` atau `note` length), harus diubah di dua tempat — rawan lupa dan tidak konsisten.

**Fix:**
```php
private function validateCart(array $cart): ?array
{
    foreach ($cart as $item) {
        if (!isset($item['variant_id'], $item['quantity'])
            || !is_int($item['variant_id'])
            || !is_numeric($item['quantity'])
            || $item['quantity'] < 1
        ) return ['success' => false, 'error' => 'Data keranjang tidak valid.'];
    }
    return null;
}
```

Dipanggil di awal kedua method:
```php
if ($error = $this->validateCart($cart)) return $error;
```

---

### Bug 2 — DTO Dibuat Tapi Tidak Dipakai Sebagai Kontrak ke Service (PATTERNS.md #5 violation)

**Lokasi:** `createOrder()` baris 160–183, `processDirectCheckout()` baris 208–234

DTO `CreateOrderData` dan `CheckoutData` dibuat dengan benar, tapi lalu datanya langsung di-unpack ulang ke array `$orderData` sebelum dikirim ke service. Ini melanggar PATTERNS.md #5 yang mewajibkan DTO sebagai kontrak langsung antar layer.

```php
// SEKARANG (melanggar standar):
$dto = new CreateOrderData(customerName: ..., orderType: ...);
$orderData = [                          // ← DTO di-unpack ke array
    'customer_name' => $dto->customerName,
    'order_type'    => $dto->orderType,
    // ...
];
$this->orderService()->processOrder($orderData, $cart, $this->existingOrder);
//                                    ↑ array dikirim, bukan DTO

// SEHARUSNYA:
$dto = new CreateOrderData(...);
$this->orderService()->processOrder($dto, $cart, $this->existingOrder);
//                                   ↑ DTO langsung sebagai kontrak
```

**Dampak:** Service layer seharusnya tidak tahu bahwa data berasal dari Livewire — DTO adalah kontrak eksplisitnya. Dengan mengirim array, kita membuang type safety yang sudah dibangun, dan DTO hanya menjadi dekorasi tanpa fungsi nyata.

**Catatan Penting:** Fix ini memerlukan perubahan signature `OrderService::processOrder()` — perlu audit semua caller sebelum dieksekusi karena berisiko breaking change. **Scope fix ini lebih besar dari yang terlihat.**

---

### Bug 3 — Halaman Kasir Tetap Terkunci Setelah Shift Dibuka (Bug Fungsional 🔴)

**Lokasi:** `resto-cashier.blade.php` — `x-show` dan `x-if` pada konten utama kasir

**Root Cause:**

`window.posInitialData.isShiftLocked` adalah **plain JavaScript object property** — bukan reactive Alpine data. Ketika Alpine mengevaluasi ekspresi `x-show="!window.posInitialData.isShiftLocked"` saat inisialisasi, ia membaca nilai statis dan **tidak akan pernah reaktif** terhadap perubahan property tersebut setelahnya.

```
openShift() berhasil di PHP
  → JS event 'shift-active' di-dispatch
  → Alpine di navbar mendengar ✓ (sudah pakai x-data dengan event listener)
  → x-show di konten kasir TIDAK berubah ✗
    (masih membaca window.posInitialData.isShiftLocked yang statis)
```

Berbeda dengan navbar yang menggunakan `x-data` Alpine proper dengan `init()` listener — nilai di sana memang reaktif karena disimpan sebagai Alpine property, bukan plain JS object.

**Fix:**

Pindahkan `isShiftLocked` dan `shiftActive` ke dalam Alpine `restoPos` data, di-inisialisasi dari `config` (yang berasal dari `posInitialData`), lalu diupdate via event listener di `init()`:

```js
// Di Alpine.data('restoPos', (config) => ({
isShiftLocked: config.isShiftLocked,
shiftActive: config.shiftActive,

init() {
    window.addEventListener('shift-active', () => {
        this.isShiftLocked = false;
        this.shiftActive = true;
    });
    window.addEventListener('shift-closed', () => {
        this.shiftActive = false;
        // isShiftLocked tetap false — shift sudah pernah dibuka hari ini
    });
    // ...existing $watch cart
},
```

Lalu di blade, ubah semua referensi statis menjadi Alpine reactive:

```html
<!-- SEBELUM (statis, tidak reaktif): -->
<template x-if="window.posInitialData.isShiftLocked">
<div x-show="!window.posInitialData.isShiftLocked && currentTab === 'cashier'">
<div x-show="!window.posInitialData.isShiftLocked && currentTab === 'queue'">
<template x-if="!window.posInitialData.isShiftLocked && currentTab === 'cashier' && ...">

<!-- SESUDAH (reaktif via Alpine state): -->
<template x-if="isShiftLocked">
<div x-show="!isShiftLocked && currentTab === 'cashier'">
<div x-show="!isShiftLocked && currentTab === 'queue'">
<template x-if="!isShiftLocked && currentTab === 'cashier' && ...">
```

---

## Urutan Eksekusi Fix

| Prioritas | Bug | Alasan |
|-----------|-----|--------|
| 🔴 1 | Bug 3 — Shift lock tidak reaktif | Bug fungsional — kasir tidak bisa dipakai setelah buka shift |
| 🟡 2 | Bug 1 — Duplikat validasi cart | Code quality — ekstrak 5 baris ke 1 method |
| 🟠 3 | Bug 2 — DTO tidak dikirim ke service | Breaking change — perlu audit `OrderService` dulu, scope lebih besar |

---

## Keputusan

- Fix Bug 3 dan Bug 1 dilakukan dalam satu commit setelah dokumen ini ditulis.
- Fix Bug 2 ditunda sampai ada sesi khusus audit `OrderService` dan semua callernya.
- Dokumen ini menjadi referensi saat mengerjakan refactor `OrderService` ke depannya.
