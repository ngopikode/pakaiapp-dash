<div wire:poll.15s class="pb-5 min-vh-100">

    {{-- Welcome Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-4 mb-md-5 gap-3 pt-3">
        <div>
            <h6 class="text-secondary small fw-bold mb-1 text-uppercase" style="letter-spacing: 1.5px;">
                Selamat Datang, {{ explode(' ', $user->name)[0] }} 👋
            </h6>
            <h2 class="fw-bolder mb-2 text-brand-gradient text-brand-gradient" style="letter-spacing: -1.2px; font-size: 2.3rem; font-family: var(--font-serif), sans-serif;">
                {{ $store->name ?? 'Setup Tokomu' }}
            </h2>
            <div class="d-flex flex-wrap align-items-center gap-2 mt-1">
                @if($store)
                    <span
                        class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 fw-bold d-flex align-items-center gap-2 border border-success border-opacity-10" style="font-size: 0.72rem;">
                        <span class="active-glow-dot"></span> Online
                    </span>
                    <a href="{{ url('/') }}"
                       target="_blank"
                       class="badge text-secondary bg-body-tertiary border rounded-pill px-3 py-2 text-decoration-none transition-all hover-translate" style="font-size: 0.72rem;">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Buka Toko
                    </a>
                @endif
            </div>
        </div>

        {{-- Header Buttons (Mobile Friendly) --}}
        <div class="d-flex flex-column flex-sm-row gap-2 mt-2 mt-md-0">
            <button wire:click="exportLaporan"
                    class="btn btn-dashboard-header btn-outline"
                    wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="exportLaporan"><i class="bi bi-file-earmark-excel-fill text-success fs-5"></i> Export Excel</span>
                <span wire:loading wire:target="exportLaporan"><span class="spinner-border spinner-border-sm"></span> Menyusun Data...</span>
            </button>
            <a href="{{ route('cashier') }}" wire:navigate
               class="btn btn-dashboard-header btn-filled text-white-fixed">
                <i class="bi bi-cart-check-fill fs-5"></i> Buka Kasir
                @if($newOrderCount > 0)
                    <span class="badge rounded-circle ms-1" style="padding: 0.35em 0.6em; background-color: #ffffff !important; color: #dc3545 !important;">{{ $newOrderCount }}</span>
                @endif
            </a>
        </div>
    </div>

    {{-- New Order Notification Glassmorphism Island --}}
    @if($newOrderCount > 0)
        <div class="card border-0 shadow-lg p-3 mb-4 transition-all"
             style="border-radius: 1.25rem;
                background: linear-gradient(135deg, rgba(202, 138, 4, 0.1) 0%, rgba(180, 83, 9, 0.05) 100%);
                border: 1px solid rgba(180, 83, 9, 0.2) !important;
                backdrop-filter: blur(8px);
                position: relative;
                overflow: hidden;">

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
                    class="btn text-white-fixed fw-bold rounded-pill px-4 py-2.5 shadow-sm flex-shrink-0 w-100 w-md-auto border-0 transition-all mt-2"
                    style="background: linear-gradient(135deg, #ca8a04, #b45309); font-size: 0.85rem;"
                    onmouseover="this.style.opacity='0.9'; this.style.transform='translateY(-1px)';"
                    onmouseout="this.style.opacity='1'; this.style.transform='translateY(0)';"
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
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3 g-md-4 mb-4">

            {{-- Omset Hari Ini --}}
            <div class="col">
                <div
                    class="card h-100 dash-card bg-gradient-caramel text-white-fixed position-relative overflow-hidden p-2 border-0">
                    <div class="position-absolute top-0 end-0 p-3 text-white-fixed-25">
                        <i class="bi bi-wallet2" style="font-size: 6rem; margin-top: -1rem; margin-right: -1rem;"></i>
                    </div>
                    <div class="card-body p-4 position-relative z-1 d-flex flex-column justify-content-between" style="min-height: 160px;">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge rounded-pill px-3 py-2 fw-bold text-white-fixed" style="font-size: 0.72rem; background-color: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.25) !important;">Omset Hari Ini</span>
                        </div>
                        <div>
                            <h2 class="fw-black mb-1 display-6 text-white-fixed" style="font-family: var(--font-serif), sans-serif; letter-spacing: -1px;">
                                Rp {{ number_format($stats['revenue_today'], 0, ',', '.') }}</h2>
                            <p class="text-white-fixed-75 small fw-bold mb-0">Dari {{ $stats['orders_today'] }}
                                Transaksi Sukses</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Omset Bulan Ini --}}
            <div class="col">
                <div
                    class="card h-100 dash-card bg-gradient-espresso text-white-fixed position-relative overflow-hidden p-2 border-0">
                    <div class="position-absolute top-0 end-0 p-3 text-white-fixed-25">
                        <i class="bi bi-graph-up-arrow"
                           style="font-size: 6rem; margin-top: -1rem; margin-right: -1rem;"></i>
                    </div>
                    <div class="card-body p-4 position-relative z-1 d-flex flex-column justify-content-between" style="min-height: 160px;">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span
                                class="badge text-white-fixed rounded-pill px-3 py-2 fw-bold" style="font-size: 0.72rem; background-color: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.25) !important;">Omset Bulan Ini</span>
                        </div>
                        <div>
                            <h2 class="fw-black mb-1 display-6 text-white-fixed" style="font-family: var(--font-serif), sans-serif; letter-spacing: -1px;">
                                Rp {{ number_format($stats['revenue_month'], 0, ',', '.') }}</h2>
                            <p class="text-white-fixed-75 small fw-bold mb-0">Total Pendapatan Bulanan</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pesanan Pending --}}
            <div class="col">
                <div
                    class="card h-100 dash-card position-relative overflow-hidden border bg-body p-2 {{ $stats['pending_orders'] > 0 ? 'border-danger border-2 shadow-danger' : 'border-secondary border-opacity-25' }}"
                    style="{{ $stats['pending_orders'] > 0 ? 'border-color: rgba(220, 53, 69, 0.45) !important; background: linear-gradient(135deg, rgba(220, 53, 69, 0.05) 0%, rgba(220, 53, 69, 0.02) 100%) !important;' : '' }}">
                    @if($stats['pending_orders'] > 0)
                        <div class="position-absolute top-0 end-0 m-3">
                            <span class="spinner-grow spinner-grow-sm text-danger" role="status"></span>
                        </div>
                    @endif
                    <div class="card-body p-4 d-flex flex-column justify-content-between" style="min-height: 160px;">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div
                                class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 45px; height: 45px;">
                                <i class="bi bi-hourglass-split fs-5"></i>
                            </div>
                            <span
                                class="badge text-secondary border rounded-pill px-3 py-2 bg-body-tertiary" style="font-size: 0.72rem;">Antrean</span>
                        </div>
                        <div>
                            <h2 class="fw-black mb-1 display-6 {{ $stats['pending_orders'] > 0 ? 'text-danger' : 'text-body' }}" style="font-family: var(--font-serif), sans-serif; letter-spacing: -1px;">
                                {{ $stats['pending_orders'] }}</h2>
                            <p class="text-secondary small fw-bold mb-0 opacity-75">Pesanan Menunggu Diproses</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 g-md-4">
            {{-- Pesanan Terbaru --}}
            <div class="col-xl-8">
                <div class="card dash-card h-100 bg-body border"
                     style="border-color: var(--bs-border-color-translucent) !important;">
                    <div
                        class="card-header bg-transparent border-0 pt-4 pb-2 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-body" style="font-family: var(--font-serif), sans-serif;"><i class="bi bi-receipt me-2 text-warning"></i>Pesanan Terbaru</h5>
                        <a href="{{ route('order') }}" wire:navigate
                           class="btn btn-secondary border bg-body-tertiary text-secondary btn-sm rounded-pill px-3 fw-bold d-none d-sm-inline-block">Kelola
                            Pesanan</a>
                    </div>
                    <div class="card-body p-3 p-md-4 pt-0 bg-body">
                        <div class="list-group list-group-flush bg-transparent">
                            @forelse($recentOrders as $order)
                                <div
                                    class="list-group-item list-group-item-custom p-3 d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div
                                            class="rounded-circle text-white-fixed d-flex align-items-center justify-content-center fw-bolder shadow-sm bg-gradient-caramel flex-shrink-0"
                                            style="width: 45px; height: 45px; font-size: 1.1rem; font-family: var(--font-serif), sans-serif;">
                                            {{ strtoupper(substr($order->customer_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <h6 class="fw-bold mb-0 text-body">{{ $order->customer_name }}</h6>
                                            </div>
                                            <div
                                                class="text-secondary small fw-medium d-flex align-items-center flex-wrap gap-2 opacity-75">
                                                <span class="badge border text-secondary bg-body"
                                                      style="font-size: 0.65rem;">#{{ $order->invoice_code }}</span>
                                                <span class="text-uppercase" style="font-size: 0.7rem;"><i
                                                        class="bi bi-tag-fill text-warning me-1"></i>{{ $order->order_type }}</span>
                                                <span class="text-secondary d-none d-sm-inline">&bull;</span>
                                                <span>{{ $order->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="text-start text-sm-end w-100 w-sm-auto ps-5 ps-sm-0 mt-2 mt-sm-0 d-flex justify-content-between d-sm-block align-items-center border-top border-sm-0 pt-2 pt-sm-0"
                                        style="border-color: var(--bs-border-color-translucent) !important;">
                                        <div class="fw-bold mb-sm-1 text-body" style="font-size: 1.1rem; font-family: var(--font-serif), sans-serif; letter-spacing: -0.5px;">
                                            Rp {{ number_format($order->total_price, 0, ',', '.') }}</div>
                                        <div>
                                            @if($order->status == 'pending')
                                                <span class="badge-pill-glow badge-pill-warning">
                                                    <span class="rounded-circle" style="width: 6px; height: 6px; background-color: currentColor; display: inline-block;"></span> Menunggu
                                                </span>
                                            @elseif($order->status == 'paid')
                                                <span class="badge-pill-glow badge-pill-success">
                                                    <span class="rounded-circle" style="width: 6px; height: 6px; background-color: currentColor; display: inline-block;"></span> Lunas
                                                </span>
                                            @elseif($order->status == 'cancelled')
                                                <span class="badge-pill-glow badge-pill-danger">
                                                    <span class="rounded-circle" style="width: 6px; height: 6px; background-color: currentColor; display: inline-block;"></span> Batal
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5">
                                    <div
                                        class="rounded-circle d-inline-flex p-4 mb-3 text-secondary bg-body-tertiary border">
                                        <i class="bi bi-inbox fs-1"></i>
                                    </div>
                                    <h6 class="fw-bold text-body">Belum Ada Pesanan</h6>
                                    <p class="text-secondary small mb-0">Pesanan terbaru akan muncul di sini secara
                                        otomatis.</p>
                                </div>
                            @endforelse
                        </div>
                        <a href="{{ route('order') }}" wire:navigate
                           class="btn btn-outline-secondary border bg-body text-secondary w-100 rounded-pill fw-bold d-block d-sm-none mt-3">
                            Lihat Semua Pesanan
                        </a>
                    </div>
                </div>
            </div>

            {{-- Sidebar Kanan (Produk Terlaris & Aksi Cepat) --}}
            <div class="col-xl-4">
                <div class="d-flex flex-column gap-3 gap-md-4 h-100">

                    {{-- WIDGET SALDO KREDIT (BARU - PREMIUM BANK CARD LAYOUT) --}}
                    <div class="card dash-card bg-gradient-copper-card border-0 shadow-lg p-1 position-relative overflow-hidden mb-2">
                        <div class="card-body p-4 d-flex flex-column justify-content-between" style="min-height: 200px;">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex flex-column">
                                    <span class="text-white-fixed-75 small fw-bold uppercase tracking-wider" style="font-size: 0.65rem; letter-spacing: 1.5px;">SALDO PAKAIAPP</span>
                                    <span class="text-white-fixed-50 small fw-medium mt-0.5" style="font-size: 0.62rem;">Credit Card Account</span>
                                </div>
                                <div class="wallet-chip"></div>
                            </div>

                            <div class="my-3">
                                <h3 class="fw-black mb-1 text-white-fixed display-6 {{ $stats['wallet_balance'] < 3000 ? 'text-danger animate-pulse' : '' }}" style="font-family: var(--font-serif), sans-serif; letter-spacing: -0.5px;">
                                    Rp {{ number_format($stats['wallet_balance'], 0, ',', '.') }}
                                </h3>
                                @if($stats['wallet_balance'] < 3000)
                                    <span class="badge bg-danger bg-opacity-20 text-danger border border-danger border-opacity-20 rounded-pill px-2.5 py-1 fw-bold mt-1" style="font-size: 0.65rem;">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Saldo menipis, isi ulang!
                                    </span>
                                @else
                                    <span class="badge rounded-pill px-2.5 py-1 fw-bold mt-1" style="font-size: 0.65rem; background-color: rgba(255, 255, 255, 0.1); color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.1) !important;">
                                        <i class="bi bi-check-circle-fill text-success me-1"></i>
                                        Cukup untuk ~{{ floor($stats['wallet_balance'] / $stats['fee_per_trx']) }} transaksi
                                    </span>
                                @endif
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top border-light border-opacity-10">
                                <span class="text-white-fixed-50 small font-monospace" style="font-size: 0.72rem; letter-spacing: 2px;">**** **** **** {{ substr(tenant('id'), 0, 4) }}</span>
                                <button class="btn btn-sm rounded-pill fw-black px-4 py-1.5 shadow-sm hover-translate" data-bs-toggle="modal" data-bs-target="#topUpModal" style="font-size: 0.75rem; background-color: #ffffff !important; color: #170903 !important; border: none !important;" type="button">
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
                            <h6 class="fw-bold mb-0 text-body" style="font-family: var(--font-serif), sans-serif;">Menu Terlaris Bulan Ini</h6>
                        </div>
                        <div class="card-body p-3 pt-1 bg-body">
                            @if(count($topProducts) > 0)
                                <div class="d-flex flex-column gap-2">
                                    @foreach($topProducts as $index => $item)
                                        <div
                                            class="d-flex align-items-center justify-content-between p-2 rounded-3 border bg-body-tertiary"
                                            style="border-color: var(--bs-border-color-translucent) !important;">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rank-badge {{ $index == 0 ? 'rank-gold' : ($index == 1 ? 'rank-silver' : ($index == 2 ? 'rank-bronze' : 'rank-default')) }}">
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

                    {{-- Quick Actions --}}
                    <div class="card dash-card p-2 bg-body border"
                         style="border-color: var(--bs-border-color-translucent) !important;">
                        <div class="card-body p-3 bg-body">
                            <h6 class="fw-bold mb-3 text-body" style="font-family: var(--font-serif), sans-serif;">Aksi Cepat</h6>
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
    <div class="modal fade" id="topUpModal" tabindex="-1" aria-labelledby="topUpModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 1.5rem; background-color: var(--bs-card-bg);">
                <div class="modal-header border-0 pb-0 pt-4 px-4 position-relative">
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-4 pt-2">
                    {{-- Decorative Wallet Icon --}}
                    <div class="d-inline-flex align-items-center justify-content-center bg-warning bg-opacity-10 rounded-circle mb-3 p-3 text-warning shadow-sm" style="width: 70px; height: 70px; border: 1px solid rgba(182, 115, 50, 0.15);">
                        <i class="bi bi-wallet2 fs-2" style="color: var(--brand-caramel, #b45309);"></i>
                    </div>
                    
                    <h5 class="modal-title fw-bold text-body mb-2" id="topUpModalLabel" style="font-family: var(--font-serif), sans-serif; font-size: 1.25rem;">
                        Isi Ulang Saldo Pakaiapp
                    </h5>
                    
                    <p class="text-secondary small mb-4 px-2">
                        Untuk melakukan pengisian ulang saldo (Top Up) akun toko Anda, silakan hubungi Administrator kami melalui WhatsApp.
                    </p>

                    <div class="bg-body-tertiary p-3 rounded-4 border mb-4 text-start d-flex align-items-center gap-3" style="border-color: var(--bs-border-color-translucent) !important;">
                        <div class="bg-success bg-opacity-10 text-success rounded-circle p-2.5 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                            <i class="bi bi-whatsapp fs-5"></i>
                        </div>
                        <div>
                            <span class="text-secondary small d-block" style="font-size: 0.72rem;">WhatsApp Admin</span>
                            <span class="fw-black text-body" style="letter-spacing: 0.5px;">+62 851-7422-1544</span>
                        </div>
                    </div>

                    <a href="https://wa.me/6285174221544?text=Halo%20Admin%2C%20saya%20ingin%20melakukan%20top%20up%20saldo%20Pakaiapp%20untuk%20toko%20saya." 
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
    background: linear-gradient(135deg, #B67332 0%, #7E4B1E 100%) !important;
    box-shadow: 0 8px 24px rgba(182, 115, 50, 0.25) !important;
}

.bg-gradient-espresso {
    background: linear-gradient(135deg, #321E14 0%, #1A0F0A 100%) !important;
    box-shadow: 0 8px 24px rgba(50, 30, 20, 0.2) !important;
}

/* Premium Wallet Card (Copper Theme) */
.bg-gradient-copper-card {
    position: relative;
    background: linear-gradient(135deg, #5C3217 0%, #30170A 50%, #170903 100%) !important;
    color: #F9F7F5 !important;
    box-shadow: 0 12px 30px rgba(92, 50, 23, 0.25) !important;
    border: 1px solid rgba(182, 115, 50, 0.25) !important;
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
    background: radial-gradient(circle, rgba(182, 115, 50, 0.25) 0%, transparent 70%);
    z-index: -1;
    pointer-events: none;
}

.bg-gradient-copper-card .wallet-chip {
    width: 38px;
    height: 28px;
    background: linear-gradient(135deg, #E8B97D 0%, #B67332 100%);
    border-radius: 6px;
    position: relative;
    box-shadow: inset 0 1px 2px rgba(255, 255, 255, 0.4);
}

.bg-gradient-copper-card .wallet-chip::after {
    content: "";
    position: absolute;
    top: 4px;
    left: 4px;
    right: 4px;
    bottom: 4px;
    border: 1px solid rgba(0, 0, 0, 0.15);
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
    background: linear-gradient(135deg, #F5D061 0%, #C49B25 100%);
    color: #4A3A0F;
    box-shadow: 0 2px 6px rgba(196, 155, 37, 0.3);
}

.rank-silver {
    background: linear-gradient(135deg, #E2E8F0 0%, #94A3B8 100%);
    color: #1E293B;
    box-shadow: 0 2px 6px rgba(148, 163, 184, 0.3);
}

.rank-bronze {
    background: linear-gradient(135deg, #EDC2A0 0%, #B07E5D 100%);
    color: #482C17;
    box-shadow: 0 2px 6px rgba(176, 126, 93, 0.3);
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

/* Text Gradient Header */
.text-brand-gradient {
    background: linear-gradient(90deg, var(--bs-primary) 0%, var(--brand-caramel) 100%);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    display: inline-block;
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
    background: linear-gradient(135deg, var(--brand-caramel), #7E4B1E);
    color: #ffffff !important;
    border: none !important;
    box-shadow: 0 4px 15px rgba(182, 115, 50, 0.25);
}

.btn-dashboard-header.btn-filled:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(182, 115, 50, 0.35);
    opacity: 0.95;
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
