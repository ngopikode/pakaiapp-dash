@if($pendingOrders->isEmpty())
    <div class="card border-0 shadow-sm p-5 text-center" style="border-radius: 1.25rem;">
        <i class="bi bi-check-circle text-success" style="font-size: 4rem; opacity: 0.3;"></i>
        <h5 class="fw-bold font-serif text-muted mt-3">Tidak ada antrian</h5>
        <p class="text-muted small">Semua pesanan sudah dibayar 🎉</p>
    </div>
@else
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3">
        @foreach($pendingOrders as $order)
            <div class="col">
                <div class="card border-0 shadow-sm h-100 overflow-hidden" style="border-radius: 1.25rem;">
                    {{-- Order Header --}}
                    <div
                        class="p-3 bg-warning bg-opacity-10 border-bottom d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">{{ $order->invoice_code }}</h6>
                            <small class="text-muted fw-bold" style="font-size: 0.7rem;">
                                {{ $order->created_at->diffForHumans() }}
                            </small>
                        </div>
                        <span class="badge bg-warning text-dark rounded-pill fw-bold px-3 py-2">
                                    <i class="bi bi-hourglass-split me-1"></i>Pending
                                </span>
                    </div>

                    {{-- Order Info --}}
                    <div class="card-body p-3">
                        <div class="d-flex gap-2 mb-3 flex-wrap">
                                    <span class="badge bg-body-tertiary text-dark border rounded-pill">
                                        <i class="bi bi-person me-1"></i>{{ $order->customer_name }}
                                    </span>
                            @if($order->table_number)
                                <span
                                    class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill">
                                            <i class="bi bi-hash"></i>Meja {{ $order->table_number }}
                                        </span>
                            @endif
                            <span class="badge bg-body-tertiary text-muted border rounded-pill text-capitalize">
                                        {{ $order->order_type }}
                                    </span>
                        </div>

                        {{-- Items --}}
                        <div class="mb-3">
                            @foreach($order->items as $item)
                                <div
                                    class="d-flex justify-content-between align-items-center py-1 border-bottom border-dashed"
                                    style="font-size: 0.85rem;">
                                            <span class="text-dark">
                                                <span class="fw-bold text-primary">{{ $item->quantity }}x</span>
                                                {{ $item->product_name }}
                                                @if($item->variant_name)
                                                    <small class="text-muted">({{ $item->variant_name }})</small>
                                                @endif
                                                @if($item->note)
                                                    <br><small class="text-muted fst-italic"><i
                                                            class="bi bi-chat-dots me-1"></i>{{ $item->note }}</small>
                                                @endif
                                            </span>
                                    <span class="fw-bold text-nowrap" style="color: var(--brand-caramel);">
                                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                            </span>
                                </div>
                            @endforeach
                        </div>

                        {{-- Total --}}
                        <div
                            class="d-flex justify-content-between align-items-center p-2 bg-body-tertiary rounded-3">
                            <span class="fw-bold text-muted small">TOTAL</span>
                            <h5 class="fw-bolder mb-0" style="color: var(--brand-caramel);">
                                Rp {{ number_format($order->subtotal, 0, ',', '.') }}
                            </h5>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="p-3 border-top bg-body-tertiary d-flex gap-2">
                        <button @click="$dispatch('open-cancel-modal', { orderId: {{ $order->id }} })"
                                class="btn btn-outline-danger fw-bold flex-shrink-0"
                                style="border-radius: 0.75rem;">
                            <i class="bi bi-x-lg"></i>
                        </button>
                        <button @click="openPayForOrder({{ json_encode([
                                            'id' => $order->id,
                                            'invoice_code' => $order->invoice_code,
                                            'customer_name' => $order->customer_name,
                                            'subtotal' => $order->subtotal,
                                        ]) }})"
                                class="btn btn-primary fw-bold flex-grow-1 d-flex align-items-center justify-content-center gap-2"
                                style="border-radius: 0.75rem;">
                            <i class="bi bi-cash-coin"></i> Bayar Sekarang
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
