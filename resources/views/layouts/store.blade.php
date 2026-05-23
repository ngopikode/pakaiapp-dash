@php
    use App\Models\StoreSetting;
    use Illuminate\Support\Facades\Storage;

    $setting = StoreSetting::where('is_active', true)->first();

    if ($setting) {
        $waNumber = preg_replace('/\D/', '', $setting->whatsapp_number ?: '6281234567890');
        if (str_starts_with($waNumber, '0')) $waNumber = '62' . substr($waNumber, 1);
        $storeName = $setting->name ?: 'EzMenu';
        $storeType = $setting->store_type ?: 'resto';

        $orderTypes = [];
        if ($storeType === 'resto') {
            if ($setting->is_dinein_active)   $orderTypes[] = ['id' => 'dinein',   'label' => 'Makan Sini'];
            if ($setting->is_takeaway_active) $orderTypes[] = ['id' => 'takeaway', 'label' => 'Bungkus'];
        } else {
            if ($setting->is_takeaway_active) $orderTypes[] = ['id' => 'takeaway', 'label' => 'Ambil Sendiri'];
        }
        if ($setting->is_delivery_active) $orderTypes[] = ['id' => 'delivery', 'label' => 'Diantar'];
        if (empty($orderTypes))           $orderTypes[] = ['id' => 'takeaway', 'label' => 'Takeaway'];
    }
@endphp

@if (! $setting)
    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Toko Tidak Ditemukan</title>

    <link rel="icon" type="image/png" href="/logo.png">
    <link rel="apple-touch-icon" href="/logo.png">
    @vite(['resources/css/store.css'])
</head>
<body class="bg-zinc-50 min-h-screen flex items-center justify-center p-6">
<div class="text-center max-w-sm">
    <div class="w-16 h-16 rounded-2xl bg-red-50 flex items-center justify-center mb-4 mx-auto">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-red-400">
            <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/>
            <path d="M12 9v4"/>
            <path d="M12 17h.01"/>
        </svg>
    </div>
    <h2 class="text-lg font-black text-zinc-900 mb-1">Toko Tutup Sementara</h2>
    <p class="text-sm text-zinc-400 mb-6 max-w-xs">Toko ini sedang dinonaktifkan sementara oleh pemiliknya.</p>
    <button onclick="window.location.reload()"
            class="bg-zinc-900 text-white px-6 py-3 rounded-xl text-xs font-black uppercase tracking-wider hover:bg-zinc-800 transition-colors active:scale-95">
        Coba Lagi
    </button>
</div>
</body>
</html>
@else
    <!DOCTYPE html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-no-progress-bar>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $setting->seo_title ?: ($storeName . ' | Menu Digital') }}</title>
        <meta name="description"
              content="{{ $setting->seo_description ?: 'Platform menu digital untuk semua jenis usaha.' }}"/>
        <meta name="keywords" content="{{ $setting->seo_keywords ?: 'menu digital, QR menu, pesan online' }}"/>
        <meta name="theme-color" content="{{ $setting->theme_color ?: '#18181b' }}">

        {{-- Favicon / Icon --}}
        @if($setting->logo)
            <link rel="icon" type="image/png" href="{{ Storage::url($setting->logo) }}">
            <link rel="apple-touch-icon" href="{{ Storage::url($setting->logo) }}">
        @else
            <link rel="icon" type="image/png" href="/logo.png">
            <link rel="apple-touch-icon" href="/logo.png">
        @endif

        <meta property="og:title" content="{{ $setting->og_title ?: ($storeName . ' | Menu Digital') }}"/>
        <meta property="og:description"
              content="{{ $setting->og_description ?: 'Buat menu online untuk usaha Anda.' }}"/>
        @if($setting->og_image)
            <meta property="og:image" content="{{ Storage::url($setting->og_image) }}"/>
        @else
            <meta property="og:image" content="/logo.png"/>
        @endif
        <meta property="og:type" content="website"/>

        @vite(['resources/css/store.css', 'resources/js/store.js'])
        @livewireStyles
        @if(config('midtrans.client_key'))
            <script
                src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
                data-client-key="{{ config('midtrans.client_key') }}"></script>
        @endif
    </head>

    <body>

    {{-- ===== PAGE LOADER — OUTSIDE x-data so Alpine errors can NEVER block it ===== --}}
    @include('pages.tenant.store._loader')

    <div
        class="bg-zinc-50 min-h-screen text-zinc-900 pb-28 font-sans antialiased relative selection:bg-[var(--primary-color)] selection:text-black"
        x-data="storeApp"
        data-default-order-type="{{ $orderTypes[0]['id'] }}"
        data-wa-number="{{ $waNumber }}"
        data-duitku-enabled="{{ config('duitku.enabled') ? 1 : 0 }}"
        data-midtrans-enabled="{{ config('midtrans.server_key') ? 1 : 0 }}"
        data-tax-active="{{ $setting->is_tax_active ? 1 : 0 }}"
        data-tax-rate="{{ $setting->tax_rate ?? 10.00 }}"
        data-service-active="{{ $setting->is_service_charge_active ? 1 : 0 }}"
        data-service-rate="{{ $setting->service_charge_rate ?? 5.00 }}"
        @open-qr-modal.window="qrOpen = true"
        @show-toast.window="showToast($event.detail.message)"
        @open-options-modal.window="openOption($event.detail.product)"
        @open-checkout-modal.window="openCheckout()"
        @keydown.escape.window="
        if (historyOpen)     { historyOpen = false; }
        else if (checkoutOpen)    { closeCheckout(); }
        else if (optionOpen) { closeOption(); }
        else if (qrOpen)     { qrOpen = false; }
    "
    >
        {{-- ===== NAVBAR (Pure Blade — zero extra DB query) ===== --}}
        @include('components.layouts.store._navbar', ['setting' => $setting])

        {{-- ===== HERO (Pure Blade — zero extra DB query) ===== --}}
        @include('pages.tenant.store._hero', ['setting' => $setting])

        {{-- ===== PAGE CONTENT (product-list is the only Livewire component) ===== --}}
        {{ $slot }}

        {{-- ===== GLOBAL TOAST ===== --}}
        <div
            class="fixed top-4 left-4 right-4 z-[300] sm:left-1/2 sm:right-auto sm:-translate-x-1/2 sm:top-6 sm:w-auto sm:min-w-[280px] bg-zinc-900 text-white px-5 py-3.5 rounded-2xl sm:rounded-full shadow-2xl shadow-zinc-900/30 transition-all duration-500 ease-out flex items-center justify-center sm:justify-start gap-3 border border-white/5 backdrop-blur-xl pointer-events-none"
            :class="toast.show ? 'translate-y-0 opacity-100 scale-100' : '-translate-y-8 opacity-0 scale-95'"
        >
            <div class="bg-emerald-500 rounded-full p-1 text-white shrink-0 shadow-lg shadow-emerald-500/30">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <path d="m9 11 3 3L22 4"/>
                </svg>
            </div>
            <span class="text-xs font-bold tracking-wide text-left flex-1 break-words" x-text="toast.message"></span>
        </div>

        {{-- ===== QR MODAL ===== --}}
        <div
            x-show="qrOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="qrOpen = false"
            class="fixed inset-0 bg-zinc-900/80 backdrop-blur-sm z-[200] flex items-center justify-center p-6"
            style="display:none"
        >
            <div @click.stop class="bg-white w-full max-w-xs rounded-2xl p-8 text-center relative overflow-hidden">
                <div
                    class="absolute top-0 left-0 w-full h-32 bg-[var(--primary-color)]/10 rounded-b-[50%] -translate-y-1/2"></div>
                <div class="relative z-10">
                    <div
                        class="bg-[var(--primary-color)] w-14 h-14 rounded-2xl rotate-3 flex items-center justify-center mx-auto mb-4 border-4 border-white shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="text-zinc-900">
                            <rect width="5" height="5" x="3" y="3" rx="1"/>
                            <rect width="5" height="5" x="16" y="3" rx="1"/>
                            <rect width="5" height="5" x="3" y="16" rx="1"/>
                            <path d="M21 16h-3a2 2 0 0 0-2 2v3"/>
                            <path d="M21 21v.01"/>
                            <path d="M12 7v3a2 2 0 0 1-2 2H7"/>
                            <path d="M3 12h.01"/>
                            <path d="M12 3h.01"/>
                            <path d="M12 16v.01"/>
                            <path d="M16 12h1"/>
                            <path d="M21 12v.01"/>
                            <path d="M12 21v-1"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-black text-zinc-900 mb-1">SCAN MENU</h2>
                    <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest mb-6">Buka di Ponsel
                        Anda</p>
                    <div class="bg-white p-2 rounded-xl border-2 border-dashed border-zinc-200 mb-6">
                        <img :src="qrUrl" alt="QR Code Menu" class="w-full aspect-square rounded-lg opacity-90"/>
                    </div>
                    <button @click="qrOpen = false"
                            class="w-full bg-zinc-900 text-white py-3.5 rounded-xl text-xs font-black uppercase tracking-wider hover:bg-zinc-800 transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        {{-- ===== OPTION MODAL (100% Client-Side) ===== --}}
        @include('pages.tenant.store.option-modal')

        {{-- ===== CHECKOUT MODAL (100% Client-Side) ===== --}}
        @include('pages.tenant.store.checkout-modal', ['orderTypes' => $orderTypes])

        {{-- ===== RIWAYAT MODAL (100% Client-Side) ===== --}}
        @include('pages.tenant.store.history-modal')
    </div>

    @livewireScripts
    </body>
    </html>
@endif
