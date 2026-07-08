# Task Breakdown (Tiket Kerja Harian)

## Dokumen Kontrol
* **Nama Proyek:** Pakaiapp.online (EzMenu Platform)
* **Fitur Utama:** AI Autonomous Menu Engine
* **Versi:** 1.0
* **Status:** Development Phase (Backlog)
* **Stack:** Full Laravel + Livewire 4

---

## 🎯 Sprint 1: Pondasi Database & Eloquent Models
*Fokus: Membangun struktur tabel dan relasi ORM tanpa menyentuh UI.*

* **[Task 1.1] Setup Migration untuk Modul AI (Estimasi: 1 Jam)**
  * Buka terminal WSL2, buat file migration baru.
  * Buat tabel `ai_pricing_rules` dan pivot `ai_rule_variants`.
  * Buat tabel `ai_chat_sessions` dan `ai_chat_messages`.
  * *Notes:* Pastikan foreign key untuk varian mengarah tepat ke `variants(id)`.
* **[Task 1.2] Setup Eloquent Models & Relationships (Estimasi: 1 Jam)**
  * Generate model `AiPricingRule`, `AiChatSession`, `AiChatMessage`.
  * Definisikan relasi `belongsToMany` antara `AiPricingRule` dan `Variant`.
  * Definisikan relasi `hasMany` dari `AiChatSession` ke `AiChatMessage`.

## 🤖 Sprint 2: Core Engine & Integrasi OpenAI
*Fokus: Menyambungkan Pakaiapp dengan otak OpenAI menggunakan API Key.*

* **[Task 2.1] Setup OpenAiMenuService (Estimasi: 2 Jam)**
  * Buat folder `app/Services` dan class `OpenAiMenuService.php`.
  * Setup HTTP Client Laravel (`Http::withToken()`) untuk menembak endpoint OpenAI Responses API terbaru.
  * Buat method `generateResponseStream()` yang menerima parameter histori chat.
* **[Task 2.2] Meracik System Prompt (Estimasi: 1.5 Jam)**
  * Susun instruksi ketat (prompt engineering) di dalam service.
  * Inject data dinamis: Tarik list varian aktif dari database, parsing ke format JSON ringkas, lalu selipkan ke dalam System Prompt agar AI tahu menu apa saja yang tersedia hari ini.

## 💼 Sprint 3: B2B Admin Dashboard (Merchant)
*Fokus: UI kontrol merchant untuk menyalakan mode Auto-Pilot.*

* **[Task 3.1] Generate & UI Layout AiEngineManager (Estimasi: 2 Jam)**
  * Eksekusi `php artisan make:livewire Admin\AiEngineManager`.
  * Styling file Blade menggunakan Bootstrap/Tailwind CSS sesuai standar dashboard Pakaiapp.
  * Buat komponen toggle switch utama untuk status AI.
* **[Task 3.2] Logika Preset Template & Rule Save (Estimasi: 2 Jam)**
  * Implementasi method `loadPresetConfig('happy_hour')`.
  * Implementasi method `saveRules()` untuk menyimpan konfigurasi jam dan nilai diskon ke tabel `ai_pricing_rules` dan merekam ID varian target ke tabel pivot `ai_rule_variants`.

## 🛍️ Sprint 4: B2C Customer UI & AI Chat
*Fokus: Antarmuka pelanggan saat scan QR, fitur chat, dan Add to Cart.*

* **[Task 4.1] Generate & UI Layout AiFloatingChat (Estimasi: 2 Jam)**
  * Eksekusi `php artisan make:livewire Customer\AiFloatingChat`.
  * Buat desain chat bubble melayang (fixed bottom-right) di atas katalog menu utama.
* **[Task 4.2] Implementasi wire:stream untuk Chat (Estimasi: 3 Jam)**
  * Sambungkan form input pelanggan dengan method `sendMessage()`.
  * Eksekusi pemanggilan `OpenAiMenuService`.
  * Gunakan `wire:stream` untuk menampilkan efek teks mengetik (typewriter) dari respons OpenAI ke browser.
* **[Task 4.3] Action Component: Add to Cart via Chat (Estimasi: 1.5 Jam)**
  * Pastikan AI mengembalikan ID Varian dalam responsnya.
  * Render ID tersebut menjadi tombol HTML "Tambah ke Keranjang".
  * Sambungkan tombol dengan method `addVariantToCart($variantId)` agar langsung masuk ke POS Pakaiapp (keranjang belanja hanya mengeksekusi harga berdasarkan `base_price` di tabel varian).

## ⏳ Sprint 5: Auto-Pilot Scheduler (Dynamic Pricing)
*Fokus: Eksekusi otomatis dari aturan yang sudah di-set oleh merchant.*

* **[Task 5.1] Setup Laravel Console Kernel / Schedule (Estimasi: 2 Jam)**
  * Buka `app/Console/Kernel.php` atau `routes/console.php` (tergantung versi Laravel).
  * Buat job yang berjalan setiap menit (`->everyMinute()`).
  * Logika Job: Cek tabel `ai_pricing_rules` yang aktif hari ini dan jam ini.
* **[Task 5.2] Kalkulasi Keranjang / Checkout Injector (Estimasi: 2 Jam)**
  * Modifikasi class/service keranjang belanja bawaan Pakaiapp.
  * Tambahkan pengecekan: Saat menghitung total belanja, cek apakah *variant* tersebut sedang terikat di aturan pivot `ai_rule_variants` yang Live.
  * Jika ya, kurangi `base_price` varian dengan `discount_value` tanpa mengubah data master di tabel `variants`.