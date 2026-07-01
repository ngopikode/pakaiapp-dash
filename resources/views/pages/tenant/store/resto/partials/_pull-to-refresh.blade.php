{{-- Pull-to-Refresh Indicator — reacts to pullY & isRefreshing Alpine state --}}
<div
    class="w-full flex justify-center items-end overflow-hidden transition-all duration-200 ease-out relative z-[100]"
    :style="`height: ${isRefreshing ? 60 : Math.min(pullY, 60)}px; opacity: ${isRefreshing ? 1 : Math.min(pullY / 60, 1)}`"
>
    <div class="flex items-center gap-2 text-[var(--text-secondary)] pb-3">
        <template x-if="isRefreshing">
            <div class="w-5 h-5 border-2 border-[var(--primary-color)] border-t-transparent rounded-full animate-spin"></div>
        </template>
        <template x-if="!isRefreshing">
            <div class="w-5 h-5 flex items-center justify-center transition-transform"
                 :style="`transform: rotate(${pullY * 3}deg)`">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <polyline points="19 12 12 19 5 12"></polyline>
                </svg>
            </div>
        </template>
        <span class="text-xs font-bold"
              x-text="isRefreshing ? 'Memuat ulang produk...' : 'Tarik untuk refresh'"></span>
    </div>
</div>
