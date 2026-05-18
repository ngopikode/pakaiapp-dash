<div wire:poll.15s class="pb-5 min-vh-100">

    {{-- Welcome Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-4 mb-md-5 gap-3 pt-3">
        <div>
            <h6 class="text-secondary small fw-bold mb-1 text-uppercase" style="letter-spacing: 1px;">
                Selamat Datang, {{ explode(' ', $user->name)[0] }} 👋
            </h6>
            <h2 class="fw-bolder mb-2 text-body" style="letter-spacing: -1px; font-size: 2.2rem;">
                {{ $store->name ?? 'Setup Tokomu' }}
            </h2>
            <div class="d-flex flex-wrap align-items-center gap-2">
                @if($store)
                    <span
                        class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 fw-bold d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-success"
                             style="width:8px; height:8px; animation: pulse-ring-caramel 2s infinite"></div> Online
                    </span>
                    <a href="{{ url('/') }}"
                       target="_blank"
                       class="badge text-secondary bg-body-tertiary border rounded-pill px-3 py-2 text-decoration-none transition-all">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Buka Toko
                    </a>
                @endif
            </div>
        </div>

        {{-- Header Buttons (Mobile Friendly) --}}
        <div class="d-flex flex-column flex-sm-row gap-2 mt-2 mt-md-0">
            <button wire:click="exportLaporan"
                    class="btn btn-secondary border bg-body text-success shadow-sm rounded-pill px-4 py-2.5 fw-bold dash-header-btn d-flex align-items-center gap-2"
                    wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="exportLaporan"><i class="bi bi-file-earmark-excel-fill fs-5"></i> Export Excel</span>
                <span wire:loading wire:target="exportLaporan"><span class="spinner-border spinner-border-sm"></span> Menyusun Data...</span>
            </button>
            <a href="{{ route('cashier') }}" wire:navigate
               class="btn bg-gradient-caramel text-white rounded-pill px-4 py-2.5 fw-bold shadow-sm dash-header-btn d-flex align-items-center gap-2 border-0">
                <i class="bi bi-cart-check-fill fs-5"></i> Buka Kasir
                @if($newOrderCount > 0)
                    <span class="badge text-danger rounded-circle ms-1 bg-white">{{ $newOrderCount }}</span>
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
                    class="btn text-white fw-bold rounded-pill px-4 py-2.5 shadow-sm flex-shrink-0 w-100 w-md-auto border-0 transition-all mt-2"
                    style="background: linear-gradient(135deg, #ca8a04, #b45309); font-size: 0.85rem;"
                    onmouseover="this.style.opacity='0.9'; this.style.transform='translateY(-1px)';"
                    onmouseout="this.style.opacity='1'; this.style.transform='translateY(0)';">
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
                    class="card h-100 dash-card bg-gradient-caramel text-white position-relative overflow-hidden p-2 border-0">
                    <div class="position-absolute top-0 end-0 p-3 opacity-25">
                        <i class="bi bi-wallet2" style="font-size: 6rem; margin-top: -1rem; margin-right: -1rem;"></i>
                    </div>
                    <div class="card-body p-4 position-relative z-1 d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge rounded-pill px-3 py-2 fw-bold bg-white text-dark bg-opacity-25">Omset Hari Ini</span>
                        </div>
                        <div>
                            <h2 class="fw-black mb-1 display-6">
                                Rp {{ number_format($stats['revenue_today'], 0, ',', '.') }}</h2>
                            <p class="text-white text-opacity-75 small fw-bold mb-0">Dari {{ $stats['orders_today'] }}
                                Transaksi Sukses</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Omset Bulan Ini --}}
            <div class="col">
                <div
                    class="card h-100 dash-card bg-gradient-espresso text-white position-relative overflow-hidden p-2 border-0">
                    <div class="position-absolute top-0 end-0 p-3 opacity-25">
                        <i class="bi bi-graph-up-arrow"
                           style="font-size: 6rem; margin-top: -1rem; margin-right: -1rem;"></i>
                    </div>
                    <div class="card-body p-4 position-relative z-1 d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span
                                class="badge bg-opacity-25 text-white border border-light border-opacity-25 rounded-pill px-3 py-2 fw-bold">Omset Bulan Ini</span>
                        </div>
                        <div>
                            <h2 class="fw-black mb-1 display-6">
                                Rp {{ number_format($stats['revenue_month'], 0, ',', '.') }}</h2>
                            <p class="text-white text-opacity-75 small fw-bold mb-0">Total Pendapatan Bulanan</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pesanan Pending --}}
            <div class="col">
                <div
                    class="card h-100 dash-card position-relative overflow-hidden border bg-body p-2 {{ $stats['pending_orders'] > 0 ? 'border-danger border-2' : 'border-secondary border-opacity-25' }}">
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
                            <span
                                class="badge text-secondary border rounded-pill px-3 py-2 bg-body-tertiary">Antrean</span>
                        </div>
                        <div>
                            <h2 class="fw-black mb-1 display-6 {{ $stats['pending_orders'] > 0 ? 'text-danger' : 'text-secondary' }}">
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
                        <h5 class="fw-bold mb-0 text-body"><i class="bi bi-receipt me-2 text-warning"></i>Pesanan
                            Terbaru</h5>
                        <a href="{{ route('order') }}" wire:navigate
                           class="btn btn-secondary border bg-body-tertiary text-secondary btn-sm rounded-pill px-3 fw-bold d-none d-sm-inline-block">Kelola
                            Pesanan</a>
                    </div>
                    <div class="card-body p-3 p-md-4 pt-0 bg-body">
                        <div class="list-group list-group-flush bg-transparent">
                            @forelse($recentOrders as $order)
                                <div
                                    class="list-group-item list-group-item-custom p-3 d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 bg-body-tertiary">
                                    <div class="d-flex align-items-center gap-3">
                                        <div
                                            class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bolder shadow-sm bg-gradient-caramel flex-shrink-0"
                                            style="width: 45px; height: 45px; font-size: 1.1rem;">
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
                                        <div class="fw-bold mb-sm-1 text-body" style="font-size: 1.1rem;">
                                            Rp {{ number_format($order->total_price, 0, ',', '.') }}</div>
                                        <div>
                                            @if($order->status == 'pending')
                                                <span
                                                    class="badge bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded-pill px-3 py-1 text-warning fw-bold">Menunggu</span>
                                            @elseif($order->status == 'paid')
                                                <span
                                                    class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1 fw-bold"><i
                                                        class="bi bi-check-circle me-1"></i> Lunas</span>
                                            @elseif($order->status == 'cancelled')
                                                <span
                                                    class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-1 fw-bold">Batal</span>
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
            {{-- Sidebar Kanan (Produk Terlaris & Aksi Cepat) --}}
            <div class="col-xl-4">
                <div class="d-flex flex-column gap-3 gap-md-4 h-100">

                    {{-- WIDGET SALDO KREDIT (BARU) --}}
                    <div class="card dash-card p-1 bg-body border shadow-sm"
                         style="border-color: var(--bs-border-color-translucent) !important;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div
                                        class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 d-flex align-items-center justify-content-center"
                                        style="width: 38px; height: 38px;">
                                        <i class="bi bi-wallet2 fs-5"></i>
                                    </div>
                                    <h6 class="fw-bold mb-0 text-body">Saldo Pakaiapp</h6>
                                </div>
                                {{-- Nanti bisa diarahkan ke halaman Top Up --}}
                                <button class="btn btn-sm btn-primary rounded-pill fw-bold px-3">Top Up</button>
                            </div>

                            <div class="bg-body-tertiary rounded-3 p-3 border"
                                 style="border-color: var(--bs-border-color-translucent) !important;">
                                <h3 class="fw-black mb-1 {{ $stats['wallet_balance'] < 3000 ? 'text-danger animate-pulse' : 'text-body' }}">
                                    Rp {{ number_format($stats['wallet_balance'], 0, ',', '.') }}
                                </h3>

                                @if($stats['wallet_balance'] < 3000)
                                    <small class="text-danger fw-bold">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Saldo menipis, isi ulang
                                        sekarang!
                                    </small>
                                @else
                                    <small class="text-secondary fw-bold">
                                        <i class="bi bi-check-circle-fill text-success me-1"></i>
                                        Cukup untuk ~{{ floor($stats['wallet_balance'] / $stats['fee_per_trx']) }}
                                        transaksi
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>
                    {{-- END WIDGET SALDO KREDIT --}}


                    {{-- Widget Produk Terlaris --}}
                    <div class="card dash-card flex-grow-1 p-2 bg-body border"
                         style="border-color: var(--bs-border-color-translucent) !important;">
                        <div class="card-header border-0 bg-transparent pt-3 px-3 d-flex align-items-center gap-2">
                            <i class="bi bi-star-fill text-warning fs-5"></i>
                            <h6 class="fw-bold mb-0 text-body">Menu Terlaris Bulan Ini</h6>
                        </div>
                        <div class="card-body p-3 pt-1 bg-body">
                            @if(count($topProducts) > 0)
                                <div class="d-flex flex-column gap-2">
                                    @foreach($topProducts as $index => $item)
                                        <div
                                            class="d-flex align-items-center justify-content-between p-2 rounded-3 border bg-body-tertiary"
                                            style="border-color: var(--bs-border-color-translucent) !important;">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="fw-bold text-secondary small" style="width: 20px;">
                                                    #{{ $index + 1 }}</div>
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
                            <h6 class="fw-bold mb-3 text-body">Aksi Cepat</h6>
                            <div class="d-flex flex-column gap-2">
                                <a href="{{ route('product.create') }}" wire:navigate
                                   class="btn border d-flex align-items-center text-start gap-3 p-3 rounded-4 shadow-sm bg-body-tertiary text-body"
                                   style="border-color: var(--bs-border-color-translucent) !important;">
                                    <div
                                        class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 flex-shrink-0 d-flex justify-content-center align-items-center"
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
</div>
