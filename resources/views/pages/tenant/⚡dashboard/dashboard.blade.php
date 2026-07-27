<main
    class="min-vh-100 rounded-[2rem] bg-slate-50 p-4 pb-10 font-sans text-slate-800 dark:bg-slate-950 dark:text-slate-200 md:p-6">
    <div class="space-y-6">

        {{-- ═══════════════════════════════════════════════════════════
             1. HEADER & GLOBAL FILTERS
        ═══════════════════════════════════════════════════════════ --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-400 dark:text-slate-500">
                    {{ $store->name ?? 'Kafe Outlet' }}
                </p>
                <h1 class="mt-1 text-2xl font-black text-slate-900 dark:text-white">
                    Dashboard
                </h1>
            </div>

            <details class="relative flex-shrink-0 w-full md:w-auto max-w-full group">
                <summary
                    class="flex list-none items-center justify-between gap-1 sm:gap-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-1.5 shadow-sm relative w-full overflow-x-auto cursor-pointer [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none] marker:content-none"
                    wire:loading.class="opacity-50 pointer-events-none">
                    <button type="button" wire:click="setDateFilter('today')"
                            class="flex-1 sm:flex-none whitespace-nowrap px-2 sm:px-4 py-2 text-xs sm:text-sm font-bold rounded-xl transition-colors text-center {{ $dateFilter === 'today' ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-950' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        Hari Ini
                    </button>
                    <button type="button" wire:click="setDateFilter('7days')"
                            class="flex-1 sm:flex-none whitespace-nowrap px-2 sm:px-4 py-2 text-xs sm:text-sm font-bold rounded-xl transition-colors text-center {{ $dateFilter === '7days' ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-950' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        7 Hari
                    </button>
                    <button type="button" wire:click="setDateFilter('30days')"
                            class="flex-1 sm:flex-none whitespace-nowrap px-2 sm:px-4 py-2 text-xs sm:text-sm font-bold rounded-xl transition-colors text-center {{ $dateFilter === '30days' ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-950' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        30 Hari
                    </button>
                    <div class="hidden sm:block w-px h-6 bg-slate-200 dark:bg-slate-700 mx-1 shrink-0"></div>
                    <span
                        class="shrink-0 px-2 sm:px-3 py-2 text-slate-400 group-open:bg-slate-100 group-open:dark:bg-slate-800 group-open:rounded-xl">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </span>
                </summary>

                <div
                    class="absolute right-0 top-full z-50 mt-2 w-72 rounded-3xl border border-slate-200 bg-white p-4 shadow-xl dark:border-slate-800 dark:bg-slate-900">
                    <h4 class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-500">Pilih Rentang Waktu</h4>
                    <div class="space-y-3">
                        <div>
                            <label class="mb-1 block text-[11px] font-bold text-slate-600 dark:text-slate-400">Mulai
                                Dari</label>
                            <input type="date" wire:model="customStartDate"
                                   class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                        </div>
                        <div>
                            <label class="mb-1 block text-[11px] font-bold text-slate-600 dark:text-slate-400">Sampai
                                Dengan</label>
                            <input type="date" wire:model="customEndDate"
                                   class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                        </div>
                        <button type="button" wire:click="applyCustomDateFilter"
                                class="mt-2 flex w-full items-center justify-center gap-2 rounded-2xl bg-emerald-800 py-2 text-sm font-bold text-white transition-colors hover:bg-emerald-900">
                            <span wire:loading.remove wire:target="applyCustomDateFilter">Terapkan Filter</span>
                            <span wire:loading wire:target="applyCustomDateFilter"
                                  class="h-4 w-4 animate-spin rounded-full border-2 border-white/30 border-t-white"></span>
                        </button>
                    </div>
                </div>
            </details>
        </div>

        {{-- ═══════════════════════════════════════════════════════════
             2. PERLU PERHATIAN (Urgent Alerts)
        ═══════════════════════════════════════════════════════════ --}}
        @if($stats['pending_orders'] > 0 || $outOfStockCount > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if($stats['pending_orders'] > 0 && $this->kitchenActive)
                    <a href="{{ route('kitchen') }}"
                       class="group flex items-center justify-between rounded-3xl border border-emerald-200 bg-emerald-50 p-4 transition-colors hover:bg-emerald-100 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:hover:bg-emerald-500/20">
                        <div class="flex items-center gap-3">
                            <div class="h-2 w-2 rounded-full bg-emerald-600 animate-pulse"></div>
                            <div>
                                <span class="block text-sm font-bold text-emerald-800 dark:text-emerald-400">Antrean Dapur Aktif</span>
                                <span class="text-xs text-emerald-700/80 dark:text-emerald-400/80">{{ $stats['pending_orders'] }} pesanan menunggu diproses</span>
                            </div>
                        </div>
                        <span class="text-emerald-600 transition-transform group-hover:translate-x-1">→</span>
                    </a>
                @endif

                @if($outOfStockCount > 0)
                    <a href="{{ route('products') }}"
                       class="group flex items-center justify-between rounded-3xl border border-red-200 bg-red-50 p-4 transition-colors hover:bg-red-100 dark:border-red-500/20 dark:bg-red-500/10 dark:hover:bg-red-500/20">
                        <div class="flex items-center gap-3">
                            <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <div>
                                <span
                                    class="block text-sm font-bold text-red-800 dark:text-red-400">Peringatan Stok</span>
                                <span
                                    class="text-xs text-red-600/80 dark:text-red-400/80">{{ $outOfStockFirst }} {{ $outOfStockCount > 1 ? '& ' . ($outOfStockCount - 1) . ' item lain' : '' }} habis</span>
                            </div>
                        </div>
                        <span class="text-red-500 transition-transform group-hover:translate-x-1">→</span>
                    </a>
                @endif
            </div>
        @endif

        {{-- ═══════════════════════════════════════════════════════════
             3. KPI UTAMA (Metrics)
        ═══════════════════════════════════════════════════════════ --}}
        <div
            class="rounded-[2rem] bg-white p-2 shadow-[0_18px_50px_rgba(15,23,42,0.06)] dark:bg-slate-900 dark:shadow-[0_18px_40px_rgba(0,0,0,0.22)]">
            <div class="hidden grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-4" wire:loading.class.remove="hidden"
                 wire:loading.class="grid" wire:target="setDateFilter,applyCustomDateFilter">
                <div class="h-32 rounded-[1.5rem] bg-slate-100 animate-pulse dark:bg-slate-800"></div>
                <div class="h-32 rounded-[1.5rem] bg-slate-100 animate-pulse dark:bg-slate-800"></div>
                <div class="h-32 rounded-[1.5rem] bg-slate-100 animate-pulse dark:bg-slate-800"></div>
                <div class="h-32 rounded-[1.5rem] bg-slate-100 animate-pulse dark:bg-slate-800"></div>
            </div>

            <div wire:loading.remove wire:target="setDateFilter,applyCustomDateFilter"
                 class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-[1.5rem] px-5 py-5 lg:border-r lg:border-slate-100 dark:lg:border-slate-800">
                    <div class="mb-4 flex items-center justify-between">
                        <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400">
                            <svg class="h-4 w-4 text-emerald-800 dark:text-emerald-500" fill="none"
                                 stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V6m0 2v8m0 0v2"></path>
                            </svg>
                            <span class="text-sm font-bold">Total Revenue</span>
                        </div>
                        <span
                            class="text-xs font-semibold text-slate-400">{{ $dateFilter === 'today' ? 'Hari Ini' : ($dateFilter === '7days' ? '7 Hari' : ($dateFilter === '30days' ? '30 Hari' : 'Kustom')) }}</span>
                    </div>
                    <div class="text-3xl font-black text-slate-900 dark:text-white">
                        Rp {{ number_format($stats['revenue_today'], 0, ',', '.') }}</div>
                    <div class="mt-2 text-xs font-semibold">
                        <span
                            class="{{ $stats['revenue_trend_today'] > 0 ? 'text-emerald-500' : 'text-slate-400' }}">{{ $stats['revenue_trend_today'] > 0 ? '▲' : '' }} {{ abs($stats['revenue_trend_today']) }}%</span>
                        <span class="text-slate-400"> vs periode lalu</span>
                    </div>
                </div>

                <div class="rounded-[1.5rem] px-5 py-5 lg:border-r lg:border-slate-100 dark:lg:border-slate-800">
                    <div class="mb-4 flex items-center gap-2 text-slate-500 dark:text-slate-400">
                        <svg class="h-4 w-4 text-emerald-800 dark:text-emerald-500" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 17v-6m4 6V7m4 10V4M5 20h14"></path>
                        </svg>
                        <span class="text-sm font-bold">Transaksi</span>
                    </div>
                    <div class="text-3xl font-black text-slate-900 dark:text-white">{{ $stats['orders_today'] }}</div>
                    <div class="mt-2 text-xs font-semibold text-slate-400">Total order tercatat</div>
                </div>

                <a wire:loading.remove wire:target="setDateFilter,applyCustomDateFilter" wire:navigate
                   href="/orders?status=pending"
                   class="group rounded-[1.5rem] px-5 py-5 lg:border-r lg:border-slate-100 dark:lg:border-slate-800">
                    <div class="mb-4 flex items-center justify-between">
                        <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400">
                            <svg class="h-4 w-4 text-emerald-800 dark:text-emerald-500" fill="none"
                                 stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm font-bold">Pesanan Menunggu</span>
                        </div>
                        <span
                            class="rounded-full bg-emerald-50 px-2 py-1 text-[11px] font-bold text-emerald-600 dark:bg-emerald-500/10">Live</span>
                    </div>
                    <div class="text-3xl font-black text-slate-900 dark:text-white">{{ $stats['pending_orders'] }}</div>
                    <div
                        class="mt-2 text-xs font-semibold {{ $stats['pending_orders'] > 0 ? 'text-emerald-600' : 'text-slate-400' }}">{{ $stats['pending_orders'] > 0 ? 'Perlu segera diproses →' : 'Semua pesanan selesai' }}</div>
                </a>

                <div class="rounded-[1.5rem] bg-slate-900 px-5 py-5 text-white dark:bg-slate-950">
                    <div class="mb-4 flex items-center gap-2 text-slate-300">
                        <svg class="h-4 w-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                        <span class="text-sm font-bold">Profit Estimasi</span>
                    </div>
                    <div class="text-3xl font-black">Rp {{ number_format($stats['profit_month'], 0, ',', '.') }}</div>
                    <div class="mt-2 text-xs font-semibold text-emerald-400">Bulan ini (Margin kotor)</div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════
             4. CHARTS & TRANSACTIONS
        ═══════════════════════════════════════════════════════════ --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div
                class="lg:col-span-2 rounded-[2rem] bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.06)] relative overflow-hidden dark:bg-slate-900 dark:shadow-[0_18px_40px_rgba(0,0,0,0.22)]">
                <div wire:loading.block wire:target="setDateFilter,applyCustomDateFilter"
                     class="space-y-5 animate-pulse">
                    <div>
                        <div class="h-6 w-40 rounded bg-slate-200 dark:bg-slate-800"></div>
                        <div class="mt-2 h-4 w-32 rounded bg-slate-100 dark:bg-slate-800/70"></div>
                    </div>
                    <div
                        class="h-[280px] rounded-[1.5rem] bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-800"></div>
                </div>

                <div wire:loading.remove wire:target="setDateFilter,applyCustomDateFilter">
                    <div class="mb-5 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <h3 class="text-lg font-black text-slate-900 dark:text-white">Sales Statistic</h3>
                            <p class="mt-1 text-xs font-semibold text-slate-500 dark:text-slate-400">
                                Tren Penjualan
                                @if($dateFilter === 'today')
                                    Hari Ini
                                @elseif($dateFilter === '7days')
                                    7 Hari Terakhir
                                @elseif($dateFilter === '30days')
                                    30 Hari Terakhir
                                @else
                                    Rentang Kustom
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="relative min-h-[280px]"
                         x-data="{
                            chart: null,
                            initChart(dataStr) {
                                if (this.chart) {
                                    this.chart.destroy();
                                }
                                let chartData = [];
                                try { chartData = JSON.parse(dataStr); } catch(e) {}

                                let revenues = chartData.map(i => i.revenue);
                                let categories = chartData.map(i => i.date);

                                const isDark = document.documentElement.classList.contains('dark');
                                const textColor = isDark ? '#94a3b8' : '#64748b';
                                const gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(15,23,42,0.06)';
                                const brandColor = '#065f46';

                                let options = {
                                    series: [{name: 'Omzet', data: revenues}],
                                    chart: { type: 'area', height: 280, toolbar: {show: false}, fontFamily: 'inherit', parentHeightOffset: 0, background: 'transparent', animations: {enabled: true, easing: 'easeinout', speed: 600} },
                                    colors: [brandColor],
                                    fill: { type: 'solid', opacity: 0.08 },
                                    dataLabels: {enabled: false},
                                    stroke: {curve: 'smooth', width: 4},
                                    xaxis: { categories: categories, axisBorder: {show: false}, axisTicks: {show: false}, labels: {style: {colors: textColor, fontSize: '11px', fontFamily: 'inherit'}}, crosshairs: {stroke: {color: brandColor, dashArray: 4}} },
                                    yaxis: { min: 0, labels: { style: {colors: textColor, fontSize: '11px', fontFamily: 'inherit'}, formatter: function(val) { if (val >= 1000000) return 'Rp ' + (val / 1000000).toFixed(1) + 'M'; if (val >= 1000) return 'Rp ' + (val / 1000).toFixed(0) + 'K'; return 'Rp ' + val; } } },
                                    grid: { borderColor: gridColor, strokeDashArray: 5, yaxis: {lines: {show: true}}, xaxis: {lines: {show: false}}, padding: {left: 10, right: 10, top: 0, bottom: 0} },
                                    theme: {mode: isDark ? 'dark' : 'light'},
                                    tooltip: { theme: isDark ? 'dark' : 'light', y: {formatter: val => 'Rp' + new Intl.NumberFormat('id-ID').format(val)} },
                                    markers: { size: 0, colors: [brandColor], strokeColors: isDark ? '#0f172a' : '#ffffff', strokeWidth: 2, hover: {size: 6} }
                                };

                                this.chart = new ApexCharts(this.$refs.chartContainer, options);
                                this.chart.render();
                            }
                         }"
                         x-init="
                            setTimeout(() => initChart($el.dataset.chart), 100);
                            let observer = new MutationObserver((mutations) => {
                                mutations.forEach((m) => {
                                    if (m.attributeName === 'data-chart') {
                                        initChart($el.dataset.chart);
                                    }
                                });
                            });
                            observer.observe($el, { attributes: true });
                         "
                         data-chart="{{ $chartData }}"
                    >
                        <div x-ref="chartContainer" wire:ignore></div>
                    </div>
                </div>
            </div>

            <div
                class="max-h-[360px] flex flex-col relative overflow-hidden rounded-[2rem] bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.06)] dark:bg-slate-900 dark:shadow-[0_18px_40px_rgba(0,0,0,0.22)]">
                <h3 class="mb-4 shrink-0 text-sm font-bold text-slate-900 dark:text-white">
                    Menu Terlaris
                    <span class="text-xs font-normal text-slate-500 dark:text-slate-400">
                        @if($dateFilter === 'today')
                            (Hari Ini)
                        @elseif($dateFilter === '7days')
                            (7 Hari)
                        @elseif($dateFilter === '30days')
                            (30 Hari)
                        @else (Kustom) @endif
                    </span>
                </h3>

                <div wire:loading.block wire:target="setDateFilter,applyCustomDateFilter"
                     class="hide-scrollbar flex-1 space-y-4 overflow-y-auto animate-pulse [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
                    @foreach(range(1, 4) as $skeleton)
                        <div
                            class="flex items-center justify-between rounded-2xl border border-slate-100 bg-slate-50/80 px-3 py-3 dark:border-slate-700 dark:bg-slate-800">
                            <div class="min-w-0 flex items-center gap-3">
                                <span
                                    class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-white shadow-sm dark:bg-slate-950"></span>
                                <span class="h-4 w-28 rounded bg-slate-200 dark:bg-slate-700"></span>
                            </div>
                            <span class="h-6 w-20 rounded-full bg-white dark:bg-slate-950"></span>
                        </div>
                    @endforeach
                </div>

                <div wire:loading.remove wire:target="setDateFilter,applyCustomDateFilter">
                    @if($topProducts->isNotEmpty())
                        <div
                            class="hide-scrollbar flex-1 space-y-4 overflow-y-auto [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
                            @foreach($topProducts as $index => $item)
                                <div
                                    class="flex items-center justify-between rounded-2xl border border-slate-100 bg-slate-50/80 px-3 py-3 dark:border-slate-700 dark:bg-slate-800">
                                    <div class="min-w-0 flex items-center gap-3">
                                        <span
                                            class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-white text-xs font-black text-slate-500 shadow-sm dark:bg-slate-950 dark:text-slate-200">{{ $index + 1 }}</span>
                                        <span
                                            class="max-w-[150px] truncate text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $item->product_name }}</span>
                                    </div>
                                    <span
                                        class="rounded-full bg-white px-2 py-1 text-xs font-bold text-slate-600 dark:bg-slate-950 dark:text-slate-300">
                                        {{ $item->total_sold }} terjual
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-slate-500 dark:text-slate-400">Belum ada data</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════
             6. AI RADAR & NAVIGASI CEPAT
        ═══════════════════════════════════════════════════════════ --}}
        <div class="w-full">
            <div
                class="w-full rounded-[2rem] bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.06)] dark:bg-slate-900 dark:shadow-[0_18px_40px_rgba(0,0,0,0.22)]">
                <div class="mb-4 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="h-5 w-5 text-emerald-800 dark:text-emerald-500" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        <h3 class="text-base font-black text-slate-900 dark:text-white">Smart Insight (Rekomendasi
                            AI)</h3>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div
                        class="cursor-pointer rounded-2xl border border-slate-100 p-4 transition-shadow hover:shadow-md dark:border-slate-800">
                        <div
                            class="mb-3 flex h-8 w-8 items-center justify-center rounded bg-emerald-100 text-emerald-600 dark:bg-emerald-500/20">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h4 class="mb-1 text-sm font-bold text-slate-800 dark:text-slate-200">Menu Engineering</h4>
                        <p class="text-xs text-slate-500">Analisis matriks BCG mendeteksi 2 produk yang perlu
                            dipromosikan (Puzzle).</p>
                    </div>

                    <div
                        class="cursor-pointer rounded-2xl border border-slate-100 p-4 transition-shadow hover:shadow-md dark:border-slate-800">
                        <div
                            class="mb-3 flex h-8 w-8 items-center justify-center rounded bg-sky-100 text-sky-600 dark:bg-sky-500/20">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <h4 class="mb-1 text-sm font-bold text-slate-800 dark:text-slate-200">Prediksi Jam Ramai</h4>
                        <p class="text-xs text-slate-500">Persiapkan staf tambahan di jam 12:00 dan 18:00 berdasarkan
                            histori 30 hari.</p>
                    </div>

                    <div
                        class="cursor-pointer rounded-2xl border border-slate-100 p-4 transition-shadow hover:shadow-md dark:border-slate-800">
                        <div
                            class="mb-3 flex h-8 w-8 items-center justify-center rounded bg-purple-100 text-purple-600 dark:bg-purple-500/20">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                        <h4 class="mb-1 text-sm font-bold text-slate-800 dark:text-slate-200">Bundle Otomatis</h4>
                        <p class="text-xs text-slate-500">Kopi Susu & Croissant dibeli bersamaan 45% waktu. Buat paket
                            hemat!</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════
             5. ADVANCED F&B ANALYTICS (Top, Types, Peak, Tables, Shifts)
        ═══════════════════════════════════════════════════════════ --}}
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            {{-- Metode Pembayaran --}}
            <div
                class="relative flex flex-col overflow-hidden rounded-[2rem] bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.06)] dark:bg-slate-900 dark:shadow-[0_18px_40px_rgba(0,0,0,0.22)]">
                <div wire:loading.block wire:target="setDateFilter,applyCustomDateFilter"
                     class="space-y-4 animate-pulse">
                    <div class="h-5 w-32 rounded bg-slate-200 dark:bg-slate-800"></div>
                    @foreach(range(1, 4) as $skeleton)
                        <div>
                            <div class="mb-2 flex justify-between">
                                <span class="h-4 w-20 rounded bg-slate-200 dark:bg-slate-700"></span>
                                <span class="h-4 w-24 rounded bg-slate-100 dark:bg-slate-800"></span>
                            </div>
                            <div class="mb-2 flex justify-between">
                                <span class="h-3 w-16 rounded bg-slate-100 dark:bg-slate-800"></span>
                                <span class="h-3 w-8 rounded bg-slate-100 dark:bg-slate-800"></span>
                            </div>
                            <div class="h-1.5 w-full rounded-full bg-slate-100 dark:bg-slate-800"></div>
                        </div>
                    @endforeach
                </div>

                <div wire:loading.remove wire:target="setDateFilter,applyCustomDateFilter">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Metode Pembayaran</h3>
                    </div>

                    @if($paymentMethods->isNotEmpty())
                        <div class="space-y-4">
                            @foreach($paymentMethods as $pm)
                                @php $pmPercent = $totalPaymentVol > 0 ? round(($pm->total / $totalPaymentVol) * 100) : 0; @endphp
                                <div>
                                    <div class="mb-1 flex justify-between text-xs font-bold">
                                        <span
                                            class="uppercase text-slate-700 dark:text-slate-300">{{ $pm->payment_method ?? 'Cash' }}</span>
                                        <span
                                            class="text-slate-900 dark:text-white">Rp {{ number_format($pm->total_amount, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="mb-1.5 flex justify-between text-[10px] text-slate-500">
                                        <span>{{ $pm->total }} Transaksi</span>
                                        <span>{{ $pmPercent }}%</span>
                                    </div>
                                    <div class="h-1.5 w-full rounded-full bg-slate-100 dark:bg-slate-800">
                                        <div class="h-1.5 rounded-full bg-emerald-500"
                                             style="width: {{ $pmPercent }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="flex flex-1 items-center justify-center text-sm font-semibold text-slate-400">
                            Belum ada data
                        </div>
                    @endif
                </div>
            </div>

            {{-- Distribusi Pesanan --}}
            <div
                class="relative overflow-hidden rounded-[2rem] bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.06)] dark:bg-slate-900 dark:shadow-[0_18px_40px_rgba(0,0,0,0.22)]">
                <div wire:loading.block wire:target="setDateFilter,applyCustomDateFilter"
                     class="space-y-3 animate-pulse">
                    <div class="h-5 w-32 rounded bg-slate-200 dark:bg-slate-800"></div>
                    @foreach(range(1, 4) as $skeleton)
                        <div>
                            <div class="mb-2 flex justify-between">
                                <span class="h-4 w-24 rounded bg-slate-200 dark:bg-slate-700"></span>
                                <span class="h-4 w-10 rounded bg-slate-100 dark:bg-slate-800"></span>
                            </div>
                            <div class="h-1.5 w-full rounded-full bg-slate-100 dark:bg-slate-800"></div>
                        </div>
                    @endforeach
                </div>

                <div wire:loading.remove wire:target="setDateFilter,applyCustomDateFilter">
                    <h3 class="mb-4 text-sm font-bold text-slate-900 dark:text-white">Distribusi Pesanan</h3>
                    @if($orderTypes->isNotEmpty())
                        <div class="space-y-3">
                            @foreach($orderTypes as $type)
                                @php $percent = $totalOrderTypes > 0 ? round(($type->total / $totalOrderTypes) * 100) : 0; @endphp
                                <div>
                                    <div class="mb-1 flex justify-between text-xs font-bold">
                                        <span class="capitalize">{{ str_replace('_', ' ', $type->order_type) }}</span>
                                        <span>{{ $percent }}%</span>
                                    </div>
                                    <div class="h-1.5 w-full rounded-full bg-slate-100 dark:bg-slate-800">
                                        <div class="h-1.5 rounded-full bg-emerald-600"
                                             style="width: {{ $percent }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="flex flex-1 items-center justify-center py-6 text-sm font-semibold text-slate-400">
                            Belum ada data transaksi
                        </div>
                    @endif
                </div>
            </div>

            {{-- Jam Tersibuk (Peak Hours) --}}
            <div
                class="relative flex flex-col justify-between overflow-hidden rounded-[2rem] bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.06)] dark:bg-slate-900 dark:shadow-[0_18px_40px_rgba(0,0,0,0.22)]">
                <div wire:loading.block wire:target="setDateFilter,applyCustomDateFilter"
                     class="space-y-4 animate-pulse">
                    <div class="h-5 w-40 rounded bg-slate-200 dark:bg-slate-800"></div>
                    @foreach(range(1, 4) as $skeleton)
                        <div>
                            <div class="mb-2 flex justify-between">
                                <span class="h-4 w-28 rounded bg-slate-200 dark:bg-slate-700"></span>
                                <span class="h-4 w-20 rounded bg-slate-100 dark:bg-slate-800"></span>
                            </div>
                            <div
                                class="ml-4 h-1.5 w-[calc(100%-1rem)] rounded-full bg-slate-100 dark:bg-slate-800"></div>
                        </div>
                    @endforeach
                </div>

                <div wire:loading.remove wire:target="setDateFilter,applyCustomDateFilter">
                    <div>
                        <h3 class="mb-4 text-sm font-bold text-slate-900 dark:text-white">Jam Tersibuk (Peak Hours)</h3>
                        @if($peakSalesTimes->isNotEmpty())
                            <div class="space-y-4">
                                @foreach($peakSalesTimes as $index => $peak)
                                    @php $peakPercent = round(($peak->orders / $maxPeak) * 100); @endphp
                                    <div>
                                        <div class="mb-1 flex justify-between text-xs font-bold">
                                            <span class="text-slate-700 dark:text-slate-300">
                                                <span class="inline-block w-4 text-slate-400">{{ $index + 1 }}.</span> {{ $peak->time_range }}
                                            </span>
                                            <span
                                                class="text-slate-900 dark:text-white">{{ $peak->orders }} Transaksi</span>
                                        </div>
                                        <div class="ml-4 h-1.5 w-full rounded-full bg-slate-100 dark:bg-slate-800"
                                             style="width: calc(100% - 1rem);">
                                            <div class="h-1.5 rounded-full bg-emerald-600"
                                                 style="width: {{ $peakPercent }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div
                                class="flex flex-1 items-center justify-center py-6 text-sm font-semibold text-slate-400">
                                Belum ada data transaksi
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>

@assets
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endassets
