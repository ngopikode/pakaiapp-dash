<div>
{{-- ===== INITIAL LOADING STATE ===== --}}
@if($isLoading)
<div class="min-h-screen flex flex-col items-center justify-center bg-zinc-50 gap-6">
    <div class="relative">
        <div class="w-20 h-20 rounded-3xl bg-gradient-to-br from-zinc-900 to-zinc-800 flex items-center justify-center shadow-2xl shadow-zinc-900/20 animate-bounce">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[var(--primary-color,#f59e0b)]">
                <path d="m16 2-2.3 2.3a3 3 0 0 0 0 4.2l1.8 1.8a3 3 0 0 0 4.2 0L22 8"/>
                <path d="M15 15 3.3 3.3a4.2 4.2 0 0 0 0 6l7.3 7.3c.9.9 2.1 1.4 3.4 1.4H20v-3.5c0-1.3-.5-2.5-1.4-3.4z"/>
                <path d="m2 22 7.5-7.5"/>
            </svg>
        </div>
        <div class="absolute -inset-2 rounded-[2rem] border-2 border-dashed border-zinc-200 animate-spin-slow"></div>
    </div>
    <div class="text-center space-y-1.5">
        <p class="text-zinc-800 text-sm font-black tracking-tight">Menyiapkan Menu</p>
        <div class="flex items-center justify-center gap-1">
            <div class="w-1.5 h-1.5 rounded-full bg-zinc-300 animate-bounce" style="animation-delay: 0ms"></div>
            <div class="w-1.5 h-1.5 rounded-full bg-zinc-400 animate-bounce" style="animation-delay: 150ms"></div>
            <div class="w-1.5 h-1.5 rounded-full bg-zinc-500 animate-bounce" style="animation-delay: 300ms"></div>
        </div>
    </div>
</div>

{{-- ===== ERROR STATE ===== --}}
@elseif($hasError)
<div class="min-h-screen flex flex-col items-center justify-center bg-zinc-50 p-6 text-center">
    <div class="w-16 h-16 rounded-2xl bg-red-50 flex items-center justify-center mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-red-400"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
    </div>
    <h2 class="text-lg font-black text-zinc-900 mb-1">Oops!</h2>
    <p class="text-sm text-zinc-400 mb-6 max-w-xs">{{ $errorMsg ?: 'Terjadi kesalahan saat memuat data.' }}</p>
    <button onclick="window.location.reload()" class="bg-zinc-900 text-white px-6 py-3 rounded-xl text-xs font-black uppercase tracking-wider hover:bg-zinc-800 transition-colors active:scale-95">
        Coba Lagi
    </button>
</div>

{{-- ===== MAIN CONTENT ===== --}}
@else
    <livewire:pages::tenant.store.header-hero/>
    <livewire:pages::tenant.store.product-list/>
@endif
</div>