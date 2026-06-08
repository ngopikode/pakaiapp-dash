<div wire:poll.15s class="pb-5 min-vh-100">

    @if(app('isMobile'))
        @include('pages.tenant.⚡dashboard.mobile-view')
    @else
        @include('pages.tenant.⚡dashboard.desktop-view')
    @endif

    {{-- Premium Top Up Modal --}}
    <div class="modal fade" id="topUpModal" tabindex="-1" aria-labelledby="topUpModalLabel" aria-hidden="true"
         wire:ignore>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg"
                 style="border-radius: 1.5rem; background-color: var(--bs-card-bg);">
                <div class="modal-header border-0 pb-0 pt-4 px-4 position-relative">
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-4 pt-2">
                    {{-- Decorative Wallet Icon --}}
                    <div
                        class="d-inline-flex align-items-center justify-content-center bg-warning bg-opacity-10 rounded-circle mb-3 p-3 text-warning shadow-sm"
                        style="width: 70px; height: 70px; border: 1px solid rgba(182, 115, 50, 0.15);">
                        <i class="bi bi-wallet2 fs-2" style="color: var(--brand-caramel, #b45309);"></i>
                    </div>

                    <h5 class="modal-title fw-bold text-body mb-2" id="topUpModalLabel"
                        style="font-family: var(--font-serif), sans-serif; font-size: 1.25rem;">
                        Isi Ulang Saldo Pakaiapp
                    </h5>

                    <p class="text-secondary small mb-4 px-2">
                        Untuk melakukan pengisian ulang saldo (Top Up) akun toko Anda, silakan hubungi Administrator
                        kami melalui WhatsApp.
                    </p>

                    <div class="bg-body-tertiary p-3 rounded-4 border mb-4 text-start d-flex align-items-center gap-3"
                         style="border-color: var(--bs-border-color-translucent) !important;">
                        <div
                            class="bg-success bg-opacity-10 text-success rounded-circle p-2.5 d-flex align-items-center justify-content-center"
                            style="width: 42px; height: 42px;">
                            <i class="bi bi-whatsapp fs-5"></i>
                        </div>
                        <div>
                            <span class="text-secondary small d-block" style="font-size: 0.72rem;">WhatsApp Admin</span>
                            <span class="fw-black text-body" style="letter-spacing: 0.5px;">+62 851-7244-1544</span>
                        </div>
                    </div>

                    <a href="https://wa.me/6285172441544?text=Halo%20Admin%2C%20saya%20ingin%20melakukan%20top%20up%20saldo%20Pakaiapp%20untuk%20toko%20saya."
                       target="_blank"
                       class="btn w-100 rounded-pill fw-bold py-2.5 d-flex align-items-center justify-content-center gap-2 text-white border-0 transition-all hover-translate"
                       style="background: linear-gradient(135deg, #25D366, #128C7E); font-size: 0.9rem; box-shadow: 0 4px 15px rgba(37, 211, 102, 0.2);">
                        <i class="bi bi-whatsapp fs-5"></i> Hubungi Admin Sekarang
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@assets
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<style>
    /* Custom Fixed Contrast Utilities (Overrides bootstrap dark-mode text-white inversion) */
    .text-white-fixed {
        color: #ffffff !important;
    }

    .text-white-fixed-75 {
        color: rgba(255, 255, 255, 0.75) !important;
    }

    .text-white-fixed-50 {
        color: rgba(255, 255, 255, 0.5) !important;
    }

    .text-white-fixed-25 {
        color: rgba(255, 255, 255, 0.25) !important;
    }

    /* Premium Card Elements */
    .dash-card {
        border-radius: 1.5rem;
        box-shadow: 0 8px 30px rgba(50, 30, 20, 0.02);
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        border: 1px solid var(--bs-border-color-translucent) !important;
        background-color: var(--bs-card-bg);
    }

    .dash-card:hover {
        box-shadow: 0 16px 40px rgba(50, 30, 20, 0.08);
        transform: translateY(-4px);
        border-color: rgba(var(--bs-primary-rgb), 0.15) !important;
    }

    /* Glassmorphism Accents & Overlays */
    .glass-panel {
        background: rgba(var(--bs-body-bg-rgb), 0.7);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid var(--bs-border-color-translucent);
    }

    /* Coffee & Sunset Gradients */
    .bg-gradient-caramel {
        background-color: #F97316 !important;
        background-image: none !important;
        box-shadow: 0 4px 12px rgba(249, 115, 22, 0.15) !important;
    }

    .bg-gradient-espresso {
        background-color: #1E293B !important;
        background-image: none !important;
        box-shadow: 0 4px 12px rgba(30, 41, 59, 0.15) !important;
    }

    /* Premium Wallet Card (Copper Theme) */
    .bg-gradient-copper-card {
        position: relative;
        background-color: #1E293B !important;
        background-image: none !important;
        color: #F9F7F5 !important;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05) !important;
        border: 1px solid var(--bs-border-color-translucent) !important;
        overflow: hidden;
        z-index: 1;
    }

    .bg-gradient-copper-card::before {
        content: "";
        position: absolute;
        top: -50%;
        right: -20%;
        width: 250px;
        height: 250px;
        background: radial-gradient(circle, rgba(249, 115, 22, 0.1) 0%, transparent 70%);
        z-index: -1;
        pointer-events: none;
    }

    .bg-gradient-copper-card .wallet-chip {
        width: 38px;
        height: 28px;
        background-color: #F97316;
        border-radius: 6px;
        position: relative;
        box-shadow: inset 0 1px 2px rgba(255, 255, 255, 0.2);
    }

    .bg-gradient-copper-card .wallet-chip::after {
        content: "";
        position: absolute;
        top: 4px;
        left: 4px;
        right: 4px;
        bottom: 4px;
        border: 1px solid rgba(0, 0, 0, 0.1);
        border-radius: 4px;
    }

    /* Aesthetic Transaction Ledger */
    .list-group-item-custom {
        border: 1px solid var(--bs-border-color-translucent);
        border-radius: 1.25rem !important;
        margin-bottom: 0.6rem;
        background-color: var(--bs-secondary-bg);
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .list-group-item-custom:hover {
        border-color: rgba(var(--bs-primary-rgb), 0.15) !important;
        background-color: var(--bs-tertiary-bg) !important;
        transform: translateX(4px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
    }

    /* Ranked Lists (Top Sellers) */
    .rank-badge {
        width: 26px;
        height: 26px;
        font-size: 0.78rem;
        font-weight: 800;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .rank-gold {
        background-color: #F59E0B;
        background-image: none !important;
        color: #1c1400;
        box-shadow: 0 2px 6px rgba(245, 158, 11, 0.2);
    }

    .rank-silver {
        background-color: #94A3B8;
        background-image: none !important;
        color: #0f172a;
        box-shadow: 0 2px 6px rgba(148, 163, 184, 0.2);
    }

    .rank-bronze {
        background-color: #D97706;
        background-image: none !important;
        color: #1c0d00;
        box-shadow: 0 2px 6px rgba(217, 119, 6, 0.2);
    }

    .rank-default {
        background-color: var(--bs-tertiary-bg);
        color: var(--bs-secondary-color);
    }

    /* Pill Badges with Glows */
    .badge-pill-glow {
        font-size: 0.72rem;
        font-weight: 700;
        border-radius: 100px;
        padding: 0.35rem 0.85rem;
        border-width: 1px;
        border-style: solid;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .badge-pill-success {
        background-color: rgba(25, 135, 84, 0.06) !important;
        border-color: rgba(25, 135, 84, 0.15) !important;
        color: #198754 !important;
    }

    .badge-pill-warning {
        background-color: rgba(245, 158, 11, 0.06) !important;
        border-color: rgba(245, 158, 11, 0.15) !important;
        color: #D97706 !important;
    }

    .badge-pill-danger {
        background-color: rgba(220, 53, 69, 0.06) !important;
        border-color: rgba(220, 53, 69, 0.15) !important;
        color: #DC3545 !important;
    }

    [data-bs-theme="dark"] .badge-pill-success {
        background-color: rgba(46, 204, 113, 0.1) !important;
        border-color: rgba(46, 204, 113, 0.2) !important;
        color: #2ecc71 !important;
    }

    [data-bs-theme="dark"] .badge-pill-warning {
        background-color: rgba(241, 196, 15, 0.1) !important;
        border-color: rgba(241, 196, 15, 0.2) !important;
        color: #f1c40f !important;
    }

    [data-bs-theme="dark"] .badge-pill-danger {
        background-color: rgba(231, 76, 60, 0.1) !important;
        border-color: rgba(231, 76, 60, 0.2) !important;
        color: #e74c3c !important;
    }

    /* Quick Action Buttons */
    .quick-action-btn {
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        border: 1px solid var(--bs-border-color-translucent) !important;
        background-color: var(--bs-secondary-bg);
        border-radius: 1.25rem;
        text-decoration: none !important;
    }

    .quick-action-btn:hover {
        transform: translateY(-2px);
        border-color: rgba(var(--bs-primary-rgb), 0.15) !important;
        background-color: var(--bs-tertiary-bg);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
    }

    .quick-action-btn:hover .icon-wrapper {
        transform: scale(1.1);
        background-color: var(--brand-caramel) !important;
        color: #ffffff !important;
    }

    .quick-action-btn .icon-wrapper {
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    /* Custom Live Sync Animations & Pulse Badges */
    .pulse-glow-caramel {
        animation: pulse-glow-brand 2s infinite;
    }

    @keyframes pulse-glow-brand {
        0% {
            transform: scale(0.9);
            box-shadow: 0 0 0 0 rgba(182, 115, 50, 0.7);
        }
        70% {
            transform: scale(1.15);
            box-shadow: 0 0 0 8px rgba(182, 115, 50, 0);
        }
        100% {
            transform: scale(0.9);
            box-shadow: 0 0 0 0 rgba(182, 115, 50, 0);
        }
    }

    .active-glow-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #198754;
        box-shadow: 0 0 8px rgba(25, 135, 84, 0.8);
        display: inline-block;
    }

    /* Page Store Name — solid, no gradient text */
    .text-brand-gradient {
        color: var(--bs-body-color) !important;
        background: none !important;
        -webkit-text-fill-color: var(--bs-body-color) !important;
        -webkit-background-clip: unset !important;
        background-clip: unset !important;
    }

    /* Custom Header Buttons */
    .btn-dashboard-header {
        font-weight: 700;
        border-radius: 100px;
        padding: 0.65rem 1.5rem;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        font-size: 0.88rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-dashboard-header.btn-outline {
        background-color: var(--bs-secondary-bg);
        border: 1px solid var(--bs-border-color) !important;
        color: var(--bs-body-color);
    }

    .btn-dashboard-header.btn-outline:hover {
        border-color: var(--brand-caramel) !important;
        color: var(--brand-caramel);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
    }

    .btn-dashboard-header.btn-filled {
        background-color: var(--brand-caramel, #B67332);
        color: #ffffff !important;
        border: none !important;
        box-shadow: 0 4px 14px rgba(182, 115, 50, 0.3);
    }

    .btn-dashboard-header.btn-filled:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(182, 115, 50, 0.4);
        background-color: #9a5f28;
        opacity: 1;
    }

    /* Responsive Overrides */
    @media (max-width: 768px) {
        .display-6 {
            font-size: 1.85rem !important;
        }

        .btn-dashboard-header {
            width: 100%;
            justify-content: center;
        }

        .list-group-item-custom {
            padding: 1.15rem !important;
        }
    }
</style>
@endassets

@script
<script>
    setTimeout(() => {
        const chartDataRaw = @json($chartData ?? '[]');
        let chartData = [];
        try {
            chartData = JSON.parse(chartDataRaw);
        } catch (e) {
        }

        if (chartData.length > 0 && document.querySelector('#revenueChart')) {
            const isDarkMode = document.documentElement.getAttribute('data-bs-theme') === 'dark';
            const textColor = isDarkMode ? '#9ca3af' : '#6c757d';
            const gridColor = isDarkMode ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';

            const options = {
                series: [{
                    name: 'Pendapatan',
                    data: chartData.map(item => item.revenue)
                }],
                chart: {
                    type: 'area',
                    height: 250,
                    toolbar: {show: false},
                    fontFamily: 'inherit',
                    parentHeightOffset: 0,
                    background: 'transparent'
                },
                colors: ['#b45309'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.4,
                        opacityTo: 0.05,
                        stops: [0, 90, 100]
                    }
                },
                dataLabels: {enabled: false},
                stroke: {curve: 'smooth', width: 3},
                xaxis: {
                    categories: chartData.map(item => item.date),
                    axisBorder: {show: false},
                    axisTicks: {show: false},
                    labels: {style: {colors: textColor}}
                },
                yaxis: {
                    labels: {
                        style: {colors: textColor},
                        formatter: function (val) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                        }
                    }
                },
                grid: {
                    borderColor: gridColor,
                    strokeDashArray: 4,
                    yaxis: {lines: {show: true}}
                },
                theme: {
                    mode: isDarkMode ? 'dark' : 'light'
                },
                tooltip: {
                    theme: isDarkMode ? 'dark' : 'light',
                    y: {
                        formatter: function (val) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                        }
                    }
                }
            };

            const chart = new ApexCharts(document.querySelector('#revenueChart'), options);
            chart.render();
        }
    }, 100);
</script>
@endscript
