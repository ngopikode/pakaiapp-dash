# Livewire Component Map & Logic Flow

## Dokumen Kontrol
* **Nama Proyek:** Pakaiapp.online (EzMenu Platform)
* **Fitur Utama:** AI Autonomous Menu Engine (Sistem Menu Otomatis & Interaktif)
* **Versi:** 1.0
* **Status:** System Design Phase
* **Stack:** Full Laravel + Livewire 3

---

## 1. Filosofi Arsitektur (Kenapa Tanpa REST API?)
Dengan menggunakan arsitektur **Full Livewire**, kompleksitas *state management* dan *routing* REST API B2C/B2B dipangkas. Komponen antarmuka (UI) dapat langsung berkomunikasi dengan *Eloquent Model* dan layanan eksternal (OpenAI) di dalam satu *codebase*. Pembaruan antarmuka (seperti menambah item ke keranjang dari hasil *chat* AI) dieksekusi melalui *Data Binding* dan *Action Methods* bawaan Livewire.

---

## 2. Pemetaan Komponen Sisi Merchant (B2B Dashboard)

### Komponen Utama: `Admin\AiEngineManager`
* **Perintah Generate:** `php artisan make:livewire Admin\AiEngineManager`
* **Lokasi View:** `resources/views/livewire/admin/ai-engine-manager.blade.php`
* **Deskripsi:** Komponen *single-page* untuk mengontrol seluruh parameter AI Auto-Pilot.

#### A. State Properties (Data Binding)
* `public bool $isAiActive` 
  *(Terhubung ke tombol toggle utama di wireframe. Menyimpan status aktif/nonaktif engine AI secara global untuk merchant tersebut).*
* `public string $selectedPreset` 
  *(Menyimpan pilihan template strategi, misal: 'happy_hour', 'weekend_boost').*
* `public array $ruleConfigurations` 
  *(Array untuk menampung input jam mulai, jam selesai, dan daftar ID varian yang ditargetkan beserta nominal diskonnya).*

#### B. Action Methods (Logika Bisnis)
* `public function toggleEngine()`
  *(Dipanggil saat merchant mengklik switch AI. Langsung meng-update status di database tanpa reload halaman).*
* `public function loadPresetConfig($presetKey)`
  *(Mengisi `$ruleConfigurations` dengan pengaturan default berdasarkan template yang dipilih).*
* `public function saveRules()`
  *(Memvalidasi input konfigurasi dan menyimpannya ke tabel `ai_pricing_rules` dan `ai_rule_variants` menggunakan Eloquent).*

---

## 3. Pemetaan Komponen Sisi Pelanggan (B2C QR Menu)

### Komponen Utama: `Customer\AiFloatingChat`
* **Perintah Generate:** `php artisan make:livewire Customer\AiFloatingChat`
* **Lokasi View:** `resources/views/livewire/customer/ai-floating-chat.blade.php`
* **Deskripsi:** Menangani antarmuka *chat bubble* melayang, *streaming* respons AI, dan interaksi transaksi langsung (*Add to Cart*).

#### A. State Properties (Data Binding)
* `public bool $isOpen = false`
  *(Mengontrol visibilitas panel chat melayang di atas katalog menu).*
* `public string $userInput = ''`
  *(Di-bind dengan `wire:model` pada form input teks pelanggan).*
* `public array $chatHistory = []`
  *(Menyimpan histori percakapan dalam sesi saat ini untuk dirender di Blade `foreach`).*
* `public string $sessionId`
  *(Menyimpan UUID untuk mencocokkan percakapan dengan tabel `ai_chat_sessions`).*

#### B. Action Methods (Logika Bisnis & Interaksi)
* `public function toggleChat()`
  *(Membuka atau menutup panel AI).*
* `public function sendMessage()`
  * **Alur Logika:**
    1. Memasukkan `$this->userInput` ke dalam `$this->chatHistory` sebagai `user`.
    2. Menyimpan pesan ke tabel `ai_chat_messages`.
    3. Memanggil *Service Class* (misal: `OpenAiService`) dengan mengirimkan histori percakapan dan katalog *variants* yang aktif.
    4. Menerima kembalian (*response stream*) dari OpenAI.
    5. Menggunakan fitur `wire:stream` (Livewire 3) untuk memunculkan efek teks mengetik (*typewriter effect*) di *frontend* tanpa memblokir proses lain.
* `public function addVariantToCart($variantId)`
  *(Dipanggil ketika pelanggan mengklik tombol "Tambah" pada Product Card yang direkomendasikan oleh AI di dalam *chat*. Langsung berinteraksi dengan layanan Keranjang/POS lokal).*

---

## 4. Layer Pendukung (Services)

Agar komponen Livewire tidak terlalu gemuk (*fat controller*), logika komunikasi HTTP ke API eksternal dipisahkan ke dalam *Service Class* khusus.

### Class: `App\Services\OpenAiMenuService`
* **Metode Utama:** `public function generateResponseStream(array $context, array $activeVariants)`
* **Tugas Pokok:** 1. Menyusun *System Prompt* yang ketat (Aturan: "Hanya rekomendasikan varian dari list yang diberikan").
  2. Melakukan HTTP Request ke *OpenAI Responses API*.
  3. Mengembalikan respons dalam bentuk format yang bisa di-*stream* oleh Livewire ke *browser* pengguna.