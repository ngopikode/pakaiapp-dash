<div class="container-fluid pb-5" x-data="{
    activeFilter: $wire.entangle('statusFilter').live,
    showGuideModal() {
        localStorage.setItem('pakaiapp_order_guide_dismissed', 'true');
        window.dispatchEvent(new CustomEvent('guide-opened'));
        const modalEl = document.getElementById('orderGuideModal');
        if (modalEl) {
            const inst = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            inst.show();
        }
    },
    splittingOrder: null,
    splitItems: [],
    get splitTotalItems() {
        return this.splitItems.reduce((acc, curr) => acc + curr.qtyToSplit, 0);
    },
    openSplitModal(order) {
        this.splittingOrder = order;
        this.splitItems = order.items.map(i => ({
            id: i.id,
            name: i.product_name,
            variant_name: i.variant_name,
            price: parseFloat(i.price),
            maxQty: parseInt(i.quantity),
            qtyToSplit: 0
        }));

        const modalEl = document.getElementById('splitBillModal');
        if (modalEl) {
            const inst = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            inst.show();
        }
    },
    submitSplitOrder() {
        if (this.splitTotalItems === 0) {
            window.dispatchEvent(new CustomEvent('notify', { detail: { type: 'warning', message: 'Pilih minimal 1 item untuk dipisah.' } }));
            return;
        }
        const totalOriginalItems = this.splitItems.reduce((acc, curr) => acc + curr.maxQty, 0);
        if (this.splitTotalItems === totalOriginalItems) {
            window.dispatchEvent(new CustomEvent('notify', { detail: { type: 'warning', message: 'Anda memilih semua item. Gunakan Bayar biasa saja.' } }));
            return;
        }

        const dataToSend = this.splitItems.filter(i => i.qtyToSplit > 0).map(i => ({
            id: i.id,
            qty: i.qtyToSplit
        }));

        @this.splitOrder(this.splittingOrder.id, dataToSend);
    }
}" wire:poll.15s>

    @php
        $storeType = \App\Models\StoreSetting::first()?->store_type ?? 'retail';
    @endphp

    {{-- Header Section (Clean & Minimalist like Product List) --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-4 gap-3 pt-3">
        <div>
            <h2 class="page-store-name mb-1" style="letter-spacing: -0.5px;">
                {{ $storeType === 'resto' ? 'Riwayat Transaksi' : 'Dashboard Pesanan' }}
            </h2>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 fw-bold border border-success border-opacity-10" style="font-size: 0.72rem;">
                    <i class="bi bi-broadcast me-1"></i> Live Update Aktif
                </span>
                <p class="text-secondary small mb-0 fw-medium">Pantau dan kelola semua transaksi masuk secara instan.</p>
            </div>
        </div>
        <div>
            <button type="button" @click="showGuideModal()" class="btn btn-outline-secondary bg-body-tertiary border text-secondary fw-bold rounded-pill px-4 py-2 d-flex align-items-center gap-2 shadow-sm transition-all" style="font-size: 0.875rem;">
                <i class="bi bi-question-circle"></i> Panduan Alur
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-12" style="min-width: 0;">
            <div class="card dash-card bg-body border h-100" style="border-color: var(--bs-border-color-translucent) !important;">

                {{-- Controls: Filters & Search --}}
                <div class="card-header bg-transparent border-0 pt-4 pb-2 px-4 d-flex flex-column flex-lg-row justify-content-between gap-3">
                    @php
                        $filters = [
                            ['id' => 'all', 'label' => 'Semua', 'count' => $allCount],
                            ['id' => 'pending', 'label' => 'Menunggu', 'count' => $pendingCount],
                            ['id' => 'paid', 'label' => 'Baru Masuk', 'count' => $paidCount],
                            ['id' => 'progress', 'label' => 'Diproses', 'count' => $progressCount],
                            ['id' => 'completed', 'label' => 'Selesai', 'count' => $completedCount],
                            ['id' => 'cancelled', 'label' => 'Batal', 'count' => $cancelledCount]
                        ];
                    @endphp

                    {{-- Mobile Filter Dropdown --}}
                    <div class="dropdown w-100 d-lg-none">
                        <button class="btn btn-outline-secondary border w-100 d-flex justify-content-between align-items-center rounded-pill px-4 py-2.5 fw-bold bg-body shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="border-color: var(--bs-border-color-translucent) !important;">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-filter fs-4 text-body"></i>
                                <span class="text-body" x-text="
                                    activeFilter === 'all' ? 'Filter: Semua Pesanan' :
                                    (activeFilter === 'pending' ? 'Filter: Menunggu' :
                                    (activeFilter === 'paid' ? 'Filter: Baru Masuk' :
                                    (activeFilter === 'progress' ? 'Filter: Diproses' :
                                    (activeFilter === 'completed' ? 'Filter: Selesai' : 'Filter: Batal'))))
                                ">Filter</span>
                            </div>
                            <i class="bi bi-chevron-down text-secondary small"></i>
                        </button>
                        <ul class="dropdown-menu w-100 border shadow-sm mt-2 p-2 rounded-4" style="background-color: var(--bs-body-bg); border-color: var(--bs-border-color-translucent) !important;">
                            @foreach($filters as $filter)
                                <li>
                                    <button type="button"
                                            class="dropdown-item rounded-3 py-2.5 px-3 fw-medium d-flex justify-content-between align-items-center mb-1 transition-all"
                                            @click="activeFilter = '{{ $filter['id'] }}'"
                                            :class="activeFilter === '{{ $filter['id'] }}' ? 'bg-primary bg-opacity-10 text-primary fw-bold' : 'text-body'">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-check2 text-primary fs-5" x-show="activeFilter === '{{ $filter['id'] }}'" x-cloak></i>
                                            <i class="bi bi-circle text-secondary opacity-25" x-show="activeFilter !== '{{ $filter['id'] }}'" style="font-size: 0.6rem; margin-left: 0.3rem;" x-cloak></i>
                                            <span :class="activeFilter === '{{ $filter['id'] }}' ? 'ms-1' : 'ms-2'">{{ $filter['label'] }}</span>
                                        </div>
                                        <span class="badge rounded-pill"
                                              :class="activeFilter === '{{ $filter['id'] }}' ? 'bg-primary text-white' : 'bg-secondary bg-opacity-25 text-secondary'"
                                              style="font-size: 0.75rem;">
                                            {{ $filter['count'] }}
                                        </span>
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Desktop Nav Pills Filter --}}
                    <div class="filter-scroll-wrapper flex-grow-1 d-none d-lg-block" style="overflow-x: auto; scrollbar-width: none; min-width: 0;">
                        <div class="d-flex gap-2 flex-nowrap pb-1">
                            @foreach($filters as $filter)
                                <button type="button"
                                        @click="activeFilter = '{{ $filter['id'] }}'"
                                        class="btn rounded-pill px-4 py-2 fw-bold d-inline-flex align-items-center gap-2 transition-all flex-shrink-0 border"
                                        :class="activeFilter === '{{ $filter['id'] }}' ? 'btn-caramel-solid' : 'bg-body-tertiary text-secondary'"
                                        style="font-size: 0.85rem; border-color: var(--bs-border-color-translucent) !important;">
                                    {{ $filter['label'] }}
                                    <span class="badge rounded-pill"
                                          :class="activeFilter === '{{ $filter['id'] }}' ? 'bg-white text-dark' : 'bg-secondary bg-opacity-25 text-secondary'"
                                          style="font-size: 0.7rem;">
                                        {{ $filter['count'] }}
                                    </span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Search Bar --}}
                    <div class="position-relative flex-shrink-0" style="min-width: 250px;">
                        <i class="bi bi-search position-absolute text-secondary"
                           style="top: 50%; left: 1rem; transform: translateY(-50%);"></i>
                        <input type="text"
                               class="form-control rounded-pill ps-5 py-2 bg-body-tertiary border"
                               style="border-color: var(--bs-border-color-translucent) !important;"
                               wire:model.live.debounce.300ms="search"
                               placeholder="Cari pesanan...">
                    </div>
                </div>

                <div class="card-body p-3 p-md-4 pt-0 bg-body">
                    <div class="list-group list-group-flush bg-transparent position-relative">

                        {{-- Loading Overlay --}}
                        <div wire:loading wire:target="statusFilter, search" class="position-absolute w-100 h-100 start-0 top-0 z-1" style="background: rgba(var(--bs-body-bg-rgb), 0.7); backdrop-filter: blur(4px);">
                            <div class="d-flex justify-content-center pt-5">
                                <div class="spinner-border text-warning" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>

                        @if($orders->isEmpty())
                            <div class="text-center py-5">
                                <div class="rounded-circle d-inline-flex p-4 mb-3 text-secondary bg-body-tertiary border" style="border-color: var(--bs-border-color-translucent) !important;">
                                    <i class="bi bi-receipt fs-1"></i>
                                </div>
                                <h6 class="fw-bold mb-1 text-body">Data Tidak Ditemukan</h6>
                                <p class="text-secondary small mb-0">Belum ada transaksi masuk di filter ini.</p>
                            </div>
                        @else
                            @foreach($orders as $order)
                                @php
                                    $typeBadges = [
                                        'dinein' => ['label' => 'Dine In', 'icon' => 'bi-cup-hot', 'class' => 'bg-primary bg-opacity-10 text-primary border-primary'],
                                        'takeaway' => ['label' => 'Takeaway', 'icon' => 'bi-bag', 'class' => 'bg-warning bg-opacity-10 text-warning border-warning'],
                                        'delivery' => ['label' => 'Delivery', 'icon' => 'bi-bicycle', 'class' => 'bg-info bg-opacity-10 text-info border-info']
                                    ];
                                    $typeInfo = $typeBadges[$order->order_type] ?? ['label' => $order->order_type, 'icon' => 'bi-receipt', 'class' => 'bg-secondary bg-opacity-10 text-secondary border-secondary'];
                                @endphp

                                <div class="list-group-item list-group-item-custom p-3">
                                    <div class="row align-items-md-center g-3">
                                        {{-- Left Side: Avatar & Info --}}
                                        <div class="col-12 col-md d-flex align-items-start gap-3">
                                            <div class="rounded-circle text-white-fixed d-flex align-items-center justify-content-center fw-bolder shadow-sm bg-gradient-caramel flex-shrink-0 mt-1"
                                                 style="width: 48px; height: 48px; font-size: 1.25rem; font-family: var(--font-serif), sans-serif;">
                                                {{ strtoupper(substr($order->customer_name, 0, 1)) }}
                                            </div>
                                            <div class="flex-grow-1" style="min-width: 0;">
                                                <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                                                    <h6 class="fw-bold mb-0 text-body text-truncate" style="max-width: 200px;">{{ $order->customer_name }}</h6>
                                                    @if($order->table_number)
                                                        <span class="badge border bg-body-tertiary text-secondary rounded-pill px-2 py-0.5 fw-bold" style="font-size: 0.7rem;">
                                                            Meja {{ $order->table_number }}
                                                        </span>
                                                    @endif
                                                    @if($order->notes)
                                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-2 py-0.5 fw-bold text-truncate"
                                                              style="font-size: 0.7rem; max-width: 150px;" title="Catatan: {{ $order->notes }}">
                                                            <i class="bi bi-card-text me-1"></i>{{ $order->notes }}
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="text-secondary small fw-medium d-flex align-items-center flex-wrap gap-2 opacity-75 mb-2">
                                                    <span class="fw-bold text-primary">#{{ $order->invoice_code }}</span>
                                                    <span class="d-none d-sm-inline">&bull;</span>

                                                    <span class="badge border {{ $typeInfo['class'] }} border-opacity-25 rounded-pill px-2 py-0.5">
                                                        <i class="bi {{ $typeInfo['icon'] }} me-1"></i>{{ $typeInfo['label'] }}
                                                    </span>
                                                    <span class="d-none d-sm-inline">&bull;</span>

                                                    @if($order->is_online)
                                                        <span class="text-success fw-bold"><i class="bi bi-globe2 me-1"></i>Online</span>
                                                    @else
                                                        <span><i class="bi bi-pc-display me-1"></i>POS Kasir</span>
                                                    @endif
                                                    <span class="d-none d-sm-inline">&bull;</span>

                                                    <span>{{ $order->created_at->format('d M, H:i') }}</span>
                                                </div>

                                                @if($order->items->isNotEmpty())
                                                    @php
                                                        $summary = $order->items->map(function($item) {
                                                            return $item->product_name . ($item->variant_name ? ' (' . $item->variant_name . ')' : '') . ($item->quantity > 1 ? ' (x' . $item->quantity . ')' : '');
                                                        })->join(', ');
                                                    @endphp
                                                    <div class="bg-body-tertiary rounded-3 p-2 text-secondary text-truncate border" style="font-size: 0.75rem; border-color: var(--bs-border-color-translucent) !important; max-width: 100%;">
                                                        <span class="fw-bold text-body">{{ $order->items->sum('quantity') }} Item:</span>
                                                        {{ $summary }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Right Side: Price & Actions --}}
                                        <div class="col-12 col-md-auto border-top border-md-0 pt-3 pt-md-0">
                                            <div class="d-flex flex-row flex-md-column align-items-center align-items-md-end justify-content-between h-100 gap-3">
                                                <div class="text-start text-md-end">
                                                    <div class="fw-bold text-body" style="font-size: 1.15rem; font-family: var(--font-serif), sans-serif; letter-spacing: -0.5px;">
                                                        Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                                    </div>
                                                    <div class="mt-1">
                                                        @if($order->status == 'pending')
                                                            <span class="badge-pill-glow badge-pill-warning">
                                                                <span class="rounded-circle" style="width: 6px; height: 6px; background-color: currentColor; display: inline-block;"></span> Menunggu
                                                            </span>
                                                        @elseif($order->status == 'paid')
                                                            <span class="badge-pill-glow badge-pill-info" style="background-color: rgba(13, 202, 240, 0.1) !important; border-color: rgba(13, 202, 240, 0.2) !important; color: #0dcaf0 !important;">
                                                                <span class="rounded-circle" style="width: 6px; height: 6px; background-color: currentColor; display: inline-block;"></span> Baru Masuk
                                                            </span>
                                                        @elseif($order->status == 'progress')
                                                            <span class="badge-pill-glow badge-pill-primary" style="background-color: rgba(13, 110, 253, 0.1) !important; border-color: rgba(13, 110, 253, 0.2) !important; color: #0d6efd !important;">
                                                                <i class="bi bi-arrow-repeat spin-slow me-1"></i> Diproses
                                                            </span>
                                                        @elseif($order->status == 'completed')
                                                            <span class="badge-pill-glow badge-pill-success">
                                                                <span class="rounded-circle" style="width: 6px; height: 6px; background-color: currentColor; display: inline-block;"></span> Selesai
                                                            </span>
                                                        @elseif($order->status == 'cancelled')
                                                            <span class="badge-pill-glow badge-pill-danger">
                                                                <span class="rounded-circle" style="width: 6px; height: 6px; background-color: currentColor; display: inline-block;"></span> Batal
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                {{-- Actions --}}
                                                <div class="d-flex flex-wrap gap-2 justify-content-end">
                                                    <button wire:click="$dispatch('openModal', { orderId: {{ $order->id }} })"
                                                            class="btn btn-sm btn-outline-secondary border bg-body text-secondary fw-bold rounded-pill px-3 shadow-sm hover-lift"
                                                            title="Lihat Detail Pesanan">
                                                        Detail
                                                    </button>

                                                    @if($order->status == 'pending')
                                                        @if($storeType === 'resto')
                                                            <a href="{{ route('cashier', ['add_to_order' => $order->id]) }}"
                                                               class="btn btn-sm btn-outline-primary fw-bold rounded-pill px-3 shadow-sm hover-lift"
                                                               title="Tambah Pesanan ke Meja Ini">
                                                                <i class="bi bi-plus-circle me-1"></i> Tambah
                                                            </a>
                                                            @if($order->items->count() > 1)
                                                                <button @click="openSplitModal({{ json_encode([
                                                                    'id' => $order->id,
                                                                    'invoice_code' => $order->invoice_code,
                                                                    'items' => $order->items
                                                                ]) }})"
                                                                        class="btn btn-sm btn-outline-warning text-dark fw-bold rounded-pill px-3 shadow-sm hover-lift"
                                                                        title="Pisah Bill (Bayar Sebagian)">
                                                                    <i class="bi bi-scissors me-1"></i> Pisah
                                                                </button>
                                                            @endif
                                                        @endif
                                                        <button wire:click="$dispatch('trigger-payment-modal', { orderId: {{ $order->id }} })"
                                                                class="btn btn-sm btn-success text-white fw-bold rounded-pill px-3 shadow-sm hover-lift">
                                                            Bayar
                                                        </button>
                                                        <button @click="$dispatch('open-cancel-modal', { orderId: {{ $order->id }} })"
                                                                class="btn btn-sm btn-outline-danger fw-bold rounded-pill px-3 shadow-sm hover-lift">
                                                            Batal
                                                        </button>
                                                    @elseif($order->status == 'paid')
                                                        <button wire:click="updateStatus({{ $order->id }}, 'progress')"
                                                                class="btn btn-sm btn-primary text-white fw-bold rounded-pill px-3 shadow-sm hover-lift">
                                                            Proses
                                                        </button>
                                                    @elseif($order->status == 'progress')
                                                        <button wire:click="updateStatus({{ $order->id }}, 'completed')"
                                                                class="btn btn-sm btn-success text-white fw-bold rounded-pill px-3 shadow-sm hover-lift">
                                                            Selesai
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                {{-- Infinite Scroll Bottom Loader --}}
                @if($orders->hasMorePages())
                    <div x-intersect.full="$wire.loadMore()" class="d-flex justify-content-center align-items-center py-4 border-top" style="border-color: var(--bs-border-color-translucent) !important;">
                        <div class="spinner-border text-secondary spinner-border-sm me-2" role="status"></div>
                        <span class="fw-bold text-secondary small">Memuat data selanjutnya...</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ===== SPLIT BILL MODAL ===== --}}
    @include('pages.tenant.order.⚡order-list._modal-split-bill')

    {{-- ===== PREMIUM TUTORIAL & HELP MODAL ===== --}}
    <div class="modal fade" id="orderGuideModal" tabindex="-1" aria-hidden="true" wire:ignore>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg"
                 style="border-radius: 1.5rem; background-color: var(--bs-card-bg); overflow: hidden;">
                 
                 {{-- Top Hero Graphic Area --}}
                 <div class="position-relative bg-body-tertiary" style="height: 160px; overflow: hidden;">
                     <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(135deg, rgba(249, 115, 22, 0.1), rgba(249, 115, 22, 0.02));"></div>
                     <!-- Decorative circles -->
                     <div class="position-absolute rounded-circle bg-warning bg-opacity-25" style="width: 200px; height: 200px; top: -50px; right: -50px;"></div>
                     <div class="position-absolute rounded-circle bg-primary bg-opacity-10" style="width: 100px; height: 100px; bottom: -20px; left: 10%;"></div>
                     
                     <div class="position-absolute top-50 start-50 translate-middle text-center w-100">
                         <div class="d-inline-flex align-items-center justify-content-center bg-body border rounded-circle shadow-sm mb-2" style="width: 64px; height: 64px; border-color: var(--bs-border-color-translucent) !important;">
                             <i class="bi bi-card-checklist text-warning fs-2"></i>
                         </div>
                         <h5 class="fw-bold mb-0 text-body" style="font-family: var(--font-serif), sans-serif;">Alur Transaksi</h5>
                     </div>
                     <button type="button" class="btn-close position-absolute top-0 end-0 m-3 shadow-sm bg-body rounded-circle p-2 opacity-75 hover-opacity-100 transition-all" data-bs-dismiss="modal" aria-label="Close"></button>
                 </div>

                 {{-- Body --}}
                 <div class="modal-body p-4 p-md-5 pt-4 bg-body">
                    <p class="text-secondary text-center small mb-4 pb-2 border-bottom" style="border-color: var(--bs-border-color-translucent) !important;">
                        Pahami alur status pesanan agar operasional toko Anda berjalan lancar dan pelanggan puas.
                    </p>

                    <div class="position-relative">
                        <!-- Connecting Line -->
                        <div class="position-absolute bg-body-tertiary rounded-pill" style="width: 4px; top: 20px; bottom: 20px; left: 23px;"></div>

                        <!-- Step 1 -->
                        <div class="d-flex gap-3 position-relative mb-4 pb-2">
                            <div class="rounded-circle bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center flex-shrink-0 z-1 border" style="width: 50px; height: 50px; border-width: 4px !important; border-color: var(--bs-body-bg) !important; box-shadow: 0 0 0 1px rgba(var(--bs-info-rgb), 0.2);">
                                <i class="bi bi-bag-plus-fill fs-5"></i>
                            </div>
                            <div class="pt-1">
                                <h6 class="fw-bold mb-1 text-body">1. Baru Masuk (Lunas)</h6>
                                <p class="text-secondary small mb-0 lh-sm" style="font-size: 0.8rem;">Pesanan baru saja dibayar. Segera klik <strong>"Proses"</strong> untuk mulai menyiapkan barang/makanan.</p>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="d-flex gap-3 position-relative mb-4 pb-2">
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center flex-shrink-0 z-1 border" style="width: 50px; height: 50px; border-width: 4px !important; border-color: var(--bs-body-bg) !important; box-shadow: 0 0 0 1px rgba(var(--bs-primary-rgb), 0.2);">
                                <i class="bi bi-arrow-repeat fs-5 spin-slow"></i>
                            </div>
                            <div class="pt-1">
                                <h6 class="fw-bold mb-1 text-body">2. Sedang Diproses</h6>
                                <p class="text-secondary small mb-0 lh-sm" style="font-size: 0.8rem;">Pesanan sedang disiapkan. Jika barang sudah siap diserahkan, klik <strong>"Selesai"</strong>.</p>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="d-flex gap-3 position-relative">
                            <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center flex-shrink-0 z-1 border" style="width: 50px; height: 50px; border-width: 4px !important; border-color: var(--bs-body-bg) !important; box-shadow: 0 0 0 1px rgba(var(--bs-success-rgb), 0.2);">
                                <i class="bi bi-check2-circle fs-5"></i>
                            </div>
                            <div class="pt-1">
                                <h6 class="fw-bold mb-1 text-body">3. Selesai</h6>
                                <p class="text-secondary small mb-0 lh-sm" style="font-size: 0.8rem;">Pesanan telah diserahkan. Transaksi sukses tercatat di sistem! 🎉</p>
                            </div>
                        </div>
                    </div>
                 </div>

                 {{-- Footer --}}
                 <div class="modal-footer bg-body border-0 p-4 pt-0 justify-content-center">
                     <button type="button" class="btn btn-warning text-white fw-bold rounded-pill px-5 py-2 shadow-sm w-100 hover-translate" data-bs-dismiss="modal">
                         Oke, Paham!
                     </button>
                 </div>
            </div>
        </div>
    </div>
</div>

@assets
<style>
    /* Styling consistent with Dashboard UI */
    .dash-card {
        border-radius: 1.5rem;
        box-shadow: 0 8px 30px rgba(50, 30, 20, 0.02);
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .list-group-item-custom {
        border: 1px solid var(--bs-border-color-translucent) !important;
        border-radius: 1.25rem !important;
        margin-bottom: 0.6rem;
        background-color: var(--bs-secondary-bg) !important;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .list-group-item-custom:hover {
        border-color: rgba(var(--bs-primary-rgb), 0.15) !important;
        background-color: var(--bs-tertiary-bg) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
    }

    .bg-gradient-caramel {
        background-color: #F97316 !important;
        background-image: none !important;
        box-shadow: 0 4px 12px rgba(249, 115, 22, 0.15) !important;
    }

    .btn-caramel-solid {
        background-color: #F97316 !important;
        color: #ffffff !important;
        border: none !important;
        box-shadow: 0 4px 14px rgba(249, 115, 22, 0.35);
    }
    .btn-caramel-solid:hover {
        background-color: #EA6D0E !important;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(249, 115, 22, 0.45);
    }

    .hover-lift {
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .hover-lift:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
    }

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

    .spin-slow {
        animation: spin-slow-key 3s linear infinite;
        display: inline-block;
    }
    @keyframes spin-slow-key {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Fix horizontal scrollbar */
    .filter-scroll-wrapper::-webkit-scrollbar {
        display: none;
    }
</style>
@endassets
