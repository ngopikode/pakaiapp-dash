@php use App\Tenant\Models\Core\StoreSetting; @endphp

    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Reverb Dynamic Configurations -->
    <meta name="reverb-app-key" content="{{ config('broadcasting.connections.reverb.client_options.key') }}">
    <meta name="reverb-port" content="{{ config('broadcasting.connections.reverb.client_options.port') }}">
    <meta name="reverb-scheme" content="{{ config('broadcasting.connections.reverb.client_options.scheme') }}">

    <title>{{ isset($title) ? $title . ' - ' : '' }}{{ StoreSetting::value('navbar_brand_text') ?? config('app.name') }}</title>

    <script>
        const theme = localStorage.getItem('theme') || 'light';
        if (theme === 'dark') document.documentElement.classList.add('dark');
    </script>

    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="manifest" href="/manifest.json">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')

    @livewireStyles

    @if(config('midtrans.server_key'))
        <script
            src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
            data-client-key="{{ config('midtrans.client_key') }}"></script>
    @endif
</head>
<body
    class="bg-slate-50 dark:bg-[#0B1120] text-slate-800 dark:text-slate-200 antialiased font-sans selection:bg-orange-500/20 selection:text-orange-600 dark:selection:text-orange-400 flex flex-col min-h-screen"
    x-data="{ showDesktopSidebar: window.innerWidth >= 1280 && localStorage.getItem('sb|sidebar-toggle') !== 'false' }"
    x-init="$watch('showDesktopSidebar', value => localStorage.setItem('sb|sidebar-toggle', value))">

<div id="global-loader"
     class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-50/90 dark:bg-[#0B1120]/90 backdrop-blur-md transition-all duration-500">
    <div class="flex flex-col items-center gap-5">
        <div class="relative w-14 h-14 flex items-center justify-center">
            <div class="absolute inset-0 rounded-full border-[3px] border-slate-200 dark:border-slate-800"></div>
            <div
                class="absolute inset-0 rounded-full border-[3px] border-orange-500 border-t-transparent border-r-transparent animate-spin"
                style="animation-duration: 0.8s;"></div>
            <i class="ph-fill ph-circle-notch text-orange-500/30 text-2xl animate-pulse"></i>
        </div>
        <span class="text-sm font-bold tracking-widest uppercase text-slate-500 dark:text-slate-400">Memuat...</span>
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

<div id="wrapper" class="flex flex-1 w-full overflow-x-hidden relative">
    @if($showSidebar)
        {{-- HANYA DI-RENDER DI DESKTOP --}}
        <div
            class="hidden xl:flex flex-col h-full shrink-0 border-r border-border bg-white/50 dark:bg-slate-900/50 backdrop-blur-xl z-20 transition-all duration-300 ease-in-out"
            :class="showDesktopSidebar ? 'w-64 opacity-100' : 'w-0 opacity-0 overflow-hidden'">
            <div class="w-64 flex flex-col h-full">
                <livewire:layouts.sidebar elementId="sidebar-wrapper"/>
            </div>
        </div>

        {{-- HANYA DI-RENDER DI MOBILE (ALPINE DRAWER) --}}
        <div x-data="{ open: false }"
             @open-mobile-sidebar.window="open=true"
             @close-mobile-sidebar.window="open=false">
            <!-- Overlay -->
            <div x-show="open" x-transition.opacity
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40 xl:hidden"
                 @click="open=false"></div>
            <!-- Drawer -->
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="-translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="-translate-x-full"
                 class="fixed inset-y-0 left-0 w-[280px] bg-white dark:bg-slate-900 z-50 xl:hidden shadow-2xl flex flex-col border-r border-border">
                <div
                    class="flex items-center justify-between px-6 py-5 border-b border-border bg-slate-50/50 dark:bg-slate-800/50">
                    <h5 class="m-0 font-serif font-extrabold text-[17px] tracking-tight text-slate-800 dark:text-white flex items-center gap-2.5">
                        <div
                            class="w-8 h-8 rounded-lg bg-gradient-to-tr from-orange-500 to-orange-400 flex items-center justify-center text-white shadow-sm shrink-0">
                            <i class="ph-bold ph-storefront text-[18px]"></i>
                        </div>
                        {{ StoreSetting::value('navbar_brand_text') ?? 'Navigasi Toko' }}
                    </h5>
                    <button type="button" @click="open=false"
                            class="p-2 -mr-2 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 rounded-full hover:bg-slate-200/50 dark:hover:bg-slate-700/50 transition-colors focus:outline-none">
                        <i class="ph-bold ph-x text-lg"></i>
                    </button>
                </div>
                <div class="flex-1 overflow-y-auto overflow-x-hidden p-0 bg-white dark:bg-slate-900">
                    <livewire:layouts.sidebar elementId="mobile-sidebar-wrapper"/>
                </div>
            </div>
        </div>
    @endif

    <div id="page-content-wrapper" class="flex-1 flex flex-col w-0 min-w-0 transition-[margin] duration-300 ease-in-out"
         @if(!$showSidebar) style="margin-left: 0 !important; padding-top: 0 !important;" @endif>
        @if($showSidebar)
            <livewire:layouts.navbar :header="$navbar ?? ($title ?? null)"/>
        @endif

        <main
            class="w-full @if($showSidebar) md:p-6 @else @endif @if(is_array($navbar ?? null) && ($navbar['mode'] ?? null) === 'pos') !p-0 overflow-hidden @endif">
            {{ $slot }}
        </main>
    </div>


</div>

@livewireScripts

@stack('scripts')

@include('components.pwa-toast')

</body>
</html>
