<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Detail Pesanan') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
          rel="stylesheet">

    @vite(['resources/css/store.css'])
    @livewireStyles
</head>

<body
    class="bg-zinc-50 min-h-screen text-zinc-900 font-sans antialiased selection:bg-[var(--primary-color)] selection:text-black pb-10">

{{ $slot }}

@livewireScripts
</body>
</html>
