<div class="py-4 py-md-5 px-3">
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

    <div id="receipt-content" class="receipt-container p-4 p-md-5 mt-2">

        @if($order->status == 'paid' || $order->status == 'completed')
            <div class="status-stamp stamp-paid">LUNAS</div>
        @else
            <div class="status-stamp stamp-unpaid">{{ $order->status }}</div>
        @endif

        <div class="text-center mb-4">
            @if($store && $store->logo)
                <img src="{{ asset('storage/' . $store->logo) }}" alt="Logo" class="mb-3"
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

        <div class="dashed-border pt-3 mb-3 receipt-monospace">
            <div class="d-flex justify-content-between text-muted small fw-bold mb-3 pb-2 border-bottom">
                <div style="width: 50%;">Item</div>
                <div class="text-center" style="width: 15%;">Qty</div>
                <div class="text-end" style="width: 35%;">Subtotal</div>
            </div>

            @foreach($order->items as $item)
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-start text-dark">
                        <div style="width: 50%; padding-right: 10px;">
                            <div class="fw-bold">{{ $item->product_name }}</div>
                            @if($item->variant_name)
                                <div class="text-muted" style="font-size: 0.8rem;">- {{ $item->variant_name }}</div>
                            @endif
                        </div>
                        <div class="text-center" style="width: 15%;">{{ $item->quantity }}</div>
                        <div class="text-end fw-bold"
                             style="width: 35%;">{{ number_format($item->subtotal, 0, ',', '.') }}</div>
                    </div>
                    @if($item->note)
                        <div class="text-muted mt-1" style="font-size: 0.75rem; font-style: italic;">
                            * {{ $item->note }}</div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="dashed-border pt-3 mb-4 receipt-monospace">
            <div class="d-flex justify-content-between mb-2 small">
                <span class="text-muted">Subtotal</span>
                <span class="fw-bold text-dark">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
            </div>
            @if($order->discount > 0)
                <div class="d-flex justify-content-between mb-2 small">
                    <span class="text-muted">Diskon</span>
                    <span class="fw-bold text-danger">- Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="d-flex justify-content-between mt-3 pt-3 border-top border-2">
                <span class="fw-bolder text-dark fs-5">TOTAL</span>
                <span class="fw-bolder text-dark fs-5">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="bg-light p-3 rounded-2 small mb-4 border">
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Metode Pembayaran</span>
                <span class="fw-bold text-dark text-uppercase">{{ $order->payment_method }}</span>
            </div>
            @if($order->payment_method == 'cash')
                <div class="d-flex justify-content-between mb-2 receipt-monospace">
                    <span class="text-muted">Tunai Diterima</span>
                    <span class="fw-bold text-dark">Rp {{ number_format($order->amount_paid, 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between receipt-monospace">
                    <span class="text-muted">Kembalian</span>
                    <span class="fw-bold text-dark">Rp {{ number_format($order->change_amount, 0, ',', '.') }}</span>
                </div>
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
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.0/dist/JsBarcode.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
    function downloadReceipt() {
        const receipt = document.getElementById('receipt-content');

        // Simpan state CSS awal
        const originalBoxShadow = receipt.style.boxShadow;
        const originalBorderRadius = receipt.style.borderRadius;

        // Hapus styling tertentu sementara agar gambar rapi (opsional)
        receipt.style.boxShadow = 'none';
        receipt.style.borderRadius = '0px';

        html2canvas(receipt, {
            scale: 2, // Resolusi tinggi agar tidak pecah
            backgroundColor: '#ffffff',
            useCORS: true // Penting jika ada gambar logo dari domain luar
        }).then(canvas => {
            // Kembalikan state CSS semula
            receipt.style.boxShadow = originalBoxShadow;
            receipt.style.borderRadius = originalBorderRadius;

            // Proses Download
            let link = document.createElement('a');
            link.download = 'Invoice-{{ $order->invoice_code }}.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
        });
    }
</script>
@endassets
