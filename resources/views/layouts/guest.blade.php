<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Pakaiapp Enterprise') }}</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <!-- Fonts: Plus Jakarta Sans (Enterprise Standard) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
          rel="stylesheet">

    @include('components.reverb-config')

    <!-- Scripts (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="antialiased font-sans text-gray-900 bg-white dark:bg-gray-950 selection:bg-brand-accent/30 transition-colors duration-300">
<div class="min-h-screen flex w-full">

    <!-- LEFT PANEL: Premium Showcase using 60-30-10 Rule (Hidden on Mobile) -->
    @php
        $storeSettings = \App\Tenant\Models\Core\StoreSetting::first();
    @endphp
    <div
        class="hidden lg:flex lg:w-1/2 relative bg-[#090D16] overflow-hidden items-center justify-center border-r border-gray-200/5 dark:border-gray-800/10">

        <!-- Content Container -->
        <div class="relative z-10 p-16 xl:p-24 max-w-2xl flex flex-col justify-between h-full w-full">
            <!-- Top Brand Logo -->
            <div class="flex items-center gap-3">
                <img src="/android-chrome-192x192.png" alt="Pakaiapp"
                     class="h-10 w-10 object-contain rounded-xl shadow-sm border border-white/5 p-0.5 bg-white">
                <span class="text-xl font-bold font-heading text-white tracking-tight">pakaiapp</span>
            </div>

            <!-- Main Value Proposition & Showcase Features -->
            <div class="my-auto space-y-6">
                <span class="text-xs font-bold uppercase tracking-widest text-brand-accent">Enterprise Dashboard</span>
                <h1 class="text-4xl xl:text-5xl font-extrabold text-white leading-tight font-heading tracking-tight">
                    Platform<br>
                    Sistem Manajemen<br>
                    Toko Anda.
                </h1>
                <p class="text-slate-400 text-sm xl:text-base leading-relaxed max-w-sm mt-6">
                    Kelola kasir, pantau performa penjualan, dan kembangkan bisnis dengan teknologi terdepan dalam satu
                    genggaman.
                </p>
            </div>

            <!-- Bottom Copyright & Links -->
            <div class="flex items-center gap-5 text-xs font-semibold text-gray-500">
                <span>&copy; {{ date('Y') }} ngopikode</span>
                <span class="w-1.5 h-1.5 rounded-full bg-gray-800"></span>
                <a href="#" class="hover:text-gray-300 transition-colors">Privacy</a>
                <span class="w-1.5 h-1.5 rounded-full bg-gray-800"></span>
                <a href="#" class="hover:text-gray-300 transition-colors">Terms of Service</a>
            </div>
        </div>
    </div>

    <!-- RIGHT PANEL: Authentication Form -->
    <div
        class="w-full lg:w-1/2 flex flex-col items-center justify-center p-6 sm:p-12 relative bg-white dark:bg-gray-950">

        <!-- Mobile Header Logo (visible only on mobile) -->
        <div class="lg:hidden absolute top-6 left-6 right-6 flex items-center gap-3">
            @if($storeSettings && $storeSettings->logo)
                <img src="{{ Storage::url($storeSettings->logo) }}" alt="{{ $storeSettings->name }}"
                     class="w-8 h-8 rounded-lg shadow-sm border border-gray-100 dark:border-gray-800">
                <span
                    class="text-xl font-bold font-heading text-gray-900 dark:text-white tracking-tight">{{ $storeSettings->name }}</span>
            @else
                <img src="/android-chrome-192x192.png" alt="Logo"
                     class="w-8 h-8 rounded-lg shadow-sm border border-gray-100 dark:border-gray-800">
                <span
                    class="text-xl font-bold font-heading text-gray-900 dark:text-white tracking-tight">pakaiapp</span>
            @endif
        </div>

        <!-- Main Content Slot (Form blends into background, no card) -->
        <div
            class="w-full max-w-[400px] relative z-10 mt-12 lg:mt-0 animate-in fade-in slide-in-from-bottom-4 duration-700">
            {{ $slot }}
        </div>

        <!-- Mobile Footer -->
        <div class="lg:hidden absolute bottom-6 text-center w-full text-xs text-gray-500 font-medium">
            &copy; {{ date('Y') }} {{ $storeSettings ? $storeSettings->name : 'ngopikode' }} Enterprise.
        </div>
    </div>

</div>
</body>
</html>
