<main class="pb-10 min-vh-100 font-sans text-slate-800 dark:text-slate-200">
    <div class="space-y-6">

        {{-- ═══════════════════════════════════════════════════════════
             1. HEADER & GLOBAL FILTERS
        ═══════════════════════════════════════════════════════════ --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white">
                    Halo, {{ explode(' ', $user->name)[0] ?? 'User' }} 👋
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    {{ $store->name ?? 'Kafe Outlet' }} · {{ today()->translatedFormat('l, d F Y') }}
                </p>
            </div>

            {{-- Date Filter Panel --}}
            <div x-data="{ open: false }" class="relative flex-shrink-0 w-full md:w-auto max-w-full">
                <div class="flex items-center justify-between gap-1 sm:gap-2 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/60 rounded-2xl p-1.5 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.1)] relative w-full overflow-x-auto [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]" wire:loading.class="opacity-50 pointer-events-none">
                    <button type="button" 
                            wire:click="setDateFilter('today')"
                            class="flex-1 sm:flex-none whitespace-nowrap px-2 sm:px-4 py-2 text-xs sm:text-sm font-bold rounded-lg transition-colors text-center {{ $dateFilter === 'today' ? 'bg-orange-500 text-white' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        Hari Ini
                    </button>
                    <button type="button" 
                            wire:click="setDateFilter('7days')"
                            class="flex-1 sm:flex-none whitespace-nowrap px-2 sm:px-4 py-2 text-xs sm:text-sm font-bold rounded-lg transition-colors text-center {{ $dateFilter === '7days' ? 'bg-orange-500 text-white' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        7 Hari
                    </button>
                    <button type="button" 
                            wire:click="setDateFilter('30days')"
                            class="flex-1 sm:flex-none whitespace-nowrap px-2 sm:px-4 py-2 text-xs sm:text-sm font-bold rounded-lg transition-colors text-center {{ $dateFilter === '30days' ? 'bg-orange-500 text-white' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        30 Hari
                    </button>
                    <div class="hidden sm:block w-px h-6 bg-slate-200 dark:bg-slate-700 mx-1 shrink-0"></div>
                    <button @click="open = !open" class="shrink-0 px-2 sm:px-3 py-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 relative text-center flex justify-center" :class="open ? 'bg-slate-100 dark:bg-slate-800 rounded-lg' : ''">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </button>
                </div>
                
                <!-- Custom Date Dropdown (Dikeluarkan dari flex container agar tidak terpotong overflow-x) -->
                <div x-show="open" @click.outside="open = false" style="display: none;"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                     class="absolute right-0 top-full mt-2 w-72 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/60 rounded-3xl shadow-xl z-50 p-4">
                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Pilih Rentang Waktu</h4>
                    <div class="space-y-3">
                        <div>
                            <label class="text-[11px] font-bold text-slate-600 dark:text-slate-400 block mb-1">Mulai Dari</label>
                            <input type="date" wire:model="customStartDate" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm rounded-2xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500 text-slate-700 dark:text-slate-200">
                        </div>
                        <div>
                            <label class="text-[11px] font-bold text-slate-600 dark:text-slate-400 block mb-1">Sampai Dengan</label>
                            <input type="date" wire:model="customEndDate" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm rounded-2xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500 text-slate-700 dark:text-slate-200">
                        </div>
                        <button @click="open = false" wire:click="applyCustomDateFilter" class="w-full mt-2 bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 rounded-2xl transition-colors text-sm flex justify-center items-center gap-2">
                            <span wire:loading.remove wire:target="applyCustomDateFilter">Terapkan Filter</span>
                            <span wire:loading wire:target="applyCustomDateFilter" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════
             2. PERLU PERHATIAN (Urgent Alerts)
        ═══════════════════════════════════════════════════════════ --}}
        @if($stats['pending_orders'] > 0 || $outOfStockCount > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if($stats['pending_orders'] > 0)
                <a href="{{ route('kitchen') }}"
                   class="group flex items-center justify-between p-4 bg-orange-50 dark:bg-orange-500/10 border border-orange-200 dark:border-orange-500/20 rounded-3xl hover:bg-orange-100 dark:hover:bg-orange-500/20 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></div>
                        <div>
                            <span class="block text-sm font-bold text-orange-800 dark:text-orange-400">Antrean Dapur Aktif</span>
                            <span class="text-xs text-orange-600/80 dark:text-orange-400/80">{{ $stats['pending_orders'] }} pesanan menunggu diproses</span>
                        </div>
                    </div>
                    <span class="text-orange-500 group-hover:translate-x-1 transition-transform">→</span>
                </a>
                @endif

                @if($outOfStockCount > 0)
                <a href="{{ route('product') }}"
                   class="group flex items-center justify-between p-4 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-3xl hover:bg-red-100 dark:hover:bg-red-500/20 transition-colors">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div>
                            <span class="block text-sm font-bold text-red-800 dark:text-red-400">Peringatan Stok</span>
                            <span class="text-xs text-red-600/80 dark:text-red-400/80">{{ $outOfStockFirst }} {{ $outOfStockCount > 1 ? '& ' . ($outOfStockCount - 1) . ' item lain' : '' }} habis</span>
                        </div>
                    </div>
                    <span class="text-red-500 group-hover:translate-x-1 transition-transform">→</span>
                </a>
                @endif
            </div>
        @endif

        {{-- ═══════════════════════════════════════════════════════════
             6. AI RADAR & NAVIGASI CEPAT
        ═══════════════════════════════════════════════════════════ --}}
        <div class="w-full">
            
            {{-- AI Radar --}}
            <div class="w-full bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/60 rounded-3xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.1)]">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        <h3 class="text-base font-black text-slate-900 dark:text-white">Smart Insight (Rekomendasi AI)</h3>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="p-4 border border-slate-100 dark:border-slate-800 rounded-2xl hover:shadow-md transition-shadow cursor-pointer">
                        <div class="w-8 h-8 rounded bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 flex items-center justify-center mb-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                        <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-1">Menu Engineering</h4>
                        <p class="text-xs text-slate-500">Analisis matriks BCG mendeteksi 2 produk yang perlu dipromosikan (Puzzle).</p>
                    </div>

                    <div class="p-4 border border-slate-100 dark:border-slate-800 rounded-2xl hover:shadow-md transition-shadow cursor-pointer">
                        <div class="w-8 h-8 rounded bg-sky-100 dark:bg-sky-500/20 text-sky-600 flex items-center justify-center mb-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-1">Prediksi Jam Ramai</h4>
                        <p class="text-xs text-slate-500">Persiapkan staf tambahan di jam 12:00 dan 18:00 berdasarkan histori 30 hari.</p>
                    </div>

                    <div class="p-4 border border-slate-100 dark:border-slate-800 rounded-2xl hover:shadow-md transition-shadow cursor-pointer">
                        <div class="w-8 h-8 rounded bg-purple-100 dark:bg-purple-500/20 text-purple-600 flex items-center justify-center mb-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        </div>
                        <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-1">Bundle Otomatis</h4>
                        <p class="text-xs text-slate-500">Kopi Susu & Croissant dibeli bersamaan 45% waktu. Buat paket hemat!</p>
                    </div>
                </div>
            </div>
        </div>



        {{-- ═══════════════════════════════════════════════════════════
             3. KPI UTAMA (Metrics)
        ═══════════════════════════════════════════════════════════ --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Skeleton Cards (Tampil saat memuat) -->
            <div wire:loading.block wire:target="setDateFilter,applyCustomDateFilter" class="bg-slate-100 dark:bg-slate-800 border border-slate-100 dark:border-slate-800/60 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.1)] h-32 animate-pulse w-full"></div>
            <div wire:loading.block wire:target="setDateFilter,applyCustomDateFilter" class="bg-slate-100 dark:bg-slate-800 border border-slate-100 dark:border-slate-800/60 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.1)] h-32 animate-pulse w-full"></div>
            <div wire:loading.block wire:target="setDateFilter,applyCustomDateFilter" class="bg-slate-100 dark:bg-slate-800 border border-slate-100 dark:border-slate-800/60 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.1)] h-32 animate-pulse w-full"></div>
            <div wire:loading.block wire:target="setDateFilter,applyCustomDateFilter" class="bg-slate-200 dark:bg-slate-700 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.1)] h-32 animate-pulse w-full"></div>

            <!-- Real Content (Sembunyi saat memuat) -->
            <div wire:loading.remove wire:target="setDateFilter,applyCustomDateFilter" class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/60 p-6 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.1)] hover:-translate-y-1 transition-transform duration-300 flex flex-col justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-2">Omzet {{ $dateFilter === 'today' ? 'Hari Ini' : ($dateFilter === '7days' ? '7 Hari' : ($dateFilter === '30days' ? '30 Hari' : 'Terpilih')) }}</span>
                <span class="text-3xl font-black text-slate-900 dark:text-white">Rp {{ number_format($stats['revenue_today'], 0, ',', '.') }}</span>
                <div class="mt-2 text-xs font-semibold">
                    <span class="{{ $stats['revenue_trend_today'] > 0 ? 'text-emerald-500' : 'text-slate-400' }}">
                        {{ $stats['revenue_trend_today'] > 0 ? '↑' : '' }} {{ abs($stats['revenue_trend_today']) }}%
                    </span>
                    <span class="text-slate-400"> vs periode lalu</span>
                </div>
            </div>

            <div wire:loading.remove wire:target="setDateFilter,applyCustomDateFilter" class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/60 p-6 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.1)] hover:-translate-y-1 transition-transform duration-300 transition-shadow flex flex-col justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-2">Transaksi</span>
                <span class="text-2xl font-black text-slate-900 dark:text-white">{{ $stats['orders_today'] }}</span>
                <div class="mt-2 text-xs font-semibold text-slate-400">
                    Total order tercatat
                </div>
            </div>

            <div wire:loading.remove wire:target="setDateFilter,applyCustomDateFilter" class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/60 p-6 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.1)] hover:-translate-y-1 transition-transform duration-300 transition-shadow flex flex-col justify-between group cursor-pointer" wire:navigate href="/orders?status=pending">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pesanan Menunggu</span>
                    <div class="w-8 h-8 rounded-full bg-orange-50 dark:bg-orange-500/10 flex items-center justify-center text-orange-500 group-hover:scale-110 transition-transform">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <span class="text-2xl font-black text-slate-900 dark:text-white">{{ $stats['pending_orders'] }}</span>
                <div class="mt-2 text-[11px] font-bold {{ $stats['pending_orders'] > 0 ? 'text-orange-500' : 'text-slate-400' }}">
                    {{ $stats['pending_orders'] > 0 ? 'Perlu segera diproses →' : 'Semua pesanan selesai' }}
                </div>
            </div>

            <div wire:loading.remove wire:target="setDateFilter,applyCustomDateFilter" class="bg-slate-900 dark:bg-slate-950 p-6 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.1)] text-white flex flex-col justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-2">Profit Estimasi</span>
                <span class="text-2xl font-black">Rp {{ number_format($stats['profit_month'], 0, ',', '.') }}</span>
                <div class="mt-2 text-xs font-semibold text-emerald-400">
                    Bulan ini (Margin kotor)
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════
             4. CHARTS & TRANSACTIONS
        ═══════════════════════════════════════════════════════════ --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Area Chart --}}
            <div class="lg:col-span-2 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/60 rounded-3xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.1)] relative overflow-hidden">
                <div wire:loading.block wire:target="setDateFilter,applyCustomDateFilter" class="absolute inset-0 z-20 bg-slate-50 dark:bg-slate-800 animate-pulse"></div>
                
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                        Tren Penjualan 
                        @if($dateFilter === 'today') (Hari Ini) 
                        @elseif($dateFilter === '7days') (7 Hari)
                        @elseif($dateFilter === '30days') (30 Hari)
                        @else (Kustom) @endif
                    </h3>
                </div>
                
                <div class="relative min-h-[250px]" 
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
                            const gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
                            const brandColor = '#f97316';
                            
                            let options = {
                                series: [{name: 'Omzet', data: revenues}],
                                chart: { type: 'area', height: 250, toolbar: {show: false}, fontFamily: 'inherit', parentHeightOffset: 0, background: 'transparent', animations: {enabled: true, easing: 'easeinout', speed: 600} },
                                colors: [brandColor],
                                fill: { type: 'solid', opacity: 0.1 },
                                dataLabels: {enabled: false},
                                stroke: {curve: 'smooth', width: 3},
                                xaxis: { categories: categories, axisBorder: {show: false}, axisTicks: {show: false}, labels: {style: {colors: textColor, fontSize: '11px', fontFamily: 'inherit'}}, crosshairs: {stroke: {color: brandColor, dashArray: 4}} },
                                yaxis: { min: 0, labels: { style: {colors: textColor, fontSize: '11px', fontFamily: 'inherit'}, formatter: function(val) { if (val >= 1000000) return 'Rp ' + (val / 1000000).toFixed(1) + 'M'; if (val >= 1000) return 'Rp ' + (val / 1000).toFixed(0) + 'K'; return 'Rp ' + val; } } },
                                grid: { borderColor: gridColor, strokeDashArray: 4, yaxis: {lines: {show: true}}, xaxis: {lines: {show: false}}, padding: {left: 10, right: 10, top: 0, bottom: 0} },
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

            {{-- Top Products --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/60 rounded-3xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.1)] max-h-[330px] flex flex-col relative overflow-hidden">
                <div wire:loading.block wire:target="setDateFilter,applyCustomDateFilter" class="absolute inset-0 z-20 bg-slate-50 dark:bg-slate-800 animate-pulse"></div>
                
                <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-4 shrink-0">
                    Menu Terlaris
                    <span class="text-xs font-normal text-slate-500">
                        @if($dateFilter === 'today') (Hari Ini) 
                        @elseif($dateFilter === '7days') (7 Hari)
                        @elseif($dateFilter === '30days') (30 Hari)
                        @else (Kustom) @endif
                    </span>
                </h3>
                @if($topProducts->isNotEmpty())
                    <div class="space-y-4 flex-1 overflow-y-auto hide-scrollbar [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
                        @foreach($topProducts as $index => $item)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="text-slate-400 font-bold text-sm w-4">{{ $index + 1 }}.</span>
                                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-300 truncate max-w-[150px]">{{ $item->product_name }}</span>
                                </div>
                                <span class="text-xs font-bold bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded text-slate-600 dark:text-slate-400">
                                    {{ $item->total_sold }} terjual
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-slate-500">Belum ada data</p>
                @endif
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════
             5. ADVANCED F&B ANALYTICS (Top, Types, Peak, Tables, Shifts)
        ═══════════════════════════════════════════════════════════ --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {{-- Metode Pembayaran --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/60 rounded-3xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.1)] flex flex-col relative overflow-hidden">
                <div wire:loading.block wire:target="setDateFilter,applyCustomDateFilter" class="absolute inset-0 z-20 bg-slate-50 dark:bg-slate-800 animate-pulse"></div>

                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Metode Pembayaran</h3>
                </div>

                @if($paymentMethods->isNotEmpty())
                    <div class="space-y-4">
                        @foreach($paymentMethods as $pm)
                            @php $pmPercent = $totalPaymentVol > 0 ? round(($pm->total / $totalPaymentVol) * 100) : 0; @endphp
                            <div>
                                <div class="flex justify-between text-xs font-bold mb-1">
                                    <span class="uppercase text-slate-700 dark:text-slate-300">{{ $pm->payment_method ?? 'Cash' }}</span>
                                    <span class="text-slate-900 dark:text-white">Rp {{ number_format($pm->total_amount, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-[10px] text-slate-500 mb-1.5">
                                    <span>{{ $pm->total }} Transaksi</span>
                                    <span>{{ $pmPercent }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-1.5">
                                    <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $pmPercent }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex-1 flex items-center justify-center text-sm font-semibold text-slate-400">
                        Belum ada data
                    </div>
                @endif
            </div>

            {{-- Distribusi Pesanan --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/60 rounded-3xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.1)] relative overflow-hidden">
                <div wire:loading.block wire:target="setDateFilter,applyCustomDateFilter" class="absolute inset-0 z-20 bg-slate-50 dark:bg-slate-800 animate-pulse"></div>
                
                <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-4">Distribusi Pesanan</h3>
                @if($orderTypes->isNotEmpty())
                    <div class="space-y-3">
                        @foreach($orderTypes as $type)
                            @php $percent = $totalOrderTypes > 0 ? round(($type->total / $totalOrderTypes) * 100) : 0; @endphp
                            <div>
                                <div class="flex justify-between text-xs font-bold mb-1">
                                    <span class="capitalize">{{ str_replace('_', ' ', $type->order_type) }}</span>
                                    <span>{{ $percent }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-1.5">
                                    <div class="bg-orange-500 h-1.5 rounded-full" style="width: {{ $percent }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex-1 flex items-center justify-center text-sm font-semibold text-slate-400 py-6">
                        Belum ada data transaksi
                    </div>
                @endif
            </div>

            {{-- Jam Tersibuk (Peak Hours) --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/60 rounded-3xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.1)] flex flex-col justify-between relative overflow-hidden">
                <div wire:loading.block wire:target="setDateFilter,applyCustomDateFilter" class="absolute inset-0 z-20 bg-slate-50 dark:bg-slate-800 animate-pulse"></div>
                
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-4">Jam Tersibuk (Peak Hours)</h3>
                    @if($peakSalesTimes->isNotEmpty())
                        <div class="space-y-4">
                            @foreach($peakSalesTimes as $index => $peak)
                                @php $peakPercent = round(($peak->orders / $maxPeak) * 100); @endphp
                                <div>
                                    <div class="flex justify-between text-xs font-bold mb-1">
                                        <span class="text-slate-700 dark:text-slate-300">
                                            <span class="inline-block w-4 text-slate-400">{{ $index + 1 }}.</span> {{ $peak->time_range }}
                                        </span>
                                        <span class="text-slate-900 dark:text-white">{{ $peak->orders }} Transaksi</span>
                                    </div>
                                    <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-1.5 ml-4" style="width: calc(100% - 1rem);">
                                        <div class="bg-orange-500 h-1.5 rounded-full" style="width: {{ $peakPercent }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="flex-1 flex items-center justify-center text-sm font-semibold text-slate-400 py-6">
                            Belum ada data transaksi
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</main>

@assets
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endassets
