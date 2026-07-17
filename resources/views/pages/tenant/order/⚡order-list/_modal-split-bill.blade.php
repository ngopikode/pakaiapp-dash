<div x-show="showSplitModalState"
     x-cloak
     class="fixed inset-0 z-[1050] overflow-y-auto"
     role="dialog" aria-modal="true" aria-labelledby="splitModalLabel"
     @keydown.escape.window="showSplitModalState = false">
    <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
        <div x-show="showSplitModalState"
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"
             @click="showSplitModalState = false" aria-hidden="true"></div>

        <div x-show="showSplitModalState"
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative flex max-h-[90vh] w-full max-w-lg transform flex-col overflow-hidden rounded-[24px] border border-border bg-card text-left shadow-2xl transition-all sm:my-8">

            {{-- Header --}}
            <div class="flex shrink-0 items-start justify-between gap-4 border-b border-border px-5 py-4">
                <div class="flex min-w-0 items-center gap-3">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-orange-500/10 text-orange-600 dark:text-orange-400">
                        <i class="ph-bold ph-split-horizontal text-xl"></i>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-extrabold text-foreground" id="splitModalLabel">Pisah Bill</h3>
                        <p class="mt-0.5 truncate text-xs font-medium text-muted-foreground" x-text="splittingOrder ? '#' + splittingOrder.invoice_code : ''"></p>
                    </div>
                </div>
                <button type="button" @click="showSplitModalState = false" aria-label="Tutup" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-muted-foreground transition-colors hover:bg-secondary hover:text-foreground">
                    <i class="ph-bold ph-x text-lg"></i>
                </button>
            </div>

            {{-- Body --}}
            <div class="min-h-0 flex-1 overflow-y-auto">
                {{-- Info Banner --}}
                <div class="flex items-start gap-3 border-b border-orange-500/20 bg-orange-500/10 px-5 py-3.5">
                    <i class="ph-fill ph-info mt-0.5 shrink-0 text-lg text-orange-500"></i>
                    <p class="text-xs font-semibold leading-relaxed text-orange-900 dark:text-orange-300">
                        Item yang dipilih akan dicabut dari pesanan ini dan dibuatkan nomor tagihan baru otomatis.
                    </p>
                </div>

                {{-- Item List --}}
                <div class="flex flex-col">
                    <template x-for="(item, index) in splitItems" :key="item.id">
                        <div class="flex items-center justify-between gap-4 border-b border-border px-5 py-3.5">
                            <div class="min-w-0 flex-1">
                                <h4 class="truncate text-sm font-bold text-foreground" x-text="item.name"></h4>
                                <template x-if="item.variant_name">
                                    <div class="truncate text-xs font-medium text-muted-foreground" x-text="item.variant_name"></div>
                                </template>
                                <div class="mt-0.5 text-xs font-bold text-emerald-600 dark:text-emerald-400" x-text="'Rp ' + item.price.toLocaleString('id-ID')"></div>
                            </div>

                            <div class="flex shrink-0 flex-col items-end gap-1.5">
                                <div class="flex items-center rounded-full border border-border bg-secondary/50 p-1">
                                    <button @click="item.qtyToSplit > 0 ? item.qtyToSplit-- : null"
                                            class="flex h-8 w-8 items-center justify-center rounded-full bg-card text-foreground shadow-sm transition-colors hover:bg-secondary disabled:opacity-40 disabled:cursor-not-allowed"
                                            :disabled="item.qtyToSplit === 0" aria-label="Kurangi">
                                        <i class="ph-bold ph-minus"></i>
                                    </button>
                                    <span class="min-w-[2.5rem] px-2 text-center text-sm font-bold text-foreground" x-text="item.qtyToSplit"></span>
                                    <button @click="item.qtyToSplit < item.maxQty ? item.qtyToSplit++ : null"
                                            class="flex h-8 w-8 items-center justify-center rounded-full bg-orange-500 text-white shadow-sm transition-colors hover:bg-orange-600 disabled:opacity-40 disabled:cursor-not-allowed"
                                            :disabled="item.qtyToSplit >= item.maxQty" aria-label="Tambah">
                                        <i class="ph-bold ph-plus"></i>
                                    </button>
                                </div>
                                <span class="rounded-full bg-secondary px-2.5 py-0.5 text-[10px] font-bold text-muted-foreground" x-text="'Maks: ' + item.maxQty"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex shrink-0 items-center gap-3 border-t border-border bg-secondary/30 px-5 py-4">
                <div class="flex flex-1 flex-col">
                    <span class="text-[11px] font-medium text-muted-foreground">Item Dipisah</span>
                    <span class="text-xl font-extrabold text-foreground" x-text="splitTotalItems"></span>
                </div>

                <button type="button" @click="showSplitModalState = false" class="rounded-2xl border border-border bg-card px-5 py-3 text-sm font-bold text-foreground transition-colors hover:bg-secondary">
                    Batal
                </button>
                <button type="button" class="flex items-center justify-center gap-2 rounded-2xl bg-orange-500 px-5 py-3 text-sm font-extrabold text-white shadow-sm transition-colors hover:bg-orange-600 disabled:cursor-not-allowed disabled:opacity-50"
                        @click="submitSplitOrder(); showSplitModalState = false;" :disabled="splitTotalItems === 0">
                    <i class="ph-bold ph-arrow-circle-right text-lg"></i> Lanjut Pisah
                </button>
            </div>
        </div>
    </div>
</div>
