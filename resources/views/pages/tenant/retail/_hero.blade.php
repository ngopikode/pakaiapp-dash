@php
    $heroImage = $setting->hero_image ?? null;
    $heroTitle = $setting->hero_title ?? 'Selamat Datang';
    $heroSubtitle = $setting->hero_subtitle ?? null;
@endphp

<div class="w-full max-w-xl mx-auto mb-1">
    @if($heroImage)
        {{-- With custom image — matches product.blade.php hero treatment --}}
        <div class="relative w-full overflow-hidden" style="aspect-ratio: 21/9;">
            <div class="absolute inset-0 bg-[var(--bg-soft)] animate-pulse" id="hero-banner-skeleton"></div>
            <img
                src="{{ \Illuminate\Support\Facades\Storage::url($heroImage) }}"
                alt="{{ $heroTitle }}"
                class="absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-700"
                onload="this.style.opacity=1; const s=document.getElementById('hero-banner-skeleton'); if(s) s.style.display='none';"
                loading="eager"
            >
            {{-- Gradient overlay — same as product.blade.php --}}
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-black/20 pointer-events-none"></div>
            {{-- Text overlay --}}
            <div class="absolute bottom-0 left-0 right-0 p-5">
                <h1 class="text-white font-black text-2xl leading-tight tracking-tight" style="text-shadow: 0 2px 16px rgba(0,0,0,0.4);">{{ $heroTitle }}</h1>
                @if($heroSubtitle)
                    <p class="text-white/75 text-xs mt-1 leading-relaxed max-w-xs">{{ $heroSubtitle }}</p>
                @endif
            </div>
        </div>
    @else
        {{-- Fallback: no image — uses same CSS vars as product.blade.php --}}
        <div class="px-4 pt-5 pb-2">
            <div class="relative overflow-hidden rounded-2xl p-6 shadow-lg" style="background: linear-gradient(135deg, var(--primary-color) 0%, color-mix(in srgb, var(--primary-color) 70%, black) 100%);">
                {{-- Decorative blobs matching product.blade.php style --}}
                <div class="absolute -top-8 -right-8 w-36 h-36 rounded-full pointer-events-none" style="background: rgba(255,255,255,0.12); filter: blur(24px);"></div>
                <div class="absolute -bottom-6 -left-6 w-24 h-24 rounded-full pointer-events-none" style="background: rgba(0,0,0,0.12); filter: blur(18px);"></div>

                <div class="relative z-10">
                    {{-- Category-style label — matching product.blade.php badge --}}
                    <span class="inline-block px-2.5 py-1 mb-3 rounded-xl border text-[10px] font-black uppercase tracking-widest" style="background: rgba(255,255,255,0.15); border-color: rgba(255,255,255,0.25); color: rgba(255,255,255,0.9); backdrop-filter: blur(4px);">Menu Digital</span>
                    <h1 class="text-white font-black text-3xl leading-tight tracking-tight">{{ $heroTitle }}</h1>
                    @if($heroSubtitle)
                        <p class="text-white/75 text-sm mt-2 leading-relaxed max-w-xs">{{ $heroSubtitle }}</p>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
