<div wire:poll.15s class="pb-5 min-vh-100">

    {{-- Welcome Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-4 mb-md-5 gap-3 pt-3">
        <div>
            <h6 class="page-greeting-label">
                Selamat Datang, {{ explode(' ', $user->name)[0] }} 👋
            </h6>
            <h2 class="page-store-name mb-2">
                {{ $store->name ?? 'Setup Tokomu' }}
            </h2>
            <div class="d-flex flex-wrap align-items-center gap-2 mt-1">
                @if($store)
                    <span
                        class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 fw-bold d-flex align-items-center gap-2 border border-success border-opacity-10"
                        style="font-size: 0.72rem;">
                        <span class="active-glow-dot"></span> Online
                    </span>
                    <a href="{{ url('/') }}"
                       target="_blank"
                       class="badge text-secondary bg-body-tertiary border rounded-pill px-3 py-2 text-decoration-none transition-all hover-translate"
                       style="font-size: 0.72rem;">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Buka Toko
                    </a>
                @endif
            </div>
        </div>

        {{-- Header Buttons (Mobile Friendly) --}}
        <div class="d-flex flex-row flex-wrap flex-sm-nowrap gap-2 mt-3 mt-md-0 w-100 w-md-auto">
            <button wire:click="exportLaporan"
                    class="btn btn-dashboard-header btn-outline flex-grow-1 py-2 px-2 px-sm-3"
                    wire:loading.attr="disabled"
                    style="font-size: 0.85rem; border-radius: 12px;">
                <span wire:loading.remove wire:target="exportLaporan"><i
                        class="bi bi-file-earmark-excel-fill text-success fs-6 me-1"></i> Export</span>
                <span wire:loading wire:target="exportLaporan"><span class="spinner-border spinner-border-sm me-1"></span> Proses...</span>
            </button>
            <a href="{{ route('cashier') }}" wire:navigate
               class="btn btn-caramel-solid text-white flex-grow-1 py-2 px-2 px-sm-3 d-flex align-items-center justify-content-center gap-1"
               style="font-size: 0.85rem; border-radius: 12px;">
                <i class="bi bi-cart-check-fill fs-6"></i> Kasir
                @if($newOrderCount > 0)
                    <span class="badge rounded-circle ms-1 shadow-sm"
                          style="padding: 0.35em 0.5em; background-color: #ffffff !important; color: #dc3545 !important; font-size: 0.7rem;">{{ $newOrderCount }}</span>
                @endif
            </a>
        </div>
    </div>

    {{-- New Order Notification Glassmorphism Island --}}
    @if($newOrderCount > 0)
        <div class="notif-new-order p-3 mb-4 transition-all"
             style="position: relative; overflow: hidden;">

            {{-- Decorative Subtle Glow Behind --}}
            <div class="position-absolute rounded-circle"
                 style="width: 150px; height: 150px; background: rgba(202, 138, 4, 0.15); filter: blur(40px); top: -50px; right: -30px; pointer-events: none;">
            </div>

            <div
                class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 position-relative z-1">
                <div class="d-flex align-items-center gap-3">
                    {{-- Glowing Icon Wrapper --}}
                    <div
                        class="d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm rounded-circle position-relative bg-body border"
                        style="width: 48px; height: 48px; border-color: rgba(180, 83, 9, 0.2) !important;">
                        <div class="position-absolute rounded-circle bg-warning opacity-25 w-100 h-100"
                             style="animation: pulse-glow 2s infinite;"></div>
                        <i class="bi bi-bell-fill fs-5"
                           style="color: var(--brand-caramel, #b45309); animation: smooth-bounce 2.5s ease infinite;"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <h6 class="fw-black mb-0 text-body" style="letter-spacing: -0.3px;">{{ $newOrderCount }}
                                Pesanan Baru Masuk!</h6>
                            <span class="badge bg-danger rounded-pill fw-bold animate-pulse"
                                  style="font-size: 0.65rem; padding: 0.25rem 0.5rem;">LIVE</span>
                        </div>
                        <p class="mb-0 small text-secondary fw-medium mt-0.5 opacity-75">Antrian kasir bertambah. Segera
                            periksa dan proses pesanan pelanggan.</p>
                    </div>
                </div>
            </div>

            {{-- Premium Action Button --}}
            <button wire:click="acknowledgeOrders"
                    class="btn btn-caramel-solid flex-shrink-0 w-100 w-md-auto mt-2"
                    type="button">
                <i class="bi bi-check2-all me-1"></i> Selesai Periksa
            </button>
        </div>

        {{-- Refined Micro-Animations --}}
        <style>
            @keyframes smooth-bounce {
                0%, 100% {
                    transform: translateY(0) rotate(0);
                }
                10% {
                    transform: translateY(-4px) rotate(-8deg);
                }
                20% {
                    transform: translateY(-4px) rotate(8deg);
                }
                30% {
                    transform: translateY(0) rotate(-4deg);
                }
                40% {
                    transform: translateY(0) rotate(4deg);
                }
                50% {
                    transform: translateY(0) rotate(0);
                }
            }

            @keyframes pulse-glow {
                0% {
                    transform: scale(0.9);
                    opacity: 0.3;
                }
                50% {
                    transform: scale(1.2);
                    opacity: 0;
                }
                100% {
                    transform: scale(0.9);
                    opacity: 0.3;
                }
            }

            .animate-pulse {
                animation: text-pulse 1.5s infinite ease-in-out;
            }

            @keyframes text-pulse {
                0%, 100% {
                    opacity: 1;
                }
                50% {
                    opacity: 0.6;
                }
            }
        </style>

        {{-- Audio System Notification --}}
        <script>
            if (!window._newOrderAlertPlayed) {
                try {
                    const ctx = new (window.AudioContext || window.webkitAudioContext)();
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(880, ctx.currentTime);
                    osc.frequency.exponentialRampToValueAtTime(1760, ctx.currentTime + 0.1);
                    gain.gain.setValueAtTime(0.15, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.4);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start();
                    osc.stop(ctx.currentTime + 0.4);
                    window._newOrderAlertPlayed = true;
                } catch (e) {
                }
            }
        </script>
    @else
        <script>window._newOrderAlertPlayed = false;</script>
    @endif

    @if(!$store)
        <div class="card bg-warning bg-opacity-10 border-0 rounded-4 p-4 mb-5 dash-card">
            <div class="d-flex flex-column flex-sm-row align-items-center text-center text-sm-start gap-4">
                <div
                    class="bg-warning text-dark rounded-circle p-3 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm"
                    style="width: 60px; height: 60px;"><i class="bi bi-shop fs-3"></i></div>
                <div class="w-100">
                    <h5 class="fw-bold mb-1 text-body">Lengkapi Profil Tokomu!</h5>
                    <p class="mb-3 small text-secondary">Pelanggan belum bisa melihat katalogmu karena informasi toko
                        masih kosong.</p>
                    <a href="{{ route('dashboard') }}"
                       class="btn btn-warning fw-bold rounded-pill px-4 border-0 text-dark">Atur Sekarang</a>
                </div>
            </div>
        </div>
    @else
        {{-- Modern Stats Row (Omset Harian & Bulanan) --}}
        <div class="row g-2 g-md-4 mb-4">

            {{-- Omset Hari Ini --}}
            <div class="col-6 col-xl-4">
                <div
                    class="card h-100 dash-card position-relative overflow-hidden border bg-body p-2 p-md-3">
                    <div class="position-absolute top-0 end-0 p-2 p-md-3" style="opacity: 0.12;">
                        <i class="bi bi-wallet2" style="font-size: 3.5rem; color: #F97316;"></i>
                    </div>
                    <div class="card-body p-2 p-md-4 position-relative z-1 d-flex flex-column justify-content-between"
                         style="min-height: 130px;">
                        <div class="d-flex justify-content-between align-items-start mb-2 mb-md-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 35px; height: 35px; background-color: rgba(249, 115, 22, 0.1); color: #F97316;">
                                <i class="bi bi-wallet2 fs-6"></i>
                            </div>
                            <span class="badge text-secondary border rounded-pill px-2 py-1 bg-body-tertiary"
                                  style="font-size: 0.65rem;">Hari Ini</span>
                        </div>
                        <div>
                            <div class="stat-number text-body mb-1" style="font-size: clamp(1.25rem, 4vw, 2.75rem);">
                                Rp {{ number_format($stats['revenue_today'], 0, ',', '.') }}
                            </div>
                            <p class="text-secondary small fw-bold mb-0 opacity-75" style="font-size: 0.65rem;">{{ $stats['orders_today'] }} Transaksi
                                @if($stats['revenue_trend_today'] != 0)
                                    <span
                                        class="ms-1 px-1 rounded {{ $stats['revenue_trend_today'] > 0 ? 'bg-success text-white' : 'bg-danger text-white' }}">
                                        <i class="bi {{ $stats['revenue_trend_today'] > 0 ? 'bi-arrow-up-right' : 'bi-arrow-down-right' }}"></i> {{ abs($stats['revenue_trend_today']) }}%
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Omset Bulan Ini --}}
            <div class="col-6 col-xl-4">
                <div
                    class="card h-100 dash-card position-relative overflow-hidden border bg-body p-2 p-md-3">
                    <div class="position-absolute top-0 end-0 p-2 p-md-3" style="opacity: 0.12;">
                        <i class="bi bi-graph-up-arrow" style="font-size: 3.5rem; color: #10B981;"></i>
                    </div>
                    <div class="card-body p-2 p-md-4 position-relative z-1 d-flex flex-column justify-content-between"
                         style="min-height: 130px;">
                        <div class="d-flex justify-content-between align-items-start mb-2 mb-md-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 35px; height: 35px; background-color: rgba(16, 185, 129, 0.1); color: #10B981;">
                                <i class="bi bi-graph-up-arrow fs-6"></i>
                            </div>
                            <span class="badge text-secondary border rounded-pill px-2 py-1 bg-body-tertiary"
                                  style="font-size: 0.65rem;">Bulan Ini</span>
                        </div>
                        <div>
                            <div class="stat-number text-body mb-1" style="font-size: clamp(1.25rem, 4vw, 2.75rem);">
                                Rp {{ number_format($stats['revenue_month'], 0, ',', '.') }}
                            </div>
                            <p class="text-secondary small fw-bold mb-0 opacity-75" style="font-size: 0.65rem;">Pendapatan
                                @if($stats['revenue_trend_month'] != 0)
                                    <span
                                        class="ms-1 px-1 rounded {{ $stats['revenue_trend_month'] > 0 ? 'bg-success text-white' : 'bg-danger text-white' }}">
                                        <i class="bi {{ $stats['revenue_trend_month'] > 0 ? 'bi-arrow-up-right' : 'bi-arrow-down-right' }}"></i> {{ abs($stats['revenue_trend_month']) }}%
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pesanan Pending --}}
            <div class="col-12 col-xl-4 mt-2 mt-xl-0">
                <div
                    class="card h-100 dash-card position-relative overflow-hidden border bg-body p-2 p-md-3 {{ $stats['pending_orders'] > 0 ? 'border-danger border-2' : 'border-secondary border-opacity-25' }}"
                    style="{{ $stats['pending_orders'] > 0 ? 'border-color: rgba(220,53,69,0.4) !important; background-color: rgba(220,53,69,0.04) !important;' : '' }}">
                        <div class="position-absolute top-0 end-0 p-2 p-md-3" style="opacity: 0.12;">
                            <i class="bi bi-hourglass-split"
                               style="font-size: 4rem; color: {{ $stats['pending_orders'] > 0 ? '#dc3545' : 'var(--bs-secondary-color)' }};"></i>
                        </div>
                        <div class="card-body p-3 p-md-4 d-flex flex-column justify-content-between" style="min-height: 100px;">
                            <div class="d-flex justify-content-between align-items-start mb-2 mb-md-3">
                                <div
                                    class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 40px; height: 40px;">
                                    <i class="bi bi-hourglass-split fs-5"></i>
                                </div>
                                <span
                                    class="badge text-secondary border rounded-pill px-3 py-1 bg-body-tertiary"
                                    style="font-size: 0.72rem;">Antrean</span>
                            </div>
                        <div>
                            <div class="stat-number {{ $stats['pending_orders'] > 0 ? 'text-danger' : 'text-body' }} mb-1" style="font-size: clamp(2rem, 4vw, 2.75rem);">
                                {{ $stats['pending_orders'] }}
                            </div>
                            <p class="text-secondary small fw-bold mb-0 opacity-75">Pesanan Menunggu Diproses</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <div class="card dash-card bg-body border"
                     style="border-color: var(--bs-border-color-translucent) !important;">
                    <div class="card-header bg-transparent border-0 pt-4 px-4">
                        <h5 class="fw-bold mb-0 text-body" style="font-family: var(--font-serif), sans-serif;"><i
                                class="bi bi-graph-up text-primary me-2"></i>Trend Pendapatan (7 Hari)</h5>
                    </div>
                    <div class="card-body p-4 pt-2" wire:ignore>
                        <div id="revenueChart" style="min-height: 250px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 g-md-4">
            {{-- Operational Insights --}}
            <div class="col-xl-8">
                <div class="row g-3 g-md-4 h-100">
                    {{-- Metode Pembayaran --}}
                    <div class="col-md-6 mb-3 mb-md-0">
                        <div class="card dash-card h-100 bg-body border" style="border-color: var(--bs-border-color-translucent) !important;">
                            <div class="card-header bg-transparent border-0 pt-4 pb-2 px-4">
                                <h6 class="fw-bold mb-0 text-body" style="font-family: var(--font-serif), sans-serif;"><i class="bi bi-credit-card-2-front-fill me-2 text-primary"></i>Metode Pembayaran (Bulan Ini)</h6>
                            </div>
                            <div class="card-body p-3 p-md-4 pt-2">
                                @if(count($paymentMethods) > 0)
                                    @php 
                                        $totalPayments = $paymentMethods->sum('total'); 
                                        $colors = ['cash' => 'success', 'qris' => 'info', 'transfer' => 'primary'];
                                        $icons = ['cash' => 'cash-stack', 'qris' => 'qr-code-scan', 'transfer' => 'bank'];
                                        $labels = ['cash' => 'Tunai (Cash)', 'qris' => 'QRIS', 'transfer' => 'Transfer Bank'];
                                    @endphp
                                    <div class="d-flex flex-column gap-3">
                                        @foreach($paymentMethods as $pm)
                                            @php 
                                                $pct = $totalPayments > 0 ? round(($pm->total / $totalPayments) * 100) : 0; 
                                                $c = $colors[$pm->payment_method] ?? 'secondary';
                                                $i = $icons[$pm->payment_method] ?? 'credit-card';
                                                $l = $labels[$pm->payment_method] ?? ucfirst($pm->payment_method);
                                            @endphp
                                            <div>
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="small fw-bold text-body"><i class="bi bi-{{$i}} text-{{$c}} me-1"></i> {{$l}}</span>
                                                    <span class="small text-secondary fw-bold">{{$pct}}% ({{$pm->total}})</span>
                                                </div>
                                                <div class="progress" style="height: 8px; border-radius: 4px; background-color: var(--bs-secondary-bg);">
                                                    <div class="progress-bar bg-{{$c}}" role="progressbar" style="width: {{$pct}}%" aria-valuenow="{{$pct}}" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4 rounded-4 bg-body-tertiary border">
                                        <small class="text-secondary fw-bold">Belum ada data pembayaran.</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Channel Pesanan --}}
                    <div class="col-md-6">
                        <div class="card dash-card h-100 bg-body border" style="border-color: var(--bs-border-color-translucent) !important;">
                            <div class="card-header bg-transparent border-0 pt-4 pb-2 px-4">
                                <h6 class="fw-bold mb-0 text-body" style="font-family: var(--font-serif), sans-serif;"><i class="bi bi-shop-window me-2 text-warning"></i>Sumber Pesanan (Bulan Ini)</h6>
                            </div>
                            <div class="card-body p-3 p-md-4 pt-2">
                                @if(count($orderTypes) > 0)
                                    @php 
                                        $totalTypes = $orderTypes->sum('total'); 
                                        $tColors = ['retail' => 'secondary', 'dinein' => 'warning', 'takeaway' => 'danger', 'online' => 'success'];
                                        $tIcons = ['retail' => 'bag', 'dinein' => 'cup-hot', 'takeaway' => 'box-seam', 'online' => 'globe'];
                                        $tLabels = ['retail' => 'Kasir Retail', 'dinein' => 'Dine-in (Makan di Tempat)', 'takeaway' => 'Takeaway (Bawa Pulang)', 'online' => 'Online Order'];
                                    @endphp
                                    <div class="d-flex flex-column gap-3">
                                        @foreach($orderTypes as $ot)
                                            @php 
                                                $pct = $totalTypes > 0 ? round(($ot->total / $totalTypes) * 100) : 0; 
                                                $c = $tColors[$ot->order_type] ?? 'primary';
                                                $i = $tIcons[$ot->order_type] ?? 'shop';
                                                $l = $tLabels[$ot->order_type] ?? ucfirst($ot->order_type);
                                            @endphp
                                            <div>
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="small fw-bold text-body"><i class="bi bi-{{$i}} text-{{$c}} me-1"></i> {{$l}}</span>
                                                    <span class="small text-secondary fw-bold">{{$ot->total}} Trx</span>
                                                </div>
                                                <div class="progress" style="height: 8px; border-radius: 4px; background-color: var(--bs-secondary-bg);">
                                                    <div class="progress-bar bg-{{$c}}" role="progressbar" style="width: {{$pct}}%" aria-valuenow="{{$pct}}" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4 rounded-4 bg-body-tertiary border">
                                        <small class="text-secondary fw-bold">Belum ada data pesanan.</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar Kanan (Produk Terlaris & Aksi Cepat) --}}
            <div class="col-xl-4">
                <div class="d-flex flex-column gap-3 gap-md-4 h-100">

                    {{-- WIDGET SALDO KREDIT (BARU - PREMIUM BANK CARD LAYOUT) --}}
                    {{-- WIDGET SALDO KREDIT (BARU - PREMIUM BANK CARD LAYOUT) --}}
                    <div
                        class="card dash-card border bg-body p-1 position-relative overflow-hidden mb-2">
                        <div class="card-body p-3 p-md-4 d-flex flex-column justify-content-between"
                             style="min-height: 160px;">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex flex-column">
                                    <span class="text-secondary small fw-bold"
                                          style="font-size: 0.65rem; letter-spacing: 1.5px; text-transform: uppercase;">SALDO PAKAIAPP</span>
                                    <span class="text-muted small fw-medium mt-1"
                                          style="font-size: 0.62rem;">Pakaiapp Credit Account</span>
                                </div>
                                {{-- Icon: solid orange --}}
                                <div class="rounded-circle d-flex align-items-center justify-content-center"
                                     style="width: 45px; height: 45px; background-color: rgba(249, 115, 22, 0.1); color: #F97316;">
                                    <i class="bi bi-wallet2 fs-5"></i>
                                </div>
                            </div>

                            <div class="my-3">
                                <div class="stat-number text-body {{ $stats['wallet_balance'] < 3000 ? 'text-danger animate-pulse' : '' }} mb-1">
                                    Rp {{ number_format($stats['wallet_balance'], 0, ',', '.') }}
                                </div>
                                @if($stats['wallet_balance'] < 3000)
                                    <span
                                        class="badge bg-danger bg-opacity-20 text-danger border border-danger border-opacity-20 rounded-pill px-2.5 py-1 fw-bold mt-1"
                                        style="font-size: 0.65rem;">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Saldo menipis, isi ulang!
                                    </span>
                                @else
                                    <span class="badge rounded-pill px-2.5 py-1 fw-bold mt-1 text-secondary bg-body-tertiary border"
                                          style="font-size: 0.65rem;">
                                        <i class="bi bi-check-circle-fill text-success me-1"></i>
                                        Cukup untuk ~{{ floor($stats['wallet_balance'] / $stats['fee_per_trx']) }} transaksi
                                    </span>
                                @endif
                            </div>

                            <div
                                class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top border-secondary border-opacity-10">
                                <span class="text-secondary small font-monospace"
                                      style="font-size: 0.72rem;">ID: {{ strtoupper(tenant('id')) }}</span>
                                <button class="btn btn-sm btn-outline-primary rounded-pill fw-bold px-3 py-1 shadow-sm"
                                        data-bs-toggle="modal" data-bs-target="#topUpModal"
                                        style="font-size: 0.75rem;"
                                        type="button">
                                    <i class="bi bi-plus-lg me-1"></i> Top Up
                                </button>
                            </div>
                        </div>
                    </div>
                    {{-- END WIDGET SALDO KREDIT --}}


                    {{-- Widget Produk Terlaris --}}
                    <div class="card dash-card flex-grow-1 p-2 bg-body border"
                         style="border-color: var(--bs-border-color-translucent) !important;">
                        <div class="card-header border-0 bg-transparent pt-3 px-3 d-flex align-items-center gap-2">
                            <i class="bi bi-star-fill text-warning fs-5"></i>
                            <h6 class="fw-bold mb-0 text-body" style="font-family: var(--font-serif), sans-serif;">Menu
                                Terlaris Bulan Ini</h6>
                        </div>
                        <div class="card-body p-3 pt-1 bg-body">
                            @if(count($topProducts) > 0)
                                <div class="d-flex flex-column gap-2">
                                    @foreach($topProducts as $index => $item)
                                        <div
                                            class="d-flex align-items-center justify-content-between p-2 rounded-3 border bg-body-tertiary"
                                            style="border-color: var(--bs-border-color-translucent) !important;">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rank-badge-solid {{ $index == 0 ? 'gold' : ($index == 1 ? 'silver' : ($index == 2 ? 'bronze' : 'other')) }}">
                                                    {{ $index + 1 }}
                                                </div>
                                                <div class="fw-bold small text-truncate text-body"
                                                     style="max-width: 150px;">{{ $item->product_name }}</div>
                                            </div>
                                            <span class="badge border shadow-sm rounded-pill bg-body text-secondary"><i
                                                    class="bi bi-graph-up-arrow text-success me-1"></i> {{ $item->total_sold }} Terjual</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4 rounded-4 bg-body-tertiary border">
                                    <small class="text-secondary fw-bold">Belum ada data penjualan bulan ini.</small>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Widget Produk Kurang Laku --}}
                    <div class="card dash-card flex-grow-1 p-2 bg-body border mt-3"
                         style="border-color: var(--bs-border-color-translucent) !important;">
                        <div class="card-header border-0 bg-transparent pt-3 px-3 d-flex align-items-center gap-2">
                            <i class="bi bi-arrow-down-right-circle-fill text-danger fs-5"></i>
                            <h6 class="fw-bold mb-0 text-body" style="font-family: var(--font-serif), sans-serif;">Perlu Perhatian (Kurang Laku)</h6>
                        </div>
                        <div class="card-body p-3 pt-1 bg-body">
                            @if(count($slowMovingProducts) > 0)
                                <div class="d-flex flex-column gap-2">
                                    @foreach($slowMovingProducts as $index => $item)
                                        <div
                                            class="d-flex align-items-center justify-content-between p-2 rounded-3 border bg-body-tertiary"
                                            style="border-color: var(--bs-border-color-translucent) !important;">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-circle bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center flex-shrink-0" style="width: 28px; height: 28px; font-size: 0.75rem; font-weight: bold;">
                                                    !
                                                </div>
                                                <div class="fw-bold small text-truncate text-body"
                                                     style="max-width: 150px;">{{ $item->name }}</div>
                                            </div>
                                            <span class="badge border shadow-sm rounded-pill bg-body text-secondary"><i
                                                    class="bi bi-graph-down-arrow text-danger me-1"></i> {{ $item->total_sold }} Terjual</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4 rounded-4 bg-body-tertiary border">
                                    <small class="text-secondary fw-bold">Semua produk terjual dengan baik!</small>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Widget Waktu Tersibuk --}}
                    <div class="card dash-card flex-grow-1 p-2 bg-body border mt-3"
                         style="border-color: var(--bs-border-color-translucent) !important;">
                        <div class="card-header border-0 bg-transparent pt-3 px-3 d-flex align-items-center gap-2">
                            <i class="bi bi-clock-history text-primary fs-5"></i>
                            <h6 class="fw-bold mb-0 text-body" style="font-family: var(--font-serif), sans-serif;">Waktu Penjualan Tersibuk</h6>
                        </div>
                        <div class="card-body p-3 pt-1 bg-body">
                            @if(count($peakSalesTimes) > 0)
                                <div class="d-flex flex-column gap-2">
                                    @foreach($peakSalesTimes as $index => $item)
                                        <div
                                            class="d-flex align-items-center justify-content-between p-2 rounded-3 border bg-body-tertiary"
                                            style="border-color: var(--bs-border-color-translucent) !important;">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 28px; height: 28px; font-size: 0.75rem; font-weight: bold;">
                                                    {{ $index + 1 }}
                                                </div>
                                                <div class="fw-bold small text-truncate text-body">
                                                    {{ $item->time_range }}
                                                </div>
                                            </div>
                                            <span class="badge border shadow-sm rounded-pill bg-body text-secondary"><i
                                                    class="bi bi-bag-check-fill text-primary me-1"></i> {{ $item->orders }} Order</span>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-2 text-center">
                                    <small class="text-secondary opacity-75" style="font-size: 0.65rem;">Berdasarkan 30 hari terakhir</small>
                                </div>
                            @else
                                <div class="text-center py-4 rounded-4 bg-body-tertiary border">
                                    <small class="text-secondary fw-bold">Belum ada data waktu penjualan.</small>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Quick Actions --}}
                    <div class="card dash-card p-2 bg-body border"
                         style="border-color: var(--bs-border-color-translucent) !important;">
                        <div class="card-body p-3 bg-body">
                            <h6 class="fw-bold mb-3 text-body" style="font-family: var(--font-serif), sans-serif;">Aksi
                                Cepat</h6>
                            <div class="d-flex flex-column gap-2">
                                <a href="{{ route('product.create') }}" wire:navigate
                                   class="quick-action-btn d-flex align-items-center text-start gap-3 p-3 text-decoration-none text-body">
                                    <div
                                        class="icon-wrapper bg-primary bg-opacity-10 text-primary rounded-circle p-2 flex-shrink-0 d-flex justify-content-center align-items-center"
                                        style="width: 40px; height: 40px;">
                                        <i class="bi bi-plus-lg fs-5"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 small">Tambah Produk Baru</h6>
                                        <small class="text-secondary d-block opacity-75" style="font-size: 0.65rem;">Perbarui
                                            katalog jualanmu</small>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
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
