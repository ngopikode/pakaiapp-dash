<!DOCTYPE html>
<html lang="id" data-bs-theme="light" id="html-root">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- ==============================================
           SEO ON-PAGE & META TAGS
      =============================================== -->
    <!-- Primary Meta Tags -->
    <title>Pakaiapp - Aplikasi Kasir Pintar UMKM Tanpa Biaya Bulanan</title>
    <meta name="title" content="Pakaiapp - Aplikasi Kasir Pintar UMKM Tanpa Biaya Bulanan">
    <meta name="description"
          content="Tinggalkan biaya langganan! Pakaiapp adalah aplikasi kasir (POS) berbasis cloud untuk UMKM F&B dan Retail. Sistem adil, cuma bayar Rp 300 per transaksi sukses.">
    <meta name="keywords"
          content="aplikasi kasir, kasir pintar, POS F&B, kasir UMKM, aplikasi kasir tanpa langganan, kasir cafe, sistem kasir retail, pakaiapp, ngopikode, aplikasi kasir medan">
    <meta name="author" content="PT Sinergi Kode Kreatif">
    <meta name="robots" content="index, follow">
    <meta name="language" content="Indonesian">

    <!-- Canonical URL (Penting biar Google nggak bingung kalau ada duplicate URL) -->
    <link rel="canonical" href="https://pakaiapp.online/">

    <!-- Open Graph / Facebook / WhatsApp (Untuk Thumbnail saat Link di-share) -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://pakaiapp.online/">
    <meta property="og:title" content="Pakaiapp - Kasir Pintar Bayar Suka-Suka">
    <meta property="og:description"
          content="Kasir sepi = Gratis. Kasir ramai = Tetap murah. Revolusi sistem kasir SaaS untuk F&B dan Retail dengan skema Rp 300/transaksi sukses.">
    <meta property="og:image" content="{{ asset('images/pakaiapp-og-banner.jpg') }}">
    <!-- Pastikan bikin gambar banner ukuran 1200x630px -->

    <!-- Twitter / X -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://pakaiapp.online/">
    <meta property="twitter:title" content="Pakaiapp - Kasir Pintar Bayar Suka-Suka">
    <meta property="twitter:description"
          content="Tinggalkan biaya langganan bulanan. Pindah ke Pakaiapp sekarang dan nikmati fitur kasir enterprise dengan harga UMKM.">
    <meta property="twitter:image" content="{{ asset('images/pakaiapp-og-banner.jpg') }}">

    <!-- ==============================================
         STRUCTURED DATA (JSON-LD) UNTUK GOOGLE RICH SNIPPET
    =============================================== -->
    <script type="application/ld+json">
        {
      "@@context": "https://schema.org",
      "@@type": "SoftwareApplication",
      "name": "Pakaiapp.online",
      "operatingSystem": "Web, Android, iOS (PWA)",
      "applicationCategory": "BusinessApplication",
      "description": "Sistem kasir pintar (POS) berbasis cloud untuk UMKM F&B dan Retail tanpa biaya langganan bulanan. Menggunakan skema potong saldo Rp 300 per transaksi.",
      "url": "https://www.pakaiapp.online",
      "offers": {
        "@type": "Offer",
        "price": "0",
        "priceCurrency": "IDR",
        "description": "Pendaftaran gratis, biaya penggunaan hanya Rp 300 per transaksi sukses."
      },
      "creator": {
        "@@type": "Organization",
        "name": "PT Sinergi Kode Kreatif (ngopikode)",
        "url": "https://www.ngopikode.com"
      }
    }
    </script>

    <!-- External Libraries for OP Interactivity -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Vite Assets (Pastikan app.scss kamu sudah update sesuai yang sebelumnya) -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        :root {
            /* Premium Minimalist SaaS Theme Variables */
            --bs-body-bg: #ffffff; /* Clean white background */
            --bs-body-color: #334155; /* Slate 700 for readable content */
            --bs-primary: #0f172a; /* Slate 900 for dark premium headings */
            --bs-primary-rgb: 15, 23, 42;
            --bs-secondary: #475569; /* Slate 600 */
            --bs-warning: #d97706; /* Sophisticated Amber instead of raw yellow */
            --bs-warning-rgb: 217, 119, 6;
            --bs-border-color: rgba(15, 23, 42, 0.06); /* Super thin hairline border */
            --bs-card-bg: #ffffff;
            
            --card-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px -1px rgba(0, 0, 0, 0.05);
            --card-shadow-hover: 0 10px 15px -3px rgba(0, 0, 0, 0.04), 0 4px 6px -4px rgba(0, 0, 0, 0.04);
        }

        [data-bs-theme="dark"] {
            --bs-body-bg: #0b0f19; /* Sleek Deep Navy/Slate */
            --bs-body-color: #cbd5e1; /* Slate 300 */
            --bs-primary: #f8fafc; /* Slate 50 */
            --bs-primary-rgb: 248, 250, 252;
            --bs-secondary: #94a3b8; /* Slate 400 */
            --bs-warning: #f59e0b; /* Amber */
            --bs-warning-rgb: 245, 158, 11;
            --bs-border-color: rgba(255, 255, 255, 0.07); /* Thin dark hairline border */
            --bs-card-bg: #111827; /* Gray 900 card bg */
            
            --card-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.2), 0 1px 2px -1px rgba(0, 0, 0, 0.2);
            --card-shadow-hover: 0 10px 15px -3px rgba(0, 0, 0, 0.3), 0 4px 6px -4px rgba(0, 0, 0, 0.3);
        }

        body {
            background-color: var(--bs-body-bg) !important;
            color: var(--bs-body-color) !important;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Typography & Modern Spacing */
        h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .display-4 {
            color: var(--bs-primary) !important;
            letter-spacing: -0.02em !important;
            line-height: 1.2 !important;
            font-weight: 800 !important;
        }
        
        .lead {
            color: var(--bs-secondary) !important;
            line-height: 1.6;
        }

        /* Premium Minimalist Cards Override */
        .glass-card, .feature-card, .card {
            background-color: var(--bs-card-bg) !important;
            border: 1px solid var(--bs-border-color) !important;
            box-shadow: var(--card-shadow) !important;
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
            border-radius: 16px !important;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        .glass-card:hover, .feature-card:hover, .card:hover {
            transform: translateY(-4px) !important;
            box-shadow: var(--card-shadow-hover) !important;
            border-color: rgba(var(--bs-warning-rgb), 0.2) !important;
        }

        /* Sleek Intercom-like Chat Widget */
        #chat-container {
            background-color: var(--bs-card-bg) !important;
            font-size: 0.875rem;
        }

        .chat-bubble-bot {
            background-color: var(--bs-body-bg) !important;
            color: var(--bs-body-color) !important;
            border: 1px solid var(--bs-border-color) !important;
            box-shadow: none !important;
            border-radius: 14px 14px 14px 4px !important;
            padding: 10px 14px !important;
            max-width: 82% !important;
        }

        .chat-bubble-user {
            background-color: var(--bs-primary) !important;
            color: var(--bs-card-bg) !important;
            border: none !important;
            box-shadow: none !important;
            border-radius: 14px 14px 4px 14px !important;
            padding: 10px 14px !important;
            max-width: 82% !important;
        }

        /* Beautiful minimalist pill buttons for choices */
        .chat-btn {
            border: 1px solid var(--bs-border-color) !important;
            background-color: var(--bs-card-bg) !important;
            color: var(--bs-body-color) !important;
            border-radius: 24px !important;
            padding: 6px 14px !important;
            font-size: 0.825rem !important;
            font-weight: 600 !important;
            transition: all 0.2s ease-in-out !important;
            box-shadow: var(--card-shadow) !important;
        }

        .chat-btn:hover {
            background-color: var(--bs-primary) !important;
            color: var(--bs-card-bg) !important;
            border-color: var(--bs-primary) !important;
            transform: translateY(-1px) !important;
            box-shadow: var(--card-shadow-hover) !important;
        }

        #register-form {
            border: 1px solid var(--bs-border-color) !important;
            background-color: var(--bs-card-bg) !important;
            border-radius: 14px !important;
        }

        .typing-indicator span {
            display: inline-block;
            width: 7px;
            height: 7px;
            background-color: var(--bs-secondary);
            border-radius: 50%;
            margin: 0 1.5px;
            animation: bounce 1.4s infinite ease-in-out both;
        }

        .typing-indicator span:nth-child(1) {
            animation-delay: -0.32s;
        }

        .typing-indicator span:nth-child(2) {
            animation-delay: -0.16s;
        }

        @keyframes bounce {
            0%, 80%, 100% {
                transform: scale(0);
            }
            40% {
                transform: scale(1);
            }
        }

        /* Range Slider Styling */
        input[type=range]::-webkit-slider-thumb {
            background: var(--bs-warning);
            cursor: pointer;
        }

        /* Premium Floating WhatsApp Button */
        .floating-wa {
            position: fixed;
            bottom: 32px;
            right: 32px;
            width: 56px;
            height: 56px;
            background-color: #22c55e !important; /* Premium WhatsApp Green */
            color: #ffffff !important;
            border-radius: 50% !important;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3), 0 2px 4px rgba(0, 0, 0, 0.05) !important;
            z-index: 9999;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            text-decoration: none !important;
        }

        .floating-wa:hover {
            transform: translateY(-4px) scale(1.05) !important;
            box-shadow: 0 8px 24px rgba(34, 197, 94, 0.4), 0 4px 8px rgba(0, 0, 0, 0.08) !important;
            background-color: #16a34a !important; /* Slightly darker hover green */
        }
    </style>
</head>
<body class="bg-body position-relative" style="overflow-x: hidden;">

<!-- Navbar dengan Toggle Dark Mode -->
<nav class="navbar navbar-expand-lg navbar-light bg-transparent py-4 position-absolute w-100 z-3" data-aos="fade-down">
    <div class="container d-flex justify-content-between">
        <a class="navbar-brand fw-black fs-4 text-body" href="#">
            <i class="bi bi-cup-hot-fill text-warning me-2"></i>pakaiapp<span class="text-secondary">.online</span>
        </a>
        <button id="themeToggle" class="btn btn-outline-secondary rounded-circle" title="Ganti Tema">
            <i class="bi bi-moon-stars-fill"></i>
        </button>
    </div>
</nav>

<!-- HERO SECTION: Typed.js & Chat UI -->
<section class="hero-section position-relative py-5 py-md-6 mt-4 overflow-hidden">
    <div class="container position-relative z-1 mt-4">
        <div class="row align-items-center">

            <!-- Teks Kiri -->
            <div class="col-lg-6 text-center text-lg-start mb-5 mb-lg-0" data-aos="fade-right">
                <span
                    class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-2 fw-bold border border-warning border-opacity-25 mb-3">
                    <i class="bi bi-stars me-1"></i> Revolusi UMKM F&B
                </span>
                <h1 class="display-4 fw-black mb-3 text-body" style="letter-spacing: -0.02em; line-height: 1.2;">
                    Kasir Pintar, <br>
                    <span class="text-gradient" id="typed-text"></span>
                </h1>
                <p class="lead text-secondary opacity-75 mb-4">
                    Sistem SaaS berbasis *Cloud* dengan arsitektur Varian Harga cerdas. Dirancang khusus oleh *Indie
                    Programmer* agar UMKM bisa *Go Digital* tanpa dipalak biaya bulanan.
                </p>
                <div class="d-flex gap-3 justify-content-center justify-content-lg-start">
                    <a href="#simulasi"
                       class="btn btn-warning btn-lg rounded-pill px-4 fw-bold shadow-sm btn-hover-grow text-dark">
                       <i class="bi bi-calculator me-2"></i>Hitung Untungmu
                    </a>
                </div>
            </div>

            <!-- Chat Kanan (Glassmorphism) -->
            <div class="col-lg-6" data-aos="fade-left" data-aos-delay="200">
                <div class="card glass-card overflow-hidden">
                    <div class="bg-body-tertiary p-3 border-bottom d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div
                                class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center position-relative"
                                style="width: 40px; height: 40px;">
                                <i class="bi bi-robot text-primary fs-5"></i>
                                <span
                                    class="position-absolute bottom-0 end-0 p-1 bg-success border border-light rounded-circle"></span>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold" style="font-size: 0.95rem;">Pakaiapp Assistant</h6>
                                <small class="text-muted" style="font-size: 11px;">Aktif & Responsif</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 fw-semibold" style="font-size: 10px; letter-spacing: 0.05em;">ONLINE</span>
                        </div>
                    </div>

                    <div class="p-4 bg-body" id="chat-container"
                         style="height: 380px; overflow-y: auto; scroll-behavior: smooth;">
                        <!-- Chat mulai masuk via JS nanti -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- INTERACTIVE SIMULATOR (OP Feature!) -->
<section id="simulasi" class="py-5 py-md-6 bg-body-tertiary border-top border-bottom position-relative">
    <div class="container py-5 text-center">
        <h2 class="fw-black text-body mb-2" data-aos="zoom-in">Simulasi Anti-Buntung</h2>
        <p class="text-secondary mb-5" data-aos="zoom-in" data-aos-delay="100">Buktikan sendiri seberapa hemat pakai
            sistem potong Rp 300 / transaksi sukses.</p>

        <div class="card bg-body border-0 shadow-sm rounded-4 p-4 p-md-5 mx-auto" style="max-width: 800px;"
             data-aos="fade-up">
            <h5 class="fw-bold mb-4">Hari ini toko kamu dapat berapa pesanan?</h5>

            <input type="range" class="form-range" id="trxSlider" min="0" max="100" value="15" step="1">
            <div class="d-flex justify-content-between text-secondary small fw-bold mt-2">
                <span>0 (Tutup/Sepi)</span>
                <span>100+ (Rame Parah!)</span>
            </div>

            <div class="mt-5 text-center">
                <h1 class="display-1 fw-black text-primary" id="trxDisplay">15</h1>
                <p class="text-secondary fw-bold text-uppercase tracking-wider">Transaksi Sukses Hari Ini</p>
            </div>

            <hr class="my-4 text-secondary opacity-25">

            <div class="row text-center g-4">
                <div class="col-md-6 border-end">
                    <p class="mb-1 text-secondary">Biaya Pakaiapp Hari Ini</p>
                    <h3 class="fw-black text-success" id="costPakaiapp">Rp 4.500</h3>
                    <small class="text-success"><i class="bi bi-check-circle-fill me-1"></i>Adil sesuai omzet</small>
                </div>
                <div class="col-md-6">
                    <p class="mb-1 text-secondary">Biaya App Langganan Bulanan</p>
                    <h3 class="fw-black text-danger">Rp 6.600 <span class="fs-6 text-muted">/hari</span></h3>
                    <small class="text-danger"><i class="bi bi-x-circle-fill me-1"></i>Toko sepi tetap bayar</small>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FEATURES SECTION 3D (Vanilla Tilt) -->
<section class="py-5 py-md-6 bg-body border-bottom">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="fw-black text-body">Fitur Enterprise, Harga UMKM</h2>
            <p class="text-secondary">Arsitektur *backend* yang matang, bukan sekadar CRUD biasa.</p>
        </div>

        <div class="row g-4">
            <!-- 3D Card 1 -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card h-100 border-0 shadow-sm p-4 rounded-4 feature-card bg-body-tertiary" data-tilt
                     data-tilt-glare data-tilt-max-glare="0.2" data-tilt-scale="1.05">
                    <div class="text-primary mb-3 display-4"><i class="bi bi-diagram-3-fill"></i></div>
                    <h4 class="fw-bold">Varian Dinamis</h4>
                    <p class="text-secondary small mb-0">Manajemen harga berdasarkan *Size* atau Tipe. Relasi tabel
                        bersih, tanpa duplikasi data menu. Sangat ringan saat dirender.</p>
                </div>
            </div>
            <!-- 3D Card 2 -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card h-100 border-0 shadow-sm p-4 rounded-4 feature-card bg-body-tertiary" data-tilt
                     data-tilt-glare data-tilt-max-glare="0.2" data-tilt-scale="1.05">
                    <div class="text-warning mb-3 display-4"><i class="bi bi-qr-code-scan"></i></div>
                    <h4 class="fw-bold">QR Self-Order</h4>
                    <p class="text-secondary small mb-0">Cetak QR di tiap meja. Pelanggan scan, pesan, dan bisa langsung
                        diarahkan ke *Payment Gateway*. Kasir tinggal pantau layar.</p>
                </div>
            </div>
            <!-- 3D Card 3 -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="card h-100 border-0 shadow-sm p-4 rounded-4 feature-card bg-body-tertiary" data-tilt
                     data-tilt-glare data-tilt-max-glare="0.2" data-tilt-scale="1.05">
                    <div class="text-success mb-3 display-4"><i class="bi bi-wallet2"></i></div>
                    <h4 class="fw-bold">Top-up Fleksibel</h4>
                    <p class="text-secondary small mb-0">Sistem dompet digital (*Wallet*). Top-up mulai Rp 15.000 saja
                        sudah bisa untuk 50 transaksi. Uang tidak akan pernah hangus.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ SECTION -->
<section class="py-5 py-md-6 bg-body-tertiary">
    <div class="container">
        <h2 class="fw-black text-center mb-5" data-aos="fade-up">Pertanyaan Sering Muncul</h2>

        <div class="accordion accordion-flush bg-body rounded-4 shadow-sm" id="faqAccordion" data-aos="fade-up"
             data-aos-delay="100">
            <div class="accordion-item rounded-top-4 border-0 border-bottom">
                <h2 class="accordion-header">
                    <button class="accordion-button fw-bold py-4 collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#faq1">
                        Kalau toko tutup sebulan, saldo saya hilang nggak?
                    </button>
                </h2>
                <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-secondary">
                        Tentu tidak! Tidak ada sistem *reset* atau langganan hangus. Uang yang kamu top-up menjadi
                        kredit transaksi abadi.
                    </div>
                </div>
            </div>
            <div class="accordion-item border-0 border-bottom">
                <h2 class="accordion-header">
                    <button class="accordion-button fw-bold py-4 collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#faq2">
                        Gimana kalau pesanan pelanggan dibatalkan?
                    </button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-secondary">
                        Sistem kami sangat adil. Saldo Rp 300 hanya terpotong saat status pesanan berubah menjadi
                        "Selesai/Dibayar". Jika batal, tidak ada potongan apapun.
                    </div>
                </div>
            </div>
            <div class="accordion-item rounded-bottom-4 border-0">
                <h2 class="accordion-header">
                    <button class="accordion-button fw-bold py-4 collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#faq3">
                        Apakah aplikasinya harus di-download di Playstore?
                    </button>
                </h2>
                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-secondary">
                        Pakaiapp adalah aplikasi berbasis *Web SaaS (PWA)*. Cukup buka lewat *browser* di HP, Tablet,
                        atau Laptop manapun, lalu "Add to Homescreen". Lebih ringan tanpa menuhin memori!
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- SECTION PILIHAN TOP-UP SALDO -->
<section id="pricing" class="py-5 py-md-6 bg-body border-top">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-2 fw-bold border border-warning border-opacity-25 mb-3">
                <i class="bi bi-tag-fill me-1"></i> Skema Pembelian
            </span>
            <h2 class="fw-black text-body mb-2">Pilihan Top-Up Saldo</h2>
            <p class="text-secondary mx-auto" style="max-width: 600px;">Mulai transaksi dengan mudah. Top-up saldo wallet Pakaiapp tanpa khawatir masa hangus.</p>
        </div>

        <div class="row g-4 justify-content-center">
            <!-- Paket Starter -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card h-100 border-0 shadow-sm p-4 rounded-4 text-center glass-card position-relative overflow-hidden"
                     data-tilt data-tilt-glare data-tilt-max-glare="0.1" data-tilt-scale="1.03">
                    <div class="card-body d-flex flex-column justify-content-between p-0">
                        <div>
                            <div class="text-primary mb-3 display-6"><i class="bi bi-patch-check"></i></div>
                            <h4 class="fw-bold mb-2">Paket Starter</h4>
                            <h2 class="fw-black text-body mb-3">Rp 15.000</h2>
                            <hr class="my-3 opacity-25">
                            <ul class="list-unstyled text-secondary small text-start mb-4">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> <strong>50</strong> Transaksi Sukses</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Rp 300 / Transaksi</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Saldo Abadi Tanpa Hangus</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Fitur POS Lengkap</li>
                            </ul>
                        </div>
                        <button onclick="triggerPayment('Paket Starter', 15000)" class="btn btn-outline-warning rounded-pill py-2 fw-bold text-dark w-100 mt-auto">
                            Beli Sekarang
                        </button>
                    </div>
                </div>
            </div>

            <!-- Paket Rame (Featured) -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card h-100 shadow-lg p-4 rounded-4 text-center glass-card position-relative overflow-hidden"
                     style="border: 2px solid var(--bs-warning) !important;"
                     data-tilt data-tilt-glare data-tilt-max-glare="0.1" data-tilt-scale="1.03">
                    <span class="position-absolute top-0 end-0 bg-warning text-dark px-3 py-1 fw-bold small rounded-bottom" style="border-bottom-left-radius: 12px; font-size: 11px;">TERPOPULER</span>
                    <div class="card-body d-flex flex-column justify-content-between p-0">
                        <div>
                            <div class="text-warning mb-3 display-6"><i class="bi bi-rocket-takeoff-fill"></i></div>
                            <h4 class="fw-bold mb-2">Paket Rame</h4>
                            <h2 class="fw-black text-body mb-3">Rp 50.000</h2>
                            <hr class="my-3 opacity-25">
                            <ul class="list-unstyled text-secondary small text-start mb-4">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> <strong>166</strong> Transaksi Sukses</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Rp 300 / Transaksi</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Saldo Abadi Tanpa Hangus</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Dukungan Premium WA</li>
                            </ul>
                        </div>
                        <button onclick="triggerPayment('Paket Rame', 50000)" class="btn btn-warning rounded-pill py-2 fw-bold text-dark w-100 mt-auto shadow-sm">
                            Beli Sekarang
                        </button>
                    </div>
                </div>
            </div>

            <!-- Paket Enterprise -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="card h-100 border-0 shadow-sm p-4 rounded-4 text-center glass-card position-relative overflow-hidden"
                     data-tilt data-tilt-glare data-tilt-max-glare="0.1" data-tilt-scale="1.03">
                    <div class="card-body d-flex flex-column justify-content-between p-0">
                        <div>
                            <div class="text-success mb-3 display-6"><i class="bi bi-building-fill-check"></i></div>
                            <h4 class="fw-bold mb-2">Paket Enterprise</h4>
                            <h2 class="fw-black text-body mb-3">Rp 100.000</h2>
                            <hr class="my-3 opacity-25">
                            <ul class="list-unstyled text-secondary small text-start mb-4">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> <strong>333</strong> Transaksi Sukses</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Rp 300 / Transaksi</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Saldo Abadi Tanpa Hangus</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Skalabilitas Multi-Outlet</li>
                            </ul>
                        </div>
                        <button onclick="triggerPayment('Paket Enterprise', 100000)" class="btn btn-outline-warning rounded-pill py-2 fw-bold text-dark w-100 mt-auto">
                            Beli Sekarang
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-body py-5 py-md-6 border-top">
    <div class="container text-center">
        <div class="mb-4">
            <a class="fw-black fs-4 text-body text-decoration-none" href="#">
                <i class="bi bi-cup-hot-fill text-warning me-2"></i>pakaiapp<span class="text-secondary">.online</span>
            </a>
        </div>
        
        <!-- Informasi Kontak Bisnis -->
        <div class="d-flex flex-wrap justify-content-center gap-3 mb-4 text-secondary small">
            <a href="mailto:support@ngopikode.com" class="text-secondary text-decoration-none hover-primary">
                <i class="bi bi-envelope-fill text-warning me-1"></i> support@ngopikode.com
            </a>
            <span class="text-muted d-none d-md-inline">|</span>
            <a href="https://wa.me/6285172441544" target="_blank" class="text-secondary text-decoration-none hover-primary">
                <i class="bi bi-whatsapp text-success me-1"></i> WhatsApp: 085172441544
            </a>
            <span class="text-muted d-none d-md-inline">|</span>
            <span class="text-secondary">
                <i class="bi bi-building text-warning me-1"></i> PT Sinergi Kode Kreatif
            </span>
        </div>

        <!-- Tautan Legalitas Kepatuhan Layanan -->
        <div class="d-flex flex-wrap justify-content-center gap-4 mb-4 small">
            <a href="#" class="text-decoration-none text-secondary hover-primary fw-semibold" data-bs-toggle="modal" data-bs-target="#tncModal">
                Syarat & Ketentuan
            </a>
            <a href="#" class="text-decoration-none text-secondary hover-primary fw-semibold" data-bs-toggle="modal" data-bs-target="#refundModal">
                Kebijakan Pengembalian Dana
            </a>
        </div>

        <p class="text-secondary small mb-0">&copy; {{ date('Y') }} pakaiapp.online. Solusi Digital dari <span
                class="fw-bold text-primary">
                <a href="https://www.ngopikode.com" target="_blank"
                   class="text-decoration-none text-primary">ngopikode.</a>
            </span>.</p>
    </div>
</footer>

<!-- Floating WhatsApp Only -->
<a href="https://wa.me/6285172441544" target="_blank" class="floating-wa shadow-lg" title="Tanya Admin">
    <i class="bi bi-whatsapp"></i>
</a>

<!-- ==============================================
     MODALS UNTUK KEPATUHAN LAYANAN
=============================================== -->
<!-- Modal Syarat & Ketentuan -->
<div class="modal fade" id="tncModal" tabindex="-1" aria-labelledby="tncModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header border-0 bg-body-tertiary px-4 py-3 d-flex align-items-center justify-content-between">
                <h5 class="modal-title fw-black text-body mb-0" id="tncModalLabel" style="font-size: 1.15rem;">
                    <i class="bi bi-shield-check text-warning me-2"></i>Syarat & Ketentuan Layanan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 py-4 text-secondary small">
                <p class="mb-4">Selamat datang di <strong>pakaiapp.online</strong>. Harap membaca Syarat & Ketentuan ini dengan saksama sebelum mendaftar dan menggunakan platform kami. Dengan mengakses atau menggunakan layanan pakaiapp.online, Anda menyatakan bahwa Anda telah membaca, memahami, dan menyetujui untuk terikat oleh seluruh ketentuan di bawah ini.</p>
                
                <div class="pe-1">
                    <!-- Pasal 1 -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-primary mb-2">1. KETENTUAN UMUM & DEFINISI</h6>
                        <ul class="list-unstyled ps-0 mb-0 d-flex flex-column gap-2 text-muted">
                            <li class="d-flex align-items-start">
                                <span class="me-2 text-warning">•</span>
                                <span><strong>pakaiapp.online</strong> adalah platform Software-as-a-Service (SaaS) aplikasi kasir pintar (Point of Sales) berbasis cloud yang dikembangkan oleh PT Sinergi Kode Kreatif.</span>
                            </li>
                            <li class="d-flex align-items-start">
                                <span class="me-2 text-warning">•</span>
                                <span><strong>Pengguna</strong> adalah pemilik usaha (merchant), beserta staf/admin yang ditunjuk, yang mendaftarkan diri dan menggunakan layanan pakaiapp.online untuk operasional bisnis mereka.</span>
                            </li>
                            <li class="d-flex align-items-start">
                                <span class="me-2 text-warning">•</span>
                                <span><strong>Layanan</strong> mencakup penyediaan sistem kasir, manajemen varian menu/produk, sistem pemesanan digital (QR self-order), serta fitur pelaporan yang tersedia di dalam platform.</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Pasal 2 -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-primary mb-2">2. PENDAFTARAN AKUN DAN KEAMANAN</h6>
                        <ul class="list-unstyled ps-0 mb-0 d-flex flex-column gap-2 text-muted">
                            <li class="d-flex align-items-start">
                                <span class="me-2 text-warning">•</span>
                                <span>Pengguna wajib memberikan data informasi bisnis yang akurat, benar, dan terbaru pada saat proses pendaftaran layanan.</span>
                            </li>
                            <li class="d-flex align-items-start">
                                <span class="me-2 text-warning">•</span>
                                <span>Pengguna bertanggung jawab penuh atas keamanan kredensial akun (username dan password) masing-masing serta segala bentuk aktivitas transaksi yang terjadi di bawah akun tersebut.</span>
                            </li>
                            <li class="d-flex align-items-start">
                                <span class="me-2 text-warning">•</span>
                                <span>Pakaiapp tidak bertanggung jawab atas kerugian yang timbul akibat kelalaian Pengguna dalam menjaga kerahasiaan akun mereka.</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Pasal 3 -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-primary mb-2">3. MEKANISME PENGGUNAAN & SALDO KREDIT (WALLET)</h6>
                        <ul class="list-unstyled ps-0 mb-0 d-flex flex-column gap-2 text-muted">
                            <li class="d-flex align-items-start">
                                <span class="me-2 text-warning">•</span>
                                <span>pakaiapp.online menggunakan skema prabayar (prepaid) melalui sistem pengisian ulang saldo digital (Top-Up Wallet).</span>
                            </li>
                            <li class="d-flex align-items-start">
                                <span class="me-2 text-warning">•</span>
                                <span>Pendaftaran akun dan penggunaan dasar aplikasi tidak dikenakan biaya langganan bulanan (Gratis Biaya Bulanan).</span>
                            </li>
                            <li class="d-flex align-items-start">
                                <span class="me-2 text-warning">•</span>
                                <span>Saldo wallet Pengguna akan terpotong secara otomatis sebesar Rp 300 (Tiga Ratus Rupiah) untuk setiap transaksi penjualan yang berstatus sukses/selesai pada sistem kasir mereka. Jika transaksi dibatalkan, tidak akan ada pemotongan saldo.</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Pasal 4 -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-primary mb-2">4. KEBIJAKAN PENGEMBALIAN DANA (REFUND POLICY)</h6>
                        <ul class="list-unstyled ps-0 mb-0 d-flex flex-column gap-2 text-muted">
                            <li class="d-flex align-items-start">
                                <span class="me-2 text-warning">•</span>
                                <span>Seluruh transaksi pengisian ulang saldo (Top-Up) yang telah berhasil diverifikasi oleh sistem bersifat final.</span>
                            </li>
                            <li class="d-flex align-items-start">
                                <span class="me-2 text-warning">•</span>
                                <span>Dana/saldo yang sudah masuk ke dalam sistem wallet pakaiapp.online tidak dapat dikembalikan, diuangkan kembali (non-refundable), atau ditransfer ke rekening bank pribadi maupun akun pengguna lain.</span>
                            </li>
                            <li class="d-flex align-items-start">
                                <span class="me-2 text-warning">•</span>
                                <span>Saldo wallet pakaiapp.online bersifat abadi dan tidak memiliki masa kedaluwarsa (tidak ada masa hangus). Saldo akan tetap utuh dan dapat digunakan kapan saja, meskipun toko atau operasional Pengguna tidak aktif dalam jangka waktu yang lama.</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Pasal 5 -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-primary mb-2">5. MATA UANG & PERUBAHAN HARGA</h6>
                        <ul class="list-unstyled ps-0 mb-0 d-flex flex-column gap-2 text-muted">
                            <li class="d-flex align-items-start">
                                <span class="me-2 text-warning">•</span>
                                <span>Seluruh bentuk transaksi pengisian saldo (top-up) dan pemotongan biaya layanan wajib menggunakan nilai mata uang Rupiah (IDR).</span>
                            </li>
                            <li class="d-flex align-items-start">
                                <span class="me-2 text-warning">•</span>
                                <span>pakaiapp.online berhak untuk melakukan penyesuaian nominal minimum top-up atau perubahan harga layanan di masa mendatang dengan pemberitahuan terlebih dahulu kepada Pengguna melalui platform resmi kami.</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Pasal 6 -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-primary mb-2">6. BATASAN TANGGUNG JAWAB (DISCLAIMER)</h6>
                        <ul class="list-unstyled ps-0 mb-0 d-flex flex-column gap-2 text-muted">
                            <li class="d-flex align-items-start">
                                <span class="me-2 text-warning">•</span>
                                <span>pakaiapp.online senantiasa berupaya menjaga performa platform agar dapat diakses 24 jam. Namun, kami tidak bertanggung jawab atas kerugian materiel atau inmateriel yang disebabkan oleh gangguan teknis, pemeliharaan sistem (maintenance), kegagalan jaringan internet Pengguna, atau gangguan eksternal pada sistem pihak ketiga (seperti gangguan dari kemitraan bank atau payment gateway).</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Pasal 7 -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-primary mb-2">7. KEBIJAKAN PRIVASI DATA</h6>
                        <ul class="list-unstyled ps-0 mb-0 d-flex flex-column gap-2 text-muted">
                            <li class="d-flex align-items-start">
                                <span class="me-2 text-warning">•</span>
                                <span>pakaiapp.online berkomitmen penuh untuk melindungi data pribadi Pengguna serta data transaksi toko Anda. Informasi yang Anda kumpulkan hanya digunakan untuk kepentingan optimalisasi layanan akun Anda dan tidak akan pernah disalahgunakan, disebarluaskan, atau dijual kepada pihak ketiga mana pun tanpa persetujuan Anda.</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Pasal 8 -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-primary mb-2">8. HUKUM YANG BERLAKU</h6>
                        <ul class="list-unstyled ps-0 mb-0 d-flex flex-column gap-2 text-muted">
                            <li class="d-flex align-items-start">
                                <span class="me-2 text-warning">•</span>
                                <span>Syarat & Ketentuan ini diatur, ditafsirkan, dan tunduk sepenuhnya pada hukum dan peraturan perundang-undangan yang berlaku di negara Republik Indonesia.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-body-tertiary px-4 py-3">
                <button type="button" class="btn btn-warning rounded-pill fw-bold text-dark px-4 shadow-sm" data-bs-dismiss="modal">Saya Setuju</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Kebijakan Pengembalian Dana -->
<div class="modal fade" id="refundModal" tabindex="-1" aria-labelledby="refundModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header border-0 bg-body-tertiary px-4 py-3 d-flex align-items-center justify-content-between">
                <h5 class="modal-title fw-black text-body mb-0" id="refundModalLabel" style="font-size: 1.15rem;">
                    <i class="bi bi-wallet2 text-warning me-2"></i>Kebijakan Pengembalian Dana (Refund Policy)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 py-4 text-secondary small">
                <p class="mb-4">Sebagai bagian dari kepatuhan operasional kami di <strong>pakaiapp.online</strong>, berikut adalah kebijakan resmi terkait pengembalian dana (refund) untuk layanan top-up saldo wallet digital Anda:</p>
                
                <div class="pe-1">
                    <div class="mb-4">
                        <h6 class="fw-bold text-primary mb-2">1. FINALITAS TRANSAKSI</h6>
                        <p class="text-muted">Seluruh transaksi pengisian ulang saldo (Top-Up) yang telah berhasil diverifikasi oleh sistem bersifat final dan mengikat. Pengguna diharapkan melakukan konfirmasi nominal sebelum menyelesaikan transaksi pembayaran.</p>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold text-primary mb-2">2. SIFAT SALDO (NON-REFUNDABLE)</h6>
                        <p class="text-muted">Dana atau saldo yang sudah masuk ke dalam sistem wallet pakaiapp.online tidak dapat dikembalikan, diuangkan kembali (non-refundable), atau ditransfer ke rekening bank pribadi maupun akun pengguna lain.</p>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold text-primary mb-2">3. SALDO ABADI (TANPA MASA KADALUWARSA)</h6>
                        <p class="text-muted">Saldo wallet pakaiapp.online bersifat abadi dan tidak memiliki masa kedaluwarsa (tidak ada masa hangus). Saldo akan tetap utuh dan dapat digunakan kapan saja untuk memotong biaya Rp 300 per transaksi sukses, meskipun toko atau operasional Pengguna tidak aktif dalam jangka waktu yang lama.</p>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold text-primary mb-2">4. HUBUNGI KAMI</h6>
                        <p class="text-muted mb-0">Jika Anda mengalami kendala teknis dalam proses top-up (seperti saldo tidak bertambah setelah transfer berhasil), silakan hubungi tim dukungan kami melalui WhatsApp di <a href="https://wa.me/6285172441544" target="_blank" class="fw-bold text-decoration-none text-primary">085172441544</a> atau email ke <a href="mailto:support@ngopikode.com" class="fw-bold text-decoration-none text-primary">support@ngopikode.com</a> dengan melampirkan bukti transfer.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-body-tertiary px-4 py-3">
                <button type="button" class="btn btn-warning rounded-pill fw-bold text-dark px-4 shadow-sm" data-bs-dismiss="modal">Saya Mengerti</button>
            </div>
        </div>
    </div>
</div>

<!-- ==============================================
     EXTERNAL LIBRARIES & OP SCRIPTS
=============================================== -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="https://unpkg.com/typed.js@2.0.16/dist/typed.umd.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.8.0/vanilla-tilt.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // 1. Initialize Animasi Scroll (AOS)
    AOS.init({once: true, offset: 50});

    // 2. Initialize Typed.js (Efek Ngetik Keren)
    new Typed('#typed-text', {
        strings: ['Bayar Pas Ada Transaksi.', 'Tanpa Biaya Bulanan.', 'Solusi Adil Untuk UMKM.', 'Bikin Operasional Rapi.'],
        typeSpeed: 50,
        backSpeed: 30,
        loop: true,
        backDelay: 2000
    });

    // 3. Dark Mode Toggle Script
    const themeBtn = document.getElementById('themeToggle');
    const htmlRoot = document.getElementById('html-root');
    const icon = themeBtn.querySelector('i');

    themeBtn.addEventListener('click', () => {
        if (htmlRoot.getAttribute('data-bs-theme') === 'light') {
            htmlRoot.setAttribute('data-bs-theme', 'dark');
            icon.classList.replace('bi-moon-stars-fill', 'bi-sun-fill');
            icon.classList.add('text-warning');
        } else {
            htmlRoot.setAttribute('data-bs-theme', 'light');
            icon.classList.replace('bi-sun-fill', 'bi-moon-stars-fill');
            icon.classList.remove('text-warning');
        }
    });

    // 4. Kalkulator Interaktif Logic
    const slider = document.getElementById('trxSlider');
    const trxDisplay = document.getElementById('trxDisplay');
    const costPakaiapp = document.getElementById('costPakaiapp');

    slider.addEventListener('input', function () {
        const trx = parseInt(this.value);
        trxDisplay.innerText = trx;

        // Rp 300 / trx
        const cost = trx * 300;

        if (trx === 0) {
            costPakaiapp.innerText = 'GRATIS!';
            costPakaiapp.classList.replace('text-success', 'text-warning');
        } else {
            costPakaiapp.innerText = 'Rp ' + cost.toLocaleString('id-ID');
            costPakaiapp.classList.replace('text-warning', 'text-success');
        }
    });

    // 5. Chat Bot UI & Logic (Lebih "Hidup")
    let userData = {jenisBisnis: '', jumlahCabang: '', namaToko: '', namaOwner: '', noWa: ''};
    const chatContainer = document.getElementById('chat-container');

    function showTypingIndicator() {
        const id = 'typing-' + Date.now();
        const html = `
            <div id="${id}" class="chat-bubble-bot mb-3 p-3 rounded-4 bg-body-tertiary border d-inline-block shadow-sm">
                <div class="typing-indicator"><span></span><span></span><span></span></div>
            </div><div class="w-100" id="clear-${id}"></div>`;
        chatContainer.insertAdjacentHTML('beforeend', html);
        scrollToBottom();
        return id;
    }

    function removeTypingIndicator(id) {
        document.getElementById(id).remove();
        document.getElementById('clear-' + id).remove();
    }

    function appendUserBubble(text) {
        const html = `<div class="d-flex justify-content-end mb-4"><div class="chat-bubble-user p-3 rounded-4 bg-primary text-white shadow-sm">${text}</div></div>`;
        chatContainer.insertAdjacentHTML('beforeend', html);
        scrollToBottom();
    }

    function appendBotBubble(text, delay = 800) {
        const typingId = showTypingIndicator();
        setTimeout(() => {
            removeTypingIndicator(typingId);
            const html = `<div class="chat-bubble-bot mb-3 p-3 rounded-4 bg-body-tertiary border d-inline-block shadow-sm" style="max-width: 85%;">${text}</div><div class="w-100"></div>`;
            chatContainer.insertAdjacentHTML('beforeend', html);
            scrollToBottom();
        }, delay);
    }

    function scrollToBottom() {
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }

    // Alur Chat Awal
    setTimeout(() => {
        appendBotBubble('Halo! 👋 Saya asisten virtual dari Pakaiapp.');
        setTimeout(() => {
            appendBotBubble('Capek ya bayar sistem kasir bulanan padahal toko kadang sepi?');
            setTimeout(() => {
                const options = `
                <div class="d-flex gap-2 flex-wrap mb-4" id="step1-options">
                    <button class="btn btn-outline-primary rounded-pill fw-bold chat-btn" onclick="nextStep(1, 'F&B (Cafe/Resto)')">🍔 F&B (Cafe/Resto)</button>
                    <button class="btn btn-outline-primary rounded-pill fw-bold chat-btn" onclick="nextStep(1, 'Retail (Toko/Kelontong)')">🛒 Retail (Toko/Kelontong)</button>
                </div>`;
                chatContainer.insertAdjacentHTML('beforeend', options);
                scrollToBottom();
            }, 1000);
        }, 1200);
    }, 500);

    function nextStep(currentStep, value) {
        if (currentStep === 1) {
            userData.jenisBisnis = value;
            document.getElementById('step1-options').remove();
            appendUserBubble(value);

            appendBotBubble(`Pilihan mantap! Bisnis ${value} emang butuh pencatatan varian yang rapi.`);
            setTimeout(() => {
                appendBotBubble(`Berapa banyak outlet yang kamu kelola sekarang?`);
                const options = `
                <div class="d-flex gap-2 flex-wrap mb-4 mt-2" id="step2-options">
                    <button class="btn btn-outline-primary rounded-pill fw-bold chat-btn" onclick="nextStep(2, 'Baru 1 Outlet')">Baru 1 Outlet</button>
                    <button class="btn btn-outline-primary rounded-pill fw-bold chat-btn" onclick="nextStep(2, '2 - 5 Outlet')">2 - 5 Outlet</button>
                    <button class="btn btn-outline-primary rounded-pill fw-bold chat-btn" onclick="nextStep(2, 'Lebih dari 5')">Lebih dari 5</button>
                </div>`;
                setTimeout(() => {
                    chatContainer.insertAdjacentHTML('beforeend', options);
                    scrollToBottom();
                }, 1200);
            }, 1000);
        } else if (currentStep === 2) {
            userData.jumlahCabang = value;
            document.getElementById('step2-options').remove();
            appendUserBubble(value);

            appendBotBubble(`Siap! Terakhir nih, biar Pakaiapp kamu bisa langsung dibuatin *database*-nya. Isi form mini ini ya! 🚀`);

            setTimeout(() => {
                const formHTML = `
                <div class="card border-1 border-primary bg-body-tertiary rounded-4 p-3 mb-4 mt-2 shadow-sm glass-card" id="register-form">
                    <div class="mb-3">
                        <input type="text" id="inputToko" class="form-control form-control-sm rounded-3 bg-body" placeholder="Nama Toko (ex: Warung 3 Saudara)">
                    </div>
                    <div class="mb-3">
                        <input type="text" id="inputNama" class="form-control form-control-sm rounded-3 bg-body" placeholder="Nama Kamu">
                    </div>
                    <div class="mb-3">
                        <input type="number" id="inputWa" class="form-control form-control-sm rounded-3 bg-body" placeholder="No. WhatsApp (08xxxx)">
                    </div>
                    <button class="btn btn-primary w-100 rounded-pill fw-bold shadow-sm chat-btn" onclick="submitToTelegram()" id="btnSubmit">
                        <i class="bi bi-rocket-takeoff-fill me-2"></i>Kirim & Buat Akun!
                    </button>
                </div>`;
                chatContainer.insertAdjacentHTML('beforeend', formHTML);
                scrollToBottom();
            }, 1200);
        }
    }

    // 6. Submit Data pakai SweetAlert (Lebih Keren dari Alert Biasa!)
    function submitToTelegram() {
        userData.namaToko = document.getElementById('inputToko').value;
        userData.namaOwner = document.getElementById('inputNama').value;
        userData.noWa = document.getElementById('inputWa').value;

        if (!userData.namaToko || !userData.namaOwner || !userData.noWa) {
            Swal.fire({
                icon: 'warning',
                title: 'Eits, ada yang kosong',
                text: 'Isi semua datanya dulu dong biar kami gampang hubunginya!',
                confirmButtonColor: 'var(--bs-primary)'
            });
            return;
        }

        const btn = document.getElementById('btnSubmit');
        btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Menghubungi Server...`;
        btn.disabled = true;

        fetch('/api/send-lead', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(userData)
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    document.getElementById('register-form').remove();
                    appendUserBubble('Data sudah dikirim!');

                    // Sweet Alert Success 🎉
                    Swal.fire({
                        icon: 'success',
                        title: 'Boom! Berhasil 🚀',
                        text: `Data ${userData.namaToko} sudah masuk ke sistem kami.`,
                        confirmButtonColor: 'var(--bs-success)',
                        background: 'var(--bs-body-bg)',
                        color: 'var(--bs-body-color)'
                    });

                    appendBotBubble(`🎉 Asik! Data *${userData.namaToko}* sudah kami terima. Tim Pakaiapp (Ngopikode) akan segera nge-WA kamu untuk serah terima akun. *Stay tuned*!`, 1000);
                } else {
                    throw new Error('Gagal dari server');
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Waduh Error 😭',
                    text: 'Koneksi ke server gagal. Coba hubungi via tombol WhatsApp mengambang ya!',
                    confirmButtonColor: 'var(--bs-danger)'
                });
                btn.innerHTML = `<i class="bi bi-rocket-takeoff-fill me-2"></i>Coba Lagi`;
                btn.disabled = false;
            });
    }

    // 7. Payment Simulation Trigger using SweetAlert2
    function triggerPayment(packageName, price) {
        Swal.fire({
            icon: 'info',
            title: 'Memproses Pembelian...',
            text: `Menyiapkan invoice untuk ${packageName} (Rp ${price.toLocaleString('id-ID')})...`,
            showConfirmButton: false,
            timer: 1200,
            background: 'var(--bs-body-bg)',
            color: 'var(--bs-body-color)'
        }).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Diarahkan ke Payment Gateway terintegrasi...',
                text: 'Sistem pembayaran aman & otomatis terverifikasi.',
                confirmButtonColor: 'var(--bs-warning)',
                confirmButtonText: 'Saya Mengerti',
                background: 'var(--bs-body-bg)',
                color: 'var(--bs-body-color)'
            });
        });
    }
</script>
</body>
</html>
