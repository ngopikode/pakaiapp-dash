@php
    $totalCash = $todayOrders->where('payment_method', 'cash')->sum('total_price');
    $totalQris = $todayOrders->where('payment_method', 'qris')->sum('total_price');
    $totalTransfer = $todayOrders->where('payment_method', 'transfer')->sum('total_price');
    $grandTotalSales = $todayOrders->sum('total_price');
    $storeSetting = \App\Models\StoreSetting::first();
    $storeName = $storeSetting->name ?? 'Toko Kami';
@endphp

{{-- 1. Stats Summary Cards --}}
<div class="row g-3 mb-4 mt-2 px-2 px-lg-0">
    <div class="col-6 col-md-3">
        <div class="card p-3 border shadow-sm bg-body"
             style="border-radius: 1.25rem; border-color: var(--bs-border-color-translucent) !important;">
            <span class="text-secondary small fw-bold"><i class="bi bi-cash-stack text-success me-1"></i>Tunai</span>
            <h5 class="fw-bold mb-0 mt-1 text-success">Rp {{ number_format($totalCash, 0, ',', '.') }}</h5>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card p-3 border shadow-sm bg-body"
             style="border-radius: 1.25rem; border-color: var(--bs-border-color-translucent) !important;">
            <span class="text-secondary small fw-bold"><i class="bi bi-qr-code-scan text-primary me-1"></i>QRIS</span>
            <h5 class="fw-bold mb-0 mt-1 text-primary">Rp {{ number_format($totalQris, 0, ',', '.') }}</h5>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card p-3 border shadow-sm bg-body"
             style="border-radius: 1.25rem; border-color: var(--bs-border-color-translucent) !important;">
            <span class="text-secondary small fw-bold"><i class="bi bi-bank text-info me-1"></i>Transfer</span>
            <h5 class="fw-bold mb-0 mt-1 text-info">Rp {{ number_format($totalTransfer, 0, ',', '.') }}</h5>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card p-3 border-0 shadow-sm text-white"
             style="border-radius: 1.25rem; background: linear-gradient(135deg, #ca8a04, #b45309);">
            <span class="opacity-75 small fw-bold"><i class="bi bi-graph-up me-1"></i>Total Omset</span>
            <h5 class="fw-bold mb-0 mt-1 text-white">Rp {{ number_format($grandTotalSales, 0, ',', '.') }}</h5>
        </div>
    </div>
</div>

{{-- 2. History Orders List --}}
@if($todayOrders->isEmpty())
    <div class="card border p-5 text-center bg-body-tertiary"
         style="border-radius: 1.25rem; border-color: var(--bs-border-color-translucent) !important;">
        <i class="bi bi-journal-x text-secondary" style="font-size: 4rem; opacity: 0.3;"></i>
        <h5 class="fw-bold mt-3 text-body">Belum ada transaksi hari ini</h5>
        <p class="text-secondary small mb-0">Mulai layani pelanggan di tab "Kasir Baru" 🚀</p>
    </div>
@else
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3 px-2 px-lg-0 pb-4">
        @foreach($todayOrders as $order)
            @php
                $waPhone = '';
                if ($order->customer_phone) {
                    $phoneClean = preg_replace('/\D/', '', $order->customer_phone);
                    if (str_starts_with($phoneClean, '0')) {
                        $waPhone = '62' . substr($phoneClean, 1);
                    } elseif (!str_starts_with($phoneClean, '62')) {
                        $waPhone = '62' . $phoneClean;
                    } else {
                        $waPhone = $phoneClean;
                    }
                }
                $strukUrl = url("/invoice/{$order->invoice_code}");
                $waMessage = "Halo Kak *" . ($order->customer_name ?: 'Pelanggan') . "*,\n\nTerima kasih telah berbelanja di *" . $storeName . "*.\nStruk digital: " . $strukUrl . "\n\nTotal: Rp " . number_format($order->total_price, 0, ',', '.');
                $waUrl = $waPhone ? "https://wa.me/{$waPhone}?text=" . rawurlencode($waMessage) : '#';
            @endphp
            <div class="col">
                <div class="card border shadow-sm h-100 overflow-hidden bg-body"
                     style="border-radius: 1.25rem; border-color: var(--bs-border-color-translucent) !important; transition: transform 0.2s;"
                     onmouseover="this.style.transform='translateY(-2px)'"
                     onmouseout="this.style.transform='translateY(0)'">

                    {{-- Card Header --}}
                    <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-body-tertiary"
                         style="border-color: var(--bs-border-color-translucent) !important;">
                        <div>
                            <h6 class="fw-bold mb-0 text-body">{{ $order->invoice_code }}</h6>
                            <small class="text-secondary fw-bold" style="font-size: 0.7rem;">
                                {{ $order->created_at->diffForHumans() }} ({{ $order->created_at->format('H:i') }})
                            </small>
                        </div>
                        @if($order->status === 'pending')
                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill fw-bold px-3 py-1.5" style="font-size: 0.75rem;">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>Belum Bayar
                            </span>
                        @elseif($order->payment_method === 'cash')
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill fw-bold px-3 py-1.5" style="font-size: 0.75rem;">
                                <i class="bi bi-cash-stack me-1"></i>Tunai
                            </span>
                        @elseif($order->payment_method === 'qris')
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill fw-bold px-3 py-1.5" style="font-size: 0.75rem;">
                                <i class="bi bi-qr-code-scan me-1"></i>QRIS
                            </span>
                        @else
                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill fw-bold px-3 py-1.5" style="font-size: 0.75rem;">
                                <i class="bi bi-bank me-1"></i>Transfer
                            </span>
                        @endif
                    </div>

                    {{-- Card Body --}}
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            {{-- Customer Badge Info --}}
                            <div class="d-flex gap-2 mb-3 flex-wrap">
                                <span class="badge bg-body-tertiary text-secondary border rounded-pill fw-medium py-1 px-2.5">
                                    <i class="bi bi-person me-1"></i>{{ $order->customer_name ?: 'Pelanggan Umum' }}
                                </span>
                                @if($order->customer_phone)
                                    <span class="badge bg-body-tertiary text-secondary border rounded-pill fw-medium py-1 px-2.5">
                                        <i class="bi bi-whatsapp me-1 text-success"></i>{{ $order->customer_phone }}
                                    </span>
                                @endif
                            </div>

                            {{-- Items purchased --}}
                            <div class="mb-3">
                                @foreach($order->items as $item)
                                    <div class="d-flex justify-content-between align-items-center py-1 border-bottom border-dashed"
                                         style="font-size: 0.85rem; border-color: var(--bs-border-color-translucent) !important;">
                                        <span class="text-body">
                                            <span class="fw-bold text-primary">{{ $item->quantity }}x</span>
                                            {{ $item->product_name }}
                                            @if($item->variant_name)
                                                <small class="text-muted">({{ $item->variant_name }})</small>
                                            @endif
                                        </span>
                                        <span class="fw-bold text-nowrap text-secondary" style="font-size: 0.85rem;">
                                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Calculation Box & Actions --}}
                        <div>
                            <div class="d-flex justify-content-between align-items-center p-2.5 bg-body-tertiary rounded-3 border mb-3">
                                <div>
                                    <span class="fw-bold text-muted" style="font-size: 0.65rem; display: block; line-height: 1;">TOTAL BERSIH</span>
                                    @if($order->discount > 0)
                                        <small class="text-danger fw-bold" style="font-size: 0.7rem;">(Diskon: Rp {{ number_format($order->discount, 0, ',', '.') }})</small>
                                    @endif
                                </div>
                                <h5 class="fw-bold mb-0 text-caramel-solid">
                                    Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                </h5>
                            </div>

                            <div class="d-flex gap-2">
                                @if($order->status === 'pending')
                                    <button @click="openPayForOrder({{ json_encode($order) }})"
                                            class="btn btn-sm btn-warning w-100 fw-bold py-2.5 d-flex align-items-center justify-content-center gap-2 border-0"
                                            style="border-radius: 0.75rem; font-size: 0.85rem; background: linear-gradient(135deg, #f59e0b, #d97706); color: white; box-shadow: 0 4px 10px rgba(217, 119, 6, 0.25);">
                                        <i class="bi bi-credit-card-2-front"></i> Bayar Sekarang
                                    </button>
                                @else
                                    <a href="{{ $strukUrl }}" target="_blank"
                                       class="btn btn-sm btn-outline-secondary fw-bold bg-body border py-2 flex-grow-1 d-flex align-items-center justify-content-center gap-1"
                                       style="border-radius: 0.75rem; font-size: 0.8rem;">
                                        <i class="bi bi-printer"></i> Lihat Struk
                                    </a>

                                    @if($order->customer_phone)
                                        <a href="{{ $waUrl }}" target="_blank"
                                           class="btn btn-sm btn-success fw-bold py-2 px-3 d-flex align-items-center justify-content-center gap-1 border-0"
                                           style="border-radius: 0.75rem; font-size: 0.8rem; background-color: #25d366;">
                                            <i class="bi bi-whatsapp"></i> Kirim WA
                                        </a>
                                    @else
                                        <div class="btn btn-sm btn-outline-success fw-bold py-2 px-3 d-flex align-items-center justify-content-center gap-1 position-relative"
                                                style="border-radius: 0.75rem; font-size: 0.8rem; cursor: pointer;"
                                                x-data="{ showInput: false, phoneNum: '' }"
                                                @click="showInput = !showInput"
                                                :class="showInput ? 'active' : ''">
                                            <i class="bi bi-whatsapp"></i> Struk WA
                                            <div x-show="showInput" class="position-absolute bg-body p-3 border shadow-lg rounded-4 text-start text-body"
                                                 style="z-index: 100; bottom: 45px; right: 10px; width: 250px;"
                                                 @click.stop>
                                                <label class="small fw-bold text-muted mb-1">Kirim ke nomor WA:</label>
                                                <div class="input-group input-group-sm">
                                                    <input type="text" class="form-control bg-body-tertiary text-body border"
                                                           x-model="phoneNum" placeholder="Contoh: 081234..."
                                                           style="border-radius: 0.5rem 0 0 0.5rem;">
                                                    <button class="btn btn-success border-0" type="button"
                                                            @click="
                                                                if (phoneNum.length >= 9) {
                                                                    $wire.updateCustomerPhone('{{ $order->invoice_code }}', phoneNum);
                                                                    let cleanP = phoneNum.replace(/\D/g, '');
                                                                    let formattedP = cleanP.startsWith('0') ? '62' + cleanP.substring(1) : (cleanP.startsWith('62') ? cleanP : '62' + cleanP);
                                                                    let dynamicUrl = `https://wa.me/${formattedP}?text=${encodeURIComponent('Halo Kak,\n\nTerima kasih telah berbelanja!\nStruk digital: {{ $strukUrl }}\n\nTotal: Rp {{ number_format($order->total_price, 0, ',', '.') }}')}`;
                                                                    window.open(dynamicUrl, '_blank');
                                                                    showInput = false;
                                                                    phoneNum = '';
                                                                    location.reload();
                                                                } else {
                                                                    showIslandToast('Nomor tidak valid!', 'warning');
                                                                }
                                                            ">
                                                        Kirim
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        @endforeach
    </div>
@endif
