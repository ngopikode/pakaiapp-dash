<div @if($order->status === 'pending') wire:poll.5s="refreshOrder" @endif class="py-4 py-md-5 px-3">
    
    <div class="mx-auto" style="max-width: 480px;">
        
        <div class="mb-4 d-flex align-items-center justify-content-between">
            <h4 class="fw-bolder mb-0 text-dark">Detail Pesanan</h4>
            <span class="badge bg-light text-dark border border-zinc-200 px-3 py-2 rounded-pill font-mono" style="font-size: 0.8rem;">
                {{ $order->invoice_code }}
            </span>
        </div>

        <!-- Banner Status -->
        <div class="bg-white rounded-4 shadow-sm border border-zinc-100 p-4 mb-4 text-center">
            @if($order->status === 'pending')
                <div class="d-inline-flex align-items-center justify-content-center bg-warning-subtle text-warning rounded-circle mb-3 p-3 animate-pulse" style="width: 60px; height: 60px; background-color: #fef3c7 !important; color: #d97706 !important;">
                    <i class="bi bi-hourglass-split fs-3"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Menunggu Pembayaran</h5>
                <p class="text-muted mb-0 text-sm" style="font-size: 0.8rem;">Silakan selesaikan pembayaran untuk memproses pesanan ini.</p>
            @elseif($order->status === 'paid')
                <div class="d-inline-flex align-items-center justify-content-center bg-info-subtle text-info rounded-circle mb-3 p-3" style="width: 60px; height: 60px; background-color: #e0f2fe !important; color: #0284c7 !important;">
                    <i class="bi bi-check-circle-fill fs-3"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Menunggu Disiapkan</h5>
                <p class="text-muted mb-0 text-sm" style="font-size: 0.8rem;">Pembayaran sukses! Pesanan sedang menunggu disiapkan oleh outlet.</p>
            @elseif($order->status === 'progress')
                <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle mb-3 p-3 animate-pulse" style="width: 60px; height: 60px; background-color: #eff6ff !important; color: #2563eb !important;">
                    <i class="bi bi-clock-fill fs-3"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Sedang Diproses</h5>
                <p class="text-muted mb-0 text-sm" style="font-size: 0.8rem;">Pesanan sedang disiapkan. Mohon ditunggu sebentar ya!</p>
            @elseif($order->status === 'completed')
                <div class="d-inline-flex align-items-center justify-content-center bg-success-subtle text-success rounded-circle mb-3 p-3" style="width: 60px; height: 60px; background-color: #f0fdf4 !important; color: #16a34a !important;">
                    <i class="bi bi-bag-check-fill fs-3"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Pesanan Selesai</h5>
                <p class="text-muted mb-0 text-sm" style="font-size: 0.8rem;">Terima kasih telah berbelanja. Selamat menikmati!</p>
            @elseif($order->status === 'cancelled')
                <div class="d-inline-flex align-items-center justify-content-center bg-danger-subtle text-danger rounded-circle mb-3 p-3" style="width: 60px; height: 60px; background-color: #fef2f2 !important; color: #dc2626 !important;">
                    <i class="bi bi-x-circle-fill fs-3"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Pesanan Dibatalkan</h5>
                <p class="text-muted mb-0 text-sm" style="font-size: 0.8rem;">Pesanan ini telah dibatalkan.</p>
            @endif
        </div>

        <!-- Info Pesanan -->
        <div class="bg-white rounded-4 shadow-sm border border-zinc-100 p-4 mb-4">
            <h6 class="fw-bold text-dark mb-3">Informasi Pesanan</h6>
            
            <div class="d-flex justify-content-between mb-2 small">
                <span class="text-muted">Tanggal</span>
                <span class="fw-bold text-dark">{{ $order->created_at->format('d M Y, H:i') }}</span>
            </div>
            
            <div class="d-flex justify-content-between mb-2 small">
                <span class="text-muted">Tipe Pesanan</span>
                <span class="fw-bold text-dark text-uppercase">
                    {{ $order->order_type }}
                    @if($order->table_number)
                        (Meja: {{ $order->table_number }})
                    @endif
                </span>
            </div>
            
            <div class="d-flex justify-content-between mb-2 small">
                <span class="text-muted">Metode Pembayaran</span>
                <span class="fw-bold text-dark text-uppercase">{{ $order->payment_method === 'cash' ? 'Manual/Tunai' : 'Digital/Online' }}</span>
            </div>
            
            @if($order->notes)
            <div class="d-flex justify-content-between mb-2 small">
                <span class="text-muted">Catatan</span>
                <span class="fw-bold text-dark text-end" style="max-width: 60%;">{{ $order->notes }}</span>
            </div>
            @endif
        </div>

        <!-- Daftar Item -->
        <div class="bg-white rounded-4 shadow-sm border border-zinc-100 p-4 mb-4">
            <h6 class="fw-bold text-dark mb-3">Daftar Produk</h6>
            
            @foreach($order->items as $item)
                <div class="d-flex justify-content-between align-items-start mb-3 pb-3 {{ !$loop->last ? 'border-bottom border-zinc-50' : '' }}">
                    <div class="d-flex gap-2">
                        <span class="fw-bold text-dark">{{ $item->quantity }}x</span>
                        <div>
                            <div class="fw-bold text-dark" style="font-size: 0.9rem;">{{ $item->product_name }}</div>
                            @if($item->variant_name)
                                <div class="text-muted" style="font-size: 0.75rem;">- {{ $item->variant_name }}</div>
                            @endif
                            @if($item->note)
                                <div class="text-muted" style="font-size: 0.75rem;">Catatan: {{ $item->note }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="fw-bold text-dark" style="font-size: 0.9rem;">
                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Ringkasan Harga -->
        <div class="bg-white rounded-4 shadow-sm border border-zinc-100 p-4 mb-4">
            <h6 class="fw-bold text-dark mb-3">Ringkasan Pembayaran</h6>
            
            <div class="d-flex justify-content-between mb-2 small">
                <span class="text-muted">Subtotal</span>
                <span class="fw-bold text-dark">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
            </div>
            
            @if(($order->service_charge_amount ?? 0) > 0)
                <div class="d-flex justify-content-between mb-2 small">
                    <span class="text-muted">Biaya Layanan ({{ number_format($order->service_charge_percentage ?? 5, 0) }}%)</span>
                    <span class="fw-bold text-dark">Rp {{ number_format($order->service_charge_amount, 0, ',', '.') }}</span>
                </div>
            @endif
            
            @if(($order->tax_amount ?? 0) > 0)
                <div class="d-flex justify-content-between mb-2 small">
                    <span class="text-muted">Pajak PB1 ({{ number_format($order->tax_percentage ?? 10, 0) }}%)</span>
                    <span class="fw-bold text-dark">Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</span>
                </div>
            @endif
            
            <div class="d-flex justify-content-between mt-3 pt-3 border-top border-2">
                <span class="fw-bolder text-dark">Total Tagihan</span>
                <span class="fw-bolder text-dark fs-5 text-primary">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="d-flex flex-column gap-3">
            @if($this->getWaUrl())
                <a href="{{ $this->getWaUrl() }}" target="_blank" class="btn btn-success rounded-4 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2 py-3 w-100" style="background-color: #25D366; border-color: #25D366;">
                    <i class="bi bi-whatsapp fs-5"></i>
                    <span>Hubungi Resto</span>
                </a>
            @endif
            
            <a href="{{ route('invoice.show', $order->invoice_code) }}" target="_blank" class="btn btn-dark rounded-4 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2 py-3 w-100">
                <i class="bi bi-receipt fs-5"></i>
                <span>Lihat Struk Pesanan</span>
            </a>
            
            <a href="{{ url('/') }}" class="btn btn-outline-secondary rounded-4 fw-bold d-flex align-items-center justify-content-center gap-2 py-3 w-100 mt-2">
                <i class="bi bi-house-door"></i>
                <span>Kembali ke Toko</span>
            </a>
        </div>

    </div>
    
</div>

@assets
<style>
    .animate-pulse {
        animation: pulse-animation 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    @keyframes pulse-animation {
        0%, 100% { opacity: 1; }
        50% { opacity: .5; }
    }
</style>
@endassets
