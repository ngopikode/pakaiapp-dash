<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Cetak Struk - {{ config('app.name', 'EzMenu Enterprise') }}</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Courier+Prime:wght@400;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    @vite(['resources/sass/app.scss'])

    <style>
        body {
            background-color: #f3f4f6;
            font-family: 'Inter', sans-serif;
        }

        @media print {
            @page {
                margin: 0; /* Wajib: Menghilangkan margin/header/footer bawaan browser */
            }

            html, body {
                background-color: white !important;
                margin: 0 !important;
                padding: 0 !important;
                height: auto !important; /* Jangan paksa setinggi layar */
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            /* Reset semua class margin/padding/flex dari body saat diprint */
            body.antialiased {
                display: block !important;
                min-height: 0 !important;
            }

            .print-container {
                box-shadow: none !important;
                border-radius: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 80mm !important; /* Sesuaikan dengan ukuran thermal kamu (80mm/58mm) */
                max-width: 80mm !important;
            }

            /* Sembunyikan elemen yang tidak perlu diprint */
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body class="antialiased">

<div class="print-container">
    {{ $slot }}
</div>

<div class="text-center mt-4 mb-5 no-print">
    <p class="small text-muted mb-0 opacity-75" style="font-size: 0.75rem;">
        {{ config('app.name', 'EzMenu Enterprise') }}
        <span class="mx-1">&bull;</span>
        Powered by &copy; {{ date('Y') }} ngopikode.
    </p>
</div>
</body>
</html>
