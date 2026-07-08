# Dokumentasi Pakaiapp Dash

Pusat dokumentasi proyek. Semua file dokumentasi terorganisir di sini.

---

## Project

Dokumen tingkat proyek: konteks, arsitektur, dan analisis bisnis.

| Dokumen | Deskripsi |
|---------|-----------|
| [AI Agent Guide](project/ai-agent-guide.md) | Konteks proyek, tech stack, dan peta dokumentasi untuk AI agent & dev baru |
| [Architecture Plan](project/architecture-plan.md) | Rencana restrukturisasi arsitektur (4 phase) |
| [POS Resto Flowchart](project/pos-resto-flowchart.md) | Alur bisnis kasir resto (Mermaid flowchart) |
| [POS Kitchen Flowchart](project/pos-kitchen-flowchart.md) | Alur bisnis dapur/kitchen (Mermaid flowchart) |
| [Tenant API Specification](project/api-spec.md) | Spesifikasi REST API untuk kasir dan menu digital |

---

## Decisions (ADRs)

Architecture Decision Records (ADRs) yang mendokumentasikan keputusan teknis besar beserta konteks dan konsekuensinya.

| Dokumen | Deskripsi |
|---------|-----------|
| [ADR-0001: Multi-Database Tenancy](decisions/0001-multi-database-tenancy.md) | Alasan pemilihan isolasi database fisik untuk tenant |
| [ADR-0002: Service Injection](decisions/0002-service-injection-guidelines.md) | Aturan standardisasi injeksi dependensi dalam kelas PHP |
| [ADR-0003: Absolute Variant Pricing](decisions/0003-absolute-variant-pricing.md) | Kebijakan isolasi harga mutlak di tingkat varian produk |

---

## Features

Dokumen spesifikasi fitur: PRD, ERD, wireframe, task breakdown.

### AI Menu Engine

Fitur unggulan: AI Autonomous Menu Engine untuk optimasi omzet merchant.

| Dokumen | Deskripsi |
|---------|-----------|
| [PRD](features/ai-menu-engine/prd.md) | Product Requirements Document |
| [ERD](features/ai-menu-engine/erd.md) | ERD & skema database |
| [Task Breakdown](features/ai-menu-engine/task-breakdown.md) | Sprint task breakdown |
| [Wireframe](features/ai-menu-engine/wireframe.md) | Wireframe & UI/UX flow |
| [Livewire Map](features/ai-menu-engine/livewire-map.md) | Livewire component map & logic flow |

---

## References

Dokumentasi referensi eksternal (offline).

### Livewire 4

Dokumentasi resmi untuk Livewire 4 yang diambil langsung dari internet, disusun sesuai urutan navigasi resmi:

#### Getting Started
| Dokumen | Deskripsi |
|---------|-----------|
| [Quickstart](references/livewire4/getting-started/quick-start.md) | Panduan cepat untuk mulai menggunakan Livewire |
| [Installation](references/livewire4/getting-started/installation.md) | Panduan instalasi dan setup awal Livewire |
| [Upgrade Guide](references/livewire4/getting-started/upgrade-guide.md) | Panduan upgrade dari Livewire v3 ke v4 |
| [UI Components](https://fluxui.dev) | Komponen UI eksternal (Flux UI) |

#### Essentials
| Dokumen | Deskripsi |
|---------|-----------|
| [Components](references/livewire4/essentials/components.md) | Referensi dasar komponen Livewire 4 |
| [Pages](references/livewire4/essentials/pages.md) | Panduan penggunaan komponen sebagai halaman penuh |
| [Properties](references/livewire4/essentials/properties.md) | Panduan state management dan public properties |
| [Actions](references/livewire4/essentials/actions.md) | Panduan menangani interaksi pengguna dan PHP methods |
| [Forms](references/livewire4/essentials/forms.md) | Panduan menangani data binding dan validasi formulir |
| [Events](references/livewire4/essentials/events.md) | Panduan komunikasi antar komponen menggunakan event |
| [Lifecycle Hooks](references/livewire4/essentials/lifecycle-hooks.md) | Panduan hook siklus hidup komponen |
| [Nesting Components](references/livewire4/essentials/nesting.md) | Panduan nested / sub-components di Livewire |
| [Testing](references/livewire4/essentials/testing.md) | Panduan unit testing untuk komponen Livewire |

#### Features
| Dokumen | Deskripsi |
|---------|-----------|
| [Alpine](references/livewire4/features/alpine.md) | Integrasi erat Livewire dengan Alpine.js |
| [Styles](references/livewire4/features/styles.md) | Panduan styling dan assets loading |
| [Navigate](references/livewire4/features/navigate.md) | SPA-like navigation menggunakan wire:navigate |
| [Islands](references/livewire4/features/islands.md) | Konsep rendering dan isolasi Livewire Islands |
| [Lazy Loading](references/livewire4/features/lazy.md) | Menunda pemuatan komponen berat / lazy load |
| [Loading States](references/livewire4/features/loading-states.md) | Menampilkan loading indicators secara dinamis |
| [Validation](references/livewire4/features/validation.md) | Validasi input form secara real-time dan backend |
| [File Uploads](references/livewire4/features/uploads.md) | Penanganan file upload di Livewire |
| [Pagination](references/livewire4/features/pagination.md) | Penanganan paginasi data secara dinamis |
| [URL Query Parameters](references/livewire4/features/url.md) | Sinkronisasi state komponen dengan URL query string |
| [Computed Properties](references/livewire4/features/computed-properties.md) | Caching data menggunakan Computed Properties |
| [Redirecting](references/livewire4/features/redirecting.md) | Melakukan pengalihan halaman di Livewire |
| [File Downloads](references/livewire4/features/downloads.md) | Mengirimkan file unduhan ke client |
| [Teleport](references/livewire4/features/teleport.md) | Memindahkan elemen HTML menggunakan Teleport |

#### HTML Directives
| Dokumen | Deskripsi |
|---------|-----------|
| [wire:bind](references/livewire4/html-directives/wire-bind.md) | Mengikat data PHP ke elemen HTML |
| [wire:click](references/livewire4/html-directives/wire-click.md) | Menangani event klik mouse |
| [wire:submit](references/livewire4/html-directives/wire-submit.md) | Menangani pengiriman form |
| [wire:model](references/livewire4/html-directives/wire-model.md) | Sinkronisasi data binding dua arah |
| [wire:loading](references/livewire4/html-directives/wire-loading.md) | Mengontrol visibility loading states |
| [wire:navigate](references/livewire4/html-directives/wire-navigate.md) | Navigasi halaman cepat tanpa reload |
| [wire:current](references/livewire4/html-directives/wire-current.md) | Styling elemen menu berdasarkan rute aktif |
| [wire:cloak](references/livewire4/html-directives/wire-cloak.md) | Menyembunyikan elemen sebelum Livewire terinisialisasi |
| [wire:dirty](references/livewire4/html-directives/wire-dirty.md) | Mendeteksi modifikasi input yang belum disimpan |
| [wire:confirm](references/livewire4/html-directives/wire-confirm.md) | Meminta konfirmasi browser sebelum mengeksekusi aksi |
| [wire:transition](references/livewire4/html-directives/wire-transition.md) | Menambahkan animasi transisi pada elemen |
| [wire:init](references/livewire4/html-directives/wire-init.md) | Menjalankan aksi PHP segera setelah komponen dimuat |
| [wire:intersect](references/livewire4/html-directives/wire-intersect.md) | Mendeteksi elemen saat masuk ke viewport |
| [wire:poll](references/livewire4/html-directives/wire-poll.md) | Memperbarui komponen secara berkala (polling) |
| [wire:offline](references/livewire4/html-directives/wire-offline.md) | Menampilkan pesan saat koneksi internet terputus |
| [wire:ignore](references/livewire4/html-directives/wire-ignore.md) | Mengabaikan pembaruan DOM oleh Livewire |
| [wire:ref](references/livewire4/html-directives/wire-ref.md) | Mereferensikan elemen HTML di JavaScript |
| [wire:replace](references/livewire4/html-directives/wire-replace.md) | Mengganti elemen DOM saat pembaruan |
| [wire:show](references/livewire4/html-directives/wire-show.md) | Mengontrol tampilan elemen berdasarkan kondisi |
| [wire:sort](references/livewire4/html-directives/wire-sort.md) | Integrasi drag-and-drop sorting |
| [wire:stream](references/livewire4/html-directives/wire-stream.md) | Streaming data ke client secara real-time |
| [wire:text](references/livewire4/html-directives/wire-text.md) | Mengganti text content elemen secara dinamis |

#### PHP Attributes
| Dokumen | Deskripsi |
|---------|-----------|
| [Async](references/livewire4/php-attributes/attribute-async.md) | Menjalankan method secara asinkron |
| [Computed](references/livewire4/php-attributes/attribute-computed.md) | Mendeklarasikan property dinamis ter-caching |
| [Defer](references/livewire4/php-attributes/attribute-defer.md) | Menunda pengiriman update property ke server |
| [Isolate](references/livewire4/php-attributes/attribute-isolate.md) | Mengisolasi pembaruan DOM komponen |
| [Js](references/livewire4/php-attributes/attribute-js.md) | Menulis JavaScript inline di dalam class PHP |
| [Json](references/livewire4/php-attributes/attribute-json.md) | Memastikan serialization data dalam format JSON |
| [Layout](references/livewire4/php-attributes/attribute-layout.md) | Menentukan layout Blade untuk komponen halaman |
| [Lazy](references/livewire4/php-attributes/attribute-lazy.md) | Penanda pemuatan lazy load pada halaman |
| [Locked](references/livewire4/php-attributes/attribute-locked.md) | Mengunci property agar tidak bisa dimodifikasi dari client |
| [Modelable](references/livewire4/php-attributes/attribute-modelable.md) | Memetakan state luar ke inner component |
| [On](references/livewire4/php-attributes/attribute-on.md) | Mendaftarkan listener untuk event Livewire |
| [Reactive](references/livewire4/php-attributes/attribute-reactive.md) | Mendeklarasikan reactive properties dari parent ke child |
| [Renderless](references/livewire4/php-attributes/attribute-renderless.md) | Menjalankan action PHP tanpa merender ulang view |
| [Session](references/livewire4/php-attributes/attribute-session.md) | Menyimpan state property ke Laravel Session |
| [Title](references/livewire4/php-attributes/attribute-title.md) | Mengatur judul halaman secara dinamis |
| [Transition](references/livewire4/php-attributes/attribute-transition.md) | Menambahkan efek transisi ke component |
| [Url](references/livewire4/php-attributes/attribute-url.md) | Menautkan state property ke URL query string |
| [Validate](references/livewire4/php-attributes/attribute-validate.md) | Mendeklarasikan aturan validasi form |

#### Blade Directives
| Dokumen | Deskripsi |
|---------|-----------|
| [@island](references/livewire4/blade-directives/directive-island.md) | Inisialisasi Livewire island di dalam blade |
| [@placeholder](references/livewire4/blade-directives/directive-placeholder.md) | Template loading placeholder untuk lazy component |
| [@persist](references/livewire4/blade-directives/directive-persist.md) | Menjaga state elemen tetap persisten saat pindah halaman |
| [@teleport](references/livewire4/blade-directives/directive-teleport.md) | Merender elemen HTML ke lokasi DOM yang lain |

#### Advanced
| Dokumen | Deskripsi |
|---------|-----------|
| [Morphing](references/livewire4/advanced/morphing.md) | Detail mekanisme DOM morphing Livewire |
| [Hydration](references/livewire4/advanced/hydration.md) | Siklus hidup data hydration & dehydration |
| [Nesting](references/livewire4/advanced/understanding-nesting.md) | Pemahaman mendalam arsitektur nested components |
| [Troubleshooting](references/livewire4/advanced/troubleshooting.md) | Penanganan masalah umum & teknik debugging |
| [Security](references/livewire4/advanced/security.md) | Proteksi keamanan dan penanganan form hijacking |
| [CSP](references/livewire4/advanced/csp.md) | Panduan konfigurasi Content Security Policy |
| [JavaScript](references/livewire4/advanced/javascript.md) | Interaksi lanjutan dengan script JS custom |
| [Synthesizers](references/livewire4/advanced/synthesizers.md) | Serialisasi custom data types (Hydration) |
| [Package Development](references/livewire4/advanced/packages.md) | Panduan pengembangan package dengan Livewire |
| [Contribution Guide](references/livewire4/advanced/contribution-guide.md) | Panduan kontribusi ke repositori Livewire |

#### Architecture
| Dokumen | Deskripsi |
|---------|-----------|
| [MFC + Alpine Architecture](references/livewire4/mfc-alpine-architecture.md) | Panduan timing MFC + Alpine.js |

### Midtrans

Dokumentasi payment gateway Midtrans (Snap, CoreAPI, GoPay, dll).

| Dokumen | Deskripsi |
|---------|-----------|
| [Payment Overview](references/midtrans/payment-overview.md) | Overview Midtrans payments |
| [Snap](references/midtrans/snap.md) | Built-in interface (Snap) |
| [Snap Integration Guide](references/midtrans/snap-snap-integration-guide.md) | Panduan integrasi Snap |
| [Snap Preparation](references/midtrans/snap-preparation.md) | Persiapan integrasi Snap |
| [Snap Interactive Demo](references/midtrans/snap-interactive-demo.md) | Demo interaktif Snap |
| [Snap Advanced Feature](references/midtrans/snap-advanced-feature.md) | Fitur lanjutan Snap |
| [CoreAPI Bank Transfer](references/midtrans/coreapi-core-api-bank-transfer-integration.md) | Integrasi bank transfer |
| [CoreAPI E-Money](references/midtrans/coreapi-e-money-integration.md) | Integrasi e-money |
| [CoreAPI Over The Counter](references/midtrans/coreapi-over-the-counter-payment-integration.md) | Pembayaran over the counter |
| [CoreAPI Card Payment](references/midtrans/coreapi-card-payment-integration.md) | Pembayaran kartu |
| [CoreAPI Cardless Credit](references/midtrans/coreapi-cardless-credit-payment-integration.md) | Cardless credit |
| [CoreAPI Advanced Features](references/midtrans/coreapi-advanced-features.md) | Fitur lanjutan CoreAPI |
| [GoPay QRIS POS](references/midtrans/gopay-qris-pos-integration.md) | Integrasi GoPay QRIS POS |
| [GoPay Static QRIS](references/midtrans/gopay-static-qris.md) | GoPay static QRIS |
| [API Authorization Headers](references/midtrans/api-authorization-headers.md) | Header autorisasi API |
| [Notification Webhooks](references/midtrans/https-notification-webhooks.md) | Webhook notifikasi |
| [Get Status API](references/midtrans/get-status-api-requests.md) | API cek status transaksi |
| [Error & Response Codes](references/midtrans/error-code-and-response-code.md) | Daftar kode error |
| [IP Addresses](references/midtrans/ip-address.md) | Daftar IP Midtrans |
| [Midtrans Account](references/midtrans/midtrans-account.md) | Akun Midtrans |
| [API Libraries & Plugins](references/midtrans/midtrans-api-libraries-plugins.md) | Library & plugin |
| [Custom Interface CoreAPI](references/midtrans/custom-interface-core-api.md) | Custom interface CoreAPI |
| [Ecommerce Platform](references/midtrans/ecommerce-platform.md) | Integrasi platform e-commerce |
| [Testing on Sandbox](references/midtrans/testing-payment-on-sandbox.md) | Testing di sandbox |
| [Transaction Status Cycle](references/midtrans/transaction-status-cycle.md) | Siklus status transaksi |
| [Technical Reference](references/midtrans/technical-reference.md) | Referensi teknis & developer tools |

---

## Archive

Arsip dokumen perencanaan dan riwayat refaktorisasi sistem:

| Dokumen | Deskripsi |
|---------|-----------|
| [Phase 2: Domain Separation](archive/refactors/PHASE2_DOMAIN_SEPARATION.md) | Catatan migrasi pemisahan domain Central dan Tenant |
| [Phase 3: View & Asset Restructuring](archive/refactors/PHASE3_VIEW_ASSET_RESTRUCTURING.md) | Catatan restrukturisasi berkas tampilan dan asset |
| [Central Refactoring Plan](archive/refactors/REFACTOR_CENTRAL.md) | Log pembersihan Controller & Service di Central |
| [Livewire Standardization Plan](archive/refactors/REFACTOR_LIVEWIRE_STANDARDIZATION.md) | Standardisasi pemanggilan Livewire 4 & Alpine.js |
| [Spatie Data Migration](archive/refactors/REFACTOR_SPATIE_DATA.md) | Migrasi request validation ke Spatie Data Objects |
| [Tenant Refactoring Plan](archive/refactors/REFACTOR_TENANT.md) | Log pembersihan Controller & Service di Tenant |
| [Webhook Refactoring Plan](archive/refactors/REFACTOR_WEBHOOK.md) | Perombakan sistem terima callback Webhook pembayaran |
| [Livewire 4 Features Plan](archive/refactors/LIVEWIRE4_FEATURES_PLAN.md) | Rencana awal adopsi fitur-fitur baru Livewire 4 |

---

> **Catatan:** Dokumentasi proyek utama (README) tetap berada di root repository (`/README.md`).
