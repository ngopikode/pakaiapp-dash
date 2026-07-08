# Pakaiapp POS — Multi-Tenant Retail SaaS

[![Laravel Version](https://img.shields.io/badge/Laravel-13.x-red.svg)](https://laravel.com)
[![Livewire Version](https://img.shields.io/badge/Livewire-4.x-blueviolet.svg)](https://livewire.laravel.com)
[![Tenancy Version](https://img.shields.io/badge/Stancl--Tenancy-3.10-blue.svg)](https://tenancyforlaravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D%208.3-8892BF.svg)](https://php.net)

Pakaiapp POS adalah aplikasi *Point of Sale* (Kasir) berbasis *Software as a Service* (SaaS) yang dirancang khusus untuk skala retail dan F&B. Sistem ini menggunakan arsitektur **Multi-Database Tenancy** untuk memastikan isolasi data fisik penuh dan keamanan tingkat tinggi bagi setiap merchant (penyewa).

Didevelop dengan standar *clean architecture* oleh **ngopikode**.

---

## 🏗️ Arsitektur & Isolasi Data
Aplikasi dipisahkan secara ketat menjadi dua konteks domain utama untuk mencegah kebocoran data dan mempermudah pemeliharaan:

1.  **Central Domain (SaaS Utama)**:
    *   Berjalan di domain induk (contoh: `pakaiapp.online`).
    *   Mengatur pendaftaran tenant baru, paket billing (free trial, Midtrans, Duitku), artikel/blog, dan konfigurasi global.
2.  **Tenant Domain (Kasir & Storefront)**:
    *   Berjalan di subdomain dinamis (contoh: `namatoko.pakaiapp.online`).
    *   Setiap toko memiliki database MySQL fisiknya sendiri.
    *   Menangani operasional kasir (POS), stok barang, resep bahan baku, manajemen pengguna toko, dan fitur *AI Autonomous Menu Engine*.

---

## 📂 Peta Struktur Direktori (Project Tree)

Berikut adalah struktur folder inti setelah restrukturisasi domain separation:

```text
pakaiapp-dash/
├── app/
│   ├── Central/               # ── Konteks SaaS Induk (Central Domain)
│   │   ├── Controllers/       # Auth, Duitku, & Midtrans Callback
│   │   ├── Data/              # Spatie Data Transfer Objects (DTO) untuk validasi & output tipe
│   │   ├── Http/Middleware/   # Middleware khusus domain Central (e.g. DuitkuEnabled)
│   │   ├── Models/            # Tenant, TenantRegistration, Quota, User Central
│   │   └── Services/          # Billing, Duitku, & Registration Services
│   │
│   ├── Tenant/                # ── Konteks Operasional Toko (Tenant Domain)
│   │   ├── Controllers/       # Web & REST API (Restaurant, Order, Duitku)
│   │   ├── Models/
│   │   │   ├── Core/          # StoreSetting, Category, Product, Order, Wallet
│   │   │   ├── Resto/         # RawMaterial, VariantRecipe (Spesifik F&B)
│   │   │   └── Ai/            # AiChatSession, AiChatMessage, AiPricingRule
│   │   └── Services/          # Order, Setting, & Wallet Services
│   │
│   ├── Shared/                # ── Kelas & Utilitas Bersama (Shared Context)
│   │   ├── Traits/            # ApiResponser, ApiPagination, Cache utility
│   │   ├── Middleware/        # Role check, IP Whitelist, FileUrl correction
│   │   └── Jobs/              # Tenant directory initialization
│   │
│   └── Providers/             # Laravel Service Providers (Tenancy, App)
│
├── database/
│   └── migrations/
│       ├── central/           # Migrasi untuk database induk SaaS (Central)
│       └── tenant/
│           ├── core/          # Migrasi dasar operasional (produk, order, user)
│           ├── resto/         # Migrasi spesifik fitur F&B (bahan mentah, resep)
│           └── retail/        # Migrasi spesifik fitur retail (akan datang)
│
├── routes/
│   ├── web.php                # Rute domain Central (landing, register, callback)
│   ├── tenant.php             # Rute Web Tenant (POS, kitchen, login dashboard)
│   └── tenant-api.php         # Rute REST API khusus Tenant
```

---

## 🛠️ Panduan Instalasi (Development)

### 1. Kebutuhan Sistem
*   PHP >= 8.3
*   Composer >= 2.0
*   Node.js & NPM
*   MySQL (User database lokal wajib memiliki hak akses `CREATE DATABASE` untuk alokasi DB tenant baru).

### 2. Clone & Install Dependencies
```bash
git clone https://github.com/ngopikode/pakaiapp-dash.git
cd pakaiapp-dash

composer install
npm install
```

### 3. Konfigurasi Environment
Salin file `.env.example` menjadi `.env`.
```bash
cp .env.example .env
php artisan key:generate
```

Buka file `.env` dan konfigurasi database induk (**Central Database**):
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pakaiapp_central
DB_USERNAME=root
DB_PASSWORD=password_kamu

# Domain utama untuk Tenancy
CENTRAL_DOMAIN=pakaiapp.test
```
*(Catatan: Buat database kosong bernama `pakaiapp_central` di MySQL sebelum lanjut ke langkah berikutnya).*

### 4. Pengujian Subdomain Lokal (Multi-Tenant)
Karena sistem ini berbasis subdomain dinamis (seperti `toko1.pakaiapp.test`), Anda perlu memetakan domain lokal agar bisa diakses di browser:

*   **Opsi A: macOS (Laravel Valet)**
    Cukup jalankan perintah berikut di direktori proyek:
    ```bash
    valet link pakaiapp
    ```
    Semua rute wildcard `*.pakaiapp.test` otomatis diarahkan ke aplikasi lokal Anda.
*   **Opsi B: Konfigurasi Manual `/etc/hosts` (Semua OS)**
    Tambahkan entri berikut ke berkas hosts sistem Anda (`/etc/hosts` pada Linux/macOS, atau `C:\Windows\System32\drivers\etc\hosts` pada Windows):
    ```env
    127.0.0.1 pakaiapp.test
    127.0.0.1 toko1.pakaiapp.test
    127.0.0.1 toko2.pakaiapp.test
    ```
*   **Opsi C: Dnsmasq (Linux/macOS Wildcard)**
    Gunakan `dnsmasq` untuk secara otomatis merutekan semua subdomain wildcard ke IP lokal:
    ```conf
    address=/pakaiapp.test/127.0.0.1
    ```

### 5. Migrasi Database
Migrasi dibagi menjadi dua tahap sesuai cakupan domainnya.

Migrasi untuk database induk (Central):
```bash
php artisan migrate
```

Migrasi untuk database tenant yang sudah terdaftar (segmentasi tipe toko):
```bash
# Menjalankan migrasi ke semua tipe tenant (retail & resto)
php artisan tenants:migrate-type all

# Atau jalankan untuk tipe spesifik
php artisan tenants:migrate-type retail
php artisan tenants:migrate-type resto
```

### 6. Compile Assets & Jalankan Server
Jalankan Vite untuk kompilasi asset:
```bash
npm run dev
```

Buka terminal baru dan jalankan server Laravel:
```bash
php artisan serve
```

---

## ⌨️ Daftar Perintah Konsol (Cheat Sheet)

| Perintah | Deskripsi |
| :--- | :--- |
| `php artisan migrate` | Jalankan migrasi database Central (SaaS induk) |
| `php artisan tenants:migrate-type all` | Jalankan migrasi core & tipe untuk seluruh tenant |
| `php artisan tenants:migrate-type retail` | Jalankan migrasi khusus untuk tenant bertipe retail |
| `php artisan tenants:migrate-type resto` | Jalankan migrasi khusus untuk tenant bertipe resto |
| `composer test` | Jalankan unit & feature testing menggunakan Pest |
| `composer lint` | Format dan perbaiki penulisan kode sesuai standar Pint |
| `composer lint:check` | Cek kepatuhan format kode tanpa mengubahnya |

---

## 📚 Pusat Referensi & Dokumentasi Detail

Semua dokumentasi proyek yang lebih rinci terpusat di direktori [`.docs/`](.docs/README.md). Gunakan tautan di bawah ini untuk mengakses panduan spesifik:

*   **Panduan Utama & AI Onboarding**: [ai-agent-guide.md](.docs/project/ai-agent-guide.md) — Konteks cepat dan petunjuk teknis.
*   **Rencana Arsitektur**: [architecture-plan.md](.docs/project/architecture-plan.md) — Cetak biru restrukturisasi kode.
*   **Spesifikasi REST API**: [api-spec.md](.docs/project/api-spec.md) — Definisi endpoint API operasional tenant.
*   **Keputusan Arsitektur (ADR)**:
    *   [ADR-0001: Tenancy](.docs/decisions/0001-multi-database-tenancy.md) — Pemilihan isolasi database fisik.
    *   [ADR-0002: Service Injection](.docs/decisions/0002-service-injection-guidelines.md) — Standar dependency injection PHP.
    *   [ADR-0003: Absolute Variant Pricing](.docs/decisions/0003-absolute-variant-pricing.md) — Isolasi harga di tingkat varian.
*   **Alur Bisnis & Teknis**:
    *   [POS Resto Flowchart](.docs/project/pos-resto-flowchart.md) — Alur lengkap operasional kasir & anti-fraud.
    *   [POS Kitchen Flowchart](.docs/project/pos-kitchen-flowchart.md) — Transmisi pesanan dari kasir ke koki dapur.
*   **Panduan Coding Standard**:
    *   [Livewire 4 Standards](.docs/references/livewire4/STANDARDS.md) — Aturan MFC & integrasi Alpine `@script`.
    *   [Coding Standards (AGENTS)](.agents/AGENTS.md) — Aturan penyuntikan service.

---

© 2026 ngopikode. All rights reserved.
