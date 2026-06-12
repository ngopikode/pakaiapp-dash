# Wireframe & UI/UX Flow (Visualisasi Kasar)

## Dokumen Kontrol
* **Nama Proyek:** Pakaiapp.online (EzMenu Platform)
* **Fitur Utama:** AI Autonomous Menu Engine (Sistem Menu Otomatis & Interaktif Berbasis AI)
* **Versi:** 1.0
* **Status:** System Design Phase
* **Target Interface:** Mobile (Sisi Pelanggan) & Desktop (Sisi Merchant)

---

## 1. UI/UX Flow (Alur Antarmuka Pengguna)

### 1.1 Sisi Pelanggan (Mobile Web View - React/Next.js)
```
[Scan QR Code Meja] 
       │
       ▼
[Halaman Utama Menu Pakaiapp] ──(Klik Floating Chat Bubble)──► [Panel Chat AI Terbuka]
                                                                        │
                                                                 (Ketik Request)
                                                                        │
                                                                        ▼
[Pelanggan Klik Tombol "Tambah"] ◄──(Render Teks + Product Card)── [AI Memproses Prompt]
               │
               ▼
[Item Masuk Keranjang Belanja] ──► [Checkout / Pesan ke Kasir]
```

### 1.2 Sisi Merchant (Desktop Admin Dashboard - Bootstrap/Livewire)
```
[Login Admin Dashboard] ──► [Klik Menu "Pakaiapp AI Engine"] 
                                         │
                                         ▼
                     [Halaman Utama AI Engine: Aktifkan Toggle Switch]
                                         │
                                         ▼
                     [Pilih Preset Strategi (e.g., Happy Hour)]
                                         │
                                         ▼
                 [Konfigurasi Aturan: Pilih Target Jam & Varian Menu]
                                         │
                                         ▼
                         [Klik Tombol "Simpan Aturan"]
```

---

## 2. Wireframe Sisi Pelanggan (Mobile Screen Layout)

### 2.1 Tampilan Katalog Utama Menu + Floating Chat Bubble
Halaman default ketika pelanggan pertama kali memindai QR code meja restoran.

```
┌────────────────────────────────────────────────────────┐
│ [≡] KAFIENIA COFFEE SHOP                     Meja: 04  │  <-- Header
├────────────────────────────────────────────────────────┤
│  🔍 Cari menu kopi atau camilan...                     │  <-- Search Bar
├────────────────────────────────────────────────────────┤
│  [ Kopi ]  [ Non-Kopi ]  [ Makanan ]  [ Camilan ]      │  <-- Kategori (Tabs)
├────────────────────────────────────────────────────────┤
│                                                        │
│  ┌──────────────────────────────────────────────────┐  │
│  │ 📷 Foto Produk  │ Espresso Blend                 │  │  <-- Katalog Item
│  │                 │ Mulai dari Rp 18.000           │  │
│  └──────────────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────────────┐  │
│  │ 📷 Foto Produk  │ Croissant Chocolate            │  │
│  │                 │ Mulai dari Rp 22.000           │  │
│  └──────────────────────────────────────────────────┘  │
│                                                        │
│                                      ┌──────────────┐  │
│                                      │  💬 Tanya AI │  │  <-- Floating Chat Bubble
│                                      └──────────────┘  │
├────────────────────────────────────────────────────────┤
│  🛒 2 Item di Keranjang (Rp 40.000)    [ Lihat Cart ]   │  <-- Sticky Bottom Bar
└────────────────────────────────────────────────────────┘
```

### 2.2 Tampilan Panel Chat AI Terbuka (AI Digital Waiter)
Ketika tombol `💬 Tanya AI` diklik, panel meluncur ke atas (*slide up*) menutupi sebagian layar tanpa meninggalkan konteks halaman menu.

```
┌────────────────────────────────────────────────────────┐
│ [X] Tutup Chat AI     🤖 Pakaiapp Digital Assistant    │
├────────────────────────────────────────────────────────┤
│ 🤖: Halo! Selamat datang di Kafienia. Ada yang bisa    │  <-- Pesan Pembuka AI
│     aku bantu pilihkan untuk menemanimu hari ini?      │
│                                                        │
│ 👤: Aku lagi pengen minum yang seger tapi gak terlalu  │  <-- Input Pelanggan
│     manis buat nemenin nugas, sama camilan manis satu. │
│                                                        │
│ 🤖: Pilihan cerdas! Ini kombinasi paling pas           │  <-- Respon Teks Streaming
│     berdasarkan rekomendasi terbaik kami:              │
│                                                        │
│     ┌──────────────────────────────────────────────┐   │
│     │ 📷 │ Iced Americano (Varian: Less Sugar)     │   │  <-- Card Produk Rekomendasi
│     │    │ Rp 18.000                               │   │      (Mengambil Data Varian)
│     │    └───────────────────────[ + Tambah ]──────┤   │
│     │ 📷 │ Croissant Chocolate (Varian: Regular)   │   │
│     │    │ Rp 22.000                               │   │
│     │    └───────────────────────[ + Tambah ]──────┤   │
│     └──────────────────────────────────────────────┘   │
│                                                        │
│ 🤖: Mau sekalian nambah Varian Ekstra Espresso Shot    │  <-- Smart Upselling Natural
│     (hanya +Rp 4.000) biar makin fokus nugasnya?       │
├────────────────────────────────────────────────────────┤
│ [ Ketik pesanmu di sini...                  ] [ Kirim ]│  <-- Input Chat Bar
└────────────────────────────────────────────────────────┘
```

---

## 3. Wireframe Sisi Merchant (Desktop Dashboard Layout)

### 3.1 Antarmuka Menu "AI Engine Manager" (Bootstrap + Livewire)
Menggunakan layout dasbor standar dengan fokus fungsionalitas tombol *switch* terpusat yang bersih.

```
┌─────────────────────────────────────────────────────────────────────────────────────────┐
│ [Pakaiapp Admin]  Dashboard  │  Menu  │  Stok  │  Transaksi  │ [AI Engine Manager]     │
├─────────────────────────────────────────────────────────────────────────────────────────┤
│                                                                                         │
│  ✨ PAKAIAPP AUTONOMOUS MENU ENGINE CONTROL CENTER                                      │
│  ─────────────────────────────────────────────────────────────────────────────────────  │
│                                                                                         │
│  ┌───────────────────────────────────────────────────────────────────────────────────┐  │
│  │ ⚡ STATUS AI AUTO-PILOT ENGINE                                                      │  │
│  │   [ TOGGLE SWITCH: ON ]  | Status: Aktif & Mengoptimalkan Menu Otomatis           │  │  <-- FR-MERCH-01
│  └───────────────────────────────────────────────────────────────────────────────────┘  │
│                                                                                         │
│  ┌───────────────────────────────────────────────────────────────────────────────────┐  │
│  │ 💰 RINGKASAN FINANSIAL AI (BULAN INI)                                              │  │  <-- FR-MERCH-03
│  │   Ekstra Omzet yang Dihasilkan AI: Rp 3.450.000 (Menyumbang +18.2% Total Penjualan)   │  │
│  └───────────────────────────────────────────────────────────────────────────────────┘  │
│                                                                                         │
│  📊 PILIH TEMPLATE STRATEGI AI (SET & FORGET)                                            │  │  <-- FR-MERCH-02
│  ┌─────────────────────────┐ ┌─────────────────────────┐ ┌─────────────────────────┐    │
│  │ [X] Happy Hour Anti Sepi│ │ [ ] Weekend Margin Boost│ │ [ ] Sikat Habis Stok    │    │
│  │ Jam sepi otomatis diskon│ │ Push menu untung tinggi │ │ Habisin bahan baku dekat│    │
│  │ di level varian produk. │ │ pas malam minggu/libur. │ │ tanggal kadaluwarsa.    │    │
│  └─────────────────────────┘ └─────────────────────────┘ └─────────────────────────┘    │
│                                                                                         │
│  🛠️ KONFIGURASI PARAMETER STRATEGI: "HAPPY HOUR ANTI SEPI"                               │
│  ┌───────────────────────────────────────────────────────────────────────────────────┐  │
│  │ Jam Mulai   : [ 14:00 ]             Jam Selesai: [ 16:00 ]                        │  │
│  │ Hari Berlaku: [ Senin ] [ Selasa ] [ Rabu ] [ Kamis ] [ Jumat ] [ Sabtu ] [ Minggu ] │  │
│  │                                                                                   │  │
│  │ Target Varian Produk & Penyesuaian Harga Dinamis:                                 │  │
│  │ ┌───────────────────────────────────────────────────────────────────────────────┐ │  │
│  │ │ [✓] Kopi Susu Aren ── Varian: Large  ── Harga Normal: Rp 22K ── Diskon AI: 15% │ │  │  <-- Target Mutlak Ke Varian
│  │ │ [✓] Kopi Susu Aren ── Varian: Medium ── Harga Normal: Rp 18K ── Diskon AI: 10% │ │  │
│  │ │ [ ] Americano      ── Varian: Regular── Harga Normal: Rp 15K ── Diskon AI: 0%  │ │  │
│  │ └───────────────────────────────────────────────────────────────────────────────┘ │  │
│  │                                                                                   │  │
│  │                                                          [ SIMPAN ATURAN ENGINE ] │  │
│  └───────────────────────────────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 4. Catatan Desain UI/UX untuk Developer

1. **Responsivitas Chat Widget:** Komponen floating chat di sisi *customer* harus menggunakan `position: fixed` dengan `bottom: 80px` agar posisinya berada tepat di atas sticky bar keranjang belanja bawaan Pakaiapp tanpa saling menutupi.
2. **Efek Streaming Teks:** Untuk elemen UI balon chat milik AI (`🤖`), teks yang keluar wajib memanfaatkan pustaka stream rendering untuk memunculkan teks kata-per-kata secara dinamis agar pengguna tidak bosan menunggu proses kompilasi respons token OpenAI secara utuh.
3. **Pemberitahuan Diskon Varian:** Saat taktik *dynamic pricing* dari template sedang aktif, teks harga di dalam katalog menu utama maupun di dalam komponen *product card* chat harus menampilkan efek coret pada harga dasar (contoh: ~~Rp 22.000~~ **Rp 18.700**) untuk memicu efek psikologis urgensi belanja pelanggan.