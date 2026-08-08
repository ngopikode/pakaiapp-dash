# Plan: Fix QRIS & WA Checkout Flow on Single Product Page (SPA)

## Masalah

Fitur **QRIS Confirm Screen** dan **WA Checkout** berjalan dengan sempurna jika *checkout modal* dibuka dari halaman **Home / Menu Utama**.

Namun, saat pengguna mengklik suatu produk dan masuk ke halaman **Detail Produk (SPA/Single Page)**, lalu checkout dari sana, alurnya kembali ke *flow Checkout POS reguler* (bukan flow QRIS/WA yang baru dibuat).

## Penyebab

Aplikasi menggunakan arsitektur Alpine.js (`store.js`) yang di-*bootstrapping* dengan `data-attributes` dari HTML.
Di halaman **Home** (`layouts/store.blade.php`), kita menyuntikkan konfigurasi WA, Pre-Order, dan QRIS:

```blade
data-qris-image="{{ ... }}"
data-wa-checkout-active="{{ ... }}"
data-preorder-active="{{ ... }}"
data-preorder-config="{{ ... }}"
```

Namun, di halaman **Single Product** (`resources/views/pages/tenant/store/resto/product.blade.php`), atribut-atribut konfigurasi di atas **TERLEWAT (missing)** pada `<div x-data="storeApp">` utamanya. Akibatnya, `store.js` yang diload dari halaman detail menganggap mode WA Checkout = *false*, Pre-Order = *false*, dan QRIS Image = *kosong*.

## Rencana Perbaikan

1. Buka file `resources/views/pages/tenant/store/resto/product.blade.php`.
2. Pada bagian tag pembuka `<div class="... bg-[var(--bg-soft)] ... " x-data="storeApp">`, tambahkan 4 buah `data-` atribut yang hilang:
   - `data-qris-image="{{ $setting?->qris_image ? '/tenant_' . tenant('id') . '/' . $setting->qris_image : '' }}"`
   - `data-wa-checkout-active="{{ $isWaCheckoutActive ? 1 : 0 }}"`
   - `data-preorder-active="{{ $isPreorderActive ? 1 : 0 }}"`
   - `data-preorder-config="{{ json_encode($preorderConfig) }}"`
3. Periksa dan pastikan variabel PHP `$isWaCheckoutActive`, `$isPreorderActive`, dan `$preorderConfig` sudah di-passing dari `ProductController` ke View `product.blade.php`.
4. Jika sudah, simpan dan refresh halaman detail produk. Form Checkout akan memiliki fitur QRIS dan WA yang identik dengan halaman home.
