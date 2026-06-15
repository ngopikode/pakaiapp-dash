@php use App\Models\GlobalSetting; @endphp
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
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

    <title>Pakaiapp - Aplikasi Kasir UMKM Tanpa Biaya Langganan</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Fraunces:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(["resources/css/welcome.css"])
    @livewireStyles

    <style>
        :root {
            --color-primary: #228B22;
            --color-secondary: #2E7D32;
            --color-accent: #8B4513;
            --color-sky: #87CEEB;
            --color-earth: #A0522D;
            --color-leaf: #4CAF50;
            --color-sun: #FFB300;
            --color-bg: #FAFAF5;
            --color-bg-alt: #F5F5E8;
            --color-bg-card: #FFFFFF;
            --color-bg-dark: #1B4332;
            --color-text: #1B4332;
            --color-text-muted: #52796F;
            --color-text-subtle: #84A98C;
            --color-border: #D8E2DC;
            --gradient-nature: linear-gradient(135deg, #228B22 0%, #4CAF50 100%);
            --font-heading: "Fraunces", serif;
            --font-body: "DM Sans", sans-serif;
        }

        body.sustainability-platform {
            font-family: var(--font-body);
            background-color: var(--color-bg);
            color: var(--color-text);
            min-height: 100vh;
        }

        .sustainability-platform h1, .sustainability-platform h2, .sustainability-platform h3, .sustainability-platform h4 {
            font-family: var(--font-heading);
            font-weight: 600;
        }

        .sustainability-platform .gradient-text {
            background: var(--gradient-nature);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .sustainability-platform .organic-card {
            background: var(--color-bg-card);
            border-radius: 24px;
            border: 1px solid var(--color-border);
            transition: all 0.3s ease;
        }

        .sustainability-platform .organic-card:hover {
            box-shadow: 0 20px 40px rgba(34,139,34,0.1);
            border-color: var(--color-primary);
        }

        .sustainability-platform .btn-primary {
            background: var(--gradient-nature);
            color: #fff;
            font-weight: 600;
            padding: 14px 32px;
            border-radius: 50px;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
        }

        .sustainability-platform .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(34,139,34,0.3);
        }

        .sustainability-platform .btn-secondary {
            background: transparent;
            color: var(--color-primary);
            font-weight: 600;
            padding: 14px 32px;
            border-radius: 50px;
            border: 2px solid var(--color-primary);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .sustainability-platform .btn-secondary:hover {
            background: var(--color-primary);
            color: #fff;
        }

        .sustainability-platform .navbar-eco {
            background: rgba(250, 250, 245, 0.9);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--color-border);
        }

        .sustainability-platform .impact-number {
            font-family: var(--font-heading);
            font-size: 3rem;
            font-weight: 700;
            color: var(--color-primary);
            line-height: 1;
        }

        .sustainability-platform .impact-label {
            color: var(--color-text-muted);
            font-size: 0.875rem;
            margin-top: 8px;
        }

        .sustainability-platform .feature-card {
            background: var(--color-bg-card);
            border-radius: 20px;
            padding: 32px;
            border: 1px solid var(--color-border);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .sustainability-platform .feature-card:hover {
            border-color: var(--color-leaf);
            box-shadow: 0 15px 40px rgba(34,139,34,0.1);
        }

        .sustainability-platform .feature-icon {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .sustainability-platform .badge-eco {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            background: rgba(34,139,34,0.1);
            color: var(--color-primary);
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .sustainability-platform .calculator-preview {
            background: var(--color-bg-card);
            border-radius: 24px;
            border: 1px solid var(--color-border);
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.08);
        }

        .sustainability-platform .leaf-decoration {
            position: absolute;
            opacity: 0.1;
        }
    </style>
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
                <a href="#features" class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors cursor-pointer font-medium">Fitur</a>
                <a href="#calculator" class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors cursor-pointer font-medium">Kalkulator</a>
                <a href="#impact" class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors cursor-pointer font-medium">Testimoni</a>
            </div>
            <div class="hidden md:flex items-center gap-4">
                <a href="/login" class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] font-medium transition-colors cursor-pointer">Masuk</a>
                <a href="/register" class="btn-primary text-sm py-2.5 px-5">Daftar Gratis</a>
            </div>
            <button class="md:hidden p-2 text-[var(--color-text)] cursor-pointer">
                <i class="ph-bold ph-list text-2xl"></i>
            </button>
        </div>
    </div>
</nav>

<section class="relative min-h-screen flex items-center pt-16 overflow-hidden">
    <div class="absolute inset-0 z-0">
        <div class="absolute top-20 right-10 w-[400px] h-[400px] rounded-full bg-[var(--color-leaf)]/10 blur-3xl"></div>
        <div class="absolute bottom-20 left-10 w-[300px] h-[300px] rounded-full bg-[var(--color-sky)]/10 blur-3xl"></div>
        <svg class="leaf-decoration absolute top-32 right-20 w-24 h-24 text-[var(--color-primary)]" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17 8C8 10 5.9 16.17 3.82 21.34l1.89.66.95-2.3c.48.17.98.3 1.34.3C19 20 22 3 22 3c-1 2-8 2.25-13 3.25S2 11.5 2 13.5s1.75 3.75 1.75 3.75C7 8 17 8 17 8z"></path>
        </svg>
    </div>
    
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-20">
        <div class="grid lg:grid-cols-2 gap-8 sm:gap-12 items-center">
            <div>
                <div class="badge-eco mb-6">
                    <i class="ph-fill ph-storefront"></i>
                    <span>Sistem Kasir Tanpa Biaya Bulanan</span>
                </div>
                
                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-[var(--color-text)] mb-6 leading-tight">
                    Tinggalkan Langganan,<br>
                    <span class="gradient-text">Bayar Suka-Suka</span>
                </h1>
                
                <p class="text-base sm:text-lg md:text-xl text-[var(--color-text-muted)] mb-8 max-w-xl">
                    Sistem kasir pintar berbasis web untuk UMKM. Kelola penjualan, menu, laporan, dan terima pembayaran QRIS dari perangkat apa saja. Toko sepi? Gratis.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 mb-8 sm:mb-12">
                    <a href="/register" class="btn-primary text-base flex items-center justify-center gap-2 text-center">
                        Buat Toko Sekarang
                    </a>
                    <a href="#calculator" class="btn-secondary text-base text-center">
                        Simulasi Biaya
                    </a>
                </div>
                
                <div class="flex flex-wrap gap-6">
                    <div class="flex items-center gap-2 text-[var(--color-text-muted)]">
                        <i class="ph-fill ph-check-circle text-[var(--color-primary)] text-lg"></i>
                        <span>Pendaftaran 100% Gratis</span>
                    </div>
                    <div class="flex items-center gap-2 text-[var(--color-text-muted)]">
                        <i class="ph-fill ph-check-circle text-[var(--color-primary)] text-lg"></i>
                        <span>Tanpa Kartu Kredit</span>
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
                                <circle cx="60" cy="60" r="54" fill="none" stroke="var(--color-border)" stroke-width="8"></circle>
                                <circle cx="60" cy="60" r="54" fill="none" stroke="url(#gradient)" stroke-width="8" stroke-linecap="round" stroke-dasharray="339.3" stroke-dashoffset="100" transform="rotate(-90 60 60)"></circle>
                                <defs>
                                    <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                        <stop offset="0%" stop-color="var(--color-primary)"></stop>
                                        <stop offset="100%" stop-color="var(--color-leaf)"></stop>
                                    </linearGradient>
                                </defs>
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span class="text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider mb-1">Cuma Bayar</span>
                                <span class="text-4xl font-bold text-[var(--color-text)]">Rp {{ $trxFee }}</span>
                                <span class="text-xs text-[var(--color-text-muted)] mt-1">/ transaksi sukses</span>
                            </div>
                        </div>
                        <p class="text-sm text-[var(--color-text-muted)] mt-6 px-4">
                            Maksimal bayar Rp {{ $cappingLimitFormatted }} sebulan. <br>Lebih dari itu? <strong style="color: var(--color-primary)">GRATIS!</strong>
                        </p>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-[var(--color-text-muted)]">50 Transaksi/Bulan</span>
                                <span class="font-medium text-[var(--color-text)]">Rp {{ number_format(50 * $trxFee, 0, ",", ".") }}</span>
                            </div>
                            <div class="h-2 bg-[var(--color-bg-alt)] rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500" style="width: 15%; background-color: var(--color-primary);"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-[var(--color-text-muted)]">250 Transaksi/Bulan</span>
                                <span class="font-medium text-[var(--color-text)]">Rp {{ number_format(250 * $trxFee, 0, ",", ".") }}</span>
                            </div>
                            <div class="h-2 bg-[var(--color-bg-alt)] rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500" style="width: 50%; background-color: var(--color-leaf);"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-[var(--color-text-muted)]">>500 Transaksi/Bulan</span>
                                <span class="font-medium text-[var(--color-text)]">Rp {{ $cappingLimitFormatted }} (Mentok)</span>
                            </div>
                            <div class="h-2 bg-[var(--color-bg-alt)] rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500" style="width: 100%; background-color: var(--color-sky);"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="absolute -top-4 -left-4 organic-card p-4 hidden lg:flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-[var(--color-leaf)] flex items-center justify-center">
                        <i class="ph-bold ph-receipt text-white"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-[var(--color-text)]">Rp 0</div>
                        <div class="text-xs text-[var(--color-text-muted)]">Biaya Tersembunyi</div>
                    </div>
                </div>
                
                <div class="absolute -bottom-4 -right-4 organic-card p-4 hidden lg:flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-[var(--color-sun)] flex items-center justify-center">
                        <i class="ph-bold ph-infinity text-white"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-[var(--color-text)]">Otomatis</div>
                        <div class="text-xs text-[var(--color-text-muted)]">Gratis Tanpa Batas</div>
                    </div>
                </div>
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
            <div class="feature-card">
                <div class="feature-icon" style="background-color: var(--color-primary)15; color: var(--color-primary);">
                    <i class="ph-bold ph-storefront text-3xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-[var(--color-text)] mb-2">Kasir Web Real-Time</h3>
                <p class="text-[var(--color-text-muted)]">
                    Proses pesanan dan cetak struk langsung dari browser HP, tablet, atau PC Anda. Tanpa perlu download.
                </p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon" style="background-color: var(--color-leaf)15; color: var(--color-leaf);">
                    <i class="ph-bold ph-qr-code text-3xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-[var(--color-text)] mb-2">QR Self-Order Meja</h3>
                <p class="text-[var(--color-text-muted)]">
                    Pelanggan tinggal scan QR di meja, pilih menu, dan bayar. Pesanan otomatis masuk ke layar kasir/dapur.
                </p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon" style="background-color: var(--color-sky)15; color: var(--color-sky);">
                    <i class="ph-bold ph-wallet text-3xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-[var(--color-text)] mb-2">Terima QRIS & E-Wallet</h3>
                <p class="text-[var(--color-text-muted)]">
                    Terima pembayaran dari GoPay, OVO, Dana, LinkAja, BCA, dll secara instan tanpa perlu mesin EDC tambahan.
                </p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon" style="background-color: var(--color-earth)15; color: var(--color-earth);">
                    <i class="ph-bold ph-chart-bar text-3xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-[var(--color-text)] mb-2">Laporan & Analitik</h3>
                <p class="text-[var(--color-text-muted)]">
                    Pantau grafik omset harian, produk paling laris, dan performa staf langsung di satu dasbor yang rapi.
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
                <div class="feature-icon" style="background-color: var(--color-primary)15; color: var(--color-primary);">
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

        <div class="text-center bg-[var(--color-bg-card)] rounded-[2rem] border border-[var(--color-border)] p-10 sm:p-16 max-w-3xl mx-auto organic-card">
            <i class="ph-fill ph-quotes text-5xl text-[var(--color-leaf)] opacity-20 mb-6"></i>
            <h3 class="text-2xl font-bold text-[var(--color-text)] mb-6 font-heading leading-relaxed">
                "Sejak pakai sistem Pakaiapp di kafe kami, operasional jauh lebih mudah. Dari kasir pintar, manajemen stok, hingga QR order ada di satu web. Pelayanan makin cepat!"
            </h3>
            <div class="flex items-center justify-center gap-4 mt-8">
                <div class="w-12 h-12 rounded-full bg-[var(--color-primary)] text-white flex items-center justify-center font-bold text-lg">M</div>
                <div class="text-left">
                    <p class="font-bold text-[var(--color-text)] text-sm">Mirayeni</p>
                    <p class="text-[var(--color-text-muted)] text-xs uppercase tracking-wider font-semibold">Owner Sama Roti Kukus</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-16 sm:py-24 bg-[var(--color-bg-dark)] relative overflow-hidden">
    <div class="absolute inset-0 z-0">
        <div class="absolute top-0 left-1/4 w-[300px] h-[300px] rounded-full bg-[var(--color-primary)]/20 blur-3xl"></div>
        <div class="absolute bottom-0 right-1/4 w-[250px] h-[250px] rounded-full bg-[var(--color-leaf)]/20 blur-3xl"></div>
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

<footer class="bg-[var(--color-bg)] border-t border-[var(--color-border)] py-10 sm:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-5 gap-6 sm:gap-8 mb-8 sm:mb-12">
            <div class="col-span-2 md:col-span-2">
                <div class="flex items-center gap-2 mb-4">
                    <img src="/android-chrome-192x192.png" alt="Pakaiapp Logo" class="w-10 h-10 rounded-full shadow-sm">
                    <span class="text-xl font-bold font-heading text-[var(--color-text)]">pakaiapp<span style="color: var(--color-primary)">.online</span></span>
                </div>
                <p class="text-[var(--color-text-muted)] text-sm mb-6 max-w-sm">
                    Sistem kasir cerdas berbasis web untuk UMKM F&B dan Retail — tanpa beban biaya langganan bulanan.
                </p>
                <div class="flex gap-4">
                    <a href="https://wa.me/6285172441544" class="w-10 h-10 rounded-full bg-[var(--color-bg-alt)] flex items-center justify-center text-[var(--color-text-muted)] hover:bg-[var(--color-primary)] hover:text-white transition-colors cursor-pointer">
                        <i class="ph-bold ph-whatsapp-logo text-xl"></i>
                    </a>
                    <a href="mailto:support@pakaiapp.online" class="w-10 h-10 rounded-full bg-[var(--color-bg-alt)] flex items-center justify-center text-[var(--color-text-muted)] hover:bg-[var(--color-primary)] hover:text-white transition-colors cursor-pointer">
                        <i class="ph-bold ph-envelope text-xl"></i>
                    </a>
                </div>
            </div>
            
            <div>
                <h4 class="font-semibold text-[var(--color-text)] mb-4">Produk</h4>
                <ul class="space-y-3">
                    <li><a href="#features" class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors text-sm">Fitur Lengkap</a></li>
                    <li><a href="#calculator" class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors text-sm">Simulasi Harga</a></li>
                    <li><a href="/login" class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors text-sm">Login Kasir</a></li>
                </ul>
            </div>
            
            <div>
                <h4 class="font-semibold text-[var(--color-text)] mb-4">Dukungan</h4>
                <ul class="space-y-3">
                    <li><a href="https://wa.me/6285172441544" class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors text-sm">WhatsApp Support</a></li>
                    <li><a href="mailto:support@pakaiapp.online" class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors text-sm">Email Support</a></li>
                </ul>
            </div>
            
            <div>
                <h4 class="font-semibold text-[var(--color-text)] mb-4">Legal</h4>
                <ul class="space-y-3">
                    <li><a href="#" class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors text-sm">Syarat & Ketentuan</a></li>
                    <li><a href="#" class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors text-sm">Kebijakan Privasi</a></li>
                </ul>
            </div>
        </div>
        
        <div class="pt-8 border-t border-[var(--color-border)] flex flex-col md:flex-row justify-between items-center gap-4">
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

<livewire:central-ai-floating-chat />

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