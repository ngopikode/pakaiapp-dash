# Plan: QRIS Confirm Screen (Retail)

Karena file `store.js` dipakai bersama antara tipe `resto` dan `retail`, maka fitur QRIS Confirmation dan layout yang tumpang tindih juga perlu disesuaikan pada file khusus RETAIL.

File target:
`resources/views/pages/tenant/store/retail/modals/checkout-modal.blade.php`

## Langkah-langkah:

### 1. Update Header x-show
Ubah baris 8 dan 15 agar memasukkan pengecekan `!showQrisConfirm`:
```blade
<div x-show="!orderSuccess && !showQrisConfirm" ...>
```
Dan pada judul Konfirmasi QRIS:
```blade
<h2 x-show="showQrisConfirm" ...>Konfirmasi QRIS</h2>
```

### 2. Tambahkan Pilihan QRIS pada `showPaymentSelector`
Di dalam div `showPaymentSelector` (bagian "Manual / COD"), tambahkan `<template x-if="qrisImage">` yang berisi card untuk memilih QRIS.

### 3. Restrukturisasi Layer Step 2
Sama persis seperti `resto`, pecah layer `checkoutStep === 2` menjadi 3 sibling sejajar:
1. `template x-if="showQrisConfirm && pendingQrisOrder"` (Berisi gambar QRIS, tombol download, dan Sticky Footer konfirmasi WA).
2. `div x-show="!showQrisConfirm && !showPaymentSelector"` (Berisi form input pemesan dan Sticky Footer tombol submit utama).
3. `div x-show="showPaymentSelector"` (Berisi list metode pembayaran dan Sticky Footer konfirmasi metode).

### 4. Ganti processOrder ke proceedToQrisOrProcess
Pada tombol submit checkout (yang bertuliskan "Lanjut Checkout" atau "Buat Pesanan"), pastikan onClick diarahkan ke `proceedToQrisOrProcess()` agar logic scan divalidasi.
