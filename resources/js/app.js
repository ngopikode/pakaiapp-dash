import "@phosphor-icons/web/bold";
import "@phosphor-icons/web/fill";
import "@phosphor-icons/web/regular";
// Import all of Bootstrap’s JS
import * as bootstrap from 'bootstrap';
import NProgress from 'nprogress';
import Swal from 'sweetalert2';

window.bootstrap = bootstrap;
window.Swal = Swal;
NProgress.configure({showSpinner: false});

let navigateTimeout = null;

document.addEventListener('livewire:navigating', () => {
    NProgress.start();
    // Debounce showLoader by 150ms to prevent aggressive flashes on fast connections
    clearTimeout(navigateTimeout);
    navigateTimeout = setTimeout(() => {
        window.showLoader();
    }, 150);
});

document.addEventListener('livewire:navigated', () => {
    NProgress.done();
    clearTimeout(navigateTimeout);
    window.hideLoader();

    // Auto-close offcanvas mobile menu when navigation triggers
    const mobileSidebarEl = document.getElementById('mobileSidebar');
    if (mobileSidebarEl) {
        const offcanvasInstance = bootstrap.Offcanvas.getInstance(mobileSidebarEl);
        if (offcanvasInstance) {
            offcanvasInstance.hide();
        }
    }
});

window.showLoader = function () {
    const loader = document.getElementById('global-loader');
    if (loader) {
        loader.classList.add('active');
    }
};

window.hideLoader = function () {
    const loader = document.getElementById('global-loader');
    if (loader) {
        loader.classList.remove('active');
    }
};

function initDesktopSidebarToggle() {
    const sidebarToggle = document.getElementById('sidebarToggle');

    const sidebarStatus = localStorage.getItem('sb|sidebar-toggle');
    if (sidebarStatus === 'true') {
        document.body.classList.add('sb-sidenav-toggled');
    }

    if (sidebarToggle) {
        sidebarToggle.onclick = function (e) {
            e.preventDefault();

            const isToggled = document.body.classList.toggle('sb-sidenav-toggled');
            localStorage.setItem('sb|sidebar-toggle', isToggled);
        };
    }
}

document.addEventListener('DOMContentLoaded', initDesktopSidebarToggle);
document.addEventListener('livewire:navigated', initDesktopSidebarToggle);


document.addEventListener('alpine:init', () => {
    Alpine.data('themeToggle', () => ({
        theme: localStorage.getItem('theme') || 'light',

        init() {
            // Pasang tema secara otomatis ke tag <html> saat web dimuat
            document.documentElement.setAttribute('data-bs-theme', this.theme);
        },

        toggleTheme() {
            // Ubah state, simpan ke local storage, dan terapkan ke <html>
            this.theme = this.theme === 'dark' ? 'light' : 'dark';
            localStorage.setItem('theme', this.theme);
            document.documentElement.setAttribute('data-bs-theme', this.theme);
        }
    }));
});

document.addEventListener('livewire:navigated', () => {

    // Fungsi untuk membuat dan menampilkan toast ala "Island"
    // ==========================================
    // 1. MODERN ISLAND TOAST NOTIFICATION (ULTRA SMOOTH)
    // ==========================================
    window.showIslandToast = function (message, type = 'success') {
        const existingToast = document.getElementById('modern-island-toast');
        if (existingToast) existingToast.remove();

        let iconHtml;
        if (type === 'success') {
            iconHtml = '<i class="bi bi-check-circle-fill text-success fs-5"></i>';
        } else if (type === 'error' || type === 'danger') {
            iconHtml = '<i class="bi bi-x-circle-fill text-danger fs-5"></i>';
        } else if (type === 'warning') {
            iconHtml = '<i class="bi bi-exclamation-triangle-fill text-warning fs-5"></i>';
        } else {
            iconHtml = '<i class="bi bi-info-circle-fill text-info fs-5"></i>';
        }

        const toast = document.createElement('div');
        toast.id = 'modern-island-toast';

        // CSS dengan Optimasi GPU dan Animasi Apple-like Spring
        toast.style.cssText = `
            position: fixed;
            top: 32px; /* Agak turun sedikit agar lebih elegan */
            left: 50%;
            transform: translate(-50%, -100px) scale(0.85); /* Posisi awal: di atas dan sedikit mengecil */
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            color: #ffffff;
            padding: 12px 24px;
            border-radius: 100px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15), inset 0 1px 3px rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 9999;
            font-size: 0.95rem;
            font-weight: 500;
            letter-spacing: 0.3px;
            opacity: 0;
            width: max-content;
            max-width: 90vw;
            will-change: transform, opacity; /* Paksa GPU untuk render (Anti-Jedag) */
            /* Pisahkan transisi: transform pakai efek spring/mantul, opacity pakai fade biasa */
            transition: transform 0.6s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.4s ease-out;
        `;

        toast.innerHTML = `
            ${iconHtml}
            <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; padding-bottom: 1px;">
                ${message}
            </span>
        `;

        document.body.appendChild(toast);

        // KUNCI ANTI-JEDAG: Beri jeda 10ms agar browser menggambar elemennya dulu
        setTimeout(() => {
            toast.style.transform = 'translate(-50%, 0) scale(1)'; /* Turun ke posisi asli dan membesar */
            toast.style.opacity = '1';
        }, 10);

        // Animasi keluar setelah 3 detik
        setTimeout(() => {
            toast.style.transform = 'translate(-50%, -100px) scale(0.85)'; /* Naik dan mengecil lagi */
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 600); // Tunggu animasi selesai baru buang dari HTML
        }, 3000);
    };

    // Daftarkan listener Livewire untuk memanggil fungsi Island Toast kita
    Livewire.on('notify', (event) => {
        const data = Array.isArray(event) ? event[0] : event;
        window.showIslandToast(data.message, data.type);
    });

    // HOOK LIVEWIRE UNTUK LOADER OTOMATIS
    Livewire.hook('commit', ({commit, succeed, fail}) => {
        // Tambahkan method lain yang dirasa berat
        const heavyActions = ['save', 'deleteProduct', 'deleteCategory', 'processPayment', 'updateStatus', 'openPaymentModal'];

        const isHeavy = commit.calls.some(call => heavyActions.includes(call.method));

        if (isHeavy) {
            window.showLoader();
            succeed(() => window.hideLoader());
            fail(() => window.hideLoader());
        }
    });

    // Listener manual untuk dispatch modal dari Livewire
    window.addEventListener('openModal', () => window.showLoader());
    window.addEventListener('trigger-payment-modal', () => window.showLoader());
    window.addEventListener('show-bootstrap-modal', () => window.hideLoader());

});

// ==========================================
// ADVANCED NETWORK STATE HANDLER
// ==========================================
let offlineBanner = null;

function updateNetworkBanner(state) {
    // state = 'offline', 'slow', 'online', 'reconnecting'
    const messages = {
        'offline': { text: 'Koneksi Terputus...', icon: 'bi-wifi-off', bg: 'rgba(220, 53, 69, 0.95)' },
        'slow': { text: 'Koneksi Lambat...', icon: 'bi-reception-1', bg: 'rgba(249, 115, 22, 0.95)' },
        'reconnecting': { text: 'Menghubungkan kembali...', icon: 'bi-arrow-repeat', bg: 'rgba(13, 110, 253, 0.95)' }
    };

    if (state === 'online') {
        if (offlineBanner) {
            // Animasi keluar
            offlineBanner.style.transform = 'translate(-50%, -50px)';
            offlineBanner.style.opacity = '0';
            setTimeout(() => {
                if (offlineBanner) {
                    offlineBanner.remove();
                    offlineBanner = null;
                }
            }, 400);
            
            if (typeof window.showIslandToast === 'function') {
                window.showIslandToast('Koneksi internet stabil', 'success');
            }
        }
        return;
    }

    const cfg = messages[state];

    if (!offlineBanner) {
        offlineBanner = document.createElement('div');
        offlineBanner.id = 'global-offline-banner';
        offlineBanner.style.cssText = `
            position: fixed;
            top: 15px;
            left: 50%;
            transform: translate(-50%, -50px);
            z-index: 1060;
            pointer-events: none;
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        `;
        document.body.appendChild(offlineBanner);
        
        setTimeout(() => {
            if(offlineBanner) {
                offlineBanner.style.transform = 'translate(-50%, 0)';
                offlineBanner.style.opacity = '1';
            }
        }, 10);
    }

    offlineBanner.innerHTML = `
        <div class="px-4 py-2 rounded-pill shadow-lg d-flex align-items-center fw-bold text-white border border-light border-opacity-25" 
             style="background: ${cfg.bg}; backdrop-filter: blur(10px); font-size: 0.85rem; letter-spacing: 0.5px; transition: background 0.4s ease;">
            <i class="bi ${cfg.icon} fs-5 me-2 ${state === 'reconnecting' ? 'spin-icon' : ''}"></i> 
            <span>${cfg.text}</span>
        </div>
        <style>
            @keyframes spin-icon { 100% { transform: rotate(360deg); } }
            .spin-icon { display: inline-block; animation: spin-icon 1.2s linear infinite; }
        </style>
    `;
}

function checkNetworkSpeed() {
    if (!navigator.onLine) {
        updateNetworkBanner('offline');
        return;
    }
    
    if (navigator.connection) {
        const type = navigator.connection.effectiveType;
        if (type === 'slow-2g' || type === '2g') {
            updateNetworkBanner('slow');
            return;
        }
    }
    
    updateNetworkBanner('online');
}

window.addEventListener('offline', () => {
    updateNetworkBanner('offline');
    if (typeof window.hideLoader === 'function') window.hideLoader();
});

window.addEventListener('online', () => {
    // Tampilkan state reconnecting sebentar
    updateNetworkBanner('reconnecting');
    // Cek ulang speed setelah delay kecil
    setTimeout(() => checkNetworkSpeed(), 2000); 
});

if (navigator.connection) {
    navigator.connection.addEventListener('change', checkNetworkSpeed);
}
