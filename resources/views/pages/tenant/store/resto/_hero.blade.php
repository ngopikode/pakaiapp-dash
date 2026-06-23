@php
    use Illuminate\Support\Facades\Storage;
    $storeName = $setting->name ?: 'EzMenu';
    $tagline = $setting->hero_tagline ?? 'Welcome to ' . $storeName;
    $logoUrl = $setting->logo ? Storage::url($setting->logo) : null;
    $ogImage = $setting->og_image ? Storage::url($setting->og_image) : null;
@endphp

<header class="relative bg-[var(--background)] border-none mb-6">
    <div class="h-48 w-full relative overflow-hidden bg-[var(--bg-soft)]">
        @if($ogImage)
            <img src="{{ $ogImage }}" alt="Cover" class="absolute inset-0 w-full h-full object-cover opacity-50" />
            <div class="absolute inset-0 bg-gradient-to-t from-[var(--background)] to-transparent"></div>
        @endif

        @if($setting->hero_tagline)
            <div class="absolute inset-0 flex items-center justify-center pt-8 z-10">
                <p class="text-sm md:text-base font-serif italic text-[var(--foreground)]/90 drop-shadow-sm px-6 text-center max-w-[320px] leading-relaxed">
                    "{{ trim($setting->hero_tagline, '"') }}"
                </p>
            </div>
        @endif

        {{-- Promo Badge (Kiri Atas) --}}
        @if($setting->hero_promo_text)
            <div class="absolute top-4 left-4 z-20">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-500 text-white text-[10px] font-black shadow-lg uppercase tracking-[0.15em]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="currentColor" class="text-yellow-300"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    {{ $setting->hero_promo_text }}
                </span>
            </div>
        @endif

        {{-- Floating Action Buttons (Kanan Atas) --}}
        <div class="absolute top-4 right-4 flex items-center gap-1.5 z-20">
            <button @click="toggleTheme()" class="relative p-2 bg-[var(--bg-soft)]/80 backdrop-blur-md hover:bg-[var(--primary-color)] group transition-all duration-300 rounded-xl active:scale-90 hover:shadow-lg hover:shadow-[var(--primary-color)]/20 border border-[var(--border)] hover:border-[var(--primary-color)]" title="Ganti Tema">
                <svg x-show="theme === 'dark'" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[var(--foreground)] group-hover:text-black transition-colors"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>
                <svg x-show="theme !== 'dark'" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[var(--foreground)] group-hover:text-black transition-colors" style="display: none;"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
            </button>
            <button @click="historyOpen = true" class="relative p-2 bg-[var(--bg-soft)]/80 backdrop-blur-md hover:bg-[var(--primary-color)] group transition-all duration-300 rounded-xl active:scale-90 hover:shadow-lg hover:shadow-[var(--primary-color)]/20 border border-[var(--border)] hover:border-[var(--primary-color)]" title="Riwayat Pesanan">
                <span x-show="historyCount > 0" x-text="historyCount" class="absolute -top-1.5 -right-1.5 bg-red-500 text-white text-[9px] font-black h-4 w-4 rounded-full flex items-center justify-center border border-white shadow-sm animate-pulse" style="display: none;"></span>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[var(--foreground)] group-hover:text-black transition-colors"><path d="M12 8v4l3 3"/><circle cx="12" cy="12" r="10"/></svg>
            </button>
            <button @click="$dispatch('open-contact-modal')" class="relative p-2 bg-[var(--bg-soft)]/80 backdrop-blur-md hover:bg-[var(--primary-color)] group transition-all duration-300 rounded-xl active:scale-90 hover:shadow-lg hover:shadow-[var(--primary-color)]/20 border border-[var(--border)] hover:border-[var(--primary-color)]" title="Hubungi Kami">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[var(--foreground)] group-hover:text-black transition-colors"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
            </button>
            <button @click="$dispatch('open-qr-modal')" class="relative p-2 bg-[var(--bg-soft)]/80 backdrop-blur-md hover:bg-[var(--primary-color)] group transition-all duration-300 rounded-xl active:scale-90 hover:shadow-lg hover:shadow-[var(--primary-color)]/20 border border-[var(--border)] hover:border-[var(--primary-color)]" title="Scan QR Menu">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[var(--foreground)] group-hover:text-black transition-colors"><rect width="5" height="5" x="3" y="3" rx="1"/><rect width="5" height="5" x="16" y="3" rx="1"/><rect width="5" height="5" x="3" y="16" rx="1"/><path d="M21 16h-3a2 2 0 0 0-2 2v3"/><path d="M21 21v.01"/><path d="M12 7v3a2 2 0 0 1-2 2H7"/><path d="M3 12h.01"/><path d="M12 3h.01"/><path d="M12 16v.01"/><path d="M16 12h1"/><path d="M21 12v.01"/><path d="M12 21v-1"/></svg>
            </button>
        </div>
        
        <div class="h-full w-full flex items-center justify-center absolute inset-0 pointer-events-none">
            <h2 class="text-3xl font-black opacity-[0.08] uppercase tracking-tighter select-none px-6 text-center leading-tight text-[var(--background)]">
                {{ $storeName }}
            </h2>
        </div>
    </div>

    <div class="px-6 -mt-10 relative flex flex-col items-center text-center pb-2">
        <!-- Logo -->
        <div class="w-20 h-20 p-1 rounded-full shadow-2xl mb-3 relative z-10 bg-[var(--surface)] ring-1 ring-[var(--border)]">
            <div class="w-full h-full rounded-full flex items-center justify-center overflow-hidden bg-[var(--bg-soft)]">
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ $storeName }}" class="w-full h-full object-cover" />
                @else
                    <div class="flex flex-col items-center justify-center h-full w-full text-center">
                        <span class="font-serif italic text-xl text-[var(--primary)] font-bold">{{ substr($storeName, 0, 2) }}</span>
                    </div>
                @endif
            </div>
            <!-- Live Indicator -->
            @if($setting->is_active)
                <div class="absolute bottom-1 right-0 w-4 h-4 border-[2px] rounded-full bg-emerald-500 border-[var(--surface)] shadow-sm"></div>
            @else
                <div class="absolute bottom-1 right-0 w-4 h-4 border-[2px] rounded-full bg-red-500 border-[var(--surface)] shadow-sm"></div>
            @endif
        </div>

        <!-- Main Title (Brand + Title) -->
        <h1 class="text-[28px] md:text-3xl font-black text-[var(--foreground)] tracking-tight leading-none mb-2">
            @if($setting->navbar_brand_text)
                {{ $setting->navbar_brand_text }} <span class="text-[var(--primary-color)]">{{ $setting->navbar_title ?: $storeName }}</span>
            @else
                {{ $setting->navbar_title ?: $storeName }}
            @endif
        </h1>

        <!-- Status Text (Below Title) -->
        @if($setting->hero_status_text)
            <div class="inline-flex items-center justify-center gap-1.5 px-3 py-1 rounded-full bg-[var(--bg-soft)] border border-[var(--border)] text-[10px] font-extrabold text-[var(--text-secondary)] uppercase tracking-[0.15em] mb-4 shadow-sm">
                @if($setting->is_active)
                    <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                @else
                    <div class="w-1.5 h-1.5 rounded-full bg-red-500"></div>
                @endif
                {{ $setting->hero_status_text }}
            </div>
        @endif

        <!-- Headline & Subtitle Inline -->
        <div class="flex items-center justify-center gap-1.5 flex-wrap mb-2">
            @if($setting->hero_headline)
                <span class="text-[11px] font-bold text-[var(--foreground)] uppercase tracking-widest">{{ $setting->hero_headline }}</span>
            @endif
            @if($setting->hero_headline && $setting->navbar_subtitle)
                <span class="w-1 h-1 rounded-full bg-[var(--border)] opacity-60"></span>
            @endif
            @if($setting->navbar_subtitle)
                <span class="text-[10px] font-bold text-[var(--text-secondary)] uppercase tracking-wider">{{ $setting->navbar_subtitle }}</span>
            @endif
        </div>

        <!-- Bottom Group: Address & Instagram -->
        <div class="flex flex-col items-center gap-2 w-full">
            <!-- Address -->
            @if($setting->address)
                <div class="flex flex-row items-start justify-center gap-1.5 text-[11px] text-[var(--text-secondary)] opacity-80 max-w-[280px] leading-relaxed">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 mt-0.5"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                    <span>{{ $setting->address }}</span>
                </div>
            @endif

            <!-- Social Media Link -->
            @if($setting->hero_instagram_url)
                <button @click="window.open('{{ $setting->hero_instagram_url }}', '_blank')" class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[var(--surface)] border border-[var(--border)] text-[11px] font-bold text-[var(--text-secondary)] hover:text-[#E1306C] hover:border-[#E1306C]/50 hover:bg-[#E1306C]/5 transition-all duration-300 shadow-sm active:scale-95 group">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="group-hover:text-[#E1306C] transition-colors"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg>
                    Follow us on Instagram
                </button>
            @endif
        </div>
    </div>
</header>
