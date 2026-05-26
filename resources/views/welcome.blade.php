<!DOCTYPE html>
<html lang="id" class="dark" data-bs-theme="dark" id="html-root">
<head>
    <script>
        if (localStorage.getItem("theme") === "light") {
            document.documentElement.classList.remove("dark");
            document.documentElement.setAttribute("data-bs-theme", "light");
        }
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Pakaiapp - Kasir Web UMKM Tanpa Biaya Langganan</title>
    <link rel="icon" type="image/png" href="/logo.png">
    <link rel="apple-touch-icon" href="/logo.png">
    <meta name="title" content="Pakaiapp - Aplikasi Kasir Web UMKM Tanpa Biaya Bulanan">
    <meta name="description" content="Tinggalkan biaya langganan! Pakaiapp adalah Super App SaaS (POS) berbasis web cloud untuk UMKM. Cuma bayar Rp 300 per transaksi sukses, dan otomatis GRATIS setelah tagihan menyentuh Rp 150rb/bulan!">
    <meta name="keywords" content="aplikasi kasir web, kasir pintar, POS F&B, kasir UMKM, aplikasi kasir tanpa langganan, kasir cafe, sistem kasir retail, pakaiapp, ngopikode, aplikasi kasir medan">
    <meta name="author" content="PT Sinergi Kode Kreatif">
    <meta name="robots" content="index, follow">
    <meta name="language" content="Indonesian">
    <link rel="canonical" href="https://pakaiapp.online/">

    <meta property="og:type" content="website">
    <meta property="og:url" content="https://pakaiapp.online/">
    <meta property="og:title" content="Pakaiapp - Kasir Web Bayar Suka-Suka">
    <meta property="og:description" content="Kasir sepi = Gratis. Kasir ramai = Otomatis Premium (Gratis Tanpa Batas) setelah Rp150.000/bulan tercapai!">
    <meta property="og:image" content="{{ asset('images/pakaiapp-og-banner.jpg') }}">
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://pakaiapp.online/">
    <meta property="twitter:title" content="Pakaiapp - Kasir Web Bayar Suka-Suka">
    <meta property="twitter:description" content="Tinggalkan biaya langganan bulanan. Pindah ke Pakaiapp sekarang dan nikmati fitur kasir enterprise dengan harga UMKM.">
    <meta property="twitter:image" content="{{ asset('images/pakaiapp-og-banner.jpg') }}">

    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@type": "SoftwareApplication",
        "name": "Pakaiapp.online",
        "operatingSystem": "Web, Android, iOS (PWA)",
        "applicationCategory": "BusinessApplication",
        "description": "Sistem kasir pintar (POS) berbasis web cloud untuk UMKM F&B dan Retail tanpa biaya langganan bulanan.",
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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,400;0,14..32,500;0,14..32,600;0,14..32,700;0,14..32,800;1,14..32,400&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
</head>
<body>

<!-- ============================================
     HEADER
============================================ -->
<header class="site-header">
    <div class="header-inner">
        <!-- Logo -->
        <a href="/" class="logo-text">pakaiapp<span class="logo-dot">.online</span></a>

        <!-- Nav -->
        <nav class="header-nav">
            <a href="#cara-daftar" class="nav-link">Cara Daftar</a>
            <a href="#fitur"       class="nav-link">Fitur</a>
            <a href="#harga"       class="nav-link">Harga</a>
            <a href="#faq"         class="nav-link">FAQ</a>
        </nav>

        <!-- Actions -->
        <div class="header-actions">
            <button id="theme-toggle" class="btn-theme-toggle" aria-label="Toggle Theme">
                <i class="bi bi-brightness-high" id="theme-icon"></i>
            </button>
            <a href="/login" class="btn-login text-decoration-none">
                <i class="bi bi-box-arrow-in-right"></i>
                <span class="d-none d-sm-inline">Masuk</span>
            </a>
            <a href="/register" class="btn-register text-decoration-none d-inline-flex align-items-center justify-content-center">
                Daftar Gratis
            </a>
        </div>
    </div>
</header>

<!-- ============================================
     HERO
============================================ -->
<section class="hero" data-aos="fade-up">
    <div class="hero-label">
        <span class="live-dot"></span>
        Kasir UMKM · Tanpa Langganan Bulanan
    </div>

    <h1 class="hero-headline">
        Kasir Web Profesional<br>
        <span class="highlight">Bayar Pas Ada Transaksi</span>
    </h1>

    <p class="hero-desc">
        Kelola penjualan, menu, laporan, dan pembayaran QRIS dari satu dashboard berbasis web — bisa dibuka dari HP, tablet, atau laptop. Cuma bayar <strong style="color:var(--text)">Rp 300</strong> per transaksi sukses, otomatis <strong style="color:var(--green)">GRATIS sepenuhnya</strong> setelah Rp 150.000/bulan.
    </p>

    <div class="hero-cta-group">
        <a href="/register" class="btn-hero-primary text-decoration-none d-inline-flex align-items-center justify-content-center" id="cta-hero-register">
            <i class="bi bi-shop me-2"></i>
            Buat Toko Sekarang — Gratis
        </a>
        <a href="#cara-daftar" class="btn-hero-secondary text-decoration-none d-inline-flex align-items-center justify-content-center">
            <i class="bi bi-play-circle me-2"></i>
            Lihat Cara Kerja
        </a>
    </div>

    <div class="hero-stats">
        <div class="hero-stat-item">
            <div class="hero-stat-number">15<span>K+</span></div>
            <div class="hero-stat-label">Toko Aktif</div>
        </div>
        <div class="hero-stat-item">
            <div class="hero-stat-number">1<span>.2M+</span></div>
            <div class="hero-stat-label">Transaksi Sukses</div>
        </div>
        <div class="hero-stat-item">
            <div class="hero-stat-number">Rp<span>0</span></div>
            <div class="hero-stat-label">Biaya Daftar</div>
        </div>
        <div class="hero-stat-item">
            <div class="hero-stat-number">2<span> Menit</span></div>
            <div class="hero-stat-label">Siap Jualan</div>
        </div>
    </div>
</section>

<hr class="section-divider">

<!-- ============================================
     CARA DAFTAR (How it Works)
============================================ -->
<div id="cara-daftar" class="how-section">
    <div class="how-inner">
        <div data-aos="fade-up">
            <div class="section-label"><i class="bi bi-lightning-charge-fill"></i> Cara Kerja</div>
            <h2 class="section-title">Jualan dalam <span class="accent">3 Langkah Mudah</span></h2>
            <p class="section-sub">Tidak perlu install aplikasi, tidak perlu keahlian teknis. Buka browser, daftar, dan langsung terima pesanan.</p>
        </div>

        <div class="how-grid" data-aos="fade-up" data-aos-delay="100">
            <div class="how-step">
                <div class="step-number">1</div>
                <p class="step-title">Daftar & Buat Toko</p>
                <p class="step-desc">Klik "Daftar Gratis", isi nama toko, email, dan password. Verifikasi email via kode OTP yang dikirim otomatis. Selesai — akun toko Anda langsung aktif.</p>
                <span class="step-time"><i class="bi bi-clock"></i> ~2 menit</span>
            </div>
            <div class="how-step">
                <div class="step-number">2</div>
                <p class="step-title">Input Menu & Produk</p>
                <p class="step-desc">Tambahkan produk atau menu Anda lengkap dengan nama, harga, kategori, dan foto. Bisa tambah variasi ukuran, rasa, atau topping juga.</p>
                <span class="step-time"><i class="bi bi-clock"></i> ~10 menit</span>
            </div>
            <div class="how-step">
                <div class="step-number">3</div>
                <p class="step-title">Langsung Terima Order</p>
                <p class="step-desc">Kasir Anda siap! Terima pesanan dari staf, atau biarkan pelanggan scan QR dan pesan sendiri. Dana masuk otomatis ke dompet toko Anda.</p>
                <span class="step-time"><i class="bi bi-check-circle-fill"></i> Siap Jualan</span>
            </div>
        </div>
    </div>
</div>

<!-- ============================================
     FEATURES
============================================ -->
<section class="section" id="fitur">
    <div data-aos="fade-up">
        <div class="section-label"><i class="bi bi-grid-3x3-gap-fill"></i> Fitur Lengkap</div>
        <h2 class="section-title">Semua yang Butuhkan<br><span class="accent">Sudah Ada di Sini</span></h2>
        <p class="section-sub">Dari kasir harian sampai laporan keuangan — satu platform, tanpa biaya tambahan.</p>
    </div>

    <div class="features-grid">

        <div class="feat-card feat-card-accent" data-aos="fade-up" data-aos-delay="0">
            <div class="feat-icon" style="background:rgba(249,115,22,0.15); color:var(--accent);">
                <i class="bi bi-receipt-cutoff"></i>
            </div>
            <div>
                <p class="feat-title">Kasir Web Real-Time</p>
                <p class="feat-desc">Proses transaksi penjualan dari browser HP atau PC. Data sinkron ke cloud secara real-time — semua staf bisa akses bersamaan.</p>
            </div>
            <div class="feat-badge" style="background:var(--accent-bg); border:1px solid var(--accent-border); color:var(--accent-light);">
                <i class="bi bi-stars"></i> Fitur Utama
            </div>
        </div>

        <div class="feat-card" data-aos="fade-up" data-aos-delay="50">
            <div class="feat-icon" style="background:var(--green-bg); color:var(--green);">
                <i class="bi bi-qr-code-scan"></i>
            </div>
            <div>
                <p class="feat-title">QR Self-Order (Meja)</p>
                <p class="feat-desc">Pelanggan scan QR di meja, pilih menu, bayar sendiri. Pesanan masuk otomatis ke layar kasir tanpa perlu staf keliling.</p>
            </div>
        </div>

        <div class="feat-card" data-aos="fade-up" data-aos-delay="100">
            <div class="feat-icon" style="background:rgba(56,189,248,0.12); color:var(--sky);">
                <i class="bi bi-wallet2"></i>
            </div>
            <div>
                <p class="feat-title">Terima QRIS & E-Wallet</p>
                <p class="feat-desc">GoPay, OVO, Dana, ShopeePay, BCA/Mandiri VA — semua terhubung. Dana langsung masuk ke dompet toko Anda.</p>
            </div>
        </div>

        <div class="feat-card" data-aos="fade-up" data-aos-delay="0">
            <div class="feat-icon" style="background:rgba(167,139,250,0.12); color:var(--purple);">
                <i class="bi bi-bar-chart-fill"></i>
            </div>
            <div>
                <p class="feat-title">Laporan & Analitik</p>
                <p class="feat-desc">Pantau omset harian, mingguan, dan bulanan. Lihat produk terlaris, jam ramai, dan performa staf dalam satu dashboard.</p>
            </div>
        </div>

        <div class="feat-card" data-aos="fade-up" data-aos-delay="50">
            <div class="feat-icon" style="background:var(--green-bg); color:var(--green);">
                <i class="bi bi-people-fill"></i>
            </div>
            <div>
                <p class="feat-title">Multi-Staf & Role</p>
                <p class="feat-desc">Tambahkan akun kasir, manajer, atau admin. Atur hak akses masing-masing agar data keuangan tetap aman terkontrol.</p>
            </div>
        </div>

        <div class="feat-card" data-aos="fade-up" data-aos-delay="100">
            <div class="feat-icon" style="background:rgba(249,115,22,0.12); color:var(--accent);">
                <i class="bi bi-box-seam"></i>
            </div>
            <div>
                <p class="feat-title">Manajemen Stok</p>
                <p class="feat-desc">Track stok bahan baku dan produk jadi. Dapat notifikasi otomatis saat stok hampir habis supaya tidak kehabisan di jam sibuk.</p>
            </div>
        </div>

        <div class="feat-card" data-aos="fade-up" data-aos-delay="0">
            <div class="feat-icon" style="background:rgba(56,189,248,0.12); color:var(--sky);">
                <i class="bi bi-printer-fill"></i>
            </div>
            <div>
                <p class="feat-title">Cetak Struk & KDS</p>
                <p class="feat-desc">Koneksikan printer thermal untuk cetak struk pelanggan, dan tampilkan Kitchen Display System (KDS) di dapur untuk atur pesanan.</p>
            </div>
        </div>

        <div class="feat-card" data-aos="fade-up" data-aos-delay="50">
            <div class="feat-icon" style="background:rgba(167,139,250,0.12); color:var(--purple);">
                <i class="bi bi-diagram-3-fill"></i>
            </div>
            <div>
                <p class="feat-title">Varian & Modifier</p>
                <p class="feat-desc">Atur variasi menu seperti ukuran (S/M/L), tingkat kemanisan, tambahan topping — dengan penyesuaian harga otomatis.</p>
            </div>
        </div>

        <div class="feat-card" data-aos="fade-up" data-aos-delay="100">
            <div class="feat-icon" style="background:var(--green-bg); color:var(--green);">
                <i class="bi bi-phone-fill"></i>
            </div>
            <div>
                <p class="feat-title">PWA — Bisa Offline</p>
                <p class="feat-desc">Pasang Pakaiapp ke homescreen HP seperti aplikasi native. Beberapa fitur tetap berjalan walau koneksi internet tidak stabil.</p>
            </div>
        </div>

    </div>
</section>

<hr class="section-divider">

<!-- ============================================
     PRICING
============================================ -->
<section class="section" id="harga">
    <div data-aos="fade-up">
        <div class="section-label"><i class="bi bi-tag-fill"></i> Harga</div>
        <h2 class="section-title">Bayar Hanya <span class="accent">Saat Ada Transaksi</span></h2>
        <p class="section-sub">Tidak ada biaya berlangganan. Tidak ada kontrak. Tidak ada biaya tersembunyi. Serius.</p>
    </div>

    <div class="pricing-layout">
        <!-- Calculator -->
        <div class="calc-card" data-aos="fade-right">
            <div class="calc-header">
                <div class="calc-icon"><i class="bi bi-calculator-fill"></i></div>
                <div>
                    <p class="calc-title">Simulasi Biaya Bulanan</p>
                    <p class="calc-sub">Geser slider untuk hitung estimasi biaya Anda</p>
                </div>
            </div>

            <div class="slider-wrap">
                <div class="slider-labels">
                    <span>Volume Transaksi / Bulan</span>
                    <strong><span id="trxDisplay">0</span> transaksi</strong>
                </div>
                <input type="range" id="trxSlider" min="0" max="2000" step="50" value="0">
            </div>

            <div class="cost-display">
                <p class="cost-label">Total Biaya Pakaiapp Bulan Ini</p>
                <p class="cost-value" id="costPakaiapp">GRATIS!</p>
                <p class="cost-note" id="costNote">Rp 300 × 0 transaksi = Rp 0</p>
                <span id="unlimitedBadge" class="unlimited-badge" style="display:none; margin: 0.5rem auto 0;">
                    🔥 Unlimited — Sisa Bulan Gratis Sepenuhnya!
                </span>
            </div>

            <div style="margin-top:1rem; padding:1rem; background:var(--bg); border:1px solid var(--border); border-radius:var(--radius); font-size:0.8rem; color:var(--text-muted); line-height:1.65;">
                <strong style="color:var(--text); display:block; margin-bottom:0.35rem;">🎉 Cara Hitung:</strong>
                Rp 300 per transaksi sukses. Kalau total tagihan <strong style="color:var(--text)">sudah tembus Rp 150.000</strong> di bulan itu, <strong style="color:var(--green)">semua transaksi berikutnya di bulan itu jadi GRATIS</strong> sampai akhir bulan!
            </div>
        </div>

        <!-- Comparison -->
        <div class="compare-card" data-aos="fade-left">
            <div class="compare-header">
                <span>Perbandingan</span>
                <span class="col-pakaiapp">Pakaiapp</span>
                <span>Kasir Lain</span>
            </div>

            <div class="compare-row">
                <span class="col-feature">Biaya Pendaftaran</span>
                <span class="col-pakaiapp"><i class="bi bi-check-circle-fill check-icon"></i> Gratis</span>
                <span class="col-other"><i class="bi bi-x-circle-fill cross-icon"></i> Bayar</span>
            </div>
            <div class="compare-row">
                <span class="col-feature">Biaya Bulanan</span>
                <span class="col-pakaiapp"><i class="bi bi-check-circle-fill check-icon"></i> Tidak Ada</span>
                <span class="col-other"><i class="bi bi-x-circle-fill cross-icon"></i> Rp 150–500rb</span>
            </div>
            <div class="compare-row">
                <span class="col-feature">Biaya Per Transaksi</span>
                <span class="col-pakaiapp"><i class="bi bi-check-circle-fill check-icon"></i> Rp 300</span>
                <span class="col-other" style="color:var(--text-muted);">Tergantung plan</span>
            </div>
            <div class="compare-row">
                <span class="col-feature">Auto Unlimited (Gratis)</span>
                <span class="col-pakaiapp"><i class="bi bi-check-circle-fill check-icon"></i> Ya, Rp 150rb</span>
                <span class="col-other"><i class="bi bi-x-circle-fill cross-icon"></i> Tidak Ada</span>
            </div>
            <div class="compare-row">
                <span class="col-feature">QRIS & E-Wallet</span>
                <span class="col-pakaiapp"><i class="bi bi-check-circle-fill check-icon"></i> Termasuk</span>
                <span class="col-other"><i class="bi bi-x-circle-fill cross-icon"></i> Biaya Ekstra</span>
            </div>
            <div class="compare-row">
                <span class="col-feature">QR Self-Order</span>
                <span class="col-pakaiapp"><i class="bi bi-check-circle-fill check-icon"></i> Termasuk</span>
                <span class="col-other"><i class="bi bi-x-circle-fill cross-icon"></i> Plan Premium</span>
            </div>
            <div class="compare-row">
                <span class="col-feature">Multi Staf & Role</span>
                <span class="col-pakaiapp"><i class="bi bi-check-circle-fill check-icon"></i> Termasuk</span>
                <span class="col-other"><i class="bi bi-x-circle-fill cross-icon"></i> Biaya Tambahan</span>
            </div>
            <div class="compare-row">
                <span class="col-feature">Kontrak Berlangganan</span>
                <span class="col-pakaiapp"><i class="bi bi-check-circle-fill check-icon"></i> Tidak Ada</span>
                <span class="col-other"><i class="bi bi-x-circle-fill cross-icon"></i> 1–12 bulan</span>
            </div>
        </div>
    </div>
</section>

<hr class="section-divider">

<!-- ============================================
     TESTIMONIALS
============================================ -->
<section class="section">
    <div data-aos="fade-up">
        <div class="section-label"><i class="bi bi-chat-quote-fill"></i> Testimoni</div>
        <h2 class="section-title">Kata Mereka yang<br><span class="accent">Sudah Pakai Pakaiapp</span></h2>
    </div>

    <div class="testi-grid">
        <div class="testi-card" data-aos="fade-up" data-aos-delay="0">
            <div class="testi-stars">
                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
            </div>
            <p class="testi-text">"Sebelumnya saya pakai kasir lain yang Rp 300rb/bulan. Pindah ke Pakaiapp, bulan pertama malah cuma bayar Rp 45.000 karena lagi sepi. Hemat banget buat usaha kecil."</p>
            <div class="testi-author">
                <div class="testi-avatar" style="background:#1A3E25;">AR</div>
                <div>
                    <p class="testi-name">Andi Ramadan</p>
                    <p class="testi-biz">Warung Kopi & Cemilan, Medan</p>
                </div>
            </div>
        </div>

        <div class="testi-card" data-aos="fade-up" data-aos-delay="80">
            <div class="testi-stars">
                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
            </div>
            <p class="testi-text">"Fitur QR self-order di meja sangat membantu. Pelanggan pesan sendiri, staf saya bisa fokus di dapur. Omset naik karena antrian berkurang drastis."</p>
            <div class="testi-author">
                <div class="testi-avatar" style="background:#1A2B3E;">SH</div>
                <div>
                    <p class="testi-name">Siti Hasanah</p>
                    <p class="testi-biz">Resto & Catering, Pekanbaru</p>
                </div>
            </div>
        </div>

        <div class="testi-card" data-aos="fade-up" data-aos-delay="160">
            <div class="testi-stars">
                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
            </div>
            <p class="testi-text">"Dashboard laporannya simple dan langsung ngerti. Saya bisa cek omset dari HP kapan saja, bahkan pas saya lagi di rumah. Support-nya juga cepat respons via WA."</p>
            <div class="testi-author">
                <div class="testi-avatar" style="background:#3D2010;">DP</div>
                <div>
                    <p class="testi-name">Dika Pratama</p>
                    <p class="testi-biz">Toko Retail Sembako, Binjai</p>
                </div>
            </div>
        </div>
    </div>
</section>

<hr class="section-divider">

<!-- ============================================
     FAQ
============================================ -->
<section class="section" id="faq">
    <div class="section" style="padding-top:0; padding-bottom:0; max-width:720px;">
        <div data-aos="fade-up">
            <div class="section-label"><i class="bi bi-question-circle-fill"></i> FAQ</div>
            <h2 class="section-title">Pertanyaan yang <span class="accent">Sering Ditanyakan</span></h2>
        </div>

        <div class="faq-list" data-aos="fade-up" data-aos-delay="80">

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    Apakah benar-benar gratis untuk daftar?
                    <i class="bi bi-chevron-down faq-chevron"></i>
                </button>
                <div class="faq-answer">Ya, pendaftaran 100% gratis dan tidak perlu kartu kredit. Anda hanya akan dikenakan biaya Rp 300 per transaksi sukses yang terjadi. Jika toko sepi atau tidak ada transaksi, tidak ada biaya sama sekali.</div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    Apa itu "Otomatis Gratis Unlimited"?
                    <i class="bi bi-chevron-down faq-chevron"></i>
                </button>
                <div class="faq-answer">Jika total tagihan Pakaiapp Anda dalam satu bulan sudah mencapai Rp 150.000 (setara 500 transaksi), maka semua transaksi berikutnya di bulan tersebut gratis sepenuhnya tanpa batas. Jadi biaya maksimal Pakaiapp dalam sebulan adalah Rp 150.000, berapapun jumlah transaksinya.</div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    Apakah perlu install aplikasi di HP?
                    <i class="bi bi-chevron-down faq-chevron"></i>
                </button>
                <div class="faq-answer">Tidak perlu! Pakaiapp berbasis web dan berjalan langsung di browser HP, tablet, atau PC. Anda bisa menambahkan shortcut ke homescreen HP layaknya aplikasi (PWA) untuk kemudahan akses.</div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    Apakah data toko saya aman?
                    <i class="bi bi-chevron-down faq-chevron"></i>
                </button>
                <div class="faq-answer">Data Anda disimpan di server cloud terenkripsi dan dibackup secara rutin. Setiap akun toko memiliki subdomain dan database terisolasi, sehingga data Anda tidak bercampur dengan toko lain.</div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    Bagaimana cara top-up dan cairkan dana penjualan?
                    <i class="bi bi-chevron-down faq-chevron"></i>
                </button>
                <div class="faq-answer">Dana hasil penjualan dari pelanggan yang bayar via QRIS/E-Wallet langsung masuk ke wallet toko Anda di Pakaiapp. Proses penarikan ke rekening bank dilakukan secara manual oleh tim kami — hubungi support via WhatsApp untuk proses pencairan.</div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    Apakah ada biaya tambahan untuk fitur QRIS atau QR Self-Order?
                    <i class="bi bi-chevron-down faq-chevron"></i>
                </button>
                <div class="faq-answer">Tidak ada! Semua fitur termasuk QRIS, QR Self-Order, multi-staf, laporan, dan manajemen stok sudah termasuk dalam satu biaya flat Rp 300/transaksi. Tidak ada paket berbeda atau fitur yang dikunci di balik paywall.</div>
            </div>

        </div>
    </div>
</section>

<!-- ============================================
     FINAL CTA
============================================ -->
<div class="cta-section">
    <div class="cta-inner" data-aos="fade-up">
        <h2 class="cta-title">
            Siap Mulai Jualan<br>
            <span class="accent">Lebih Efisien Hari Ini?</span>
        </h2>
        <p class="cta-desc">
            Bergabung bersama 15.000+ toko yang sudah pakai Pakaiapp. Daftar gratis, setup dalam 2 menit, langsung bisa terima pesanan.
        </p>
        <a href="/register" class="btn-hero-primary text-decoration-none d-inline-flex align-items-center justify-content-center" style="margin: 0 auto; font-size:1.05rem; padding: 1rem 2.5rem;">
            <i class="bi bi-shop me-2"></i>
            Buat Akun Toko — Gratis
        </a>
        <div class="cta-notes">
            <span><i class="bi bi-check-circle-fill"></i> Tanpa biaya langganan</span>
            <span><i class="bi bi-check-circle-fill"></i> Tanpa kartu kredit</span>
            <span><i class="bi bi-check-circle-fill"></i> Siap dalam 2 menit</span>
        </div>
    </div>
</div>

<!-- ============================================
     FOOTER
============================================ -->
<footer class="site-footer">
    <div class="footer-inner">
        <div class="footer-brand">
            <a href="/" class="footer-logo">pakaiapp<span class="logo-dot">.online</span></a>
            <p class="footer-tagline">Sistem kasir berbasis web untuk UMKM F&B dan Retail — tanpa biaya langganan bulanan.</p>
            <div class="footer-contact-item">
                <i class="bi bi-whatsapp" style="color:var(--green);"></i>
                <a href="https://wa.me/6285172441544" target="_blank">0851-7244-1544</a>
            </div>
            <div class="footer-contact-item">
                <i class="bi bi-envelope-fill" style="color:var(--accent);"></i>
                <a href="mailto:support@pakaiapp.online">support@pakaiapp.online</a>
            </div>
        </div>

        <div>
            <p class="footer-col-title">Produk</p>
            <ul class="footer-links-list">
                <li><a href="#fitur">Fitur Lengkap</a></li>
                <li><a href="#harga">Cara Hitung Biaya</a></li>
                <li><a href="#cara-daftar">Cara Daftar</a></li>
                <li><a href="/login">Masuk ke Dashboard</a></li>
            </ul>
        </div>

        <div>
            <p class="footer-col-title">Dukungan</p>
            <ul class="footer-links-list">
                <li><a href="#faq">FAQ</a></li>
                <li><a href="https://wa.me/6285172441544" target="_blank">WhatsApp Support</a></li>
                <li><a href="mailto:support@pakaiapp.online">Email Support</a></li>
            </ul>
        </div>

        <div>
            <p class="footer-col-title">Legal</p>
            <ul class="footer-links-list">
                <li><a href="#" data-bs-toggle="modal" data-bs-target="#tncModal">Syarat & Ketentuan</a></li>
                <li><a href="#" data-bs-toggle="modal" data-bs-target="#refundModal">Kebijakan Refund</a></li>
            </ul>
        </div>
    </div>

    <div class="footer-bottom">
        <span>&copy; {{ date('Y') }} pakaiapp.online &mdash; Produk dari <a href="https://www.ngopikode.com" target="_blank">ngopikode</a> (PT Sinergi Kode Kreatif)</span>
        <span style="display:flex; align-items:center; gap:0.5rem; font-size:0.72rem;">
            <i class="bi bi-shield-check" style="color:var(--green);"></i> Platform Aman & Terenkripsi
        </span>
    </div>
</footer>

<!-- ============================================
     FLOATING ACTION BUTTON (WA + Tour Guide)
============================================ -->
<div class="fab-container" id="fabContainer">
    <!-- Speed Dial Options -->
    <div class="fab-menu" id="fabMenu">
        <!-- Option 1: WA Chat -->
        <div class="fab-item" id="fabWaBtn" onclick="window.open('https://wa.me/6285172441544','_blank')">
            <div class="fab-item-tooltip">Hubungi via WhatsApp</div>
            <div class="fab-item-btn fab-wa">
                <i class="bi bi-whatsapp"></i>
            </div>
        </div>
        <!-- Option 2: Interactive Tour -->
        <div class="fab-item" id="fabTourBtn" onclick="startTour()">
            <div class="fab-item-tooltip">Tur Fitur Interaktif</div>
            <div class="fab-item-btn fab-tour">
                <i class="bi bi-map"></i>
            </div>
        </div>
    </div>

    <!-- Main FAB Button -->
    <button class="fab-main" id="fabMain" onclick="toggleFab()" aria-label="Bantuan">
        <i class="bi bi-headset" id="fabIcon"></i>
        <span class="fab-pulse"></span>
    </button>

    <!-- Nudge Bubble -->
    <div class="fab-nudge" id="fabNudge" onclick="toggleFab()">
        <div class="fab-nudge-text">
            <span class="fab-nudge-emoji">💡</span>
            <div>
                <div class="fab-nudge-title">Bingung atau penasaran?</div>
                <div class="fab-nudge-sub">Coba tur fitur gratis kami!</div>
            </div>
        </div>
        <button class="fab-nudge-close" onclick="event.stopPropagation(); dismissNudge()" aria-label="Tutup">
            <i class="bi bi-x"></i>
        </button>
    </div>
</div>

<!-- ============================================
     GUIDED TOUR CHATBOT POPUP
============================================ -->
<div class="tour-overlay" id="tourOverlay" onclick="closeTourOverlay(event)"></div>

<div class="tour-popup" id="tourPopup">
    <div class="tour-popup-header">
        <div class="tour-bot-avatar"><i class="bi bi-stars"></i></div>
        <div>
            <div class="tour-bot-name">Asisten Pakaiapp</div>
            <div class="tour-bot-status"><span class="tour-dot"></span> Online</div>
        </div>
        <button class="tour-close-btn" onclick="closeTour()"><i class="bi bi-x-lg"></i></button>
    </div>

    <div class="tour-messages" id="tourMessages"></div>

    <div class="tour-actions" id="tourActions">
        <button class="tour-btn-next" id="tourNextBtn" onclick="tourNext()">
            <i class="bi bi-chevron-right"></i> Selanjutnya
        </button>
        <button class="tour-btn-skip" onclick="closeTour()">Tutup Tur</button>
    </div>
</div>

<!-- Tour Highlight Tooltip -->
<div class="tour-spotlight-tooltip" id="tourTooltip"></div>

<!-- ============================================
     MODAL TNC
============================================ -->
<div class="modal fade" id="tncModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-bold">
                    <i class="bi bi-shield-check me-2" style="color:var(--accent);"></i>Syarat & Ketentuan Layanan
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body small" style="color:var(--text-muted); line-height:1.7;">
                <p class="mb-4">Selamat datang di <strong style="color:var(--text)">pakaiapp.online</strong>. Harap membaca Syarat & Ketentuan ini dengan saksama sebelum mendaftar dan menggunakan platform kami.</p>
                <h6 class="fw-bold mb-2" style="color:var(--text)">1. KETENTUAN UMUM & DEFINISI</h6>
                <ul class="mb-4">
                    <li><strong style="color:var(--text)">pakaiapp.online</strong> adalah platform Software-as-a-Service (SaaS) aplikasi kasir pintar (Point of Sales) berbasis web cloud yang dikembangkan oleh PT Sinergi Kode Kreatif.</li>
                    <li><strong style="color:var(--text)">Pengguna</strong> adalah pemilik usaha (merchant), beserta staf/admin yang ditunjuk, yang mendaftarkan diri.</li>
                    <li><strong style="color:var(--text)">Layanan</strong> mencakup penyediaan sistem kasir, manajemen varian menu, etalase online (QR self-order), dan pelaporan.</li>
                </ul>
                <h6 class="fw-bold mb-2" style="color:var(--text)">2. PENDAFTARAN AKUN DAN KEAMANAN</h6>
                <ul class="mb-4">
                    <li>Pengguna wajib memberikan data informasi bisnis yang akurat, benar, dan terbaru pada saat proses pendaftaran.</li>
                    <li>Pengguna bertanggung jawab penuh atas keamanan kredensial akun dan hak akses karyawan masing-masing.</li>
                </ul>
                <h6 class="fw-bold mb-2" style="color:var(--text)">3. FUNGSI DOMPET DIGITAL (WALLET) & BIAYA TRANSAKSI</h6>
                <ul class="mb-4">
                    <li>Platform menggunakan sistem Dompet Digital terpusat untuk: (1) menampung saldo Top-Up prabayar untuk pemotongan biaya sistem, dan (2) menampung dana hasil penjualan dari Payment Gateway.</li>
                    <li>Pendaftaran akun dan penggunaan dasar aplikasi tidak dikenakan biaya langganan bulanan.</li>
                    <li>Setiap transaksi penjualan yang berstatus sukses/selesai akan dikenakan biaya sistem sebesar <strong style="color:var(--text)">Rp 300</strong> yang dipotong otomatis dari Saldo Wallet Pengguna.</li>
                </ul>
                <h6 class="fw-bold mb-2" style="color:var(--text)">4. PENARIKAN DANA (WITHDRAWAL) & PENGEMBALIAN DANA</h6>
                <ul class="mb-4">
                    <li>Saldo yang bersumber dari hasil penjualan (Payment Gateway) dapat ditarik oleh Pengguna ke rekening bank yang didaftarkan.</li>
                    <li>Proses penarikan saat ini dilakukan secara manual oleh tim admin pakaiapp.online.</li>
                    <li>Saldo yang bersumber dari Top-Up prabayar bersifat non-refundable (tidak dapat ditarik atau diuangkan kembali).</li>
                </ul>
                <h6 class="fw-bold mb-2" style="color:var(--text)">5. HUKUM YANG BERLAKU</h6>
                <ul class="mb-0">
                    <li>Syarat & Ketentuan ini diatur, ditafsirkan, dan tunduk sepenuhnya pada hukum negara Republik Indonesia.</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-submit-checkout" style="width:auto; padding: 0.6rem 1.5rem; font-size:0.875rem;" data-bs-dismiss="modal">Saya Setuju</button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================
     MODAL REFUND
============================================ -->
<div class="modal fade" id="refundModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-bold">
                    <i class="bi bi-wallet2 me-2" style="color:var(--accent);"></i>Kebijakan Pengembalian Dana
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body small" style="color:var(--text-muted); line-height:1.7;">
                <p class="mb-4">Sebagai bagian dari kepatuhan operasional kami di <strong style="color:var(--text)">pakaiapp.online</strong>, berikut adalah kebijakan resmi terkait pengembalian dana (refund):</p>
                <h6 class="fw-bold mb-2" style="color:var(--text)">1. FINALITAS TRANSAKSI TOP-UP</h6>
                <p class="mb-4">Seluruh transaksi pengisian ulang saldo (Top-Up) yang telah berhasil diverifikasi oleh sistem bersifat final dan mengikat.</p>
                <h6 class="fw-bold mb-2" style="color:var(--text)">2. PEMISAHAN JENIS SALDO & PENARIKAN</h6>
                <p class="mb-4"><strong style="color:var(--text)">Saldo Top-Up prabayar bersifat mutlak non-refundable</strong>. Namun, <strong style="color:var(--text)">Saldo Pendapatan</strong> dari hasil transaksi penjualan online dapat ditarik secara manual ke rekening bank pemilik usaha yang telah diverifikasi.</p>
                <h6 class="fw-bold mb-2" style="color:var(--text)">3. SALDO ABADI</h6>
                <p class="mb-4">Saldo top-up pada wallet pakaiapp.online bersifat abadi dan tidak memiliki masa kedaluwarsa.</p>
                <h6 class="fw-bold mb-2" style="color:var(--text)">4. HUBUNGI KAMI</h6>
                <p class="mb-0">Hubungi kami melalui WhatsApp di <strong style="color:var(--accent)">085172441544</strong> atau email ke <strong style="color:var(--text)">support@pakaiapp.online</strong>.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-submit-checkout" style="width:auto; padding: 0.6rem 1.5rem; font-size:0.875rem;" data-bs-dismiss="modal">Saya Mengerti</button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================
     SCRIPTS
============================================ -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(config('midtrans.is_production'))
    <script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
@else
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
@endif

<script src="{{ asset('js/welcome.js') }}"></script>
</body>
</html>
