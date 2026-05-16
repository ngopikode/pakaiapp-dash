<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>EzMenu | Pesan Menu Ez Banget</title>
    <meta name="description"
          content="Platform menu digital untuk semua jenis usaha kuliner. Buat menu online dengan mudah, cepat, dan modern."/>
    <meta name="keywords" content="EzMenu, menu digital, QR menu, pesan online, SaaS kuliner, restoran online"/>
    <meta name="theme-color" content="#18181b">

    <!-- Default Open Graph Preview untuk brand EzMenu -->
    <meta property="og:title" content="EzMenu | Pesan Menu Ez Banget"/>
    <meta property="og:description" content="Buat menu online untuk restoran Anda dengan mudah, cepat, dan modern."/>
    <meta property="og:image" content="/logo.png"/>
    <meta property="og:type" content="website"/>

    @vite(['resources/css/store.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body>
<div
    class="bg-zinc-50 min-h-screen text-zinc-900 pb-28 font-sans antialiased relative selection:bg-[var(--primary-color)] selection:text-black"
    {{--    onTouchStart={handleTouchStart}--}}
    {{--    onTouchMove={handleTouchMove}--}}
    {{--    onTouchEnd={handleTouchEnd}--}}
>
    <livewire:layouts.store.navbar/>

    {{ $slot }}
</div>

@livewireScripts
</body>
</html>
