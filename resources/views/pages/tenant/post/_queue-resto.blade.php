@if($pendingOrders->isEmpty())
    {{-- Kondisi Antrian Kosong - Aman Dark Mode --}}
    <div class="card border p-5 text-center bg-body-tertiary"
         style="border-radius: 1.25rem; border-color: var(--bs-border-color-translucent) !important;">
        <i class="bi bi-check-circle text-success" style="font-size: 4rem; opacity: 0.5;"></i>
        <h5 class="fw-bold mt-3">Tidak ada antrian</h5>
        <p class="text-secondary small mb-0">Semua pesanan sudah dibayar 🎉</p>
    </div>
@else
    {{-- Grid List Antrian Pending --}}
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3">
        @foreach($pendingOrders as $order)
            <div class="col">
                <div class="card border shadow-sm h-100 overflow-hidden bg-body"
                     style="border-radius: 1.25rem; border-color: var(--bs-border-color-translucent) !important;">

                    {{-- Order Header - Menggunakan warna warning semi transparan yang aman di mode gelap --}}
                    <div class="p-3 border-bottom d-flex justify-content-between align-items-center"
                         style="background: rgba(255, 193, 7, 0.1); border-color: var(--bs-border-color-translucent) !important;">
                        <div>
                            <h6 class="fw-bold mb-0 text-body">{{ $order->invoice_code }}</h6>
                            <small class="text-secondary fw-bold" style="font-size: 0.7rem;">
                                {{ $order->created_at->diffForHumans() }}
                            </small>
                        </div>
                        <span class="badge bg-warning text-dark rounded-pill fw-bold px-3 py-2">
                            <i class="bi bi-hourglass-split me-1"></i>Pending
                        </span>
                    </div>

                    {{-- Order Info / Body Card --}}
                    <div class="card-body p-3">
                        <div class="d-flex gap-2 mb-3 flex-wrap">
                            <span class="badge bg-body-tertiary text-secondary border rounded-pill fw-medium">
                                <i class="bi bi-person me-1"></i>{{ $order->customer_name }}
                            </span>
                            @if($order->table_number)
                                <span
                                    class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill fw-medium">
                                    <i class="bi bi-hash"></i>Meja {{ $order->table_number }}
                                </span>
                            @endif
                            @if($order->notes)
                                <span
                                    class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill fw-medium" title="Catatan: {{ $order->notes }}">
                                    <i class="bi bi-card-text me-1"></i>Catatan
                                </span>
                            @endif
                            <span
                                class="badge bg-body-tertiary text-secondary border rounded-pill text-capitalize fw-medium">
                                {{ $order->order_type }}
                            </span>
                        </div>

                        {{-- Items List --}}
                        <div class="mb-3">
                            @foreach($order->items as $item)
                                <div
                                    class="d-flex justify-content-between align-items-center py-1 border-bottom border-dashed"
                                    style="font-size: 0.85rem; border-color: var(--bs-border-color-translucent) !important;">
                                    <span class="text-body">
                                        <span class="fw-bold text-primary">{{ $item->quantity }}x</span>
                                        {{ $item->product_name }}
                                        @if($item->variant_name)
                                            <small class="text-muted">({{ $item->variant_name }})</small>
                                        @endif
                                        @if($item->note)
                                            <br><small class="text-warning fst-italic"><i
                                                    class="bi bi-chat-dots me-1"></i>{{ $item->note }}</small>
                                        @endif
                                    </span>
                                    <span class="fw-bold text-nowrap" style="color: var(--brand-caramel, #b45309);">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </span>
                                </div>
                            @endforeach

                            @if(($order->service_charge_amount ?? 0) > 0)
                                <div class="d-flex justify-content-between align-items-center py-1 text-muted" style="font-size: 0.8rem;">
                                    <span>Biaya Layanan ({{ number_format($order->service_charge_percentage ?? 5, 0) }}%)</span>
                                    <span>Rp {{ number_format($order->service_charge_amount, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            @if(($order->tax_amount ?? 0) > 0)
                                <div class="d-flex justify-content-between align-items-center py-1 text-muted" style="font-size: 0.8rem;">
                                    <span>Pajak PB1 ({{ number_format($order->tax_percentage ?? 10, 0) }}%)</span>
                                    <span>Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</span>
                                </div>
                            @endif
                        </div>
 
                        {{-- Total Info Box --}}
                        <div
                            class="d-flex justify-content-between align-items-center p-2 bg-body-tertiary rounded-3 border">
                            <span class="fw-bold text-muted small">TOTAL</span>
                            <h5 class="fw-bolder mb-0" style="color: var(--brand-caramel, #b45309);">
                                Rp {{ number_format($order->total_price ?? $order->subtotal, 0, ',', '.') }}
                            </h5>
                        </div>
                    </div>
 
                    {{-- Actions Footer --}}
                    <div class="p-3 border-top bg-body-tertiary d-flex gap-2"
                         style="border-color: var(--bs-border-color-translucent) !important;">
                        <button @click="$dispatch('open-cancel-modal', { orderId: {{ $order->id }} })"
                                class="btn btn-outline-danger fw-bold flex-shrink-0 bg-body"
                                style="border-radius: 0.75rem; width: 38px; height: 38px; padding: 0; display: flex; align-items: center; justify-content: center;" title="Batalkan Pesanan">
                            <i class="bi bi-x-lg"></i>
                        </button>
                        <a href="{{ route('cashier', ['add_to_order' => $order->id]) }}"
                           class="btn btn-outline-primary fw-bold flex-shrink-0 bg-body"
                           style="border-radius: 0.75rem; width: 38px; height: 38px; padding: 0; display: flex; align-items: center; justify-content: center;" title="Tambah Pesanan ke Meja Ini">
                            <i class="bi bi-plus-lg fs-5"></i>
                        </a>
                        @if($order->items->count() > 1)
                        <button @click="openSplitModal({{ json_encode([
                                    'id' => $order->id,
                                    'invoice_code' => $order->invoice_code,
                                    'items' => $order->items
                                ]) }})"
                                class="btn btn-outline-warning fw-bold flex-shrink-0 bg-body"
                                style="border-radius: 0.75rem; width: 38px; height: 38px; padding: 0; display: flex; align-items: center; justify-content: center;" title="Pisah Bill (Bayar Sebagian)">
                            <i class="bi bi-scissors fs-5 text-warning" style="filter: brightness(0.8);"></i>
                        </button>
                        @endif
                        <button @click="openPayForOrder({{ json_encode([
                                            'id' => $order->id,
                                            'invoice_code' => $order->invoice_code,
                                            'customer_name' => $order->customer_name,
                                            'subtotal' => $order->subtotal,
                                            'total_price' => $order->total_price ?? $order->subtotal,
                                        ]) }})"
                                class="btn btn-primary fw-bold flex-grow-1 d-flex align-items-center justify-content-center gap-2 text-white border-0"
                                style="border-radius: 0.75rem; background-color: #F97316;">
                            <i class="bi bi-cash-coin"></i> Bayar Sekarang
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
