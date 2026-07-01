<!DOCTYPE html>
<html lang="id" class="scroll-smooth dark" id="html-root">
<head>
    <script>
        // Use the same advanced expiry-based theme check
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Masuk | Pakaiapp</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    


    @vite(['resources/css/welcome.css'])
</head>
<body class="sustainability-platform bg-[var(--color-bg)] text-[var(--color-text)] font-sans antialiased min-h-screen flex flex-col relative overflow-hidden transition-colors duration-300">

    <!-- Background Decoration -->
    <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-[var(--color-primary)] opacity-10 rounded-full blur-[100px] pointer-events-none"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-[var(--color-secondary)] opacity-10 rounded-full blur-[100px] pointer-events-none"></div>

    <!-- Header Navigation -->
    <header class="w-full max-w-5xl mx-auto px-4 sm:px-6 py-6 flex justify-between items-center relative z-10">
        <!-- Logo -->
        <a href="/" class="flex items-center gap-2">
            <img src="/android-chrome-192x192.png" alt="Pakaiapp Logo" class="w-10 h-10 rounded-full shadow-sm">
            <span class="text-xl font-bold text-[var(--color-text)]">pakaiapp</span>
        </a>
        
        <div class="flex items-center gap-3 shrink-0">
            <!-- Theme Toggle Button -->
            <button id="theme-toggle" class="p-2 text-[var(--color-text-muted)] hover:text-[var(--color-primary)] rounded-full transition-colors" aria-label="Toggle Theme">
                <i class="ph-bold ph-sun text-xl" id="theme-icon"></i>
            </button>
            <a href="/" class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] text-sm font-semibold flex items-center gap-1.5 transition-colors">
                <i class="ph-bold ph-arrow-left"></i> Kembali
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow flex items-center justify-center px-4 sm:px-6 relative z-10 pb-12">
        <div class="bg-[var(--color-bg-card)] border border-[var(--color-border)] rounded-[2.5rem] w-full max-w-md p-8 sm:p-10 shadow-xl relative overflow-hidden transition-colors">
            
            <div class="relative z-10 text-center mb-8">
                <div class="w-16 h-16 bg-[var(--color-primary)]/10 text-[var(--color-primary)] rounded-2xl flex items-center justify-center mx-auto mb-5 border border-[var(--color-primary)]/20">
                    <i class="ph-fill ph-storefront text-3xl"></i>
                </div>
                <h1 class="font-heading text-2xl font-extrabold text-[var(--color-text)] mb-2">Masuk ke Toko</h1>
                <p class="text-[var(--color-text-muted)] text-sm font-light">Kelola kasir & pantau jualan hari ini.</p>
            </div>

            <!-- Form -->
            <form id="formLogin" class="relative z-10 space-y-6">
                @csrf
                <div>
                    <label for="login_input" class="block text-xs font-bold uppercase tracking-wider text-[var(--color-text-muted)] mb-2">Email atau Subdomain</label>
                    <div class="relative rounded-2xl border border-[var(--color-border)] bg-[var(--color-bg-alt)] p-1 flex items-center focus-within:border-[var(--color-primary)] focus-within:ring-2 focus-within:ring-[var(--color-primary)]/20 transition-all">
                        <div class="pl-3 text-[var(--color-text-muted)]"><i class="ph-bold ph-key text-lg"></i></div>
                        <input type="text" id="login_input" required autofocus
                            class="w-full bg-transparent border-none px-3 py-3 text-[var(--color-text)] placeholder-[var(--color-text-subtle)] focus:outline-none text-sm"
                            placeholder="budi@email.com / namatoko">
                    </div>
                </div>

                <button type="submit" id="btnSubmitLogin" class="w-full btn-primary font-bold py-3.5 rounded-2xl shadow-md transition-all flex justify-center items-center gap-2">
                    <i class="ph-bold ph-arrow-right text-lg"></i> Temukan Toko
                </button>
            </form>

            <!-- Store list results container -->
            <div id="storeListContainer" class="relative z-10 mt-8 pt-8 border-t border-[var(--color-border)]" style="display: none;">
                <h3 class="text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider mb-4 text-center">Toko Anda</h3>
                <div id="storeList" class="space-y-3">
                    <!-- Dynamic stores will render here -->
                </div>
            </div>

            <div class="relative z-10 mt-8 text-center text-sm text-[var(--color-text-muted)] font-light">
                Belum punya toko? <a href="/register" class="font-bold text-[var(--color-primary)] hover:underline">Daftar Sekarang</a>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="py-6 text-center text-xs text-[var(--color-text-muted)] relative z-10 transition-colors">
        &copy; {{ date('Y') }} Pakaiapp. Hak Cipta Dilindungi.
    </footer>

    <!-- Toast Notification alerts -->
    <div id="customToast" class="fixed top-6 right-6 bg-[var(--color-bg-card)] border border-[var(--color-border)] shadow-xl rounded-2xl px-5 py-4 flex items-center gap-3 transform translate-x-[150%] transition-transform duration-300 z-[110]">
        <i class="ph-fill ph-warning-circle text-amber-500 text-2xl"></i>
        <span id="toastMsg" class="font-semibold text-[var(--color-text)] text-sm"></span>
    </div>

    <!-- Alert Dialog Modals -->
    <div id="customModal" class="custom-modal-overlay" onclick="window.closeCustomAlert()">
        <div class="custom-modal-box bg-[var(--color-bg-card)] border border-[var(--color-border)]" onclick="event.stopPropagation()">
            <div id="modalIcon" class="w-16 h-16 rounded-full bg-[var(--color-bg-alt)] border border-[var(--color-border)] flex items-center justify-center mx-auto mb-6 text-3xl shadow-sm text-[var(--color-text)]"></div>
            <h3 id="modalTitle" class="font-heading font-bold text-[var(--color-text)] text-xl mb-3"></h3>
            <p id="modalDesc" class="text-[var(--color-text-muted)] text-sm mb-6 leading-relaxed font-light"></p>
            <button id="modalBtn" onclick="window.closeCustomAlert()" class="w-full btn-primary font-bold py-3.5 rounded-xl transition-colors shadow-md text-sm">Tutup</button>
        </div>
    </div>

    @vite(['resources/js/welcome.js'])
</body>
</html>
