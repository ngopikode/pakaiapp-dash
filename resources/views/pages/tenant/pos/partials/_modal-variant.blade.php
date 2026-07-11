{{-- ===== VARIANT MODAL (Shared between Resto & Retail) ===== --}}
<div class="modal fade" id="variantModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-[1.5rem] border border-emerald-800/15 bg-white shadow-lg dark:border-slate-700 dark:bg-slate-900">
            <div class="modal-header rounded-t-[1.5rem] border-b border-emerald-800/10 bg-white px-4 pb-3 pt-4 dark:border-slate-800 dark:bg-slate-900">
                <div>
                    <h4 class="mb-1 font-black text-emerald-800 dark:text-emerald-400">Pilih Varian</h4>
                    <p class="mb-0 text-sm font-semibold text-slate-500 dark:text-slate-400" x-text="selectedProduct ? selectedProduct.name : ''"></p>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body rounded-b-[1.5rem] bg-white p-4 dark:bg-slate-900">
                <div class="flex flex-col gap-2">
                    <template x-if="selectedProduct">
                        <template x-for="variant in selectedProduct.variants" :key="variant.id">
                            <button type="button"
                                    class="flex w-full flex-row items-center justify-between rounded-2xl border bg-white p-3 text-start transition-all"
                                    :class="variant.stock > 0 ? 'border-emerald-800/20 shadow-sm' : 'border-slate-200 bg-slate-50 opacity-50 dark:border-slate-800 dark:bg-slate-950'"
                                    :disabled="variant.stock <= 0"
                                    @click="if(variant.stock > 0) addVariantToCart(variant)"
                                    style="border-color: var(--bs-border-color-translucent) !important;">
                                <div>
                                    <h6 class="mb-1 font-black text-slate-900 dark:text-white" x-text="variant.name"></h6>
                                    <template x-if="variant.sku">
                                        <div class="mb-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
                                            <i class="bi bi-upc-scan me-1"></i><span x-text="variant.sku"></span>
                                        </div>
                                    </template>
                                    <span class="inline-flex items-center rounded-full border px-2.5 py-1 text-[11px] font-bold"
                                          :class="variant.stock > 0 ? 'border-emerald-800/20 bg-emerald-50 text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-400' : 'border-red-200 bg-red-50 text-red-600'"
                                          x-text="variant.stock > 0 ? 'Tersedia: ' + variant.stock : 'Stok Habis'"></span>
                                </div>
                                <div class="text-end">
                                    <template x-if="variant.active_discount_price && Number(variant.active_discount_price) > 0 && Number(variant.active_discount_price) < Number(variant.price)">
                                        <div class="flex flex-col items-end">
                                            <span class="text-xs font-semibold text-red-500 line-through" x-text="'Rp ' + formatRupiah(variant.price)"></span>
                                            <h5 class="mb-0 font-black text-emerald-800 dark:text-emerald-400" x-text="'Rp ' + formatRupiah(variant.active_discount_price)"></h5>
                                        </div>
                                    </template>
                                    <template x-if="!variant.active_discount_price || Number(variant.active_discount_price) === 0 || Number(variant.active_discount_price) >= Number(variant.price)">
                                        <h5 class="mb-0 font-black text-emerald-800 dark:text-emerald-400" x-text="'Rp ' + formatRupiah(variant.price)"></h5>
                                    </template>
                                </div>
                            </button>
                        </template>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>
