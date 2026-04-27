<div>
    <div class="modal fade" id="orderDetailModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 1.25rem; overflow: hidden;">

                @if($selectedOrder)
                    <div class="modal-header bg-light bg-opacity-75 border-bottom-0 px-4 pt-4 pb-3">
                        <div class="w-100 d-flex justify-content-between align-items-start">
                            <div>
                                <h4 class="fw-bolder text-primary mb-1">INV #{{ $selectedOrder->invoice_code }}</h4>
                                <div class="text-muted small fw-medium text-uppercase mt-2">
                                    <span class="badge bg-secondary me-2">{{ $selectedOrder->order_type }}</span>
                                    <i class="bi bi-calendar-event me-1"></i> {{ $selectedOrder->created_at->format('d M Y, H:i') }}
                                </div>
                            </div>
                            <button type="button" class="btn-close shadow-none bg-white rounded-circle p-2 border"
                                    data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                    </div>

                    <div class="modal-body p-0">
                        <div class="row g-0 h-100">

                            <div class="col-lg-4 p-4 border-end border-light bg-white">
                                <div class="mb-4">
                                    <span class="text-uppercase small fw-bold text-muted" style="letter-spacing: 1px;">Status Pembayaran</span>
                                    <div class="mt-2">
                                        @if($selectedOrder->status == 'pending')
                                            <div
                                                class="alert alert-warning border-0 bg-warning bg-opacity-10 text-dark d-flex align-items-center mb-0">
                                                <i class="bi bi-hourglass-split fs-4 text-warning me-3"></i>
                                                <div><strong class="d-block">Belum Lunas</strong><small>Pesanan belum
                                                        dibayar.</small></div>
                                            </div>
                                        @elseif($selectedOrder->status == 'paid')
                                            <div
                                                class="alert alert-success border-0 bg-success bg-opacity-10 text-dark d-flex align-items-center mb-0">
                                                <i class="bi bi-check-circle-fill fs-4 text-success me-3"></i>
                                                <div><strong class="d-block">Lunas</strong><small>Pembayaran telah
                                                        diterima.</small></div>
                                            </div>
                                        @elseif($selectedOrder->status == 'cancelled')
                                            <div
                                                class="alert alert-danger border-0 bg-danger bg-opacity-10 text-dark d-flex align-items-center mb-0">
                                                <i class="bi bi-x-octagon-fill fs-4 text-danger me-3"></i>
                                                <div><strong class="d-block">Dibatalkan</strong><small>Pesanan ini
                                                        dibatalkan.</small></div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <span class="text-uppercase small fw-bold text-muted" style="letter-spacing: 1px;">Informasi Pelanggan</span>
                                    <div class="bg-light p-3 rounded-3 mt-2">
                                        <div class="mb-2">
                                            <small class="text-muted d-block">Nama Pelanggan</small>
                                            <span class="fw-bold text-dark">{{ $selectedOrder->customer_name }}</span>
                                        </div>
                                        @if($selectedOrder->customer_phone)
                                            <div class="mb-2">
                                                <small class="text-muted d-block">No. Telepon</small>
                                                <span
                                                    class="fw-bold text-dark">{{ $selectedOrder->customer_phone }}</span>
                                            </div>
                                        @endif
                                        @if($selectedOrder->table_number)
                                            <div>
                                                <small class="text-muted d-block">Nomor Meja</small>
                                                <span
                                                    class="badge bg-dark fs-6">{{ $selectedOrder->table_number }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div>
                                    <span class="text-uppercase small fw-bold text-muted" style="letter-spacing: 1px;">Rincian Pembayaran</span>
                                    <div class="bg-light p-3 rounded-3 mt-2">
                                        <div class="d-flex justify-content-between mb-2 small">
                                            <span class="text-muted">Metode</span>
                                            <span
                                                class="fw-bold text-uppercase">{{ $selectedOrder->payment_method }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2 small">
                                            <span class="text-muted">Subtotal</span>
                                            <span
                                                class="fw-bold">Rp {{ number_format($selectedOrder->subtotal, 0, ',', '.') }}</span>
                                        </div>
                                        @if($selectedOrder->discount > 0)
                                            <div class="d-flex justify-content-between mb-2 small text-danger">
                                                <span>Diskon</span>
                                                <span
                                                    class="fw-bold">- Rp {{ number_format($selectedOrder->discount, 0, ',', '.') }}</span>
                                            </div>
                                        @endif
                                        <hr class="my-2 border-secondary border-opacity-25">
                                        <div class="d-flex justify-content-between mb-2 small">
                                            <span class="text-muted">Dibayar</span>
                                            <span
                                                class="fw-bold text-success">Rp {{ number_format($selectedOrder->amount_paid, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between small">
                                            <span class="text-muted">Kembalian</span>
                                            <span
                                                class="fw-bold">Rp {{ number_format($selectedOrder->change_amount, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-8 d-flex flex-column bg-light bg-opacity-25">

                                <div class="px-4 pt-4 pb-0">
                                    <ul class="nav nav-pills bg-white p-1 rounded-pill shadow-sm d-inline-flex border"
                                        role="tablist" style="font-size: 0.9rem;">
                                        <li class="nav-item" role="presentation">
                                            <button
                                                class="nav-link active bg-primary rounded-pill fw-bold px-4 py-2"
                                                data-bs-toggle="pill" data-bs-target="#tab-items" type="button"
                                                role="tab">
                                                <i class="bi bi-list-ul me-1"></i> Daftar Pesanan
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link rounded-pill bg-light fw-bold px-4 py-2"
                                                    data-bs-toggle="pill" data-bs-target="#tab-invoice" type="button"
                                                    role="tab">
                                                <i class="bi bi-receipt me-1"></i> Preview Invoice
                                            </button>
                                        </li>
                                    </ul>
                                </div>

                                <div class="p-4 flex-grow-1 tab-content">

                                    <div class="tab-pane fade show active h-100" id="tab-items" role="tabpanel">
                                        <div class="d-flex flex-column gap-3">
                                            @foreach($selectedOrder->items as $item)
                                                <div
                                                    class="d-flex justify-content-between align-items-start bg-white p-3 border rounded-3 shadow-sm">
                                                    <div class="d-flex align-items-start gap-3">
                                                        <div
                                                            class="badge bg-dark text-white border p-2 rounded-2 fs-6 mt-1">
                                                            {{ $item->quantity }}x
                                                        </div>
                                                        <div>
                                                            <h6 class="fw-bold text-dark mb-0">{{ $item->product_name }}</h6>
                                                            @if($item->variant_name)
                                                                <small class="text-muted d-block mt-1"><i
                                                                        class="bi bi-tag"></i>
                                                                    Varian: {{ $item->variant_name }}</small>
                                                            @endif
                                                            @if($item->note)
                                                                <div
                                                                    class="bg-warning bg-opacity-10 text-warning-emphasis p-2 rounded mt-2 small fw-medium">
                                                                    <i class="bi bi-chat-quote me-1"></i>
                                                                    "{{ $item->note }}"
                                                                </div>
                                                            @endif
                                                            <div class="small text-muted mt-2">@
                                                                Rp {{ number_format($item->price, 0, ',', '.') }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="fw-bolder text-dark fs-6 text-end">
                                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="tab-pane fade h-100" id="tab-invoice" role="tabpanel">
                                        <div
                                            class="w-100 h-100 bg-white rounded-3 shadow-sm border overflow-hidden d-flex flex-column">
                                            <div
                                                class="bg-light border-bottom px-3 py-2 d-flex justify-content-between align-items-center">
                                                <span class="small fw-bold text-muted"><i
                                                        class="bi bi-printer me-1"></i> Mode Cetak</span>
                                                <a href="{{ url('/invoice/' . $selectedOrder->invoice_code) }}"
                                                   target="_blank"
                                                   class="btn btn-sm btn-outline-secondary rounded-pill fw-bold"
                                                   style="font-size: 0.75rem;">
                                                    Buka Fullscreen <i class="bi bi-box-arrow-up-right ms-1"></i>
                                                </a>
                                            </div>
                                            <iframe src="{{ url('/invoice/' . $selectedOrder->invoice_code) }}"
                                                    class="w-100 flex-grow-1 border-0"
                                                    style="min-height: 500px;"></iframe>
                                        </div>
                                    </div>

                                </div>

                                <div class="p-4 bg-white border-top mt-auto rounded-bottom-end">
                                    <div class="d-flex justify-content-between align-items-end mb-4">
                                        <span class="text-muted fw-bold">Total Akhir</span>
                                        <h2 class="fw-bolder text-primary mb-0">
                                            Rp {{ number_format($selectedOrder->total_price, 0, ',', '.') }}</h2>
                                    </div>

                                    <div class="d-flex flex-column flex-sm-row gap-2">
                                        @if($selectedOrder->status == 'pending')
                                            <button wire:click="updateStatus('cancelled')" wire:loading.attr="disabled"
                                                    class="btn btn-light border text-danger py-2 px-4 fw-bold flex-grow-1 rounded-3">
                                                <span wire:loading.remove wire:target="updateStatus('cancelled')">Batalkan Pesanan</span>
                                                <span wire:loading wire:target="updateStatus('cancelled')"
                                                      class="spinner-border spinner-border-sm"></span>
                                            </button>

                                            <button wire:click="updateStatus('paid')" wire:loading.attr="disabled"
                                                    class="btn btn-dark py-2 px-4 fw-bold flex-grow-1 shadow-sm rounded-3">
                                                <span wire:loading.remove wire:target="updateStatus('paid')">Tandai Lunas</span>
                                                <span wire:loading wire:target="updateStatus('paid')"
                                                      class="spinner-border spinner-border-sm"></span>
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-dark py-2 px-4 fw-bold w-100 rounded-3"
                                                    data-bs-dismiss="modal">
                                                Tutup
                                            </button>
                                        @endif
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                @else
                    <div class="modal-body py-5 d-flex flex-column justify-content-center align-items-center">
                        <div class="spinner-border text-dark mb-3" role="status"
                             style="width: 3rem; height: 3rem;"></div>
                        <h6 class="text-muted fw-bold">Memuat data pesanan...</h6>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@script
<script>
    $wire.on('show-order-modal', () => {
        const orderModalElement = document.getElementById('orderDetailModal');
        const orderModal = bootstrap.Modal.getOrCreateInstance(orderModalElement);
        orderModal.show();
    });

    $wire.on('hide-order-modal', () => {
        const orderModalElement = document.getElementById('orderDetailModal');
        const orderModal = bootstrap.Modal.getOrCreateInstance(orderModalElement);
        orderModal.hide();
    });
</script>
@endscript
