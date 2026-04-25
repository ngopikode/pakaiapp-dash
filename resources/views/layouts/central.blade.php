<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    @livewireStyles
</head>
<body>

<div id="global-loader"
     class="d-none justify-content-center align-items-center position-fixed top-0 start-0 w-100 h-100 bg-white"
     style="z-index: 9999; opacity: 0.8;">
    <div class="text-center">
        <div class="spinner-border text-brand" style="width: 3rem; height: 3rem;" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2 text-dark fw-bold">Memproses Data...</p>
    </div>
</div>

<div id="wrapper">

    <div id="page-content-wrapper">

        <main class="container-fluid px-4 py-4">
            {{ $slot }}
        </main>
    </div>
</div>

@livewireScripts
</body>
</html>
