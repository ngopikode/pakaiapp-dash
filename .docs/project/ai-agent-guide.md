# 🤖 Panduan AI Agent & Direktori Dokumentasi Proyek

Halo, AI Agent (dan Developer)! File ini dibuat khusus untuk memberikan konteks cepat mengenai struktur proyek, teknologi yang digunakan, dan peta dokumentasi. **Baca file ini terlebih dahulu** sebelum menganalisis kode lain untuk menghindari *scanning* seluruh proyek yang memakan banyak token.

## 📌 1. Konteks Proyek
**Nama Proyek:** Pakaiapp POS - Multi-Tenant Retail SaaS  
**Deskripsi:** Aplikasi *Point of Sale* (Kasir) berbasis *Software as a Service* (SaaS) yang dirancang khusus untuk bisnis retail. Aplikasi ini menggunakan arsitektur **Multi-Database Tenancy** untuk memastikan isolasi data dan keamanan tingkat tinggi bagi setiap toko (penyewa).

## 🛠️ 2. Teknologi Utama (Tech Stack)
Proyek ini menggunakan *stack* berikut:
- **Backend:** PHP 8.3+, Laravel 13.x
- **Frontend / Interactivity:** Livewire 4.x
- **UI Component / Styling:** Bootstrap 5.x (Admin Dashboard/POS) & TailwindCSS (Landing Page & Customer QR Menu)
- **Multi-Tenancy:** Stancl/Tenancy (Arsitektur Multi-Database)
- **Asset Bundler:** Vite

## 🏗️ 3. Konsep Arsitektur Penting
- **Isolasi Data Penuh (Multi-Tenancy):** Setiap *tenant* (toko) memiliki *database* fisiknya sendiri untuk keamanan dan pemisahan data. Rute dibagi antara aplikasi utama (SaaS) di `routes/web.php` dan operasional kasir di `routes/tenant.php`.
- **Subdomain Routing:** Akses toko melalui subdomain dinamis (contoh: `namatoko.pakaiapp.online`). Semua komponen Livewire untuk tenant harus di-*render* via rute tenant agar *middleware* Tenancy berjalan.
- **Real-time POS:** Transaksi kasir berjalan instan tanpa *reload* halaman menggunakan fitur Livewire seperti `wire:navigate` dan `wire:model.live`.

## 📁 4. Peta Dokumentasi (Pusat Referensi)

Semua dokumentasi proyek terpusat di direktori [`.docs/`](../../README.md). Berikut peta referensinya:

### Project
| Dokumen | Lokasi | Deskripsi |
| --- | --- | --- |
| **Panduan AI (File ini)** | [`project/ai-agent-guide.md`](./ai-agent-guide.md) | Ringkasan instan (konteks, *tech stack*, arsitektur) untuk AI agent & dev baru. |
| **Architecture Plan** | [`project/architecture-plan.md`](./architecture-plan.md) | Rencana restrukturisasi arsitektur (4 phase). |
| **Flowchart POS Resto** | [`project/pos-resto-flowchart.md`](./pos-resto-flowchart.md) | Alur (*flowchart*) operasional kasir/resto. |
| **Flowchart POS Kitchen** | [`project/pos-kitchen-flowchart.md`](./pos-kitchen-flowchart.md) | Alur pesanan dari kasir ke dapur. |
| **Tenant API Spec** | [`project/api-spec.md`](./api-spec.md) | Spesifikasi REST API untuk kasir dan menu digital. |

### Decisions (ADRs)
| Dokumen | Lokasi | Deskripsi |
| --- | --- | --- |
| **ADR-0001: Tenancy** | [`decisions/0001-multi-database-tenancy.md`](../decisions/0001-multi-database-tenancy.md) | Alasan pemilihan isolasi database fisik untuk tenant. |
| **ADR-0002: Service Injection** | [`decisions/0002-service-injection-guidelines.md`](../decisions/0002-service-injection-guidelines.md) | Aturan standardisasi injeksi dependensi dalam kelas PHP. |
| **ADR-0003: Absolute Variant Pricing** | [`decisions/0003-absolute-variant-pricing.md`](../decisions/0003-absolute-variant-pricing.md) | Kebijakan isolasi harga mutlak di tingkat varian produk. |

### Features — AI Menu Engine
| Dokumen | Lokasi | Deskripsi |
| --- | --- | --- |
| **PRD** | [`features/ai-menu-engine/prd.md`](../features/ai-menu-engine/prd.md) | Product Requirements Document untuk AI Menu Engine. |
| **ERD** | [`features/ai-menu-engine/erd.md`](../features/ai-menu-engine/erd.md) | ERD & Skema Database (Pondasi Data). |
| **Task Breakdown** | [`features/ai-menu-engine/task-breakdown.md`](../features/ai-menu-engine/task-breakdown.md) | Task Breakdown (Tiket Kerja Harian). |
| **Wireframe** | [`features/ai-menu-engine/wireframe.md`](../features/ai-menu-engine/wireframe.md) | Wireframe & UI/UX Flow (Visualisasi Kasar). |
| **Livewire Map** | [`features/ai-menu-engine/livewire-map.md`](../features/ai-menu-engine/livewire-map.md) | Livewire Component Map & Logic Flow. |

### References
| Dokumen | Lokasi | Deskripsi |
| --- | --- | --- |
| **Livewire 4 Components** | [`references/livewire4/components.md`](../references/livewire4/components.md) | Referensi *offline* komponen Livewire 4. Rujuk untuk *best practice*. |
| **MFC + Alpine Timing** | [`references/livewire4/mfc-alpine-architecture.md`](../references/livewire4/mfc-alpine-architecture.md) | *Timing* eksekusi asinkron `.js` MFC Livewire 4 & `Alpine.data()`. |
| **Midtrans Payment Docs** | [`references/midtrans/`](../references/midtrans/) | 26 file dokumentasi payment gateway Midtrans (Snap, CoreAPI, GoPay, dll). |

> Dokumentasi proyek utama (README) tetap di root: [`/README.md`](../../README.md).

---
> **Catatan untuk AI Agent:** Ketika kamu ditugaskan membuat fitur atau memperbaiki *bug*, patuhi tumpukan teknologi di atas (gunakan Livewire/Bootstrap, **jangan** buat komponen Vue/React/Tailwind jika tidak diminta). Perhatikan letak struktur *folder* multi-tenant, terutama perbedaan `migrations/` dan `migrations/tenant/`.
