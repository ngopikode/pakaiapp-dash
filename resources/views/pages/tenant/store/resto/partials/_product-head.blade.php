@php use Illuminate\Support\Facades\Storage; @endphp
<head>
    <script>
        // Prevent FOUC and apply theme
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else if (localStorage.theme === 'light') {
            document.documentElement.classList.remove('dark');
        } else {
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        }
    </script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>:root { --primary-color: {{ $themeColor }}; }</style>

    {{-- Primary SEO --}}
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $ogDesc }}"/>
    <meta name="keywords"
          content="{{ $product->name }}, {{ $product->category?->name ?? 'Menu' }}, {{ $storeName }}, pesan online, menu resto"/>
    <meta name="theme-color" content="{{ $setting?->theme_color ?: '#18181b' }}">

    {{-- Favicon / Icon --}}
    @if($setting?->logo)
        <link rel="icon" type="image/png" href="{{ Storage::url($setting->logo) }}">
        <link rel="apple-touch-icon" href="{{ Storage::url($setting->logo) }}">
    @else
        <link rel="icon" type="image/x-icon" href="/favicon.ico">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    @endif

    <link rel="canonical" href="{{ $canonicalUrl }}"/>

    {{-- Open Graph (WhatsApp / Facebook / Instagram crawler) --}}
    <meta property="og:title" content="{{ $pageTitle }}"/>
    <meta property="og:description" content="{{ $ogDesc }}"/>
    <meta property="og:type" content="product"/>
    <meta property="og:url" content="{{ $canonicalUrl }}"/>
    <meta property="og:site_name" content="{{ $storeName }}"/>

    @if($product->image)
        <meta property="og:image" content="{{ Storage::url($product->image) }}?v={{ $imageVersion }}"/>
        <meta property="og:image:width" content="800"/>
        <meta property="og:image:height" content="600"/>
        <meta property="og:image:alt" content="{{ $product->name }}"/>
    @else
        <meta property="og:image" content="{{ url('/apple-touch-icon.png') }}"/>
    @endif

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image"/>
    <meta name="twitter:title" content="{{ $pageTitle }}"/>
    <meta name="twitter:description" content="{{ $ogDesc }}"/>
    @if($product->image)
        <meta name="twitter:image" content="{{ Storage::url($product->image) }}?v={{ $imageVersion }}"/>
    @endif

    {{-- JSON-LD Structured Data (Google rich results) --}}
    <script type="application/ld+json">
        {
            "@@context": "https://schema.org",
            "@type": "Product",
            "name": {{ json_encode($product->name) }},
            "description": {{ json_encode($product->description ?? 'Menu spesial dari ' . $storeName) }},
            "url": {{ json_encode($canonicalUrl) }},
            "offers": {
                "@type": "Offer",
                "price": "{{ $product->price }}",
                "priceCurrency": "IDR",
                "availability": "{{ $product->is_active ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock' }}"
            }
        @if($product->image)
            ,"image": {{ json_encode(Storage::url($product->image)) }}
        @endif
        @if($product->category)
            ,"category": {{ json_encode($product->category->name) }}
        @endif
        }
    </script>

    @vite(['resources/css/store.css', 'resources/js/store.js'])
    @livewireStyles
    @if(config('midtrans.client_key'))
        <script
            src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
            data-client-key="{{ config('midtrans.client_key') }}"></script>
    @endif
</head>
