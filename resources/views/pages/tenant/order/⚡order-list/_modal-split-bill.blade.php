<div x-show="showSplitModalState"
     style="display: none;"
     x-transition.opacity
     class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4"
     @keydown.escape.window="showSplitModalState = false">
    
    <div x-show="showSplitModalState"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-lg shadow-2xl flex flex-col max-h-[90vh]"
         @click.outside="showSplitModalState = false">

        {{-- Header --}}
        <div class="border-b border-border px-6 py-4 flex shrink-0 items-center justify-between rounded-t-3xl bg-white dark:bg-slate-900">
            <div class="flex items-center gap-3">
                <div class="bg-orange-500/10 text-orange-600 rounded-full flex items-center justify-center w-11 h-11">
                    <i class="ph-bold ph-split-horizontal text-xl"></i>
                </div>
                <div>
                    <h5 class="font-bold mb-0 text-slate-800 dark:text-slate-200 tracking-tight text-lg">Pisah Bill</h5>
                    <div class="text-slate-500 dark:text-slate-400 text-sm font-medium" x-text="splittingOrder ? '#' + splittingOrder.invoice_code : ''"></div>
                </div>
            </div>
            <button type="button" @click="showSplitModalState = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <i class="ph-bold ph-x text-lg"></i>
            </button>
        </div>

        {{-- Body --}}
        <div class="p-0 bg-white dark:bg-slate-900 overflow-y-auto min-h-0 flex-1">
            {{-- Info Banner --}}
            <div class="bg-orange-500/10 border-b border-orange-500/20 px-6 py-4 flex gap-3 items-start">
                <i class="ph-fill ph-info text-orange-500 mt-0.5 text-xl"></i>
                <p class="mb-0 text-slate-700 dark:text-slate-300 text-sm font-medium leading-relaxed">
                    Item yang dipilih akan <b class="text-orange-600 dark:text-orange-500">dicabut</b> dari pesanan saat ini dan dibuatkan <b>Nomor Tagihan Baru</b> secara otomatis.
                </p>
            </div>

            {{-- Item List --}}
            <div class="flex flex-col mt-2">
                <template x-for="(item, index) in splitItems" :key="item.id">
                    <div class="border-b border-border px-6 py-4 flex justify-between items-center gap-4">
                        
                        {{-- Item Info --}}
                        <div class="flex-1 min-w-0">
                            <h6 class="font-bold mb-1 text-slate-800 dark:text-slate-200 truncate" x-text="item.name"></h6>
                            <template x-if="item.variant_name">
                                <div class="text-sm text-slate-500 dark:text-slate-400 mb-1 font-medium truncate" x-text="item.variant_name"></div>
                            </template>
                            <div class="font-bold text-brand-accent text-sm" x-text="'Rp ' + item.price.toLocaleString('id-ID')"></div>
                        </div>

                        {{-- Item Controls --}}
                        <div class="flex flex-col items-end gap-2 shrink-0">
                            <div class="flex items-center bg-slate-50 dark:bg-slate-800/50 rounded-full border border-border shadow-sm p-1">
                                <button @click="item.qtyToSplit > 0 ? item.qtyToSplit-- : null"
                                        class="w-8 h-8 rounded-full bg-white dark:bg-slate-700 shadow-sm border border-border flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-600 transition-colors"
                                        :class="item.qtyToSplit === 0 ? 'opacity-50 cursor-not-allowed' : ''"
                                        :disabled="item.qtyToSplit === 0">
                                    <i class="ph-bold ph-minus"></i>
                                </button>
                                
                                <span class="font-bold px-3 text-sm text-slate-800 dark:text-slate-200 min-w-[2.5rem] text-center" x-text="item.qtyToSplit"></span>
                                
                                <button @click="item.qtyToSplit < item.maxQty ? item.qtyToSplit++ : null"
                                        class="w-8 h-8 rounded-full bg-orange-500 text-white shadow-sm flex items-center justify-center hover:bg-orange-600 transition-colors"
                                        :class="item.qtyToSplit >= item.maxQty ? 'opacity-50 cursor-not-allowed' : ''"
                                        :disabled="item.qtyToSplit >= item.maxQty">
                                    <i class="ph-bold ph-plus"></i>
                                </button>
                            </div>
                            <span class="bg-slate-500/10 text-slate-600 border border-slate-500/20 rounded-full px-2.5 py-0.5 text-[10px] font-bold" x-text="'Maks: ' + item.maxQty"></span>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Footer --}}
        <div class="border-t border-border p-4 md:p-6 bg-white dark:bg-slate-900 rounded-b-3xl flex items-center gap-4">
            <div class="flex flex-col flex-1 pl-2">
                <span class="text-slate-500 dark:text-slate-400 font-medium text-xs">Total Item Dipisah</span>
                <span class="font-bold text-slate-800 dark:text-slate-200 text-xl" x-text="splitTotalItems"></span>
            </div>
            
            <button type="button" @click="showSplitModalState = false" class="border border-border text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 font-bold rounded-full px-6 py-2.5 transition-colors text-sm">
                Batal
            </button>
            <button type="button" class="bg-orange-500 hover:bg-orange-600 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold rounded-full px-6 py-2.5 flex items-center justify-center gap-2 shadow-sm transition-all text-sm"
                    @click="submitSplitOrder(); showSplitModalState = false;" :disabled="splitTotalItems === 0">
                <i class="ph-bold ph-arrow-circle-right text-lg"></i> Lanjut Pisah
            </button>
        </div>
    </div>
</div>
