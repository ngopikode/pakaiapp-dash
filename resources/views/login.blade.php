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
    
    <title>Masuk Toko | Pakaiapp POS</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    
    <meta name="description" content="Masuk ke dashboard Pakaiapp POS untuk mengelola toko, melihat laporan penjualan realtime, dan pantau performa bisnis dari mana saja.">
    <meta name="keywords" content="login pakaiapp, dashboard kasir, masuk pos online">
    <link rel="canonical" href="https://www.pakaiapp.online/login">

    <!-- Fonts & CDNs -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    

    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    @vite(['resources/css/welcome.css'])
</head>
<body class="bg-zinc-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-sans antialiased min-h-screen flex flex-col relative overflow-hidden transition-colors duration-300">

    <!-- Background Glow Decor -->
    <div class="absolute top-1/4 -left-32 w-96 h-96 bg-emerald-500/10 dark:bg-emerald-500/5 rounded-full blur-[100px] -z-10"></div>
    <div class="absolute bottom-0 right-0 w-1/2 h-1/2 bg-indigo-500/10 dark:bg-indigo-500/5 rounded-full blur-[120px] -z-10"></div>

    <!-- Header Navigation -->
    <header class="w-full max-w-5xl mx-auto px-4 sm:px-6 py-4 sm:py-6 flex justify-between items-center relative z-10">
        <!-- Logo -->
        <a href="/" class="flex items-center gap-1.5 sm:gap-2">
            <i class="ph-fill ph-circles-four text-emerald-600 dark:text-emerald-400 text-xl sm:text-2xl shrink-0"></i>
            <span class="font-heading font-extrabold text-base sm:text-xl tracking-tight text-slate-900 dark:text-white">pakaiapp<span class="text-emerald-500 hidden sm:inline">.online</span></span>
        </a>
        
        <div class="flex items-center gap-2 sm:gap-3 shrink-0">
            <!-- Theme Toggle Button (DO NOT change id, expected by welcome.js) -->
            <button id="theme-toggle" class="p-1.5 sm:p-2 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors" aria-label="Toggle Theme">
                <i class="ph-bold ph-sun text-lg sm:text-xl" id="theme-icon"></i>
            </button>
            <a href="/" class="text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white text-xs sm:text-sm font-semibold flex items-center gap-1 transition-colors px-1.5 sm:px-2 py-1 rounded-lg">
                <i class="ph-bold ph-arrow-left"></i> Kembali
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow flex items-center justify-center px-4 sm:px-6 relative z-10 pb-12">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2.5rem] w-full max-w-md p-8 sm:p-10 shadow-xl relative overflow-hidden transition-colors">
            
            <div class="relative z-10 text-center mb-8">
                <div class="w-16 h-16 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-emerald-100 dark:border-emerald-900/30">
                    <i class="ph-fill ph-storefront text-3xl"></i>
                </div>
                <h1 class="font-heading text-2xl font-extrabold text-slate-900 dark:text-white">Masuk ke Toko</h1>
                <p class="text-slate-500 dark:text-slate-400 text-sm mt-2 font-light">Masukkan email terdaftar atau subdomain toko Anda.</p>
            </div>

            <!-- Form (DO NOT change input and submit IDs, expected by welcome.js) -->
            <form id="formLogin" class="relative z-10 space-y-5">
                @csrf
                <div>
                    <label for="login_input" class="block text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-2">Email Pemilik atau Subdomain</label>
                    <div class="relative rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 p-1 flex items-center focus-within:border-emerald-500 focus-within:ring-2 focus-within:ring-emerald-500/20 transition-all">
                        <div class="pl-3 text-slate-400"><i class="ph-bold ph-key text-lg"></i></div>
                        <input type="text" id="login_input" required autofocus
                            class="w-full bg-transparent border-none px-3 py-3 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 focus:outline-none text-sm"
                            placeholder="budi@email.com atau namatoko">
                    </div>
                    <span class="text-[0.7rem] text-slate-400 dark:text-slate-500 block mt-2 font-light">Masukkan email untuk melihat semua toko Anda, atau subdomain toko langsung.</span>
                </div>

                <button type="submit" id="btnSubmitLogin" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white dark:bg-emerald-500 dark:hover:bg-emerald-600 dark:text-slate-950 font-bold py-3.5 rounded-2xl shadow-md transition-all hover:scale-[1.01] flex justify-center items-center gap-2">
                    <i class="ph-bold ph-arrow-right"></i> Temukan Toko
                </button>
            </form>

            <!-- Store list results container (DO NOT change IDs, expected by welcome.js) -->
            <div id="storeListContainer" class="relative z-10 mt-6 pt-6 border-t border-slate-100 dark:border-slate-800" style="display: none;">
                <h3 class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-4">Toko Anda Yang Terdaftar</h3>
                <div id="storeList" class="space-y-3">
                    <!-- Dynamic stores will render here -->
                </div>
            </div>

            <div class="relative z-10 mt-8 text-center text-sm text-slate-500 dark:text-slate-400 font-light">
                Belum punya toko? <a href="/register" class="font-bold text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300">Daftar Sekarang</a>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="py-6 text-center text-xs text-slate-400 dark:text-slate-600 relative z-10 transition-colors">
        &copy; {{ date('Y') }} Pakaiapp.online. Hak Cipta Dilindungi.
    </footer>

    <!-- Toast Notification alerts -->
    <div id="customToast" class="fixed top-6 right-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl rounded-2xl px-5 py-4 flex items-center gap-3 transform translate-x-[150%] transition-transform duration-300 z-[110]">
        <i class="ph-fill ph-warning-circle text-amber-500 text-2xl"></i>
        <span id="toastMsg" class="font-semibold text-slate-900 dark:text-white text-sm"></span>
    </div>

    <!-- Alert Dialog Modals -->
    <div id="customModal" class="custom-modal-overlay" onclick="window.closeCustomAlert()">
        <div class="custom-modal-box" onclick="event.stopPropagation()">
            <div id="modalIcon" class="w-16 h-16 rounded-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 flex items-center justify-center mx-auto mb-6 text-3xl shadow-sm"></div>
            <h3 id="modalTitle" class="font-heading font-bold text-slate-900 dark:text-white text-xl mb-3"></h3>
            <p id="modalDesc" class="text-slate-500 dark:text-slate-400 text-sm mb-6 leading-relaxed font-light"></p>
            <button id="modalBtn" onclick="window.closeCustomAlert()" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white dark:bg-emerald-500 dark:hover:bg-emerald-600 dark:text-slate-950 font-bold py-3.5 rounded-xl transition-colors shadow-md text-sm">Tutup</button>
        </div>
    </div>

    @vite(['resources/js/welcome.js'])
</body>
</html>
