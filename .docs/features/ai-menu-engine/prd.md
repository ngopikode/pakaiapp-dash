# Product Requirements Document (PRD)

## Dokumen Kontrol
* **Nama Proyek:** Pakaiapp.online (EzMenu Platform)
* **Fitur Utama:** AI Autonomous Menu Engine (Sistem Menu Otomatis & Interaktif Berbasis AI)
* **Versi:** 1.0
* **Status:** Approved for Development
* **Peran Penulis:** Project Manager / Tech Lead

---

## 1. Ringkasan Eksekutif & Filosofi Produk
Pakaiapp diposisikan bukan sekadar sebagai aplikasi Point of Sales (POS) atau menu digital QR statis biasa yang sudah jenuh di pasar (*red ocean*). Pakaiapp adalah sebuah **"Mesin Pengganda Omzet"** bagi pemilik bisnis F&B (Food & Beverage). 

Filosofi dasar pengembangan fitur ini adalah **"Sengaja didesain untuk menaikkan omzet"**. Seluruh subsistem operasional standar (kasir, manajemen stok, cetak struk, laporan keuangan) diturunkan posisinya menjadi fitur pendukung. Nilai jual utama (*flagship core feature*) yang ditawarkan kepada merchant adalah kemampuan kecerdasan buatan (AI) yang bekerja secara otonom untuk mendongkrak *Average Order Value* (AOV) dan mengoptimalkan margin keuntungan melalui rekayasa menu digital yang dinamis.

---

## 2. Metrik Keberhasilan (KPI Proyek)
* **Peningkatan Bisnis (Merchant):** Rata-rata nilai keranjang belanja (*Average Order Value* / AOV) merchant meningkat minimal 15% - 25% dalam waktu 30 hari setelah aktivasi fitur AI.
* **Adopsi Sistem:** Minimal 40% dari total merchant aktif Pakaiapp mengadopsi dan menyalakan modul AI Auto-Pilot ini di dashboard mereka.
* **Retensi Pelanggan:** Tingkat interaksi pelanggan (*engagement rate*) dengan AI Digital Waiter mencapai minimal 60% dari total pemindaian QR menu di meja.

---

## 3. Cakupan Fitur & Kebutuhan Fungsional

### 3.1 Sisi Pelanggan: AI Digital Waiter (B2C)
Modul interaktif yang bertindak sebagai pelayan digital pintar di HP pelanggan setelah melakukan pemindaian QR Code meja.

* **FR-CUST-01: Floating Interractive Chat UI**
  Sistem harus menyediakan antarmuka obrolan (*chat bubble*) yang melayang di atas katalog menu, responsif, ringkas, dan tidak mengganggu navigasi utama menu.
* **FR-CUST-02: Contextual Recommendation Engine**
  AI harus mampu membaca input teks atau suara dari pelanggan (misal: *"Rekomendasi kopi susu yang gak terlalu manis tapi tetep strong buat nemenin nugas, sama camilan asin"*), lalu menganalisis basis data menu aktif, dan memberikan rekomendasi produk yang tepat.
* **FR-CUST-03: Direct Action UI (Add to Cart from Chat)**
  Rekomendasi yang diberikan AI tidak boleh sekadar teks mentah. AI harus menyematkan komponen UI (*Product Card*) yang berisi nama varian, harga, dan tombol **"Tambah ke Keranjang"** secara langsung di dalam balon chat.
* **FR-CUST-04: High-Margin Smart Upselling**
  Sebelum pelanggan menekan tombol *checkout*, AI secara natural wajib menawarkan varian pendamping (*add-on* atau *side dish*) yang memiliki margin keuntungan bersih tertinggi bagi merchant berdasarkan konfigurasi sistem.

### 3.2 Sisi Merchant: AI Auto-Pilot & Dashboard Control (B2B)
Modul konfigurasi dan pemantauan kinerja untuk pemilik restoran/kafe di dalam Dashboard Admin.

* **FR-MERCH-01: One-Click Toggle Activation**
  Merchant dapat mengaktifkan atau menonaktifkan seluruh fungsi otonom AI lewat satu tombol *switch* tunggal yang simpel di dashboard.
* **FR-MERCH-02: Preset Strategy Templates (Set and Forget)**
  Menyediakan *template* strategi bisnis siap pakai tanpa memaksa merchant menyusun logika matematis sendiri:
  * *Template "Happy Hour Anti Sepi":* Otomatis mengaktifkan diskon/penyesuaian harga khusus pada varian tertentu di jam-jam sepi (misal jam 14.00 - 16.00).
  * *Template "Weekend Margin Boost":* Otomatis memprioritaskan rekomendasi varian bermargin tebal saat akhir pekan.
  * *Template "Sikat Habis Stok":* Otomatis mendorong menu yang bahan bakunya melimpah atau mendekati masa kedaluwarsa ke posisi teratas rekomendasi chat.
* **FR-MERCH-03: AI Revenue Financial Summary**
  Dashboard admin wajib menampilkan satu baris metrik keuangan khusus yang mengisolasi nominal keuntungan: *"Bulan ini, Pakaiapp AI menyumbang ekstra omzet sebesar Rp X.XXX.XXX melalui sistem otomatisasi dan upselling."*

---

## 4. Arsitektur Data & Aturan Ketat Database

Untuk menjaga performa aplikasi tetap optimal, bersih, dan menghindari redundansi data, arsitektur database wajib mematuhi aturan berikut:

* **Pusat Harga Mutlak pada Varian (Absolute Variant Pricing Rule):**
  Tabel master `products` **TIDAK BOLEH** menyimpan kolom harga (`price`). Seluruh entitas harga wajib diisolasi penuh di dalam tabel komponen anak, yaitu `variants`. Aturan ini bersifat mutlak untuk mengakomodasi bisnis retail dan F&B skala kompleks.
* **Implikasi pada Modul AI:**
  Ketika fitur *Dynamic Pricing* dari AI Auto-Pilot berjalan (misalnya menurunkan harga kopi ukuran *Large* saat *Happy Hour*), sistem hanya perlu memanipulasi nilai harga atau menyuntikkan harga *override* sementara pada relasi di tabel `variants`. Hal ini menjamin bahwa perhitungan kasir (POS), struk belanja, dapur, dan pelaporan omzet akhir tetap merujuk pada satu titik data yang konsisten tanpa merusak integritas data induk produk.

---

## 5. Spesifikasi Teknis & Integrasi Sistem

Pengembangan fitur ini diselaraskan dengan efisiensi performa tinggi di lingkungan WSL2 dengan target stack sebagai berikut:

* **Backend Engine (Core Logic):** **PHP dengan Framework Laravel**. Bertanggung jawab penuh atas manajemen database, penanganan logika bisnis, pengelolaan *cron jobs / scheduled workers* untuk skema *dynamic pricing*, serta enkapsulasi komunikasi aman menuju API eksternal.
* **Frontend Admin Dashboard:** **Bootstrap & Livewire**. Digunakan untuk membangun halaman manajemen AI di sisi merchant agar interaksi antarmuka tetap dinamis, bersih, *clean*, cepat matang, dan mudah di-maintenance secara independen.
* **Frontend Customer QR Menu:** **React / Next.js**. Diimplementasikan pada sisi pelanggan untuk mengejar performa rendering yang instan dan kapabilitas penanganan *real-time streaming text* (efek mengetik teks AI).
* **AI Integration Layer:** Menggunakan **OpenAI Responses API / Conversations API** resmi (Menghindari penggunaan Assistants API lawas yang sudah didepresiasi). Koneksi dikelola menggunakan *Service Class* khusus di Laravel untuk mengirimkan skrip *System Prompt* ketat, data struktural menu harian (diambil dari tabel `variants`), dan sisa stok bahan.
* **Streaming Handler:** Memanfaatkan **Vercel AI SDK** di sisi *frontend* pelanggan untuk menangani transmisi *buffer* teks dari OpenAI secara *real-time* ke komponen UI chat agar menghemat beban memori server.

---

## 6. Alur Pengguna (User Flow)

### 6.1 Alur Aktivasi Strategi oleh Merchant
```
[Login Dashboard Admin] -> [Masuk Menu AI Engine] -> [Pilih Preset Template (e.g. Happy Hour)] 
  -> [Set Jam & Varian Target] -> [Klik Switch ON] -> [Laravel Scheduler Mengunci Rule]
```

### 6.2 Alur Pemesanan Interaktif oleh Pelanggan
```
[Scan QR Meja] -> [Buka Floating Chat AI] -> [Input Request: "Menu non-kopi segar"] 
  -> [Laravel Mengirim Konteks Varian Aktif ke OpenAI] -> [OpenAI Memproses Respon Struktural] 
  -> [Next.js Merender Teks Streaming + Product Card Varian] -> [Pelanggan Klik "Tambah ke Keranjang"] 
  -> [Varian Masuk Cart POS Resto]
```

---

## 7. Batasan Sistem, Keamanan, & Penanganan Kasus Batas (Edge Cases)

* **Kontrol Halusinasi AI (Strict Context Guard):**
  *System prompt* OpenAI harus dikunci seketat mungkin. AI dilarang keras menjawab pertanyaan di luar ruang lingkup menu restoran merchant, dilarang membahas kompetitor, dan dilarang memberikan diskon di luar nominal yang terdaftar secara sah di database aturan `variants`.
* **Efisiensi Token & Biaya API (Token Cost Management):**
  Sesi percakapan (*conversation state*) antara satu pelanggan di meja dibatasi maksimal 5-7 putaran percakapan (*turns*). Jika melewati batas, sesi chat akan diarsip otomatis dan pelanggan diarahkan untuk langsung melakukan *checkout* guna menekan konsumsi biaya API Key OpenAI.
* **Kecepatan Respon (Latency Threshold):**
  Waktu tunggu sejak pelanggan menekan tombol kirim chat hingga teks pertama muncul (*Time to First Token*) tidak boleh melebihi 2.0 detik. Jika koneksi API OpenAI mengalami keterlambatan (*timeout*), sistem harus melakukan *fallback* otomatis dengan menampilkan opsi menu statis paling populer dari database lokal.