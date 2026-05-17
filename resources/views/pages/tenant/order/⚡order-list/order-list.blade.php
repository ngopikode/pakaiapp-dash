<div x-data="{ activeFilter: $wire.entangle('statusFilter').live }" class="pb-5">

    {{-- Header Section --}}
    <div class="mb-4 pt-2">
        <h2 class="fw-bolder mb-1" style="letter-spacing: -0.5px;">Pesanan</h2>
        <p class="text-secondary small mb-0 fw-medium">Pantau dan kelola semua transaksi masuk secara real-time.</p>
    </div>

    {{-- Controls: Search & Filters --}}
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">

        {{-- Segmented Filters --}}
        <div class="filter-nav">
            @php
                $filters = [
                    ['id' => 'all', 'label' => 'Semua', 'count' => $allCount],
                    ['id' => 'pending', 'label' => 'Menunggu', 'count' => $pendingCount],
                    ['id' => 'paid', 'label' => 'Selesai', 'count' => $paidCount],
                    ['id' => 'cancelled', 'label' => 'Batal', 'count' => $cancelledCount]
                ];
            @endphp
            @foreach($filters as $filter)
                <button type="button"
                        @click="activeFilter = '{{ $filter['id'] }}'"
                        :class="activeFilter === '{{ $filter['id'] }}' ? 'active' : ''"
                        class="filter-btn d-flex align-items-center gap-2">
                    {{ $filter['label'] }}
                    <span class="badge rounded-pill"
                          :class="activeFilter === '{{ $filter['id'] }}' ? 'bg-primary text-white' : 'bg-secondary bg-opacity-10 text-secondary'">
                        {{ $filter['count'] }}
                    </span>
                </button>
            @endforeach
        </div>

        {{-- Search Bar --}}
        <div class="position-relative" style="min-width: 280px;">
            <i class="bi bi-search position-absolute text-muted"
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

        {{-- 2. LIVE DATA LAYER (Sembunyi otomatis pas loading) --}}
        <div wire:loading.remove wire:target="statusFilter, search" class="bg-body">
            @if($orders->isEmpty())
                <div class="text-center py-5 bg-body-tertiary">
                    <div class="bg-body rounded-circle d-inline-flex p-4 mb-3 text-muted border">
                        <i class="bi bi-receipt fs-1"></i>
                    </div>
                    <h6 class="fw-bold mb-2">Data Tidak Ditemukan</h6>
                    <p class="text-muted small mb-0">Belum ada transaksi masuk di filter ini.</p>
                </div>
            @else
                <div class="d-flex flex-column">
                    @foreach($orders as $order)
                        <div class="order-row p-3 p-md-4" wire:key="order-{{ $order->id }}">
                            <div
                                class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">

                                {{-- Left: Customer Info --}}
                                <div class="d-flex align-items-center gap-3 w-100">
                                    <div class="avatar-initial flex-shrink-0">
                                        {{ strtoupper(substr($order->customer_name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0 flex-grow-1">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <h6 class="fw-bold mb-0 text-truncate">{{ $order->customer_name }}</h6>
                                            @if($order->table_number)
                                                <span class="badge bg-body-tertiary text-secondary border fw-bold"
                                                      style="font-size: 0.75rem;">Meja {{ $order->table_number }}</span>
                                            @endif
                                        </div>
                                        <div class="d-flex flex-wrap align-items-center gap-2 small text-muted">
                                            <span class="fw-bold text-secondary">#{{ $order->invoice_code }}</span>
                                            <span>&bull;</span>
                                            <span class="text-capitalize fw-medium">{{ $order->order_type }}</span>
                                            <span>&bull;</span>
                                            <span>{{ $order->created_at->format('H:i') }}</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Middle: Status & Price --}}
                                <div
                                    class="d-flex flex-row flex-md-column align-items-center align-items-md-end justify-content-between w-100 w-md-auto mt-2 mt-md-0 px-1 px-md-0"
                                    style="min-width: 150px;">
                                    <div class="fw-bolder mb-md-2" style="font-size: 1.1rem;">
                                        Rp {{ number_format($order->total_price, 0, ',', '.') }}</div>
                                    <div>
                                        @if($order->status == 'pending')
                                            <span class="badge badge-soft-warning rounded-pill px-2 py-1">Menunggu Pembayaran</span>
                                        @elseif($order->status == 'paid')
                                            <span class="badge badge-soft-success rounded-pill px-2 py-1"><i
                                                    class="bi bi-check2"></i> Selesai</span>
                                        @elseif($order->status == 'cancelled')
                                            <span
                                                class="badge badge-soft-danger rounded-pill px-2 py-1">Dibatalkan</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Mobile Action Grid --}}
                                <div class="d-md-none mobile-action-grid mt-2">
                                    <button wire:click="$dispatch('openModal', { orderId: {{ $order->id }} })"
                                            class="btn btn-outline-secondary fw-bold rounded-3 py-2 btn-sm mobile-action-full">
                                        Lihat Detail
                                    </button>
                                    @if($order->status == 'pending')
                                        <button @click="$dispatch('open-cancel-modal', { orderId: {{ $order->id }} })"
                                                class="btn btn-outline-danger fw-bold rounded-3 py-2 btn-sm">Batal
                                        </button>
                                        <button
                                            wire:click="$dispatch('trigger-payment-modal', { orderId: {{ $order->id }} })"
                                            class="btn btn-primary fw-bold rounded-3 py-2 btn-sm">Bayar
                                        </button>
                                    @endif
                                </div>

                                {{-- Desktop Actions - Bersih, Instan, & Bebas Bug Potong Kontainer --}}
                                <div class="d-none d-md-flex gap-2 ms-md-3">
                                    {{-- Tombol Lihat Detail --}}
                                    <button wire:click="$dispatch('openModal', { orderId: {{ $order->id }} })"
                                            class="btn btn-outline-secondary rounded-circle"
                                            title="Detail Pesanan"
                                            style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-eye"></i>
                                    </button>

                                    {{-- Tombol Aksi Langsung Muncul Tanpa Sembunyi Di Dropdown --}}
                                    @if($order->status == 'pending')
                                        {{-- Tombol Bayar Cepat --}}
                                        <button
                                            wire:click="$dispatch('trigger-payment-modal', { orderId: {{ $order->id }} })"
                                            class="btn btn-outline-success rounded-circle"
                                            title="Proses Pembayaran"
                                            style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; background: rgba(25, 135, 84, 0.05);">
                                            <i class="bi bi-cash"></i>
                                        </button>

                                        {{-- Tombol Batalkan Cepat --}}
                                        <button @click="$dispatch('open-cancel-modal', { orderId: {{ $order->id }} })"
                                                class="btn btn-outline-danger rounded-circle"
                                                title="Batalkan Pesanan"
                                                style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; background: rgba(220, 53, 69, 0.05);">
                                            <i class="bi bi-x-lg"></i>
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
</div>
