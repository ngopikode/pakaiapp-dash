<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="X-Frame-Options" content="DENY">
    <title>Status Pembayaran — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/payment-return.css') }}">
</head>
<body>
    <div class="card">
        @php
            $status = $order?->status ?? 'unknown';
            $isPaid = $status === 'paid';
            $isCancelled = $status === 'cancelled';
        @endphp

        {{-- Icon --}}
        <div class="icon-circle {{ $isPaid ? 'success' : ($isCancelled ? 'failed' : 'pending') }}">
            @if ($isPaid)
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <path d="m9 11 3 3L22 4"/>
                </svg>
            @elseif ($isCancelled)
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="m15 9-6 6"/>
                    <path d="m9 9 6 6"/>
                </svg>
            @else
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#eab308" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
            @endif
        </div>

        {{-- Title & Subtitle --}}
        @if ($isPaid)
            <h1>Pembayaran Berhasil! 🎉</h1>
            <p class="subtitle">Terima kasih! Pesananmu sudah kami terima dan sedang diproses.</p>
        @elseif ($isCancelled)
            <h1>Pembayaran Gagal</h1>
            <p class="subtitle">Maaf, pembayaran tidak berhasil diproses. Silakan coba lagi atau pilih metode pembayaran lain.</p>
        @elseif ($order)
            <h1>Menunggu Pembayaran</h1>
            <p class="subtitle">Pesananmu sudah dibuat. Selesaikan pembayaran untuk melanjutkan.</p>
        @else
            <h1>Transaksi Selesai</h1>
            <p class="subtitle">Terima kasih telah berbelanja!</p>
        @endif

        {{-- Invoice Info --}}
        @if ($order)
            @if(! $isPaid && ! $isCancelled && config('duitku.sandbox'))
                <div style="background-color: #fffbeb; border-left: 4px solid #f59e0b; border-radius: 0.75rem; padding: 0.75rem 1rem; margin-bottom: 1.25rem; text-align: left; display: flex; gap: 0.5rem; align-items: start;">
                    <span style="color: #f59e0b; font-size: 1.1rem; line-height: 1; flex-shrink: 0; margin-top: 1px;">⚠️</span>
                    <div>
                        <h6 style="font-weight: 700; font-size: 0.75rem; color: #78350f; margin-bottom: 0.15rem;">Mode Uji Coba (Sandbox)</h6>
                        <p style="font-size: 0.68rem; color: #92400e; line-height: 1.35; margin: 0;">
                            Website ini sedang dalam tahap uji coba pembayaran. Jangan gunakan kartu kredit atau rekening asli.
                        </p>
                    </div>
                </div>
            @endif

            <div class="invoice-box">
                <div style="text-align: left;">
                    <span class="invoice-label">Kode Invoice</span>
                    {{-- Escape output untuk mencegah XSS --}}
                    <span class="invoice-code">{{ e($order->invoice_code) }}</span>
                </div>
                <span class="status-badge {{ e($status) }}">
                    @if ($isPaid) ✓ Lunas
                    @elseif ($isCancelled) ✗ Gagal
                    @else ⏳ Pending
                    @endif
                </span>
            </div>

            {{-- Payment URL jika masih pending --}}
            @if (! $isPaid && ! $isCancelled && $order->duitku_payment_url)
                <a href="{{ e($order->duitku_payment_url) }}" class="btn btn-primary" id="btn-pay-now">
                    Bayar Sekarang
                </a>
            @endif
        @endif

        <hr class="divider">

        {{-- Actions --}}
        <a href="{{ url('/') }}" class="btn btn-secondary" id="btn-back-menu">
            Kembali ke Menu
        </a>

        <p class="brand">Powered by PakaiApp · Duitku</p>
    </div>
</body>
</html>
