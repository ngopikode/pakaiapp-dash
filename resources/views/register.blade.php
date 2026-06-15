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
    
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://www.pakaiapp.online/register">
    <meta property="twitter:title" content="Daftar Aplikasi Kasir POS Online Gratis | Pakaiapp">
    <meta property="twitter:description" content="Daftar aplikasi kasir POS pintar dari Pakaiapp. Tingkatkan omzet bisnis F&B dan Retail Anda dengan fitur pencatatan otomatis, stok, dan laporan realtime.">
    <meta property="twitter:image" content="{{ asset('images/og-banner.png') }}">

    <!-- Google Fonts & Tailwind -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    

        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>

    @vite(['resources/css/welcome.css'])
</head>
<body class="bg-zinc-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-sans antialiased h-screen flex flex-col overflow-hidden relative transition-colors duration-300">

    <!-- Background Ambient Glow -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-emerald-500/5 blur-[150px] rounded-full -z-10 pointer-events-none"></div>

    <!-- Header Navigation -->
    <header class="w-full px-4 sm:px-6 py-4 sm:py-5 flex justify-between items-center bg-white/50 dark:bg-slate-900/50 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 shrink-0 z-10 transition-colors">
        <a href="/" class="text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white text-xs sm:text-sm font-semibold flex items-center gap-1.5 transition-colors px-1.5 sm:px-2 py-1 rounded-lg">
            <i class="ph-bold ph-arrow-left text-base"></i> <span class="hidden sm:inline">Kembali</span>
        </a>
        
        <div class="font-heading font-extrabold text-base sm:text-xl text-slate-900 dark:text-white flex items-center gap-1.5">
            <i class="ph-fill ph-circles-four text-emerald-600 dark:text-emerald-400 text-lg sm:text-xl shrink-0"></i> pakaiapp<span class="text-emerald-500 hidden sm:inline">.online</span>
        </div>
        
        <!-- Theme Toggle Button (DO NOT change id, expected by welcome.js) -->
        <button id="theme-toggle" class="p-1.5 sm:p-2 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors" aria-label="Toggle Theme">
            <i class="ph-bold ph-sun text-lg sm:text-xl" id="theme-icon"></i>
        </button>
    </header>

    <!-- Main Conversational Interface (DO NOT change wrapper, question, and inputs IDs, expected by welcome.js) -->
    <main class="flex-grow relative flex flex-col items-center justify-center w-full max-w-3xl mx-auto p-4 sm:p-6" id="promptMain">
        
        <!-- Center Question Area (DO NOT change id) -->
        <div class="w-full text-center transition-all duration-300 mb-8" id="questionArea">
            <div class="w-16 h-16 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/30 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm">
                <i class="ph-fill ph-magic-wand text-3xl animate-pulse"></i>
            </div>
            <!-- Dynamic Question fields -->
            <h1 id="aiQuestion" class="font-heading text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900 dark:text-white mb-3 tracking-tight">Halo! Siapa nama toko atau bisnis Anda?</h1>
            <p id="aiSubQuestion" class="text-slate-500 dark:text-slate-400 text-sm sm:text-base font-light">Mari kita siapkan kasir cerdas Anda dalam hitungan detik.</p>
        </div>

        <div class="w-full max-w-xl mx-auto mt-2">
            
            <!-- Input container box (DO NOT change input and send buttons IDs) -->
            <div id="inputContainer" class="relative group rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-1.5 flex items-center focus-within:border-emerald-500 focus-within:ring-2 focus-within:ring-emerald-500/20 transition-all shadow-sm">
                <input type="text" id="chatInput" autocomplete="off"
                    class="w-full bg-transparent border-none px-4 py-3 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 focus:outline-none text-base sm:text-lg"
                    placeholder="Ketik nama tokomu di sini...">
                <button id="btnSend" class="bg-emerald-600 hover:bg-emerald-700 text-white dark:bg-emerald-500 dark:hover:bg-emerald-600 dark:text-slate-950 rounded-xl w-12 h-12 flex items-center justify-center shrink-0 transition-colors disabled:opacity-50 shadow-md">
                    <i class="ph-bold ph-arrow-up text-2xl font-bold"></i>
                </button>
            </div>

            <!-- Choice buttons dynamic grid container (DO NOT change ID) -->
            <div id="choicesContainer" class="grid grid-cols-1 sm:grid-cols-2 gap-3" style="display: none;"></div>
            
            <!-- Actions & Status footer -->
            <div class="mt-8 flex flex-col items-center gap-4">
                <!-- Step Back Button (DO NOT change ID) -->
                <button type="button" id="btnStepBack" style="display: none;" class="text-xs font-semibold text-slate-400 dark:text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 transition-colors flex items-center gap-1 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-900">
                    <i class="ph-bold ph-arrow-counterclockwise"></i> Ganti Jawaban Sebelumnya
                </button>
                <div class="text-xs text-slate-400 dark:text-slate-600 flex items-center gap-1.5 opacity-80">
                    <i class="ph-fill ph-shield-check text-emerald-600 dark:text-emerald-400"></i> Data Anda aman terenkripsi
                </div>
            </div>
        </div>
    </main>

    <!-- Fullscreen Loading Overlay (DO NOT change wrapper and text IDs) -->
    <div id="loadingOverlay" style="display: none;" class="fixed inset-0 bg-white/95 dark:bg-slate-950/95 backdrop-blur-sm z-[100] flex flex-col items-center justify-center transition-colors">
        <div class="relative w-20 h-20 mb-6">
            <div class="absolute inset-0 border-4 border-slate-200 dark:border-slate-800 rounded-full"></div>
            <div class="absolute inset-0 border-4 border-emerald-600 dark:border-emerald-500 rounded-full border-t-transparent animate-spin"></div>
        </div>
        <div id="loadingText" class="font-heading font-extrabold text-slate-900 dark:text-white text-xl animate-pulse">Menyiapkan workspace...</div>
    </div>

    <!-- Toast Notification alerts (DO NOT change wrapper and message IDs) -->
    <div id="customToast" class="fixed top-6 right-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl rounded-2xl px-5 py-4 flex items-center gap-3 transform translate-x-[150%] transition-transform duration-300 z-[110]">
        <i class="ph-fill ph-warning-circle text-amber-500 text-2xl"></i>
        <span id="toastMsg" class="font-semibold text-slate-900 dark:text-white text-sm"></span>
    </div>

    <!-- Alert Dialog Modals (DO NOT change wrapper, icon, title, description, and button IDs) -->
    <div id="customModal" class="custom-modal-overlay" onclick="window.closeCustomAlert()">
        <div class="custom-modal-box" onclick="event.stopPropagation()">
            <div id="modalIcon" class="w-16 h-16 rounded-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 flex items-center justify-center mx-auto mb-6 text-3xl"></div>
            <h3 id="modalTitle" class="font-heading font-bold text-slate-900 dark:text-white text-xl mb-3"></h3>
            <p id="modalDesc" class="text-slate-500 dark:text-slate-400 text-sm mb-6 leading-relaxed font-light"></p>
            <button id="modalBtn" onclick="window.closeCustomAlert()" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white dark:bg-emerald-500 dark:hover:bg-emerald-600 dark:text-slate-950 font-bold py-3.5 rounded-xl transition-colors shadow-md text-sm">Tutup</button>
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
                            // Style category / package choice buttons
                            if (btn.classList.contains('btn-choice-pill') || btn.className.trim() === '' || btn.className.includes('btn-primary')) {
                                btn.className = 'btn-choice-pill';
                            }
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
