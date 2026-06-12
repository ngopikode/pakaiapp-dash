@php
    use App\Models\StoreSetting;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    /** @var \App\Models\Product $product */

    $setting = StoreSetting::first();

    $waNumber   = '';
    $orderTypes = [['id' => 'takeaway', 'label' => 'Takeaway']];

    if ($setting) {
        $waNumber = preg_replace('/\D/', '', $setting->whatsapp_number ?: '6281234567890');
        if (str_starts_with($waNumber, '0')) $waNumber = '62' . substr($waNumber, 1);
        $storeType  = $setting->store_type ?: 'resto';
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

    // Same shape as product-list.blade.php — Alpine storeApp expects this format.
    $productData = [
        'id'              => $product->id,
        'name'            => $product->name,
        'description'     => $product->description,
        'image'           => $product->image ? Storage::url($product->image) : null,
        'price'           => $product->price,
        'formatted_price' => $product->formatted_price,
        'category'        => $product->category?->name ?? '',
        'is_active'       => $product->is_active,
        'has_variants'    => $product->has_variants,
        'selection_type'  => $product->selection_type ?? 'single',
        'max_selections'  => $product->max_selections ?? 1,
        'default_variant_id' => $product->variants->firstWhere('name', 'Default')?->id ?? $product->variants->first()?->id,
        'variants'        => $product->variants->map(fn ($v) => [
            'id'    => $v->id,
            'name'  => $v->name,
            'price' => $v->price,
        ])->toArray(),
        'extras'          => $product->extras->where('is_active', true)->map(fn ($e) => [
            'id'    => $e->id,
            'name'  => $e->name,
            'price' => $e->price,
        ])->values()->toArray(),
    ];

    // --- SEO & META OPTIMIZATION ---
    $storeName    = $setting?->name ?? 'Menu Digital';
    $themeColor   = $setting?->theme_color ?? '#f59e0b';
    $canonicalUrl = url()->current();

    // 1. Dynamic Title (Lebih hangat)
    $pageTitle = "{$product->name} di {$storeName}";

    // 2. Dynamic Description (Gabungan trik lama + dilimit 155 char agar SEO friendly di Google)
    $hooks = ['Cuma', 'Hanya', 'Spesial', 'Nikmati seharga', 'Dapatkan cuma', 'Pesan sekarang'];
    $randomHook = $hooks[array_rand($hooks)];
    $priceString = 'Rp ' . number_format($product->price, 0, ',', '.');
    $rawDesc = $product->description ? trim($product->description) : "Menu favorit dari {$storeName}.";

    // Hasil: "Cuma Rp 15.000! Nasi goreng gila pedas mampus..."
    $fullDesc = "{$randomHook} {$priceString}! {$rawDesc}";
    $ogDesc = Str::limit($fullDesc, 155, '...');

    // 3. Image Versioning (Agar thumbnail WA terupdate otomatis jika foto diubah)
    $imageVersion = $product->updated_at ? $product->updated_at->timestamp : time();
@endphp
    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-no-progress-bar>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>:root {
            --primary-color: {{ $themeColor }};
        }</style>

    {{-- Primary SEO --}}
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $ogDesc }}"/>
    {{-- Tambahan dynamic keyword dari kategori --}}
    <meta name="keywords"
          content="{{ $product->name }}, {{ $product->category?->name ?? 'Menu' }}, {{ $storeName }}, pesan online, menu resto"/>
    <meta name="theme-color" content="{{ $setting->theme_color ?: '#18181b' }}">

    {{-- Favicon / Icon --}}
    @if($setting->logo)
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

<body>

{{-- ===== PAGE LOADER — OUTSIDE x-data so Alpine errors can NEVER block it ===== --}}
@include('pages.tenant.store.resto._loader')

<div
    class="bg-zinc-100 min-h-screen text-zinc-900 font-sans antialiased relative selection:bg-[var(--primary-color)] selection:text-black"
    x-data="storeApp"
    data-default-order-type="{{ $orderTypes[0]['id'] }}"
    data-wa-number="{{ $waNumber }}"
    data-duitku-enabled="{{ config('duitku.enabled') ? 1 : 0 }}"
    data-midtrans-enabled="{{ config('midtrans.server_key') ? 1 : 0 }}"
    data-tax-active="{{ $setting?->is_tax_active ? 1 : 0 }}"
    data-tax-rate="{{ $setting?->tax_rate ?? 10.00 }}"
    data-service-active="{{ $setting?->is_service_charge_active ? 1 : 0 }}"
    data-service-rate="{{ $setting?->service_charge_rate ?? 5.00 }}"
    @show-toast.window="showToast($event.detail.message)"
    @open-options-modal.window="openOption($event.detail.product)"
    @open-checkout-modal.window="openCheckout()"
    @keydown.escape.window="
        if (checkoutOpen)    { closeCheckout(); }
        else if (optionOpen) { closeOption(); }
    "
>
    {{-- ===== PRODUCT CONTENT ===== --}}
    <div
        x-data="{
            product: @js($productData),
            scrolled: false,
            get qtyInCart() {
                const i = cart.find(x => x.cartName === this.product.name);
                return i ? i.qty : 0;
            },
            isRefreshing: false,
            pullY: 0,
            pullStartY: 0,
            handleTouchStart(e) {
                if (window.scrollY === 0) this.pullStartY = e.touches[0].clientY;
                else this.pullStartY = 0;
            },
            handleTouchMove(e) {
                if (!this.pullStartY || this.isRefreshing) return;
                const dist = e.touches[0].clientY - this.pullStartY;
                if (dist > 0 && window.scrollY <= 0) {
                    this.pullY = Math.min(dist, 100);
                }
            },
            async handleTouchEnd() {
                if (!this.pullStartY) return;
                if (this.pullY > 60) {
                    this.isRefreshing = true;
                    this.pullY = 60;
                    await new Promise(r => setTimeout(r, 400));
                    if (typeof Livewire !== 'undefined') {
                        Livewire.navigate(window.location.href);
                    } else {
                        window.location.reload();
                    }
                } else {
                    this.pullY = 0;
                }
                this.pullStartY = 0;
            }
        }"
        @scroll.window="scrolled = window.scrollY > window.innerHeight * 0.25"
        @touchstart="handleTouchStart($event)"
        @touchmove="handleTouchMove($event)"
        @touchend="handleTouchEnd()"
    >
        {{-- Floating header (transparent to solid on scroll) --}}
        <header
            class="fixed top-0 left-0 right-0 z-[110] transition-all duration-300 px-4 py-3 flex items-center justify-between max-w-xl mx-auto"
            :class="scrolled ? 'bg-white/90 backdrop-blur-xl border-b border-zinc-100 shadow-sm' : 'bg-transparent'"
        >
            <a
                href="/"
                wire:navigate.hover
                class="p-2.5 rounded-full transition-all duration-300 active:scale-90 border"
                :class="scrolled ? 'bg-white text-zinc-900 border-zinc-200' : 'bg-black/20 backdrop-blur-md text-white border-white/20'"
                aria-label="Kembali ke menu"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m12 19-7-7 7-7"/>
                    <path d="M19 12H5"/>
                </svg>
            </a>

            <h2
                class="text-sm font-black text-zinc-900 truncate max-w-[200px] transition-all duration-300"
                :class="scrolled ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-2'"
                x-text="product.name"
            ></h2>

            <div class="flex items-center gap-2">
                <a
                    :href="'{{ route('product.story', $product) }}'"
                    target="_blank" rel="noreferrer"
                    class="p-2.5 rounded-full transition-all duration-300 active:scale-90 border hover:bg-[#25D366] hover:text-white hover:border-[#25D366]"
                    :class="scrolled ? 'bg-white text-zinc-900 border-zinc-200' : 'bg-black/20 backdrop-blur-md text-white border-white/20'"
                    aria-label="Bagikan ke WhatsApp"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                         fill="currentColor">
                        <path
                            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.888-.788-1.489-1.761-1.663-2.06-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                    </svg>
                </a>
                <button
                    @click.prevent.stop="$store.utils.shareProduct({{ json_encode($productData) }}, '{{ $storeName }}')"
                    class="p-2.5 rounded-full transition-all duration-300 active:scale-90 border"
                    :class="scrolled ? 'bg-white text-zinc-900 border-zinc-200' : 'bg-black/20 backdrop-blur-md text-white border-white/20'"
                    aria-label="Bagikan link"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="18" cy="5" r="3"/>
                        <circle cx="6" cy="12" r="3"/>
                        <circle cx="18" cy="19" r="3"/>
                        <line x1="8.59" x2="15.42" y1="13.51" y2="17.49"/>
                        <line x1="15.41" x2="8.59" y1="6.51" y2="10.49"/>
                    </svg>
                </button>
            </div>
        </header>

        {{-- Pull-to-Refresh Indicator --}}
        <div
            class="w-full flex justify-center items-end overflow-hidden transition-all duration-200 ease-out relative z-[100]"
            :style="`height: ${isRefreshing ? 60 : Math.min(pullY, 60)}px; opacity: ${isRefreshing ? 1 : Math.min(pullY / 60, 1)}`"
        >
            <div class="flex items-center gap-2 text-zinc-500 pb-3">
                <template x-if="isRefreshing">
                    <div
                        class="w-5 h-5 border-2 border-[var(--primary-color)] border-t-transparent rounded-full animate-spin"></div>
                </template>
                <template x-if="!isRefreshing">
                    <div class="w-5 h-5 flex items-center justify-center transition-transform"
                         :style="`transform: rotate(${pullY * 3}deg)`">↓
                    </div>
                </template>
                <span class="text-xs font-bold"
                      x-text="isRefreshing ? 'Memuat ulang produk...' : 'Tarik untuk refresh'"></span>
            </div>
        </div>

        {{-- Parallax Hero Image --}}
        <div class="sticky top-0 w-full h-[45vh] z-0 overflow-hidden">
            @if($product->image)
                <div class="relative w-full h-full bg-zinc-900">
                    <div id="hero-skeleton" class="absolute inset-0 bg-zinc-800 animate-pulse"></div>
                    <img
                        src="{{ Storage::url($product->image) }}"
                        alt="{{ $product->name }}"
                        class="absolute inset-0 w-full h-full object-cover transition-opacity duration-700 opacity-0"
                        onload="this.style.opacity=1; const s=document.getElementById('hero-skeleton'); if(s) s.style.display='none';"
                        loading="eager"
                    />
                </div>
            @else
                <div class="w-full h-full bg-zinc-100 flex flex-col items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                         class="text-zinc-300">
                        <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/>
                        <circle cx="9" cy="9" r="2"/>
                        <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                    </svg>
                    <span class="text-[10px] font-bold text-zinc-300 uppercase tracking-widest">No Image</span>
                </div>
            @endif
            <div
                class="absolute inset-0 bg-gradient-to-b from-black/40 via-transparent to-transparent pointer-events-none"></div>
        </div>

        {{-- Scrollable content card --}}
        <main
            class="relative z-10 max-w-xl mx-auto bg-white min-h-[60vh] rounded-t-[2rem] -mt-8 pb-44 shadow-[0_-10px_40px_rgba(0,0,0,0.1)]">
            <div class="w-full flex justify-center pt-3 pb-5">
                <div class="w-12 h-1.5 bg-zinc-200 rounded-full"></div>
            </div>

            <div class="px-6">
                <div class="flex justify-between items-start gap-4">
                    <div class="flex-1">
                        @if($product->category)
                            <span
                                class="inline-block px-3 py-1.5 rounded-xl bg-zinc-100 text-zinc-600 text-[10px] font-black uppercase tracking-widest mb-3 border border-zinc-200/60">{{ $product->category->name }}</span>
                        @endif
                        <h1 class="text-[1.75rem] font-black text-zinc-900 leading-tight tracking-tight">{{ $product->name }}</h1>
                    </div>
                    <div class="text-right pt-1 shrink-0 min-w-0">
                        <div
                            class="text-lg md:text-2xl font-black text-[var(--primary-color)] font-mono tracking-tighter whitespace-nowrap">
                            {{ $product->formatted_price }}
                        </div>
                    </div>
                </div>

                <div class="h-px bg-zinc-100 my-6"></div>

                <div class="space-y-3">
                    <h3 class="text-xs font-black uppercase tracking-widest text-zinc-900 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="text-zinc-400">
                            <line x1="3" x2="21" y1="6" y2="6"/>
                            <line x1="3" x2="21" y1="12" y2="12"/>
                            <line x1="3" x2="21" y1="18" y2="18"/>
                        </svg>
                        Deskripsi Menu
                    </h3>
                    <p class="text-sm text-zinc-500 leading-relaxed">{{ $product->description ?: 'Tidak ada deskripsi untuk menu ini.' }}</p>
                </div>

                @if($product->has_variants && $product->variants->count() > 0)
                    <div class="mt-6 space-y-3">
                        <h3 class="text-xs font-black uppercase tracking-widest text-zinc-900 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="text-zinc-400">
                                <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                                <line x1="3" x2="21" y1="6" y2="6"/>
                                <path d="M16 10a4 4 0 0 1-8 0"/>
                            </svg>
                            Pilihan Varian
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($product->variants as $variant)
                                <span
                                    class="px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-xl text-xs font-bold text-zinc-600">
                                    {{ $variant->name }}
                                    @if($product->selection_type !== 'multiple')
                                        <span
                                            class="text-[var(--primary-color)] ml-1">— Rp {{ number_format($variant->price, 0, ',', '.') }}</span>
                                    @endif
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                @php $activeExtras = $product->extras->where('is_active', true); @endphp
                @if($activeExtras->count() > 0)
                    <div class="mt-6 space-y-3">
                        <h3 class="text-xs font-black uppercase tracking-widest text-zinc-900 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="text-zinc-400">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" x2="12" y1="8" y2="16"/>
                                <line x1="8" x2="16" y1="12" y2="12"/>
                            </svg>
                            Pilihan Tambahan (Add-On)
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($activeExtras as $extra)
                                <span
                                    class="px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-xl text-xs font-bold text-zinc-600">
                                    {{ $extra->name }}
                                    <span
                                        class="text-[var(--primary-color)] ml-1">+Rp {{ number_format($extra->price, 0, ',', '.') }}</span>
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </main>

        {{-- Fixed Bottom Action Bar --}}
        <div
            class="fixed bottom-0 left-0 right-0 z-[120] bg-white border-t border-zinc-100 px-5 py-4 shadow-[0_-10px_30px_rgba(0,0,0,0.05)]">
            <div class="max-w-xl mx-auto flex flex-col gap-2.5">

                {{-- Stepper (non-variant, non-extra item already in cart) --}}
                <template
                    x-if="qtyInCart > 0 && !product.has_variants && !(product.extras && product.extras.length > 0)">
                    <div class="flex items-center justify-between rounded-2xl p-1.5 w-full bg-zinc-900 shadow-xl">
                        <button @click="updateQty(product.name, -1)"
                                class="w-12 h-12 flex items-center justify-center rounded-xl text-white hover:bg-zinc-700 transition-all active:scale-90">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round">
                                <line x1="5" x2="19" y1="12" y2="12"/>
                            </svg>
                        </button>
                        <span class="font-black text-lg text-white tabular-nums" x-text="qtyInCart"></span>
                        <button @click="addToCart(product)"
                                class="w-12 h-12 flex items-center justify-center text-zinc-900 bg-[var(--primary-color)] hover:brightness-110 rounded-xl transition-all active:scale-90">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round">
                                <line x1="12" x2="12" y1="5" y2="19"/>
                                <line x1="5" x2="19" y1="12" y2="12"/>
                            </svg>
                        </button>
                    </div>
                </template>

                {{-- Add / Variant / Extra button --}}
                <template
                    x-if="qtyInCart === 0 || product.has_variants || (product.extras && product.extras.length > 0)">
                    <button
                        @click="(product.has_variants || (product.extras && product.extras.length > 0)) ? openOption(product) : addToCart(product)"
                        :disabled="!product.is_active"
                        class="w-full py-4 rounded-2xl text-sm font-black uppercase tracking-widest transition-all active:scale-95 flex items-center justify-center gap-2"
                        :class="product.is_active ? 'bg-zinc-900 text-white shadow-xl hover:bg-zinc-800' : 'bg-zinc-200 text-zinc-400 cursor-not-allowed'"
                    >
                        <template x-if="!product.is_active"><span>Produk Habis</span></template>
                        <template x-if="product.is_active && product.has_variants">
                            <span class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round" class="text-[var(--primary-color)]"><path
                                        d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" x2="21"
                                                                                                      y1="6" y2="6"/><path
                                        d="M16 10a4 4 0 0 1-8 0"/></svg>
                                Pilih Opsi Varian
                            </span>
                        </template>
                        <template
                            x-if="product.is_active && !product.has_variants && product.extras && product.extras.length > 0">
                            <span class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round" class="text-[var(--primary-color)]"><circle cx="12" cy="12"
                                                                                                         r="10"/><line
                                        x1="12" x2="12" y1="8" y2="16"/><line x1="8" x2="16" y1="12" y2="12"/></svg>
                                Pilih Add-On &bull; {{ $product->formatted_price }}
                            </span>
                        </template>
                        <template
                            x-if="product.is_active && !product.has_variants && !(product.extras && product.extras.length > 0)">
                            <span class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round" class="text-[var(--primary-color)]"><line x1="12" x2="12"
                                                                                                       y1="5" y2="19"/><line
                                        x1="5" x2="19" y1="12" y2="12"/></svg>
                                Tambah ke Keranjang &bull; {{ $product->formatted_price }}
                            </span>
                        </template>
                    </button>
                </template>

                {{-- Floating checkout button (when cart has items) --}}
                <template x-if="totalQty > 0">
                    <button
                        @click="openCheckout()"
                        class="w-full bg-gradient-to-r from-zinc-900 to-zinc-800 text-white p-4 rounded-2xl shadow-lg flex justify-between items-center border border-white/10 relative overflow-hidden group hover:shadow-xl transition-all duration-300 active:scale-[0.98]"
                    >
                        <div
                            class="absolute inset-0 bg-[var(--primary-color)]/5 group-hover:bg-[var(--primary-color)]/10 transition-colors duration-500"></div>
                        <div class="relative flex items-center gap-3.5">
                            <div
                                class="bg-[var(--primary-color)] text-zinc-900 w-11 h-11 rounded-xl flex items-center justify-center font-black text-sm shadow-md"
                                x-text="totalQty"></div>
                            <div class="text-left">
                                <span class="block text-[9px] font-bold uppercase tracking-widest text-zinc-400 mb-0.5">Total Estimasi</span>
                                <span class="font-bold text-lg text-white font-mono leading-none"
                                      x-text="formatPrice(totalCart)"></span>
                            </div>
                        </div>
                        <div class="relative flex items-center gap-2 pr-1">
                            <span class="text-[10px] font-black uppercase tracking-widest">Checkout</span>
                            <div
                                class="bg-white/10 p-1.5 rounded-full group-hover:bg-[var(--primary-color)]/20 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <path d="m9 18 6-6-6-6"/>
                                </svg>
                            </div>
                        </div>
                    </button>
                </template>
            </div>
        </div>
    </div>{{-- /product x-data --}}

    {{-- Toast --}}
    <div
        class="fixed top-4 left-4 right-4 z-[9999] sm:left-1/2 sm:right-auto sm:-translate-x-1/2 sm:top-6 sm:w-auto sm:min-w-[280px] bg-zinc-900 text-white px-5 py-3.5 rounded-2xl sm:rounded-full shadow-2xl shadow-zinc-900/30 transition-all duration-500 ease-out flex items-center justify-center sm:justify-start gap-3 border border-white/5 backdrop-blur-xl pointer-events-none"
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

    {{-- Variant Option Modal --}}
    @include('pages.tenant.store.resto.option-modal')

    {{-- Checkout Modal --}}
    @include('pages.tenant.store.resto.checkout-modal', ['orderTypes' => $orderTypes])

</div>

@livewireScripts
</body>
</html>
