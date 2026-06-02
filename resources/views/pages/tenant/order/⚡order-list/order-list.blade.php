<div x-data="{
    activeFilter: $wire.entangle('statusFilter').live,
    showGuideModal() {
        localStorage.setItem('pakaiapp_order_guide_dismissed', 'true');
        window.dispatchEvent(new CustomEvent('guide-opened'));
        const modalEl = document.getElementById('orderGuideModal');
        if (modalEl) {
            const inst = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            inst.show();
        }
    }
}" wire:poll.15s class="pb-5">

    {{-- Header Section --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 pt-2">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h2 class="fw-bolder mb-0" style="letter-spacing: -0.5px;">Dashboard Pesanan</h2>
                <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill fw-bold px-2 py-0.5" style="font-size: 0.75rem;">
                    <i class="bi bi-clock-history me-1"></i>Real-time
                </span>
            </div>
            <p class="text-secondary small mb-0 fw-medium">Pantau, proses, dan kelola semua transaksi masuk (POS & Online) secara instan.</p>
        </div>
        <div>
            <button type="button" @click="showGuideModal()" class="btn btn-outline-primary border-2 fw-bold rounded-pill px-3 py-1.5 d-flex align-items-center gap-2 shadow-sm" style="font-size: 0.875rem;">
                <i class="bi bi-question-circle"></i> Panduan Alur
            </button>
        </div>
    </div>


    {{-- Controls: Search & Filters --}}
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">

        {{-- Segmented Filters --}}
        <div class="filter-nav">
            @php
                $filters = [
                    ['id' => 'all', 'label' => 'Semua', 'count' => $allCount, 'icon' => 'bi-grid-fill', 'color' => 'primary'],
                    ['id' => 'pending', 'label' => 'Menunggu', 'count' => $pendingCount, 'icon' => 'bi-hourglass-split', 'color' => 'warning'],
                    ['id' => 'paid', 'label' => 'Baru Masuk', 'count' => $paidCount, 'icon' => 'bi-bag-plus-fill', 'color' => 'info'],
                    ['id' => 'progress', 'label' => 'Diproses', 'count' => $progressCount, 'icon' => 'bi-arrow-repeat', 'color' => 'primary', 'spin' => true],
                    ['id' => 'completed', 'label' => 'Selesai', 'count' => $completedCount, 'icon' => 'bi-check-circle-fill', 'color' => 'success'],
                    ['id' => 'cancelled', 'label' => 'Batal', 'count' => $cancelledCount, 'icon' => 'bi-x-circle-fill', 'color' => 'danger']
                ];
            @endphp
            @foreach($filters as $filter)
                <button type="button"
                        @click="activeFilter = '{{ $filter['id'] }}'"
                        :class="activeFilter === '{{ $filter['id'] }}' ? 'active active-{{ $filter['id'] }}' : ''"
                        class="filter-btn d-flex align-items-center gap-2">
                    <i class="bi {{ $filter['icon'] }} {{ ($filter['spin'] ?? false) ? 'spin-slow' : '' }} text-{{ $filter['color'] }}-emphasis"></i>
                    <span>{{ $filter['label'] }}</span>
                    <span class="badge rounded-pill bg-{{ $filter['color'] }}-subtle text-{{ $filter['color'] }}-emphasis border border-{{ $filter['color'] }}-subtle">
                        {{ $filter['count'] }}
                    </span>
                </button>
            @endforeach
        </div>

        {{-- Search Bar --}}
        <div class="position-relative" style="min-width: 280px;">
            <i class="bi bi-search position-absolute text-body-secondary"
               style="top: 50%; left: 1rem; transform: translateY(-50%);"></i>
            <input type="text"
                   class="form-control glass-input rounded-pill ps-5 py-2"
                   wire:model.live.debounce.300ms="search"
                   placeholder="Cari pesanan...">
        </div>
    </div>

    {{-- Orders Container Canvas --}}
    <div class="card border-0 shadow-sm rounded-4 bg-transparent overflow-hidden">

        {{-- 1. SKELETON LAYER (Hanya Aktif Pas Loading Cari / Filter) --}}
        <div wire:loading wire:target="statusFilter, search" class="w-100 bg-body">
            <div class="d-flex flex-column">
                @for($i = 0; $i < 4; $i++)
                    <div class="order-row p-3 p-md-4">
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                            <div class="d-flex align-items-center gap-3 w-100">
                                <!-- Avatar Skeleton -->
                                <div class="skeleton-block rounded-circle"
                                     style="width: 42px; height: 42px; flex-shrink: 0;"></div>
                                <div class="w-100 flex-grow-1">
                                    <!-- Name Skeleton -->
                                    <div class="skeleton-block mb-2" style="width: 35%; height: 18px;"></div>
                                    <!-- Invoice Info Skeleton -->
                                    <div class="skeleton-block" style="width: 20%; height: 12px;"></div>
                                </div>
                            </div>
                            <!-- Price & Badge Skeleton -->
                            <div
                                class="d-flex flex-row flex-md-column align-items-md-end justify-content-between w-100 w-md-auto gap-2"
                                style="min-width: 150px;">
                                <div class="skeleton-block d-md-block" style="width: 80px; height: 20px;"></div>
                                <div class="skeleton-block"
                                     style="width: 110px; height: 22px; border-radius: 2rem;"></div>
                            </div>
                            <!-- Button Action Skeleton (Desktop) -->
                            <div class="d-none d-md-flex gap-2 ms-md-3">
                                <div class="skeleton-block rounded-circle" style="width: 38px; height: 38px;"></div>
                                <div class="skeleton-block rounded-circle" style="width: 38px; height: 38px;"></div>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        <div wire:loading.remove wire:target="statusFilter, search" class="bg-body">
            @if($orders->isEmpty())
                <div class="text-center py-5 bg-body-tertiary">
                    <div class="bg-body rounded-circle d-inline-flex p-4 mb-3 text-body-secondary border">
                        <i class="bi bi-receipt fs-1"></i>
                    </div>
                    <h6 class="fw-bold mb-2">Data Tidak Ditemukan</h6>
                    <p class="text-body-secondary small mb-0">Belum ada transaksi masuk di filter ini.</p>
                </div>
            @else
                <div class="d-flex flex-column">
                    @foreach($orders as $order)
                        @php
                            $typeBadges = [
                                'dinein' => ['label' => 'Dine In', 'icon' => 'bi-cup-hot', 'class' => 'bg-primary-subtle text-primary-emphasis border border-primary-subtle'],
                                'takeaway' => ['label' => 'Takeaway', 'icon' => 'bi-bag', 'class' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle'],
                                'delivery' => ['label' => 'Delivery', 'icon' => 'bi-bicycle', 'class' => 'bg-info-subtle text-info-emphasis border border-info-subtle']
                            ];
                            $typeInfo = $typeBadges[$order->order_type] ?? ['label' => $order->order_type, 'icon' => 'bi-receipt', 'class' => 'bg-body-secondary text-secondary'];
                        @endphp
                        <div class="order-row p-3 p-md-4" wire:key="order-{{ $order->id }}">
                            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">

                                {{-- Left: Customer Info & Ordered Items --}}
                                <div class="d-flex align-items-start gap-3 w-100">
                                    <div class="avatar-initial flex-shrink-0 mt-1">
                                        {{ strtoupper(substr($order->customer_name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0 flex-grow-1">
                                        {{-- Customer Name & Table Number --}}
                                        <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                                            <h6 class="fw-bold mb-0 text-truncate text-body-emphasis" style="font-size: 0.95rem;">{{ $order->customer_name }}</h6>
                                            @if($order->table_number)
                                                <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle fw-bold px-2 py-0.5"
                                                      style="font-size: 0.7rem;">
                                                    Meja {{ $order->table_number }}
                                                </span>
                                            @endif
                                            @if($order->notes)
                                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle fw-bold px-2 py-0.5 text-truncate"
                                                      style="font-size: 0.7rem; max-width: 150px;" title="Catatan: {{ $order->notes }}">
                                                    <i class="bi bi-card-text me-1"></i>{{ $order->notes }}
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Clean Bulleted Metadata Line --}}
                                        <div class="d-flex flex-wrap align-items-center gap-1.5 small text-body-secondary mb-1">
                                            <span class="fw-bold text-primary" style="font-size: 0.8rem;">#{{ $order->invoice_code }}</span>
                                            <span class="text-muted opacity-50">•</span>

                                            @if($order->is_online)
                                                <span class="text-success fw-bold"><i class="bi bi-globe2 me-1"></i>Online</span>
                                            @else
                                                <span class="text-secondary fw-semibold"><i class="bi bi-pc-display me-1"></i>POS Kasir</span>
                                            @endif
                                            <span class="text-muted opacity-50">•</span>

                                            <span class="fw-semibold"><i class="{{ $typeInfo['icon'] }} me-1"></i>{{ $typeInfo['label'] }}</span>

                                            @if($order->payment_method)
                                                <span class="text-muted opacity-50">•</span>
                                                @php
                                                    $paymentIcon = match(strtolower($order->payment_method)) {
                                                        'cash' => 'bi-cash-coin',
                                                        'qris' => 'bi-qr-code-scan',
                                                        'transfer' => 'bi-bank',
                                                        default => 'bi-credit-card'
                                                    };
                                                @endphp
                                                <span class="text-uppercase"><i class="bi {{ $paymentIcon }} me-1"></i>{{ $order->payment_method }}</span>
                                            @endif

                                            <span class="text-muted opacity-50">•</span>
                                            <span><i class="bi bi-clock me-1"></i>{{ $order->created_at->format('H:i') }}</span>
                                        </div>

                                        {{-- Compact Ordered Items Line (No Heavy Containers) --}}
                                        @if($order->items->isNotEmpty())
                                            @php
                                                $summary = $order->items->map(function($item) {
                                                    return $item->product_name . ($item->variant_name ? ' (' . $item->variant_name . ')' : '') . ($item->quantity > 1 ? ' (x' . $item->quantity . ')' : '');
                                                })->join(', ');
                                            @endphp
                                            <div class="small text-secondary text-truncate mt-1" style="font-size: 0.78rem; max-width: 580px;" title="{{ $summary }}">
                                                <i class="bi bi-box-seam me-1 text-primary-emphasis" style="font-size: 0.8rem;"></i>
                                                <span class="fw-bold text-body-secondary">{{ $order->items->sum('quantity') }} Item:</span>
                                                <span>{{ $summary }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Middle: Status & Price --}}
                                <div class="d-flex flex-row flex-md-column align-items-center align-items-md-end justify-content-between w-100 w-md-auto mt-2 mt-md-0 px-1 px-md-0"
                                     style="min-width: 160px;">
                                    <div class="fw-bold text-body-emphasis mb-md-1" style="font-size: 1.1rem; letter-spacing: -0.3px;">
                                        Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                    </div>
                                    <div>
                                        @if($order->status == 'pending')
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-0.5 fw-bold" style="font-size: 0.72rem;">
                                                <i class="bi bi-hourglass-split me-1"></i>Menunggu
                                            </span>
                                        @elseif($order->status == 'paid')
                                            <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill px-2 py-0.5 fw-bold" style="font-size: 0.72rem;">
                                                <i class="bi bi-bag-plus me-1"></i>Baru Masuk
                                            </span>
                                        @elseif($order->status == 'progress')
                                            <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle rounded-pill px-2 py-0.5 fw-bold" style="font-size: 0.72rem;">
                                                <i class="bi bi-arrow-repeat spin-slow me-1"></i>Diproses
                                            </span>
                                        @elseif($order->status == 'completed')
                                            <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-2 py-0.5 fw-bold" style="font-size: 0.72rem;">
                                                <i class="bi bi-check-circle me-1"></i>Selesai
                                            </span>
                                        @elseif($order->status == 'cancelled')
                                            <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle rounded-pill px-2 py-0.5 fw-bold" style="font-size: 0.72rem;">
                                                <i class="bi bi-x-circle me-1"></i>Batal
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Mobile Action Grid --}}
                                <div class="d-md-none mobile-action-grid mt-2 w-100">
                                    <button wire:click="$dispatch('openModal', { orderId: {{ $order->id }} })"
                                            class="btn btn-outline-secondary fw-bold rounded-pill py-2 btn-sm mobile-action-full">
                                        <i class="bi bi-eye me-1"></i> Lihat Detail
                                    </button>
                                    @if($order->status == 'pending')
                                        <button @click="$dispatch('open-cancel-modal', { orderId: {{ $order->id }} })"
                                                class="btn btn-outline-danger fw-bold rounded-pill py-2 btn-sm">
                                            <i class="bi bi-x-lg me-1"></i> Batal
                                        </button>
                                        <button wire:click="$dispatch('trigger-payment-modal', { orderId: {{ $order->id }} })"
                                                class="btn btn-success fw-bold text-white rounded-pill py-2 btn-sm">
                                            <i class="bi bi-cash me-1"></i> Bayar
                                        </button>
                                    @elseif($order->status == 'paid')
                                        <button wire:click="updateStatus({{ $order->id }}, 'progress')"
                                                class="btn btn-primary fw-bold text-white rounded-pill py-2 btn-sm mobile-action-full">
                                            <i class="bi bi-play-fill me-1"></i> Proses Pesanan
                                        </button>
                                    @elseif($order->status == 'progress')
                                        <button wire:click="updateStatus({{ $order->id }}, 'completed')"
                                                class="btn btn-success fw-bold text-white rounded-pill py-2 btn-sm mobile-action-full">
                                            <i class="bi bi-check-lg me-1"></i> Selesaikan
                                        </button>
                                    @endif
                                </div>

                                {{-- Desktop Actions - Visual & Self-Explanatory Pill Buttons --}}
                                <div class="d-none d-md-flex gap-2 ms-md-3">
                                    {{-- Detail Button --}}
                                    <button wire:click="$dispatch('openModal', { orderId: {{ $order->id }} })"
                                            class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 fw-bold d-flex align-items-center gap-1 shadow-sm"
                                            title="Lihat Detail Pesanan">
                                        <i class="bi bi-eye me-1"></i> Detail
                                    </button>

                                    {{-- Contextual Process Action Buttons --}}
                                    @if($order->status == 'pending')
                                        <button wire:click="$dispatch('trigger-payment-modal', { orderId: {{ $order->id }} })"
                                                class="btn btn-sm btn-success text-white rounded-pill px-3 py-1 fw-bold d-flex align-items-center gap-1 shadow-sm"
                                                title="Bayar Pesanan">
                                            <i class="bi bi-cash me-1"></i> Bayar
                                        </button>

                                        <button @click="$dispatch('open-cancel-modal', { orderId: {{ $order->id }} })"
                                                class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1 fw-bold d-flex align-items-center gap-1 shadow-sm"
                                                title="Batalkan Pesanan">
                                            <i class="bi bi-x-lg me-1"></i> Batal
                                        </button>
                                    @elseif($order->status == 'paid')
                                        <button wire:click="updateStatus({{ $order->id }}, 'progress')"
                                                class="btn btn-sm btn-primary text-white rounded-pill px-3 py-1 fw-bold d-flex align-items-center gap-1 shadow-sm"
                                                title="Mulai Proses Pembuatan">
                                            <i class="bi bi-play-fill me-1"></i> Proses
                                        </button>
                                    @elseif($order->status == 'progress')
                                        <button wire:click="updateStatus({{ $order->id }}, 'completed')"
                                                class="btn btn-sm btn-success text-white rounded-pill px-3 py-1 fw-bold d-flex align-items-center gap-1 shadow-sm"
                                                title="Selesaikan Pesanan">
                                            <i class="bi bi-check-lg me-1"></i> Selesai
                                        </button>
                                    @endif
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Infinite Scroll Bottom Loader --}}
    @if($orders->hasMorePages())
        <div x-intersect.full="$wire.loadMore()" class="d-flex justify-content-center align-items-center py-4">
            <div class="spinner-border text-secondary spinner-border-sm me-2" role="status"></div>
            <span class="fw-bold text-muted small">Memuat data...</span>
        </div>
    @endif
    {{-- ===== PREMIUM TUTORIAL & HELP MODAL ===== --}}
    <div class="modal fade" id="orderGuideModal" tabindex="-1" aria-hidden="true" wire:ignore>
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow-lg d-flex flex-column bg-body text-body"
                 style="border-radius: 1.5rem; max-height: 90vh; border-color: var(--bs-border-color-translucent) !important;">

                {{-- Header (Premium Gradient) --}}
                <div class="modal-header border-bottom px-4 py-3 flex-shrink-0 text-white"
                     style="border-radius: 1.5rem 1.5rem 0 0; background: #F97316; border: none;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-journal-bookmark-fill fs-4"></i>
                        <h5 class="fw-bold mb-0">Panduan Mengelola Pesanan</h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {{-- Body (Premium Glassmorphism Tabs and Cards) --}}
                <div class="modal-body p-4 bg-body overflow-y-auto">
                    <div class="text-center mb-4">
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill fw-bold px-3 py-1.5 mb-2" style="font-size: 0.8rem;">
                            <i class="bi bi-clock-history me-1"></i>Alur Manajemen Transaksi
                        </span>
                        <h4 class="fw-bold text-body">Pahami Alur Kerja Pesananmu 🚀</h4>
                        <p class="text-secondary small max-w-lg mx-auto">Sistem kami terhubung secara real-time ke kasir dan toko online. Pelajari status pesanan untuk operasional bebas hambatan.</p>
                    </div>

                    <div class="row g-3">
                        <!-- Step 1: Baru Masuk -->
                        <div class="col-md-6">
                            <div class="card h-100 p-3 border shadow-sm bg-body-tertiary" style="border-radius: 1.25rem; border-color: var(--bs-border-color-translucent) !important;">
                                <div class="d-flex gap-3 align-items-start">
                                    <div class="bg-info bg-opacity-10 text-info rounded-4 d-flex align-items-center justify-content-center p-2.5 flex-shrink-0" style="width: 48px; height: 48px;">
                                        <i class="bi bi-bag-plus-fill fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1 text-body">Baru Masuk (Lunas)</h6>
                                        <p class="text-secondary small mb-0" style="font-size: 0.8rem;">Pesanan dari Toko Online atau kasir yang **sudah dibayar**. Segera klik tombol **"Proses Pesanan"** (ikon putar) untuk memberitahu dapur atau tim penyiapan.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Sedang Diproses -->
                        <div class="col-md-6">
                            <div class="card h-100 p-3 border shadow-sm bg-body-tertiary" style="border-radius: 1.25rem; border-color: var(--bs-border-color-translucent) !important;">
                                <div class="d-flex gap-3 align-items-start">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-4 d-flex align-items-center justify-content-center p-2.5 flex-shrink-0" style="width: 48px; height: 48px;">
                                        <i class="bi bi-arrow-repeat fs-4 spin-slow"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1 text-body">Sedang Diproses</h6>
                                        <p class="text-secondary small mb-0" style="font-size: 0.8rem;">Pesanan sedang disiapkan. Jika barang/makanan sudah siap diserahkan ke pelanggan/kurir, klik tombol **"Selesaikan"** (ikon centang hijau).</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Selesai -->
                        <div class="col-md-6">
                            <div class="card h-100 p-3 border shadow-sm bg-body-tertiary" style="border-radius: 1.25rem; border-color: var(--bs-border-color-translucent) !important;">
                                <div class="d-flex gap-3 align-items-start">
                                    <div class="bg-success bg-opacity-10 text-success rounded-4 d-flex align-items-center justify-content-center p-2.5 flex-shrink-0" style="width: 48px; height: 48px;">
                                        <i class="bi bi-check-circle-fill fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1 text-body">Pesanan Selesai</h6>
                                        <p class="text-secondary small mb-0" style="font-size: 0.8rem;">Pesanan selesai sepenuhnya. Transaksi ini tercatat rapi di histori keuangan dan stok barang terpotong secara aman dan akurat.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 4: Menunggu Bayar -->
                        <div class="col-md-6">
                            <div class="card h-100 p-3 border shadow-sm bg-body-tertiary" style="border-radius: 1.25rem; border-color: var(--bs-border-color-translucent) !important;">
                                <div class="d-flex gap-3 align-items-start">
                                    <div class="bg-warning bg-opacity-10 text-warning rounded-4 d-flex align-items-center justify-content-center p-2.5 flex-shrink-0" style="width: 48px; height: 48px;">
                                        <i class="bi bi-hourglass-split fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1 text-body">Menunggu Bayar (Pending)</h6>
                                        <p class="text-secondary small mb-0" style="font-size: 0.8rem;">Pesanan kasir yang disimpan sebagai antrean. Pelanggan belum melunasi pembayaran. Anda bisa memicu pembayaran cepat atau membatalkannya.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tips Section --}}
                    <div class="mt-4 p-3 rounded-4" style="background: rgba(var(--bs-primary-rgb), 0.05); border: 1px dashed rgba(var(--bs-primary-rgb), 0.3);">
                        <h6 class="fw-bold mb-2 text-primary"><i class="bi bi-lightning-charge me-2"></i>Tips Cepat Pengelolaan Dashboard</h6>
                        <ul class="text-secondary small mb-0 ps-3" style="font-size: 0.8rem; line-height: 1.5;">
                            <li><strong>Aksi Instan Tanpa Buka Modal:</strong> Anda bisa memproses atau menyelesaikan pesanan langsung menggunakan tombol aksi bulat di sisi kanan daftar pesanan.</li>
                            <li><strong>Sinkronisasi Otomatis Toko Online:</strong> Pesanan online baru dari pelanggan akan langsung berbunyi dan masuk ke daftar ini secara real-time.</li>
                            <li><strong>Penyaringan Cepat:</strong> Klik salah satu kartu statistik di bagian atas halaman untuk langsung menyaring daftar pesanan berdasarkan statusnya.</li>
                        </ul>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="modal-footer bg-body-tertiary border-top p-3 flex-shrink-0"
                     style="border-radius: 0 0 1.5rem 1.5rem; border-color: var(--bs-border-color-translucent) !important;">
                    <button type="button" class="btn btn-secondary border fw-bold rounded-pill px-4 shadow-sm bg-body text-body" data-bs-dismiss="modal">
                        Mengerti, Siap! 👍
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== PREMIUM ONBOARDING BANNER (FIRST TIME ACCESS) ===== --}}
    <div x-data="{
             showBanner: false,
             init() {
                 setTimeout(() => {
                     if (!localStorage.getItem('pakaiapp_order_guide_dismissed')) {
                         this.showBanner = true;
                     }
                 }, 1500);
             },
             dismiss() {
                 this.showBanner = false;
                 localStorage.setItem('pakaiapp_order_guide_dismissed', 'true');
             },
             openGuide() {
                 this.dismiss();
                 const modalEl = document.getElementById('orderGuideModal');
                 if (modalEl) {
                     const inst = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                     inst.show();
                 }
             }
         }"
         @guide-opened.window="showBanner = false"
         x-show="showBanner"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         class="position-fixed bottom-0 start-0 m-3 m-md-4 p-3 shadow-lg border text-body onboarding-banner-mobile"
         style="z-index: 1040; width: 320px; border-radius: 1.25rem; background: rgba(var(--bs-body-bg-rgb), 0.85); backdrop-filter: blur(12px); border-color: var(--bs-border-color-translucent) !important;">
         <style>
             @media (max-width: 767.98px) {
                 .onboarding-banner-mobile {
                     bottom: 65px !important;
                 }
             }
         </style>

         <div class="d-flex align-items-start gap-3">
             <div class="bg-warning bg-opacity-10 text-warning rounded-4 d-flex align-items-center justify-content-center flex-shrink-0"
                  style="width: 42px; height: 42px; color: #ca8a04 !important;">
                 <i class="bi bi-lightbulb fs-4"></i>
             </div>
             <div class="flex-grow-1">
                 <div class="d-flex justify-content-between align-items-start">
                     <h6 class="fw-bold mb-1 text-body small">Butuh Bantuan Alur? 👋</h6>
                     <button @click="dismiss()" class="btn-close shadow-none" style="font-size: 0.75rem;" aria-label="Close"></button>
                 </div>
                 <p class="text-secondary mb-2" style="font-size: 0.75rem; line-height: 1.35;">
                     Pelajari alur manajemen transaksi dan kelola pesanan online/offline Anda dengan lancar.
                 </p>
                 <button @click="openGuide()" class="btn btn-warning btn-sm fw-bold rounded-pill w-100 text-white"
                         style="background: #F97316; border: none; font-size: 0.75rem;">
                     Buka Panduan Alur <i class="bi bi-arrow-right ms-1"></i>
                 </button>
             </div>
         </div>
    </div>

    {{-- Event listener untuk menampilkan toast penunjuk saat modal panduan ditutup --}}
    <script>
        if (!window.hasOrderGuideCloseListener) {
            window.hasOrderGuideCloseListener = true;
            document.addEventListener('hidden.bs.modal', (event) => {
                if (event.target && event.target.id === 'orderGuideModal') {
                    if (typeof showIslandToast === 'function') {
                        showIslandToast('💡 Butuh panduan alur lagi? Klik tombol Panduan Alur di sebelah judul halaman!', 'info');
                    }
                }
            });
        }
    </script>
</div>

