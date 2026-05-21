<div>
    <div class="modal fade" id="orderDetailModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg bg-body text-body"
                 style="border-radius: 1.25rem; overflow: hidden;">

                @if($order)
                    <div class="modal-header bg-body border-bottom-0 px-4 pt-4 pb-3">
                        <div class="w-100 d-flex justify-content-between align-items-start">
                            <div>
                                <h4 class="fw-bolder mb-1" style="color: var(--brand-caramel, #b45309);">INV
                                    #{{ $order->invoice_code }}</h4>
                                <div class="text-secondary small fw-medium text-uppercase mt-2">
                                    <span
                                        class="badge bg-body-tertiary text-secondary border me-2">{{ $order->order_type }}</span>
                                    <i class="bi bi-calendar-event me-1"></i> {{ $order->created_at->format('d M Y, H:i') }}
                                </div>
                            </div>
                            <button type="button" class="btn-close shadow-none rounded-circle p-2 border bg-body"
                                    data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                    </div>

                    <div class="modal-body p-0 bg-body">
                        <div class="row g-0 h-100">

                            <!-- Kiri: Info Ringkasan Status & Pelanggan -->
                            <div class="col-lg-4 p-4 border-end"
                                 style="border-color: var(--bs-border-color-translucent) !important;">
                                <div class="mb-4">
                                    <span class="text-uppercase small fw-bold text-muted" style="letter-spacing: 1px;">Status Pembayaran</span>
                                    <div class="mt-2">
                                        @if($order->status == 'pending')
                                            <div
                                                class="alert alert-warning border-0 bg-warning bg-opacity-10 d-flex align-items-center mb-0">
                                                <i class="bi bi-hourglass-split fs-4 text-warning me-3"></i>
                                                <div><strong class="d-block">Belum Lunas</strong><small
                                                        class="text-secondary">Pesanan belum dibayar.</small></div>
                                            </div>
                                        @elseif($order->status == 'paid')
                                            <div
                                                class="alert alert-success border-0 bg-success bg-opacity-10 d-flex align-items-center mb-0">
                                                <i class="bi bi-check-circle-fill fs-4 text-success me-3"></i>
                                                <div><strong class="d-block">Lunas</strong><small
                                                        class="text-secondary">Pembayaran telah diterima.</small></div>
                                            </div>
                                        @elseif($order->status == 'cancelled')
                                            <div
                                                class="alert alert-danger border-0 bg-danger bg-opacity-10 d-flex align-items-center mb-0">
                                                <i class="bi bi-x-octagon-fill fs-4 text-danger me-3"></i>
                                                <div>
                                                    <strong class="d-block">Dibatalkan</strong><small
                                                        class="text-secondary">Pesanan ini dibatalkan.</small>
                                                    @if($order->cancellation_note)
                                                        <br>
                                                        <small
                                                            class="text-danger fw-medium">{{ $order->cancellation_note }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <span class="text-uppercase small fw-bold text-muted" style="letter-spacing: 1px;">Informasi Pelanggan</span>
                                    <div class="p-3 rounded-3 mt-2 bg-body-tertiary border">
                                        <div class="mb-2">
                                            <small class="text-muted d-block">Nama Pelanggan</small>
                                            <span class="fw-bold">{{ $order->customer_name }}</span>
                                        </div>
                                        @if($order->customer_phone)
                                            <div class="mb-2">
                                                <small class="text-muted d-block">No. Telepon</small>
                                                <span class="fw-bold">{{ $order->customer_phone }}</span>
                                            </div>
                                        @endif
                                        @if($order->table_number)
                                            <div>
                                                <small class="text-muted d-block">Nomor Meja</small>
                                                <span
                                                    class="badge bg-body border text-body fs-6 mt-1">Meja {{ $order->table_number }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div>
                                    <span class="text-uppercase small fw-bold text-muted" style="letter-spacing: 1px;">Rincian Pembayaran</span>
                                    <div class="p-3 rounded-3 mt-2 bg-body-tertiary border">
                                        <div class="d-flex justify-content-between mb-2 small">
                                            <span class="text-muted">Metode</span>
                                            <span class="fw-bold text-uppercase">{{ $order->formatted_payment_method }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2 small">
                                            <span class="text-muted">Subtotal</span>
                                            <span
                                                class="fw-bold">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                                        </div>
                                        @php
                                            $totalItemDiscount = $order->items->sum('discount');
                                            $extraDiscount = max(0, $order->discount - $totalItemDiscount);
                                        @endphp
                                        @if($totalItemDiscount > 0)
                                            <div class="d-flex justify-content-between mb-2 small text-danger">
                                                <span>Diskon Item</span>
                                                <span class="fw-bold">- Rp {{ number_format($totalItemDiscount, 0, ',', '.') }}</span>
                                            </div>
                                        @endif
                                        @if($extraDiscount > 0)
                                            <div class="d-flex justify-content-between mb-2 small text-danger">
                                                <span>Diskon Ekstra</span>
                                                <span class="fw-bold">- Rp {{ number_format($extraDiscount, 0, ',', '.') }}</span>
                                            </div>
                                        @endif
                                        <hr class="my-2" style="border-color: var(--bs-border-color) !important;">
                                        <div class="d-flex justify-content-between mb-2 small">
                                            <span class="text-muted">Dibayar</span>
                                            <span
                                                class="fw-bold text-success">Rp {{ number_format($order->amount_paid, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between small">
                                            <span class="text-muted">Kembalian</span>
                                            <span class="fw-bold"
                                                  style="color: var(--brand-caramel, #b45309);">Rp {{ number_format($order->change_amount, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Kanan: Tab Konten Menu & Iframe -->
                            <div class="col-lg-8 d-flex flex-column bg-body" x-data="{ tab: 'items' }">

                                <div class="px-4 pt-4 pb-0 bg-body">
                                    <ul class="nav nav-pills p-1 bg-body-tertiary rounded-pill w-100 d-flex mb-4 border shadow-sm">
                                        <li class="nav-item flex-grow-1 text-center">
                                            <button @click="tab = 'items'"
                                                    :class="tab === 'items' ? 'btn-primary text-white shadow-sm' : 'bg-transparent text-secondary'"
                                                    class="nav-link rounded-pill fw-bold px-4 py-2 transition-all border-0 w-100"
                                                    type="button">
                                                <i class="bi bi-cart3 me-1"></i> Daftar Menu
                                            </button>
                                        </li>
                                        <li class="nav-item flex-grow-1 text-center">
                                            <button @click="tab = 'invoice'"
                                                    :class="tab === 'invoice' ? 'btn-primary text-white shadow-sm' : 'bg-transparent text-secondary'"
                                                    class="nav-link rounded-pill fw-bold px-4 py-2 transition-all border-0 w-100"
                                                    type="button">
                                                <i class="bi bi-receipt me-1"></i> Rincian Tagihan
                                            </button>
                                        </li>
                                    </ul>
                                </div>

                                <div class="p-4 flex-grow-1 position-relative bg-body">

                                    {{-- List Item Menu --}}
                                    <div x-show="tab === 'items'" class="h-100" style="display: none;">
                                        <div class="d-flex flex-column gap-3 h-100 overflow-y-auto"
                                             style="max-height: 500px;">
                                            @foreach($order->items as $item)
                                                <div
                                                    class="d-flex justify-content-between align-items-start p-3 border rounded-3 shadow-sm bg-body-tertiary">
                                                    <div class="d-flex align-items-start gap-3">
                                                        <div
                                                            class="badge bg-body border text-body p-2 rounded-2 fs-6 mt-1">
                                                            {{ $item->quantity }}x
                                                        </div>
                                                        <div>
                                                            <h6 class="fw-bold mb-0">{{ $item->product_name }}</h6>
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
                                                            <div class="small text-muted mt-1">@
                                                                Rp {{ number_format($item->price, 0, ',', '.') }}</div>
                                                            @if($item->discount > 0)
                                                                <div class="text-danger small mt-1 fw-bold" style="font-size: 0.75rem;">
                                                                    <i class="bi bi-tag-fill me-1"></i> Diskon Item: -Rp {{ number_format($item->discount, 0, ',', '.') }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="text-end text-body">
                                                        @if($item->discount > 0)
                                                            <span class="text-muted text-decoration-line-through small d-block fw-normal" style="font-size: 0.75rem;">
                                                                Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                                                            </span>
                                                        @endif
                                                        <span class="fw-bolder fs-6">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Live Iframe Print Preview --}}
                                    <div x-show="tab === 'invoice'" class="h-100" style="display: none;">
                                        <div
                                            class="w-100 h-100 rounded-3 shadow-sm border overflow-hidden d-flex flex-column bg-body">
                                            <div
                                                class="border-bottom px-3 py-2 d-flex justify-content-between align-items-center bg-body-tertiary">
                                                <span class="small fw-bold text-muted"><i
                                                        class="bi bi-printer me-1"></i> Mode Cetak</span>
                                                <a href="{{ url('/invoice/' . $order->invoice_code) }}"
                                                   target="_blank"
                                                   class="btn btn-sm btn-outline-secondary rounded-pill fw-bold"
                                                   style="font-size: 0.75rem;">
                                                    Buka Fullscreen <i class="bi bi-box-arrow-up-right ms-1"></i>
                                                </a>
                                            </div>
                                            <iframe src="{{ url('/invoice/' . $order->invoice_code) }}"
                                                    class="w-100 flex-grow-1 border-0"
                                                    style="min-height: 500px; background: transparent;"></iframe>
                                        </div>
                                    </div>

                                </div>

                                {{-- Footer Total & Actions --}}
                                <div class="p-4 border-top mt-auto rounded-bottom-end bg-body-tertiary">
                                    <div class="d-flex justify-content-between align-items-end mb-4">
                                        <span class="text-muted fw-bold">Total Akhir</span>
                                        <h2 class="fw-black mb-0" style="color: var(--brand-caramel, #b45309);">
                                            Rp {{ number_format($order->total_price, 0, ',', '.') }}</h2>
                                    </div>

                                    <div class="d-flex flex-column flex-sm-row gap-2">
                                        @if($order->status == 'pending')
                                            <button wire:click="updateStatus('cancelled')" wire:loading.attr="disabled"
                                                    class="btn btn-outline-danger border py-2 px-4 fw-bold flex-grow-1 rounded-3 bg-body">
                                                <span wire:loading.remove wire:target="updateStatus('cancelled')">Batalkan Pesanan</span>
                                                <span wire:loading wire:target="updateStatus('cancelled')"
                                                      class="spinner-border spinner-border-sm"></span>
                                            </button>

                                            <button wire:click="triggerPayment" wire:loading.attr="disabled"
                                                    class="btn btn-dark py-2 px-4 fw-bold flex-grow-1 shadow-sm rounded-3 text-white"
                                                    style="background: linear-gradient(135deg, #ca8a04, #b45309); border: none;">
                                                <span wire:loading.remove
                                                      wire:target="triggerPayment">Bayar Pesanan</span>
                                                <span wire:loading wire:target="triggerPayment"
                                                      class="spinner-border spinner-border-sm"></span>
                                            </button>
                                        @else
                                            <button type="button"
                                                    class="btn btn-secondary py-2 px-4 fw-bold w-100 rounded-3"
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
                    <div class="modal-body py-5 d-flex flex-column justify-content-center align-items-center bg-body">
                        <div class="spinner-border text-secondary mb-3" role="status"
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
