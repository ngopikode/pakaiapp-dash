@php
    $brandText  = $setting->navbar_brand_text ?? 'Ez';
    $title      = $setting->navbar_title ?? $setting->name ?? 'EzRetail';
    $subtitle   = $setting->navbar_subtitle ?? 'Toko Online Terpercaya';
    $logo       = $setting->logo ?? '';
    $themeColor = $setting->theme_color ?? '#3b82f6'; // default blue for retail
@endphp

<nav class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-zinc-200/50 shadow-sm" style="--primary-color: {{ $themeColor }};">
    <div class="max-w-xl mx-auto flex justify-between items-center px-5 py-3">
        <div class="flex items-center gap-3">
            <div class="bg-gradient-to-br from-[var(--primary-color)] to-blue-500 w-10 h-10 rounded-full flex items-center justify-center shadow-md border border-black/5 hover:scale-105 transition-transform duration-300 overflow-hidden shrink-0">
                @if($logo)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($logo) }}" alt="{{ $title }}" class="w-full h-full object-cover" />
                @else
                    <span class="font-brand text-white font-bold text-lg leading-none pt-0.5">{{ $brandText }}</span>
                @endif
            </div>
            <div onclick="window.scrollTo({top: 0, behavior: 'smooth'})" class="cursor-pointer active:scale-95 transition-transform">
                <h1 class="font-extrabold text-base leading-none tracking-tight text-zinc-900">{{ $title }}</h1>
                <p class="text-[10px] font-medium text-zinc-500 mt-0.5">{{ $subtitle }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            {{-- Tombol Riwayat --}}
            <button
                @click="historyOpen = true"
                class="relative p-2.5 bg-zinc-50 hover:bg-zinc-100 group transition-all duration-300 rounded-full active:scale-90 border border-zinc-200 hover:border-[var(--primary-color)]"
                title="Riwayat Belanja"
            >
                <span
                    x-show="historyCount > 0"
                    x-text="historyCount"
                    class="absolute -top-1 -right-1 bg-red-500 text-white text-[9px] font-black h-4 w-4 rounded-full flex items-center justify-center border border-white shadow-sm"
                    style="display: none;"
                ></span>
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-700 group-hover:text-[var(--primary-color)] transition-colors"><path d="M12 8v4l3 3"/><circle cx="12" cy="12" r="10"/></svg>
            </button>
        </div>
    </div>
</nav>
