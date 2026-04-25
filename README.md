# Pakaiapp POS - Multi-Tenant Retail SaaS

Pakaiapp POS adalah aplikasi *Point of Sale* (Kasir) berbasis *Software as a Service* (SaaS) yang dirancang khusus untuk bisnis retail (seperti toko baju). Aplikasi ini menggunakan arsitektur **Multi-Database Tenancy** untuk memastikan isolasi data dan keamanan tingkat tinggi bagi setiap toko (penyewa).

Didevelop oleh **ngopikode**.

## 🚀 Tech Stack

- **Framework:** Laravel 13.x
- **Frontend / Reactivity:** Livewire 4.x
- **UI Component:** Bootstrap 5.x
- **Multi-Tenancy:** Stancl/Tenancy (Arsitektur Multi-Database)
- **Asset Bundler:** Vite

## ✨ Fitur Utama

- **Isolasi Data Penuh:** Setiap *tenant* (toko) memiliki *database* fisiknya sendiri (Multi-Database).
- **Subdomain Routing:** Akses toko melalui subdomain dinamis (contoh: `namatoko.pakaiapp.online`).
- **Real-time POS:** Transaksi kasir instan tanpa *reload* halaman menggunakan `wire:navigate` dan `wire:model.live`.
- **Manajemen Varian Produk:** Dukungan dinamis untuk varian item (contoh: Ukuran M, L, XL dan Warna Merah, Hitam).
- **Struk Digital (WhatsApp):** Pengiriman bukti transaksi otomatis melalui link ke WhatsApp pelanggan.

---

## 🛠️ Panduan Instalasi (Development)

Ikuti langkah-langkah di bawah ini untuk menjalankan *project* ini di *local machine*.

### 1. Kebutuhan Sistem
- PHP >= 8.4
- Composer >= 2.0
- Node.js & NPM
- MySQL (User *database* harus memiliki hak akses `CREATE DATABASE`)

### 2. Clone & Install Dependencies
```bash
git clone [https://github.com/username/pakaiapp-pos.git](https://github.com/username/pakaiapp-pos.git)
cd pakaiapp-pos

composer install
npm install
```

### 3. Konfigurasi Environment
Salin file `.env.example` menjadi `.env`.
```bash
cp .env.example .env
php artisan key:generate
```

Buka file `.env` dan konfigurasikan koneksi **Central Database** (database utama untuk menampung data toko/penyewa):
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pakaiapp_central
DB_USERNAME=root
DB_PASSWORD=password_kamu

# Tambahkan domain utama untuk Tenancy
CENTRAL_DOMAIN=pakaiapp.test
```
*Catatan: Buat database kosong bernama `pakaiapp_central` di MySQL sebelum lanjut ke langkah berikutnya.*

### 4. Migrasi Database
Karena menggunakan sistem *multi-tenant*, migrasi dibagi menjadi dua tahap.

Migrasi untuk aplikasi induk (Central):
```bash
php artisan migrate
```

*(Opsional)* Jika kamu sudah memiliki *tenant* terdaftar dan ingin menjalankan migrasi ke semua *database tenant*:
```bash
php artisan tenants:migrate
```

### 5. Compile Assets & Jalankan Server
Jalankan Vite untuk *compile* Bootstrap dan *assets* lainnya:
```bash
npm run dev
```

Buka terminal baru dan jalankan server Laravel:
```bash
php artisan serve
```

---

## 📂 Struktur Multi-Tenant

Proyek ini memisahkan logika sentral (SaaS) dan operasional kasir (Tenant):

- **Routes:**
    - `routes/web.php`: Rute untuk *landing page* dan pendaftaran *tenant* (SaaS Utama).
    - `routes/tenant.php`: Rute untuk aplikasi kasir. Semua rute di sini otomatis mengenali *database* sesuai subdomain.
- **Migrations:**
    - `database/migrations/`: Tabel induk (`users` admin pusat, `tenants`, `domains`).
    - `database/migrations/tenant/`: Tabel operasional toko (`products`, `categories`, `orders`, dll).

## 💡 Catatan Livewire 3 di Tenancy

Rute *update* Livewire telah dikonfigurasi di `AppServiceProvider` untuk selalu melewati *middleware* Tenancy. Saat membuat komponen Livewire baru untuk fitur POS:
1. Buat komponen seperti biasa: `php artisan make:livewire Pos/Checkout`
2. Pastikan pemanggilan komponen dilakukan di dalam *view* yang di-*render* dari `routes/tenant.php`.

---

© 2026 ngopikode. All rights reserved.
