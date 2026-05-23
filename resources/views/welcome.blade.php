<!DOCTYPE html>
<html lang="id" data-bs-theme="light" id="html-root">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- ==============================================
           SEO ON-PAGE, META TAGS & SCHEMA JSON-LD
      =============================================== -->
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

    <link rel="canonical" href="https://pakaiapp.online/">

    <meta property="og:type" content="website">
    <meta property="og:url" content="https://pakaiapp.online/">
    <meta property="og:title" content="Pakaiapp - Kasir Pintar Bayar Suka-Suka">
    <meta property="og:description"
          content="Kasir sepi = Gratis. Kasir ramai = Tetap murah. Revolusi sistem kasir SaaS untuk F&B dan Retail dengan skema Rp 300/transaksi sukses.">
    <meta property="og:image" content="{{ asset('images/pakaiapp-og-banner.jpg') }}">

    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://pakaiapp.online/">
    <meta property="twitter:title" content="Pakaiapp - Kasir Pintar Bayar Suka-Suka">
    <meta property="twitter:description"
          content="Tinggalkan biaya langganan bulanan. Pindah ke Pakaiapp sekarang dan nikmati fitur kasir enterprise dengan harga UMKM.">
    <meta property="twitter:image" content="{{ asset('images/pakaiapp-og-banner.jpg') }}">

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

    <!-- External Libraries -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        /* CSS Tambahan Khusus Landing Page */
        body {
            font-family: var(--font-sans);
        }

        .font-serif {
            font-family: var(--font-serif);
        }

        .py-huge {
            padding-top: 7rem;
            padding-bottom: 7rem;
        }

        /* Glass/Bento Card styling */
        .bento-card {
            background: var(--bs-card-bg);
            border: 1px solid var(--bs-border-color);
            border-radius: 24px;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            overflow: hidden;
        }

        .bento-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            border-color: var(--brand-caramel);
        }

        [data-bs-theme="dark"] .bento-card:hover {
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        /* Hero Text Clamp */
        .hero-title {
            font-size: clamp(2.5rem, 5vw, 4.5rem);
            line-height: 1.1;
            letter-spacing: -0.03em;
        }

        /* Custom Slider (Anti-Buntung) */
        input[type=range] {
            -webkit-appearance: none;
            width: 100%;
            background: transparent;
        }

        input[type=range]::-webkit-slider-thumb {
            -webkit-appearance: none;
            height: 24px;
            width: 24px;
            border-radius: 50%;
            background: var(--brand-caramel);
            cursor: pointer;
            margin-top: -10px;
            box-shadow: 0 0 10px rgba(182, 115, 50, 0.5);
        }

        input[type=range]::-webkit-slider-runnable-track {
            width: 100%;
            height: 6px;
            cursor: pointer;
            background: var(--bs-border-color);
            border-radius: 10px;
        }

        /* Chat UI Refined */
        .chat-container-wrap {
            height: 420px;
            overflow-y: auto;
            scroll-behavior: smooth;
        }

        .chat-bubble-bot {
            background-color: var(--bs-tertiary-bg);
            color: var(--bs-body-color);
            border-radius: 16px 16px 16px 4px;
            padding: 12px 16px;
            font-size: 0.95rem;
            max-width: 85%;
        }

        .chat-bubble-user {
            background-color: var(--brand-caramel);
            color: #fff;
            border-radius: 16px 16px 4px 16px;
            padding: 12px 16px;
            font-size: 0.95rem;
            max-width: 85%;
        }

        /* =========================================
           FITUR BARU: MARQUEE LOGO (SOCIAL PROOF)
           ========================================= */
        .marquee-container {
            overflow: hidden;
            white-space: nowrap;
            position: relative;
            background: var(--bs-body-bg);
            padding: 2rem 0;
            border-top: 1px solid var(--bs-border-color);
            border-bottom: 1px solid var(--bs-border-color);
        }

        .marquee-content {
            display: inline-block;
            animation: marqueeScroll 30s linear infinite;
        }

        .marquee-item {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            margin: 0 2rem;
            font-family: var(--font-serif);
            font-weight: 800;
            font-size: 1.4rem;
            color: var(--bs-tertiary-color);
            opacity: 0.5;
            transition: all 0.3s ease;
            letter-spacing: -0.02em;
        }

        .marquee-item:hover {
            opacity: 1;
            color: var(--brand-caramel);
            transform: scale(1.05);
        }

        @keyframes marqueeScroll {
            0% {
                transform: translateX(0);
            }
            100% {
                transform: translateX(-50%);
            }
        }

        .marquee-container::before, .marquee-container::after {
            content: '';
            position: absolute;
            top: 0;
            width: 150px;
            height: 100%;
            z-index: 2;
        }

        .marquee-container::before {
            left: 0;
            background: linear-gradient(to right, var(--bs-body-bg) 0%, transparent 100%);
        }

        .marquee-container::after {
            right: 0;
            background: linear-gradient(to left, var(--bs-body-bg) 0%, transparent 100%);
        }

        /* List Keunggulan di Fitur */
        .feature-list li {
            position: relative;
            padding-left: 1.75rem;
            margin-bottom: 0.75rem;
            color: var(--bs-secondary-color);
        }

        .feature-list li::before {
            content: '\F26A'; /* Bootstrap icon bi-check-circle-fill */
            font-family: 'bootstrap-icons';
            position: absolute;
            left: 0;
            top: 2px;
            color: var(--brand-caramel);
            font-size: 1.1rem;
        }

        /* Avatar untuk Testimonial */
        .avatar-img {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--brand-caramel);
        }

        .floating-wa {
            position: fixed;
            bottom: 32px;
            right: 32px;
            width: 60px;
            height: 60px;
            background-color: #25D366;
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            box-shadow: 0 8px 24px rgba(37, 211, 102, 0.4);
            z-index: 999;
            transition: transform 0.3s ease;
        }

        .floating-wa:hover {
            transform: scale(1.1);
            color: #fff;
        }
    </style>
</head>
<body class="bg-body position-relative">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg py-4 position-absolute w-100 z-3" data-aos="fade-down">
    <div class="container d-flex justify-content-between align-items-center">
        <a class="navbar-brand font-serif fw-bold fs-4 text-body d-flex align-items-center gap-2" href="#">
            <i class="bi bi-cup-hot-fill" style="color: var(--brand-caramel);"></i>
            pakaiapp<span style="color: var(--bs-tertiary-color);">.online</span>
        </a>
        <button id="themeToggle" class="btn btn-outline-secondary rounded-circle border-0 bg-tertiary-bg"
                style="width: 45px; height: 45px;" aria-label="Toggle Dark Mode">
            <i class="bi bi-moon-stars-fill"></i>
        </button>
    </div>
</nav>

<!-- HERO SECTION -->
<section class="py-huge overflow-hidden position-relative">
    <div class="container position-relative z-1 mt-5">
        <div class="row align-items-center g-5">
            <!-- Text Kiri -->
            <div class="col-lg-6" data-aos="fade-right">
                <div class="d-inline-block px-3 py-2 rounded-pill mb-4"
                     style="background: rgba(var(--bs-warning-rgb), 0.1); border: 1px solid rgba(var(--bs-warning-rgb), 0.2);">
                    <span class="fw-bold small" style="color: var(--brand-caramel);"><i class="bi bi-stars me-1"></i> Resolusi Cerdas F&B & Retail</span>
                </div>
                <h1 class="hero-title font-serif fw-bolder text-body mb-4">
                    Kasir Cepat, <br>
                    <span id="typed-text" style="color: var(--brand-caramel);"></span>
                </h1>
                <p class="fs-5 text-secondary mb-5" style="line-height: 1.7; max-width: 500px;">
                    Ubah cara operasional toko Anda. <strong>Pakaiapp</strong> membebaskan UMKM dari jebakan biaya
                    langganan bulanan. Sistem awan (Cloud) cerdas, bayar cuma kalau ada transaksi sukses.
                </p>
                <div class="d-flex gap-3">
                    <a href="#simulasi" class="btn btn-primary btn-lg rounded-pill px-4 py-3 fw-bold shadow-sm">
                        <i class="bi bi-calculator me-2"></i>Hitung Untungmu
                    </a>
                    <a href="#pricing"
                       class="btn btn-outline-secondary btn-lg rounded-pill px-4 py-3 fw-bold bg-secondary-bg">
                        Lihat Paket
                    </a>
                </div>
            </div>

            <!-- Chatbot Kanan -->
            <div class="col-lg-6" data-aos="fade-left" data-aos-delay="200">
                <div class="bento-card bg-secondary-bg shadow-lg">
                    <div
                        class="p-3 border-bottom border-color d-flex align-items-center justify-content-between bg-body">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 45px; height: 45px; background: rgba(var(--bs-primary-rgb), 0.1);">
                                <i class="bi bi-robot fs-5 text-primary"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 font-serif fw-bold text-body">Pakaiapp Assistant</h6>
                                <small style="color: var(--bs-tertiary-color); font-size: 12px;">Aktif membalas</small>
                            </div>
                        </div>
                        <span class="badge rounded-pill" style="background: rgba(37, 211, 102, 0.1); color: #25D366;">Online</span>
                    </div>
                    <div class="p-4 chat-container-wrap" id="chat-container">
                        <!-- Chat JS goes here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- MARQUEE LOGO (SOCIAL PROOF) -->
<section class="marquee-container" data-aos="fade-in">
    <div class="marquee-content">
        <!-- Set 1 -->
        <span class="marquee-item"><i class="bi bi-shop"></i> Kopi Senja</span>
        <span class="marquee-item"><i class="bi bi-basket2"></i> Minimarket Kita</span>
        <span class="marquee-item"><i class="bi bi-cup-straw"></i> Boba Time</span>
        <span class="marquee-item"><i class="bi bi-egg-fried"></i> Warteg Modern</span>
        <span class="marquee-item"><i class="bi bi-bag-heart"></i> Butik Hijab</span>
        <span class="marquee-item"><i class="bi bi-shop-window"></i> Angkringan Joss</span>
        <!-- Set 2 -->
        <span class="marquee-item"><i class="bi bi-shop"></i> Kopi Senja</span>
        <span class="marquee-item"><i class="bi bi-basket2"></i> Minimarket Kita</span>
        <span class="marquee-item"><i class="bi bi-cup-straw"></i> Boba Time</span>
        <span class="marquee-item"><i class="bi bi-egg-fried"></i> Warteg Modern</span>
        <span class="marquee-item"><i class="bi bi-bag-heart"></i> Butik Hijab</span>
        <span class="marquee-item"><i class="bi bi-shop-window"></i> Angkringan Joss</span>
    </div>
</section>

<!-- SIMULASI SECTION -->
<section id="simulasi" class="py-huge bg-tertiary-bg border-bottom border-color">
    <div class="container text-center">
        <h2 class="font-serif fw-bolder text-body mb-3" data-aos="zoom-in">Simulasi Anti-Buntung</h2>
        <p class="text-secondary mb-5 fs-5 mx-auto" style="max-width: 600px;" data-aos="zoom-in" data-aos-delay="100">
            Geser slider di bawah. Buktikan sendiri seberapa hemat pakai sistem bayar per transaksi sukses.
        </p>

        <div class="bento-card p-4 p-md-5 mx-auto bg-body" style="max-width: 800px;" data-aos="fade-up">
            <h5 class="fw-bold mb-4 text-body font-serif">Hari ini toko kamu dapat berapa pesanan?</h5>

            <div class="px-3 mb-4">
                <input type="range" id="trxSlider" min="0" max="100" value="15" step="1">
                <div class="d-flex justify-content-between text-tertiary-color small fw-bold mt-3">
                    <span>0 (Sepi)</span>
                    <span>100+ (Rame Parah)</span>
                </div>
            </div>

            <div class="text-center my-5">
                <h1 class="display-1 fw-bolder font-serif" id="trxDisplay" style="color: var(--brand-caramel);">15</h1>
                <p class="text-secondary fw-bold text-uppercase tracking-wider">Transaksi Sukses</p>
            </div>

            <hr class="border-color my-4">

            <div class="row g-4">
                <div class="col-md-6 border-end border-color">
                    <p class="mb-2 text-secondary">Biaya Pakaiapp Hari Ini</p>
                    <h2 class="fw-bolder text-body font-serif mb-2" id="costPakaiapp">Rp 4.500</h2>
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2"><i
                            class="bi bi-check-circle-fill me-1"></i> Adil sesuai omzet</span>
                </div>
                <div class="col-md-6">
                    <p class="mb-2 text-secondary">Biaya App Langganan</p>
                    <h2 class="fw-bolder text-body font-serif mb-2">Rp 6.600 <span class="fs-6 text-tertiary-color">/hari</span>
                    </h2>
                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2"><i
                            class="bi bi-x-circle-fill me-1"></i> Sepi tetap bayar full</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==================================================
     FEATURES SECTION 2.0 (ASIMETRIS BENTO GRID)
=================================================== -->
<section class="py-huge bg-body">
    <div class="container">
        <div class="text-center mb-5 pb-3" data-aos="fade-up">
            <span class="badge rounded-pill px-3 py-2 mb-3 fw-bold border"
                  style="background: rgba(var(--bs-primary-rgb), 0.1); color: var(--bs-primary); border-color: rgba(var(--bs-primary-rgb), 0.2) !important;">
                <i class="bi bi-rocket-takeoff-fill me-1"></i> Ekosistem Lengkap
            </span>
            <h2 class="font-serif fw-bolder text-body mb-3">Bukan Sekadar Mesin Kasir</h2>
            <p class="text-secondary fs-5 mx-auto" style="max-width: 700px;">Dari meja kasir hingga pantauan <i>back-office</i>.
                Semua alat yang Anda butuhkan untuk membesarkan bisnis, berada di dalam satu <i>super-app</i>.</p>
        </div>

        <div class="row g-4">
            <!-- Blok 1: Kasir Cepat -->
            <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
                <div class="bento-card p-4 p-md-5 h-100 bg-secondary-bg">
                    <div class="mb-4 d-inline-flex p-3 rounded-4" style="background: rgba(var(--bs-primary-rgb), 0.1);">
                        <i class="bi bi-basket2-fill fs-2 text-primary"></i>
                    </div>
                    <h3 class="font-serif fw-bold text-body mb-3">Kasir Offline Super Cepat</h3>
                    <p class="text-secondary mb-4">Urai antrean dalam hitungan detik. Dirancang sangat responsif dengan
                        perlindungan anti-kecurangan.</p>
                    <ul class="list-unstyled feature-list mb-0">
                        <li><strong>Akses Kasir Terpisah:</strong> Karyawan fokus melayani tanpa bisa mengutak-atik
                            laporan keuntungan.
                        </li>
                        <li><strong>Catatan Batal Transaksi:</strong> Kasir wajib input alasan jika membatalkan struk,
                            menekan angka kecurangan.
                        </li>
                        <li><strong>E-Receipt WhatsApp:</strong> Hemat kertas struk, kirim invoice digital detail
                            langsung ke WA pembeli.
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Blok 2: Manajemen Fleksibel -->
            <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
                <div class="bento-card p-4 p-md-5 h-100 bg-secondary-bg">
                    <div class="mb-4 d-inline-flex p-3 rounded-4" style="background: rgba(var(--bs-warning-rgb), 0.1);">
                        <i class="bi bi-boxes fs-2" style="color: var(--brand-caramel);"></i>
                    </div>
                    <h3 class="font-serif fw-bold text-body mb-3">Fleksibel untuk Retail & F&B</h3>
                    <p class="text-secondary mb-4">Jual apa saja, atur variasi sesuka hati tanpa batas tabel
                        <i>database</i> yang bikin pusing.</p>
                    <ul class="list-unstyled feature-list mb-0">
                        <li><strong>Varian Retail (SKU):</strong> Atur harga dan stok berbeda untuk varian Ukuran (S, M,
                            L) dan Warna (Merah, Hitam).
                        </li>
                        <li><strong>Add-ons F&B:</strong> Tambahkan aturan <i>Topping</i> ekstra atau Pilihan Level
                            Pedas (Single/Multi-choice).
                        </li>
                        <li><strong>Kategori Rapi:</strong> Pengelompokan produk otomatis agar mudah dicari di kasir dan
                            etalase digital.
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Blok 3: Toko Digital -->
            <div class="col-lg-6" data-aos="fade-up" data-aos-delay="300">
                <div class="bento-card p-4 p-md-5 h-100 bg-secondary-bg">
                    <div class="mb-4 d-inline-flex p-3 rounded-4" style="background: rgba(37, 211, 102, 0.1);">
                        <i class="bi bi-globe-americas fs-2 text-success"></i>
                    </div>
                    <h3 class="font-serif fw-bold text-body mb-3">Toko Digital & Multi-Payment</h3>
                    <p class="text-secondary mb-4">Toko Anda otomatis siap *Go-Online* dengan etalase digital dan sistem
                        pembayaran terpusat.</p>
                    <ul class="list-unstyled feature-list mb-0">
                        <li><strong>Katalog Publik & QR Order:</strong> Pelanggan bisa scan QR di meja, pesan dari HP,
                            pesanan masuk ke layar Anda.
                        </li>
                        <li><strong>Terima Segala Pembayaran:</strong> Otomatis terintegrasi *Payment Gateway* (QRIS,
                            OVO, Dana, ShopeePay, Transfer Bank).
                        </li>
                        <li><strong>Sistem Dompet (Wallet):</strong> Dana transaksi *online* mengendap aman di dompet
                            toko dan siap ditarik (<i>withdraw</i>) ke rekening pribadi.
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Blok 4: Back-office -->
            <div class="col-lg-6" data-aos="fade-up" data-aos-delay="400">
                <div class="bento-card p-4 p-md-5 h-100 bg-secondary-bg">
                    <div class="mb-4 d-inline-flex p-3 rounded-4" style="background: rgba(var(--bs-primary-rgb), 0.1);">
                        <i class="bi bi-graph-up-arrow fs-2 text-primary"></i>
                    </div>
                    <h3 class="font-serif fw-bold text-body mb-3">Kontrol Penuh <i>Back-Office</i></h3>
                    <p class="text-secondary mb-4">Pantau kesehatan bisnis dari mana saja. <i>Dashboard</i> super admin
                        eksklusif hanya untuk Anda (Owner).</p>
                    <ul class="list-unstyled feature-list mb-0">
                        <li><strong>Grafik Real-time (Livewire):</strong> Pantau total pesanan dan omzet hari ini tanpa
                            perlu muat ulang halaman.
                        </li>
                        <li><strong>Atur Pajak & Service:</strong> Pemilik bisa menyesuaikan persentase Pajak (PB1) dan
                            Layanan secara mandiri.
                        </li>
                        <li><strong>Manajemen Pegawai & Kuota:</strong> Tambahkan akun kasir, atur <i>shift</i>, atau
                            <i>upgrade</i> slot produk kapan pun Anda butuh ekspansi.
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- TESTIMONIAL (SOCIAL PROOF) -->
<section class="py-huge bg-secondary-bg border-top border-color">
    <div class="container">
        <div class="text-center mb-5 pb-3" data-aos="fade-up">
            <span class="badge rounded-pill px-3 py-2 mb-3 fw-bold border"
                  style="background: rgba(var(--bs-warning-rgb), 0.1); color: var(--brand-caramel); border-color: rgba(var(--bs-warning-rgb), 0.3) !important;">
                <i class="bi bi-chat-quote-fill me-1"></i> #PakaiappAndalan
            </span>
            <h2 class="font-serif fw-bolder text-body mb-3">Bagaimana Pendapat Mereka?</h2>
            <p class="text-secondary fs-5 mx-auto" style="max-width: 600px;">Pemilik bisnis mulai beralih ke Pakaiapp
                karena kebebasan yang diberikan. Ini kata mereka.</p>
        </div>

        <div class="row g-4">
            <!-- Testi 1 -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="bento-card p-4 h-100 bg-body d-flex flex-column">
                    <div class="d-flex align-items-center mb-4">
                        <img src="https://ui-avatars.com/api/?name=Budi+Santoso&background=321E14&color=fff"
                             alt="Budi Santoso" loading="lazy" class="avatar-img me-3 shadow-sm">
                        <div>
                            <h6 class="font-serif fw-bold text-body mb-0">Budi Santoso</h6>
                            <small class="text-secondary">Owner Kedai Kopi</small>
                        </div>
                    </div>
                    <p class="text-secondary fst-italic mb-0 mt-auto">"Dulu pusing tiap akhir bulan harus bayar
                        langganan POS padahal omzet lagi turun. Sejak pakai Pakaiapp, pengeluaran jadi adil banget.
                        Sistem QR Order-nya juga sangat ngebantu saat jam rame!"</p>
                </div>
            </div>
            <!-- Testi 2 -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="bento-card p-4 h-100 bg-body d-flex flex-column"
                     style="border: 1px solid rgba(var(--bs-warning-rgb), 0.3);">
                    <div class="d-flex align-items-center mb-4">
                        <img src="https://ui-avatars.com/api/?name=Siti+Aisyah&background=B67332&color=fff"
                             alt="Siti Aisyah" loading="lazy" class="avatar-img me-3 shadow-sm">
                        <div>
                            <h6 class="font-serif fw-bold text-body mb-0">Siti Aisyah</h6>
                            <small class="text-secondary">Toko Retail Pakaian</small>
                        </div>
                    </div>
                    <p class="text-secondary fst-italic mb-0 mt-auto">"Sistem pencatatan ukuran dan warna baju sangat
                        rapi. Nggak ada lagi menu yang dobel-dobel. Paling seneng sama fitur top-up saldo yang uangnya
                        nggak bakal hangus."</p>
                </div>
            </div>
            <!-- Testi 3 -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="bento-card p-4 h-100 bg-body d-flex flex-column">
                    <div class="d-flex align-items-center mb-4">
                        <img src="https://ui-avatars.com/api/?name=Reza+Fahlevi&background=846A58&color=fff"
                             alt="Reza Fahlevi" loading="lazy" class="avatar-img me-3 shadow-sm">
                        <div>
                            <h6 class="font-serif fw-bold text-body mb-0">Reza Fahlevi</h6>
                            <small class="text-secondary">Pemilik Warung Makan</small>
                        </div>
                    </div>
                    <p class="text-secondary fst-italic mb-0 mt-auto">"Tinggal buka dari browser HP kasir, aplikasinya
                        ringan banget nggak perlu install dari Playstore yang menuhin memori. Sangat terjangkau buat
                        kelas warung menengah."</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- PRICING SECTION -->
<section id="pricing" class="py-huge bg-tertiary-bg border-top border-color">
    <div class="container">
        <div class="text-center mb-5 pb-3" data-aos="fade-up">
            <h2 class="font-serif fw-bolder text-body mb-3">Pilihan Saldo Digital</h2>
            <p class="text-secondary fs-5 mx-auto" style="max-width: 600px;">Mulai dengan investasi super minim. <br>Lebih
                murah dari harga dua cangkir kopi susu.</p>
        </div>

        <div class="row g-4 justify-content-center">
            <!-- Paket Starter -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="bento-card p-5 h-100 bg-body d-flex flex-column text-center">
                    <h4 class="font-serif fw-bold text-body mb-2">Starter</h4>
                    <h1 class="font-serif fw-bolder mb-4" style="color: var(--brand-caramel);">Rp 15k</h1>
                    <ul class="list-unstyled text-secondary text-start mb-5 mx-auto" style="max-width: 200px;">
                        <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i> <strong>50</strong>
                            Transaksi
                        </li>
                        <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i> Rp 300 / trx</li>
                        <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i> Saldo Abadi</li>
                    </ul>
                    <button class="btn btn-outline-primary w-100 rounded-pill py-3 fw-bold mt-auto btn-pilih-paket"
                            data-paket="Starter" data-harga="15000">Pilih Paket
                    </button>
                </div>
            </div>

            <!-- Paket Rame (Featured) -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="bento-card p-5 h-100 d-flex flex-column text-center position-relative"
                     style="background: var(--bs-primary); border-color: var(--bs-primary);">
                    <span
                        class="position-absolute top-0 start-50 translate-middle-x py-1 px-3 rounded-bottom-3 fw-bold small text-white"
                        style="background: var(--brand-caramel);">TERPOPULER</span>
                    <h4 class="font-serif fw-bold text-white mt-3 mb-2">Rame</h4>
                    <h1 class="font-serif fw-bolder text-white mb-4">Rp 50k</h1>
                    <ul class="list-unstyled text-white text-opacity-75 text-start mb-5 mx-auto"
                        style="max-width: 200px;">
                        <li class="mb-3"><i class="bi bi-check-circle-fill text-warning me-2"></i> <strong
                                class="text-white">166</strong> Transaksi
                        </li>
                        <li class="mb-3"><i class="bi bi-check-circle-fill text-warning me-2"></i> Rp 300 / trx</li>
                        <li class="mb-3"><i class="bi bi-check-circle-fill text-warning me-2"></i> Saldo Abadi</li>
                        <li class="mb-3"><i class="bi bi-check-circle-fill text-warning me-2"></i> Prioritas WA</li>
                    </ul>
                    <button class="btn w-100 rounded-pill py-3 fw-bold mt-auto btn-pilih-paket"
                            style="background: var(--brand-caramel); color: #fff; border: none;" data-paket="Rame"
                            data-harga="50000">Pilih Paket
                    </button>
                </div>
            </div>

            <!-- Paket Enterprise -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="bento-card p-5 h-100 bg-body d-flex flex-column text-center">
                    <h4 class="font-serif fw-bold text-body mb-2">Enterprise</h4>
                    <h1 class="font-serif fw-bolder text-body mb-4">Rp 100k</h1>
                    <ul class="list-unstyled text-secondary text-start mb-5 mx-auto" style="max-width: 200px;">
                        <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i> <strong>333</strong>
                            Transaksi
                        </li>
                        <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i> Rp 300 / trx</li>
                        <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i> Saldo Abadi</li>
                        <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i> Multi-Outlet</li>
                    </ul>
                    <button class="btn btn-outline-primary w-100 rounded-pill py-3 fw-bold mt-auto btn-pilih-paket"
                            data-paket="Enterprise" data-harga="100000">Pilih Paket
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="py-5 bg-body border-top border-color">
    <div class="container text-center">
        <a class="font-serif fw-bolder fs-4 text-body text-decoration-none mb-4 d-inline-block" href="#">
            <i class="bi bi-cup-hot-fill" style="color: var(--brand-caramel);"></i> pakaiapp<span
                style="color: var(--bs-tertiary-color);">.online</span>
        </a>

        <div class="d-flex flex-wrap justify-content-center gap-4 mb-4 text-secondary small">
            <a href="mailto:support@ngopikode.com" class="text-secondary text-decoration-none"><i
                    class="bi bi-envelope-fill me-1"></i> support@ngopikode.com</a>
            <a href="https://wa.me/6285172441544" target="_blank" class="text-secondary text-decoration-none"><i
                    class="bi bi-whatsapp me-1"></i> 085172441544</a>
            <span><i class="bi bi-building me-1"></i> PT Sinergi Kode Kreatif</span>
        </div>

        <div class="d-flex flex-wrap justify-content-center gap-4 mb-4 small fw-semibold">
            <a href="#" class="text-decoration-none" style="color: var(--brand-caramel);" data-bs-toggle="modal"
               data-bs-target="#tncModal">Syarat & Ketentuan</a>
            <a href="#" class="text-decoration-none" style="color: var(--brand-caramel);" data-bs-toggle="modal"
               data-bs-target="#refundModal">Kebijakan Pengembalian Dana</a>
        </div>

        <p class="text-secondary small mb-0">&copy; {{ date('Y') }} pakaiapp.online. Solusi Digital dari <a
                href="https://www.ngopikode.com" target="_blank" class="fw-bold"
                style="color: var(--brand-caramel); text-decoration: none;">ngopikode.</a></p>
    </div>
</footer>

<!-- Floating WhatsApp -->
<a href="https://wa.me/6285172441544" target="_blank" class="floating-wa">
    <i class="bi bi-whatsapp"></i>
</a>

<!-- ==============================================
     MODAL CHECKOUT (API)
=============================================== -->
<div class="modal fade" id="checkoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bento-card border-0">
            <div class="modal-header border-bottom border-color bg-tertiary-bg py-4 px-4">
                <h5 class="modal-title font-serif fw-bold text-body">
                    <i class="bi bi-cart-check-fill me-2" style="color: var(--brand-caramel);"></i>Selesaikan
                    Pendaftaran
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-body">
                <div class="p-3 rounded-3 mb-4"
                     style="background: rgba(var(--brand-caramel-rgb, 182, 115, 50), 0.1); border: 1px dashed var(--brand-caramel);">
                    <p class="mb-1 text-secondary small">Paket Terpilih:</p>
                    <h5 class="font-serif fw-bold text-body mb-0" id="checkoutPaketName">-</h5>
                </div>
                <form id="formCheckout">
                    <input type="hidden" id="inputPaketName">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Nama Toko / Usaha</label>
                        <input type="text" class="form-control" id="inputCheckoutToko" required
                               placeholder="Contoh: Warung Kopi Senja">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Nama Pemilik</label>
                        <input type="text" class="form-control" id="inputCheckoutNama" required
                               placeholder="Nama lengkap Anda">
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-secondary">No. WhatsApp</label>
                        <input type="number" class="form-control" id="inputCheckoutWa" required
                               placeholder="08xxxxxxxx">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-bold"
                            id="btnSubmitCheckout">
                        Buat Akun & Lanjut Pembayaran <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ==============================================
     MODAL TNC
=============================================== -->
<div class="modal fade" id="tncModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content bento-card border-0">
            <div class="modal-header border-bottom border-color bg-tertiary-bg py-4 px-4">
                <h5 class="modal-title font-serif fw-bold text-body">
                    <i class="bi bi-shield-check me-2" style="color: var(--brand-caramel);"></i>Syarat & Ketentuan
                    Layanan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 p-md-5 bg-body text-secondary small" style="line-height: 1.7;">
                <p class="mb-4">Selamat datang di <strong>pakaiapp.online</strong>. Harap membaca Syarat & Ketentuan ini
                    dengan saksama sebelum mendaftar dan menggunakan platform kami.</p>
                <h6 class="fw-bold text-body font-serif mt-4 mb-2">1. KETENTUAN UMUM & DEFINISI</h6>
                <ul class="mb-4 text-secondary">
                    <li><strong>pakaiapp.online</strong> adalah platform Software-as-a-Service (SaaS) aplikasi kasir
                        pintar (Point of Sales) berbasis cloud.
                    </li>
                    <li><strong>Pengguna</strong> adalah pemilik usaha beserta staf/admin yang ditunjuk.</li>
                    <li><strong>Layanan</strong> mencakup sistem kasir, manajemen varian menu, QR self-order, dan
                        pelaporan.
                    </li>
                </ul>
                <h6 class="fw-bold text-body font-serif mb-2">2. PENDAFTARAN AKUN DAN KEAMANAN</h6>
                <ul class="mb-4 text-secondary">
                    <li>Pengguna wajib memberikan data informasi bisnis yang akurat, benar, dan terbaru.</li>
                    <li>Pengguna bertanggung jawab penuh atas keamanan kredensial akun.</li>
                </ul>
                <h6 class="fw-bold text-body font-serif mb-2">3. MEKANISME PENGGUNAAN & SALDO KREDIT</h6>
                <ul class="mb-4 text-secondary">
                    <li>pakaiapp.online menggunakan skema prabayar melalui sistem pengisian ulang saldo (Top-Up
                        Wallet).
                    </li>
                    <li>Pendaftaran akun dan penggunaan dasar tidak dikenakan biaya langganan bulanan.</li>
                    <li>Saldo akan terpotong Rp 300 untuk setiap transaksi sukses. Jika batal, tidak ada potongan.</li>
                </ul>
                <h6 class="fw-bold text-body font-serif mb-2">4. KEBIJAKAN PENGEMBALIAN DANA</h6>
                <ul class="mb-4 text-secondary">
                    <li>Tunduk pada aturan Refund Policy (Top-up final, non-refundable, saldo abadi).</li>
                </ul>
                <h6 class="fw-bold text-body font-serif mb-2">5. HUKUM YANG BERLAKU</h6>
                <ul class="mb-0 text-secondary">
                    <li>Syarat & Ketentuan ini tunduk sepenuhnya pada hukum negara Republik Indonesia.</li>
                </ul>
            </div>
            <div class="modal-footer border-top border-color bg-tertiary-bg py-3 px-4">
                <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Saya
                    Setuju
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ==============================================
     MODAL REFUND POLICY
=============================================== -->
<div class="modal fade" id="refundModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content bento-card border-0">
            <div class="modal-header border-bottom border-color bg-tertiary-bg py-4 px-4">
                <h5 class="modal-title font-serif fw-bold text-body">
                    <i class="bi bi-wallet2 me-2" style="color: var(--brand-caramel);"></i>Kebijakan Pengembalian Dana
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 p-md-5 bg-body text-secondary small" style="line-height: 1.7;">
                <p class="mb-4">Sebagai bagian dari kepatuhan operasional kami di <strong>pakaiapp.online</strong>,
                    berikut adalah kebijakan resmi terkait pengembalian dana (refund) untuk layanan top-up saldo:</p>
                <h6 class="fw-bold text-body font-serif mt-4 mb-2">1. FINALITAS TRANSAKSI</h6>
                <p class="mb-4">Seluruh transaksi pengisian ulang saldo (Top-Up) yang telah berhasil diverifikasi oleh
                    sistem bersifat final dan mengikat. Pengguna diharapkan melakukan konfirmasi nominal sebelum
                    menyelesaikan pembayaran.</p>
                <h6 class="fw-bold text-body font-serif mb-2">2. SIFAT SALDO (NON-REFUNDABLE)</h6>
                <p class="mb-4">Dana atau saldo yang sudah masuk ke dalam sistem wallet pakaiapp.online tidak dapat
                    dikembalikan, diuangkan kembali (non-refundable), atau ditransfer ke rekening bank pribadi maupun
                    akun pengguna lain.</p>
                <h6 class="fw-bold text-body font-serif mb-2">3. SALDO ABADI (TANPA MASA KADALUWARSA)</h6>
                <p class="mb-4">Saldo wallet pakaiapp.online bersifat abadi dan tidak memiliki masa kedaluwarsa. Saldo
                    akan tetap utuh dan dapat digunakan kapan saja untuk memotong biaya Rp 300 per transaksi sukses,
                    meskipun toko tidak aktif dalam jangka waktu lama.</p>
                <h6 class="fw-bold text-body font-serif mb-2">4. HUBUNGI KAMI</h6>
                <p class="mb-0">Jika Anda mengalami kendala teknis (saldo tidak bertambah setelah transfer), hubungi
                    dukungan kami melalui WhatsApp di <strong style="color: var(--brand-caramel);">085172441544</strong>
                    atau email ke <strong>support@ngopikode.com</strong> dengan melampirkan bukti transfer.</p>
            </div>
            <div class="modal-footer border-top border-color bg-tertiary-bg py-3 px-4">
                <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Saya
                    Mengerti
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="https://unpkg.com/typed.js@2.0.16/dist/typed.umd.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.8.0/vanilla-tilt.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // 1. Initializations
    AOS.init({once: true, offset: 50, duration: 800});

    new Typed('#typed-text', {
        strings: ['Bisnis Melesat.', 'Bayar Pas Ada Transaksi.', 'Bebas Biaya Bulanan.', 'Solusi Adil UMKM.'],
        typeSpeed: 50, backSpeed: 30, loop: true, backDelay: 2000
    });

    // 2. Dark Mode Toggle
    const themeBtn = document.getElementById('themeToggle');
    const htmlRoot = document.getElementById('html-root');
    const icon = themeBtn.querySelector('i');

    themeBtn.addEventListener('click', () => {
        if (htmlRoot.getAttribute('data-bs-theme') === 'light') {
            htmlRoot.setAttribute('data-bs-theme', 'dark');
            icon.classList.replace('bi-moon-stars-fill', 'bi-sun-fill');
            icon.style.color = 'var(--brand-caramel)';
        } else {
            htmlRoot.setAttribute('data-bs-theme', 'light');
            icon.classList.replace('bi-sun-fill', 'bi-moon-stars-fill');
            icon.style.color = '';
        }
    });

    // 3. Kalkulator Simulasi
    const slider = document.getElementById('trxSlider');
    const trxDisplay = document.getElementById('trxDisplay');
    const costPakaiapp = document.getElementById('costPakaiapp');

    slider.addEventListener('input', function () {
        const trx = parseInt(this.value);
        trxDisplay.innerText = trx;
        const cost = trx * 300;

        if (trx === 0) {
            costPakaiapp.innerText = 'GRATIS!';
            costPakaiapp.style.color = 'var(--brand-caramel)';
        } else {
            costPakaiapp.innerText = 'Rp ' + cost.toLocaleString('id-ID');
            costPakaiapp.style.color = '';
        }
    });

    // 4. Premium Chat Bot Logic
    const chatContainer = document.getElementById('chat-container');

    function appendBotBubble(text, delay = 600) {
        setTimeout(() => {
            const html = `<div class="d-flex justify-content-start mb-3"><div class="chat-bubble-bot shadow-sm">${text}</div></div>`;
            chatContainer.insertAdjacentHTML('beforeend', html);
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }, delay);
    }

    setTimeout(() => {
        appendBotBubble('Halo! 👋 Saya asisten virtual dari Pakaiapp.');
        appendBotBubble('Udah capek kan bayar sistem kasir bulanan padahal toko kadang sepi?', 1500);
        appendBotBubble('Dengan Pakaiapp, kamu cuma bayar Rp 300 kalau ada transaksi sukses. Uang saldo nggak akan hangus sampai kiamat! Keren kan? Klik <strong>Hitung Untungmu</strong> di samping! 🚀', 2500);
    }, 1000);

    // 5. Logic Modal API Checkout
    const btnPakets = document.querySelectorAll('.btn-pilih-paket');
    let modalCheckout;

    document.addEventListener('DOMContentLoaded', () => {
        modalCheckout = new bootstrap.Modal(document.getElementById('checkoutModal'));

        btnPakets.forEach(btn => {
            btn.addEventListener('click', function () {
                const namaPaket = this.getAttribute('data-paket');
                const hargaPaket = this.getAttribute('data-harga');

                document.getElementById('checkoutPaketName').innerText = `${namaPaket} (Rp ${parseInt(hargaPaket).toLocaleString('id-ID')})`;
                document.getElementById('inputPaketName').value = namaPaket;

                modalCheckout.show();
            });
        });
    });

    document.getElementById('formCheckout').addEventListener('submit', function (e) {
        e.preventDefault();

        const btnSubmit = document.getElementById('btnSubmitCheckout');
        const originalText = btnSubmit.innerHTML;

        const payload = {
            namaToko: document.getElementById('inputCheckoutToko').value,
            namaOwner: document.getElementById('inputCheckoutNama').value,
            noWa: document.getElementById('inputCheckoutWa').value,
            paket: document.getElementById('inputPaketName').value,
            jenisBisnis: 'Umum',
            jumlahCabang: '1'
        };

        btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
        btnSubmit.disabled = true;

        fetch('/api/send-lead', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(payload)
        })
            .then(response => response.json())
            .then(data => {
                modalCheckout.hide();

                Swal.fire({
                    icon: 'success',
                    title: 'Pendaftaran Berhasil! 🚀',
                    text: `Sistem kami sedang menyiapkan paket ${payload.paket} untuk ${payload.namaToko}. Tim Pakaiapp akan segera menghubungi via WhatsApp.`,
                    confirmButtonColor: 'var(--brand-caramel)',
                    background: 'var(--bs-card-bg)',
                    color: 'var(--bs-body-color)',
                    customClass: {popup: 'bento-card'}
                });

                this.reset();
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Koneksi Terputus',
                    text: 'Gagal menghubungi server. Silakan klik tombol WhatsApp di pojok kanan bawah untuk bantuan manual.',
                    confirmButtonColor: 'var(--bs-danger)',
                    background: 'var(--bs-card-bg)',
                    color: 'var(--bs-body-color)'
                });
            })
            .finally(() => {
                btnSubmit.innerHTML = originalText;
                btnSubmit.disabled = false;
            });
    });
</script>
</body>
</html>
