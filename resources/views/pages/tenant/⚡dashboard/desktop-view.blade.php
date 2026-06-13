{{-- Welcome Header Row --}}
<div class="row align-items-center mb-4 pb-3 border-bottom pt-3">
    {{-- Store Identity Left Column --}}
    <div class="col-12 col-md-6 mb-3 mb-md-0">
        <div class="d-inline-flex align-items-center gap-2 flex-wrap">
            <h2 class="page-store-name mb-0 fw-bold text-body" style="font-size: 1.75rem;">
                {{ $store->name ?? 'Setup Tokomu' }}
            </h2>
            @if($store)
                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2.5 py-1 fw-bold d-flex align-items-center gap-1 border border-success border-opacity-10" style="font-size: 0.65rem; height: fit-content;">
                    <span class="active-glow-dot" style="width:6px;height:6px;"></span> Online
                </span>
                <a href="{{ url('/') }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1 text-decoration-none d-flex align-items-center gap-1 ms-1" style="font-size: 0.72rem; height: fit-content; font-weight: 600;">
                    <i class="bi bi-box-arrow-up-right"></i> Buka Toko
                </a>
            @endif
        </div>
    </div>

    {{-- Actions & Wallet Right Column --}}
    <div class="col-12 col-md-6 text-md-end">
        <div class="d-flex flex-wrap align-items-center justify-content-start justify-content-md-end gap-3">
            {{-- Profile Info --}}
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.8rem;">
                    {{ substr(explode(' ', $user->name)[0], 0, 1) }}
                </div>
                <span class="text-secondary small fw-medium">
                    Halo, {{ explode(' ', $user->name)[0] }}
                </span>
            </div>
            
            <span class="text-secondary opacity-25 d-none d-md-inline">|</span>

            {{-- Saldo Pakaiapp Wallet Badge --}}
            <div class="d-flex align-items-center justify-content-between gap-3 py-1.5 px-3 rounded-pill border bg-body shadow-sm" style="border-color: var(--bs-border-color-translucent) !important; font-size: 0.85rem;">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning flex-shrink-0" style="width: 28px; height: 28px;">
                        <i class="bi bi-wallet2" style="font-size: 0.85rem;"></i>
                    </div>
                    <div class="d-flex flex-column lh-1 text-start">
                        <span class="text-secondary small fw-bold" style="font-size: 0.55rem; letter-spacing: 0.5px; text-transform: uppercase;">Saldo Pakaiapp</span>
                        <div class="d-flex align-items-center gap-2 mt-0.5">
                            <span class="fw-bold text-body {{ $stats['wallet_balance'] < 3000 ? 'text-danger animate-pulse' : '' }}" style="font-size: 0.9rem;">
                                Rp {{ number_format($stats['wallet_balance'], 0, ',', '.') }}
                            </span>
                            @if($stats['wallet_balance'] >= 3000)
                                <span class="badge bg-body-tertiary text-secondary border fw-medium" style="font-size: 0.55rem; padding: 2px 4px;">~{{ floor($stats['wallet_balance'] / $stats['fee_per_trx']) }} trx</span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-10 fw-medium animate-pulse" style="font-size: 0.55rem; padding: 2px 4px;">Habis!</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="border-start ps-3 py-0.5">
                    <button class="btn btn-sm p-0 text-primary fw-bold hover-translate" data-bs-toggle="modal" data-bs-target="#topUpModal" type="button">
                        <i class="bi bi-plus-circle-fill fs-5"></i>
                    </button>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="d-flex align-items-center gap-2">
                <button wire:click="exportLaporan" class="btn btn-outline-secondary btn-sm py-2 px-3 rounded-3 d-flex align-items-center gap-1" wire:loading.attr="disabled" style="font-size: 0.8rem; font-weight: 600;">
                    <span wire:loading.remove wire:target="exportLaporan"><i class="bi bi-file-earmark-excel-fill text-success fs-6 me-1"></i> Export</span>
                    <span wire:loading wire:target="exportLaporan"><span class="spinner-border spinner-border-sm me-1"></span> Proses...</span>
                </button>
                <a href="{{ route('cashier') }}" wire:navigate.hover class="btn btn-warning btn-sm py-2 px-3 rounded-3 d-flex align-items-center justify-content-center gap-1 text-white fw-bold" style="font-size: 0.8rem; font-weight: 750; background-color: var(--brand-caramel, #b45309); border: none;">
                    <i class="bi bi-cart-check-fill fs-6"></i> Kasir
                    @if($newOrderCount > 0)
                        <span class="badge rounded-circle ms-1 bg-white text-danger" style="font-size: 0.7rem;">{{ $newOrderCount }}</span>
                    @endif
                </a>
            </div>
        </div>
    </div>
</div>

{{-- New Order Notification Glassmorphism Island --}}
@if($newOrderCount > 0)
    <div class="notif-new-order p-3 mb-4 transition-all" style="position: relative; overflow: hidden; border-radius: 1rem;">
        {{-- Decorative Subtle Glow Behind --}}
        <div class="position-absolute rounded-circle" style="width: 150px; height: 150px; background: rgba(202, 138, 4, 0.15); filter: blur(40px); top: -50px; right: -30px; pointer-events: none;"></div>

        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 position-relative z-1">
            <div class="d-flex align-items-center gap-3">
                {{-- Glowing Icon Wrapper --}}
                <div class="d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm rounded-circle position-relative bg-body border" style="width: 48px; height: 48px; border-color: rgba(180, 83, 9, 0.2) !important;">
                    <div class="position-absolute rounded-circle bg-warning opacity-25 w-100 h-100" style="animation: pulse-glow 2s infinite;"></div>
                    <i class="bi bi-bell-fill fs-5" style="color: var(--brand-caramel, #b45309); animation: smooth-bounce 2.5s ease infinite;"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h6 class="fw-black mb-0 text-body" style="letter-spacing: -0.3px;">{{ $newOrderCount }} Pesanan Baru Masuk!</h6>
                        <span class="badge bg-danger rounded-pill fw-bold animate-pulse" style="font-size: 0.65rem; padding: 0.25rem 0.5rem;">LIVE</span>
                    </div>
                    <p class="mb-0 small text-secondary fw-medium mt-0.5 opacity-75">Antrian kasir bertambah. Segera periksa dan proses pesanan pelanggan.</p>
                </div>
            </div>
            {{-- Premium Action Button --}}
            <button wire:click="acknowledgeOrders" class="btn btn-caramel-solid flex-shrink-0 w-100 w-md-auto" type="button">
                <i class="bi bi-check2-all me-1"></i> Selesai Periksa
            </button>
        </div>
    </div>

    {{-- Refined Micro-Animations --}}
    <style>
        @keyframes smooth-bounce {
            0%, 100% { transform: translateY(0) rotate(0); }
            10% { transform: translateY(-4px) rotate(-8deg); }
            20% { transform: translateY(-4px) rotate(8deg); }
            30% { transform: translateY(0) rotate(-4deg); }
            40% { transform: translateY(0) rotate(4deg); }
            50% { transform: translateY(0) rotate(0); }
        }

        @keyframes pulse-glow {
            0% { transform: scale(0.9); opacity: 0.3; }
            50% { transform: scale(1.2); opacity: 0; }
            100% { transform: scale(0.9); opacity: 0.3; }
        }

        .animate-pulse {
            animation: text-pulse 1.5s infinite ease-in-out;
        }

        @keyframes text-pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
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
            } catch (e) {}
        }
    </script>
@else
    <script>window._newOrderAlertPlayed = false;</script>
@endif

@if(!$store)
    <div class="card bg-warning bg-opacity-10 border-0 rounded-4 p-4 mb-5 dash-card">
        <div class="d-flex flex-column flex-sm-row align-items-center text-center text-sm-start gap-4">
            <div class="bg-warning text-dark rounded-circle p-3 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 60px; height: 60px;">
                <i class="bi bi-shop fs-3"></i>
            </div>
            <div class="w-100">
                <h5 class="fw-bold mb-1 text-body">Lengkapi Profil Tokomu!</h5>
                <p class="mb-3 small text-secondary">Pelanggan belum bisa melihat katalogmu karena informasi toko masih kosong.</p>
                <a href="{{ route('dashboard') }}" class="btn btn-warning fw-bold rounded-pill px-4 border-0 text-dark">Atur Sekarang</a>
            </div>
        </div>
    </div>
@else
    {{-- ROW 1: CORE METRICS GRID --}}
    <div class="row g-3 mb-4">
        {{-- Omset Hari Ini Card --}}
        <div class="col-12 col-md-4">
            <div class="card h-100 rounded-3 shadow-sm border p-3">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning" style="width: 40px; height: 40px;">
                        <i class="bi bi-wallet2 fs-5" style="color: var(--brand-caramel, #b45309);"></i>
                    </div>
                    <span class="badge text-secondary border rounded-pill px-2.5 py-1 bg-body-tertiary" style="font-size: 0.72rem;">Hari Ini</span>
                </div>
                <div>
                    <h3 class="fw-bold text-body mb-2" style="font-size: 1.75rem; letter-spacing: -0.5px;">
                        Rp {{ number_format($stats['revenue_today'], 0, ',', '.') }}
                    </h3>
                    <div class="d-flex flex-column gap-1">
                        <p class="text-secondary small fw-bold mb-0 opacity-75" style="font-size: 0.72rem;">
                            {{ $stats['orders_today'] }} Transaksi
                            @if($stats['revenue_trend_today'] != 0)
                                <span class="ms-1 px-1.5 py-0.5 rounded {{ $stats['revenue_trend_today'] > 0 ? 'bg-success text-white' : 'bg-danger text-white' }}" style="font-size: 0.65rem;">
                                    <i class="bi {{ $stats['revenue_trend_today'] > 0 ? 'bi-arrow-up-right' : 'bi-arrow-down-right' }}"></i> {{ abs($stats['revenue_trend_today']) }}%
                                </span>
                            @endif
                        </p>
                        <p class="text-success fw-bold mb-0" style="font-size: 0.78rem;">
                            <i class="bi bi-piggy-bank-fill me-1"></i>Laba: Rp {{ number_format($stats['profit_today'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pendapatan Bulan Ini Card --}}
        <div class="col-12 col-md-4">
            <div class="card h-100 rounded-3 shadow-sm border p-3">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success" style="width: 40px; height: 40px;">
                        <i class="bi bi-graph-up-arrow fs-5"></i>
                    </div>
                    <span class="badge text-secondary border rounded-pill px-2.5 py-1 bg-body-tertiary" style="font-size: 0.72rem;">Bulan Ini</span>
                </div>
                <div>
                    <h3 class="fw-bold text-body mb-2" style="font-size: 1.75rem; letter-spacing: -0.5px;">
                        Rp {{ number_format($stats['revenue_month'], 0, ',', '.') }}
                    </h3>
                    <div class="d-flex flex-column gap-1">
                        <p class="text-secondary small fw-bold mb-0 opacity-75" style="font-size: 0.72rem;">
                            Pendapatan
                            @if($stats['revenue_trend_month'] != 0)
                                <span class="ms-1 px-1.5 py-0.5 rounded {{ $stats['revenue_trend_month'] > 0 ? 'bg-success text-white' : 'bg-danger text-white' }}" style="font-size: 0.65rem;">
                                    <i class="bi {{ $stats['revenue_trend_month'] > 0 ? 'bi-arrow-up-right' : 'bi-arrow-down-right' }}"></i> {{ abs($stats['revenue_trend_month']) }}%
                                </span>
                            @endif
                        </p>
                        <p class="text-success fw-bold mb-0" style="font-size: 0.78rem;">
                            <i class="bi bi-piggy-bank-fill me-1"></i>Laba: Rp {{ number_format($stats['profit_month'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Antrean Card --}}
        <div class="col-12 col-md-4">
            <div class="card h-100 rounded-3 shadow-sm border p-3 {{ $stats['pending_orders'] > 0 ? 'card-pending-alert' : '' }}">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center {{ $stats['pending_orders'] > 0 ? 'bg-danger bg-opacity-10 text-danger' : 'bg-secondary bg-opacity-10 text-secondary' }}" style="width: 40px; height: 40px;">
                        <i class="bi bi-hourglass-split fs-5"></i>
                    </div>
                    <span class="badge text-secondary border rounded-pill px-2.5 py-1 bg-body-tertiary" style="font-size: 0.72rem;">Antrean</span>
                </div>
                <div>
                    <h3 class="fw-bold {{ $stats['pending_orders'] > 0 ? 'text-danger' : 'text-body' }} mb-2" style="font-size: 1.75rem; letter-spacing: -0.5px;">
                        {{ $stats['pending_orders'] }} Pesanan
                    </h3>
                    <p class="text-secondary small fw-bold mb-0 opacity-75" style="font-size: 0.72rem;">Pesanan Menunggu Diproses</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ROW 2: MAIN CONTENT & SIDEBAR SPLIT --}}
    <div class="row g-4 align-items-start">
        {{-- LEFT MAIN COLUMN --}}
        <div class="col-12 col-lg-8 d-flex flex-column gap-3">
            {{-- Item A: AI Daily Briefing --}}
            <div>
                <livewire:pages::tenant.ai-daily-briefing
                    :stats="$stats"
                    :topProducts="$topProducts"
                    :slowMovingProducts="$slowMovingProducts"
                    :key="'ai-briefing-' . time()"
                />
            </div>

            {{-- Item B: Trend Pendapatan (7 Hari) --}}
            <div class="card rounded-3 shadow-sm border p-3">
                <h5 class="fw-bold mb-3 text-body" style="font-size: 1.1rem;"><i class="bi bi-graph-up text-primary me-2"></i>Trend Pendapatan (7 Hari)</h5>
                
                <div class="position-relative" wire:ignore>
                    @php
                        $chartDataArr = json_decode($chartData ?? '[]');
                        $hasChartData = false;
                        if (is_array($chartDataArr) && count($chartDataArr) > 0) {
                            foreach ($chartDataArr as $item) {
                                if (($item->revenue ?? 0) > 0) {
                                    $hasChartData = true;
                                    break;
                                }
                            }
                        }
                    @endphp
                    
                    @if(!$hasChartData)
                        <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center z-3 chart-empty-overlay">
                            <div class="text-center d-flex flex-column align-items-center">
                                <i class="bi bi-graph-up text-secondary opacity-50 fs-2 mb-2"></i>
                                <p class="mb-0 text-secondary fw-medium small" style="font-size: 0.8rem;">Belum ada data penjualan 7 hari terakhir.</p>
                            </div>
                        </div>
                    @endif
                    
                    <div id="revenueChart" style="min-height: 250px; @if(!$hasChartData) opacity: 0.35; @endif"></div>
                </div>
            </div>

            {{-- Item C: Sub-row for breakdown metrics --}}
            <div class="row g-3">
                {{-- Metode Pembayaran Card --}}
                <div class="col-12 col-md-6">
                    <div class="card h-100 rounded-3 shadow-sm border p-3">
                        <h6 class="fw-bold mb-3 text-body" style="font-size: 0.95rem;"><i class="bi bi-credit-card-2-front-fill me-2 text-primary"></i>Metode Pembayaran (Bulan Ini)</h6>
                        
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
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-{{$c}}" role="progressbar" style="width: {{$pct}}%" aria-valuenow="{{$pct}}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="d-flex flex-column align-items-center justify-content-center py-4">
                                <i class="bi bi-credit-card text-muted opacity-50 fs-2 mb-2"></i>
                                <span class="text-muted small text-center">Belum ada data pembayaran.</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Sumber Pesanan Card --}}
                <div class="col-12 col-md-6">
                    <div class="card h-100 rounded-3 shadow-sm border p-3">
                        <h6 class="fw-bold mb-3 text-body" style="font-size: 0.95rem;"><i class="bi bi-shop-window me-2 text-warning"></i>Sumber Pesanan (Bulan Ini)</h6>
                        
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
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-{{$c}}" role="progressbar" style="width: {{$pct}}%" aria-valuenow="{{$pct}}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="d-flex flex-column align-items-center justify-content-center py-4">
                                <i class="bi bi-shop text-muted opacity-50 fs-2 mb-2"></i>
                                <span class="text-muted small text-center">Belum ada data pesanan.</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT SIDEBAR COLUMN --}}
        <div class="col-12 col-lg-4 d-flex flex-column gap-3">
            {{-- Item A: Produk Terlaris Bulan Ini --}}
            <div class="card rounded-3 shadow-sm border p-3">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-star-fill text-warning fs-5"></i>
                    <h6 class="fw-bold mb-0 text-body" style="font-size: 0.95rem;">Produk Terlaris Bulan Ini</h6>
                </div>

                @if(count($topProducts) > 0)
                    <div class="d-flex flex-column gap-2">
                        @foreach($topProducts as $index => $item)
                            <div class="d-flex align-items-center justify-content-between p-2 rounded-3 border bg-body-tertiary" style="border-color: var(--bs-border-color-translucent) !important;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rank-badge {{ $index == 0 ? 'rank-gold' : ($index == 1 ? 'rank-silver' : ($index == 2 ? 'rank-bronze' : 'rank-default')) }}">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="fw-bold small text-truncate text-body" style="max-width: 150px;">{{ $item->product_name }}</div>
                                </div>
                                <span class="badge border shadow-sm rounded-pill bg-body text-secondary"><i class="bi bi-graph-up-arrow text-success me-1"></i> {{ $item->total_sold }} Terjual</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="d-flex flex-column align-items-center justify-content-center py-4 text-center">
                        <i class="bi bi-box-seam text-muted opacity-50 fs-2 mb-2"></i>
                        <span class="text-muted small">Belum ada data penjualan bulan ini.</span>
                    </div>
                @endif
            </div>

            {{-- Item B: Perlu Perhatian (Kurang Laku) --}}
            <div class="card rounded-3 shadow-sm border p-3">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-arrow-down-right-circle-fill text-danger fs-5"></i>
                    <h6 class="fw-bold mb-0 text-body" style="font-size: 0.95rem;">Perlu Perhatian (Kurang Laku)</h6>
                </div>

                @if(count($slowMovingProducts) > 0)
                    <div class="d-flex flex-column gap-2">
                        @foreach($slowMovingProducts as $index => $item)
                            <div class="d-flex align-items-center justify-content-between p-2 rounded-3 border bg-body-tertiary" style="border-color: var(--bs-border-color-translucent) !important;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center flex-shrink-0" style="width: 28px; height: 28px; font-size: 0.75rem; font-weight: bold;">
                                        !
                                    </div>
                                    <div class="fw-bold small text-truncate text-body" style="max-width: 150px;">{{ $item->name }}</div>
                                </div>
                                <span class="badge border shadow-sm rounded-pill bg-body text-secondary"><i class="bi bi-graph-down-arrow text-danger me-1"></i> {{ $item->total_sold }} Terjual</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="d-flex flex-column align-items-center justify-content-center py-4 text-center">
                        <i class="bi bi-check2-circle text-muted fs-3 mb-2"></i>
                        <span class="text-muted small">Semua produk terjual dengan baik!</span>
                    </div>
                @endif
            </div>

            {{-- Item C: Waktu Penjualan Tersibuk --}}
            <div class="card rounded-3 shadow-sm border p-3">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-clock-history text-primary fs-5"></i>
                    <h6 class="fw-bold mb-0 text-body" style="font-size: 0.95rem;">Waktu Penjualan Tersibuk</h6>
                </div>

                @if(count($peakSalesTimes) > 0)
                    <div class="d-flex flex-column gap-2">
                        @foreach($peakSalesTimes as $index => $item)
                            <div class="d-flex align-items-center justify-content-between p-2 rounded-3 border bg-body-tertiary" style="border-color: var(--bs-border-color-translucent) !important;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 28px; height: 28px; font-size: 0.75rem; font-weight: bold;">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="fw-bold small text-truncate text-body">{{ $item->time_range }}</div>
                                </div>
                                <span class="badge border shadow-sm rounded-pill bg-body text-secondary"><i class="bi bi-bag-check-fill text-primary me-1"></i> {{ $item->orders }} Order</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-2 text-center">
                        <small class="text-muted opacity-75" style="font-size: 0.68rem;">Berdasarkan 30 hari terakhir</small>
                    </div>
                @else
                    <div class="d-flex flex-column align-items-center justify-content-center py-4 text-center">
                        <i class="bi bi-clock text-muted fs-3 mb-2"></i>
                        <span class="text-muted small">Belum ada data waktu penjualan.</span>
                    </div>
                @endif
            </div>

            {{-- Item D: Aksi Cepat - Tambah Produk Baru --}}
            <a href="{{ route('product.create') }}" wire:navigate.hover
               class="action-dashed-card d-flex flex-column align-items-center justify-content-center text-center p-4 text-decoration-none text-body rounded-3"
               style="border: 2px dashed var(--bs-border-color-translucent) !important; min-height: 140px;">
                <div class="rounded-circle d-flex justify-content-center align-items-center mb-2" style="width: 48px; height: 48px; background-color: rgba(249, 115, 22, 0.1); color: #f97316;">
                    <i class="bi bi-plus-lg fs-4"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-1 text-body" style="font-size: 0.95rem;">Tambah Produk Baru</h6>
                    <small class="text-muted d-block" style="font-size: 0.78rem;">Aksi Cepat - Perbarui katalog jualanmu</small>
                </div>
            </a>
        </div>
    </div>
@endif
