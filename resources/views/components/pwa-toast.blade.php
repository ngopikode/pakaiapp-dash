@php
    $isTenant = function_exists('tenant') && tenant('id');
    $appName = 'Pakaiapp';
    if ($isTenant) {
        $setting = \App\Models\StoreSetting::first();
        $appName = ($setting->name ?? tenant('id')) . ' Dashboard';
    }
@endphp

<!-- PWA Install Toast -->
<div id="pwa-toast" class="pwa-toast" style="display: none;">
    <div class="pwa-toast-content">
        <div class="pwa-icon">
            <img src="{{ asset('android-chrome-192x192.png') }}" alt="App Icon">
        </div>
        <div class="pwa-text">
            <strong>Install {{ $appName }}</strong>
            <span>Tambahkan ke layar utama HP kamu untuk akses super cepat!</span>
        </div>
    </div>
    <div class="pwa-toast-actions">
        <button id="pwa-dismiss" class="pwa-btn-dismiss">Nanti</button>
        <button id="pwa-install" class="pwa-btn-install">Install App</button>
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
    background: #ffffff;
    border-radius: 16px;
    padding: 16px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.15);
    z-index: 99999;
    display: flex;
    flex-direction: column;
    gap: 14px;
    transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    border: 1px solid rgba(0,0,0,0.05);
    font-family: inherit;
}
.pwa-toast.show {
    transform: translate(-50%, 0);
}
.pwa-toast-content {
    display: flex;
    align-items: center;
    gap: 12px;
}
.pwa-icon img {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    object-fit: contain;
}
.pwa-text {
    display: flex;
    flex-direction: column;
}
.pwa-text strong {
    font-size: 15px;
    color: #1f2937;
    margin-bottom: 2px;
    font-weight: 700;
}
.pwa-text span {
    font-size: 12.5px;
    color: #6b7280;
    line-height: 1.4;
}
.pwa-toast-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}
.pwa-btn-dismiss, .pwa-btn-install {
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 13.5px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
}
.pwa-btn-dismiss {
    background: #f3f4f6;
    color: #4b5563;
}
.pwa-btn-dismiss:hover { background: #e5e7eb; }
.pwa-btn-install {
    background: #22c55e;
    color: white;
}
.pwa-btn-install:hover { background: #16a34a; }
</style>

<script>
    // Register Service Worker for PWA
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
    const pwaToast = document.getElementById('pwa-toast');
    const pwaInstallBtn = document.getElementById('pwa-install');
    const pwaDismissBtn = document.getElementById('pwa-dismiss');

    window.addEventListener('beforeinstallprompt', (e) => {
        // Prevent Chrome 67 and earlier from automatically showing the prompt
        e.preventDefault();
        // Stash the event so it can be triggered later.
        window.deferredPrompt = e;
        
        // Show install button in sidebar if it exists
        const sidebarBtn = document.getElementById('sidebar-pwa-install');
        if (sidebarBtn) {
            sidebarBtn.style.display = 'flex';
        }
        
        // Check if user has dismissed it recently
        const lastDismissed = localStorage.getItem('pwaDismissedTs');
        const delayDaysMs = 1 * 24 * 60 * 60 * 1000; // 1 day
        
        if (!lastDismissed || (Date.now() - parseInt(lastDismissed)) > delayDaysMs) {
            if (pwaToast) {
                pwaToast.style.display = 'flex';
                // Slight delay to allow display block to render before CSS transform
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
            console.log(`User response to the install prompt: ${outcome}`);
            
            if (outcome === 'accepted') {
                window.deferredPrompt = null;
                const sidebarBtn = document.getElementById('sidebar-pwa-install');
                if (sidebarBtn) sidebarBtn.style.display = 'none';
            }
        } else {
            // Fallback alert if deferredPrompt is not available
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
</script>
