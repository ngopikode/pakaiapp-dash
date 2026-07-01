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
<div id="pwa-toast" class="pwa-toast bg-body-tertiary border shadow-lg" style="display: none;">
    <div class="d-flex align-items-center gap-3">
        <div class="pwa-icon flex-shrink-0" style="width: 48px; height: 48px; border-radius: 12px; overflow: hidden; background: var(--bs-secondary-bg);">
            <img src="{{ asset('android-chrome-192x192.png') }}" alt="App Icon" class="w-100 h-100 object-fit-cover">
        </div>
        <div class="d-flex flex-column">
            <strong class="text-body fw-bold mb-1" style="font-size: 0.95rem;">Install {{ $appName }}</strong>
            <span class="text-secondary" style="font-size: 0.8rem; line-height: 1.3;">Tambahkan ke layar utama HP kamu untuk akses super cepat!</span>
        </div>
    </div>
    <div class="d-flex gap-2 mt-1">
        <button id="pwa-dismiss" class="btn border fw-bold px-3 py-2 rounded-3 text-body flex-grow-1" style="font-size: 0.85rem; background-color: var(--bs-body-bg);">Nanti</button>
        <button id="pwa-install" class="btn btn-success fw-bold px-3 py-2 rounded-3 flex-grow-1 text-white shadow-sm" style="font-size: 0.85rem;">Install App</button>
    </div>
</div>

<style>
.pwa-toast {
    position: fixed;
    bottom: 24px;
    left: 50%;
    transform: translate(-50%, 150%);
    width: calc(100% - 48px);
    max-width: 400px;
    border-radius: 16px;
    padding: 16px;
    z-index: 99999;
    display: flex;
    flex-direction: column;
    gap: 14px;
    transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    font-family: inherit;
    border-color: var(--bs-border-color-translucent) !important;
}
.pwa-toast.show {
    transform: translate(-50%, 0);
}

@media (max-width: 768px) {
    .pwa-toast {
        bottom: calc(var(--bottom-nav-height, 65px) + 20px) !important;
    }
}
</style>

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
                    pwaToast.style.display = 'flex';
                    setTimeout(() => {
                        pwaToast.classList.add('show');
                    }, 500);
                }
            }
        });

        window.installPwa = async function() {
            if (window.deferredPrompt) {
                if (pwaToast) {
                    pwaToast.classList.remove('show');
                    setTimeout(() => { pwaToast.style.display = 'none'; }, 400);
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
                pwaToast.classList.remove('show');
                setTimeout(() => { pwaToast.style.display = 'none'; }, 400);
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
