{{-- Welcome Header (Mobile Optimized) --}}
<div class="d-flex flex-column gap-3 pt-2 mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h6 class="page-greeting-label mb-1 text-secondary" style="font-size: 0.75rem;">
                Halo, {{ explode(' ', $user->name)[0] }} 👋
            </h6>
            <div class="d-flex align-items-center gap-2">
                <h2 class="page-store-name mb-0 fw-bold text-body fs-4">
                    {{ $store->name ?? 'Setup Tokomu' }}
                </h2>
                @if($store)
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-0.5 fw-bold d-flex align-items-center gap-1 border border-success border-opacity-10" style="font-size: 0.6rem;">
                        <span class="active-glow-dot" style="width:5px;height:5px;"></span> Online
                    </span>
                @endif
            </div>
        </div>
        
        {{-- Buka Toko --}}
        @if($store)
            <a href="{{ url('/') }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1 text-decoration-none" style="font-size: 0.7rem; font-weight: 600;">
                <i class="bi bi-box-arrow-up-right me-0.5"></i> Buka
            </a>
        @endif
    </div>

    {{-- Saldo & Actions --}}
    <div class="d-flex align-items-center gap-2 w-100">
        {{-- Saldo Anda --}}
        <div class="d-flex align-items-center justify-content-between gap-2 py-2 px-3 rounded-pill border bg-body shadow-sm flex-grow-1" style="border-color: var(--bs-border-color-translucent) !important;">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning flex-shrink-0" style="width: 26px; height: 26px;">
                    <i class="bi bi-wallet2" style="font-size: 0.75rem;"></i>
                </div>
                <div class="d-flex flex-column lh-1">
                    <span class="text-secondary text-uppercase fw-bold" style="font-size: 0.55rem; letter-spacing: 0.5px;">Saldo</span>
                    <span class="fw-bold text-body" style="font-size: 0.8rem;">
                        Rp {{ number_format($stats['wallet_balance'], 0, ',', '.') }}
                    </span>
                </div>
            </div>
            <button class="btn btn-sm p-0 text-primary" data-bs-toggle="modal" data-bs-target="#topUpModal" type="button">
                <i class="bi bi-plus-circle-fill" style="font-size: 1rem;"></i>
            </button>
        </div>

        {{-- Kasir Button --}}
        <a href="{{ route('cashier') }}" wire:navigate.hover class="btn btn-warning py-2 px-3 shadow-sm rounded-pill text-decoration-none text-white fw-bold d-inline-flex align-items-center justify-content-center gap-1" style="font-size: 0.8rem; height: 38px; background-color: var(--brand-caramel, #b45309); border: none;">
            <i class="bi bi-cart-check-fill"></i> Kasir
            @if($newOrderCount > 0)
                <span class="badge rounded-circle bg-white text-danger" style="font-size: 0.65rem; padding: 0.25em 0.4em;">{{ $newOrderCount }}</span>
            @endif
        </a>

        {{-- Export/Rekap --}}
        <button wire:click="exportLaporan" class="btn btn-outline-secondary py-2 px-2.5 shadow-sm rounded-pill" style="height: 38px; width: 38px; padding: 0; display: inline-flex; align-items: center; justify-content: center;" wire:loading.attr="disabled" title="Export Laporan">
            <span wire:loading.remove wire:target="exportLaporan"><i class="bi bi-file-earmark-excel-fill text-success fs-6"></i></span>
            <span wire:loading wire:target="exportLaporan"><span class="spinner-border spinner-border-sm text-success"></span></span>
        </button>
    </div>
</div>

{{-- New Order Notification Glassmorphism Island --}}
@if($newOrderCount > 0)
    <div class="notif-new-order p-3 mb-4 transition-all" style="position: relative; overflow: hidden; border-radius: 1rem;">
        {{-- Decorative Subtle Glow Behind --}}
        <div class="position-absolute rounded-circle" style="width: 150px; height: 150px; background: rgba(202, 138, 4, 0.15); filter: blur(40px); top: -50px; right: -30px; pointer-events: none;"></div>

        <div class="d-flex flex-column align-items-start justify-content-between gap-3 position-relative z-1">
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
            <button wire:click="acknowledgeOrders" class="btn btn-caramel-solid w-100 mt-2" type="button">
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
    {{-- Metrics Row (Mobile Grid at the top) --}}
    <div class="row g-3 mb-4">
        {{-- Omset Hari Ini --}}
        <div class="col-6">
            <div class="card h-100 rounded-3 shadow-sm border p-3 bg-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge text-secondary border rounded-pill px-2 py-0.5 bg-body-tertiary" style="font-size: 0.6rem;">Hari Ini</span>
                </div>
                <div>
                    <div class="text-body fw-bold mb-1" style="font-size: 1.1rem; letter-spacing: -0.5px;">
                        Rp {{ number_format($stats['revenue_today'], 0, ',', '.') }}
                    </div>
                    <span class="text-secondary fw-bold opacity-75" style="font-size: 0.65rem;">{{ $stats['orders_today'] }} Trx</span>
                </div>
            </div>
        </div>

        {{-- Omset Bulan Ini --}}
        <div class="col-6">
            <div class="card h-100 rounded-3 shadow-sm border p-3 bg-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge text-secondary border rounded-pill px-2 py-0.5 bg-body-tertiary" style="font-size: 0.6rem;">Bulan Ini</span>
                </div>
                <div>
                    <div class="text-body fw-bold mb-1" style="font-size: 1.1rem; letter-spacing: -0.5px;">
                        Rp {{ number_format($stats['revenue_month'], 0, ',', '.') }}
                    </div>
                    <span class="text-secondary fw-bold opacity-75" style="font-size: 0.65rem;">Laba: Rp {{ number_format($stats['profit_month'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Pesanan Pending --}}
        <div class="col-12">
            <div class="card rounded-3 shadow-sm border p-3 bg-body {{ $stats['pending_orders'] > 0 ? 'border-danger border-2' : '' }}"
                 style="{{ $stats['pending_orders'] > 0 ? 'background-color: rgba(220,53,69,0.04) !important;' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="bi bi-hourglass-split fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold {{ $stats['pending_orders'] > 0 ? 'text-danger' : 'text-body' }} fs-5 lh-1">
                                {{ $stats['pending_orders'] }} Pesanan
                            </div>
                            <span class="text-secondary small fw-bold opacity-75" style="font-size:0.7rem;">Antrean Pesanan</span>
                        </div>
                    </div>
                    @if($stats['pending_orders'] > 0)
                        <a href="{{ route('cashier') }}" class="btn btn-sm btn-danger rounded-pill fw-bold px-3">Proses</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- AI Daily Briefing Component --}}
    <div class="mb-4">
        <livewire:pages::tenant.ai-daily-briefing
            :stats="$stats"
            :topProducts="$topProducts"
            :slowMovingProducts="$slowMovingProducts"
            :key="'ai-briefing-mobile-' . time()"
        />
    </div>

    {{-- Trend Pendapatan (7 Hari) chart card --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="card rounded-3 shadow-sm border p-3 bg-body">
                <h6 class="fw-bold mb-3 text-body"><i class="bi bi-graph-up text-primary me-2"></i>Trend Pendapatan (7 Hari)</h6>
                
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
                                <i class="bi bi-graph-up text-secondary opacity-50 fs-3 mb-2"></i>
                                <p class="mb-0 text-secondary fw-medium" style="font-size: 0.72rem;">Belum ada data penjualan 7 hari terakhir.</p>
                            </div>
                        </div>
                    @endif
                    
                    <div id="revenueChart" style="min-height: 200px; @if(!$hasChartData) opacity: 0.35; @endif"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom Modules Stack --}}
    <div class="d-flex flex-column gap-3 pb-5">
        {{-- Quick Actions --}}
        <a href="{{ route('product.create') }}" wire:navigate.hover
           class="action-dashed-card d-flex flex-column align-items-center justify-content-center text-center p-3 text-decoration-none text-body rounded-3"
           style="border: 2px dashed var(--bs-border-color-translucent) !important; min-height: 120px;">
            <div class="rounded-circle d-flex justify-content-center align-items-center mb-2" style="width: 40px; height: 40px; background-color: rgba(249, 115, 22, 0.1); color: #f97316;">
                <i class="bi bi-plus-lg fs-5"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-1 small text-body">Tambah Produk Baru</h6>
                <small class="text-muted d-block" style="font-size: 0.65rem;">Aksi Cepat - Perbarui katalog jualanmu</small>
            </div>
        </a>

        {{-- Widget Produk Terlaris --}}
        <div class="card rounded-3 shadow-sm border p-3 bg-body">
            <div class="d-flex align-items-center gap-2 mb-3">
                <i class="bi bi-star-fill text-warning fs-5"></i>
                <h6 class="fw-bold mb-0 text-body">
                    Produk Terlaris Bulan Ini
                </h6>
            </div>
            
            @if(count($topProducts) > 0)
                <div class="d-flex flex-column gap-2">
                    @foreach($topProducts as $index => $item)
                        <div class="d-flex align-items-center justify-content-between p-2 rounded-3 border bg-body-tertiary" style="border-color: var(--bs-border-color-translucent) !important;">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rank-badge {{ $index == 0 ? 'rank-gold' : ($index == 1 ? 'rank-silver' : ($index == 2 ? 'rank-bronze' : 'rank-default')) }}">
                                    {{ $index + 1 }}
                                </div>
                                <div class="fw-bold small text-truncate text-body" style="max-width: 140px;">{{ $item->product_name }}</div>
                            </div>
                            <span class="badge border shadow-sm rounded-pill bg-body text-secondary"><i class="bi bi-graph-up-arrow text-success me-1"></i> {{ $item->total_sold }} Terjual</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="d-flex flex-column align-items-center justify-content-center py-4 text-center">
                    <i class="bi bi-box-seam text-muted opacity-50 fs-3 mb-2"></i>
                    <span class="text-muted small">Belum ada data penjualan.</span>
                </div>
            @endif
        </div>

        {{-- Metode Pembayaran --}}
        <div class="card rounded-3 shadow-sm border p-3 bg-body">
            <h6 class="fw-bold mb-3 text-body"><i class="bi bi-credit-card-2-front-fill me-2 text-primary"></i>Metode Pembayaran</h6>
            
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
                <div class="d-flex flex-column align-items-center justify-content-center py-4 text-center">
                    <i class="bi bi-credit-card text-muted opacity-50 fs-3 mb-2"></i>
                    <span class="text-muted small">Belum ada data pembayaran.</span>
                </div>
            @endif
        </div>

        {{-- Channel/Sumber Pesanan --}}
        <div class="card rounded-3 shadow-sm border p-3 bg-body">
            <h6 class="fw-bold mb-3 text-body"><i class="bi bi-shop-window me-2 text-warning"></i>Sumber Pesanan</h6>
            
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
                <div class="d-flex flex-column align-items-center justify-content-center py-4 text-center">
                    <i class="bi bi-shop text-muted opacity-50 fs-3 mb-2"></i>
                    <span class="text-muted small">Belum ada data pesanan.</span>
                </div>
            @endif
        </div>

        {{-- Widget Produk Kurang Laku (Perlu Perhatian) --}}
        <div class="card rounded-3 shadow-sm border p-3 bg-body">
            <div class="d-flex align-items-center gap-2 mb-3">
                <i class="bi bi-arrow-down-right-circle-fill text-danger fs-5"></i>
                <h6 class="fw-bold mb-0 text-body">Perlu Perhatian (Kurang Laku)</h6>
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

        {{-- Widget Waktu Tersibuk --}}
        <div class="card rounded-3 shadow-sm border p-3 bg-body">
            <div class="d-flex align-items-center gap-2 mb-3">
                <i class="bi bi-clock-history text-primary fs-5"></i>
                <h6 class="fw-bold mb-0 text-body">Waktu Penjualan Tersibuk</h6>
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
            @else
                <div class="d-flex flex-column align-items-center justify-content-center py-4 text-center">
                    <i class="bi bi-clock text-muted fs-3 mb-2"></i>
                    <span class="text-muted small">Belum ada data waktu penjualan.</span>
                </div>
            @endif
        </div>
    </div>
@endif
