<section class="px-3 py-2">
    <div class="relative max-w-xl mx-auto bg-gradient-to-br from-[#18181b] via-[#1e1e22] to-[#111113] rounded-[2rem] p-7 overflow-hidden text-white shadow-xl shadow-zinc-900/20 border border-white/[0.04]">

        <!-- Decorative blurs -->
        <div class="absolute -top-16 -right-16 w-56 h-56 bg-[var(--primary-color)]/15 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-20 -left-20 w-40 h-40 bg-[var(--primary-color)]/8 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-full h-1/2 bg-gradient-to-t from-black/40 to-transparent"></div>

        <!-- Subtle grid pattern -->
        <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 24px 24px"></div>

        <div class="relative z-10">
            <div class="flex flex-wrap items-center gap-2 mb-5">
                <span class="bg-[var(--primary-color)] text-black px-3 py-1.5 rounded-full text-[9px] font-black uppercase tracking-wider shadow-lg shadow-[var(--primary-color)]/25 flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
                    {{ $promoText }}
                </span>
                <div class="bg-white/10 backdrop-blur-md px-3 py-1.5 rounded-full border border-white/10 flex items-center gap-1.5">
                    <div class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse shadow-lg shadow-emerald-400/50"></div>
                    <span class="text-[9px] font-bold uppercase tracking-wider text-zinc-300">{{ $statusText }}</span>
                </div>
            </div>

            <h2 class="text-[2.25rem] font-black leading-[0.9] mb-4 text-white uppercase tracking-tighter">
                {{ $headlineParts[0] }} <br/>
                @if(count($headlineParts) > 1)
                    <span class="text-[var(--primary-color)] italic font-serif">&</span> {{ $headlineParts[1] }}
                @endif
            </h2>

            <div class="mb-6 border-l-2 border-[var(--primary-color)]/40 pl-3.5 space-y-1">
                <p class="text-zinc-300 text-xs leading-relaxed font-medium">
                    {{ $address ?: 'Lokasi belum diatur' }}
                </p>
                <p class="text-[var(--primary-color)] text-[10px] italic font-medium opacity-90">
                    {{ $tagline }}
                </p>
            </div>

            <div class="flex gap-3">
                <button onclick="document.getElementById('menu-start')?.scrollIntoView({behavior: 'smooth', block: 'start'})" class="bg-[var(--primary-color)] text-zinc-900 px-6 py-3.5 rounded-xl text-[10px] font-black uppercase tracking-widest flex items-center gap-2 shadow-lg shadow-[var(--primary-color)]/25 active:scale-95 transition-all hover:brightness-110 hover:shadow-xl hover:shadow-[var(--primary-color)]/30">
                    Pesan Sekarang 
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </button>
                <a href="{{ $instagramUrl }}" target="_blank" rel="noreferrer" class="bg-white/10 border border-white/10 text-white px-4 py-3.5 rounded-xl flex items-center gap-2 hover:bg-white/20 transition-all active:scale-95 backdrop-blur-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg>
                </a>
            </div>
        </div>

        <div class="absolute -right-4 bottom-2 opacity-[0.07] animate-float">
            <svg xmlns="http://www.w3.org/2000/svg" width="128" height="128" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"/><line x1="6" x2="6" y1="2" y2="4"/><line x1="10" x2="10" y1="2" y2="4"/><line x1="14" x2="14" y1="2" y2="4"/></svg>
        </div>
    </div>
</section>