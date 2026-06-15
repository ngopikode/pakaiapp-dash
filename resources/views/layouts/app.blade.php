@php use App\Models\StoreSetting; @endphp
    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' - ' : '' }}{{ \App\Models\StoreSetting::value('navbar_brand_text') ?? config('app.name') }}</title>

    <script>
        const theme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-bs-theme', theme);
    </script>

    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="manifest" href="/manifest.json">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    @stack('styles')

    @livewireStyles

    @if(config('midtrans.server_key'))
        <script
            src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
            data-client-key="{{ config('midtrans.client_key') }}"></script>
    @endif

    <script>
        // iPadOS 13+ requests desktop site by default and spoofs User-Agent as Macintosh.
        // We detect touch support on MacIntel to identify iPads, set a cookie, and reload once.
        if (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1 && document.cookie.indexOf('is_ipad=1') === -1) {
            document.cookie = 'is_ipad=1; path=/; max-age=31536000'; // 1 year
            window.location.reload();
        }
    </script>
</head>
<body>

<div id="global-loader" class="global-loader">
    <div class="loader-content">
        <div class="loader-spinner-wrapper">
            <div class="loader-ring"></div>
            <div class="loader-ring"></div>
            <div class="loader-ring"></div>
        </div>
        <span class="loader-text">Memuat Halaman...</span>
    </div>
</div>

<?php
$userMenuRole = auth()->user()?->role ?? 'cashier';
$storeType = StoreSetting::first()?->store_type ?? 'retail';

$allRoles = [
    ['manager'], ['manager'], ['manager'], ['manager', 'cashier'], ['manager', 'cashier'],
    ['manager'], ['manager'], ['manager'], ['manager', 'cashier']
];
if ($storeType === 'resto') {
    $allRoles[] = ['manager', 'kitchen'];
}

$accessibleMenus = collect($allRoles)->filter(fn($roles) => in_array($userMenuRole, $roles))->count();
$showSidebar = $accessibleMenus > 1;
?>

<div id="wrapper">
    @if($showSidebar)
        @if(!($isMobile ?? false))
            {{-- HANYA DI-RENDER DI DESKTOP --}}
            <div class="desktop-sidebar-container h-100">
                <livewire:layouts.sidebar elementId="sidebar-wrapper"/>
            </div>
        @endif
    @endif

    <div id="page-content-wrapper"
         @if(!$showSidebar) style="margin-left: 0 !important; padding-top: 0 !important;" @endif>
        @if($showSidebar && !($isMobile ?? false))
            <livewire:layouts.navbar :header="$title ?? null"/>
        @endif

        <main class="container-fluid @if($showSidebar) p-3 @else @endif">
            {{ $slot }}
        </main>
    </div>

    @if($showSidebar && ($isMobile ?? false))
        {{-- HANYA DI-RENDER DI MOBILE --}}
        <livewire:layouts.bottom-navbar/>
    @endif
</div>

@livewireScripts

@stack('scripts')

@include('components.pwa-toast')

</body>
</html>
