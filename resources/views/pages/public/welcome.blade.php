@php use App\Central\Models\GlobalSetting; @endphp
    <!DOCTYPE html>
<html lang="id" class="scroll-smooth" id="html-root">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $trxFee = GlobalSetting::where("key", "default_trx_fee")->first()?->value ?? 300;
        $cappingLimit = GlobalSetting::where("key", "default_capping_limit")->first()?->value ?? 150000;
        $cappingLimitFormatted = number_format($cappingLimit, 0, ",", ".");
        $cappingLimitShort = ($cappingLimit / 1000) . "rb";
    @endphp

    <title>Pakaiapp - Kasir Pintar & Menu Digital Premium untuk F&B</title>
    <meta name="description"
          content="Tinggalkan langganan bulanan! Pakaiapp: Kasir Pintar (POS) & Menu Digital Mewah dengan QR Self-Order. UMKM F&B naik kelas, cuma bayar Rp {{ $trxFee }}/transaksi sukses, dan otomatis GRATIS setelah tagihan menyentuh Rp {{ $cappingLimitShort }}/bulan!">
    <meta name="keywords"
          content="pakai app, pakaiapp, menu digital, qr order, aplikasi kasir web, kasir pintar, POS F&B, kasir UMKM, aplikasi kasir tanpa langganan, kasir cafe, sistem kasir retail">
    <meta name="author" content="PT Sinergi Kode Kreatif">
    <meta name="robots" content="index, follow">
    <meta name="language" content="Indonesian">
    <link rel="canonical" href="https://www.pakaiapp.online/">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://www.pakaiapp.online/">
    <meta property="og:title" content="Pakaiapp - Kasir Pintar & Menu Digital F&B">
    <meta property="og:description"
          content="Kasir sepi = Gratis. Menu Digital Mewah = Siap Pakai. Otomatis Premium (Gratis Tanpa Batas) setelah Rp{{ $cappingLimitFormatted }}/bulan tercapai!">
    <meta property="og:image" content="{{ asset('images/og-banner.png') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://www.pakaiapp.online/">
    <meta property="twitter:title" content="Pakaiapp - Kasir Web Bayar Suka-Suka">
    <meta property="twitter:description"
          content="Tinggalkan biaya langganan bulanan. Pindah ke Pakaiapp sekarang dan nikmati fitur kasir enterprise dengan harga UMKM.">
    <meta property="twitter:image" content="{{ asset('images/og-banner.png') }}">

    <script type="application/ld+json">
        {
            "@@context": "https://schema.org",
            "@@type": "SoftwareApplication",
            "name": "Pakaiapp.online",
            "operatingSystem": "Web, Android, iOS (PWA)",
            "applicationCategory": "BusinessApplication",
            "description": "Sistem kasir pintar (POS) berbasis web cloud untuk UMKM F&B dan Retail tanpa biaya langganan bulanan.",
            "url": "https://www.pakaiapp.online",
            "offers": {
                "@@type": "Offer",
                "price": "0",
                "priceCurrency": "IDR",
                "description": "Pendaftaran gratis, biaya penggunaan hanya Rp {{ $trxFee }} per transaksi sukses."
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
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    @vite(["resources/css/welcome.css"])
    @livewireStyles


</head>
<body class="sustainability-platform overflow-x-hidden">

<nav class="navbar-eco fixed top-0 left-0 right-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="/" class="flex items-center gap-2">
                <img src="/android-chrome-192x192.png" alt="Pakaiapp Logo" class="w-10 h-10 rounded-full shadow-sm">
                <span class="text-xl font-bold" style="color: var(--color-text);">pakaiapp</span>
            </a>
            <div class="hidden md:flex items-center gap-6 sm:gap-8">
                <a href="#cara-daftar"
                   class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors cursor-pointer font-medium">Cara
                    Kerja</a>
                <a href="#features"
                   class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors cursor-pointer font-medium">Fitur</a>
                <a href="#calculator"
                   class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors cursor-pointer font-medium">Kalkulator</a>
                <a href="#impact"
                   class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors cursor-pointer font-medium">Testimoni</a>
                <a href="#faq"
                   class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors cursor-pointer font-medium">FAQ</a>
            </div>
            <div class="flex items-center gap-2 sm:gap-4">
                <!-- Theme Toggle (Shared) -->
                <button id="theme-toggle"
                        class="p-1.5 text-slate-500 dark:text-slate-400 hover:text-[var(--color-primary)] transition-colors"
                        aria-label="Toggle Theme">
                    <i class="ph-bold ph-sun text-lg sm:text-xl" id="theme-icon"></i>
                </button>

                <!-- Login (Icon on mobile, Text on desktop) -->
                <a href="/login"
                   class="md:hidden p-1.5 text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors"
                   aria-label="Masuk">
                    <i class="ph-bold ph-sign-in text-xl"></i>
                </a>
                <a href="/login"
                   class="hidden md:block text-[var(--color-text-muted)] hover:text-[var(--color-primary)] font-medium transition-colors cursor-pointer">Masuk</a>

                <!-- Register (Small button on mobile, Normal button on desktop) -->
                <a href="/register" class="btn-primary text-xs py-1.5 px-3 sm:text-sm sm:py-2.5 sm:px-5">Daftar</a>


            </div>
        </div>
    </div>

</nav>

<section class="relative min-h-screen flex items-center pt-16 overflow-hidden">
    <div class="absolute inset-0 z-0">
        <div class="absolute top-20 right-10 w-[400px] h-[400px] rounded-full bg-[var(--color-leaf)]/10 blur-3xl"></div>
        <div
            class="absolute bottom-20 left-10 w-[300px] h-[300px] rounded-full bg-[var(--color-sky)]/10 blur-3xl"></div>
        <svg class="leaf-decoration absolute top-32 right-20 w-24 h-24 text-[var(--color-primary)]" fill="currentColor"
             viewBox="0 0 24 24">
            <path
                d="M17 8C8 10 5.9 16.17 3.82 21.34l1.89.66.95-2.3c.48.17.98.3 1.34.3C19 20 22 3 22 3c-1 2-8 2.25-13 3.25S2 11.5 2 13.5s1.75 3.75 1.75 3.75C7 8 17 8 17 8z"></path>
        </svg>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-20">
        <div class="grid lg:grid-cols-2 gap-8 sm:gap-12 items-center">
            <div>


                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-[var(--color-text)] mb-6 leading-tight">
                    Tinggalkan Langganan,<br>
                    <span class="gradient-text">Bayar Suka-Suka</span>
                </h1>

                <p class="text-base sm:text-lg md:text-xl text-[var(--color-text-muted)] mb-8 max-w-xl">
                    Sistem <strong>Kasir Pintar (POS)</strong> & <strong>Menu Digital Premium</strong> untuk F&B. Kelola
                    penjualan, sediakan QR Self-Order canggih, dan terima QRIS dari perangkat apa saja. Toko sepi?
                    Gratis.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 mb-8 sm:mb-12">
                    <a href="/register"
                       class="btn-primary text-base flex items-center justify-center gap-2 text-center">
                        Buat Toko Sekarang
                    </a>
                    <a href="#calculator" class="btn-secondary text-base text-center">
                        Simulasi Biaya
                    </a>
                </div>

                <div class="flex flex-wrap gap-6">
                    <div class="flex items-center gap-2 text-[var(--color-text-muted)]">
                        <i class="ph-fill ph-check-circle text-[var(--color-primary)] text-lg"></i>
                        <span>Menu Digital Mewah</span>
                    </div>
                    <div class="flex items-center gap-2 text-[var(--color-text-muted)]">
                        <i class="ph-fill ph-check-circle text-[var(--color-primary)] text-lg"></i>
                        <span>Pendaftaran 100% Gratis</span>
                    </div>
                    <div class="flex items-center gap-2 text-[var(--color-text-muted)]">
                        <i class="ph-fill ph-check-circle text-[var(--color-primary)] text-lg"></i>
                        <span>Auto Capping Limit</span>
                    </div>
                </div>
            </div>

            <div class="relative">
                <div class="calculator-preview p-6 sm:p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-semibold text-[var(--color-text)]">Live Biaya Dashboard</h3>
                        <span class="badge-eco text-xs">Simulasi</span>
                    </div>

                    <div class="text-center mb-8">
                        <div class="relative inline-block">
                            <svg class="w-48 h-48" viewBox="0 0 120 120">
                                <circle cx="60" cy="60" r="54" fill="none" stroke="var(--color-border)"
                                        stroke-width="8"></circle>
                                <circle cx="60" cy="60" r="54" fill="none" stroke="url(#gradient)" stroke-width="8"
                                        stroke-linecap="round" stroke-dasharray="339.3" stroke-dashoffset="100"
                                        transform="rotate(-90 60 60)"></circle>
                                <defs>
                                    <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                        <stop offset="0%" stop-color="var(--color-primary)"></stop>
                                        <stop offset="100%" stop-color="var(--color-leaf)"></stop>
                                    </linearGradient>
                                </defs>
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span
                                    class="text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider mb-1">Cuma Bayar</span>
                                <span class="text-4xl font-bold text-[var(--color-text)]">Rp {{ $trxFee }}</span>
                                <span class="text-xs text-[var(--color-text-muted)] mt-1">/ transaksi sukses</span>
                            </div>
                        </div>
                        <p class="text-sm text-[var(--color-text-muted)] mt-6 px-4">
                            Maksimal bayar Rp {{ $cappingLimitFormatted }} sebulan. <br>Lebih dari itu? <strong
                                style="color: var(--color-primary)">GRATIS!</strong>
                        </p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-[var(--color-text-muted)]">50 Transaksi/Bulan</span>
                                <span
                                    class="font-medium text-[var(--color-text)]">Rp {{ number_format(50 * $trxFee, 0, ",", ".") }}</span>
                            </div>
                            <div class="h-2 bg-[var(--color-bg-alt)] rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500"
                                     style="width: 15%; background-color: var(--color-primary);"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-[var(--color-text-muted)]">250 Transaksi/Bulan</span>
                                <span
                                    class="font-medium text-[var(--color-text)]">Rp {{ number_format(250 * $trxFee, 0, ",", ".") }}</span>
                            </div>
                            <div class="h-2 bg-[var(--color-bg-alt)] rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500"
                                     style="width: 50%; background-color: var(--color-leaf);"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-[var(--color-text-muted)]">>500 Transaksi/Bulan</span>
                                <span class="font-medium text-[var(--color-text)]">Rp {{ $cappingLimitFormatted }} (Mentok)</span>
                            </div>
                            <div class="h-2 bg-[var(--color-bg-alt)] rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500"
                                     style="width: 100%; background-color: var(--color-sky);"></div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
</section>

<!-- Showcase Menu Digital -->
<section id="menu-digital" class="py-24 relative overflow-hidden bg-[var(--color-bg)]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 items-center">

            <!-- Text Content -->
            <div class="order-2 lg:order-1">
                <div
                    class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-600 dark:text-amber-400 text-xs font-semibold uppercase tracking-wider mb-4">
                    <i class="ph-fill ph-star"></i> Premium Luxury Experience
                </div>
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold text-[var(--color-text)] mb-6 leading-tight">
                    Lebih Dari Sekadar Kasir.<br>
                    <span class="gradient-text">Menu Digital Bintang Lima.</span>
                </h2>
                <p class="text-lg text-[var(--color-text-muted)] mb-8">
                    Manjakan pelanggan Anda dengan pengalaman pemesanan mandiri yang elegan. Tanpa perlu mengunduh
                    aplikasi tambahan, pelanggan cukup memindai QR di meja.
                </p>

                <div class="space-y-6">
                    <div class="flex gap-4">
                        <div
                            class="w-12 h-12 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-600 shrink-0">
                            <i class="ph-bold ph-magic-wand text-2xl"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-[var(--color-text)] mb-1">Desain Premium & Mewah</h4>
                            <p class="text-[var(--color-text-muted)] text-sm">Tinggalkan buku menu kusam. Hadirkan
                                antarmuka digital yang memukau (mendukung Tema Gelap/Terang) layaknya restoran kelas
                                atas.</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div
                            class="w-12 h-12 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-600 shrink-0">
                            <i class="ph-bold ph-robot text-2xl"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-[var(--color-text)] mb-1">Dilengkapi Asisten AI
                                Pintar</h4>
                            <p class="text-[var(--color-text-muted)] text-sm">Pelanggan bingung mau makan apa? Asisten
                                AI bawaan kami siap memberikan rekomendasi layaknya pelayan pribadi.</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div
                            class="w-12 h-12 rounded-xl bg-[var(--color-primary)]/10 flex items-center justify-center text-[var(--color-primary)] shrink-0">
                            <i class="ph-bold ph-lightning text-2xl"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-[var(--color-text)] mb-1">Pesan & Bayar Tanpa Antre</h4>
                            <p class="text-[var(--color-text-muted)] text-sm">Pesanan langsung masuk ke dapur/kasir dan
                                pelanggan bisa membayar seketika via QRIS atau E-Wallet dari meja.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- UI Mockup -->
            <div class="order-1 lg:order-2 relative flex justify-center lg:justify-end">
                <div
                    class="absolute inset-0 bg-gradient-to-tr from-amber-500/10 to-indigo-500/10 blur-3xl rounded-full w-[80%] h-[80%] m-auto"></div>

                <!-- Mockup Container -->
                <div
                    class="relative w-[280px] sm:w-[320px] h-[580px] sm:h-[650px] bg-zinc-950 rounded-[3rem] p-2 shadow-[0_20px_50px_rgba(0,0,0,0.3)] border-4 border-zinc-800 -rotate-2 hover:rotate-0 transition-transform duration-500">
                    <!-- Screen -->
                    <div
                        class="w-full h-full bg-zinc-900 rounded-[2.5rem] overflow-hidden relative flex flex-col border border-zinc-800 shadow-inner">
                        <!-- Top Bar -->
                        <div
                            class="h-16 flex items-center justify-between px-6 shrink-0 relative z-10 border-b border-zinc-800/50 bg-zinc-900/80 backdrop-blur-md">
                            <div class="text-amber-500 font-bold tracking-widest text-xs sm:text-sm uppercase">Kopi
                                Senja
                            </div>
                            <div class="w-8 h-8 rounded-full bg-zinc-800 flex items-center justify-center">
                                <i class="ph-bold ph-list text-zinc-300"></i>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 p-4 space-y-4 overflow-hidden relative">
                            <!-- Banner -->
                            <div
                                class="w-full h-28 sm:h-32 rounded-2xl bg-gradient-to-br from-zinc-800 to-zinc-900 border border-zinc-700/50 flex items-center justify-center p-4">
                                <div class="text-center">
                                    <div class="text-[10px] sm:text-xs text-amber-500 font-bold mb-1 tracking-wider">
                                        SPECIAL OFFER
                                    </div>
                                    <div class="text-zinc-100 font-semibold text-sm sm:text-base">Caramel Macchiato
                                    </div>
                                </div>
                            </div>

                            <!-- Categories -->
                            <div class="flex gap-2">
                                <div
                                    class="px-3 sm:px-4 py-1.5 rounded-full bg-amber-500 text-zinc-950 text-[10px] sm:text-xs font-bold">
                                    Coffee
                                </div>
                                <div
                                    class="px-3 sm:px-4 py-1.5 rounded-full bg-zinc-800 text-zinc-400 text-[10px] sm:text-xs font-semibold">
                                    Non-Coffee
                                </div>
                            </div>

                            <!-- Products -->
                            <div class="space-y-3">
                                <div class="flex gap-3 bg-zinc-800/50 p-3 rounded-2xl border border-zinc-700/30">
                                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-xl bg-zinc-700 shrink-0"></div>
                                    <div class="flex-1">
                                        <div class="text-zinc-100 font-semibold text-xs sm:text-sm">Espresso</div>
                                        <div class="text-zinc-400 text-[9px] sm:text-[10px] mt-1 line-clamp-1">Single
                                            origin arabica beans
                                        </div>
                                        <div class="text-amber-500 font-bold text-[10px] sm:text-xs mt-2">Rp 18.000
                                        </div>
                                    </div>
                                    <div
                                        class="w-6 h-6 rounded-full bg-amber-500 text-zinc-950 flex items-center justify-center self-end">
                                        <i class="ph-bold ph-plus text-xs"></i>
                                    </div>
                                </div>
                                <div class="flex gap-3 bg-zinc-800/50 p-3 rounded-2xl border border-zinc-700/30">
                                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-xl bg-zinc-700 shrink-0"></div>
                                    <div class="flex-1">
                                        <div class="text-zinc-100 font-semibold text-xs sm:text-sm">Caffe Latte</div>
                                        <div class="text-zinc-400 text-[9px] sm:text-[10px] mt-1 line-clamp-1">Espresso
                                            with steamed milk
                                        </div>
                                        <div class="text-amber-500 font-bold text-[10px] sm:text-xs mt-2">Rp 24.000
                                        </div>
                                    </div>
                                    <div
                                        class="w-6 h-6 rounded-full bg-amber-500 text-zinc-950 flex items-center justify-center self-end">
                                        <i class="ph-bold ph-plus text-xs"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Floating AI Button -->
                        <div
                            class="absolute bottom-20 right-4 w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-zinc-100 text-zinc-900 flex items-center justify-center shadow-[0_0_20px_rgba(244,244,245,0.3)]">
                            <i class="ph-fill ph-sparkle text-lg sm:text-xl"></i>
                        </div>

                        <!-- Bottom Bar -->
                        <div
                            class="h-14 sm:h-16 bg-amber-500 m-2 mt-0 rounded-2xl flex items-center justify-between px-4 sm:px-5 shrink-0">
                            <div class="text-zinc-950 font-bold text-sm">2 Item</div>
                            <div class="text-zinc-950 font-black text-sm flex items-center gap-1">Checkout <i
                                    class="ph-bold ph-arrow-right"></i></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<section id="calculator"
         class="py-24 relative overflow-hidden transition-colors bg-[var(--color-bg-alt)] border-t border-[var(--color-border)]">
    <div class="max-w-5xl mx-auto px-4">
        <!-- Section Header -->
        <div class="text-center max-w-2xl mx-auto mb-16">
            <div
                class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-[var(--color-primary)]/10 border border-[var(--color-primary)]/20 text-[var(--color-primary)] text-xs font-semibold uppercase tracking-wider mb-3">
                <i class="ph-fill ph-tag"></i> Skema Adil
            </div>
            <h2 class="font-heading text-3xl sm:text-4xl font-extrabold text-[var(--color-text)]">Bayar Hanya <span
                    class="text-[var(--color-primary)]">Saat Ada Penjualan</span></h2>
            <p class="text-[var(--color-text-muted)] mt-4 leading-relaxed font-light">Kami meniadakan biaya langganan
                bulanan. Anda hanya ditarik biaya kecil per transaksi sukses. Sepi = Rp 0.</p>
        </div>

        <div class="grid md:grid-cols-2 gap-8 lg:gap-12 items-center">

            <!-- Calculator Card -->
            <div
                class="max-w-lg mx-auto w-full bg-[var(--color-bg-card)] border border-[var(--color-border)] rounded-[2.5rem] p-8 sm:p-10 shadow-lg relative transition-colors">
                <div class="absolute top-8 right-8">
                    <i class="ph-duotone ph-calculator text-4xl text-[var(--color-primary)] opacity-20"></i>
                </div>
                <h3 class="font-heading text-2xl font-bold text-[var(--color-text)] mb-6">Simulasi Biaya Bulanan</h3>

                <div class="mb-8">
                    <div class="flex justify-between items-end mb-4">
                        <span
                            class="text-sm font-semibold text-[var(--color-text-muted)]">Volume Transaksi / Bulan</span>
                        <span class="font-heading font-extrabold text-2xl text-[var(--color-text)]">
                            <span id="trxDisplay">0</span> <span
                                class="text-xs font-normal text-[var(--color-text-subtle)] uppercase ml-1">Transaksi</span>
                        </span>
                    </div>
                    <!-- Range Slider -->
                    <input type="range" id="trxSlider" min="0" max="2000" step="50" value="0"
                           class="w-full h-2 bg-[var(--color-bg-alt)] rounded-lg appearance-none cursor-pointer outline-none transition-all [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:w-6 [&::-webkit-slider-thumb]:h-6 [&::-webkit-slider-thumb]:bg-[var(--color-primary)] [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:shadow-md">
                </div>

                <div class="p-6 rounded-2xl bg-[var(--color-bg-alt)] border border-[var(--color-border)] text-center">
                    <div class="text-xs font-semibold uppercase tracking-wider text-[var(--color-text-subtle)] mb-2">
                        Total Biaya Pakaiapp Bulan Ini
                    </div>
                    <!-- Displays "GRATIS!" or formatted cost -->
                    <div
                        class="font-heading text-4xl sm:text-5xl font-extrabold text-[var(--color-primary)] tracking-tight mb-2"
                        id="costPakaiapp">GRATIS!
                    </div>
                    <!-- Display breakdown -->
                    <div class="text-xs font-mono text-[var(--color-text-muted)]" id="costNote">Rp {{ $trxFee }} × 0
                        transaksi = Rp 0
                    </div>

                    <!-- Unlimited Capping Badge -->
                    <div id="unlimitedBadge"
                         class="hidden mt-4 bg-[var(--color-primary)]/10 text-[var(--color-primary)] text-xs font-bold px-3 py-2 rounded-xl border border-[var(--color-primary)]/20 w-full animate-pulse justify-center items-center gap-1.5">
                        <i class="ph-fill ph-sparkles"></i> Unlimited — Sisa Bulan Gratis Sepenuhnya!
                    </div>
                </div>

                <div
                    class="mt-6 p-4 bg-[var(--color-bg)] border border-[var(--color-border)] rounded-2xl text-xs text-[var(--color-text-muted)] leading-relaxed font-light">
                    <strong class="text-[var(--color-text)] font-bold block mb-1">🎉 Cara Hitung:</strong>
                    Anda hanya membayar Rp {{ $trxFee }} per transaksi sukses. Apabila total tagihan Anda dalam sebulan
                    sudah mencapai <strong
                        class="text-[var(--color-primary)] font-semibold">Rp {{ $cappingLimitFormatted }}</strong>,
                    semua transaksi selanjutnya di bulan tersebut GRATIS!
                </div>
            </div>

            <!-- Comparison Sidebar (Hidden on mobile) -->
            <div class="hidden md:block space-y-6">
                <h3 class="font-heading text-2xl font-bold text-[var(--color-text)]">Kenapa Pindah ke Pakaiapp?</h3>
                <p class="text-[var(--color-text-muted)]">Perbandingan mencolok skema biaya tradisional vs skema adil
                    Pakaiapp.</p>
                <div class="space-y-4 mt-6">
                    <!-- Pakaiapp -->
                    <div
                        class="p-6 rounded-2xl bg-[var(--color-primary)]/10 border border-[var(--color-primary)]/20 shadow-sm relative overflow-hidden">
                        <div class="absolute -right-4 -bottom-4 p-4 opacity-10">
                            <i class="ph-fill ph-check-circle text-8xl text-[var(--color-primary)]"></i>
                        </div>
                        <div class="flex items-center gap-3 mb-3 relative z-10">
                            <i class="ph-fill ph-check-circle text-[var(--color-primary)] text-xl"></i>
                            <span class="font-semibold text-[var(--color-text)]">Pakaiapp</span>
                        </div>
                        <ul class="text-sm text-[var(--color-text-muted)] space-y-2 ml-8 relative z-10 list-disc pl-1">
                            <li><strong class="text-[var(--color-text)]">Gratis 100%</strong> biaya langganan bulanan
                            </li>
                            <li>Toko sepi = <strong class="text-[var(--color-primary)]">Bayar Rp 0</strong></li>
                            <li>Semua fitur enterprise langsung terbuka</li>
                            <li>Ada <strong class="text-[var(--color-text)]">Capping Limit</strong> (Batas maksimal
                                bayar)
                            </li>
                        </ul>
                    </div>
                    <!-- Competitor -->
                    <div
                        class="p-6 rounded-2xl bg-red-50 dark:bg-red-950/20 border border-red-100 dark:border-red-900/30">
                        <div class="flex items-center gap-3 mb-3">
                            <i class="ph-fill ph-x-circle text-red-500 text-xl"></i>
                            <span class="font-semibold text-red-700 dark:text-red-400">Aplikasi Kasir Biasa</span>
                        </div>
                        <ul class="text-sm text-red-600/80 dark:text-red-400/80 space-y-2 ml-8 list-disc pl-1">
                            <li>Biaya langganan Rp 150rb - 300rb per bulan</li>
                            <li>Toko lagi sepi tetap ditagih penuh</li>
                            <li>Fitur canggih dikunci di paket mahal</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="cara-daftar" class="py-24 relative">

    <div class="max-w-5xl mx-auto px-4">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <div
                class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-indigo-50 dark:bg-indigo-950/50 border border-indigo-200/50 dark:border-indigo-800/30 text-indigo-700 dark:text-indigo-400 text-xs font-semibold uppercase tracking-wider mb-3">
                <i class="ph-fill ph-lightning"></i> Cara Kerja
            </div>
            <h2 class="font-heading text-3xl sm:text-4xl font-extrabold text-[var(--color-text)]">Aplikasi Kasir Tanpa
                Pasang: <span class="text-emerald-600 dark:text-emerald-400">3 Langkah</span></h2>
            <p class="text-[var(--color-text-muted)] mt-4 leading-relaxed font-light">Aplikasi kasir berbasis web cloud
                sepenuhnya. Tidak perlu unduh aplikasi dari App Store, tidak perlu ahli IT. Buka browser, daftar,
                langsung bisa terima pesanan.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Step 1 -->
            <div
                class="bg-[var(--color-bg-card)] border border-[var(--color-border)] p-8 rounded-[2rem] shadow-sm relative overflow-hidden group hover:border-emerald-500/50 dark:hover:border-emerald-400/50 transition-all duration-300">

                <div
                    class="w-12 h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-6">
                    <i class="ph-bold ph-storefront text-2xl"></i>
                </div>
                <h3 class="font-heading text-xl font-bold text-[var(--color-text)] mb-3">Daftar & Buat Toko</h3>
                <p class="text-[var(--color-text-muted)] text-sm leading-relaxed mb-6 font-light">Klik "Daftar Gratis",
                    isi nama tokomu, email, dan nomor WhatsApp. Verifikasi OTP dari email secara instan. Workspace
                    kasirmu langsung siap.</p>
                <span
                    class="inline-flex items-center gap-1 text-xs font-semibold text-[var(--color-text-subtle)] bg-[var(--color-bg-alt)] dark:text-[var(--color-text-subtle)] px-3 py-1 rounded-full"><i
                        class="ph ph-clock"></i> ~2 Menit</span>
            </div>

            <!-- Step 2 -->
            <div
                class="bg-[var(--color-bg-card)] border border-[var(--color-border)] p-8 rounded-[2rem] shadow-sm relative overflow-hidden group hover:border-indigo-500/50 dark:hover:border-indigo-400/50 transition-all duration-300">

                <div
                    class="w-12 h-12 rounded-2xl bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center mb-6">
                    <i class="ph-bold ph-list-plus text-2xl"></i>
                </div>
                <h3 class="font-heading text-xl font-bold text-[var(--color-text)] mb-3">Input Menu & Cetak QR</h3>
                <p class="text-[var(--color-text-muted)] text-sm leading-relaxed mb-6 font-light">Masukkan produk atau
                    menu Anda lengkap dengan harga, kategori, dan foto. Sistem otomatis membuatkan Menu Digital elegan
                    beserta kode QR untuk tiap meja.</p>
                <span
                    class="inline-flex items-center gap-1 text-xs font-semibold text-[var(--color-text-subtle)] bg-[var(--color-bg-alt)] dark:text-[var(--color-text-subtle)] px-3 py-1 rounded-full"><i
                        class="ph ph-clock"></i> ~10 Menit</span>
            </div>

            <!-- Step 3 -->
            <div
                class="bg-[var(--color-bg-card)] border border-[var(--color-border)] p-8 rounded-[2rem] shadow-sm relative overflow-hidden group hover:border-emerald-500/50 dark:hover:border-emerald-400/50 transition-all duration-300">

                <div
                    class="w-12 h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-6">
                    <i class="ph-bold ph-paper-plane text-2xl"></i>
                </div>
                <h3 class="font-heading text-xl font-bold text-[var(--color-text)] mb-3">Kasir & Pelanggan
                    Terhubung</h3>
                <p class="text-[var(--color-text-muted)] text-sm leading-relaxed mb-6 font-light">Pelanggan memesan
                    mandiri lewat Menu Digital di meja, dan pesanan langsung tampil di layar Kasir/Dapur. Staf kasir
                    juga tetap bisa melayani manual.</p>
                <span
                    class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/30 px-3 py-1 rounded-full"><i
                        class="ph ph-check-circle"></i> Siap Jualan</span>
            </div>
        </div>
    </div>
</section>

<section id="features" class="py-16 sm:py-24 bg-[var(--color-bg-alt)]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10 sm:mb-16">
            <div class="badge-eco mx-auto mb-4">
                <span>Fitur Super Lengkap</span>
            </div>
            <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-[var(--color-text)] mb-4">
                Segala yang Dibutuhkan <br><span class="gradient-text">UMKM Naik Kelas</span>
            </h2>
            <p class="text-lg text-[var(--color-text-muted)] max-w-2xl mx-auto">
                Satu aplikasi kasir web cerdas untuk kelola pesanan, stok, hingga analitik bisnis Anda.
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="feature-card border-2 border-[var(--color-primary)]/20 shadow-lg relative overflow-hidden">
                <div
                    class="absolute top-0 right-0 bg-[var(--color-primary)] text-white text-[10px] font-bold px-3 py-1 rounded-bl-xl uppercase tracking-wider">
                    Unggulan
                </div>
                <div class="feature-icon" style="background-color: var(--color-leaf)15; color: var(--color-leaf);">
                    <i class="ph-bold ph-star text-3xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-[var(--color-text)] mb-2">Menu Digital Premium & QR</h3>
                <p class="text-[var(--color-text-muted)]">
                    Desain UI super mewah, Asisten AI bawaan, dan fitur self-order dari meja. Pelanggan memesan dan
                    membayar mandiri tanpa antre di kasir.
                </p>
            </div>

            <div class="feature-card">
                <div class="feature-icon"
                     style="background-color: var(--color-primary)15; color: var(--color-primary);">
                    <i class="ph-bold ph-storefront text-3xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-[var(--color-text)] mb-2">Kasir Pintar (POS)</h3>
                <p class="text-[var(--color-text-muted)]">
                    Proses pesanan dan cetak struk langsung dari browser HP, tablet, atau PC kasir Anda. Tanpa perlu
                    download aplikasi.
                </p>
            </div>

            <div class="feature-card">
                <div class="feature-icon" style="background-color: var(--color-sky)15; color: var(--color-sky);">
                    <i class="ph-bold ph-wallet text-3xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-[var(--color-text)] mb-2">Terima QRIS & E-Wallet</h3>
                <p class="text-[var(--color-text-muted)]">
                    Terima pembayaran dari GoPay, OVO, Dana, LinkAja, BCA, dll secara instan tanpa perlu mesin EDC
                    tambahan.
                </p>
            </div>

            <div class="feature-card">
                <div class="feature-icon" style="background-color: var(--color-earth)15; color: var(--color-earth);">
                    <i class="ph-bold ph-chart-bar text-3xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-[var(--color-text)] mb-2">Laporan & Analitik</h3>
                <p class="text-[var(--color-text-muted)]">
                    Pantau grafik omset harian, produk paling laris, dan performa staf langsung di satu dasbor yang
                    rapi.
                </p>
            </div>

            <div class="feature-card">
                <div class="feature-icon" style="background-color: var(--color-sun)15; color: var(--color-sun);">
                    <i class="ph-bold ph-package text-3xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-[var(--color-text)] mb-2">Manajemen Stok</h3>
                <p class="text-[var(--color-text-muted)]">
                    Lacak stok bahan baku produk dan dapatkan notifikasi peringatan otomatis saat stok menipis.
                </p>
            </div>

            <div class="feature-card">
                <div class="feature-icon"
                     style="background-color: var(--color-primary)15; color: var(--color-primary);">
                    <i class="ph-bold ph-users text-3xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-[var(--color-text)] mb-2">Multi-Staf & Hak Akses</h3>
                <p class="text-[var(--color-text-muted)]">
                    Beri batas akses yang berbeda untuk Owner, Manajer, Kasir, dan Dapur agar data keuangan tetap aman.
                </p>
            </div>
        </div>
    </div>
</section>

<section id="impact" class="py-16 sm:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10 sm:mb-16">
            <div class="badge-eco mx-auto mb-4">
                <span>Ekosistem Pakaiapp</span>
            </div>
            <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-[var(--color-text)] mb-4">
                Memberi Kemudahan <br><span class="gradient-text">Bagi Belasan Ribu UMKM</span>
            </h2>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-20">
            <div class="organic-card p-6 text-center">
                <div class="impact-number">15K+</div>
                <div class="impact-label">Toko UMKM Aktif</div>
            </div>
            <div class="organic-card p-6 text-center">
                <div class="impact-number">1.2M+</div>
                <div class="impact-label">Transaksi Sukses</div>
            </div>
            <div class="organic-card p-6 text-center">
                <div class="impact-number">Rp 0</div>
                <div class="impact-label">Biaya Pendaftaran</div>
            </div>
            <div class="organic-card p-6 text-center">
                <div class="impact-number">2 Min</div>
                <div class="impact-label">Setup Langsung Jualan</div>
            </div>
        </div>

        <div
            class="text-center bg-[var(--color-bg-card)] rounded-[2rem] border border-[var(--color-border)] p-10 sm:p-16 max-w-3xl mx-auto organic-card">
            <i class="ph-fill ph-quotes text-5xl text-[var(--color-leaf)] opacity-20 mb-6"></i>
            <h3 class="text-2xl font-bold text-[var(--color-text)] mb-6 font-heading leading-relaxed">
                "Sejak pakai sistem Pakaiapp di kafe kami, operasional jauh lebih mudah. Dari kasir pintar, manajemen
                stok, hingga QR order ada di satu web. Pelayanan makin cepat!"
            </h3>
            <div class="flex items-center justify-center gap-4 mt-8">
                <div
                    class="w-12 h-12 rounded-full bg-[var(--color-primary)] text-white flex items-center justify-center font-bold text-lg">
                    M
                </div>
                <div class="text-left">
                    <p class="font-bold text-[var(--color-text)] text-sm">Mirayeni</p>
                    <p class="text-[var(--color-text-muted)] text-xs uppercase tracking-wider font-semibold">Owner Sama
                        Roti Kukus</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-16 sm:py-24 bg-[var(--color-bg-dark)] relative overflow-hidden">
    <div class="absolute inset-0 z-0">
        <div
            class="absolute top-0 left-1/4 w-[300px] h-[300px] rounded-full bg-[var(--color-primary)]/20 blur-3xl"></div>
        <div
            class="absolute bottom-0 right-1/4 w-[250px] h-[250px] rounded-full bg-[var(--color-leaf)]/20 blur-3xl"></div>
    </div>

    <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 text-white/80 text-sm mb-6">
            <i class="ph-bold ph-rocket text-[var(--color-leaf)]"></i>
            <span>Mulai Transformasi Bisnismu</span>
        </div>

        <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-6 font-heading">
            Siap Jualan Lebih Efisien <br><span class="text-[var(--color-leaf)]">Hari Ini Juga?</span>
        </h2>

        <p class="text-lg text-white/70 mb-8 max-w-2xl mx-auto font-body">
            Bergabung bersama belasan ribu toko yang sudah memakai Pakaiapp. Daftar gratis, langsung siap jualan.
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center mb-8 sm:mb-12">
            <a href="/register" class="btn-primary text-base">Buat Akun Toko Gratis</a>
        </div>

        <div class="flex flex-wrap justify-center gap-6 sm:gap-8 text-white/60 font-medium">
            <div class="flex items-center gap-2">
                <i class="ph-fill ph-check-circle text-[var(--color-leaf)]"></i>
                <span>Tanpa Biaya Bulanan</span>
            </div>
            <div class="flex items-center gap-2">
                <i class="ph-fill ph-check-circle text-[var(--color-leaf)]"></i>
                <span>Tanpa Kartu Kredit</span>
            </div>
            <div class="flex items-center gap-2">
                <i class="ph-fill ph-check-circle text-[var(--color-leaf)]"></i>
                <span>Langsung Aktif</span>
            </div>
        </div>
    </div>
</section>

<section id="faq" class="py-24 bg-[var(--color-bg-card)] border-t border-[var(--color-border)] transition-colors">
    <div class="max-w-4xl mx-auto px-4">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <div
                class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200/50 dark:border-emerald-800/30 text-emerald-700 dark:text-emerald-400 text-xs font-semibold uppercase tracking-wider mb-3">
                <i class="ph-fill ph-question"></i> FAQ
            </div>
            <h2 class="font-heading text-3xl sm:text-4xl font-extrabold text-[var(--color-text)]">Pertanyaan Umum <span
                    class="text-emerald-600 dark:text-emerald-400">Terbanyak</span></h2>
        </div>

        <div class="space-y-4 max-w-3xl mx-auto">
            <!-- FAQ 1 -->
            <div
                class="border border-[var(--color-border)] bg-[var(--color-bg-alt)] rounded-2xl overflow-hidden transition-colors duration-300">
                <button
                    class="w-full text-left px-6 py-5 flex justify-between items-center focus:outline-none hover:bg-slate-100/50 dark:hover:bg-slate-900/50 transition-colors"
                    onclick="toggleAccordion(this)">
                    <span
                        class="font-bold text-[var(--color-text)] text-base">Apakah benar-benar gratis untuk daftar?</span>
                    <i class="ph ph-caret-down text-[var(--color-text-subtle)] transition-transform duration-300"></i>
                </button>
                <div class="faq-content max-h-0 overflow-hidden transition-all duration-300">
                    <p class="px-6 pb-6 text-[var(--color-text-muted)] text-sm leading-relaxed font-light">Ya,
                        pendaftaran 100% gratis dan tidak perlu kartu kredit. Anda hanya akan dikenakan biaya
                        Rp {{ $trxFee }} per transaksi sukses yang terjadi. Jika toko sepi atau tidak ada transaksi,
                        tidak ada biaya sama sekali.</p>
                </div>
            </div>

            <!-- FAQ 2 -->
            <div
                class="border border-[var(--color-border)] bg-[var(--color-bg-alt)] rounded-2xl overflow-hidden transition-colors duration-300">
                <button
                    class="w-full text-left px-6 py-5 flex justify-between items-center focus:outline-none hover:bg-slate-100/50 dark:hover:bg-slate-900/50 transition-colors"
                    onclick="toggleAccordion(this)">
                    <span
                        class="font-bold text-[var(--color-text)] text-base">Apa itu "Otomatis Gratis Unlimited"?</span>
                    <i class="ph ph-caret-down text-[var(--color-text-subtle)] transition-transform duration-300"></i>
                </button>
                <div class="faq-content max-h-0 overflow-hidden transition-all duration-300">
                    <p class="px-6 pb-6 text-[var(--color-text-muted)] text-sm leading-relaxed font-light">Jika total
                        tagihan Pakaiapp Anda dalam satu bulan sudah mencapai Rp {{ $cappingLimitFormatted }}
                        (setara {{ floor($cappingLimit / $trxFee) }} transaksi), maka semua transaksi berikutnya di
                        bulan tersebut gratis sepenuhnya tanpa batas. Jadi biaya maksimal Pakaiapp dalam sebulan adalah
                        Rp {{ $cappingLimitFormatted }}, berapapun jumlah transaksinya.</p>
                </div>
            </div>

            <!-- FAQ 3 -->
            <div
                class="border border-[var(--color-border)] bg-[var(--color-bg-alt)] rounded-2xl overflow-hidden transition-colors duration-300">
                <button
                    class="w-full text-left px-6 py-5 flex justify-between items-center focus:outline-none hover:bg-slate-100/50 dark:hover:bg-slate-900/50 transition-colors"
                    onclick="toggleAccordion(this)">
                    <span
                        class="font-bold text-[var(--color-text)] text-base">Apakah perlu install aplikasi di HP?</span>
                    <i class="ph ph-caret-down text-[var(--color-text-subtle)] transition-transform duration-300"></i>
                </button>
                <div class="faq-content max-h-0 overflow-hidden transition-all duration-300">
                    <p class="px-6 pb-6 text-[var(--color-text-muted)] text-sm leading-relaxed font-light">Tidak perlu!
                        Pakaiapp berbasis web dan berjalan langsung di browser HP, tablet, atau PC. Anda bisa
                        menambahkan shortcut ke homescreen HP layaknya aplikasi (PWA) untuk kemudahan akses.</p>
                </div>
            </div>

            <!-- FAQ 4 -->
            <div
                class="border border-[var(--color-border)] bg-[var(--color-bg-alt)] rounded-2xl overflow-hidden transition-colors duration-300">
                <button
                    class="w-full text-left px-6 py-5 flex justify-between items-center focus:outline-none hover:bg-slate-100/50 dark:hover:bg-slate-900/50 transition-colors"
                    onclick="toggleAccordion(this)">
                    <span class="font-bold text-[var(--color-text)] text-base">Apakah data toko saya aman?</span>
                    <i class="ph ph-caret-down text-[var(--color-text-subtle)] transition-transform duration-300"></i>
                </button>
                <div class="faq-content max-h-0 overflow-hidden transition-all duration-300">
                    <p class="px-6 pb-6 text-[var(--color-text-muted)] text-sm leading-relaxed font-light">Data Anda
                        disimpan di server cloud terenkripsi dan dibackup secara rutin. Setiap akun toko memiliki
                        subdomain dan database terisolasi, sehingga data Anda tidak bercampur dengan toko lain.</p>
                </div>
            </div>

            <!-- FAQ 5 -->
            <div
                class="border border-[var(--color-border)] bg-[var(--color-bg-alt)] rounded-2xl overflow-hidden transition-colors duration-300">
                <button
                    class="w-full text-left px-6 py-5 flex justify-between items-center focus:outline-none hover:bg-slate-100/50 dark:hover:bg-slate-900/50 transition-colors"
                    onclick="toggleAccordion(this)">
                    <span class="font-bold text-[var(--color-text)] text-base">Bagaimana cara top-up dan cairkan dana penjualan?</span>
                    <i class="ph ph-caret-down text-[var(--color-text-subtle)] transition-transform duration-300"></i>
                </button>
                <div class="faq-content max-h-0 overflow-hidden transition-all duration-300">
                    <p class="px-6 pb-6 text-[var(--color-text-muted)] text-sm leading-relaxed font-light">Dana hasil
                        penjualan dari pelanggan yang bayar via QRIS/E-Wallet langsung masuk ke wallet toko Anda di
                        Pakaiapp. Proses penarikan ke rekening bank dilakukan secara manual oleh tim kami — hubungi
                        support via WhatsApp untuk proses pencairan.</p>
                </div>
            </div>

            <!-- FAQ 6 -->
            <div
                class="border border-[var(--color-border)] bg-[var(--color-bg-alt)] rounded-2xl overflow-hidden transition-colors duration-300">
                <button
                    class="w-full text-left px-6 py-5 flex justify-between items-center focus:outline-none hover:bg-slate-100/50 dark:hover:bg-slate-900/50 transition-colors"
                    onclick="toggleAccordion(this)">
                    <span class="font-bold text-[var(--color-text)] text-base">Apakah ada biaya tambahan untuk fitur QRIS atau QR Self-Order?</span>
                    <i class="ph ph-caret-down text-[var(--color-text-subtle)] transition-transform duration-300"></i>
                </button>
                <div class="faq-content max-h-0 overflow-hidden transition-all duration-300">
                    <p class="px-6 pb-6 text-[var(--color-text-muted)] text-sm leading-relaxed font-light">Tidak ada!
                        Semua fitur termasuk QRIS, QR Self-Order, multi-staf, laporan, dan manajemen stok sudah termasuk
                        dalam satu biaya flat Rp {{ $trxFee }}/transaksi. Tidak ada paket berbeda atau fitur yang
                        dikunci di balik paywall.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="bg-[var(--color-bg)] border-t border-[var(--color-border)] py-10 sm:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-5 gap-6 sm:gap-8 mb-8 sm:mb-12">
            <div class="col-span-2 md:col-span-2">
                <div class="flex items-center gap-2 mb-4">
                    <img src="/android-chrome-192x192.png" alt="Pakaiapp Logo" class="w-10 h-10 rounded-full shadow-sm">
                    <span class="text-xl font-bold font-heading text-[var(--color-text)]">pakaiapp<span
                            style="color: var(--color-primary)">.online</span></span>
                </div>
                <p class="text-[var(--color-text-muted)] text-sm mb-6 max-w-sm">
                    Sistem kasir cerdas berbasis web untuk UMKM F&B dan Retail — tanpa beban biaya langganan bulanan.
                </p>
                <div class="flex gap-4">
                    <a href="https://wa.me/6285172441544"
                       class="w-10 h-10 rounded-full bg-[var(--color-bg-alt)] flex items-center justify-center text-[var(--color-text-muted)] hover:bg-[var(--color-primary)] hover:text-white transition-colors cursor-pointer">
                        <i class="ph-bold ph-whatsapp-logo text-xl"></i>
                    </a>
                    <a href="mailto:support@pakaiapp.online"
                       class="w-10 h-10 rounded-full bg-[var(--color-bg-alt)] flex items-center justify-center text-[var(--color-text-muted)] hover:bg-[var(--color-primary)] hover:text-white transition-colors cursor-pointer">
                        <i class="ph-bold ph-envelope text-xl"></i>
                    </a>
                </div>
            </div>

            <div>
                <h4 class="font-semibold text-[var(--color-text)] mb-4">Produk</h4>
                <ul class="space-y-3">
                    <li><a href="#cara-daftar"
                           class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors text-sm">Cara
                            Kerja</a></li>
                    <li><a href="#features"
                           class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors text-sm">Fitur
                            Lengkap</a></li>
                    <li><a href="#calculator"
                           class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors text-sm">Simulasi
                            Harga</a></li>
                    <li><a href="/login"
                           class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors text-sm">Login
                            Kasir</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-semibold text-[var(--color-text)] mb-4">Dukungan</h4>
                <ul class="space-y-3">
                    <li><a href="https://wa.me/6285172441544"
                           class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors text-sm">WhatsApp
                            Support</a></li>
                    <li><a href="mailto:support@pakaiapp.online"
                           class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors text-sm">Email
                            Support</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-semibold text-[var(--color-text)] mb-4">Legal</h4>
                <ul class="space-y-3">
                    <li><a href="#" onclick="openModal('tncModal')"
                           class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors text-sm">Syarat
                            & Ketentuan</a></li>
                    <li><a href="#" onclick="openModal('refundModal')"
                           class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors text-sm">Kebijakan
                            Pengembalian Dana</a></li>
                </ul>
            </div>
        </div>

        <div
            class="pt-8 border-t border-[var(--color-border)] flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-[var(--color-text-muted)] text-sm">
                &copy; {{ date("Y") }} pakaiapp.online. Produk dari ngopikode (PT Sinergi Kode Kreatif)
            </p>
            <div class="flex items-center gap-2 text-sm text-[var(--color-text-muted)]">
                <i class="ph-bold ph-shield-check text-[var(--color-leaf)]"></i>
                <span>Platform Aman & Terenkripsi</span>
            </div>
        </div>
    </div>
</footer>


<!-- ============================================
     MODALS (T&C & REFUND WITH 100% COMPLETE ORIGINAL TEXTS)
============================================ -->

<!-- Modal TNC -->
<div id="tncModal" class="custom-modal fixed inset-0 z-[100] flex items-center justify-center p-4">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('tncModal')"></div>
    <!-- Box content -->
    <div
        class="custom-modal-content relative bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2.5rem] w-full max-w-2xl max-h-[85vh] overflow-hidden flex flex-col shadow-2xl transition-colors">
        <!-- Header -->
        <div
            class="px-8 py-5 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center shrink-0">
            <h3 class="font-heading font-extrabold text-slate-900 dark:text-white text-lg flex items-center gap-2">
                <i class="ph-fill ph-shield-check text-emerald-600 dark:text-emerald-400"></i> Syarat & Ketentuan
                Layanan
            </h3>
            <button onclick="closeModal('tncModal')"
                    class="p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-white rounded-full transition-colors">
                <i class="ph-bold ph-x text-lg"></i></button>
        </div>
        <!-- Scrollable Body -->
        <div
            class="p-8 overflow-y-auto text-slate-600 dark:text-slate-300 text-sm leading-relaxed space-y-5 font-light">
            <p>Selamat datang di <strong class="text-slate-900 dark:text-white font-bold">Pakaiapp</strong>. Harap
                membaca Syarat & Ketentuan ini dengan saksama sebelum mendaftar dan menggunakan platform kami.</p>

            <h4 class="font-heading font-bold text-slate-900 dark:text-white text-base">1. KETENTUAN UMUM &
                DEFINISI</h4>
            <ul class="list-disc pl-5 space-y-1.5">
                <li><strong class="text-slate-800 dark:text-white">Pakaiapp</strong> adalah platform
                    Software-as-a-Service (SaaS) aplikasi kasir pintar (Point of Sales) berbasis web cloud yang
                    dikembangkan oleh PT Sinergi Kode Kreatif.
                </li>
                <li><strong class="text-slate-800 dark:text-white">Pengguna</strong> adalah pemilik usaha (merchant),
                    beserta staf/admin yang ditunjuk, yang mendaftarkan diri.
                </li>
                <li><strong class="text-slate-800 dark:text-white">Layanan</strong> mencakup penyediaan sistem kasir,
                    manajemen varian menu, etalase online (QR self-order), dan pelaporan.
                </li>
            </ul>

            <h4 class="font-heading font-bold text-slate-900 dark:text-white text-base">2. PENDAFTARAN AKUN DAN
                KEAMANAN</h4>
            <ul class="list-disc pl-5 space-y-1.5">
                <li>Pengguna wajib memberikan data informasi bisnis yang akurat, benar, dan terbaru pada saat proses
                    pendaftaran.
                </li>
                <li>Pengguna bertanggung jawab penuh atas keamanan kredensial akun dan hak akses karyawan
                    masing-masing.
                </li>
            </ul>

            <h4 class="font-heading font-bold text-slate-900 dark:text-white text-base">3. FUNGSI DOMPET DIGITAL
                (WALLET) & BIAYA TRANSAKSI</h4>
            <ul class="list-disc pl-5 space-y-1.5">
                <li>Platform menggunakan sistem Dompet Digital terpusat untuk: (1) menampung saldo Top-Up prabayar untuk
                    pemotongan biaya sistem, dan (2) menampung dana hasil penjualan dari Payment Gateway.
                </li>
                <li>Pendaftaran akun dan penggunaan dasar aplikasi tidak dikenakan biaya langganan bulanan.</li>
                <li>Setiap transaksi penjualan yang berstatus sukses/selesai akan dikenakan biaya sistem sebesar <strong
                        class="text-slate-800 dark:text-white">Rp {{ $trxFee }}</strong> yang dipotong otomatis dari
                    Saldo Wallet Pengguna.
                </li>
            </ul>

            <h4 class="font-heading font-bold text-slate-900 dark:text-white text-base">4. PENARIKAN DANA (WITHDRAWAL) &
                PENGEMBALIAN DANA</h4>
            <ul class="list-disc pl-5 space-y-1.5">
                <li>Saldo yang bersumber dari hasil penjualan (Payment Gateway) dapat ditarik oleh Pengguna ke rekening
                    bank yang didaftarkan.
                </li>
                <li>Proses penarikan saat ini dilakukan secara manual oleh tim admin.</li>
                <li>Saldo yang bersumber dari Top-Up prabayar bersifat non-refundable (tidak dapat ditarik atau
                    diuangkan kembali).
                </li>
            </ul>

            <h4 class="font-heading font-bold text-slate-900 dark:text-white text-base">5. HUKUM YANG BERLAKU</h4>
            <ul class="list-disc pl-5 space-y-1.5">
                <li>Syarat & Ketentuan ini diatur, ditafsirkan, dan tunduk sepenuhnya pada hukum negara Republik
                    Indonesia.
                </li>
            </ul>
        </div>
        <!-- Footer actions -->
        <div class="px-8 py-4 border-t border-slate-100 dark:border-slate-800 flex justify-end shrink-0">
            <button onclick="closeModal('tncModal')"
                    class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 text-white dark:bg-emerald-500 dark:hover:bg-emerald-600 dark:text-slate-950 font-bold py-2.5 px-6 rounded-xl transition-all shadow-sm text-sm">
                Saya Setuju
            </button>
        </div>
    </div>
</div>

<!-- Modal Refund -->
<div id="refundModal" class="custom-modal fixed inset-0 z-[100] flex items-center justify-center p-4">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('refundModal')"></div>
    <!-- Box content -->
    <div
        class="custom-modal-content relative bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2.5rem] w-full max-w-2xl max-h-[85vh] overflow-hidden flex flex-col shadow-2xl transition-colors">
        <!-- Header -->
        <div
            class="px-8 py-5 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center shrink-0">
            <h3 class="font-heading font-extrabold text-slate-900 dark:text-white text-lg flex items-center gap-2">
                <i class="ph-fill ph-wallet text-emerald-600 dark:text-emerald-400"></i> Kebijakan Pengembalian Dana
            </h3>
            <button onclick="closeModal('refundModal')"
                    class="p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-white rounded-full transition-colors">
                <i class="ph-bold ph-x text-lg"></i></button>
        </div>
        <!-- Scrollable Body -->
        <div
            class="p-8 overflow-y-auto text-slate-600 dark:text-slate-300 text-sm leading-relaxed space-y-5 font-light">
            <p>Sebagai bagian dari kepatuhan operasional kami di <strong
                    class="text-slate-900 dark:text-white font-bold">Pakaiapp</strong>, berikut adalah kebijakan resmi
                terkait pengembalian dana (refund):</p>

            <h4 class="font-heading font-bold text-slate-900 dark:text-white text-base">1. FINALITAS TRANSAKSI
                TOP-UP</h4>
            <p>Seluruh transaksi pengisian ulang saldo (Top-Up) yang telah berhasil diverifikasi oleh sistem bersifat
                final dan mengikat.</p>

            <h4 class="font-heading font-bold text-slate-900 dark:text-white text-base">2. PEMISAHAN JENIS SALDO &
                PENARIKAN</h4>
            <p><strong class="text-slate-800 dark:text-white font-semibold">Saldo Top-Up prabayar bersifat mutlak
                    non-refundable</strong>. Namun, <strong class="text-slate-800 dark:text-white font-semibold">Saldo
                    Pendapatan</strong> dari hasil transaksi penjualan online dapat ditarik secara manual ke rekening
                bank pemilik usaha yang telah diverifikasi.</p>

            <h4 class="font-heading font-bold text-slate-900 dark:text-white text-base">3. SALDO ABADI</h4>
            <p>Saldo top-up pada wallet Pakaiapp bersifat abadi dan tidak memiliki masa kedaluwarsa.</p>

            <h4 class="font-heading font-bold text-slate-900 dark:text-white text-base">4. HUBUNGI KAMI</h4>
            <p>Hubungi kami melalui WhatsApp di <strong class="text-emerald-600 dark:text-emerald-400 font-semibold">085172441544</strong>
                atau email ke <strong
                    class="text-slate-800 dark:text-white font-semibold">support@pakaiapp.online</strong>.</p>
        </div>
        <!-- Footer actions -->
        <div class="px-8 py-4 border-t border-slate-100 dark:border-slate-800 flex justify-end shrink-0">
            <button onclick="closeModal('refundModal')"
                    class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 text-white dark:bg-emerald-500 dark:hover:bg-emerald-600 dark:text-slate-950 font-bold py-2.5 px-6 rounded-xl transition-all shadow-sm text-sm">
                Saya Mengerti
            </button>
        </div>
    </div>
</div>


<!-- Modal controller vanilla JS -->
<script>
    function openModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }
    }

    function toggleAccordion(btn) {
        const content = btn.nextElementSibling;
        const icon = btn.querySelector('i');

        if (content.style.maxHeight && content.style.maxHeight !== '0px') {
            content.style.maxHeight = '0px';
            icon.classList.remove('rotate-180');
        } else {
            // close other accordions first
            document.querySelectorAll('.faq-content').forEach(c => c.style.maxHeight = '0px');
            document.querySelectorAll('button i').forEach(i => i.classList.remove('rotate-180'));

            content.style.maxHeight = content.scrollHeight + 'px';
            icon.classList.add('rotate-180');
        }
    }
</script>

@if(config('midtrans.enabled'))
    @if(config('midtrans.is_production'))
        <script src="https://app.midtrans.com/snap/snap.js"
                data-client-key="{{ config('midtrans.client_key') }}"></script>
    @else
        <script src="https://app.sandbox.midtrans.com/snap/snap.js"
                data-client-key="{{ config('midtrans.client_key') }}"></script>
    @endif
@endif

<!-- Configuration needed by welcome.js -->


<livewire:central.ai-floating-chat/>

<script>
    window.PAKAIAAPP_CONFIG = {
        trxFee: {{ $trxFee }},
        cappingLimit: {{ $cappingLimit }},
        cappingLimitFormatted: "{{ $cappingLimitFormatted }}",
        cappingLimitShort: "{{ $cappingLimitShort }}"
    };
</script>
@vite(["resources/js/welcome.js"])
</body>
</html>
