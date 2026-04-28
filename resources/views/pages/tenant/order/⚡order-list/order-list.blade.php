<div x-data="{ activeFilter: $wire.entangle('statusFilter').live }">

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end mb-4 gap-3">
        <div>
            <h2 class="fw-bolder text-dark mb-1" style="letter-spacing: -0.5px;">Daftar Pesanan</h2>
            <p class="text-secondary mb-0 fw-medium">Kelola transaksi Retail dan F&B hari ini.</p>
        </div>

        <div class="position-relative" style="min-width: 300px;">
            <i class="bi bi-search position-absolute text-muted fs-5"
               style="top: 50%; left: 1.25rem; transform: translateY(-50%);"></i>
            <input type="text"
                   class="form-control form-control-lg rounded-pill border border-light shadow-sm ps-5 text-sm fw-medium transition-all"
                   wire:model.live.debounce.300ms="search"
                   placeholder="Cari Invoice, Nama, atau Meja...">
        </div>
    </div>

    <div class="row row-cols-2 row-cols-lg-4 g-3 mb-4">
        @php
            $filters = [
                ['id' => 'all', 'count' => $allCount, 'label' => 'Semua Pesanan', 'icon' => 'bi-inbox-fill', 'color' => 'dark'],
                ['id' => 'pending', 'count' => $pendingCount, 'label' => 'Belum Lunas', 'icon' => 'bi-hourglass-split', 'color' => 'warning'],
                ['id' => 'paid', 'count' => $paidCount, 'label' => 'Lunas', 'icon' => 'bi-check-circle-fill', 'color' => 'success'],
                ['id' => 'cancelled', 'count' => $cancelledCount, 'label' => 'Dibatalkan', 'icon' => 'bi-x-octagon-fill', 'color' => 'danger']
            ];
        @endphp

        @foreach($filters as $filter)
            <div class="col">
                <div @click="activeFilter = '{{ $filter['id'] }}'"
                     :class="activeFilter === '{{ $filter['id'] }}' ? 'border-{{ $filter['color'] }} border-2 shadow' : 'border-light shadow-sm hover-shadow'"
                     class="card rounded-4 border transition-all h-100" style="cursor: pointer;">
                    <div class="card-body p-3 p-xl-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div
                                class="p-2 rounded-circle bg-{{ $filter['color'] }} bg-opacity-10 text-{{ $filter['color'] }}">
                                <i class="bi {{ $filter['icon'] }} fs-5"></i>
                            </div>
                        </div>
                        <h3 class="fw-bolder text-dark mb-0">{{ $filter['count'] }}</h3>
                        <p class="small fw-medium mb-0 mt-1 text-muted">{{ $filter['label'] }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="position-relative" wire:poll.15s>

        <div wire:loading wire:target="statusFilter, search"
             class="w-100 position-absolute top-0 start-0 z-2 bg-opacity-75 rounded-4"
             style="min-height: 400px; backdrop-filter: blur(3px);">
            <div class="d-flex flex-column gap-3 py-4 px-2 px-md-3">
                @for($i = 0; $i < 4; $i++)
                    <div class="card border-0 shadow-sm rounded-4">
                        <div
                            class="card-body p-3 p-md-4 d-flex flex-column flex-md-row align-items-start align-items-md-center gap-3 placeholder-glow">
                            <span class="placeholder col-2 rounded d-none d-md-block"></span>
                            <div class="d-flex align-items-center gap-3 flex-grow-1 w-100">
                                <span class="placeholder rounded-circle" style="width: 48px; height: 48px;"></span>
                                <div class="d-flex flex-column gap-2 w-75">
                                    <span class="placeholder col-8 rounded"></span>
                                    <span class="placeholder col-4 rounded bg-secondary"></span>
                                </div>
                            </div>
                            <span class="placeholder col-2 rounded"></span>
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        <div wire:loading.remove wire:target="statusFilter, search" class="w-100">

            @if($orders->isEmpty())
                <div
                    class="card border border-light border-2 border-dashed shadow-none rounded-4 text-center py-5 my-3">
                    <div class="card-body py-5">
                        <div
                            class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                            style="width: 80px; height: 80px;">
                            <i class="bi bi-search fs-2 text-muted"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Data Tidak Ditemukan</h5>
                        <p class="text-secondary mb-0">Belum ada pesanan yang sesuai dengan pencarian atau filter saat
                            ini.</p>
                    </div>
                </div>
            @else
                <div class="d-flex flex-column gap-3">
                    @foreach($orders as $order)
                        <div class="card border border-light shadow-sm rounded-4 transition-all hover-shadow"
                             wire:key="order-{{ $order->id }}">
                            <div
                                class="card-body p-3 p-md-4 d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 gap-md-4">

                                <div
                                    class="w-100 w-md-auto d-flex d-md-block justify-content-between align-items-center"
                                    style="min-width: 130px;">
                                    <div class="fw-bolder text-primary fs-5">#{{ $order->invoice_code }}</div>
                                    <div class="small text-muted fw-medium mt-1"><i
                                            class="bi bi-clock me-1"></i>{{ $order->created_at->format('H:i') }}</div>
                                </div>

                                <div class="d-flex align-items-center gap-3 flex-grow-1 w-100">
                                    <div
                                        class="rounded-circle text-dark d-flex align-items-center justify-content-center fw-bolder shadow-sm border border-white"
                                        style="width: 48px; height: 48px; font-size: 1.2rem; flex-shrink: 0;">
                                        {{ strtoupper(substr($order->customer_name, 0, 1)) }}
                                    </div>
                                    <div class="overflow-hidden">
                                        <h6 class="fw-bold text-dark mb-1 text-truncate">
                                            {{ $order->customer_name }}
                                            @if($order->table_number)
                                                <span class="badge bg-dark ms-2">Meja {{ $order->table_number }}</span>
                                            @endif
                                        </h6>
                                        <div class="d-flex align-items-center gap-2 small">
                                            <span class="text-secondary fw-medium text-capitalize"><i
                                                    class="bi bi-bag-check me-1"></i>{{ $order->order_type }}</span>
                                            <span class="text-light">•</span>
                                            <span
                                                class="text-secondary fw-medium text-uppercase">{{ $order->payment_method }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="w-100 w-md-auto pt-3 pt-md-0 mt-2 mt-md-0" style="min-width: 140px;">
                                    @if($order->status == 'pending')
                                        <span
                                            class="badge bg-warning bg-opacity-10 text-warning-emphasis rounded-pill px-3 py-2 fw-bold w-100 text-center text-md-start">Belum Lunas</span>
                                    @elseif($order->status == 'paid')
                                        <span
                                            class="badge bg-success bg-opacity-10 text-success-emphasis rounded-pill px-3 py-2 fw-bold w-100 text-center text-md-start"><i
                                                class="bi bi-check-circle me-1"></i> Lunas</span>
                                    @elseif($order->status == 'cancelled')
                                        <span
                                            class="badge bg-danger bg-opacity-10 text-danger-emphasis rounded-pill px-3 py-2 fw-bold w-100 text-center text-md-start">Dibatalkan</span>
                                    @endif
                                </div>

                                <div
                                    class="w-100 w-md-auto text-md-end d-flex d-md-block justify-content-between align-items-center"
                                    style="min-width: 130px;">
                                    <div class="fw-bolder text-dark fs-5">
                                        Rp {{ number_format($order->total_price, 0, ',', '.') }}</div>
                                </div>

                                <div class="w-100 w-md-auto d-flex justify-content-end gap-2 mt-1 mt-md-0">
                                    <button wire:click="$dispatch('openModal', { orderId: {{ $order->id }} })"
                                            class="btn btn-primary rounded-3 shadow-sm border fw-bold px-3 py-2 flex-grow-1 flex-md-grow-0 transition-all">
                                        <i class="bi bi-receipt"></i> Detail
                                    </button>

                                    @if($order->status == 'pending')
                                        <button wire:click="updateStatus({{ $order->id }}, 'paid')"
                                                wire:loading.attr="disabled"
                                                class="btn btn-dark rounded-3 shadow-sm fw-bold px-3 py-2 flex-grow-1 flex-md-grow-0 transition-all">
                                            Bayar
                                        </button>
                                    @endif
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>

                @if($orders->hasMorePages())
                    <div x-intersect.full="$wire.loadMore()"
                         class="d-flex justify-content-center align-items-center py-5 mt-2">
                        <div class="spinner-border text-dark spinner-border-sm me-2" role="status"></div>
                        <span class="fw-bold text-muted small">Memuat lebih banyak pesanan...</span>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
