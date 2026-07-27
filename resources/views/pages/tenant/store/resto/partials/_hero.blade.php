@php
    use Illuminate\Support\Facades\Storage;
    $storeName = $setting->name ?: 'EzMenu';
    $logoUrl   = $setting->logo    ? Storage::url($setting->logo)    : null;
    $ogImage   = $setting->og_image ? Storage::url($setting->og_image) : null;
@endphp

<header class="relative bg-[var(--background)] border-none mb-4 max-w-xl mx-auto">

    {{-- ===== COVER AREA ===== --}}
    <div class="h-40 w-full relative overflow-hidden bg-[var(--bg-soft)]">

        @if($ogImage)
            <div id="store-cover-skeleton" class="absolute inset-0 bg-[var(--bg-soft)] animate-pulse"></div>
            <img src="{{ $ogImage }}" alt="{{ $storeName }}"
                 class="absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-700"
                 onload="this.style.opacity=1; const s=document.getElementById('store-cover-skeleton'); if(s) s.style.display='none';"
                 loading="eager">
        @else
            <div class="absolute inset-0"
                 style="background: linear-gradient(160deg, var(--primary-color) 0%, color-mix(in srgb, var(--primary-color) 55%, #000) 100%);"></div>
        @endif

        {{-- Gradient overlay — Nyatu mulus di bawah, tapi tetep gelap pekat ke atas --}}
        <div
            class="absolute inset-0 bg-gradient-to-t from-[var(--background)] from-0% to-100% pointer-events-none"></div>
        
        {{-- TOP-LEFT: Promo badge (back to original position) --}}
        @if($setting->hero_promo_text)
            <div class="absolute top-4 left-4 z-20">
                <span
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-500 text-white text-[10px] font-black shadow-lg uppercase tracking-[0.15em]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                         fill="currentColor" class="text-yellow-300"><path
                            d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    {{ $setting->hero_promo_text }}
                </span>
            </div>
        @endif

        {{-- TOP-RIGHT: Action buttons --}}
        <div class="absolute top-4 right-4 flex items-center gap-1.5 z-20">
            <button @click="toggleTheme()"
                    class="p-2.5 rounded-full transition-all duration-300 active:scale-90 border bg-black/20 backdrop-blur-md text-white border-white/20"
                    title="Ganti Tema">
                <svg x-show="theme === 'dark'" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                     stroke-linejoin="round">
                    <circle cx="12" cy="12" r="4"/>
                    <path d="M12 2v2"/>
                    <path d="M12 20v2"/>
                    <path d="m4.93 4.93 1.41 1.41"/>
                    <path d="m17.66 17.66 1.41 1.41"/>
                    <path d="M2 12h2"/>
                    <path d="M20 12h2"/>
                    <path d="m6.34 17.66-1.41 1.41"/>
                    <path d="m19.07 4.93-1.41 1.41"/>
                </svg>
                <svg x-show="theme !== 'dark'" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                     stroke-linejoin="round" style="display:none">
                    <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/>
                </svg>
            </button>
            <button @click="historyOpen = true"
                    class="relative p-2.5 rounded-full transition-all duration-300 active:scale-90 border bg-black/20 backdrop-blur-md text-white border-white/20"
                    title="Riwayat Pesanan">
                <span x-show="historyCount > 0" x-text="historyCount"
                      class="absolute -top-1.5 -right-1.5 bg-red-500 text-white text-[9px] font-black h-4 w-4 rounded-full flex items-center justify-center border border-white shadow-sm animate-pulse"
                      style="display:none;"></span>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 8v4l3 3"/>
                    <circle cx="12" cy="12" r="10"/>
                </svg>
            </button>
            <button @click="$dispatch('open-contact-modal')"
                    class="p-2.5 rounded-full transition-all duration-300 active:scale-90 border bg-black/20 backdrop-blur-md text-white border-white/20"
                    title="Hubungi Kami">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M12 16v-4"/>
                    <path d="M12 8h.01"/>
                </svg>
            </button>
            <button @click="$dispatch('open-qr-modal')"
                    class="p-2.5 rounded-full transition-all duration-300 active:scale-90 border bg-black/20 backdrop-blur-md text-white border-white/20"
                    title="Scan QR Menu">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
            </button>
        </div>

        {{-- CENTER: Tagline — Shadow & Outer makin kuat di light mode, aman di dark mode --}}
        @if($setting->hero_tagline)
            <div class="absolute inset-0 flex items-center justify-center z-10 pointer-events-none px-10">
                <p class="text-sm font-serif italic text-white/95 text-center leading-relaxed max-w-[280px]
          font-medium dark:font-normal
          [text-shadow:0_1px_3px_rgba(0,0,0,1),0_0_15px_rgba(0,0,0,1),0_0_30px_rgba(0,0,0,0.8)]
          dark:[text-shadow:0_1px_8px_rgba(0,0,0,0.5)]">
                    "{{ trim($setting->hero_tagline, '"') }}"
                </p>
            </div>
        @endif
    </div>

    {{-- ===== IDENTITY SECTION — logo kiri, info kanan ===== --}}
    <div class="px-5 -mt-10 relative z-20 flex items-center gap-4 pb-4 max-w-xl mx-auto">

        {{-- Logo — float up, left-aligned --}}
        <div
            class="w-20 h-20 rounded-full bg-[var(--surface)] ring-4 ring-[var(--background)] shadow-xl shrink-0 relative flex items-center justify-center">
            @if($logoUrl)
                <img src="{{ $logoUrl }}" alt="{{ $storeName }}" class="w-full h-full object-cover rounded-full">
            @else
                <div class="w-full h-full rounded-full flex items-center justify-center"
                     style="background: linear-gradient(135deg, var(--primary-color), color-mix(in srgb, var(--primary-color) 60%, #000));">
                    <span class="text-2xl font-black text-white">{{ substr($storeName, 0, 2) }}</span>
                </div>
            @endif

            {{-- Live dot (Sudah Diperbaiki) --}}
            <div
                class="absolute bottom-0 right-0 w-4 h-4 rounded-full border-2 border-[var(--surface)] shadow-md shrink-0 {{ $setting->is_active ? 'bg-emerald-500' : 'bg-red-400' }}"></div>
        </div>

        {{-- Store info — right of logo, center-aligned --}}
        <div class="flex-1 min-w-0 pt-8"> {{-- Ditambah pt-8 agar turun sedikit menghindari tabrakan warna --}}
            <h1 class="text-2xl font-black text-[var(--foreground)] tracking-tight leading-tight">
                @if($setting->navbar_brand_text)
                    {{ $setting->navbar_brand_text }}&nbsp;<span
                        style="color: color-mix(in srgb, var(--primary-color) 85%, #000);">{{ $setting->navbar_title ?: $storeName }}</span>
                @else
                    {{ $setting->navbar_title ?: $storeName }}
                @endif
            </h1>

            {{-- Headline · Subtitle — satu baris --}}
            @if($setting->hero_headline || $setting->navbar_subtitle)
                <div class="flex items-center gap-1.5 flex-wrap mt-1">
                    @if($setting->hero_headline)
                        <span
                            class="text-sm font-medium text-[var(--text-secondary)]">{{ $setting->hero_headline }}</span>
                    @endif
                    @if($setting->hero_headline && $setting->navbar_subtitle)
                        <span class="w-1 h-1 rounded-full bg-[var(--border)] opacity-60 shrink-0"></span>
                    @endif
                    @if($setting->navbar_subtitle)
                        <span
                            class="text-xs font-medium text-[var(--text-secondary)] opacity-80">{{ $setting->navbar_subtitle }}</span>
                    @endif
                </div>
            @endif

            @php
                $statusText = $setting->hero_status_text;
                $statusColor = 'bg-red-400'; // Default closed

                if (!empty($todayHours)) {
                    if ($isOpenNow) {
                        $statusText = 'Buka Sekarang';
                        $statusColor = 'bg-emerald-500 animate-pulse';
                    } elseif ($todayHours['is_closed']) {
                        $statusText = 'Tutup Hari Ini';
                    } else {
                        $statusText = 'Sedang Tutup';
                    }
                } elseif ($setting->is_active) { // Fallback to old is_active if no operating_hours
                    $statusText = 'Buka Sekarang';
                    $statusColor = 'bg-emerald-500 animate-pulse';
                }
            @endphp

            @if($statusText)
                <div
                    class="inline-flex items-center gap-1.5 mt-2.5 px-2.5 py-1 rounded-xl bg-[var(--bg-soft)] border border-[var(--border)] text-[10px] font-black uppercase tracking-widest text-[var(--text-secondary)]">
                    <div class="w-1.5 h-1.5 rounded-full shrink-0 {{ $statusColor }}"></div>
                    {{ $statusText }}
                </div>
            @endif

            {{-- Menampilkan jam operasional hari ini jika ada --}}
            @if(!empty($todayHours) && !$todayHours['is_closed'])
                <p class="text-xs text-[var(--text-secondary)] mt-1 opacity-70">
                    {{ $todayHours['open'] }} – {{ $todayHours['close'] }}
                </p>
            @elseif(!empty($todayHours) && $todayHours['is_closed'])
                <p class="text-xs text-[var(--text-secondary)] mt-1 opacity-70">
                    Buka lagi besok
                </p>
            @endif

        </div>

    </div>

</header>

