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
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: #f4f4f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .card {
            background: #fff;
            border-radius: 1.5rem;
            padding: 2.5rem 2rem;
            max-width: 420px;
            width: 100%;
            text-align: center;
            box-shadow: 0 4px 32px rgba(0,0,0,0.08);
        }

        .icon-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        .icon-circle.success { background: #ecfdf5; }
        .icon-circle.pending { background: #fefce8; }
        .icon-circle.failed  { background: #fef2f2; }

        .icon-circle svg { width: 40px; height: 40px; }

        h1 {
            font-size: 1.375rem;
            font-weight: 900;
            color: #18181b;
            margin-bottom: 0.5rem;
        }

        p.subtitle {
            font-size: 0.875rem;
            color: #71717a;
            font-weight: 500;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .invoice-box {
            background: #f4f4f5;
            border-radius: 1rem;
            padding: 1rem 1.25rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .invoice-label {
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #a1a1aa;
            display: block;
            margin-bottom: 0.25rem;
        }

        .invoice-code {
            font-size: 0.9rem;
            font-weight: 900;
            font-family: monospace;
            color: #18181b;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.3rem 0.75rem;
            border-radius: 99px;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .status-badge.paid    { background: #ecfdf5; color: #059669; }
        .status-badge.pending { background: #fefce8; color: #ca8a04; }
        .status-badge.cancelled { background: #fef2f2; color: #dc2626; }

        .btn {
            display: block;
            width: 100%;
            padding: 0.875rem;
            border-radius: 0.875rem;
            font-weight: 800;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.15s ease;
            margin-bottom: 0.625rem;
        }
        .btn:last-child { margin-bottom: 0; }
        .btn:active { transform: scale(0.98); }

        .btn-primary { background: #18181b; color: #fff; }
        .btn-primary:hover { background: #27272a; }

        .btn-secondary { background: #f4f4f5; color: #52525b; }
        .btn-secondary:hover { background: #e4e4e7; }

        .divider {
            border: none;
            border-top: 1px solid #f4f4f5;
            margin: 1.25rem 0;
        }

        .brand {
            font-size: 0.7rem;
            color: #a1a1aa;
            font-weight: 600;
            margin-top: 1.5rem;
        }
    </style>
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
