<div class="pb-5 min-vh-100">

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
                        <i class="ph-fill ph-wallet fs-2" style="color: #10B981;"></i>
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
                            <i class="ph-fill ph-whatsapp-logo fs-5"></i>
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
                        <i class="ph-fill ph-whatsapp-logo fs-5"></i> Hubungi Admin Sekarang
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@assets
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<style>
    {!! file_get_contents(resource_path('views/pages/tenant/⚡dashboard/dashboard.css')) !!}
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

        if (document.querySelector('#revenueChart')) {
            const isDarkMode = document.documentElement.getAttribute('data-bs-theme') === 'dark';
            const textColor = isDarkMode ? '#9ca3af' : '#6c757d';
            const gridColor = isDarkMode ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';

            // Generate last 7 days zero-filled data if empty or invalid to render empty grid lines
            if (!chartData || chartData.length === 0) {
                chartData = [];
                for (let i = 6; i >= 0; i--) {
                    const d = new Date();
                    d.setDate(d.getDate() - i);
                    const label = d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short' });
                    chartData.push({ date: label, revenue: 0 });
                }
            }

            const revenues = chartData.map(item => item.revenue);
            const maxRevenue = Math.max(...revenues, 0);

            let yaxisConfig = {
                min: 0,
                forceNiceScale: true,
                decimalsInFloat: 0,
                labels: {
                    style: {colors: textColor},
                    formatter: function (val) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(Math.round(val));
                    }
                }
            };

            // Fix the Y-axis tick scaling decimals issue on low/zero sales data
            if (maxRevenue < 50000) {
                yaxisConfig.max = maxRevenue === 0 ? 50000 : Math.max(maxRevenue, 50000);
                yaxisConfig.tickAmount = 5; // Rp 0, Rp 10.000, Rp 20.000, Rp 30.000, Rp 40.000, Rp 50.000
            }

            const options = {
                series: [{
                    name: 'Pendapatan',
                    data: revenues
                }],
                chart: {
                    type: 'area',
                    height: 250,
                    toolbar: {show: false},
                    fontFamily: 'inherit',
                    parentHeightOffset: 0,
                    background: 'transparent'
                },
                colors: ['#10B981'],
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
                yaxis: yaxisConfig,
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
