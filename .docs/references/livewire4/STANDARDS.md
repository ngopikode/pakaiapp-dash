# Livewire 4 Standards & Best Practices

Dokumen ini merupakan panduan spesifik proyek untuk pengembangan menggunakan Livewire 4. Tujuannya adalah menjaga agar *codebase* tetap rapi, konsisten, dan mudah dipelihara.

## 1. Naming Conventions & Namespaces

Proyek ini menggunakan konvensi nama yang eksplisit agar komponen mudah ditemukan:

- **Pages (`pages::`)**: Digunakan untuk komponen yang merepresentasikan satu halaman penuh. Komponen ini biasanya diletakkan di `resources/views/pages/` dan kelas PHP-nya bisa ditempatkan di `app/Livewire/` atau sejenisnya.
- **Components (`components::`)**: Digunakan untuk komponen UI yang bisa digunakan kembali (reusable), seperti modal, tombol, chat floating, dsb.

Saat memanggil komponen:
```blade
{{-- Halaman --}}
<livewire:pages::tenant.invoice.show />

{{-- Reusable Component --}}
<livewire:components::tenant.ai-floating-chat />
```

## 2. Route::livewire() vs Route::get()

- Gunakan `Route::livewire('/path', 'pages::component')` jika halaman tersebut 100% dirender oleh Livewire dan tidak memerlukan logic deteksi kondisi kompleks (seperti membedakan layout/tipe toko di level request yang lebih cocok di middleware atau controller).
- Gunakan `Route::get('/path', Controller::class)` jika halaman tersebut memerlukan logika penentuan view yang berbeda secara dinamis, misalnya `HomeController` yang merender halaman berbeda untuk Resto vs Retail.

## 3. Layouts & Structure

Layout file (misal `store.blade.php`) harus tetap sebersih mungkin.

### Aturan Layout:
1. **Pemisahan Concern**: Jika layout melebihi 100-150 baris karena ada markup modal, toast, atau head tags, pindahkan bagian tersebut ke direktori `layouts/_partials/`.
2. **Alpine.js & Pull-to-Refresh**: Logika Alpine yang terikat kuat ke elemen utama `<div x-data="app">` boleh dibiarkan di dalam layout utama jika terlalu sulit dipisahkan tanpa merusak *scope*.
3. **Embedded Components**: Komponen Livewire berukuran kecil atau global (seperti `ai-floating-chat`) boleh ditanam (`<livewire:... />`) di dalam layout, tetapi harus diletakkan di bagian akhir atau di blok khusus untuk menjaga kebersihan struktur.

## 4. `@livewireStyles` dan `@livewireScripts`

- Selalu sertakan `@livewireStyles` di dalam tag `<head>`.
- Selalu sertakan `@livewireScripts` sebelum penutup tag `</body>`.
- Jangan gunakan `@livewireScripts(persist: true)` atau directive lanjutan lainnya kecuali ada kebutuhan yang sangat spesifik dan didokumentasikan di Pull Request.

## 5. JavaScript Eksternal & Alpine.js (`@script`)

Livewire 4 sering digunakan dengan *Multi-File Component (MFC)*. Saat Anda memisahkan JavaScript ke file `.js`:
- **JANGAN** mendaftarkan `Alpine.data()` di file `.js` eksternal. Ini akan menyebabkan *race condition* karena file `.js` dimuat secara asinkron.
- **SELALU** gunakan direktif `@script` di dalam file `.blade.php` Anda untuk mendaftarkan logika Alpine komponen.

**Contoh yang Benar (`.blade.php`):**
```blade
<div x-data="myComponent">
    ...
</div>

@script
<script>
    Alpine.data('myComponent', () => ({
        // state and functions
    }))
</script>
@endscript
```

Gunakan file `.js` eksternal **hanya** untuk mengimpor library, fungsi global murni, atau listener global. Referensi: [MFC Alpine Architecture](./mfc-alpine-architecture.md).

## Referensi Upstream Livewire
- [Pages](./essentials/pages.md)
- [Components](./essentials/components.md)
- [Layout Attribute](./php-attributes/attribute-layout.md)
