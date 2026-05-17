<div wire:poll.10s class="pb-5">
    <style>
        .dash-card {
            border: none;
            border-radius: 1.25rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .dash-card:hover {
            box-shadow: 0 8px 30px rgba(0,0,0,0.06);
            transform: translateY(-3px);
        }
        .bg-gradient-primary {
            background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
        }
        .bg-gradient-success {
            background: linear-gradient(135deg, #10b981 0%, #047857 100%);
        }
        .bg-gradient-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #b45309 100%);
        }
        .bg-glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.5);
        }
        .list-group-item-custom {
            border: none;
            border-radius: 1rem !important;
            margin-bottom: 0.5rem;
            transition: all 0.2s;
        }
        .list-group-item-custom:hover {
            background-color: #f8fafc;
        }
        @keyframes pulse-ring {
            0% { transform: scale(0.8); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
            100% { transform: scale(0.8); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }
    </style>

    {{-- Welcome Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-4">
        <div>
            <h6 class="text-primary text-uppercase small fw-bold mb-1" style="letter-spacing: 1.5px;">
                Selamat Datang kembali, {{ explode(' ', $user->name)[0] }} 👋
            </h6>
            <h2 class="fw-black mb-2 text-dark" style="letter-spacing: -1px; font-size: 2rem;">
                {{ $store->name ?? 'Setup Tokomu Dulu Yuk' }}
            </h2>
            <div class="d-flex flex-wrap align-items-center gap-3">
                @if($store)
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 fw-bold d-flex align-items-center gap-1">
                        <div class="rounded-circle bg-success" style="width:6px; height:6px; animation: pulse-ring 2s infinite"></div> Aktif
                    </span>
                    <a href="https://{{ tenant('id') }}.{{ config('tenancy.central_domains')[2] ?? 'pakaiapp.online' }}"
                       target="_blank" class="text-muted text-decoration-none small fw-bold hover-text-primary transition-all d-flex align-items-center gap-1">
                        <i class="bi bi-box-arrow-up-right"></i> Kunjungi Toko
                    </a>
                @else
                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-2 fw-bold">Setup Diperlukan</span>
                @endif
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('order') }}" wire:navigate class="btn btn-dark rounded-pill px-4 py-2.5 fw-bold shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-grid-fill"></i> Kasir / POS
                @if($newOrderCount > 0)
                    <span class="badge bg-danger rounded-circle ms-1">{{ $newOrderCount }}</span>
                @endif
            </a>
        </div>
    </div>

    {{-- New Order Notification Banner --}}
    @if($newOrderCount > 0)
        <div class="alert bg-success text-white border-0 shadow-lg rounded-4 p-4 mb-5 d-flex align-items-center justify-content-between gap-3 position-relative overflow-hidden">
            <div class="position-absolute top-0 end-0 w-50 h-100" style="background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1)); transform: skewX(-20deg) translateX(20%);"></div>
            <div class="d-flex align-items-center gap-4 position-relative z-1">
                <div class="bg-white text-success rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 50px; height: 50px;">
                    <i class="bi bi-bell-fill fs-4" style="animation: swing 2s ease-in-out infinite;"></i>
                </div>
                <div>
                    <h5 class="fw-black mb-1">Wow, {{ $newOrderCount }} Pesanan Baru Masuk! 🎉</h5>
                    <p class="mb-0 text-white text-opacity-75 small fw-medium">Jangan biarkan pelanggan menunggu, segera proses pesanannya ya.</p>
                </div>
            </div>
            <div class="d-flex gap-2 position-relative z-1 flex-shrink-0">
                <button wire:click="acknowledgeOrders" class="btn btn-light text-success btn-sm rounded-pill px-4 py-2 fw-bold shadow-sm">
                    Oke, Siap!
                </button>
            </div>
        </div>
        <style>
            @keyframes swing { 20% { transform: rotate(15deg); } 40% { transform: rotate(-10deg); } 60% { transform: rotate(5deg); } 80% { transform: rotate(-5deg); } 100% { transform: rotate(0deg); } }
        </style>
        <script>
            if (!window._newOrderAlertPlayed) {
                try {
                    const ctx = new (window.AudioContext || window.webkitAudioContext)();
                    const osc = ctx.createOscillator(); const gain = ctx.createGain();
                    osc.type = 'sine'; osc.frequency.setValueAtTime(880, ctx.currentTime);
                    osc.frequency.exponentialRampToValueAtTime(1760, ctx.currentTime + 0.1);
                    gain.gain.setValueAtTime(0.2, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.5);
                    osc.connect(gain); gain.connect(ctx.destination);
                    osc.start(); osc.stop(ctx.currentTime + 0.5);
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
                <div class="bg-warning text-white rounded-circle p-3 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 60px; height: 60px;">
                    <i class="bi bi-shop fs-3"></i>
                </div>
                <div class="w-100">
                    <h5 class="fw-bold text-dark mb-1">Lengkapi Profil Tokomu!</h5>
                    <p class="mb-3 small text-muted">Pelanggan belum bisa melihat katalogmu karena informasi toko masih kosong.</p>
                    <a href="{{ route('dashboard') }}" class="btn btn-warning text-dark fw-bold rounded-pill px-4">Atur Sekarang</a>
                </div>
            </div>
        </div>
    @else
        {{-- Modern Stats Row --}}
        <div class="row row-cols-1 row-cols-md-3 g-4 mb-5">
            {{-- Revenue Card --}}
            <div class="col">
                <div class="card h-100 dash-card bg-gradient-success text-white position-relative overflow-hidden">
                    <div class="position-absolute top-0 end-0 p-3 opacity-25">
                        <i class="bi bi-graph-up-arrow" style="font-size: 6rem; margin-top: -1rem; margin-right: -1rem;"></i>
                    </div>
                    <div class="card-body p-4 p-xl-5 position-relative z-1 d-flex flex-column justify-content-between">
                        <div>
                            <span class="badge bg-white bg-opacity-25 text-white rounded-pill px-3 py-2 mb-3 fw-bold backdrop-blur">Hari Ini</span>
                            <p class="text-white text-opacity-75 small fw-bold text-uppercase tracking-wider mb-1">Pendapatan Bersih</p>
                            <h2 class="fw-black mb-0 display-6" style="letter-spacing: -1px;">Rp {{ number_format($stats['revenue_today'], 0, ',', '.') }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Orders Card --}}
            <div class="col">
                <div class="card h-100 dash-card bg-white position-relative overflow-hidden">
                    <div class="card-body p-4 p-xl-5 d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                                <i class="bi bi-bag-check-fill fs-4"></i>
                            </div>
                            <span class="badge bg-light text-muted border rounded-pill px-3 py-2">Total Pesanan</span>
                        </div>
                        <div>
                            <h2 class="fw-black text-dark mb-1 display-6">{{ $stats['orders_today'] }}</h2>
                            <p class="text-muted small fw-bold mb-0 uppercase">Transaksi Sukses</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pending Card --}}
            <div class="col">
                <div class="card h-100 dash-card bg-white position-relative overflow-hidden border {{ $stats['pending_orders'] > 0 ? 'border-warning border-2' : '' }}">
                    @if($stats['pending_orders'] > 0)
                        <div class="position-absolute top-0 end-0 p-3">
                            <span class="badge bg-danger rounded-circle p-2 shadow-sm"><span class="visually-hidden">New alerts</span></span>
                        </div>
                    @endif
                    <div class="card-body p-4 p-xl-5 d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                                <i class="bi bi-hourglass-split fs-4"></i>
                            </div>
                            <span class="badge bg-light text-muted border rounded-pill px-3 py-2">Perlu Diproses</span>
                        </div>
                        <div>
                            <h2 class="fw-black text-{{ $stats['pending_orders'] > 0 ? 'dark' : 'muted' }} mb-1 display-6">{{ $stats['pending_orders'] }}</h2>
                            <p class="text-muted small fw-bold mb-0 uppercase">Pesanan Menunggu</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            {{-- Recent Orders --}}
            <div class="col-xl-8">
                <div class="card dash-card bg-white h-100 p-2">
                    <div class="card-header bg-transparent border-0 pt-4 pb-2 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-black text-dark mb-0">Pesanan Terbaru</h5>
                        <a href="{{ route('order') }}" wire:navigate class="btn btn-light text-primary btn-sm rounded-pill px-4 fw-bold">Lihat Semua</a>
                    </div>
                    <div class="card-body p-3">
                        <div class="list-group list-group-flush">
                            @forelse($recentOrders as $order)
                                <div class="list-group-item list-group-item-custom p-3 px-4 d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle text-dark d-flex align-items-center justify-content-center fw-bolder shadow-sm bg-light" style="width: 45px; height: 45px; font-size: 1.1rem;">
                                            {{ strtoupper(substr($order->customer_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <h6 class="fw-bold mb-0 text-dark">{{ $order->customer_name }}</h6>
                                                <span class="badge bg-light text-secondary border rounded-pill" style="font-size: 0.65rem;">#{{ $order->invoice_code }}</span>
                                            </div>
                                            <div class="text-muted small fw-medium d-flex align-items-center gap-2">
                                                <span class="text-uppercase" style="font-size: 0.7rem;"><i class="bi bi-tag-fill text-light-emphasis me-1"></i>{{ $order->order_type }}</span>
                                                <span class="text-light-emphasis">&bull;</span>
                                                <span>{{ $order->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-start text-sm-end ms-sm-auto ps-5 ps-sm-0">
                                        <div class="fw-black text-dark mb-1">Rp {{ number_format($order->total_price, 0, ',', '.') }}</div>
                                        <div>
                                            @if($order->status == 'pending')
                                                <span class="badge bg-warning bg-opacity-10 text-warning-emphasis rounded-pill px-3 py-1">Menunggu</span>
                                            @elseif($order->status == 'paid')
                                                <span class="badge bg-success bg-opacity-10 text-success-emphasis rounded-pill px-3 py-1"><i class="bi bi-check-circle me-1"></i> Selesai</span>
                                            @elseif($order->status == 'cancelled')
                                                <span class="badge bg-danger bg-opacity-10 text-danger-emphasis rounded-pill px-3 py-1">Batal</span>
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
                                    <p class="text-muted small mb-0">Pesanan terbaru akan muncul di sini secara otomatis.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="col-xl-4">
                <div class="d-flex flex-column gap-4 h-100">
                    <div class="card dash-card bg-gradient-primary text-white border-0 position-relative overflow-hidden p-2" style="min-height: 200px;">
                        <div class="position-absolute top-0 end-0 w-100 h-100" style="background: radial-gradient(circle at top right, rgba(255,255,255,0.2), transparent 60%);"></div>
                        <div class="position-absolute bottom-0 end-0 p-4 opacity-25">
                            <i class="bi bi-grid-fill" style="font-size: 6rem; transform: rotate(15deg); margin-bottom: -2rem; margin-right: -1rem;"></i>
                        </div>
                        <div class="card-body p-4 position-relative z-1 d-flex flex-column justify-content-between h-100">
                            <div>
                                <span class="badge bg-white bg-opacity-25 text-white rounded-pill px-3 py-2 mb-3 fw-bold backdrop-blur">Katalog Menu</span>
                                <h2 class="fw-black mb-1 display-6">{{ $stats['active_products'] }}</h2>
                                <p class="text-white text-opacity-75 small fw-bold">Menu Aktif Ditampilkan</p>
                            </div>
                            <a href="{{ route('product') }}" wire:navigate class="btn btn-light text-primary fw-bold rounded-pill px-4 py-3 shadow-sm w-100 mt-3 hover-lift">
                                Kelola Katalog <i class="bi bi-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>

                    <div class="card dash-card bg-white flex-grow-1 p-2">
                        <div class="card-header border-0 bg-transparent pt-4 px-4">
                            <h6 class="fw-black mb-0 text-dark">Aksi Cepat</h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="d-flex flex-column gap-2">
                                <a href="{{ route('product.create') }}" wire:navigate class="btn btn-light d-flex align-items-center text-start gap-3 p-3 rounded-4 border-0 hover-lift w-100">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 flex-shrink-0 d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;">
                                        <i class="bi bi-plus-lg fs-5"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark">Tambah Produk Baru</h6>
                                        <small class="text-muted d-block" style="font-size: 0.7rem;">Perbarui menu/katalog jualanmu</small>
                                    </div>
                                </a>
                                <button type="button" @click="$dispatch('open-qr-modal')" class="btn btn-light d-flex align-items-center text-start gap-3 p-3 rounded-4 border-0 hover-lift w-100 mt-1">
                                    <div class="bg-info bg-opacity-10 text-info rounded-circle p-2 flex-shrink-0 d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;">
                                        <i class="bi bi-qr-code-scan fs-5"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark">Cetak QR Code Toko</h6>
                                        <small class="text-muted d-block" style="font-size: 0.7rem;">Bantu pelanggan pesan di tempat</small>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
