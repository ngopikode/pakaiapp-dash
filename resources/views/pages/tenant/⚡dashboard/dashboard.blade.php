<div wire:poll.15s class="pb-5 bg-light min-vh-100">

    {{-- Welcome Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-4 mb-md-5 gap-3 pt-3">
        <div>
            <h6 class="text-muted small fw-bold mb-1 text-uppercase" style="letter-spacing: 1px;">
                Selamat Datang, {{ explode(' ', $user->name)[0] }} 👋
            </h6>
            <h2 class="fw-bolder mb-2 text-espresso" style="letter-spacing: -1px; font-size: 2.2rem;">
                {{ $store->name ?? 'Setup Tokomu' }}
            </h2>
            <div class="d-flex flex-wrap align-items-center gap-2">
                @if($store)
                    <span
                        class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 fw-bold d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-success"
                             style="width:8px; height:8px; animation: pulse-ring-caramel 2s infinite"></div> Online
                    </span>
                    <a href="https://{{ tenant('id') }}.{{ config('tenancy.central_domains')[2] ?? 'pakaiapp.online' }}"
                       target="_blank"
                       class="badge bg-light text-secondary border rounded-pill px-3 py-2 text-decoration-none hover-text-primary transition-all">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Buka Toko
                    </a>
                @endif
            </div>
        </div>

        {{-- Header Buttons (Mobile Friendly) --}}
        <div class="d-flex flex-column flex-sm-row gap-2 mt-2 mt-md-0">
            <button wire:click="exportLaporan"
                    class="btn btn-white border shadow-sm rounded-pill px-4 py-2.5 fw-bold dash-header-btn text-success d-flex align-items-center gap-2"
                    wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="exportLaporan"><i class="bi bi-file-earmark-excel-fill fs-5"></i> Export Excel</span>
                <span wire:loading wire:target="exportLaporan"><span class="spinner-border spinner-border-sm"></span> Menyusun Data...</span>
            </button>
            <a href="{{ route('cashier') }}" wire:navigate
               class="btn bg-gradient-caramel text-white rounded-pill px-4 py-2.5 fw-bold shadow-sm dash-header-btn d-flex align-items-center gap-2 border-0">
                <i class="bi bi-cart-check-fill fs-5"></i> Buka Kasir
                @if($newOrderCount > 0)
                    <span class="badge bg-white text-danger rounded-circle ms-1">{{ $newOrderCount }}</span>
                @endif
            </a>
        </div>
    </div>

    {{-- New Order Notification Banner --}}
    @if($newOrderCount > 0)
        <div
            class="alert border-0 shadow-sm rounded-4 p-4 mb-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3"
            style="background-color: #fef3c7; color: #92400e; border-left: 5px solid #d97706 !important;">
            <div class="d-flex align-items-center gap-3">
                <div
                    class="bg-white text-warning rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm"
                    style="width: 45px; height: 45px;">
                    <i class="bi bi-bell-fill fs-5" style="animation: swing 2s ease-in-out infinite;"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1 text-espresso">Wow, {{ $newOrderCount }} Pesanan Masuk! 🎉</h5>
                    <p class="mb-0 small fw-medium">Ada pelanggan yang nungguin pesanannya nih, segera proses ya!</p>
                </div>
            </div>
            <button wire:click="acknowledgeOrders"
                    class="btn btn-warning text-dark fw-bold rounded-pill px-4 py-2 shadow-sm flex-shrink-0 w-100 w-md-auto">
                Oke, Siap!
            </button>
        </div>
        <style>@keyframes swing {
                   20% {
                       transform: rotate(15deg);
                   }
                   40% {
                       transform: rotate(-10deg);
                   }
                   60% {
                       transform: rotate(5deg);
                   }
                   80% {
                       transform: rotate(-5deg);
                   }
                   100% {
                       transform: rotate(0deg);
                   }
               }</style>
        <script>
            if (!window._newOrderAlertPlayed) {
                try {
                    const ctx = new (window.AudioContext || window.webkitAudioContext)();
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(880, ctx.currentTime);
                    osc.frequency.exponentialRampToValueAtTime(1760, ctx.currentTime + 0.1);
                    gain.gain.setValueAtTime(0.2, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.5);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start();
                    osc.stop(ctx.currentTime + 0.5);
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
                    class="bg-warning text-white rounded-circle p-3 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm"
                    style="width: 60px; height: 60px;"><i class="bi bi-shop fs-3"></i></div>
                <div class="w-100">
                    <h5 class="fw-bold text-dark mb-1">Lengkapi Profil Tokomu!</h5>
                    <p class="mb-3 small text-muted">Pelanggan belum bisa melihat katalogmu karena informasi toko masih
                        kosong.</p>
                    <a href="{{ route('dashboard') }}" class="btn btn-warning text-dark fw-bold rounded-pill px-4">Atur
                        Sekarang</a>
                </div>
            </div>
        </div>
    @else
        {{-- Modern Stats Row (Omset Harian & Bulanan) --}}
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3 g-md-4 mb-4">

            {{-- Omset Hari Ini --}}
            <div class="col">
                <div class="card h-100 dash-card bg-gradient-caramel text-white position-relative overflow-hidden p-2">
                    <div class="position-absolute top-0 end-0 p-3 opacity-25">
                        <i class="bi bi-wallet2" style="font-size: 6rem; margin-top: -1rem; margin-right: -1rem;"></i>
                    </div>
                    <div class="card-body p-4 position-relative z-1 d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                        <span
                            class="badge bg-white text-dark rounded-pill px-3 py-2 fw-bold shadow-sm">Omset Hari Ini</span>
                        </div>
                        <div>
                            <h2 class="fw-black mb-1 display-6">Rp {{ number_format($stats['revenue_today'], 0, ',', '.')
                            }}</h2>
                            <p class="text-white text-opacity-75 small fw-bold mb-0">Dari {{ $stats['orders_today'] }}
                                Transaksi Sukses</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Omset Bulan Ini --}}
            <div class="col">
                <div class="card h-100 dash-card bg-gradient-espresso text-white position-relative overflow-hidden p-2">
                    <div class="position-absolute top-0 end-0 p-3 opacity-25">
                        <i class="bi bi-graph-up-arrow"
                           style="font-size: 6rem; margin-top: -1rem; margin-right: -1rem;"></i>
                    </div>
                    <div class="card-body p-4 position-relative z-1 d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                        <span
                            class="badge bg-white bg-opacity-25 text-white border border-light border-opacity-25 rounded-pill px-3 py-2 fw-bold">Omset Bulan Ini</span>
                        </div>
                        <div>
                            <h2 class="fw-black mb-1 display-6">Rp {{ number_format($stats['revenue_month'], 0, ',', '.')
                            }}</h2>
                            <p class="text-white text-opacity-75 small fw-bold mb-0">Total Pendapatan Bulanan</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pesanan Pending --}}
            <div class="col">
                <div
                    class="card h-100 dash-card bg-white position-relative overflow-hidden border {{ $stats['pending_orders'] > 0 ? 'border-danger border-2' : '' }} p-2">
                    @if($stats['pending_orders'] > 0)
                        <div class="position-absolute top-0 end-0 m-3">
                            <span class="spinner-grow spinner-grow-sm text-danger" role="status"></span>
                        </div>
                    @endif
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div
                                class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 45px; height: 45px;">
                                <i class="bi bi-hourglass-split fs-5"></i>
                            </div>
                            <span class="badge bg-light text-muted border rounded-pill px-3 py-2">Antrean</span>
                        </div>
                        <div>
                            <h2 class="fw-black text-{{ $stats['pending_orders'] > 0 ? 'danger' : 'muted' }} mb-1 display-6">
                                {{ $stats['pending_orders'] }}</h2>
                            <p class="text-muted small fw-bold mb-0">Pesanan Menunggu Diproses</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 g-md-4">
            {{-- Pesanan Terbaru --}}
            <div class="col-xl-8">
                <div class="card dash-card bg-white h-100">
                    <div
                        class="card-header bg-transparent border-0 pt-4 pb-2 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold text-espresso mb-0"><i class="bi bi-receipt me-2 text-warning"></i>Pesanan
                            Terbaru</h5>
                        <a href="{{ route('order') }}" wire:navigate
                           class="btn btn-light border text-primary btn-sm rounded-pill px-3 fw-bold shadow-sm d-none d-sm-inline-block">Kelola
                            Pesanan</a>
                    </div>
                    <div class="card-body p-3 p-md-4 pt-0">
                        <div class="list-group list-group-flush">
                            @forelse($recentOrders as $order)
                                <div
                                    class="list-group-item list-group-item-custom p-3 d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div
                                            class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bolder shadow-sm bg-gradient-caramel flex-shrink-0"
                                            style="width: 45px; height: 45px; font-size: 1.1rem;">
                                            {{ strtoupper(substr($order->customer_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <h6 class="fw-bold mb-0 text-dark">{{ $order->customer_name }}</h6>
                                            </div>
                                            <div
                                                class="text-muted small fw-medium d-flex align-items-center flex-wrap gap-2">
                                                <span class="badge bg-light border text-secondary"
                                                      style="font-size: 0.65rem;">#{{ $order->invoice_code }}</span>
                                                <span class="text-uppercase" style="font-size: 0.7rem;"><i
                                                        class="bi bi-tag-fill text-warning me-1"></i>{{ $order->order_type }}</span>
                                                <span class="text-light-emphasis d-none d-sm-inline">&bull;</span>
                                                <span>{{ $order->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="text-start text-sm-end w-100 w-sm-auto ps-5 ps-sm-0 mt-2 mt-sm-0 d-flex justify-content-between d-sm-block align-items-center border-top border-sm-0 pt-2 pt-sm-0">
                                        <div class="fw-bold text-dark mb-sm-1" style="font-size: 1.1rem;">Rp {{
                                    number_format($order->total_price, 0, ',', '.') }}
                                        </div>
                                        <div>
                                            @if($order->status == 'pending')
                                                <span
                                                    class="badge bg-warning bg-opacity-10 text-dark border border-warning border-opacity-25 rounded-pill px-3 py-1">Menunggu</span>
                                            @elseif($order->status == 'paid')
                                                <span
                                                    class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1"><i
                                                        class="bi bi-check-circle me-1"></i> Lunas</span>
                                            @elseif($order->status == 'cancelled')
                                                <span
                                                    class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-1">Batal</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5">
                                    <div class="bg-light rounded-circle d-inline-flex p-4 mb-3 text-muted">
                                        <i class="bi bi-inbox fs-1"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark">Belum Ada Pesanan</h6>
                                    <p class="text-muted small mb-0">Pesanan terbaru akan muncul di sini secara
                                        otomatis.</p>
                                </div>
                            @endforelse
                        </div>
                        <a href="{{ route('order') }}" wire:navigate
                           class="btn btn-light border text-primary w-100 rounded-pill fw-bold shadow-sm d-block d-sm-none mt-3">Lihat
                            Semua Pesanan</a>
                    </div>
                </div>
            </div>

            {{-- Sidebar Kanan (Produk Terlaris & Aksi Cepat) --}}
            <div class="col-xl-4">
                <div class="d-flex flex-column gap-3 gap-md-4 h-100">

                    {{-- Widget Produk Terlaris --}}
                    <div class="card dash-card bg-white flex-grow-1 p-2">
                        <div class="card-header border-0 bg-transparent pt-3 px-3 d-flex align-items-center gap-2">
                            <i class="bi bi-star-fill text-warning fs-5"></i>
                            <h6 class="fw-bold mb-0 text-espresso">Menu Terlaris Bulan Ini</h6>
                        </div>
                        <div class="card-body p-3 pt-1">
                            @if(count($topProducts) > 0)
                                <div class="d-flex flex-column gap-2">
                                    @foreach($topProducts as $index => $item)
                                        <div
                                            class="d-flex align-items-center justify-content-between p-2 rounded-3 bg-light border border-white">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="fw-bold text-muted small" style="width: 20px;">
                                                    #{{ $index + 1 }}</div>
                                                <div class="fw-bold text-dark small text-truncate"
                                                     style="max-width: 150px;">{{
                                        $item->product_name }}
                                                </div>
                                            </div>
                                            <span class="badge bg-white text-dark border shadow-sm rounded-pill"><i
                                                    class="bi bi-graph-up-arrow text-success me-1"></i> {{ $item->total_sold }} Terjual</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4 bg-light rounded-4">
                                    <small class="text-muted fw-bold">Belum ada data penjualan bulan ini.</small>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Quick Actions --}}
                    <div class="card dash-card bg-white p-2">
                        <div class="card-body p-3">
                            <h6 class="fw-bold mb-3 text-espresso">Aksi Cepat</h6>
                            <div class="d-flex flex-column gap-2">
                                <a href="{{ route('product.create') }}" wire:navigate
                                   class="btn bg-light border d-flex align-items-center text-start gap-3 p-3 rounded-4 shadow-sm">
                                    <div
                                        class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 flex-shrink-0 d-flex justify-content-center align-items-center"
                                        style="width: 40px; height: 40px;">
                                        <i class="bi bi-plus-lg fs-5"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark small">Tambah Produk Baru</h6>
                                        <small class="text-muted d-block" style="font-size: 0.65rem;">Perbarui katalog
                                            jualanmu</small>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    @endif
</div>
