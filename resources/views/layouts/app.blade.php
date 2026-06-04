<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name') }}</title>

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
    $storeType = \App\Models\StoreSetting::first()?->store_type ?? 'retail';
    
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
        <div class="d-none d-md-flex">
            <livewire:layouts.sidebar elementId="sidebar-wrapper"/>
        </div>

        <div class="offcanvas offcanvas-start d-md-none" tabindex="-1" id="mobileSidebar"
             aria-labelledby="mobileSidebarLabel" style="width: 280px;">
            <div class="offcanvas-header border-bottom">
                <h5 class="offcanvas-title font-serif fw-bold" id="mobileSidebarLabel">{{ config('app.name') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body p-0">
                <livewire:layouts.sidebar elementId="mobile-sidebar-wrapper"/>
            </div>
        </div>
    @endif

    <div id="page-content-wrapper" @if(!$showSidebar) style="margin-left: 0 !important; padding-top: 0 !important;" @endif>
        @if($showSidebar)
            <livewire:layouts.navbar :header="$header ?? null"/>
        @endif

        <main class="container-fluid @if($showSidebar) p-3 @else p-0 @endif">
            {{ $slot }}
        </main>
    </div>

    @if($showSidebar)
        <livewire:layouts.bottom-navbar/>
    @endif
</div>

@livewireScripts

@stack('scripts')

@include('components.pwa-toast')

</body>
</html>
