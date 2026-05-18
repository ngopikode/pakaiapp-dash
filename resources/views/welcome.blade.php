<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pakaiapp.online - Sistem Kasir Pintar Bayar Suka-Suka</title>
    <!-- Tambahan AOS CSS untuk Animasi Scroll -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body class="bg-body position-relative">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-transparent py-4 position-absolute w-100 z-3" data-aos="fade-down">
    <div class="container">
        <a class="navbar-brand fw-black fs-4 text-body" href="#">
            <i class="bi bi-cup-hot-fill text-warning me-2"></i>pakaiapp<span class="text-secondary">.online</span>
        </a>
        <a href="https://wa.me/628XXXXXXXXXX?text=Halo%20Mas%20Bowo,%20saya%20mau%20daftar%20pakaiapp.online"
           target="_blank" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm btn-hover-grow">
            Hubungi Kami
        </a>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero-section position-relative overflow-hidden">
    {{-- Dekorasi Bulat Animasi --}}
    <div class="blob-shape bg-warning bg-opacity-10 position-absolute rounded-circle"
         style="top: -10%; right: -5%;"></div>

    <div class="container position-relative z-1 mt-5 pt-5">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0 text-center text-lg-start" data-aos="fade-right" data-aos-duration="1000">
                <span
                    class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-2 fw-bold border border-warning border-opacity-25 mb-3 pulse-badge">
                    🚀 Solusi Digital UMKM F&B
                </span>
                <h1 class="display-3 fw-black mb-3 text-body" style="letter-spacing: -1.5px;">
                    Kasir Pintar, <br>
                    <span class="text-gradient">Bayar Cuma Pas Ada Transaksi.</span>
                </h1>
                <p class="lead text-secondary mb-4 opacity-75">
                    Tinggalkan aplikasi kasir dengan biaya langganan bulanan yang mencekik. Di pakaiapp, toko sepi =
                    gratis. Toko ramai = tetap paling murah!
                </p>
                <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center justify-content-lg-start">
                    <a href="https://wa.me/628XXXXXXXXXX" target="_blank"
                       class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow btn-hover-grow d-flex align-items-center justify-content-center gap-2">
                        Daftar Sekarang <i class="bi bi-whatsapp"></i>
                    </a>
                    <a href="#cara-kerja"
                       class="btn btn-outline-secondary bg-body btn-lg rounded-pill px-4 fw-bold btn-hover-grow">
                        Pelajari Dulu
                    </a>
                </div>
            </div>

            <div class="col-lg-6 position-relative" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
                {{-- Ilustrasi Dashboard Kasir Interaktif --}}
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden hero-pos-card floating-mockup">
                    <div class="bg-body-tertiary p-2 border-bottom d-flex gap-2">
                        <div class="rounded-circle bg-danger" style="width:12px; height:12px;"></div>
                        <div class="rounded-circle bg-warning" style="width:12px; height:12px;"></div>
                        <div class="rounded-circle bg-success" style="width:12px; height:12px;"></div>
                    </div>
                    <div class="p-4 bg-body position-relative" style="height: 380px;">
                        <h5 class="fw-bold mb-4 d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-shop text-warning me-2"></i>Warung 3 Saudara</span>
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 fs-6">
                                <span class="spinner-grow spinner-grow-sm me-1" role="status" aria-hidden="true"></span>
                                Live
                            </span>
                        </h5>

                        <!-- Pesanan masuk dengan animasi delay -->
                        <div
                            class="order-item animated-item-1 d-flex justify-content-between mb-3 p-3 bg-body-tertiary rounded-3 border">
                            <div>
                                <h6 class="fw-bold mb-0">Nasi Goreng Spesial</h6>
                                <small class="text-secondary">Varian: Pedas Sedang</small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold">Rp 25.000</div>
                                <small class="text-secondary">x2</small>
                            </div>
                        </div>

                        <div
                            class="order-item animated-item-2 d-flex justify-content-between mb-3 p-3 bg-body-tertiary rounded-3 border">
                            <div>
                                <h6 class="fw-bold mb-0">Es Teh Manis</h6>
                                <small class="text-secondary">Varian: Jumbo</small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold">Rp 8.000</div>
                                <small class="text-secondary">x2</small>
                            </div>
                        </div>

                        <div class="position-absolute bottom-0 start-0 w-100 p-4 bg-body animated-checkout">
                            <hr class="mt-0 mb-3 text-secondary">
                            <div class="d-flex justify-content-between mb-3">
                                <span class="fw-bold text-secondary">Total Tagihan</span>
                                <span class="fw-black fs-5 text-primary">Rp 66.000</span>
                            </div>
                            <button class="btn btn-primary w-100 rounded-pill fw-bold py-3 btn-checkout">
                                <i class="bi bi-check-circle me-2"></i>Selesaikan Pembayaran
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- NEW: Pain Point vs Solution Section -->
<section class="py-5 bg-body">
    <div class="container py-5">
        <div class="row align-items-center mb-5">
            <div class="col-lg-6 text-center text-lg-start mb-4 mb-lg-0" data-aos="fade-right">
                <h2 class="fw-black text-body mb-3">Kenapa F&B Harus Pindah ke pakaiapp?</h2>
                <p class="text-secondary lead">Kami mengubah aturan main agar UMKM tidak terbebani biaya tak
                    terlihat.</p>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="p-4 rounded-4 bg-danger bg-opacity-10 border border-danger border-opacity-25 h-100">
                            <h5 class="fw-bold text-danger mb-3"><i class="bi bi-x-circle me-2"></i>Aplikasi Lain</h5>
                            <ul class="list-unstyled text-secondary small mb-0">
                                <li class="mb-2">❌ Bayar langganan ratusan ribu/bulan.</li>
                                <li class="mb-2">❌ Toko tutup, argonya tetap jalan.</li>
                                <li>❌ Fitur dibatasi sesuai paket.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div
                            class="p-4 rounded-4 bg-success bg-opacity-10 border border-success border-opacity-25 h-100">
                            <h5 class="fw-bold text-success mb-3"><i class="bi bi-check-circle me-2"></i>pakaiapp.online
                            </h5>
                            <ul class="list-unstyled text-secondary small mb-0">
                                <li class="mb-2">✅ Bebas biaya bulanan selamanya.</li>
                                <li class="mb-2">✅ Cuma bayar kalau ada transaksi sukses.</li>
                                <li>✅ Semua fitur premium terbuka untuk UMKM.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Section (Tetap sama, tambahkan data-aos) -->
<section id="cara-kerja" class="py-5 bg-body-tertiary">
    <div class="container py-5">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="fw-black text-body">Gimana Sistem Bayarnya?</h2>
            <p class="text-secondary">Sistem kredit revolusioner yang memihak pada UMKM.</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-5 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card feature-card border-0 shadow-sm rounded-4 h-100 p-4 bg-body text-center">
                    <div class="display-1 text-warning mb-3"><i class="bi bi-coin"></i></div>
                    <h3 class="fw-black mb-2 text-body">Cuma Rp 300 / Transaksi</h3>
                    <p class="text-secondary mb-0">
                        Kami hanya memotong kredit dompet Anda sebesar Rp300 <b>HANYA JIKA</b> ada pesanan yang sukses
                        dibayar pelanggan. Pesanan batal? Uang kembali!
                    </p>
                </div>
            </div>
            <div class="col-lg-5 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card feature-card border-0 shadow-sm rounded-4 h-100 p-4 bg-body text-center">
                    <div class="display-1 text-primary mb-3"><i class="bi bi-shop"></i></div>
                    <h3 class="fw-black mb-2 text-body">Tanpa Biaya Bulanan</h3>
                    <p class="text-secondary mb-0">
                        Toko sedang libur? Sedang sepi? Tenang, Anda tidak akan ditagih biaya sewa server sepeser pun.
                        Saldo Anda tidak akan hangus.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Floating WhatsApp Button -->
<a href="https://wa.me/628XXXXXXXXXX" target="_blank" class="floating-wa shadow-lg" data-bs-toggle="tooltip"
   data-bs-placement="left" title="Konsultasi Gratis!">
    <i class="bi bi-whatsapp"></i>
</a>

<!-- Tambahkan script AOS & inisialisasi -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        once: true, // Animasi hanya berjalan sekali
        offset: 50
    });
</script>
</body>
</html>
