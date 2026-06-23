@php
    use Illuminate\Support\Facades\Storage;
    $storeName = $setting->name ?: 'EzMenu';
    $logoUrl = $setting->logo ? Storage::url($setting->logo) : null;
@endphp

<nav class="h-[65px] sticky top-0 z-50 bg-[var(--surface)]/80 backdrop-blur-xl border-b border-[var(--border)] shadow-sm shadow-[var(--border)] transition-colors duration-300">
    <div class="max-w-xl mx-auto flex justify-between items-center px-5 py-3">
        <div class="flex items-center gap-3">
            <div class="bg-gradient-to-br from-[var(--primary-color)] to-[var(--primary-color)] w-10 h-10 rounded-xl flex items-center justify-center shadow-lg shadow-[var(--primary-color)]/20 border border-black/5 rotate-[-2deg] hover:rotate-0 transition-transform duration-300 overflow-hidden shrink-0">
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ $storeName }} Logo" class="w-full h-full object-cover">
                @else
                    <span class="font-serif italic text-xl text-black font-bold">{{ substr($storeName, 0, 2) }}</span>
                @endif
            </div>
            <div onclick="window.scrollTo({top: 0, behavior: 'smooth'})" class="cursor-pointer active:scale-95 transition-transform">
                <h1 class="font-extrabold text-sm leading-none tracking-tight uppercase text-[var(--foreground)]">{{ $storeName }}</h1>
                <p class="text-[9px] font-bold text-[var(--text-secondary)] uppercase tracking-[0.2em] mt-0.5">{{ $setting->hero_tagline ?? 'Store' }}</p>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-2">
            <button @click="theme = theme === 'dark' ? 'light' : 'dark'" class="w-9 h-9 rounded-xl bg-[var(--bg-soft)] border border-[var(--border)] flex items-center justify-center text-[var(--foreground)] hover:bg-[var(--primary)] hover:text-black transition-colors shadow-sm active:scale-95" title="Ganti Tema">
                <svg x-show="theme === 'dark'" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>
                <svg x-show="theme !== 'dark'" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
            </button>
            <button @click="historyOpen = true" class="w-9 h-9 rounded-xl bg-[var(--bg-soft)] border border-[var(--border)] flex items-center justify-center text-[var(--foreground)] hover:bg-[var(--primary)] hover:text-black transition-colors shadow-sm active:scale-95 relative" title="Riwayat Pesanan">
                <span x-show="historyCount > 0" x-text="historyCount" class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-black h-4 w-4 rounded-full flex items-center justify-center border border-[var(--surface)] shadow-sm" style="display: none;"></span>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v4l3 3"/><circle cx="12" cy="12" r="10"/></svg>
            </button>
        </div>
    </div>
</nav>
