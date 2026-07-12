@once
@php
    $isTenant = function_exists('tenant') && tenant('id');
    $appName = 'Pakaiapp';
    if ($isTenant) {
        $setting = \App\Tenant\Models\Core\StoreSetting::first();
        $appName = ($setting->name ?? tenant('id')) . ' Dashboard';
    }
@endphp

<!-- PWA Install Toast -->
<div id="pwa-toast" class="pwa-toast fixed bottom-4 left-1/2 z-[9999] hidden w-[90%] max-w-md -translate-x-1/2 flex-col gap-3 rounded-2xl border border-emerald-800/15 bg-white p-4 shadow-xl dark:border-slate-700 dark:bg-slate-900 sm:bottom-6">
    <div class="flex items-center gap-3">
        <div class="h-12 w-12 shrink-0 overflow-hidden rounded-xl bg-slate-100 dark:bg-slate-800">
            <img src="{{ asset('android-chrome-192x192.png') }}" alt="App Icon" class="h-full w-full object-cover">
        </div>
        <div class="flex flex-col">
            <strong class="mb-0.5 text-sm font-bold text-slate-900 dark:text-white">Install {{ $appName }}</strong>
            <span class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Tambahkan ke layar utama HP kamu untuk akses super cepat!</span>
        </div>
    </div>
    <div class="flex gap-2">
        <button id="pwa-dismiss" class="flex-1 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-bold text-slate-700 shadow-sm transition-colors hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-300 dark:hover:bg-slate-800">Nanti</button>
        <button id="pwa-install" class="flex-1 rounded-xl bg-emerald-800 px-3 py-2 text-sm font-bold text-white shadow-sm transition-colors hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:bg-emerald-500 dark:text-slate-950 dark:hover:bg-emerald-400">Install App</button>
    </div>
</div>



<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js').then(registration => {
                console.log('SW registered successfully');
            }).catch(error => {
                console.log('SW registration failed:', error);
            });
        });
    }

    window.deferredPrompt = null;
    
    document.addEventListener('DOMContentLoaded', () => {
        const pwaToast = document.getElementById('pwa-toast');
        const pwaInstallBtn = document.getElementById('pwa-install');
        const pwaDismissBtn = document.getElementById('pwa-dismiss');

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            window.deferredPrompt = e;
            
            document.querySelectorAll('.sidebar-pwa-install-btn').forEach(btn => {
                btn.style.setProperty('display', 'flex', 'important');
            });
            
            const lastDismissed = localStorage.getItem('pwaDismissedTs');
            const delayDaysMs = 1 * 24 * 60 * 60 * 1000;
            
            if (!lastDismissed || (Date.now() - parseInt(lastDismissed)) > delayDaysMs) {
                if (pwaToast) {
                    pwaToast.classList.remove('hidden');
                }
            }
        });

        window.installPwa = async function() {
            if (window.deferredPrompt) {
                if (pwaToast) {
                    pwaToast.classList.add('hidden');
                }
                
                window.deferredPrompt.prompt();
                const { outcome } = await window.deferredPrompt.userChoice;
                
                if (outcome === 'accepted') {
                    window.deferredPrompt = null;
                    document.querySelectorAll('.sidebar-pwa-install-btn').forEach(btn => {
                        btn.style.setProperty('display', 'none', 'important');
                    });
                }
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Cara Install App',
                        text: 'Untuk menginstall aplikasi ini, buka menu browser (ikon titik tiga di pojok kanan atas) lalu pilih "Tambahkan ke Layar Utama" (Add to Home screen).',
                        confirmButtonColor: '#22c55e'
                    });
                } else {
                    alert('Buka menu browser Anda dan pilih "Tambahkan ke Layar Utama" (Add to Home screen) untuk menginstall aplikasi.');
                }
            }
        };

        if (pwaInstallBtn) {
            pwaInstallBtn.addEventListener('click', () => window.installPwa());
        }

        if (pwaDismissBtn) {
            pwaDismissBtn.addEventListener('click', () => {
                pwaToast.classList.add('hidden');
                localStorage.setItem('pwaDismissedTs', Date.now().toString());
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Bisa Di-install Kapan Saja',
                        text: 'Anda bisa menginstall aplikasi ini kapan saja lewat menu "Install App" di sidebar kiri, atau lewat menu browser (Add to Home screen).',
                        confirmButtonColor: '#22c55e',
                        timer: 5000
                    });
                } else {
                    alert('Anda bisa menginstall aplikasi ini kapan saja lewat menu "Install App" di sidebar atau menu browser (Add to Home screen).');
                }
            });
        }
    });
</script>
@endonce
