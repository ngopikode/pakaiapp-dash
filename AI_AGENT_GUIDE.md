# 🤖 Panduan AI Agent & Direktori Dokumentasi Proyek

Halo, AI Agent (dan Developer)! File ini dibuat khusus untuk memberikan konteks cepat mengenai struktur proyek, teknologi yang digunakan, dan peta dokumentasi. **Baca file ini terlebih dahulu** sebelum menganalisis kode lain untuk menghindari *scanning* seluruh proyek yang memakan banyak token.

## 📌 1. Konteks Proyek
**Nama Proyek:** Pakaiapp POS - Multi-Tenant Retail SaaS  
**Deskripsi:** Aplikasi *Point of Sale* (Kasir) berbasis *Software as a Service* (SaaS) yang dirancang khusus untuk bisnis retail. Aplikasi ini menggunakan arsitektur **Multi-Database Tenancy** untuk memastikan isolasi data dan keamanan tingkat tinggi bagi setiap toko (penyewa).

## 🛠️ 2. Teknologi Utama (Tech Stack)
Proyek ini menggunakan *stack* berikut:
- **Backend:** PHP 8.3+, Laravel 13.x
- **Frontend / Interactivity:** Livewire 4.x
- **UI Component / Styling:** Bootstrap 5.x
- **Multi-Tenancy:** Stancl/Tenancy (Arsitektur Multi-Database)
- **Asset Bundler:** Vite

## 🏗️ 3. Konsep Arsitektur Penting
- **Isolasi Data Penuh (Multi-Tenancy):** Setiap *tenant* (toko) memiliki *database* fisiknya sendiri untuk keamanan dan pemisahan data. Rute dibagi antara aplikasi utama (SaaS) di `routes/web.php` dan operasional kasir di `routes/tenant.php`.
- **Subdomain Routing:** Akses toko melalui subdomain dinamis (contoh: `namatoko.pakaiapp.online`). Semua komponen Livewire untuk tenant harus di-*render* via rute tenant agar *middleware* Tenancy berjalan.
- **Real-time POS:** Transaksi kasir berjalan instan tanpa *reload* halaman menggunakan fitur Livewire seperti `wire:navigate` dan `wire:model.live`.

## 📁 4. Peta File Markdown (Pusat Referensi)
Proyek ini sudah dilengkapi file-file dokumentasi berekstensi `.md`. Alih-alih meraba-raba struktur sistem, rujuklah file-file di bawah ini jika kamu membutuhkan referensi spesifik:

| Nama Dokumentasi | Lokasi File | Fungsi / Deskripsi |
| --- | --- | --- |
| **Panduan AI (File ini)** | [`AI_AGENT_GUIDE.md`](./AI_AGENT_GUIDE.md) | Memberikan ringkasan instan (konteks, *tech stack*, arsitektur) untuk agen AI maupun dev baru. |
| **Dokumentasi Proyek (README)** | [`README.md`](./README.md) | Penjelasan fitur utama, panduan instalasi, dan struktur multi-tenant *database*. |
| **Flowchart POS Resto** | [`POS_RESTO_FLOWCHART_ANAYSIS.md`](./POS_RESTO_FLOWCHART_ANAYSIS.md) | Analisis alur (*flowchart*) operasional kasir/resto. |
| **Flowchart POS Kitchen** | [`POS_KITCHEN_FLOWCHART_ANALYSIS.md`](./POS_KITCHEN_FLOWCHART_ANALYSIS.md) | Analisis alur pesanan dari kasir ke dapur. |
| **Docs: Livewire 4 Components**| [`livewire4docs/components.md`](./livewire4docs/components.md) | Referensi dokumentasi *offline* cara kerja komponen Livewire 4. Rujuk file ini untuk *best practice* versi terbaru Livewire. |

---
> **Catatan untuk AI Agent:** Ketika kamu ditugaskan membuat fitur atau memperbaiki *bug*, patuhi tumpukan teknologi di atas (gunakan Livewire/Bootstrap, **jangan** buat komponen Vue/React/Tailwind jika tidak diminta). Perhatikan letak struktur *folder* multi-tenant, terutama perbedaan `migrations/` dan `migrations/tenant/`.
