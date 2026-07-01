<!DOCTYPE html>
<html lang="id" class="scroll-smooth dark" id="html-root">
<head>
    <script>
        // Theme expiry check and initial load
        const themeExpiry = localStorage.getItem('theme_expiry');
        const now = new Date().getTime();
        if (themeExpiry && now > parseInt(themeExpiry)) {
            localStorage.removeItem('theme');
            localStorage.removeItem('theme_expiry');
        }
        
        let initialTheme = localStorage.getItem('theme');
        if (!initialTheme) {
            const hour = new Date().getHours();
            initialTheme = (hour >= 18 || hour < 6) ? 'dark' : 'light';
        }
        
        if (initialTheme === 'light') {
            document.documentElement.classList.remove('dark');
            document.documentElement.setAttribute('data-bs-theme', 'light');
        } else {
            document.documentElement.classList.add('dark');
            document.documentElement.setAttribute('data-bs-theme', 'dark');
        }
    </script>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Daftar Aplikasi Kasir POS Online Gratis | Pakaiapp</title>
    <meta name="description" content="Daftar aplikasi kasir POS pintar dari Pakaiapp. Tingkatkan omzet bisnis F&B dan Retail Anda dengan fitur pencatatan otomatis, stok, dan laporan realtime.">
    <meta name="keywords" content="daftar aplikasi kasir, kasir online, pos system, aplikasi toko, pakaiapp pos">
    <link rel="canonical" href="https://www.pakaiapp.online/register">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://www.pakaiapp.online/register">
    <meta property="og:title" content="Daftar Aplikasi Kasir POS Online Gratis | Pakaiapp">
    <meta property="og:description" content="Daftar aplikasi kasir POS pintar dari Pakaiapp. Tingkatkan omzet bisnis F&B dan Retail Anda dengan fitur pencatatan otomatis, stok, dan laporan realtime.">
    <meta property="og:image" content="{{ asset('images/og-banner.png') }}">
    
    <!-- Google Fonts & Tailwind -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>

    <style>
        html.dark {
            --color-bg: #1B2421;
            --color-bg-alt: #222E2A;
            --color-bg-card: #161D1A;
            --color-bg-dark: #121815;
            --color-text: #E2E8F0;
            --color-text-muted: #94A3B8;
            --color-text-subtle: #475569;
            --color-border: #2C3E38;
        }
        html.dark body.sustainability-platform .navbar-eco {
            background: rgba(27, 36, 33, 0.9);
        }

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
            --font-heading: "Outfit", sans-serif;
            --font-body: "Plus Jakarta Sans", sans-serif;
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

    @vite(['resources/css/welcome.css'])
</head>
<body class="sustainability-platform bg-[var(--color-bg)] text-[var(--color-text)] font-sans antialiased h-screen flex flex-col overflow-hidden relative transition-colors duration-300">

    <!-- Background Decoration -->
    <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-[var(--color-primary)] opacity-10 rounded-full blur-[100px] pointer-events-none -z-10"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-[var(--color-secondary)] opacity-10 rounded-full blur-[100px] pointer-events-none -z-10"></div>

    <!-- Header Navigation -->
    <header class="w-full px-4 sm:px-6 py-4 sm:py-5 flex justify-between items-center bg-[var(--color-bg)]/80 backdrop-blur-md border-b border-[var(--color-border)] shrink-0 z-10 transition-colors">
        <a href="/" class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] text-xs sm:text-sm font-semibold flex items-center gap-1.5 transition-colors px-1.5 sm:px-2 py-1 rounded-lg">
            <i class="ph-bold ph-arrow-left text-base"></i> <span class="hidden sm:inline">Kembali</span>
        </a>
        
        <div class="font-heading font-extrabold text-base sm:text-xl text-[var(--color-text)] flex items-center gap-1.5">
            <img src="/android-chrome-192x192.png" alt="Pakaiapp Logo" class="w-6 h-6 sm:w-8 sm:h-8 rounded-full shadow-sm">
            pakaiapp<span class="text-[var(--color-primary)] hidden sm:inline">.online</span>
        </div>
        
        <!-- Theme Toggle Button (DO NOT change id, expected by welcome.js) -->
        <button id="theme-toggle" class="p-1.5 sm:p-2 text-[var(--color-text-muted)] hover:text-[var(--color-primary)] hover:bg-[var(--color-bg-alt)] rounded-full transition-colors" aria-label="Toggle Theme">
            <i class="ph-bold ph-sun text-lg sm:text-xl" id="theme-icon"></i>
        </button>
    </header>

    <!-- Main Conversational Interface -->
    <main class="flex-grow relative flex flex-col items-center justify-center w-full max-w-3xl mx-auto p-4 sm:p-6" id="promptMain">
        
        <!-- Center Question Area -->
        <div class="w-full text-center transition-all duration-300 mb-8" id="questionArea">
            <div class="w-16 h-16 bg-[var(--color-primary)]/10 text-[var(--color-primary)] border border-[var(--color-primary)]/20 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-sm">
                <i class="ph-fill ph-magic-wand text-3xl animate-pulse"></i>
            </div>
            <!-- Dynamic Question fields -->
            <h1 id="aiQuestion" class="font-heading text-2xl sm:text-3xl lg:text-4xl font-extrabold text-[var(--color-text)] mb-3 tracking-tight">Halo! Siapa nama toko atau bisnis Anda?</h1>
            <p id="aiSubQuestion" class="text-[var(--color-text-muted)] text-sm sm:text-base font-light">Mari kita siapkan kasir cerdas Anda dalam hitungan detik.</p>
        </div>

        <div class="w-full max-w-xl mx-auto mt-2">
            
            <!-- Input container box -->
            <div id="inputContainer" class="relative group rounded-[1.5rem] bg-[var(--color-bg-card)] border border-[var(--color-border)] p-1.5 flex items-center focus-within:border-[var(--color-primary)] focus-within:ring-2 focus-within:ring-[var(--color-primary)]/20 transition-all shadow-lg">
                <input type="text" id="chatInput" autocomplete="off"
                    class="w-full bg-transparent border-none px-4 py-3 text-[var(--color-text)] placeholder-[var(--color-text-subtle)] focus:outline-none text-base sm:text-lg"
                    placeholder="Ketik nama tokomu di sini...">
                <button id="btnSend" class="btn-primary rounded-xl w-12 h-12 flex items-center justify-center shrink-0 transition-colors disabled:opacity-50 shadow-md">
                    <i class="ph-bold ph-arrow-up text-2xl font-bold"></i>
                </button>
            </div>

            <!-- Choice buttons dynamic grid container -->
            <div id="choicesContainer" class="grid grid-cols-1 sm:grid-cols-2 gap-3" style="display: none;"></div>
            
            <!-- Actions & Status footer -->
            <div class="mt-8 flex flex-col items-center gap-4">
                <!-- Step Back Button -->
                <button type="button" id="btnStepBack" style="display: none;" class="text-xs font-semibold text-[var(--color-text-muted)] hover:text-[var(--color-text)] transition-colors flex items-center gap-1.5 px-4 py-2 rounded-full border border-[var(--color-border)] bg-[var(--color-bg-alt)] hover:bg-[var(--color-border)]">
                    <i class="ph-bold ph-arrow-counterclockwise"></i> Ganti Jawaban Sebelumnya
                </button>
                <div class="text-xs text-[var(--color-text-subtle)] flex items-center gap-1.5 opacity-80">
                    <i class="ph-fill ph-shield-check text-[var(--color-primary)]"></i> Data Anda aman terenkripsi
                </div>
            </div>
        </div>
    </main>

    <!-- Fullscreen Loading Overlay -->
    <div id="loadingOverlay" style="display: none;" class="fixed inset-0 bg-[var(--color-bg)]/95 backdrop-blur-sm z-[100] flex flex-col items-center justify-center transition-colors">
        <div class="relative w-20 h-20 mb-6">
            <div class="absolute inset-0 border-4 border-[var(--color-border)] rounded-full"></div>
            <div class="absolute inset-0 border-4 border-[var(--color-primary)] rounded-full border-t-transparent animate-spin"></div>
        </div>
        <div id="loadingText" class="font-heading font-extrabold text-[var(--color-text)] text-xl animate-pulse">Menyiapkan workspace...</div>
    </div>

    <!-- Toast Notification alerts -->
    <div id="customToast" class="fixed top-6 right-6 bg-[var(--color-bg-card)] border border-[var(--color-border)] shadow-xl rounded-2xl px-5 py-4 flex items-center gap-3 transform translate-x-[150%] transition-transform duration-300 z-[110]">
        <i class="ph-fill ph-warning-circle text-amber-500 text-2xl"></i>
        <span id="toastMsg" class="font-semibold text-[var(--color-text)] text-sm"></span>
    </div>

    <!-- Alert Dialog Modals -->
    <div id="customModal" class="custom-modal-overlay" onclick="window.closeCustomAlert()">
        <div class="custom-modal-box bg-[var(--color-bg-card)] border border-[var(--color-border)]" onclick="event.stopPropagation()">
            <div id="modalIcon" class="w-16 h-16 rounded-full bg-[var(--color-bg-alt)] border border-[var(--color-border)] flex items-center justify-center mx-auto mb-6 text-3xl text-[var(--color-text)]"></div>
            <h3 id="modalTitle" class="font-heading font-bold text-[var(--color-text)] text-xl mb-3"></h3>
            <p id="modalDesc" class="text-[var(--color-text-muted)] text-sm mb-6 leading-relaxed font-light"></p>
            <button id="modalBtn" onclick="window.closeCustomAlert()" class="w-full btn-primary font-bold py-3.5 rounded-xl transition-colors shadow-md text-sm">Tutup</button>
        </div>
    </div>

    <script>
        window.PAKAIAAPP_CONFIG = {
            duitkuEnabled: {{ config('duitku.enabled') ? 'true' : 'false' }},
            midtransEnabled: {{ config('midtrans.server_key') ? 'true' : 'false' }}
        };

        // Mutation Observer to style buttons dynamically appended inside choicesContainer
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'childList') {
                    const container = document.getElementById('choicesContainer');
                    if (container && (container.contains(mutation.target) || mutation.target === container)) {
                        const buttons = container.querySelectorAll('button');
                        buttons.forEach(btn => {
                            // Style category / package choice buttons to look extremely premium
                            btn.className = 'w-full py-4 px-6 rounded-2xl border-2 border-[var(--color-border)] bg-[var(--color-bg-card)] text-[var(--color-text)] font-semibold hover:border-[var(--color-primary)] hover:bg-[var(--color-primary)]/5 transition-all text-sm sm:text-base text-center cursor-pointer shadow-sm';
                        });
                    }
                }
            });
        });
        const targetNode = document.getElementById('choicesContainer');
        if(targetNode) observer.observe(targetNode, { childList: true, subtree: true });
    </script>
    @vite(['resources/js/welcome.js'])
</body>
</html>
