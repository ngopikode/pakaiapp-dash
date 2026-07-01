{{-- Toast Notification — muncul dari atas saat ada aksi (tambah ke cart, dll) --}}
<div
    class="fixed top-4 left-4 right-4 z-[9999] sm:left-1/2 sm:right-auto sm:-translate-x-1/2 sm:top-6 sm:w-auto sm:min-w-[280px] bg-[var(--foreground)] text-[var(--background)] px-5 py-3.5 rounded-2xl sm:rounded-full shadow-2xl shadow-zinc-900/30 transition-all duration-500 ease-out flex items-center justify-center sm:justify-start gap-3 border border-white/5 backdrop-blur-xl pointer-events-none -translate-y-8 opacity-0 scale-95"
    :class="toast.show ? 'translate-y-0 opacity-100 scale-100' : '-translate-y-8 opacity-0 scale-95'"
>
    <div class="bg-emerald-500 rounded-full p-1 text-[var(--background)] shrink-0 shadow-lg shadow-emerald-500/30">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
            <path d="m9 11 3 3L22 4"/>
        </svg>
    </div>
    <span class="text-xs font-bold tracking-wide text-left flex-1 break-words" x-text="toast.message"></span>
</div>
