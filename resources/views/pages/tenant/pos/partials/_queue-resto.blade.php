@if($pendingOrders->isEmpty())
    {{-- Empty State - Modern Minimalist --}}
    <div class="d-flex flex-column justify-content-center align-items-center py-5 rounded-4 border bg-body shadow-sm"
         style="min-height: 40vh;">
        <div class="bg-body-secondary rounded-circle d-flex justify-content-center align-items-center mb-4 shadow-sm" style="width: 100px; height: 100px;">
            <i class="bi bi-receipt text-secondary" style="font-size: 3rem;"></i>
        </div>
        <h4 class="fw-bolder text-body mb-2" style="font-family: 'Poppins', sans-serif;">Antrian Kosong</h4>
        <p class="text-secondary text-center max-w-sm mb-0" style="font-family: 'Open Sans', sans-serif;">
            Belum ada pesanan yang tertahan. Semua pesanan sudah diselesaikan dengan baik!
        </p>
    </div>
@else
    {{-- Grid List Antrian Pending --}}
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
        @foreach($pendingOrders as $order)
            <div class="col">
                <div class="card queue-card h-100" style="border-radius: 1.25rem; overflow: hidden; font-family: 'Open Sans', sans-serif;">
                    
                    {{-- Premium Header --}}
                    <div class="p-4 d-flex justify-content-between align-items-start queue-header">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <h6 class="fw-bolder mb-0 text-body" style="font-family: 'Poppins', sans-serif; font-size: 1.1rem;">
                                    {{ $order->invoice_code }}
                                </h6>
                            </div>
                            <small class="text-body-secondary fw-semibold d-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                <i class="bi bi-clock"></i> {{ $order->created_at->diffForHumans() }}
                            </small>
                        </div>
                        <div class="d-flex flex-column align-items-end gap-2">
                            <span class="badge bg-warning text-dark rounded-pill fw-bold px-3 py-1 border border-warning shadow-sm" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                <i class="bi bi-hourglass-split me-1"></i>PENDING
                            </span>
                            @if($order->is_online)
                                <span class="badge rounded-pill fw-bold px-3 py-1 border border-primary shadow-sm bg-primary text-white" style="font-size: 0.7rem;">
                                    <i class="bi bi-phone me-1"></i>Digital Menu
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Order Body --}}
                    <div class="card-body p-0 d-flex flex-column">
                        
                        {{-- Meta Tags --}}
                        <div class="px-4 py-3 d-flex flex-wrap gap-2 queue-meta-area">
                            <span class="badge bg-body text-body border rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-1">
                                <i class="bi bi-person text-secondary"></i> {{ $order->customer_name }}
                            </span>
                            @if($order->table_number)
                                <span class="badge bg-body text-body border rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-1">
                                    <i class="bi bi-hash text-secondary"></i> Meja {{ $order->table_number }}
                                </span>
                            @endif
                            <span class="badge bg-body text-body border rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-1 text-capitalize">
                                <i class="bi bi-bag text-secondary"></i> {{ $order->order_type }}
                            </span>
                            @if($order->notes)
                                <span class="badge bg-body text-warning border-warning border-opacity-50 border rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-1" title="Catatan: {{ $order->notes }}">
                                    <i class="bi bi-card-text"></i> Catatan
                                </span>
                            @endif
                        </div>

                        {{-- Items List --}}
                        <div class="p-4 flex-grow-1" style="font-size: 0.85rem;">
                            @foreach($order->items as $item)
                                <div class="item-row d-flex justify-content-between align-items-start py-2 px-2 rounded-2 mb-1">
                                    <div class="text-body pe-2">
                                        <div class="d-flex align-items-start gap-2">
                                            <span class="fw-bold text-white rounded px-2 py-1" style="background-color: #334155; font-size: 0.75rem;">{{ $item->quantity }}x</span>
                                            <div>
                                                <span class="fw-semibold">{{ $item->product_name }}</span>
                                                @if($item->variant_name)
                                                    <span class="text-body-secondary ms-1">({{ $item->variant_name }})</span>
                                                @endif
                                                @if($item->discount > 0)
                                                    <div class="text-success fw-bold mt-1" style="font-size: 0.7rem;"><i class="bi bi-tag-fill me-1"></i>Hemat Rp {{ number_format($item->discount * $item->quantity, 0, ',', '.') }}</div>
                                                @endif
                                                @if($item->note)
                                                    <div class="text-warning fst-italic mt-1" style="font-size: 0.75rem;"><i class="bi bi-chat-dots me-1"></i>{{ $item->note }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="text-end">
                                            @if($item->discount > 0)
                                                <small class="text-danger text-decoration-line-through d-block mb-1" style="font-size: 0.7rem; opacity: 0.7;">
                                                    Rp {{ number_format($item->subtotal + ($item->discount * $item->quantity), 0, ',', '.') }}
                                                </small>
                                            @endif
                                            <span class="fw-bolder text-body" style="font-family: 'Poppins', sans-serif;">
                                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                            </span>
                                        </div>
                                        @if($order->status !== 'completed' && $order->status !== 'paid' && $item->kitchen_status === 'waiting')
                                        <button wire:click="voidItem({{ $item->id }})"
                                                wire:confirm="Yakin ingin membatalkan item ini? Stok akan dikembalikan otomatis."
                                                class="btn btn-sm btn-outline-danger border-0 rounded-circle d-flex align-items-center justify-content-center" 
                                                style="width: 28px; height: 28px;" title="Batal (Void) Item">
                                            <i class="bi bi-x-circle-fill"></i>
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                            <hr class="my-3 border-secondary opacity-10">

                            {{-- Fees & Discounts --}}
                            <div class="px-2">
                                @if(($order->service_charge_amount ?? 0) > 0)
                                    <div class="d-flex justify-content-between align-items-center py-1 text-body-secondary" style="font-size: 0.8rem;">
                                        <span>Biaya Layanan ({{ number_format($order->service_charge_percentage ?? 5, 0) }}%)</span>
                                        <span class="fw-semibold text-body">Rp {{ number_format($order->service_charge_amount, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                                @if(($order->tax_amount ?? 0) > 0)
                                    <div class="d-flex justify-content-between align-items-center py-1 text-body-secondary" style="font-size: 0.8rem;">
                                        <span>Pajak PB1 ({{ number_format($order->tax_percentage ?? 10, 0) }}%)</span>
                                        <span class="fw-semibold text-body">Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                                @if(($order->discount ?? 0) > 0)
                                    <div class="d-flex justify-content-between align-items-center py-1 text-success" style="font-size: 0.8rem;">
                                        <span class="fw-bold"><i class="bi bi-scissors me-1"></i>Diskon</span>
                                        <span class="fw-bold">- Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
 
                        {{-- Total Info Box --}}
                        <div class="mx-4 mb-4 mt-auto p-3 rounded-4 queue-total-box border d-flex justify-content-between align-items-center shadow-sm">
                            <span class="queue-total-label fw-semibold text-uppercase" style="letter-spacing: 1px; font-size: 0.75rem;">Total Pembayaran</span>
                            <h4 class="fw-bolder mb-0 queue-total-value" style="font-family: 'Poppins', sans-serif; letter-spacing: -0.5px;">
                                Rp {{ number_format($order->total_price ?? $order->subtotal, 0, ',', '.') }}
                            </h4>
                        </div>
                    </div>
 
                    {{-- Actions Footer --}}
                    <div class="p-3 bg-body border-top d-flex flex-column gap-3" style="border-color: var(--bs-border-color-translucent) !important;">
                        <div class="d-flex gap-2">
                            @if($order->items->whereIn('kitchen_status', ['processing', 'ready', 'completed'])->count() === 0)
                            <button @click="$dispatch('open-cancel-modal', { orderId: {{ $order->id }} })"
                                    class="btn-modern btn-danger-modern btn fw-bold flex-grow-1"
                                    style="border-radius: 0.75rem; height: 42px; display: flex; align-items: center; justify-content: center;" title="Batalkan Pesanan">
                                <i class="bi bi-trash3"></i>
                            </button>
                            @endif
                            <button wire:click="setEditOrder({{ $order->id }})"
                               class="btn-modern btn fw-bold flex-grow-1 border"
                               style="border-radius: 0.75rem; height: 42px; display: flex; align-items: center; justify-content: center;" title="Tambah Pesanan ke Meja Ini">
                                <i class="bi bi-plus-lg fs-5"></i>
                            </button>
                            @if($order->items->count() > 1)
                            <button @click="openSplitModal({{ json_encode([
                                        'id' => $order->id,
                                        'invoice_code' => $order->invoice_code,
                                        'items' => $order->items
                                    ]) }})"
                                    class="btn-modern btn fw-bold flex-grow-1 border"
                                    style="border-radius: 0.75rem; height: 42px; display: flex; align-items: center; justify-content: center;" title="Pisah Bill (Bayar Sebagian)">
                                <i class="bi bi-layout-split fs-5"></i>
                            </button>
                            @endif
                            @if($pendingOrders->where('id', '!=', $order->id)->where('amount_paid', 0)->count() > 0 && $order->amount_paid == 0)
                            <button @click="openMergeModal({{ json_encode(['id' => $order->id, 'invoice_code' => $order->invoice_code]) }})"
                                    class="btn-modern btn fw-bold flex-grow-1 border"
                                    style="border-radius: 0.75rem; height: 42px; display: flex; align-items: center; justify-content: center;" title="Gabung Struk / Merge Bill">
                                <i class="bi bi-intersect fs-5"></i>
                            </button>
                            @endif
                        </div>
                        <button @click="openPayForOrder({{ json_encode([
                                            'id' => $order->id,
                                            'invoice_code' => $order->invoice_code,
                                            'customer_name' => $order->customer_name,
                                            'subtotal' => $order->subtotal,
                                            'total_price' => $order->total_price ?? $order->subtotal,
                                        ]) }})"
                                class="queue-cta-btn btn text-white fw-bold w-100 d-flex align-items-center justify-content-center gap-2 shadow-sm border-0"
                                style="border-radius: 0.75rem; height: 48px;">
                            <i class="bi bi-cash-coin fs-5"></i> Bayar Sekarang
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
