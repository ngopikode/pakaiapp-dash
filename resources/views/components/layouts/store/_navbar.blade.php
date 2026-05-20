@php
    $brandText  = $setting->navbar_brand_text ?? 'Ez';
    $title      = $setting->navbar_title ?? $setting->name ?? 'EzMenu';
    $subtitle   = $setting->navbar_subtitle ?? 'Menu Digital';
    $logo       = $setting->logo ?? '';
    $themeColor = $setting->theme_color ?? '#f59e0b';
@endphp

<nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-xl border-b border-zinc-100/50 shadow-sm shadow-zinc-100/50">
    <style>
        :root { --primary-color: {{ $themeColor }}; }
    </style>
    <div class="max-w-xl mx-auto flex justify-between items-center px-5 py-3">
        <div class="flex items-center gap-3">
            <div class="bg-gradient-to-br from-[var(--primary-color)] to-[var(--primary-color)] w-10 h-10 rounded-xl flex items-center justify-center shadow-lg shadow-[var(--primary-color)]/20 border border-black/5 rotate-[-2deg] hover:rotate-0 transition-transform duration-300 overflow-hidden shrink-0">
                @if($logo)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($logo) }}" alt="{{ $title }}" class="w-full h-full object-cover" />
                @else
                    <span class="font-brand text-zinc-900 text-lg leading-none pt-0.5">{{ $brandText }}</span>
                @endif
            </div>
            <div onclick="window.scrollTo({top: 0, behavior: 'smooth'})" class="cursor-pointer active:scale-95 transition-transform">
                <h1 class="font-extrabold text-sm leading-none tracking-tight uppercase text-zinc-900">{{ $title }}</h1>
                <p class="text-[9px] font-bold text-zinc-400 uppercase tracking-[0.2em] mt-0.5">{{ $subtitle }}</p>
            </div>
        </div>
        <button
            @click="$dispatch('open-qr-modal')"
            class="p-2.5 bg-zinc-50 hover:bg-[var(--primary-color)] transition-all duration-300 rounded-xl active:scale-90 hover:shadow-lg hover:shadow-[var(--primary-color)]/20 border border-zinc-100 hover:border-[var(--primary-color)]">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-900"><rect width="5" height="5" x="3" y="3" rx="1"/><rect width="5" height="5" x="16" y="3" rx="1"/><rect width="5" height="5" x="3" y="16" rx="1"/><path d="M21 16h-3a2 2 0 0 0-2 2v3"/><path d="M21 21v.01"/><path d="M12 7v3a2 2 0 0 1-2 2H7"/><path d="M3 12h.01"/><path d="M12 3h.01"/><path d="M12 16v.01"/><path d="M16 12h1"/><path d="M21 12v.01"/><path d="M12 21v-1"/></svg>
        </button>
    </div>
</nav>
