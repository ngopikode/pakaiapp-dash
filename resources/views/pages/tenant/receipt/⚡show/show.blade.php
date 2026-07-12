<div class="receipt-page px-4 py-5 sm:px-5 sm:py-6">
    <div class="no-print mb-4 flex justify-center gap-2">
        <button @click="$wire.markAsPrinted().then(() => setTimeout(() => window.print(), 150))"
                class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition-colors duration-200 hover:bg-slate-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-900/20 motion-reduce:transition-none">
            <i class="ph-bold ph-printer"></i> Cetak
        </button>
        <button type="button" onclick="downloadReceipt()"
                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm transition-colors duration-200 hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-900/10 motion-reduce:transition-none">
            <i class="ph-bold ph-download-simple"></i> Unduh
        </button>
    </div>

    <div id="receipt-content" class="receipt-container p-4 sm:p-5">
        <div class="mb-4 text-center">
            @if($store && $store->logo)
                <div class="mx-auto mb-3 flex items-center justify-center rounded-full border border-slate-200 bg-white shadow-sm" style="width:56px;height:56px">
                    <img src="{{ Storage::url($store->logo) }}" alt="Logo" class="h-[44px] w-[44px] rounded-full object-contain">
                </div>
            @else
                <div class="mx-auto mb-4 flex h-[60px] w-[60px] items-center justify-center rounded-full bg-slate-900 text-2xl font-bold text-white shadow-sm">
                    {{ substr($store->name ?? 'T', 0, 1) }}
                </div>
            @endif

            <h3 class="mb-1 text-[1.3rem] font-extrabold tracking-tight text-slate-900">{{ $store->name ?? 'Nama Toko' }}</h3>
            @if($store && $store->address)
                <p class="text-xs text-slate-500">{{ $store->address }}</p>
            @endif
            @if($store && $store->whatsapp_number)
                <p class="text-xs text-slate-500">WA: {{ $store->whatsapp_number }}</p>
            @endif
        </div>

        <div class="dashed-border mb-3 py-3 text-center">
            <h6 class="text-xs font-bold uppercase tracking-[3px] text-slate-500">Struk Pembayaran</h6>
        </div>

        <div class="mb-4 space-y-2.5 text-[0.8rem]">
            <div class="flex items-start justify-between gap-4">
                <span class="text-slate-500">No. Pesanan</span>
                <span class="receipt-monospace text-right font-bold text-slate-900">{{ $order->invoice_code }}</span>
            </div>
            <div class="flex items-start justify-between gap-4">
                <span class="text-slate-500">Tanggal</span>
                <span class="text-right font-bold text-slate-900">{{ $order->created_at->translatedFormat('d F Y, H:i') }} WIB</span>
            </div>
            <div class="flex items-start justify-between gap-4">
                <span class="text-slate-500">Tipe Pesanan</span>
                <span class="text-right font-bold uppercase text-slate-900">
                    {{ $order->order_type }}
                    @if($order->table_number)
                        <span class="ml-1 inline-block rounded bg-slate-900 px-2 py-0.5 text-[0.65rem] font-bold text-white">Meja: {{ $order->table_number }}</span>
                    @endif
                </span>
            </div>
            @if($order->notes)
                <div class="flex justify-between gap-4">
                    <span class="shrink-0 text-slate-500">Catatan</span>
                    <span class="text-right font-bold text-slate-900">{{ $order->notes }}</span>
                </div>
            @endif
            <div class="flex justify-between">
                <span class="text-slate-500">Pelanggan</span>
                <div class="text-right font-bold text-slate-900">
                    {{ $order->customer_name ?? 'Guest' }}
                    @if($order->customer_phone)
                        <div class="font-normal text-slate-500">{{ $order->customer_phone }}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="dashed-border mb-3 pt-3 receipt-monospace text-[0.82rem]">
            <div class="mb-3 grid grid-cols-[1fr_64px_96px] gap-2 border-b border-slate-200 pb-2 text-[0.75rem] font-bold text-slate-500">
                <div>Item</div>
                <div class="text-center">Qty</div>
                <div class="text-right">Subtotal</div>
            </div>

            @foreach($order->items as $item)
                <div class="mb-3">
                    <div class="grid grid-cols-[1fr_64px_96px] items-start gap-2 text-slate-900">
                        <div class="min-w-0 pr-1">
                            <div class="text-[0.85rem] font-bold">{{ $item->product_name }}</div>
                            @if($item->variant_name)
                                <div class="text-[0.72rem] text-slate-500">- {{ $item->variant_name }}</div>
                            @endif
                            @if($item->discount > 0)
                                <div class="text-[0.72rem] text-red-600">Diskon: -Rp {{ number_format($item->discount, 0, ',', '.') }}</div>
                            @endif
                        </div>
                        <div class="text-center">
                            <span class="font-bold">{{ $item->quantity }}x</span>
                            <div class="text-[0.72rem] text-slate-500">@ Rp {{ number_format($item->price, 0, ',', '.') }}</div>
                        </div>
                        <div class="text-right text-[0.85rem] font-bold">
                            @if($item->discount > 0)
                                <div class="block text-[0.72rem] font-normal text-slate-400 line-through">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</div>
                            @endif
                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                        </div>
                    </div>
                    @if($item->note)
                        <div class="mt-1 text-[0.72rem] italic text-slate-500">* {{ $item->note }}</div>
                    @endif
                </div>
            @endforeach
        </div>

        @php
            $totalItemDiscount = $order->items->sum('discount');
            $extraDiscount = max(0, $order->discount - $totalItemDiscount);
        @endphp
        <div class="dashed-border mb-4 pt-3 receipt-monospace text-[0.82rem]">
            <div class="mb-2 flex justify-between">
                <span class="text-slate-500">Subtotal</span>
                <span class="font-bold text-slate-900">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
            </div>
            @if($totalItemDiscount > 0)
                <div class="mb-2 flex justify-between">
                    <span class="text-slate-500">Diskon Item</span>
                    <span class="font-bold text-red-600">- Rp {{ number_format($totalItemDiscount, 0, ',', '.') }}</span>
                </div>
            @endif
            @if($extraDiscount > 0)
                <div class="mb-2 flex justify-between">
                    <span class="text-slate-500">Diskon Ekstra</span>
                    <span class="font-bold text-red-600">- Rp {{ number_format($extraDiscount, 0, ',', '.') }}</span>
                </div>
            @endif
            @if(($order->service_charge_amount ?? 0) > 0)
                <div class="mb-2 flex justify-between">
                    <span class="text-slate-500">Biaya Layanan ({{ number_format($order->service_charge_percentage ?? 5, 0) }}%)</span>
                    <span class="font-bold text-slate-900">Rp {{ number_format($order->service_charge_amount, 0, ',', '.') }}</span>
                </div>
            @endif
            @if(($order->tax_amount ?? 0) > 0)
                <div class="mb-2 flex justify-between">
                    <span class="text-slate-500">Pajak PB1 ({{ number_format($order->tax_percentage ?? 10, 0) }}%)</span>
                    <span class="font-bold text-slate-900">Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="mt-3 flex justify-between border-t-2 border-slate-200 pt-3">
                <span class="text-lg font-extrabold text-slate-900">TOTAL</span>
                <span class="text-lg font-extrabold text-slate-900">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="payment-box relative mb-4 overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 p-3">
            <div class="mb-2 flex justify-between">
                <span class="text-slate-500">Metode Pembayaran</span>
                <span class="font-bold uppercase text-slate-900">{{ $order->formatted_payment_method ?? $order->payment_method }}</span>
            </div>
            @if($order->payment_method == 'cash')
                <div class="mb-2 flex justify-between receipt-monospace text-[0.82rem]">
                    <span class="text-slate-500">Tunai Diterima</span>
                    <span class="font-bold text-slate-900">Rp {{ number_format($order->amount_paid, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between receipt-monospace text-[0.82rem]">
                    <span class="text-slate-500">Kembalian</span>
                    <span class="font-bold text-slate-900">Rp {{ number_format($order->change_amount, 0, ',', '.') }}</span>
                </div>
            @endif

            @if(in_array($order->status, ['paid', 'progress', 'completed']))
                <div class="status-stamp stamp-paid">LUNAS</div>
            @else
                <div class="status-stamp stamp-unpaid uppercase">BELUM LUNAS</div>
            @endif
        </div>

        <div class="dashed-border mt-4 pt-3 text-center">
            <img id="receipt-qrcode"
                 src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode(url('/receipt/' . $order->invoice_code)) }}&bgcolor=ffffff&color=111827&margin=0"
                 alt="QR Code"
                 class="mx-auto mb-2 inline-block rounded-2xl border border-slate-200 bg-white p-2"
                 style="width: 82px; height: 82px; object-fit: contain;">
            <div class="mb-2 text-[0.7rem] tracking-[1px] text-slate-500">{{ $order->invoice_code }}</div>
            <p class="mb-1 text-sm font-bold text-slate-900">Terima Kasih!</p>
            <p class="text-[0.78rem] leading-snug text-slate-500">Struk ini adalah bukti pembayaran yang sah.<br>Harap disimpan dengan baik.</p>
        </div>
    </div>
</div>

@assets
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
    function downloadReceipt() {
        const receipt = document.getElementById('receipt-content');
        if (!receipt) return;

        const originalBoxShadow = receipt.style.boxShadow;
        const originalBorderRadius = receipt.style.borderRadius;

        receipt.style.boxShadow = 'none';
        receipt.style.borderRadius = '0px';

        html2canvas(receipt, {
            scale: 2,
            backgroundColor: '#ffffff',
            useCORS: true,
        }).then((canvas) => {
            receipt.style.boxShadow = originalBoxShadow;
            receipt.style.borderRadius = originalBorderRadius;

            const link = document.createElement('a');
            link.download = 'Receipt-{{ $order->invoice_code }}.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
        });
    }
</script>
@endassets
