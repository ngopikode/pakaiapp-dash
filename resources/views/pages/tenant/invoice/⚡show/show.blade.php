<div @if($order->status === 'pending') wire:poll.5s="refreshOrder" @endif class="py-4 py-md-5 px-3">
    <!-- Premium Copy Toast Notification -->
    <div id="custom-toast" class="custom-toast bg-dark text-white rounded-3 shadow-lg px-4 py-2 text-center"
         style="display:none; position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 9999; font-size: 0.85rem; font-weight: bold;">
        Tersalin!
    </div>

    <div class="mx-auto mb-4 d-flex flex-wrap justify-content-center align-items-center gap-2 no-print"
         style="max-width: 450px;">
        <button onclick="window.print()"
                class="btn btn-dark rounded-3 fw-bold shadow-sm d-flex align-items-center gap-2 px-3 py-2">
            <i class="bi bi-printer"></i> Cetak
        </button>
        <button onclick="downloadReceipt()"
                class="btn btn-primary rounded-3 shadow-sm px-3 py-2 d-flex align-items-center gap-2">
            <i class="bi bi-download"></i> Download
        </button>
        <a href="https://wa.me/?text={{ urlencode(url()->current()) }}" target="_blank"
           class="btn btn-success rounded-3 shadow-sm px-3 py-2 d-flex align-items-center gap-2">
            <i class="bi bi-whatsapp"></i> Bagikan
        </a>
    </div>

    <!-- Panel Selesaikan Pembayaran Dinamis (Duitku) -->
    @if($order->status === 'pending' && ($order->duitku_va_number || $order->duitku_payment_url))
        @php
            $duitkuDetails = $this->getPaymentMethodDetails();
            $instructions = $this->getPaymentInstructions();
        @endphp
        <div class="payment-instruction-container mx-auto mb-4 p-4 rounded-4 shadow-sm bg-white border no-print"
             style="max-width: 420px; border-radius: 16px !important;">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="badge bg-warning text-dark fw-bold text-uppercase px-2.5 py-1.5 animate-pulse"
                      style="font-size: 0.7rem; border-radius: 6px;">Menunggu Pembayaran</span>
                <span class="small text-muted d-flex align-items-center gap-1" style="font-size: 0.75rem;">
                    <i class="bi bi-clock-history"></i> Cek otomatis...
                </span>
            </div>

            @if(config('duitku.sandbox'))
                <div class="alert alert-warning border-0 rounded-3 mb-3 p-3 text-start d-flex gap-2.5 shadow-none"
                     style="background-color: #fffbeb; border-left: 4px solid #f59e0b !important; border-radius: 10px !important;">
                    <i class="bi bi-exclamation-triangle-fill text-warning fs-5 flex-shrink-0" style="margin-top: 1px;"></i>
                    <div>
                        <h6 class="fw-bold mb-1" style="font-size: 0.8rem; color: #78350f;">Mode Uji Coba (Sandbox)</h6>
                        <p class="mb-0 text-muted" style="font-size: 0.7rem; line-height: 1.4; color: #92400e !important;">
                            Website ini sedang dalam tahap uji coba pembayaran. Jangan gunakan kartu kredit atau rekening asli.
                        </p>
                    </div>
                </div>
            @endif

            <div class="d-flex align-items-center gap-3 bg-light p-3 rounded-3 mb-4"
                 style="border-radius: 12px !important;">
                <img src="{{ $duitkuDetails['logo'] }}" alt="{{ $duitkuDetails['name'] }}"
                     class="rounded shadow-sm bg-white" style="width: 55px; height: auto; padding: 2px;">
                <div>
                    <h6 class="fw-bold mb-0.5 text-dark" style="font-size: 0.9rem;">{{ $duitkuDetails['name'] }}</h6>
                    <span class="text-muted small" style="font-size: 0.75rem;">Pembayaran digital via Duitku</span>
                </div>
            </div>

            <!-- Nominal Pembayaran -->
            <div class="mb-4 text-center py-3 bg-dark text-white rounded-3 position-relative overflow-hidden"
                 style="border-radius: 12px !important;">
                <span class="text-zinc-400 small text-uppercase tracking-wider fw-bold d-block mb-1"
                      style="font-size: 0.7rem; color: #a1a1aa;">Total Tagihan</span>
                <h3 class="fw-bolder mb-2 text-warning font-mono" style="font-size: 1.45rem;">
                    Rp {{ number_format($order->total_price, 0, ',', '.') }}</h3>
                <button onclick="copyToClipboard('{{ $order->total_price }}', 'Nominal Berhasil Disalin!')"
                        class="btn btn-outline-light btn-sm rounded-pill px-3 fw-bold border-zinc-700 hover:bg-zinc-800 text-xs"
                        style="font-size: 0.7rem;">
                    <i class="bi bi-clipboard me-1"></i> Salin Nominal
                </button>
            </div>

            @if($order->duitku_va_number)
                <!-- Virtual Account -->
                <div class="mb-4">
                    <label class="form-label text-muted small fw-bold text-uppercase tracking-wider mb-2"
                           style="font-size: 0.7rem;">Nomor Virtual Account</label>
                    <div class="d-flex align-items-center gap-2 p-3 bg-light border rounded-3 justify-content-between"
                         style="border-radius: 12px !important;">
                        <span class="fs-5 fw-bold font-mono text-dark"
                              style="letter-spacing: 1px;">{{ $order->duitku_va_number }}</span>
                        <button
                            onclick="copyToClipboard('{{ $order->duitku_va_number }}', 'Nomor VA Berhasil Disalin!')"
                            class="btn btn-primary btn-sm rounded-3 shadow-sm px-3 fw-bold flex-shrink-0 d-flex align-items-center gap-1.5"
                            style="border-radius: 8px !important;">
                            <i class="bi bi-clipboard"></i> Salin
                        </button>
                    </div>
                </div>

                <!-- Petunjuk Pembayaran -->
                <div>
                    <label class="form-label text-muted small fw-bold text-uppercase tracking-wider mb-2"
                           style="font-size: 0.7rem;">Petunjuk Pembayaran</label>
                    <div class="accordion" id="accordionInstructions">
                        @foreach($instructions as $title => $steps)
                            <div class="accordion-item border rounded-3 mb-2 overflow-hidden bg-white shadow-sm"
                                 style="border-radius: 10px !important;">
                                <h2 class="accordion-header" id="heading{{ \Illuminate\Support\Str::slug($title) }}">
                                    <button
                                        class="accordion-button collapsed fw-bold text-dark bg-white py-3 px-3 shadow-none"
                                        type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse{{ \Illuminate\Support\Str::slug($title) }}"
                                        aria-expanded="false"
                                        aria-controls="collapse{{ \Illuminate\Support\Str::slug($title) }}"
                                        style="font-size: 0.8rem;">
                                        {{ $title }}
                                    </button>
                                </h2>
                                <div id="collapse{{ \Illuminate\Support\Str::slug($title) }}"
                                     class="accordion-collapse collapse"
                                     aria-labelledby="heading{{ \Illuminate\Support\Str::slug($title) }}"
                                     data-bs-parent="#accordionInstructions">
                                    <div class="accordion-body bg-light text-muted small py-3 px-3"
                                         style="font-size: 0.75rem;">
                                        <ol class="mb-0 ps-3">
                                            @foreach($steps as $step)
                                                <li class="mb-2">{!! $step !!}</li>
                                            @endforeach
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <!-- Non-VA (QRIS, E-Wallet, CC) -->
                <div class="text-center py-2">
                    <p class="text-muted small mb-4" style="font-size: 0.75rem; line-height: 1.5;">
                        Silakan klik tombol di bawah ini untuk memproses pembayaran digital Anda via portal pembayaran
                        aman Duitku.
                    </p>
                    <a href="{{ $order->duitku_payment_url }}" target="_blank"
                       class="btn btn-dark w-100 py-3 rounded-3 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2 text-uppercase tracking-wider"
                       style="font-size: 0.8rem; border-radius: 12px !important;">
                        <i class="bi bi-wallet2"></i> Bayar Sekarang via Duitku
                    </a>
                </div>
            @endif
        </div>
    @endif

    @if(($store->store_type ?? 'resto') === 'resto')
        <div class="mx-auto mb-4 d-flex flex-wrap justify-content-center align-items-center gap-2 no-print"
             style="max-width: 450px;">

            <a href="{{ url('/') }}"
               class="btn btn-outline-dark rounded-3 fw-bold shadow-sm gap-2 px-3 py-2 w-100 text-center">
                <i class="bi bi-house-door"></i> Kembali ke Menu Utama
            </a>
        </div>
    @endif

    <div id="receipt-content" class="receipt-container p-4 p-md-5 mt-2">
        <div class="text-center mb-4">
            @if($store && $store->logo)
                <img src="{{ Storage::url($store->logo) }}" alt="Logo" class="mb-3"
                     style="max-height: 55px; object-fit: contain;">
            @else
                <div
                    class="bg-dark text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm"
                    style="width: 55px; height: 55px; font-size: 1.5rem; font-weight: bold;">
                    {{ substr($store->name ?? 'T', 0, 1) }}
                </div>
            @endif

            <h3 class="fw-bolder text-dark mb-1" style="letter-spacing: -0.5px;">{{ $store->name ?? 'Nama Toko' }}</h3>
            @if($store && $store->address)
                <p class="text-muted small mb-0">{{ $store->address }}</p>
            @endif
            @if($store && $store->whatsapp_number)
                <p class="text-muted small mb-0">WA: {{ $store->whatsapp_number }}</p>
            @endif
        </div>

        <div class="dashed-border py-3 mb-3">
            <h6 class="text-center fw-bold text-muted text-uppercase mb-0" style="letter-spacing: 3px;">E-Receipt</h6>
        </div>

        <div class="mb-4 small">
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">No. Pesanan</span>
                <span class="fw-bold text-dark receipt-monospace">{{ $order->invoice_code }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Tanggal</span>
                <span class="fw-bold text-dark">{{ $order->created_at->format('d M Y, H:i') }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Tipe Pesanan</span>
                <span class="fw-bold text-dark text-uppercase">{{ $order->order_type }}
                    @if($order->table_number)
                        <span class="badge bg-dark ms-1">Meja: {{ $order->table_number }}</span>
                    @endif
                </span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Pelanggan</span>
                <span class="fw-bold text-dark">{{ $order->customer_name }}</span>
            </div>
        </div>

        <div class="dashed-border pt-3 mb-3 receipt-monospace" style="font-size: 0.85rem;">
            <div class="d-flex justify-content-between text-muted small fw-bold mb-3 pb-2 border-bottom"
                 style="font-size: 0.8rem;">
                <div style="width: 50%;">Item</div>
                <div class="text-center" style="width: 20%;">Qty</div>
                <div class="text-end" style="width: 30%;">Subtotal</div>
            </div>

            @foreach($order->items as $item)
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-start text-dark">
                        <div style="width: 50%; padding-right: 5px;">
                            <div class="fw-bold" style="font-size: 0.9rem;">{{ $item->product_name }}</div>
                            @if($item->variant_name)
                                <div class="text-muted" style="font-size: 0.75rem;">- {{ $item->variant_name }}</div>
                            @endif
                            @if($item->discount > 0)
                                <div class="text-danger small" style="font-size: 0.75rem;">Diskon:
                                    -Rp {{ number_format($item->discount, 0, ',', '.') }}</div>
                            @endif
                        </div>
                        <div class="text-center" style="width: 20%;">
                            <span class="fw-bold">{{ $item->quantity }}x</span>
                            <div class="text-muted" style="font-size: 0.75rem;">@
                                Rp {{ number_format($item->price, 0, ',', '.') }}</div>
                        </div>
                        <div class="text-end fw-bold" style="width: 30%; font-size: 0.9rem;">
                            @if($item->discount > 0)
                                <span class="text-muted text-decoration-line-through d-block fw-normal"
                                      style="font-size: 0.75rem;">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                            @endif
                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                        </div>
                    </div>
                    @if($item->note)
                        <div class="text-muted mt-1" style="font-size: 0.75rem; font-style: italic;">
                            * {{ $item->note }}</div>
                    @endif
                </div>
            @endforeach
        </div>

        @php
            $totalItemDiscount = $order->items->sum('discount');
            $extraDiscount = max(0, $order->discount - $totalItemDiscount);
        @endphp
        <div class="dashed-border pt-3 mb-4 receipt-monospace" style="font-size: 0.85rem;">
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Subtotal</span>
                <span class="fw-bold text-dark">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
            </div>
            @if($totalItemDiscount > 0)
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Diskon Item</span>
                    <span class="fw-bold text-danger">- Rp {{ number_format($totalItemDiscount, 0, ',', '.') }}</span>
                </div>
            @endif
            @if($extraDiscount > 0)
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Diskon Ekstra</span>
                    <span class="fw-bold text-danger">- Rp {{ number_format($extraDiscount, 0, ',', '.') }}</span>
                </div>
            @endif
            @if(($order->service_charge_amount ?? 0) > 0)
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Biaya Layanan ({{ number_format($order->service_charge_percentage ?? 5, 0) }}%)</span>
                    <span
                        class="fw-bold text-dark">Rp {{ number_format($order->service_charge_amount, 0, ',', '.') }}</span>
                </div>
            @endif
            @if(($order->tax_amount ?? 0) > 0)
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Pajak PB1 ({{ number_format($order->tax_percentage ?? 10, 0) }}%)</span>
                    <span class="fw-bold text-dark">Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="d-flex justify-content-between mt-3 pt-3 border-top border-2">
                <span class="fw-bolder text-dark fs-5">TOTAL</span>
                <span class="fw-bolder text-dark fs-5">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="bg-light p-3 rounded-2 small mb-4 border payment-box">
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Metode Pembayaran</span>
                <span class="fw-bold text-dark text-uppercase">
                    @if($order->duitku_payment_method)
                        {{ $this->getPaymentMethodDetails()['name'] }}
                    @else
                        {{ $order->formatted_payment_method }}
                    @endif
                </span>
            </div>
            @if($order->payment_method == 'cash')
                <div class="d-flex justify-content-between mb-2 receipt-monospace" style="font-size: 0.85rem;">
                    <span class="text-muted">Tunai Diterima</span>
                    <span class="fw-bold text-dark">Rp {{ number_format($order->amount_paid, 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between receipt-monospace" style="font-size: 0.85rem;">
                    <span class="text-muted">Kembalian</span>
                    <span class="fw-bold text-dark">Rp {{ number_format($order->change_amount, 0, ',', '.') }}</span>
                </div>
            @endif

            @if($order->status == 'paid' || $order->status == 'completed')
                <div class="status-stamp stamp-paid">LUNAS</div>
            @else
                <div class="status-stamp stamp-unpaid text-uppercase">{{ $order->status }}</div>
            @endif
        </div>

        <div class="text-center mt-4 pt-3 dashed-border">
            <div class="text-center mb-3">
                <svg x-init="JsBarcode($el, '{{ $order->invoice_code }}', {
                    format: 'CODE128',
                    lineColor: '#111827',
                    width: 1.5,
                    height: 40,
                    displayValue: false,
                    margin: 0
                })"></svg>
            </div>
            <p class="fw-bold text-dark mb-1">Terima Kasih!</p>
            <p class="text-muted" style="font-size: 0.8rem;">Struk ini adalah bukti pembayaran yang sah.<br>Harap
                disimpan dengan baik.</p>
        </div>
    </div>
</div>

@assets
<style>
    .receipt-container {
        max-width: 420px;
        margin: 0 auto;
        background: #fff;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
        border-radius: 12px 12px 0 0;
        position: relative;
        padding-bottom: 25px;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .receipt-container::after {
        content: "";
        position: absolute;
        bottom: -10px;
        left: 0;
        right: 0;
        height: 10px;
        background-size: 20px 20px;
        background-repeat: repeat-x;
        background-image: linear-gradient(135deg, #fff 25%, transparent 25%),
        linear-gradient(225deg, #fff 25%, transparent 25%);
        background-position: 0 0;
    }

    .dashed-border {
        border-top: 2px dashed #cbd5e1;
    }

    .receipt-monospace {
        font-family: 'Courier Prime', 'Courier New', Courier, monospace;
    }

    .payment-box {
        position: relative;
        overflow: hidden;
        border: 1px solid #e2e8f0 !important;
        background-color: #f8fafc !important;
    }

    .status-stamp {
        position: absolute;
        top: 50%;
        right: 15px;
        font-size: 1.5rem;
        font-weight: 800;
        text-transform: uppercase;
        border: 3px solid;
        border-radius: 8px;
        padding: 4px 12px;
        transform: translateY(-50%) rotate(-12deg);
        opacity: 0.15;
        pointer-events: none;
        z-index: 10;
        letter-spacing: 2px;
    }

    .stamp-paid {
        color: #16a34a;
        border-color: #16a34a;
    }

    .stamp-unpaid {
        color: #dc2626;
        border-color: #dc2626;
    }

    @media print {
        .receipt-container {
            box-shadow: none !important;
            border: none !important;
            border-radius: 0 !important;
            padding: 0 !important;
            margin: 0 !important;
            max-width: 100% !important;
            width: 100% !important;
        }

        .receipt-container::after {
            display: none !important;
        }

        .no-print {
            display: none !important;
        }

        body {
            background-color: #fff !important;
        }
    }

    /* Premium Custom Duitku UI Styles */
    .payment-instruction-container {
        max-width: 420px;
        margin: 0 auto;
        background: #fff;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(0, 0, 0, 0.06);
    }

    .custom-toast {
        transition: opacity 0.3s ease;
        background-color: #111827 !important;
        border: 1px solid rgba(255, 255, 255, 0.1);
        letter-spacing: 0.5px;
    }

    .animate-pulse {
        animation: pulse-animation 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    @keyframes pulse-animation {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: .5;
        }
    }

    .accordion-button:focus {
        box-shadow: none !important;
    }

    .accordion-button:not(.collapsed) {
        color: #111827 !important;
        background-color: #f8fafc !important;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.0/dist/JsBarcode.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
    function downloadReceipt() {
        const receipt = document.getElementById('receipt-content');
        const originalBoxShadow = receipt.style.boxShadow;
        const originalBorderRadius = receipt.style.borderRadius;

        receipt.style.boxShadow = 'none';
        receipt.style.borderRadius = '0px';

        html2canvas(receipt, {
            scale: 2,
            backgroundColor: '#ffffff',
            useCORS: true
        }).then(canvas => {
            receipt.style.boxShadow = originalBoxShadow;
            receipt.style.borderRadius = originalBorderRadius;

            let link = document.createElement('a');
            link.download = 'Invoice-{{ $order->invoice_code }}.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
        });
    }

    function copyToClipboard(text, successMessage) {
        navigator.clipboard.writeText(text).then(function () {
            const toast = document.getElementById('custom-toast');
            toast.textContent = successMessage;
            toast.style.display = 'block';
            toast.style.opacity = '1';
            setTimeout(function () {
                toast.style.opacity = '0';
                setTimeout(function () {
                    toast.style.display = 'none';
                }, 300);
            }, 2000);
        }).catch(function (err) {
            console.error('Gagal menyalin: ', err);
        });
    }
</script>
@endassets
