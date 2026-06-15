@php use App\Models\GlobalSetting; @endphp
<!DOCTYPE html>
<html lang="id" class="scroll-smooth dark" id="html-root">
<head>
    <!-- Prevent Theme Flicker -->
    <script>
        if (localStorage.getItem('theme') === 'light' || (!('theme' in localStorage) && !window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.remove('dark');
        } else {
            document.documentElement.classList.add('dark');
        }
    </script>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $trxFee = GlobalSetting::where('key', 'default_trx_fee')->first()?->value ?? 300;
        $cappingLimit = GlobalSetting::where('key', 'default_capping_limit')->first()?->value ?? 150000;
        $cappingLimitFormatted = number_format($cappingLimit, 0, ',', '.');
        $cappingLimitShort = ($cappingLimit / 1000) . 'rb';
    @endphp

    <title>Pakaiapp (Pakai App Online) - Kasir Web UMKM Tanpa Biaya Langganan</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">
    
    <meta name="title" content="Pakaiapp (Pakai App Online) - Aplikasi Kasir Web UMKM Tanpa Biaya Bulanan">
    <meta name="description" content="Tinggalkan biaya langganan! Pakaiapp adalah Super App SaaS (POS) berbasis web cloud untuk UMKM. Cuma bayar Rp {{ $trxFee }} per transaksi sukses, dan otomatis GRATIS setelah tagihan menyentuh Rp {{ $cappingLimitShort }}/bulan!">
    <meta name="keywords" content="pakai app, pakai app online, pakaiapp online, aplikasi kasir web, kasir pintar, POS F&B, kasir UMKM, aplikasi kasir tanpa langganan, kasir cafe, sistem kasir retail, pakaiapp, ngopikode, aplikasi kasir medan">
    <meta name="author" content="PT Sinergi Kode Kreatif">
    <meta name="robots" content="index, follow">
    <meta name="language" content="Indonesian">
    <link rel="canonical" href="https://www.pakaiapp.online/">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://www.pakaiapp.online/">
    <meta property="og:title" content="Pakaiapp - Kasir Web Bayar Suka-Suka">
    <meta property="og:description" content="Kasir sepi = Gratis. Kasir ramai = Otomatis Premium (Gratis Tanpa Batas) setelah Rp{{ $cappingLimitFormatted }}/bulan tercapai!">
    <meta property="og:image" content="{{ asset('images/og-banner.png') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://www.pakaiapp.online/">
    <meta property="twitter:title" content="Pakaiapp - Kasir Web Bayar Suka-Suka">
    <meta property="twitter:description" content="Tinggalkan biaya langganan bulanan. Pindah ke Pakaiapp sekarang dan nikmati fitur kasir enterprise dengan harga UMKM.">
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

    <!-- Google Fonts & Tailwind -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    

        @livewireStyles
    
    @vite(['resources/css/welcome.css'])
    <style>
        /* Sustainability Platform Eco Theme */
        :root {
            --color-primary: #228B22;
            --color-secondary: #2E7D32;
            --color-accent: #8B4513;
            --color-sky: #87CEEB;
            --color-earth: #A0522D;
            --color-leaf: #4CAF50;
            --color-sun: #FFB300;
            --color-bg: #FFFFFF;
            --color-bg-alt: #F8FAFC;
            --color-bg-card: #FFFFFF;
            --color-bg-dark: #1B4332;
            --color-text: #0F172A;
            --color-text-muted: #64748B;
            --color-text-subtle: #84A98C;
            --color-border: #E2E8F0;
            --gradient-nature: linear-gradient(135deg, #228B22 0%, #4CAF50 100%);
            --font-heading: 'Plus Jakarta Sans', sans-serif;
        }

        .dark {
            --color-bg: #0B120F;
            --color-bg-alt: #111A15;
            --color-bg-card: #151F19;
            --color-bg-dark: #070A08;
            --color-text: #E8EAE6;
            --color-text-muted: #8BA89D;
            --color-text-subtle: #5C7A6E;
            --color-border: #213329;
        }

        .eco-theme {
            background-color: var(--color-bg);
            color: var(--color-text);
        }
        
        .eco-theme .gradient-text {
            background: var(--gradient-nature);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .eco-theme .badge-eco {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            background: rgba(34, 139, 34, 0.1);
            color: var(--color-primary);
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .eco-theme .btn-primary {
            background: var(--gradient-nature);
            color: #fff;
            font-weight: 600;
            padding: 14px 32px;
            border-radius: 50px;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 4px 15px rgba(34, 139, 34, 0.2);
        }

        .eco-theme .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(34, 139, 34, 0.3);
        }

        .eco-theme .btn-secondary {
            background: transparent;
            color: var(--color-primary);
            font-weight: 600;
            padding: 14px 32px;
            border-radius: 50px;
            border: 2px solid var(--color-primary);
            transition: all 0.3s ease;
        }

        .eco-theme .btn-secondary:hover {
            background: var(--color-primary);
            color: #fff;
        }

        .eco-theme .calculator-preview {
            background: var(--color-bg-card);
            border-radius: 24px;
            border: 1px solid var(--color-border);
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.08);
        }

        .eco-theme .organic-card {
            background: var(--color-bg-card);
            border-radius: 24px;
            border: 1px solid var(--color-border);
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }
        
        .eco-theme .organic-card:hover {
            box-shadow: 0 20px 40px rgba(34, 139, 34, 0.1);
            border-color: var(--color-primary);
        }
    </style>

</head>
<body class="eco-theme font-sans antialiased overflow-x-hidden">

<!-- ============================================
     NAVBAR ECO STYLE
============================================ -->
<nav class="fixed top-0 left-0 right-0 z-50 bg-white/80 dark:bg-slate-950/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="/" class="flex items-center gap-2">
                <div class="w-10 h-10 rounded-full bg-emerald-600 flex items-center justify-center">
                    <i class="ph-bold ph-storefront text-white text-xl"></i>
                </div>
                <span class="text-xl font-bold text-slate-900 dark:text-white">pakaiapp</span>
            </a>
            
            <div class="hidden md:flex items-center gap-6 sm:gap-8">
                <a href="#cara-daftar" class="text-slate-600 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors cursor-pointer font-medium">Cara Kerja</a>
                <a href="#fitur" class="text-slate-600 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors cursor-pointer font-medium">Fitur</a>
                <a href="#harga" class="text-slate-600 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors cursor-pointer font-medium">Harga</a>
                <a href="#faq" class="text-slate-600 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors cursor-pointer font-medium">FAQ</a>
            </div>
            
            <div class="hidden md:flex items-center gap-4">
                <button id="theme-toggle" class="p-2 text-slate-500 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors cursor-pointer">
                    <i class="ph-bold ph-sun text-xl" id="theme-icon"></i>
                </button>
                <a href="/login" class="text-slate-600 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 font-medium transition-colors cursor-pointer">Masuk</a>
                <a href="/register" class="bg-emerald-600 hover:bg-emerald-700 text-white dark:bg-emerald-500 dark:hover:bg-emerald-600 dark:text-slate-950 text-sm font-medium py-2.5 px-5 rounded-xl transition-all shadow-sm hover:shadow-md">Daftar Gratis</a>
            </div>
            
            <button class="md:hidden p-2 text-slate-900 dark:text-white cursor-pointer">
                <i class="ph-bold ph-list text-2xl"></i>
            </button>
        </div>
    </div>
</nav>

<!-- ============================================
     HERO SECTION
============================================ -->
<section class="min-h-screen flex items-center pt-28 pb-16 relative overflow-hidden">
    <!-- Ambient backgrounds (Glow) -->
    <div class="absolute top-1/4 left-10 w-[400px] h-[400px] bg-emerald-500/10 dark:bg-emerald-500/5 rounded-full blur-[100px] -z-10"></div>
    <div class="absolute bottom-10 right-10 w-[500px] h-[500px] bg-indigo-500/10 dark:bg-indigo-500/5 rounded-full blur-[120px] -z-10"></div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
        <div class="grid lg:grid-cols-12 gap-12 items-center">
            <!-- Left Info -->
            <div class="lg:col-span-7 z-10 text-center lg:text-left">

                <h1 class="font-heading text-4xl sm:text-5xl lg:text-[3.8rem] font-extrabold tracking-tight leading-[1.08] text-slate-900 dark:text-white">
                    Bebaskan Bisnismu dari<br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-indigo-600 dark:from-emerald-400 dark:to-indigo-400">Beban Langganan Kasir</span>
                </h1>
                
                <p class="text-base sm:text-lg text-slate-600 dark:text-slate-300 mt-6 leading-relaxed max-w-2xl mx-auto lg:mx-0 font-light">
                    Sepi? Gratis. Ramai? Bayar Suka-suka. Kelola penjualan, menu, laporan, dan QRIS dari HP, tablet, atau laptop. Cuma bayar <strong class="text-slate-900 dark:text-white">Rp {{ $trxFee }}</strong> per transaksi sukses, otomatis <strong class="text-emerald-600 dark:text-emerald-400">GRATIS sepenuhnya</strong> setelah mencapai Rp {{ $cappingLimitFormatted }}/bulan!
                </p>

                <!-- Special Offer Info -->
                <div class="text-amber-600 dark:text-amber-400 font-bold text-sm mt-4 flex items-center justify-center lg:justify-start gap-1.5">
                    <i class="ph-fill ph-gift text-lg"></i> Spesial Hari Ini: Gratis Kuota 100 Transaksi Pertama!
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-4 mt-8 justify-center lg:justify-start">
                    <a href="/register" id="cta-hero-register" class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 text-white dark:bg-emerald-500 dark:hover:bg-emerald-600 dark:text-slate-950 font-bold py-4 px-8 rounded-full shadow-lg transition-all hover:scale-105 flex items-center justify-center gap-2 text-lg">
                        <i class="ph-bold ph-storefront"></i> Buat Toko Sekarang — Gratis
                    </a>
                    <a href="#cara-daftar" class="w-full sm:w-auto border border-slate-300 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-900 text-slate-700 dark:text-slate-300 font-bold py-4 px-8 rounded-full transition-colors flex items-center justify-center gap-2 text-lg">
                        <i class="ph-bold ph-play-circle"></i> Lihat Cara Kerja
                    </a>
                </div>
            </div>

            <!-- Right Visual Mockup -->
            <div class="lg:col-span-5 relative hidden lg:block">
                <!-- Outer Glass Panel -->
                <div class="relative w-full aspect-[4/5] bg-white dark:bg-slate-900 rounded-[3rem] border border-slate-200 dark:border-slate-800 p-8 shadow-2xl overflow-hidden flex flex-col justify-between transition-colors">
                    <div class="absolute inset-0 bg-gradient-to-tr from-emerald-500/5 to-transparent pointer-events-none"></div>
                    
                    <div class="flex justify-between items-center border-b border-slate-200 dark:border-slate-800 pb-4">
                        <div class="flex gap-2">
                            <div class="w-3 h-3 rounded-full bg-slate-300 dark:bg-slate-700"></div>
                            <div class="w-3 h-3 rounded-full bg-slate-300 dark:bg-slate-700"></div>
                            <div class="w-3 h-3 rounded-full bg-slate-300 dark:bg-slate-700"></div>
                        </div>
                        <div class="text-xs font-mono text-slate-400 dark:text-slate-500">POS Live Dashboard</div>
                    </div>

                    <!-- Inner Mockup Data -->
                    <div class="my-auto py-4">
                        <div class="text-sm text-slate-500 dark:text-slate-400 mb-1">Total Penjualan Toko</div>
                        <div class="font-heading text-4xl font-extrabold text-slate-900 dark:text-white mb-6">Rp 4.250<span class="text-emerald-500">.000</span></div>
                        
                        <div class="space-y-3">
                            <div class="h-14 w-full bg-slate-50 dark:bg-slate-950 rounded-2xl border border-slate-200/50 dark:border-slate-800/50 flex items-center px-4">
                                <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-950 flex items-center justify-center mr-3 shrink-0"><i class="ph-fill ph-coffee text-emerald-600 dark:text-emerald-400 text-lg"></i></div>
                                <div class="flex flex-col gap-1">
                                    <div class="h-3.5 w-24 bg-slate-300 dark:bg-slate-700 rounded-full"></div>
                                    <div class="h-2 w-16 bg-slate-200 dark:bg-slate-800 rounded-full"></div>
                                </div>
                                <div class="ml-auto flex flex-col items-end gap-1">
                                    <div class="h-3 w-12 bg-slate-300 dark:bg-slate-700 rounded-full"></div>
                                    <span class="text-[0.65rem] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/50 px-2 py-0.5 rounded">Sukses</span>
                                </div>
                            </div>
                            <div class="h-14 w-full bg-slate-50 dark:bg-slate-950 rounded-2xl border border-slate-200/50 dark:border-slate-800/50 flex items-center px-4">
                                <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-950 flex items-center justify-center mr-3 shrink-0"><i class="ph-fill ph-bowl-food text-indigo-600 dark:text-indigo-400 text-lg"></i></div>
                                <div class="flex flex-col gap-1">
                                    <div class="h-3.5 w-32 bg-slate-300 dark:bg-slate-700 rounded-full"></div>
                                    <div class="h-2 w-20 bg-slate-200 dark:bg-slate-800 rounded-full"></div>
                                </div>
                                <div class="ml-auto flex flex-col items-end gap-1">
                                    <div class="h-3 w-14 bg-slate-300 dark:bg-slate-700 rounded-full"></div>
                                    <span class="text-[0.65rem] font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/50 px-2 py-0.5 rounded">Meja 04</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Analytics Stats inside card -->
                    <div class="flex items-center justify-between border-t border-slate-200 dark:border-slate-800 pt-4 mt-2">
                        <div class="text-center w-1/2 border-r border-slate-200 dark:border-slate-800">
                            <span class="text-[0.65rem] uppercase font-bold text-slate-400 block">Transaksi</span>
                            <span class="text-base font-bold text-slate-800 dark:text-slate-100">142</span>
                        </div>
                        <div class="text-center w-1/2">
                            <span class="text-[0.65rem] uppercase font-bold text-slate-400 block">Capping Limit</span>
                            <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/50 px-2 py-1 rounded inline-block mt-0.5">Rp {{ $cappingLimitShort }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
     STATS PANEL
============================================ -->
<section class="py-12 bg-white dark:bg-slate-900 border-y border-slate-200 dark:border-slate-800 transition-colors">
    <div class="max-w-5xl mx-auto px-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div class="flex flex-col items-center">
                <span class="font-heading font-extrabold text-3xl sm:text-4xl text-slate-900 dark:text-white">15K+</span>
                <span class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">Toko UMKM Aktif</span>
            </div>
            <div class="flex flex-col items-center">
                <span class="font-heading font-extrabold text-3xl sm:text-4xl text-slate-900 dark:text-white">1.2M+</span>
                <span class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">Transaksi Sukses</span>
            </div>
            <div class="flex flex-col items-center">
                <span class="font-heading font-extrabold text-3xl sm:text-4xl text-emerald-600 dark:text-emerald-400">Rp 0</span>
                <span class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">Biaya Pendaftaran</span>
            </div>
            <div class="flex flex-col items-center">
                <span class="font-heading font-extrabold text-3xl sm:text-4xl text-indigo-600 dark:text-indigo-400">2 Menit</span>
                <span class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">Siap Mulai Jualan</span>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
     HOW IT WORKS (CARA KERJA)
============================================ -->
<section id="cara-daftar" class="py-24 relative">
    <div class="max-w-5xl mx-auto px-4">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <div class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-indigo-50 dark:bg-indigo-950/50 border border-indigo-200/50 dark:border-indigo-800/30 text-indigo-700 dark:text-indigo-400 text-xs font-semibold uppercase tracking-wider mb-3">
                <i class="ph-fill ph-lightning"></i> Cara Kerja
            </div>
            <h2 class="font-heading text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white">Aplikasi Kasir Tanpa Pasang: <span class="text-emerald-600 dark:text-emerald-400">3 Langkah</span></h2>
            <p class="text-slate-500 dark:text-slate-400 mt-4 leading-relaxed font-light">Aplikasi kasir berbasis web cloud sepenuhnya. Tidak perlu unduh aplikasi dari App Store, tidak perlu ahli IT. Buka browser, daftar, langsung bisa terima pesanan.</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <!-- Step 1 -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-8 rounded-[2rem] shadow-sm relative overflow-hidden group hover:border-emerald-500/50 dark:hover:border-emerald-400/50 transition-all duration-300">
                <div class="absolute -right-4 -top-4 w-20 h-20 bg-emerald-50 dark:bg-emerald-950/20 rounded-full flex items-center justify-center font-heading text-5xl font-extrabold text-emerald-200 dark:text-emerald-900/50 group-hover:scale-110 transition-transform">1</div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-6">
                    <i class="ph-bold ph-storefront text-2xl"></i>
                </div>
                <h3 class="font-heading text-xl font-bold text-slate-900 dark:text-white mb-3">Daftar & Buat Toko</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed mb-6 font-light">Klik "Daftar Gratis", isi nama tokomu, email, dan nomor WhatsApp. Verifikasi OTP dari email secara instan. Workspace kasirmu langsung siap.</p>
                <span class="inline-flex items-center gap-1 text-xs font-semibold text-slate-400 bg-slate-100 dark:bg-slate-800 dark:text-slate-400 px-3 py-1 rounded-full"><i class="ph ph-clock"></i> ~2 Menit</span>
            </div>

            <!-- Step 2 -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-8 rounded-[2rem] shadow-sm relative overflow-hidden group hover:border-indigo-500/50 dark:hover:border-indigo-400/50 transition-all duration-300">
                <div class="absolute -right-4 -top-4 w-20 h-20 bg-indigo-50 dark:bg-indigo-950/20 rounded-full flex items-center justify-center font-heading text-5xl font-extrabold text-indigo-200 dark:text-indigo-900/50 group-hover:scale-110 transition-transform">2</div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center mb-6">
                    <i class="ph-bold ph-list-plus text-2xl"></i>
                </div>
                <h3 class="font-heading text-xl font-bold text-slate-900 dark:text-white mb-3">Input Menu & Produk</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed mb-6 font-light">Masukkan produk atau menu Anda lengkap dengan harga, kategori, dan foto. Pengguna juga bisa menambahkan variasi/addon seperti ukuran rasa.</p>
                <span class="inline-flex items-center gap-1 text-xs font-semibold text-slate-400 bg-slate-100 dark:bg-slate-800 dark:text-slate-400 px-3 py-1 rounded-full"><i class="ph ph-clock"></i> ~10 Menit</span>
            </div>

            <!-- Step 3 -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-8 rounded-[2rem] shadow-sm relative overflow-hidden group hover:border-emerald-500/50 dark:hover:border-emerald-400/50 transition-all duration-300">
                <div class="absolute -right-4 -top-4 w-20 h-20 bg-emerald-50 dark:bg-emerald-950/20 rounded-full flex items-center justify-center font-heading text-5xl font-extrabold text-emerald-200 dark:text-emerald-900/50 group-hover:scale-110 transition-transform">3</div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-6">
                    <i class="ph-bold ph-paper-plane text-2xl"></i>
                </div>
                <h3 class="font-heading text-xl font-bold text-slate-900 dark:text-white mb-3">Langsung Jualan</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed mb-6 font-light">Sistem kasir siap! Terima pembayaran staf, atau cetak kode QR meja agar pelanggan bisa pesan & bayar mandiri. Dana masuk langsung ke tokomu.</p>
                <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/30 px-3 py-1 rounded-full"><i class="ph ph-check-circle"></i> Siap Jualan</span>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
     FEATURES (8 PILLARS)
============================================ -->
<section id="fitur" class="py-24 bg-white dark:bg-slate-900 transition-colors">
    <div class="max-w-5xl mx-auto px-4">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <div class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200/50 dark:border-emerald-800/30 text-emerald-700 dark:text-emerald-400 text-xs font-semibold uppercase tracking-wider mb-3">
                <i class="ph-fill ph-circles-four"></i> Fitur Lengkap
            </div>
            <h2 class="font-heading text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white">Ekosistem Super Lengkap <span class="text-emerald-600 dark:text-emerald-400">Kasir POS</span></h2>
            <p class="text-slate-500 dark:text-slate-400 mt-4 leading-relaxed font-light">Kelola operasional harian hingga laporan pembukuan usaha dalam satu aplikasi kasir berbasis web cloud tanpa langganan.</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- 1. Kasir Web -->
            <div class="border border-slate-200 dark:border-slate-800 hover:border-emerald-500/50 dark:hover:border-emerald-400/50 p-6 rounded-3xl transition-all hover:shadow-sm bg-zinc-50 dark:bg-slate-950/40 relative">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-4">
                    <i class="ph-bold ph-receipt text-xl"></i>
                </div>
                <h3 class="font-heading font-bold text-lg text-slate-900 dark:text-white">Kasir Web Real-Time</h3>
                <p class="text-slate-500 dark:text-slate-400 text-xs mt-2 leading-relaxed font-light">Proses transaksi penjualan dari browser HP, tablet, atau PC. Data otomatis sinkron di cloud secara bersamaan.</p>
                <span class="absolute top-4 right-4 text-[0.6rem] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-100/60 dark:bg-emerald-950/50 px-2 py-0.5 rounded-full uppercase"><i class="ph-fill ph-star text-[8px] mr-1"></i>Utama</span>
            </div>

            <!-- 2. QR Self-Order -->
            <div class="border border-slate-200 dark:border-slate-800 hover:border-emerald-500/50 dark:hover:border-emerald-400/50 p-6 rounded-3xl transition-all hover:shadow-sm bg-zinc-50 dark:bg-slate-950/40">
                <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center mb-4">
                    <i class="ph-bold ph-qr-code text-xl"></i>
                </div>
                <h3 class="font-heading font-bold text-lg text-slate-900 dark:text-white">QR Self-Order (Meja)</h3>
                <p class="text-slate-500 dark:text-slate-400 text-xs mt-2 leading-relaxed font-light">Pelanggan scan QR di meja, pilih menu, bayar langsung. Pesanan terkirim otomatis ke layar kasir.</p>
            </div>

            <!-- 3. QRIS & E-Wallet -->
            <div class="border border-slate-200 dark:border-slate-800 hover:border-emerald-500/50 dark:hover:border-emerald-400/50 p-6 rounded-3xl transition-all hover:shadow-sm bg-zinc-50 dark:bg-slate-950/40">
                <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-950 flex items-center justify-center mb-4">
                    <i class="ph-bold ph-wallet text-xl"></i>
                </div>
                <h3 class="font-heading font-bold text-lg text-slate-900 dark:text-white">Terima QRIS & E-Wallet</h3>
                <p class="text-slate-500 dark:text-slate-400 text-xs mt-2 leading-relaxed font-light">GoPay, OVO, ShopeePay, Dana, LinkAja, BCA, hingga Mandiri VA terhubung otomatis dengan aman.</p>
            </div>

            <!-- 4. Laporan & Analitik -->
            <div class="border border-slate-200 dark:border-slate-800 hover:border-emerald-500/50 dark:hover:border-emerald-400/50 p-6 rounded-3xl transition-all hover:shadow-sm bg-zinc-50 dark:bg-slate-950/40">
                <div class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center mb-4">
                    <i class="ph-bold ph-chart-bar text-xl"></i>
                </div>
                <h3 class="font-heading font-bold text-lg text-slate-900 dark:text-white">Laporan & Analitik</h3>
                <p class="text-slate-500 dark:text-slate-400 text-xs mt-2 leading-relaxed font-light">Pantau omset harian, produk terlaris, jam teramai toko, dan kinerja kasir dalam satu dasbor.</p>
            </div>

            <!-- 5. Multi-Staf & Role -->
            <div class="border border-slate-200 dark:border-slate-800 hover:border-emerald-500/50 dark:hover:border-emerald-400/50 p-6 rounded-3xl transition-all hover:shadow-sm bg-zinc-50 dark:bg-slate-950/40">
                <div class="w-10 h-10 rounded-xl bg-teal-100 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400 flex items-center justify-center mb-4">
                    <i class="ph-bold ph-users text-xl"></i>
                </div>
                <h3 class="font-heading font-bold text-lg text-slate-900 dark:text-white">Multi-Staf & Role</h3>
                <p class="text-slate-500 dark:text-slate-400 text-xs mt-2 leading-relaxed font-light">Tambahkan akun manajer, kasir, atau dapur dengan batas akses data keuangan yang terlindungi.</p>
            </div>

            <!-- 6. Manajemen Stok -->
            <div class="border border-slate-200 dark:border-slate-800 hover:border-emerald-500/50 dark:hover:border-emerald-400/50 p-6 rounded-3xl transition-all hover:shadow-sm bg-zinc-50 dark:bg-slate-950/40">
                <div class="w-10 h-10 rounded-xl bg-sky-100 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 flex items-center justify-center mb-4">
                    <i class="ph-bold ph-package text-xl"></i>
                </div>
                <h3 class="font-heading font-bold text-lg text-slate-900 dark:text-white">Manajemen Stok</h3>
                <p class="text-slate-500 dark:text-slate-400 text-xs mt-2 leading-relaxed font-light">Lacak ketersediaan stok bahan baku produk serta peringatan notifikasi otomatis saat stok menipis.</p>
            </div>

            <!-- 7. Cetak Struk & KDS -->
            <div class="border border-slate-200 dark:border-slate-800 hover:border-emerald-500/50 dark:hover:border-emerald-400/50 p-6 rounded-3xl transition-all hover:shadow-sm bg-zinc-50 dark:bg-slate-950/40">
                <div class="w-10 h-10 rounded-xl bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center mb-4">
                    <i class="ph-bold ph-printer text-xl"></i>
                </div>
                <h3 class="font-heading font-bold text-lg text-slate-900 dark:text-white">Cetak Struk & KDS</h3>
                <p class="text-slate-500 dark:text-slate-400 text-xs mt-2 leading-relaxed font-light">Hubungkan printer thermal via bluetooth/USB untuk cetak struk, dan tampilkan pesanan di layar dapur (KDS).</p>
            </div>

            <!-- 8. Varian & Modifier -->
            <div class="border border-slate-200 dark:border-slate-800 hover:border-emerald-500/50 dark:hover:border-emerald-400/50 p-6 rounded-3xl transition-all hover:shadow-sm bg-zinc-50 dark:bg-slate-950/40">
                <div class="w-10 h-10 rounded-xl bg-pink-100 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400 flex items-center justify-center mb-4">
                    <i class="ph-bold ph-list-bullets text-xl"></i>
                </div>
                <h3 class="font-heading font-bold text-lg text-slate-900 dark:text-white">Varian & Modifier</h3>
                <p class="text-slate-500 dark:text-slate-400 text-xs mt-2 leading-relaxed font-light">Atur pilihan rasa, level pedas, topping ekstra, atau ukuran (S/M/L) dengan penyesuaian harga otomatis.</p>
            </div>

            <!-- 9. PWA Offline Mode -->
            <div class="border border-slate-200 dark:border-slate-800 hover:border-emerald-500/50 dark:hover:border-emerald-400/50 p-6 rounded-3xl transition-all hover:shadow-sm bg-zinc-50 dark:bg-slate-950/40">
                <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-4">
                    <i class="ph-bold ph-cell-signal-slash text-xl"></i>
                </div>
                <h3 class="font-heading font-bold text-lg text-slate-900 dark:text-white">PWA — Bisa Offline</h3>
                <p class="text-slate-500 dark:text-slate-400 text-xs mt-2 leading-relaxed font-light">Pasang pintasan kasir ke layar HP utama. Transaksi tetap lancar tercatat meskipun koneksi internet terputus.</p>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
     PRICING & CALCULATOR
============================================ -->
<section id="harga" class="py-24 relative overflow-hidden transition-colors">
    <div class="max-w-5xl mx-auto px-4">
        <!-- Section Header -->
        <div class="text-center max-w-2xl mx-auto mb-16">
            <div class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200/50 dark:border-emerald-800/30 text-emerald-700 dark:text-emerald-400 text-xs font-semibold uppercase tracking-wider mb-3">
                <i class="ph-fill ph-tag"></i> Skema Adil
            </div>
            <h2 class="font-heading text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white">Bayar Hanya <span class="text-emerald-600 dark:text-emerald-400">Saat Ada Penjualan</span></h2>
            <p class="text-slate-500 dark:text-slate-400 mt-4 leading-relaxed font-light">Kami meniadakan biaya langganan bulanan. Anda hanya ditarik biaya kecil per transaksi sukses. Sepi = Rp 0.</p>
        </div>

        <div class="grid lg:grid-cols-12 gap-8 items-start">
            
            <!-- Calculator Card (DO NOT change input and display element IDs) -->
            <div class="lg:col-span-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2.5rem] p-8 sm:p-10 shadow-lg relative transition-colors">
                <div class="absolute top-8 right-8">
                    <i class="ph-duotone ph-calculator text-4xl text-emerald-500/20 dark:text-emerald-400/20"></i>
                </div>
                <h3 class="font-heading text-2xl font-bold text-slate-900 dark:text-white mb-6">Simulasi Biaya Bulanan</h3>
                
                <div class="mb-8">
                    <div class="flex justify-between items-end mb-4">
                        <span class="text-sm font-semibold text-slate-500 dark:text-slate-400">Volume Transaksi / Bulan</span>
                        <span class="font-heading font-extrabold text-2xl text-slate-900 dark:text-white">
                            <span id="trxDisplay">0</span> <span class="text-xs font-normal text-slate-400 dark:text-slate-500 uppercase ml-1">Transaksi</span>
                        </span>
                    </div>
                    <!-- Range Slider -->
                    <input type="range" id="trxSlider" min="0" max="2000" step="50" value="0" 
                        class="w-full h-2 bg-slate-100 dark:bg-slate-950 rounded-lg appearance-none cursor-pointer outline-none transition-all [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:w-6 [&::-webkit-slider-thumb]:h-6 [&::-webkit-slider-thumb]:bg-emerald-600 dark:[&::-webkit-slider-thumb]:bg-emerald-500 [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:shadow-md">
                </div>

                <div class="p-6 rounded-2xl bg-zinc-50 dark:bg-slate-950/60 border border-slate-200/50 dark:border-slate-800/50 text-center">
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-2">Total Biaya Pakaiapp Bulan Ini</div>
                    <!-- Displays "GRATIS!" or formatted cost -->
                    <div class="font-heading text-4xl sm:text-5xl font-extrabold text-emerald-600 dark:text-emerald-400 tracking-tight mb-2" id="costPakaiapp">GRATIS!</div>
                    <!-- Display breakdown -->
                    <div class="text-xs font-mono text-slate-500 dark:text-slate-400" id="costNote">Rp {{ $trxFee }} × 0 transaksi = Rp 0</div>
                    
                    <!-- Unlimited Capping Badge -->
                    <div id="unlimitedBadge" class="hidden mt-4 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 text-xs font-bold px-3 py-2 rounded-xl border border-emerald-200/50 dark:border-emerald-800/30 w-full animate-pulse justify-center items-center gap-1.5">
                        <i class="ph-fill ph-sparkles"></i> Unlimited — Sisa Bulan Gratis Sepenuhnya!
                    </div>
                </div>

                <div class="mt-6 p-4 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-light">
                    <strong class="text-slate-800 dark:text-white font-bold block mb-1">🎉 Cara Hitung:</strong>
                    Anda hanya membayar Rp {{ $trxFee }} per transaksi sukses. Apabila total tagihan Anda dalam sebulan sudah mencapai <strong class="text-emerald-600 dark:text-emerald-400 font-semibold">Rp {{ $cappingLimitFormatted }}</strong>, semua transaksi selanjutnya di bulan tersebut **GRATIS**!
                </div>
            </div>

            <!-- Comparison Table -->
            <div class="lg:col-span-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2.5rem] p-8 shadow-lg overflow-hidden transition-colors">
                <h3 class="font-heading text-2xl font-bold text-slate-900 dark:text-white mb-6">Perbandingan Biaya</h3>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 dark:border-slate-800 text-slate-400 font-bold uppercase tracking-wider">
                                <th class="py-4">Fitur / Biaya</th>
                                <th class="py-4 text-emerald-600 dark:text-emerald-400">Pakaiapp</th>
                                <th class="py-4">Kasir Lain</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-light text-slate-600 dark:text-slate-300">
                            <tr>
                                <td class="py-3.5 font-medium text-slate-800 dark:text-white">Biaya Pendaftaran</td>
                                <td class="py-3.5 text-emerald-600 dark:text-emerald-400 font-bold"><i class="ph-bold ph-check-circle mr-1"></i> Gratis</td>
                                <td class="py-3.5 text-red-500"><i class="ph-bold ph-x-circle mr-1"></i> Bayar</td>
                            </tr>
                            <tr>
                                <td class="py-3.5 font-medium text-slate-800 dark:text-white">Biaya Bulanan</td>
                                <td class="py-3.5 text-emerald-600 dark:text-emerald-400 font-bold"><i class="ph-bold ph-check-circle mr-1"></i> Tidak Ada</td>
                                <td class="py-3.5 text-red-500"><i class="ph-bold ph-x-circle mr-1"></i> Rp 150rb-500rb</td>
                            </tr>
                            <tr>
                                <td class="py-3.5 font-medium text-slate-800 dark:text-white">Biaya Per Transaksi</td>
                                <td class="py-3.5 text-emerald-600 dark:text-emerald-400 font-bold"><i class="ph-bold ph-check-circle mr-1"></i> Rp {{ $trxFee }}</td>
                                <td class="py-3.5 text-slate-400 font-medium">Tergantung plan</td>
                            </tr>
                            <tr>
                                <td class="py-3.5 font-medium text-slate-800 dark:text-white">Auto Unlimited Capping</td>
                                <td class="py-3.5 text-emerald-600 dark:text-emerald-400 font-bold"><i class="ph-bold ph-check-circle mr-1"></i> Ya, Rp {{ $cappingLimitShort }}</td>
                                <td class="py-3.5 text-red-500"><i class="ph-bold ph-x-circle mr-1"></i> Tidak Ada</td>
                            </tr>
                            <tr>
                                <td class="py-3.5 font-medium text-slate-800 dark:text-white">QRIS & E-Wallet</td>
                                <td class="py-3.5 text-emerald-600 dark:text-emerald-400 font-bold"><i class="ph-bold ph-check-circle mr-1"></i> Termasuk</td>
                                <td class="py-3.5 text-red-500"><i class="ph-bold ph-x-circle mr-1"></i> Biaya Ekstra</td>
                            </tr>
                            <tr>
                                <td class="py-3.5 font-medium text-slate-800 dark:text-white">QR Self-Order</td>
                                <td class="py-3.5 text-emerald-600 dark:text-emerald-400 font-bold"><i class="ph-bold ph-check-circle mr-1"></i> Termasuk</td>
                                <td class="py-3.5 text-red-500"><i class="ph-bold ph-x-circle mr-1"></i> Plan Premium</td>
                            </tr>
                            <tr>
                                <td class="py-3.5 font-medium text-slate-800 dark:text-white">Multi Staf & Role</td>
                                <td class="py-3.5 text-emerald-600 dark:text-emerald-400 font-bold"><i class="ph-bold ph-check-circle mr-1"></i> Termasuk</td>
                                <td class="py-3.5 text-red-500"><i class="ph-bold ph-x-circle mr-1"></i> Biaya Ekstra</td>
                            </tr>
                            <tr>
                                <td class="py-3.5 font-medium text-slate-800 dark:text-white">Kontrak Berlangganan</td>
                                <td class="py-3.5 text-emerald-600 dark:text-emerald-400 font-bold"><i class="ph-bold ph-check-circle mr-1"></i> Tidak Ada</td>
                                <td class="py-3.5 text-red-500"><i class="ph-bold ph-x-circle mr-1"></i> 1-12 Bulan</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
     TESTIMONIALS
============================================ -->
<section class="py-24 bg-zinc-50 dark:bg-slate-950 border-t border-slate-200 dark:border-slate-800 transition-colors">
    <div class="max-w-5xl mx-auto px-4">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <div class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200/50 dark:border-emerald-800/30 text-emerald-700 dark:text-emerald-400 text-xs font-semibold uppercase tracking-wider mb-3">
                <i class="ph-fill ph-chats-teardrop"></i> Testimoni
            </div>
            <h2 class="font-heading text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white">Kata Mereka yang <span class="text-emerald-600 dark:text-emerald-400">Sudah Pakai Pakaiapp</span></h2>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <!-- Testi 1: Mirayeni -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-8 rounded-3xl shadow-sm hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between transition-colors">
                <div>
                    <div class="flex text-amber-400 gap-1 mb-4">
                        <i class="ph-fill ph-star"></i><i class="ph-fill ph-star"></i><i class="ph-fill ph-star"></i><i class="ph-fill ph-star"></i><i class="ph-fill ph-star"></i>
                    </div>
                    <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed mb-6 font-light">"Alhamdulillah, sejak pakai sistem ini di Sama Roti Kukus (Seruput & Gigit), operasional cafe jadi jauh lebih mudah. Dari aplikasi kasir pintar (POS), manajemen stok, hingga urusan toko online ada di satu platform praktis. Pelayanan makin cepat dan transaksi lebih rapi. Cocok banget buat UMKM kuliner!"</p>
                </div>
                <div class="flex items-center gap-3 border-t border-slate-100 dark:border-slate-800 pt-4">
                    <div class="w-10 h-10 rounded-full bg-emerald-700 text-white flex items-center justify-center font-bold text-sm shrink-0">M</div>
                    <div>
                        <p class="font-bold text-slate-900 dark:text-white text-sm">Mirayeni</p>
                        <p class="text-slate-500 dark:text-slate-400 text-[0.7rem] uppercase tracking-wider font-semibold">Owner Sama Roti Kukus</p>
                    </div>
                </div>
            </div>

            <!-- Testi 2: Siti Hasanah -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-8 rounded-3xl shadow-sm hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between transition-colors">
                <div>
                    <div class="flex text-amber-400 gap-1 mb-4">
                        <i class="ph-fill ph-star"></i><i class="ph-fill ph-star"></i><i class="ph-fill ph-star"></i><i class="ph-fill ph-star"></i><i class="ph-fill ph-star"></i>
                    </div>
                    <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed mb-6 font-light">"Fitur QR self-order di meja sangat membantu. Pelanggan pesan sendiri, staf saya bisa fokus di dapur. Omset naik karena antrian berkurang drastis."</p>
                </div>
                <div class="flex items-center gap-3 border-t border-slate-100 dark:border-slate-800 pt-4">
                    <div class="w-10 h-10 rounded-full bg-indigo-900 text-white flex items-center justify-center font-bold text-sm shrink-0">SH</div>
                    <div>
                        <p class="font-bold text-slate-900 dark:text-white text-sm">Siti Hasanah</p>
                        <p class="text-slate-500 dark:text-slate-400 text-[0.7rem] uppercase tracking-wider font-semibold">Resto & Catering, Pekanbaru</p>
                    </div>
                </div>
            </div>

            <!-- Testi 3: Dika Pratama -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-8 rounded-3xl shadow-sm hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between transition-colors">
                <div>
                    <div class="flex text-amber-400 gap-1 mb-4">
                        <i class="ph-fill ph-star"></i><i class="ph-fill ph-star"></i><i class="ph-fill ph-star"></i><i class="ph-fill ph-star"></i><i class="ph-fill ph-star"></i>
                    </div>
                    <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed mb-6 font-light">"Dashboard laporannya simple dan langsung ngerti. Saya bisa cek omset dari HP kapan saja, bahkan pas saya lagi di rumah. Support-nya juga cepat respons via WA."</p>
                </div>
                <div class="flex items-center gap-3 border-t border-slate-100 dark:border-slate-800 pt-4">
                    <div class="w-10 h-10 rounded-full bg-amber-700 text-white flex items-center justify-center font-bold text-sm shrink-0">DP</div>
                    <div>
                        <p class="font-bold text-slate-900 dark:text-white text-sm">Dika Pratama</p>
                        <p class="text-slate-500 dark:text-slate-400 text-[0.7rem] uppercase tracking-wider font-semibold">Toko Retail Sembako, Binjai</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
     FAQ (ACCORDION)
============================================ -->
<section id="faq" class="py-24 bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 transition-colors">
    <div class="max-w-4xl mx-auto px-4">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <div class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200/50 dark:border-emerald-800/30 text-emerald-700 dark:text-emerald-400 text-xs font-semibold uppercase tracking-wider mb-3">
                <i class="ph-fill ph-question"></i> FAQ
            </div>
            <h2 class="font-heading text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white">Pertanyaan Umum <span class="text-emerald-600 dark:text-emerald-400">Terbanyak</span></h2>
        </div>

        <div class="space-y-4 max-w-3xl mx-auto">
            <!-- FAQ 1 -->
            <div class="border border-slate-200 dark:border-slate-800 bg-zinc-50 dark:bg-slate-950 rounded-2xl overflow-hidden transition-colors duration-300">
                <button class="w-full text-left px-6 py-5 flex justify-between items-center focus:outline-none hover:bg-slate-100/50 dark:hover:bg-slate-900/50 transition-colors" onclick="toggleAccordion(this)">
                    <span class="font-bold text-slate-900 dark:text-white text-base">Apakah benar-benar gratis untuk daftar?</span>
                    <i class="ph ph-caret-down text-slate-400 transition-transform duration-300"></i>
                </button>
                <div class="faq-content max-h-0 overflow-hidden transition-all duration-300">
                    <p class="px-6 pb-6 text-slate-500 dark:text-slate-400 text-sm leading-relaxed font-light">Ya, pendaftaran 100% gratis dan tidak perlu kartu kredit. Anda hanya akan dikenakan biaya Rp {{ $trxFee }} per transaksi sukses yang terjadi. Jika toko sepi atau tidak ada transaksi, tidak ada biaya sama sekali.</p>
                </div>
            </div>

            <!-- FAQ 2 -->
            <div class="border border-slate-200 dark:border-slate-800 bg-zinc-50 dark:bg-slate-950 rounded-2xl overflow-hidden transition-colors duration-300">
                <button class="w-full text-left px-6 py-5 flex justify-between items-center focus:outline-none hover:bg-slate-100/50 dark:hover:bg-slate-900/50 transition-colors" onclick="toggleAccordion(this)">
                    <span class="font-bold text-slate-900 dark:text-white text-base">Apa itu "Otomatis Gratis Unlimited"?</span>
                    <i class="ph ph-caret-down text-slate-400 transition-transform duration-300"></i>
                </button>
                <div class="faq-content max-h-0 overflow-hidden transition-all duration-300">
                    <p class="px-6 pb-6 text-slate-500 dark:text-slate-400 text-sm leading-relaxed font-light">Jika total tagihan Pakaiapp Anda dalam satu bulan sudah mencapai Rp {{ $cappingLimitFormatted }} (setara {{ floor($cappingLimit / $trxFee) }} transaksi), maka semua transaksi berikutnya di bulan tersebut gratis sepenuhnya tanpa batas. Jadi biaya maksimal Pakaiapp dalam sebulan adalah Rp {{ $cappingLimitFormatted }}, berapapun jumlah transaksinya.</p>
                </div>
            </div>

            <!-- FAQ 3 -->
            <div class="border border-slate-200 dark:border-slate-800 bg-zinc-50 dark:bg-slate-950 rounded-2xl overflow-hidden transition-colors duration-300">
                <button class="w-full text-left px-6 py-5 flex justify-between items-center focus:outline-none hover:bg-slate-100/50 dark:hover:bg-slate-900/50 transition-colors" onclick="toggleAccordion(this)">
                    <span class="font-bold text-slate-900 dark:text-white text-base">Apakah perlu install aplikasi di HP?</span>
                    <i class="ph ph-caret-down text-slate-400 transition-transform duration-300"></i>
                </button>
                <div class="faq-content max-h-0 overflow-hidden transition-all duration-300">
                    <p class="px-6 pb-6 text-slate-500 dark:text-slate-400 text-sm leading-relaxed font-light">Tidak perlu! Pakaiapp berbasis web dan berjalan langsung di browser HP, tablet, atau PC. Anda bisa menambahkan shortcut ke homescreen HP layaknya aplikasi (PWA) untuk kemudahan akses.</p>
                </div>
            </div>

            <!-- FAQ 4 -->
            <div class="border border-slate-200 dark:border-slate-800 bg-zinc-50 dark:bg-slate-950 rounded-2xl overflow-hidden transition-colors duration-300">
                <button class="w-full text-left px-6 py-5 flex justify-between items-center focus:outline-none hover:bg-slate-100/50 dark:hover:bg-slate-900/50 transition-colors" onclick="toggleAccordion(this)">
                    <span class="font-bold text-slate-900 dark:text-white text-base">Apakah data toko saya aman?</span>
                    <i class="ph ph-caret-down text-slate-400 transition-transform duration-300"></i>
                </button>
                <div class="faq-content max-h-0 overflow-hidden transition-all duration-300">
                    <p class="px-6 pb-6 text-slate-500 dark:text-slate-400 text-sm leading-relaxed font-light">Data Anda disimpan di server cloud terenkripsi dan dibackup secara rutin. Setiap akun toko memiliki subdomain dan database terisolasi, sehingga data Anda tidak bercampur dengan toko lain.</p>
                </div>
            </div>

            <!-- FAQ 5 -->
            <div class="border border-slate-200 dark:border-slate-800 bg-zinc-50 dark:bg-slate-950 rounded-2xl overflow-hidden transition-colors duration-300">
                <button class="w-full text-left px-6 py-5 flex justify-between items-center focus:outline-none hover:bg-slate-100/50 dark:hover:bg-slate-900/50 transition-colors" onclick="toggleAccordion(this)">
                    <span class="font-bold text-slate-900 dark:text-white text-base">Bagaimana cara top-up dan cairkan dana penjualan?</span>
                    <i class="ph ph-caret-down text-slate-400 transition-transform duration-300"></i>
                </button>
                <div class="faq-content max-h-0 overflow-hidden transition-all duration-300">
                    <p class="px-6 pb-6 text-slate-500 dark:text-slate-400 text-sm leading-relaxed font-light">Dana hasil penjualan dari pelanggan yang bayar via QRIS/E-Wallet langsung masuk ke wallet toko Anda di Pakaiapp. Proses penarikan ke rekening bank dilakukan secara manual oleh tim kami — hubungi support via WhatsApp untuk proses pencairan.</p>
                </div>
            </div>

            <!-- FAQ 6 -->
            <div class="border border-slate-200 dark:border-slate-800 bg-zinc-50 dark:bg-slate-950 rounded-2xl overflow-hidden transition-colors duration-300">
                <button class="w-full text-left px-6 py-5 flex justify-between items-center focus:outline-none hover:bg-slate-100/50 dark:hover:bg-slate-900/50 transition-colors" onclick="toggleAccordion(this)">
                    <span class="font-bold text-slate-900 dark:text-white text-base">Apakah ada biaya tambahan untuk fitur QRIS atau QR Self-Order?</span>
                    <i class="ph ph-caret-down text-slate-400 transition-transform duration-300"></i>
                </button>
                <div class="faq-content max-h-0 overflow-hidden transition-all duration-300">
                    <p class="px-6 pb-6 text-slate-500 dark:text-slate-400 text-sm leading-relaxed font-light">Tidak ada! Semua fitur termasuk QRIS, QR Self-Order, multi-staf, laporan, dan manajemen stok sudah termasuk dalam satu biaya flat Rp {{ $trxFee }}/transaksi. Tidak ada paket berbeda atau fitur yang dikunci di balik paywall.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
     FINAL CTA
============================================ -->
<section class="py-20 relative bg-zinc-50 dark:bg-slate-950 transition-colors">
    <div class="max-w-4xl mx-auto px-4 text-center bg-gradient-to-r from-emerald-600 to-teal-700 dark:from-emerald-950 dark:to-slate-900 text-white rounded-[2.5rem] p-10 sm:p-16 shadow-2xl relative overflow-hidden border border-emerald-500/20 dark:border-emerald-800/30">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-white/10 to-transparent pointer-events-none"></div>
        <div class="relative z-10 max-w-2xl mx-auto">
            <h2 class="font-heading text-3xl sm:text-4xl lg:text-5xl font-extrabold mb-6 leading-tight">Siap Mulai Jualan Lebih Efisien Hari Ini?</h2>
            <p class="text-sm sm:text-base text-emerald-100/90 font-light mb-8 max-w-lg mx-auto">Bergabung bersama 15.000+ toko yang sudah pakai Pakaiapp. Daftar gratis, setup dalam 2 menit, langsung bisa terima pesanan.</p>
            <a href="/register" class="inline-flex items-center gap-2 bg-white text-emerald-700 dark:text-slate-950 hover:bg-emerald-50 dark:bg-emerald-400 dark:hover:bg-emerald-300 font-bold py-4 px-10 rounded-full shadow-lg transition-all hover:scale-105 text-lg">
                <i class="ph-bold ph-storefront"></i> Buat Akun Toko — Gratis
            </a>
            <div class="flex flex-wrap justify-center items-center gap-6 mt-8 text-xs text-emerald-100/70 font-semibold">
                <span class="flex items-center gap-1"><i class="ph-bold ph-check-circle"></i> Tanpa Biaya Bulanan</span>
                <span class="flex items-center gap-1"><i class="ph-bold ph-check-circle"></i> Tanpa Kartu Kredit</span>
                <span class="flex items-center gap-1"><i class="ph-bold ph-check-circle"></i> Siap dalam 2 Menit</span>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
     FOOTER
============================================ -->
<footer class="bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 py-16 text-slate-500 dark:text-slate-400 transition-colors">
    <div class="max-w-5xl mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 pb-12 border-b border-slate-200 dark:border-slate-800">
            <!-- Left Info -->
            <div class="md:col-span-6">
                <a href="/" class="flex items-center gap-2 font-heading font-extrabold text-2xl text-slate-900 dark:text-white mb-4">
                    <i class="ph-fill ph-circles-four text-emerald-600 dark:text-emerald-400 text-2xl"></i> pakaiapp<span class="text-emerald-500">.online</span>
                </a>
                <p class="text-sm font-light leading-relaxed max-w-sm mb-6">Sistem kasir berbasis web untuk UMKM F&B dan Retail — tanpa biaya langganan bulanan.</p>
                <div class="space-y-2 text-xs">
                    <div class="flex items-center gap-2">
                        <i class="ph-bold ph-whatsapp text-emerald-600 dark:text-emerald-400 text-base"></i>
                        <a href="https://wa.me/6285172441544" target="_blank" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors font-medium">0851-7244-1544</a>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="ph-bold ph-envelope text-indigo-600 dark:text-indigo-400 text-base"></i>
                        <a href="mailto:support@pakaiapp.online" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors font-medium">support@pakaiapp.online</a>
                    </div>
                </div>
            </div>

            <!-- Links columns -->
            <div class="grid grid-cols-3 gap-6 md:col-span-6">
                <div>
                    <p class="font-heading font-bold text-slate-900 dark:text-white text-sm mb-4">Produk</p>
                    <ul class="space-y-2 text-xs font-light">
                        <li><a href="#fitur" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Fitur Lengkap</a></li>
                        <li><a href="#harga" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Cara Hitung</a></li>
                        <li><a href="#cara-daftar" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Cara Daftar</a></li>
                        <li><a href="/login" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors font-semibold">Login Kasir</a></li>
                    </ul>
                </div>
                <div>
                    <p class="font-heading font-bold text-slate-900 dark:text-white text-sm mb-4">Dukungan</p>
                    <ul class="space-y-2 text-xs font-light">
                        <li><a href="#faq" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">FAQ</a></li>
                        <li><a href="https://wa.me/6285172441544" target="_blank" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">WA Support</a></li>
                        <li><a href="mailto:support@pakaiapp.online" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Email Support</a></li>
                    </ul>
                </div>
                <div>
                    <p class="font-heading font-bold text-slate-900 dark:text-white text-sm mb-4">Legal</p>
                    <ul class="space-y-2 text-xs font-light">
                        <li><button onclick="openModal('tncModal')" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors text-left">Syarat & Ketentuan</button></li>
                        <li><button onclick="openModal('refundModal')" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors text-left">Kebijakan Refund</button></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-8 text-[0.7rem] text-slate-400 dark:text-slate-500">
            <span>&copy; {{ date('Y') }} pakaiapp.online &mdash; Produk dari <a href="https://www.ngopikode.com" target="_blank" class="hover:text-slate-600 dark:hover:text-slate-400 transition-colors font-semibold">ngopikode</a> (PT Sinergi Kode Kreatif)</span>
            <span class="flex items-center gap-1.5"><i class="ph-bold ph-shield-check text-emerald-600 dark:text-emerald-400 text-sm"></i> Platform Aman & Terenkripsi</span>
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
    <div class="custom-modal-content relative bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2.5rem] w-full max-w-2xl max-h-[85vh] overflow-hidden flex flex-col shadow-2xl transition-colors">
        <!-- Header -->
        <div class="px-8 py-5 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center shrink-0">
            <h3 class="font-heading font-extrabold text-slate-900 dark:text-white text-lg flex items-center gap-2">
                <i class="ph-fill ph-shield-check text-emerald-600 dark:text-emerald-400"></i> Syarat & Ketentuan Layanan
            </h3>
            <button onclick="closeModal('tncModal')" class="p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-white rounded-full transition-colors"><i class="ph-bold ph-x text-lg"></i></button>
        </div>
        <!-- Scrollable Body -->
        <div class="p-8 overflow-y-auto text-slate-600 dark:text-slate-300 text-sm leading-relaxed space-y-5 font-light">
            <p>Selamat datang di <strong class="text-slate-900 dark:text-white font-bold">Pakaiapp</strong>. Harap membaca Syarat & Ketentuan ini dengan saksama sebelum mendaftar dan menggunakan platform kami.</p>
            
            <h4 class="font-heading font-bold text-slate-900 dark:text-white text-base">1. KETENTUAN UMUM & DEFINISI</h4>
            <ul class="list-disc pl-5 space-y-1.5">
                <li><strong class="text-slate-800 dark:text-white">Pakaiapp</strong> adalah platform Software-as-a-Service (SaaS) aplikasi kasir pintar (Point of Sales) berbasis web cloud yang dikembangkan oleh PT Sinergi Kode Kreatif.</li>
                <li><strong class="text-slate-800 dark:text-white">Pengguna</strong> adalah pemilik usaha (merchant), beserta staf/admin yang ditunjuk, yang mendaftarkan diri.</li>
                <li><strong class="text-slate-800 dark:text-white">Layanan</strong> mencakup penyediaan sistem kasir, manajemen varian menu, etalase online (QR self-order), dan pelaporan.</li>
            </ul>

            <h4 class="font-heading font-bold text-slate-900 dark:text-white text-base">2. PENDAFTARAN AKUN DAN KEAMANAN</h4>
            <ul class="list-disc pl-5 space-y-1.5">
                <li>Pengguna wajib memberikan data informasi bisnis yang akurat, benar, dan terbaru pada saat proses pendaftaran.</li>
                <li>Pengguna bertanggung jawab penuh atas keamanan kredensial akun dan hak akses karyawan masing-masing.</li>
            </ul>

            <h4 class="font-heading font-bold text-slate-900 dark:text-white text-base">3. FUNGSI DOMPET DIGITAL (WALLET) & BIAYA TRANSAKSI</h4>
            <ul class="list-disc pl-5 space-y-1.5">
                <li>Platform menggunakan sistem Dompet Digital terpusat untuk: (1) menampung saldo Top-Up prabayar untuk pemotongan biaya sistem, dan (2) menampung dana hasil penjualan dari Payment Gateway.</li>
                <li>Pendaftaran akun dan penggunaan dasar aplikasi tidak dikenakan biaya langganan bulanan.</li>
                <li>Setiap transaksi penjualan yang berstatus sukses/selesai akan dikenakan biaya sistem sebesar <strong class="text-slate-800 dark:text-white">Rp {{ $trxFee }}</strong> yang dipotong otomatis dari Saldo Wallet Pengguna.</li>
            </ul>

            <h4 class="font-heading font-bold text-slate-900 dark:text-white text-base">4. PENARIKAN DANA (WITHDRAWAL) & PENGEMBALIAN DANA</h4>
            <ul class="list-disc pl-5 space-y-1.5">
                <li>Saldo yang bersumber dari hasil penjualan (Payment Gateway) dapat ditarik oleh Pengguna ke rekening bank yang didaftarkan.</li>
                <li>Proses penarikan saat ini dilakukan secara manual oleh tim admin.</li>
                <li>Saldo yang bersumber dari Top-Up prabayar bersifat non-refundable (tidak dapat ditarik atau diuangkan kembali).</li>
            </ul>

            <h4 class="font-heading font-bold text-slate-900 dark:text-white text-base">5. HUKUM YANG BERLAKU</h4>
            <ul class="list-disc pl-5 space-y-1.5">
                <li>Syarat & Ketentuan ini diatur, ditafsirkan, dan tunduk sepenuhnya pada hukum negara Republik Indonesia.</li>
            </ul>
        </div>
        <!-- Footer actions -->
        <div class="px-8 py-4 border-t border-slate-100 dark:border-slate-800 flex justify-end shrink-0">
            <button onclick="closeModal('tncModal')" class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 text-white dark:bg-emerald-500 dark:hover:bg-emerald-600 dark:text-slate-950 font-bold py-2.5 px-6 rounded-xl transition-all shadow-sm text-sm">Saya Setuju</button>
        </div>
    </div>
</div>

<!-- Modal Refund -->
<div id="refundModal" class="custom-modal fixed inset-0 z-[100] flex items-center justify-center p-4">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('refundModal')"></div>
    <!-- Box content -->
    <div class="custom-modal-content relative bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2.5rem] w-full max-w-2xl max-h-[85vh] overflow-hidden flex flex-col shadow-2xl transition-colors">
        <!-- Header -->
        <div class="px-8 py-5 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center shrink-0">
            <h3 class="font-heading font-extrabold text-slate-900 dark:text-white text-lg flex items-center gap-2">
                <i class="ph-fill ph-wallet text-emerald-600 dark:text-emerald-400"></i> Kebijakan Pengembalian Dana
            </h3>
            <button onclick="closeModal('refundModal')" class="p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-white rounded-full transition-colors"><i class="ph-bold ph-x text-lg"></i></button>
        </div>
        <!-- Scrollable Body -->
        <div class="p-8 overflow-y-auto text-slate-600 dark:text-slate-300 text-sm leading-relaxed space-y-5 font-light">
            <p>Sebagai bagian dari kepatuhan operasional kami di <strong class="text-slate-900 dark:text-white font-bold">Pakaiapp</strong>, berikut adalah kebijakan resmi terkait pengembalian dana (refund):</p>
            
            <h4 class="font-heading font-bold text-slate-900 dark:text-white text-base">1. FINALITAS TRANSAKSI TOP-UP</h4>
            <p>Seluruh transaksi pengisian ulang saldo (Top-Up) yang telah berhasil diverifikasi oleh sistem bersifat final dan mengikat.</p>
            
            <h4 class="font-heading font-bold text-slate-900 dark:text-white text-base">2. PEMISAHAN JENIS SALDO & PENARIKAN</h4>
            <p><strong class="text-slate-800 dark:text-white font-semibold">Saldo Top-Up prabayar bersifat mutlak non-refundable</strong>. Namun, <strong class="text-slate-800 dark:text-white font-semibold">Saldo Pendapatan</strong> dari hasil transaksi penjualan online dapat ditarik secara manual ke rekening bank pemilik usaha yang telah diverifikasi.</p>
            
            <h4 class="font-heading font-bold text-slate-900 dark:text-white text-base">3. SALDO ABADI</h4>
            <p>Saldo top-up pada wallet Pakaiapp bersifat abadi dan tidak memiliki masa kedaluwarsa.</p>
            
            <h4 class="font-heading font-bold text-slate-900 dark:text-white text-base">4. HUBUNGI KAMI</h4>
            <p>Hubungi kami melalui WhatsApp di <strong class="text-emerald-600 dark:text-emerald-400 font-semibold">085172441544</strong> atau email ke <strong class="text-slate-800 dark:text-white font-semibold">support@pakaiapp.online</strong>.</p>
        </div>
        <!-- Footer actions -->
        <div class="px-8 py-4 border-t border-slate-100 dark:border-slate-800 flex justify-end shrink-0">
            <button onclick="closeModal('refundModal')" class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 text-white dark:bg-emerald-500 dark:hover:bg-emerald-600 dark:text-slate-950 font-bold py-2.5 px-6 rounded-xl transition-all shadow-sm text-sm">Saya Mengerti</button>
        </div>
    </div>
</div>

<livewire:central-ai-floating-chat />

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
        <script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    @else
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    @endif
@endif

<!-- Configuration needed by welcome.js -->
<script>
    window.PAKAIAAPP_CONFIG = {
        trxFee: {{ $trxFee }},
        cappingLimit: {{ $cappingLimit }},
        cappingLimitFormatted: '{{ $cappingLimitFormatted }}',
        cappingLimitShort: '{{ $cappingLimitShort }}'
    };
</script>
@vite(['resources/js/welcome.js'])
</body>
</html>
