@persist('app-loader')
<div
    id="app-loader"
    class="fixed inset-0 z-[2000] bg-zinc-50/70 backdrop-blur-md flex flex-col items-center justify-center gap-6 transition-opacity duration-300 opacity-100 pointer-events-auto"
    style="display: flex;"
>
    <div class="relative">
        <div
            class="w-20 h-20 rounded-3xl bg-gradient-to-br from-[var(--primary-color)] to-blue-600 flex items-center justify-center shadow-2xl shadow-blue-500/20 animate-bounce">
            {{-- Shopping Bag SVG --}}
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/>
                <path d="M3 6h18"/>
                <path d="M16 10a4 4 0 0 1-8 0"/>
            </svg>
        </div>
        <div class="absolute -inset-2 rounded-[2rem] border-2 border-dashed border-blue-200 animate-spin"
             style="animation-duration:3s"></div>
    </div>

    <div class="text-center space-y-1.5">
        <p class="text-zinc-800 text-sm font-black tracking-tight">Menyiapkan Etalase</p>
        <div class="flex items-center justify-center gap-1">
            <div class="w-1.5 h-1.5 rounded-full bg-zinc-300 animate-bounce" style="animation-delay:0ms"></div>
            <div class="w-1.5 h-1.5 rounded-full bg-zinc-400 animate-bounce" style="animation-delay:150ms"></div>
            <div class="w-1.5 h-1.5 rounded-full bg-zinc-500 animate-bounce" style="animation-delay:300ms"></div>
        </div>
    </div>
</div>
@endpersist
