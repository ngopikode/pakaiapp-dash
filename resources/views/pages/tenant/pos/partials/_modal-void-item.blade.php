{{-- Void Item Modal --}}
<div x-show="isVoidItemModalOpen" x-cloak class="relative z-[9999]" @keydown.escape.window="isVoidItemModalOpen = false">
    <div x-show="isVoidItemModalOpen"
         x-transition:enter="ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"
         @click="isVoidItemModalOpen = false"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="isVoidItemModalOpen"
                 x-transition:enter="ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-100" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-[24px] bg-card border border-border shadow-2xl transition-all sm:my-8 w-full max-w-[420px] flex flex-col p-6 sm:p-8">
                
                <div class="mx-auto flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-full bg-destructive/10 ring-8 ring-destructive/5 mb-6">
                    <i class="ph-fill ph-warning-circle text-3xl text-destructive"></i>
                </div>

                <div class="text-center mb-6">
                    <h3 class="text-xl font-extrabold text-foreground tracking-tight mb-2" id="voidItemModalLabel">Batalkan Item?</h3>
                    <p class="text-sm text-muted-foreground font-medium px-2">
                        Aksi ini tidak dapat dikembalikan. Stok akan dipulihkan secara otomatis.
                    </p>
                </div>

                <div class="flex gap-3 w-full">
                    <button type="button" @click="isVoidItemModalOpen = false"
                            class="flex-1 rounded-2xl border border-border bg-card px-4 py-3.5 text-[14px] font-bold text-muted-foreground transition-colors hover:bg-secondary hover:text-foreground">
                        Batal
                    </button>
                    <button type="button" @click="$wire.voidItem(voidItemId); isVoidItemModalOpen = false"
                            class="flex-[2] flex items-center justify-center gap-2 rounded-2xl bg-destructive px-4 py-3.5 text-[14px] font-bold text-destructive-foreground shadow-lg shadow-destructive/20 transition-all hover:bg-destructive/90 active:scale-[0.98]">
                        Batalkan Item
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>
