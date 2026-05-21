<div>
    <div class="modal fade" id="orderDetailModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg bg-body text-body" style="border-radius: 1.25rem; overflow: hidden; max-height: 90vh;">

                @if($order)

                    {{-- ─── MODAL HEADER ───────────────────────────────────────── --}}
                    <div class="modal-header bg-body border-bottom px-4 py-3 gap-3" style="flex-shrink:0;">
                        <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-2 flex-grow-1 min-w-0">
                            <div class="min-w-0">
                                <h5 class="fw-black mb-0 text-truncate" style="color: var(--brand-caramel, #b45309); letter-spacing: -0.3px;">
                                    #{{ $order->invoice_code }}
                                </h5>
                                <div class="text-muted small mt-1 d-flex flex-wrap gap-2 align-items-center">
                                    <span class="badge bg-body-secondary text-secondary border text-capitalize">{{ $order->order_type }}</span>
                                    @if($order->table_number)
                                        <span class="badge bg-body-secondary text-secondary border">Meja {{ $order->table_number }}</span>
                                    @endif
                                    <span><i class="bi bi-clock me-1"></i>{{ $order->created_at->format('d M Y, H:i') }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Status Pill --}}
                        <div class="flex-shrink-0">
                            @if($order->status == 'pending')
                                <span class="badge rounded-pill px-3 py-2 fw-bold" style="background: rgba(234,179,8,0.12); color: #ca8a04; font-size:0.8rem;">
                                    <i class="bi bi-hourglass-split me-1"></i>Menunggu
                                </span>
                            @elseif($order->status == 'paid')
                                <span class="badge rounded-pill px-3 py-2 fw-bold" style="background: rgba(22,163,74,0.12); color: #16a34a; font-size:0.8rem;">
                                    <i class="bi bi-check-circle-fill me-1"></i>Lunas
                                </span>
                            @elseif($order->status == 'cancelled')
                                <span class="badge rounded-pill px-3 py-2 fw-bold" style="background: rgba(220,53,69,0.1); color: #dc2626; font-size:0.8rem;">
                                    <i class="bi bi-x-circle-fill me-1"></i>Dibatalkan
                                </span>
                            @endif
                        </div>

                        <button type="button" class="btn-close rounded-circle border bg-body shadow-none flex-shrink-0" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>

                    {{-- ─── MODAL BODY ──────────────────────────────────────────── --}}
                    <div class="modal-body p-0 bg-body overflow-auto">
                        <div class="row g-0 h-100">

                            {{-- ── KOLOM KIRI: Info Pelanggan + Pembayaran ────────── --}}
                            <div class="col-lg-4 p-4 border-end" style="border-color: var(--bs-border-color-translucent) !important;">

                                {{-- Pelanggan --}}
                                <div class="mb-4">
                                    <p class="text-uppercase fw-bold text-muted mb-2" style="font-size:0.68rem; letter-spacing:1.2px;">Pelanggan</p>
                                    <div class="d-flex align-items-center gap-3 p-3 rounded-3 bg-body-tertiary border">
                                        <div class="rounded-circle bg-body border d-flex align-items-center justify-content-center flex-shrink-0 fw-black"
                                             style="width:40px;height:40px;font-size:1.1rem;color:var(--brand-caramel,#b45309);">
                                            {{ strtoupper(substr($order->customer_name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="fw-bold text-truncate">{{ $order->customer_name }}</div>
                                            @if($order->customer_phone)
                                                <div class="small text-muted"><i class="bi bi-telephone me-1"></i>{{ $order->customer_phone }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Rincian Pembayaran --}}
                                <div class="mb-4">
                                    <p class="text-uppercase fw-bold text-muted mb-2" style="font-size:0.68rem; letter-spacing:1.2px;">Rincian Pembayaran</p>
                                    <div class="rounded-3 border overflow-hidden">
                                        @php
                                            $totalItemDiscount = $order->items->sum('discount');
                                            $extraDiscount = max(0, $order->discount - $totalItemDiscount);
                                        @endphp
                                        <table class="table table-sm mb-0 small" style="font-size:0.85rem;">
                                            <tbody>
                                                <tr>
                                                    <td class="text-muted ps-3 border-0 py-2">Subtotal</td>
                                                    <td class="fw-bold text-end pe-3 border-0 py-2">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
                                                </tr>
                                                @if(($order->service_charge_amount ?? 0) > 0)
                                                    <tr>
                                                        <td class="text-muted ps-3 py-2">Service ({{ $order->service_charge_percentage }}%)</td>
                                                        <td class="fw-bold text-end pe-3 py-2">Rp {{ number_format($order->service_charge_amount, 0, ',', '.') }}</td>
                                                    </tr>
                                                @endif
                                                @if(($order->tax_amount ?? 0) > 0)
                                                    <tr>
                                                        <td class="text-muted ps-3 py-2">Pajak ({{ $order->tax_percentage }}%)</td>
                                                        <td class="fw-bold text-end pe-3 py-2">Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</td>
                                                    </tr>
                                                @endif
                                                @if($totalItemDiscount > 0)
                                                    <tr>
                                                        <td class="text-danger ps-3 py-2">Diskon Item</td>
                                                        <td class="fw-bold text-end pe-3 py-2 text-danger">- Rp {{ number_format($totalItemDiscount, 0, ',', '.') }}</td>
                                                    </tr>
                                                @endif
                                                @if($extraDiscount > 0)
                                                    <tr>
                                                        <td class="text-danger ps-3 py-2">Diskon Ekstra</td>
                                                        <td class="fw-bold text-end pe-3 py-2 text-danger">- Rp {{ number_format($extraDiscount, 0, ',', '.') }}</td>
                                                    </tr>
                                                @endif
                                                <tr class="border-top">
                                                    <td class="fw-black ps-3 py-2 text-dark">TOTAL</td>
                                                    <td class="fw-black text-end pe-3 py-2" style="color:var(--brand-caramel,#b45309); font-size:1rem;">
                                                        Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                {{-- Metode & Status Bayar --}}
                                @if($order->status !== 'pending')
                                    <div>
                                        <p class="text-uppercase fw-bold text-muted mb-2" style="font-size:0.68rem; letter-spacing:1.2px;">Pembayaran Diterima</p>
                                        <div class="p-3 rounded-3 bg-body-tertiary border">
                                            <div class="d-flex justify-content-between align-items-center mb-2 small">
                                                <span class="text-muted">Metode</span>
                                                <span class="badge bg-body border text-body fw-bold">{{ $order->formatted_payment_method }}</span>
                                            </div>
                                            @if($order->payment_method == 'cash')
                                                <div class="d-flex justify-content-between small mb-1">
                                                    <span class="text-muted">Dibayar</span>
                                                    <span class="fw-bold text-success">Rp {{ number_format($order->amount_paid, 0, ',', '.') }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between small">
                                                    <span class="text-muted">Kembalian</span>
                                                    <span class="fw-bold" style="color:var(--brand-caramel,#b45309);">Rp {{ number_format($order->change_amount, 0, ',', '.') }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                @if($order->status == 'cancelled' && $order->cancellation_note)
                                    <div class="mt-3 p-3 rounded-3 border" style="background: rgba(220,53,69,0.05); border-color: rgba(220,53,69,0.2) !important;">
                                        <p class="text-uppercase fw-bold mb-1" style="font-size:0.68rem; letter-spacing:1.2px; color:#dc2626;">Alasan Pembatalan</p>
                                        <p class="small text-danger mb-0 fw-medium">{{ $order->cancellation_note }}</p>
                                    </div>
                                @endif
                            </div>

                            {{-- ── KOLOM KANAN: Tab Item + Invoice ──────────────── --}}
                            <div class="col-lg-8 d-flex flex-column bg-body" x-data="{ tab: 'items' }">

                                {{-- Tab Switcher --}}
                                <div class="px-4 pt-4 pb-0 flex-shrink-0">
                                    <div class="nav nav-pills p-1 bg-body-tertiary rounded-pill border shadow-sm d-flex mb-4">
                                        <button @click="tab = 'items'"
                                                :class="tab === 'items' ? 'btn-primary text-white shadow-sm' : 'bg-transparent text-secondary'"
                                                class="nav-link rounded-pill fw-bold px-4 py-2 border-0 flex-grow-1 transition-all"
                                                type="button">
                                            <i class="bi bi-bag-check me-1"></i>
                                            Item <span class="badge rounded-pill ms-1" :class="tab === 'items' ? 'bg-white text-primary' : 'bg-body-secondary text-secondary'">{{ $order->items->count() }}</span>
                                        </button>
                                        <button @click="tab = 'invoice'"
                                                :class="tab === 'invoice' ? 'btn-primary text-white shadow-sm' : 'bg-transparent text-secondary'"
                                                class="nav-link rounded-pill fw-bold px-4 py-2 border-0 flex-grow-1 transition-all"
                                                type="button">
                                            <i class="bi bi-receipt me-1"></i> Struk
                                        </button>
                                    </div>
                                </div>

                                {{-- Tab Content --}}
                                <div class="px-4 pb-2 flex-grow-1 overflow-auto" style="min-height: 0;">

                                    {{-- Tab: Daftar Item --}}
                                    <div x-show="tab === 'items'" x-cloak class="d-flex flex-column gap-2 pb-2">
                                        @foreach($order->items as $item)
                                            <div class="d-flex align-items-start gap-3 p-3 border rounded-3 bg-body-tertiary">
                                                <div class="badge bg-body border text-body fw-black rounded-2 px-2 py-2 flex-shrink-0" style="font-size:0.9rem; min-width: 36px; text-align:center;">
                                                    {{ $item->quantity }}x
                                                </div>
                                                <div class="flex-grow-1 min-w-0">
                                                    <div class="fw-bold text-truncate">{{ $item->product_name }}</div>
                                                    @if($item->variant_name)
                                                        <div class="small text-muted"><i class="bi bi-tag me-1"></i>{{ $item->variant_name }}</div>
                                                    @endif
                                                    @if($item->note)
                                                        <div class="small mt-1 px-2 py-1 rounded-2 fw-medium" style="background:rgba(234,179,8,0.1); color:#92400e;">
                                                            <i class="bi bi-chat-quote me-1"></i>"{{ $item->note }}"
                                                        </div>
                                                    @endif
                                                    <div class="small text-muted mt-1">@ Rp {{ number_format($item->price, 0, ',', '.') }}</div>
                                                </div>
                                                <div class="text-end flex-shrink-0">
                                                    @if($item->discount > 0)
                                                        <div class="text-muted text-decoration-line-through small">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</div>
                                                    @endif
                                                    <div class="fw-bolder text-body">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</div>
                                                    @if($item->discount > 0)
                                                        <div class="small text-danger fw-bold">- Rp {{ number_format($item->discount, 0, ',', '.') }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    {{-- Tab: Invoice / Struk --}}
                                    <div x-show="tab === 'invoice'" x-cloak style="height: 480px;">
                                        <div class="w-100 h-100 rounded-3 border overflow-hidden d-flex flex-column">
                                            <div class="border-bottom px-3 py-2 d-flex justify-content-between align-items-center bg-body-tertiary flex-shrink-0">
                                                <span class="small fw-bold text-muted"><i class="bi bi-receipt me-1"></i>Preview Struk</span>
                                                <a href="{{ url('/invoice/' . $order->invoice_code) }}"
                                                   target="_blank"
                                                   class="btn btn-sm btn-outline-secondary rounded-pill fw-bold"
                                                   style="font-size:0.75rem;">
                                                    Buka Tab Baru <i class="bi bi-box-arrow-up-right ms-1"></i>
                                                </a>
                                            </div>
                                            <iframe src="{{ url('/invoice/' . $order->invoice_code) }}"
                                                    class="w-100 flex-grow-1 border-0"
                                                    style="background: transparent;">
                                            </iframe>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ─── MODAL FOOTER: Action Buttons ───────────────────────── --}}
                    <div class="modal-footer bg-body border-top px-4 py-3" style="flex-shrink:0;">
                        @if($order->status == 'pending')
                            <div class="d-flex gap-2 w-100" x-data="{ confirmCancel: false }">
                                {{-- Tutup --}}
                                <button type="button" class="btn btn-outline-secondary fw-bold rounded-3 px-4"
                                        data-bs-dismiss="modal" x-show="!confirmCancel">
                                    Tutup
                                </button>

                                {{-- Batalkan — inline confirm, tidak pakai browser dialog --}}
                                <template x-if="!confirmCancel">
                                    <button @click="confirmCancel = true"
                                            class="btn btn-outline-danger fw-bold rounded-3 px-4">
                                        <i class="bi bi-x-lg me-1"></i>Batalkan
                                    </button>
                                </template>

                                {{-- Konfirmasi Batal --}}
                                <template x-if="confirmCancel">
                                    <div class="d-flex align-items-center gap-2 flex-grow-1 p-2 rounded-3 border" style="background:rgba(220,53,69,0.05); border-color:rgba(220,53,69,0.2) !important;">
                                        <i class="bi bi-exclamation-triangle-fill text-danger flex-shrink-0"></i>
                                        <span class="small fw-medium text-danger flex-grow-1">Yakin batalkan pesanan ini?</span>
                                        <button @click="confirmCancel = false" class="btn btn-sm btn-outline-secondary rounded-3 fw-bold">Tidak</button>
                                        <button wire:click="updateStatus('cancelled')" wire:loading.attr="disabled"
                                                class="btn btn-sm btn-danger rounded-3 fw-bold">
                                            <span wire:loading.remove wire:target="updateStatus('cancelled')">Ya, Batalkan</span>
                                            <span wire:loading wire:target="updateStatus('cancelled')" class="spinner-border spinner-border-sm"></span>
                                        </button>
                                    </div>
                                </template>

                                {{-- Bayar — selalu di kanan --}}
                                <button wire:click="triggerPayment" wire:loading.attr="disabled"
                                        class="btn fw-bold rounded-3 px-4 ms-auto text-white flex-shrink-0"
                                        style="background: linear-gradient(135deg, #ca8a04, #b45309); border: none; min-width: 130px;">
                                    <span wire:loading.remove wire:target="triggerPayment">
                                        <i class="bi bi-cash-coin me-1"></i>Bayar
                                    </span>
                                    <span wire:loading wire:target="triggerPayment" class="spinner-border spinner-border-sm"></span>
                                </button>
                            </div>
                        @else
                            <div class="d-flex gap-2 w-100 justify-content-between">
                                <a href="{{ url('/invoice/' . $order->invoice_code) }}" target="_blank"
                                   class="btn btn-outline-secondary fw-bold rounded-3 px-4">
                                    <i class="bi bi-receipt me-1"></i>Lihat Struk
                                </a>
                                <button type="button" class="btn btn-dark fw-bold rounded-3 px-5" data-bs-dismiss="modal">
                                    Tutup
                                </button>
                            </div>
                        @endif
                    </div>

                @else
                    <div class="modal-body py-5 d-flex flex-column justify-content-center align-items-center bg-body">
                        <div class="spinner-border text-secondary mb-3" role="status" style="width:2.5rem;height:2.5rem;"></div>
                        <p class="text-muted fw-bold mb-0">Memuat data pesanan...</p>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>

@script
<script>
    $wire.on('show-order-modal', () => {
        const el = document.getElementById('orderDetailModal');
        bootstrap.Modal.getOrCreateInstance(el).show();
    });

    $wire.on('hide-order-modal', () => {
        const el = document.getElementById('orderDetailModal');
        bootstrap.Modal.getOrCreateInstance(el).hide();
    });
</script>
@endscript
