{{--
    _loader.blade.php — Full-page loading overlay.

    Uses Alpine + Livewire window events. No external JS needed.
      • Initial load   : visible from first HTML paint (style="display:flex"), hidden
                         once livewire:initialized fires → x-show fades it out.
      • wire:navigate.hover  : re-shown on livewire:navigating, hidden on livewire:navigate.hoverd.

    Placed OUTSIDE x-data="storeApp" so Alpine errors in storeApp can never
    prevent this from hiding.
--}}
@persist('app-loader')
<div
    id="app-loader"
    class="fixed inset-0 z-[2000] bg-stone-50/80 dark:bg-stone-950/80 backdrop-blur-md flex flex-col items-center justify-center gap-6 transition-opacity duration-300 opacity-100 pointer-events-auto"
    style="display: flex;"
>
    <div class="relative">
        <div
            class="w-20 h-20 rounded-3xl bg-gradient-to-br from-zinc-900 to-zinc-800 flex items-center justify-center shadow-2xl shadow-zinc-900/20 border border-zinc-800 animate-bounce">
            {{-- UtensilsCrossed SVG (Lucide equivalent) --}}
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round"
                 class="text-[var(--primary-color,#f59e0b)]">
                <path d="m16 2-2.3 2.3a3 3 0 0 0 0 4.2l1.8 1.8a3 3 0 0 0 4.2 0L22 8"/>
                <path d="M15 15 3.3 3.3a4.2 4.2 0 0 0 0 6l7.3 7.3c.9.9 2.1 1.4 3.4 1.4H20v-3.5c0-1.3-.5-2.5-1.4-3.4z"/>
                <path d="m2 22 7.5-7.5"/>
            </svg>
        </div>
        {{-- Slow spinning dashed ring --}}
        <div class="absolute -inset-2 rounded-[2rem] border-2 border-dashed border-[var(--primary-color)]/50 animate-spin"
             style="animation-duration:3s"></div>
    </div>

    <div class="text-center space-y-1.5">
        <p class="text-zinc-900 dark:text-zinc-100 text-sm font-black tracking-tight">Menyiapkan Menu</p>
        <div class="flex items-center justify-center gap-1.5">
            <div class="w-1.5 h-1.5 rounded-full bg-[var(--primary-color)] animate-dot-1"></div>
            <div class="w-1.5 h-1.5 rounded-full bg-[var(--primary-color)] animate-dot-2"></div>
            <div class="w-1.5 h-1.5 rounded-full bg-[var(--primary-color)] animate-dot-3"></div>
        </div>
    </div>
</div>
@endpersist
