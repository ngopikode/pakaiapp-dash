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
    <link href="https://fonts.googleapis.com/css2?family=Courier+Prime:wght@400;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])
    @livewireStyles
</head>

<body class="bg-slate-50 font-sans text-slate-950 antialiased print:bg-white">

<div class="mx-auto w-full">
    {{ $slot }}
</div>

<div class="no-print mb-5 mt-4 text-center">
    <p class="mb-0 text-xs text-slate-400">
        {{ config('app.name', 'EzMenu Enterprise') }}
        <span class="mx-1">&bull;</span>
        Powered by &copy; {{ date('Y') }} ngopikode.
    </p>
</div>
@livewireScripts
</body>
</html>
