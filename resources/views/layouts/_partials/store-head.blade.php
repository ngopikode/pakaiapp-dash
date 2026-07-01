<script>
    // Prevent FOUC
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    } else if (localStorage.theme === 'light') {
        document.documentElement.classList.remove('dark');
    } else {
        // Default to dark mode if not specified
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    }
</script>
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
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
@endif

<meta property="og:title" content="{{ $setting->og_title ?: ($storeName . ' | Menu Digital') }}"/>
<meta property="og:description"
      content="{{ $setting->og_description ?: 'Buat menu online untuk usaha Anda.' }}"/>
@if($setting->og_image)
    <meta property="og:image" content="{{ Storage::url($setting->og_image) }}"/>
@else
    <meta property="og:image" content="/apple-touch-icon.png"/>
@endif
<meta property="og:type" content="website"/>

@vite(['resources/css/store.css', 'resources/js/store.js'])
@livewireStyles
@if(config('midtrans.client_key'))
    <script
        src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
        data-client-key="{{ config('midtrans.client_key') }}"></script>
@endif
