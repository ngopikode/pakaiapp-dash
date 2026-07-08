# ERD & Skema Database (Pondasi Data)

## Dokumen Kontrol
* **Nama Proyek:** Pakaiapp.online (EzMenu Platform)
* **Fitur Utama:** AI Autonomous Menu Engine (Sistem Menu Otomatis & Interaktif)
* **Versi:** 1.0
* **Status:** System Design Phase

---

## 1. Aturan Mutlak Arsitektur Data
1. **Zero Price on Products:** Tabel `products` sama sekali tidak boleh memiliki kolom harga.
2. **Absolute Variant Pricing:** Harga dasar (`base_price`) wajib tersimpan eksklusif di tabel `variants`.
3. **Non-Destructive Override:** AI tidak boleh menimpa (overwrite) `base_price` secara permanen saat mengeksekusi harga dinamis. AI hanya menyuntikkan harga promo melalui relasi rule yang aktif (Pivot Table).

---

## 2. Visualisasi ERD (Entity Relationship Diagram)

```mermaid
erDiagram
    PRODUCTS ||--|{ PRODUCT_VARIANTS : "has"
    
    AI_PRICING_RULES ||--|{ AI_RULE_VARIANTS : "applies_to"
    PRODUCT_VARIANTS ||--o{ AI_RULE_VARIANTS : "has_dynamic_price"

    AI_CHAT_SESSIONS ||--|{ AI_CHAT_MESSAGES : "contains"

    %% Struktur Inti Menu
    PRODUCTS {
        bigint id PK
        bigint category_id FK
        string name
        text description
        string image
        boolean tax_included
        boolean has_variants
        boolean is_active
    }

    PRODUCT_VARIANTS {
        bigint id PK
        bigint product_id FK
        string sku
        string name "Nama varian (e.g. M - Hitam)"
        decimal cost "HPP"
        decimal price "Harga Jual Dasar (Wajib)"
        decimal active_discount_price "Harga diskon jika aktif"
        string active_discount_name "Nama diskon"
        int stock
        int min_stock
    }

    %% Modul AI: Auto-Pilot Pricing (B2B)
    AI_PRICING_RULES {
        bigint id PK
        string rule_name "e.g., Happy Hour Anti Sepi"
        string rule_type "percentage | fixed_cut"
        time start_time "e.g., 14:00"
        time end_time "e.g., 16:00"
        json active_days "['Mon', 'Tue']"
        boolean is_active
    }

    AI_RULE_VARIANTS {
        bigint id PK
        bigint ai_pricing_rule_id FK
        bigint product_variant_id FK
        decimal discount_value "Nilai diskon AI"
    }

    %% Modul AI: Chat / Digital Waiter (B2C)
    AI_CHAT_SESSIONS {
        bigint id PK
        string table_number
        string session_token "UUID/Token unik"
        int turn_count "Turn chat"
    }

    AI_CHAT_MESSAGES {
        bigint id PK
        bigint ai_chat_session_id FK
        enum role "system | user | assistant"
        text content
        int tokens_used
    }
```

---

## 3. Spesifikasi Tabel & Logika Relasi

### A. Core Menu Engine (Tabel Eksisting - Penyesuaian)
1. **`products`**
   * *Fungsi:* Menyimpan identitas induk makanan/minuman/pakaian.
   * *Catatan:* Bebas dari kolom `price`.
2. **`product_variants`**
   * *Fungsi:* Menyimpan entitas riil yang dibeli, termasuk harga dasar, diskon aktif, dan stok.
   * *Kolom Kunci:* `price` (Decimal). Harga ini menjadi acuan utama sebelum dipotong oleh sistem AI. `active_discount_price` menampung diskon aktif jika ada.

### B. AI Auto-Pilot Pricing Module (Tabel Baru)
1. **`ai_pricing_rules`**
   * *Fungsi:* Menyimpan konfigurasi "Set & Forget" dari merchant (misal: Happy Hour).
   * *Cara Kerja:* Cron job Laravel akan mengecek tabel ini setiap menit. Jika waktu saat ini (H:i) cocok dengan `start_time` dan `active_days`, aturan ini dianggap *Live*.
2. **`ai_rule_variants` (Pivot Table)**
   * *Fungsi:* Menghubungkan aturan dinamis (Rule) dengan target spesifik varian produk.
   * *Cara Kerja untuk menghindari redundansi:* Saat pelanggan *checkout*, sistem POS Pakaiapp akan melakukan pengecekan:
     `Harga Akhir = Cek apakah variant ini terhubung dengan ai_pricing_rules yang aktif? Jika Ya, (price - discount_value). Jika Tidak, gunakan price.`

### C. AI Contextual Chat Module (Tabel Baru)
1. **`ai_chat_sessions`**
   * *Fungsi:* Membatasi konteks percakapan di satu meja agar AI tidak berhalusinasi dan menghemat token.
   * *Kolom Kunci:* `turn_count` (Integer). Setiap ada pesan masuk/keluar, nilai ini bertambah. Jika mencapai batas maksimal (misal: 7), sesi ditutup.
2. **`ai_chat_messages`**
   * *Fungsi:* Menyimpan histori pesan sebagai *array context* yang akan dikirim kembali ke API OpenAI (menggunakan `role`: user/assistant).
   * *Kolom Kunci:* `tokens_used`. Digunakan untuk men-generate laporan "Biaya AI" di dashboard admin.