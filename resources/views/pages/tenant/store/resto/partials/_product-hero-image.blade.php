@php use Illuminate\Support\Facades\Storage; @endphp

{{-- Parallax Hero Image — sticky saat scroll --}}
<div class="sticky top-0 w-full h-[45vh] z-0 overflow-hidden">
    @if($product->image)
        <div class="relative w-full h-full bg-[var(--foreground)]">
            <div id="hero-skeleton" class="absolute inset-0 bg-zinc-700 animate-pulse"></div>
            <img
                src="{{ Storage::url($product->image) }}"
                alt="{{ $product->name }}"
                class="absolute inset-0 w-full h-full object-cover transition-opacity duration-700 opacity-0"
                onload="this.style.opacity=1; const s=document.getElementById('hero-skeleton'); if(s) s.style.display='none';"
                loading="eager"
            />
        </div>
    @else
        <div class="w-full h-full bg-[var(--bg-soft)] flex flex-col items-center justify-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                 class="text-[var(--border)]">
                <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/>
                <circle cx="9" cy="9" r="2"/>
                <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
            </svg>
            <span class="text-[10px] font-bold text-[var(--border)] uppercase tracking-widest">No Image</span>
        </div>
    @endif
    <div class="absolute inset-0 bg-gradient-to-b from-black/40 via-transparent to-transparent pointer-events-none"></div>
</div>
