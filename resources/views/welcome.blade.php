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
    <link rel="icon" type="image/png" href="/logo.png">
    <link rel="apple-touch-icon" href="/logo.png">
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
      "@type": "SoftwareApplication",
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
        "@type": "Organization",
        "name": "PT Sinergi Kode Kreatif (ngopikode)",
        "url": "https://www.ngopikode.com"
      }
    }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Aset Utama Proyek Laravel (Vite SASS & JS) -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        /* CSS Kustom (Melengkapi app.scss Anda untuk feel yang lebih manusiawi & indie) */

        /* Tipografi Ekstrem untuk Hierarki yang Jelas */
        .display-hero {
            font-size: clamp(2.5rem, 5vw, 4.5rem);
            line-height: 1.1;
            letter-spacing: -0.03em;
        }

        .section-title {
            font-size: clamp(2rem, 3vw, 3rem);
            letter-spacing: -0.02em;
            line-height: 1.2;
        }

        /* Spasi Modern (Bukan mt-5/mb-5 standar Bootstrap) */
        .py-huge {
            padding-top: clamp(5rem, 10vw, 8rem);
            padding-bottom: clamp(5rem, 10vw, 8rem);
        }

        /* Desain Card yang lebih "Clean" & Tipis */
        .premium-card {
            background-color: var(--bs-card-bg);
            border: 1px solid var(--bs-border-color);
            border-radius: 24px;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .premium-card:hover {
            border-color: var(--brand-caramel);
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(50, 30, 20, 0.08);
        }

        [data-bs-theme="dark"] .premium-card:hover {
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        /* Aksen Warna Text */
        .text-caramel {
            color: var(--brand-caramel) !important;
        }

        .bg-caramel-soft {
            background-color: rgba(182, 115, 50, 0.1) !important;
        }

        /* Slider Kustom Premium */
        .slider-wrapper {
            background: var(--bs-tertiary-bg);
            border: 1px solid var(--bs-border-color);
            border-radius: 100px;
            padding: 24px 32px;
        }

        input[type=range].custom-slider {
            -webkit-appearance: none;
            width: 100%;
            height: 6px;
            border-radius: 10px;
            background: var(--bs-border-color);
            outline: none;
        }

        input[type=range].custom-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--brand-caramel);
            cursor: pointer;
            border: 4px solid var(--bs-card-bg);
            box-shadow: 0 4px 12px rgba(182, 115, 50, 0.3);
            transition: transform 0.2s;
        }

        input[type=range].custom-slider::-webkit-slider-thumb:hover {
            transform: scale(1.2);
        }

        /* Chatbot Modern (Lebih mirip widget asli, bukan kotak kaku) */
        .chat-ui-container {
            border: 1px solid var(--bs-border-color);
            border-radius: 24px;
            background: var(--bs-card-bg);
            box-shadow: 0 24px 48px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        [data-bs-theme="dark"] .chat-ui-container {
            box-shadow: 0 24px 48px rgba(0, 0, 0, 0.2);
        }

        .chat-bubble-bot {
            background-color: var(--bs-tertiary-bg);
            color: var(--bs-body-color);
            border-radius: 20px 20px 20px 4px;
            padding: 14px 18px;
            font-size: 0.95rem;
            max-width: 85%;
            border: 1px solid var(--bs-border-color);
        }

        .chat-bubble-user {
            background-color: var(--bs-primary);
            color: var(--bs-body-bg); /* Balik warna untuk kontras */
            border-radius: 20px 20px 4px 20px;
            padding: 14px 18px;
            font-size: 0.95rem;
            max-width: 85%;
        }

        .chat-action-btn {
            background: transparent;
            border: 1px solid var(--bs-border-color);
            color: var(--bs-body-color);
            border-radius: 100px;
            padding: 8px 16px;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.2s;
        }

        .chat-action-btn:hover {
            border-color: var(--brand-caramel);
            color: var(--brand-caramel);
            background: rgba(182, 115, 50, 0.05);
        }

        /* Floating Badge */
        .floating-badge {
            display: inline-flex;
            align-items: center;
            padding: 8px 16px;
            border-radius: 100px;
            border: 1px solid var(--bs-border-color);
            background: var(--bs-card-bg);
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--bs-body-color);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        }

        /* Form Modal Kustom */
        .premium-input {
            background-color: var(--bs-tertiary-bg) !important;
            border: 1px solid transparent !important;
            border-radius: 12px !important;
            padding: 14px 18px !important;
            font-size: 1rem !important;
            transition: all 0.3s ease;
        }

        .premium-input:focus {
            background-color: var(--bs-card-bg) !important;
            border-color: var(--brand-caramel) !important;
            box-shadow: 0 0 0 4px rgba(182, 115, 50, 0.1) !important;
        }
    </style>
</head>
<body class="bg-body font-sans text-body">

<nav class="navbar navbar-expand-lg py-4 position-absolute w-100 z-3">
    <div class="container d-flex justify-content-between align-items-center">
        <a class="navbar-brand fw-extrabold fs-4 font-serif text-body d-flex align-items-center gap-2" href="#">
            <div class="bg-caramel-soft text-caramel rounded-circle d-flex align-items-center justify-content-center"
                 style="width: 40px; height: 40px;">
                <i class="bi bi-cup-hot-fill"></i>
            </div>
            <span>pakaiapp<span class="opacity-50 fw-normal">.online</span></span>
        </a>
        <div class="d-flex gap-3 align-items-center">
            <a href="#pricing"
               class="text-decoration-none fw-semibold text-body d-none d-md-block hover-caramel transition">Paket
                Saldo</a>
            <button id="themeToggle"
                    class="btn btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center"
                    style="width: 44px; height: 44px;" title="Ubah Tema">
                <i class="bi bi-moon-stars-fill"></i>
            </button>
        </div>
    </div>
</nav>

<section class="position-relative pt-5 pb-5 pt-md-7 pb-md-6 overflow-hidden">
    <div class="container mt-5 pt-5">
        <div class="row align-items-center gx-xl-5">

            <!-- Kolom Kiri: Copywriting Punchy -->
            <div class="col-lg-6 mb-5 mb-lg-0" data-aos="fade-up">
                <div class="floating-badge mb-4">
                    <span class="text-caramel me-2">✦</span> Revolusi Kasir UMKM
                </div>
                <h1 class="display-hero fw-extrabold font-serif mb-4 text-body">
                    Toko lagi sepi? <br>
                    <span class="text-secondary">Nggak perlu bayar langganan.</span>
                </h1>
                <p class="fs-5 text-secondary mb-5" style="line-height: 1.7; max-width: 500px;">
                    Pakaiapp adalah sistem kasir (POS) cerdas. Kamu hanya ditarik biaya <strong class="text-body">Rp
                        300</strong> ketika ada pesanan yang sukses dibayar. Adil, transparan, dan nggak bikin boncos.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="#simulasi" class="btn btn-primary rounded-pill px-5 py-3 fw-bold fs-6 border-0">
                        Hitung Penghematan
                    </a>
                </div>

                <div class="mt-5 d-flex align-items-center gap-3 text-secondary small fw-medium">
                    <div class="d-flex align-items-center gap-1">
                        <i class="bi bi-check-circle-fill text-caramel"></i> Tanpa Kontrak
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <i class="bi bi-check-circle-fill text-caramel"></i> Saldo Abadi
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <i class="bi bi-check-circle-fill text-caramel"></i> Support WA
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Widget Chatbot Interaktif -->
            <div class="col-lg-6" data-aos="fade-up" data-aos-delay="150">
                <div class="chat-ui-container mx-auto" style="max-width: 480px; height: 500px;">
                    <!-- Header Chat -->
                    <div class="p-3 border-bottom d-flex align-items-center gap-3 bg-body-tertiary">
                        <div class="position-relative">
                            <div
                                class="bg-primary text-body-bg rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 48px; height: 48px;">
                                <i class="bi bi-robot fs-5"></i>
                            </div>
                            <span
                                class="position-absolute bottom-0 end-0 bg-success border border-2 border-white rounded-circle"
                                style="width: 14px; height: 14px;"></span>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold font-serif fs-6 text-body">Pakaiapp Assistant</h6>
                            <span class="small text-secondary d-block" style="font-size: 0.8rem;">Siap membantu setup toko kamu</span>
                        </div>
                    </div>
                    <!-- Body Chat -->
                    <div class="p-4 overflow-auto flex-grow-1" id="chat-container">
                        <!-- Konten diisi oleh JS -->
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<section id="simulasi" class="py-huge bg-body-tertiary border-top border-bottom">
    <div class="container text-center">
        <h2 class="section-title fw-extrabold font-serif mb-3 text-body" data-aos="fade-up">Simulasi Anti-Boncos</h2>
        <p class="fs-5 text-secondary mb-5 mx-auto" style="max-width: 600px;" data-aos="fade-up" data-aos-delay="100">
            Geser *slider* di bawah untuk melihat perbandingan biaya Pakaiapp dengan aplikasi kasir langganan bulanan
            lainnya.
        </p>

        <div class="premium-card p-4 p-md-5 mx-auto text-start" style="max-width: 700px;" data-aos="fade-up"
             data-aos-delay="200">
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <span class="text-secondary fw-semibold small text-uppercase tracking-wider">Perkiraan Omzet</span>
                    <h3 class="fw-bold font-serif mb-0 text-body">Pesanan per hari</h3>
                </div>
                <div class="text-end">
                    <span class="display-5 fw-extrabold text-caramel font-serif" id="trxDisplay">20</span>
                    <span class="text-secondary fw-medium ms-1">trx</span>
                </div>
            </div>

            <div class="slider-wrapper mb-5">
                <input type="range" class="custom-slider" id="trxSlider" min="0" max="100" value="20" step="1">
                <div class="d-flex justify-content-between text-secondary mt-3 small fw-semibold">
                    <span>Sepi (0)</span>
                    <span>Rame Banget (100+)</span>
                </div>
            </div>

            <div class="row g-4 rounded-4 bg-body p-4 border">
                <div class="col-6 border-end">
                    <span class="d-block text-secondary small fw-bold mb-1">Biaya Pakaiapp</span>
                    <h4 class="fw-extrabold text-body font-serif mb-1" id="costPakaiapp">Rp 6.000</h4>
                    <span
                        class="badge bg-success bg-opacity-10 text-success rounded-pill fw-medium border border-success border-opacity-25">Dipotong dari Saldo</span>
                </div>
                <div class="col-6 ps-md-4">
                    <span class="d-block text-secondary small fw-bold mb-1">Kasir Kompetitor</span>
                    <h4 class="fw-extrabold text-danger font-serif mb-1">Rp 6.600<span
                            class="fs-6 fw-normal text-secondary">/hr</span></h4>
                    <span
                        class="badge bg-danger bg-opacity-10 text-danger rounded-pill fw-medium border border-danger border-opacity-25">Wajib bayar bulanan</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-huge bg-body">
    <div class="container">
        <div class="row mb-5 align-items-end" data-aos="fade-up">
            <div class="col-md-8">
                <h2 class="section-title fw-extrabold font-serif mb-3 text-body">Bukan sekadar kasir.</h2>
                <p class="fs-5 text-secondary mb-0">Arsitektur *backend* yang dirancang khusus untuk operasional cepat,
                    rapi, dan transparan.</p>
            </div>
        </div>

        <div class="row g-4">
            <!-- Card 1 -->
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
                <div class="premium-card h-100 p-4 p-lg-5 d-flex flex-column">
                    <div
                        class="bg-caramel-soft text-caramel rounded-3 d-flex align-items-center justify-content-center mb-4"
                        style="width: 56px; height: 56px;">
                        <i class="bi bi-diagram-3-fill fs-4"></i>
                    </div>
                    <h4 class="fw-bold font-serif text-body mb-3">Varian & Topping Dinamis</h4>
                    <p class="text-secondary mb-0 mt-auto line-height-base">Atur relasi harga berdasarkan ukuran atau
                        tambahan topping tanpa perlu menduplikasi menu. Database lebih bersih dan ringan.</p>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="200">
                <div class="premium-card h-100 p-4 p-lg-5 d-flex flex-column">
                    <div
                        class="bg-caramel-soft text-caramel rounded-3 d-flex align-items-center justify-content-center mb-4"
                        style="width: 56px; height: 56px;">
                        <i class="bi bi-qr-code-scan fs-4"></i>
                    </div>
                    <h4 class="fw-bold font-serif text-body mb-3">QR Self-Order Meja</h4>
                    <p class="text-secondary mb-0 mt-auto line-height-base">Ubah meja menjadi titik penjualan. Pelanggan
                        scan QR, pesan sendiri, dan pesanan otomatis masuk ke layar kasir atau dapur.</p>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
                <div class="premium-card h-100 p-4 p-lg-5 d-flex flex-column bg-body-tertiary border-0">
                    <div
                        class="bg-body text-body rounded-3 border d-flex align-items-center justify-content-center mb-4"
                        style="width: 56px; height: 56px;">
                        <i class="bi bi-wallet2 fs-4"></i>
                    </div>
                    <h4 class="fw-bold font-serif text-body mb-3">Saldo Tanpa Masa Hangus</h4>
                    <p class="text-secondary mb-0 mt-auto line-height-base">Uang yang kamu top-up adalah hak milikmu
                        sepenuhnya. Tidak ada sistem kedaluwarsa atau hangus jika toko sedang libur panjang.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="pricing" class="py-huge bg-body-tertiary border-top">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title fw-extrabold font-serif mb-3 text-body">Mulai dari nominal kecil.</h2>
            <p class="fs-5 text-secondary mx-auto" style="max-width: 600px;">Pilih kuota saldo yang sesuai dengan
                kecepatan omzet toko kamu. Bebas *top-up* kapan saja.</p>
        </div>

        <div class="row justify-content-center g-4">
            <!-- Paket 1 -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="premium-card p-4 p-xl-5 h-100 d-flex flex-column bg-body">
                    <h5 class="fw-bold font-serif text-secondary mb-2">Paket Starter</h5>
                    <div class="d-flex align-items-baseline mb-4">
                        <span class="fs-4 fw-bold text-body me-1">Rp</span>
                        <span class="display-5 fw-extrabold text-body font-serif"
                              style="letter-spacing: -0.05em;">15<span class="fs-3">.000</span></span>
                    </div>
                    <ul class="list-unstyled mb-5 d-flex flex-column gap-3 text-secondary">
                        <li class="d-flex align-items-start gap-3"><i class="bi bi-check2 text-caramel fs-5"></i> <span>Mendapat <strong>50</strong> Kuota Transaksi</span>
                        </li>
                        <li class="d-flex align-items-start gap-3"><i class="bi bi-check2 text-caramel fs-5"></i> <span>Saldo Abadi (Tanpa Hangus)</span>
                        </li>
                        <li class="d-flex align-items-start gap-3"><i class="bi bi-check2 text-caramel fs-5"></i> <span>Akses Semua Fitur POS</span>
                        </li>
                    </ul>
                    <button
                        class="btn btn-outline-secondary rounded-pill w-100 py-3 fw-bold mt-auto border-2 hover-caramel"
                        onclick="openCheckoutModal('Paket Starter', 15000, 50)">
                        Pilih Starter
                    </button>
                </div>
            </div>

            <!-- Paket 2 (Highlighted) -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="premium-card p-4 p-xl-5 h-100 d-flex flex-column position-relative"
                     style="border: 2px solid var(--brand-caramel);">
                    <div
                        class="position-absolute top-0 start-50 translate-middle badge rounded-pill bg-caramel-soft text-caramel border border-warning border-opacity-50 px-3 py-2 fw-bold font-serif">
                        Paling Sering Dibeli
                    </div>
                    <h5 class="fw-bold font-serif text-caramel mb-2 mt-2">Paket Rame</h5>
                    <div class="d-flex align-items-baseline mb-4">
                        <span class="fs-4 fw-bold text-body me-1">Rp</span>
                        <span class="display-5 fw-extrabold text-body font-serif"
                              style="letter-spacing: -0.05em;">50<span class="fs-3">.000</span></span>
                    </div>
                    <ul class="list-unstyled mb-5 d-flex flex-column gap-3 text-secondary">
                        <li class="d-flex align-items-start gap-3"><i class="bi bi-check2 text-caramel fs-5"></i> <span>Mendapat <strong>166</strong> Kuota Transaksi</span>
                        </li>
                        <li class="d-flex align-items-start gap-3"><i class="bi bi-check2 text-caramel fs-5"></i> <span>Saldo Abadi (Tanpa Hangus)</span>
                        </li>
                        <li class="d-flex align-items-start gap-3"><i class="bi bi-check2 text-caramel fs-5"></i> <span>Prioritas Bantuan WhatsApp</span>
                        </li>
                    </ul>
                    <button class="btn btn-primary rounded-pill w-100 py-3 fw-bold mt-auto border-0 shadow-sm"
                            style="background-color: var(--brand-caramel); color: #fff;"
                            onclick="openCheckoutModal('Paket Rame', 50000, 166)">
                        Pilih Paket Rame
                    </button>
                </div>
            </div>

            <!-- Paket 3 -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="premium-card p-4 p-xl-5 h-100 d-flex flex-column bg-body">
                    <h5 class="fw-bold font-serif text-secondary mb-2">Paket Enterprise</h5>
                    <div class="d-flex align-items-baseline mb-4">
                        <span class="fs-4 fw-bold text-body me-1">Rp</span>
                        <span class="display-5 fw-extrabold text-body font-serif"
                              style="letter-spacing: -0.05em;">100<span class="fs-3">.000</span></span>
                    </div>
                    <ul class="list-unstyled mb-5 d-flex flex-column gap-3 text-secondary">
                        <li class="d-flex align-items-start gap-3"><i class="bi bi-check2 text-caramel fs-5"></i> <span>Mendapat <strong>333</strong> Kuota Transaksi</span>
                        </li>
                        <li class="d-flex align-items-start gap-3"><i class="bi bi-check2 text-caramel fs-5"></i> <span>Saldo Abadi (Tanpa Hangus)</span>
                        </li>
                        <li class="d-flex align-items-start gap-3"><i class="bi bi-check2 text-caramel fs-5"></i> <span>Skalabilitas Multi-Outlet</span>
                        </li>
                    </ul>
                    <button
                        class="btn btn-outline-secondary rounded-pill w-100 py-3 fw-bold mt-auto border-2 hover-caramel"
                        onclick="openCheckoutModal('Paket Enterprise', 100000, 333)">
                        Pilih Enterprise
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="py-5 bg-body border-top">
    <div class="container text-center">
        <a class="fw-extrabold fs-5 font-serif text-body text-decoration-none d-inline-flex align-items-center gap-2 mb-4"
           href="#">
            <i class="bi bi-cup-hot-fill text-caramel"></i> pakaiapp.online
        </a>

        <div class="d-flex flex-wrap justify-content-center gap-4 mb-4 text-secondary small fw-medium">
            <a href="mailto:support@ngopikode.com" class="text-secondary text-decoration-none"><i
                    class="bi bi-envelope me-1"></i> support@ngopikode.com</a>
            <a href="https://wa.me/6285172441544" target="_blank" class="text-secondary text-decoration-none"><i
                    class="bi bi-whatsapp me-1"></i> 085172441544</a>
            <span><i class="bi bi-building me-1"></i> PT Sinergi Kode Kreatif</span>
        </div>

        <p class="text-secondary small opacity-75 mb-0">&copy; {{ date('Y') }} Hak Cipta Dilindungi. Dibuat dengan
            presisi oleh <a href="https://www.ngopikode.com" target="_blank"
                            class="text-body fw-bold text-decoration-none">ngopikode</a>.</p>
    </div>
</footer>

<div class="modal fade" id="checkoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content premium-card border-0">
            <div class="modal-header border-bottom-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-extrabold font-serif text-body mb-0 fs-4">Lengkapi Data Toko</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Info Paket Terpilih -->
                <div
                    class="bg-caramel-soft border border-warning border-opacity-25 rounded-4 p-3 mb-4 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="d-block text-secondary small fw-bold mb-1">Paket Terpilih</span>
                        <h6 class="fw-bold font-serif text-body mb-0" id="modalPackageName">Paket Starter</h6>
                    </div>
                    <div class="text-end">
                        <span class="d-block fw-bold text-caramel" id="modalPackagePrice">Rp 15.000</span>
                        <span class="small text-secondary" id="modalPackageQuota">50 trx</span>
                    </div>
                </div>

                <form id="formCheckout">
                    <input type="hidden" id="checkoutPackageData" value="">

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold text-secondary ms-1">Nama Usaha / Toko</label>
                            <input type="text" class="form-control premium-input" id="coToko"
                                   placeholder="Contoh: Kopi Sinergi" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-secondary ms-1">Nama Pemilik / Pengelola</label>
                            <input type="text" class="form-control premium-input" id="coNama"
                                   placeholder="Nama lengkap Anda" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-secondary ms-1">Nomor WhatsApp Aktif</label>
                            <input type="number" class="form-control premium-input" id="coWa" placeholder="08xxxxxxxxxx"
                                   required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary ms-1">Jenis Bisnis</label>
                            <select class="form-select premium-input" id="coJenis" required>
                                <option value="" selected disabled>Pilih...</option>
                                <option value="F&B (Cafe/Resto)">F&B (Cafe/Resto)</option>
                                <option value="Retail (Toko)">Retail (Toko)</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary ms-1">Jumlah Outlet</label>
                            <select class="form-select premium-input" id="coCabang" required>
                                <option value="" selected disabled>Pilih...</option>
                                <option value="1 Outlet">1 Outlet</option>
                                <option value="2-5 Outlet">2 - 5 Outlet</option>
                                <option value="> 5 Outlet">Lebih dari 5</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4 pt-2">
                        <button type="submit"
                                class="btn btn-primary rounded-pill w-100 py-3 fw-bold border-0 shadow-sm d-flex align-items-center justify-content-center gap-2"
                                id="btnProsesCheckout" style="background-color: var(--brand-caramel); color: #fff;">
                            Proses Pendaftaran & Pembayaran <i class="bi bi-arrow-right"></i>
                        </button>
                        <p class="text-center text-secondary mt-3 mb-0" style="font-size: 0.75rem;">Dengan menekan
                            tombol, Anda menyetujui Syarat & Ketentuan Pakaiapp.</p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Ensure Bootstrap JS is loaded for Modal -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // 1. Inisialisasi AOS (Animasi Scroll Halus)
    AOS.init({once: true, offset: 30, duration: 800, easing: 'ease-out-cubic'});

    // 2. Logika Toggle Tema (Sesuai dengan SCSS Anda)
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

    // 3. Logika Simulator Range Slider
    const slider = document.getElementById('trxSlider');
    const trxDisplay = document.getElementById('trxDisplay');
    const costPakaiapp = document.getElementById('costPakaiapp');

    slider.addEventListener('input', function () {
        const trx = parseInt(this.value);
        trxDisplay.innerText = trx;
        const cost = trx * 300;

        if (trx === 0) {
            costPakaiapp.innerText = 'GRATIS';
            costPakaiapp.classList.replace('text-body', 'text-success');
        } else {
            costPakaiapp.innerText = 'Rp ' + cost.toLocaleString('id-ID');
            costPakaiapp.classList.replace('text-success', 'text-body');
        }
    });

    // 4. Logika Chatbot Premium (Satu Alur)
    const chatContainer = document.getElementById('chat-container');

    function appendBotMessage(text, delay = 600) {
        return new Promise(resolve => {
            const typingId = 'typing-' + Date.now();
            const typingHTML = `
                <div id="${typingId}" class="d-flex align-items-end gap-2 mb-3">
                    <div class="chat-bubble-bot text-secondary small">Mengetik...</div>
                </div>`;
            chatContainer.insertAdjacentHTML('beforeend', typingHTML);
            chatContainer.scrollTop = chatContainer.scrollHeight;

            setTimeout(() => {
                document.getElementById(typingId).remove();
                const html = `
                    <div class="d-flex align-items-end gap-2 mb-3 fade-in-up">
                        <div class="chat-bubble-bot">${text}</div>
                    </div>`;
                chatContainer.insertAdjacentHTML('beforeend', html);
                chatContainer.scrollTop = chatContainer.scrollHeight;
                resolve();
            }, delay);
        });
    }

    function appendUserAction(text, actionName) {
        const id = 'action-' + Date.now();
        const html = `
            <div id="${id}" class="d-flex justify-content-end mb-3 fade-in-up">
                <button class="chat-action-btn" onclick="executeChatAction('${id}', '${text}', '${actionName}')">${text}</button>
            </div>`;
        chatContainer.insertAdjacentHTML('beforeend', html);
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }

    window.executeChatAction = function (elementId, text, actionName) {
        document.getElementById(elementId).remove();
        const html = `
            <div class="d-flex justify-content-end mb-3">
                <div class="chat-bubble-user">${text}</div>
            </div>`;
        chatContainer.insertAdjacentHTML('beforeend', html);
        chatContainer.scrollTop = chatContainer.scrollHeight;

        if (actionName === 'start') {
            appendBotMessage('Pilihan yang tepat! Daripada bayar bulanan, lebih baik dana dialihkan ke stok barang kan? 😊', 800)
                .then(() => appendBotMessage('Kira-kira, bisnis kamu bergerak di bidang apa?', 600))
                .then(() => {
                    appendUserAction('F&B (Cafe/Resto)', 'fnb');
                    appendUserAction('Retail (Toko Kelontong)', 'retail');
                });
        } else if (actionName === 'fnb' || actionName === 'retail') {
            appendBotMessage('Catat! Database Pakaiapp bisa menyesuaikan varian harga untuk bisnis kamu secara dinamis.', 800)
                .then(() => appendBotMessage('Untuk mendaftar, langsung saja pilih paket di bagian bawah halaman ini ya! 👇', 1000));
        }
    };

    // Inisialisasi percakapan pertama
    setTimeout(() => {
        appendBotMessage('Halo! 👋 Selamat datang di Pakaiapp.')
            .then(() => appendBotMessage('Lagi cari aplikasi kasir yang adil dan nggak mengikat tagihan bulanan?', 800))
            .then(() => appendUserAction('Iya, betul banget!', 'start'));
    }, 1000);

    // 5. Logika Modal Checkout & Fetch API (/api/send-lead)
    let checkoutModalInstance;

    // Pastikan Bootstrap siap sebelum menginisialisasi modal
    document.addEventListener('DOMContentLoaded', function () {
        const modalEl = document.getElementById('checkoutModal');
        if (modalEl) {
            checkoutModalInstance = new bootstrap.Modal(modalEl);
        }
    });

    window.openCheckoutModal = function (packageName, price, quota) {
        document.getElementById('modalPackageName').innerText = packageName;
        document.getElementById('modalPackagePrice').innerText = 'Rp ' + price.toLocaleString('id-ID');
        document.getElementById('modalPackageQuota').innerText = quota + ' trx';
        document.getElementById('checkoutPackageData').value = packageName;

        if (checkoutModalInstance) {
            checkoutModalInstance.show();
        }
    };

    document.getElementById('formCheckout').addEventListener('submit', function (e) {
        e.preventDefault();

        const btn = document.getElementById('btnProsesCheckout');
        const originalBtnText = btn.innerHTML;

        // Data yang dikumpulkan untuk API sesuai request Anda
        const payload = {
            paketPilihan: document.getElementById('checkoutPackageData').value,
            namaToko: document.getElementById('coToko').value,
            namaOwner: document.getElementById('coNama').value,
            noWa: document.getElementById('coWa').value,
            jenisBisnis: document.getElementById('coJenis').value,
            jumlahCabang: document.getElementById('coCabang').value
        };

        // Loading State
        btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Memproses...`;
        btn.disabled = true;

        // Fetch API
        fetch('/api/send-lead', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(payload)
        })
            .then(response => {
                if (!response.ok) throw new Error('Gagal merespons dari server');
                return response.json();
            })
            .then(data => {
                // Tutup Modal
                if (checkoutModalInstance) checkoutModalInstance.hide();

                // Tampilkan SweetAlert Sukses Ala Premium
                Swal.fire({
                    icon: 'success',
                    title: 'Pendaftaran Berhasil!',
                    html: `Terima kasih <b>${payload.namaOwner}</b>.<br>Tim kami akan segera menghubungi nomor WA Anda untuk serah terima akun <b>${payload.namaToko}</b> dan panduan pembayaran.`,
                    confirmButtonColor: 'var(--brand-caramel)',
                    confirmButtonText: 'Selesai',
                    background: 'var(--bs-card-bg)',
                    color: 'var(--bs-body-color)',
                    customClass: {
                        popup: 'premium-card'
                    }
                });

                // Reset Form
                document.getElementById('formCheckout').reset();
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops.. Terjadi Kesalahan',
                    text: 'Koneksi ke server gagal. Pastikan API /api/send-lead tersedia atau hubungi kami via WhatsApp.',
                    confirmButtonColor: 'var(--bs-primary)',
                    background: 'var(--bs-card-bg)',
                    color: 'var(--bs-body-color)'
                });
            })
            .finally(() => {
                // Kembalikan tombol seperti semula
                btn.innerHTML = originalBtnText;
                btn.disabled = false;
            });
    });
</script>

<style>
    /* Utility Tambahan untuk Animasi JS */
    .fade-in-up {
        animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
</body>
</html>
