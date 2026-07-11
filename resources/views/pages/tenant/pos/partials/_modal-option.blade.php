<div class="modal fade" id="optionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-[1.5rem] border border-emerald-800/15 bg-white shadow-lg dark:border-slate-700 dark:bg-slate-900">
            <div class="modal-header rounded-t-[1.5rem] border-b border-emerald-800/10 bg-emerald-50/60 px-4 pb-3 pt-4 dark:border-slate-800 dark:bg-slate-900">
                <div>
                    <h5 class="mb-1 font-black text-slate-900 dark:text-white">Pilih Varian</h5>
                    <p class="mb-0 text-sm font-semibold text-slate-500 dark:text-slate-400" x-text="optionProduct ? optionProduct.name : ''"></p>
                    <div x-show="optionProduct && optionProduct.selection_type === 'multiple'">
                        <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-1 text-[11px] font-extrabold text-amber-800 dark:bg-amber-500/20 dark:text-amber-400"
                              x-text="'Pilih maks ' + (optionProduct ? optionProduct.max_selections : 0) + ' pilihan'"></span>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body rounded-b-[1.5rem] bg-white p-4 dark:bg-slate-900">
                <div class="flex max-h-[50vh] flex-col gap-2 overflow-y-auto">
                    <div x-show="optionProduct">
                        <div>
                            {{-- Variants --}}
                            <div x-show="optionProduct?.variants?.length > 0 && optionProduct?.has_variants">
                                <div class="mb-3 flex flex-col gap-2">
                                    <template x-for="variant in (optionProduct?.variants || [])" :key="variant.id">
                                        <button type="button"
                                                class="flex w-full flex-row items-center justify-between rounded-2xl border bg-white p-3 text-start transition-all"
                                                :class="{
                                                    'border-emerald-500 bg-emerald-50 shadow-sm': isOptionSelected(variant.name),
                                                    'border-slate-200 bg-slate-50 opacity-50 dark:border-slate-800 dark:bg-slate-950': variant.stock <= 0,
                                                    'border-slate-200 dark:border-slate-700': variant.stock > 0 && !isOptionSelected(variant.name)
                                                }"
                                                :disabled="variant.stock <= 0"
                                                @click="if(variant.stock > 0) toggleOption(variant)">
                                            <div class="flex items-center gap-3">
                                                <i class="bi fs-4" x-show="optionProduct?.selection_type === 'multiple'"
                                                   :class="isOptionSelected(variant.name) ? 'bi-check-square-fill text-emerald-700 dark:text-emerald-400' : 'bi-square text-slate-300'"></i>
                                                <i class="bi fs-4" x-show="optionProduct?.selection_type !== 'multiple'"
                                                   :class="isOptionSelected(variant.name) ? 'bi-record-circle-fill text-emerald-700 dark:text-emerald-400' : 'bi-circle text-slate-300'"></i>
                                                <div>
                                                    <h6 class="mb-0 font-black text-slate-900 dark:text-white" x-text="variant.name"></h6>
                                                </div>
                                            </div>
                                            <div class="text-end" x-show="optionProduct?.selection_type !== 'multiple'">
                                                <template x-if="variant.active_discount_price && Number(variant.active_discount_price) > 0 && Number(variant.active_discount_price) < Number(variant.price)">
                                                    <div class="flex flex-col items-end">
                                                        <span class="text-xs font-semibold text-red-500 line-through" x-text="'Rp ' + formatRupiah(variant.price)"></span>
                                                        <span class="text-sm font-black text-emerald-800 dark:text-emerald-400" x-text="'Rp ' + formatRupiah(variant.active_discount_price)"></span>
                                                    </div>
                                                </template>
                                                <template x-if="!variant.active_discount_price || Number(variant.active_discount_price) === 0 || Number(variant.active_discount_price) >= Number(variant.price)">
                                                    <h6 class="mb-0 font-black text-slate-600 dark:text-slate-300" x-text="'+ Rp ' + formatRupiah(variant.price)"></h6>
                                                </template>
                                            </div>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            {{-- Add-ons / Extras --}}
                            <div x-show="optionProduct?.extras?.length > 0">
                                <div>
                                    <div x-show="optionProduct?.has_variants">
                                        <div class="my-3 flex items-center gap-3">
                                            <div class="flex-1 border-t border-emerald-800/10 dark:border-slate-800"></div>
                                            <span class="text-xs font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">Tambahan / Extra</span>
                                            <div class="flex-1 border-t border-emerald-800/10 dark:border-slate-800"></div>
                                        </div>
                                    </div>

                                    <div class="flex flex-col gap-2">
                                        <template x-for="extra in (optionProduct?.extras || [])" :key="extra.id">
                                            <button type="button"
                                                    class="flex w-full flex-row items-center justify-between rounded-2xl border bg-white p-3 text-start transition-all"
                                                    :class="{
                                                        'border-emerald-500 bg-emerald-50': isExtraSelected(extra.name),
                                                        'border-slate-200 dark:border-slate-700': !isExtraSelected(extra.name)
                                                    }"
                                                    @click="toggleExtra(extra)">
                                                <div class="flex items-center gap-3">
                                                    <i class="bi fs-4"
                                                       :class="isExtraSelected(extra.name) ? 'bi-check-square-fill text-emerald-700 dark:text-emerald-400' : 'bi-square text-slate-300'"></i>
                                                    <div>
                                                        <h6 class="mb-0 font-black text-slate-900 dark:text-white" x-text="extra.name"></h6>
                                                    </div>
                                                </div>
                                                <h6 class="mb-0 font-black text-slate-600 dark:text-slate-300" x-text="'+ Rp ' + formatRupiah(extra.price)"></h6>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Qty + Confirm --}}
                <div x-show="optionProduct && (optionSelected.length > 0 || !optionProduct.has_variants || (optionProduct.extras && optionProduct.extras.length > 0))"
                     class="mt-4 border-t border-emerald-800/10 pt-3 dark:border-slate-800">
                    <div class="mb-4 flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-600 dark:text-slate-400">Jumlah Pesanan</span>
                        <div class="flex items-center rounded-full border border-emerald-800/20 bg-emerald-50/60 p-1 dark:border-slate-700 dark:bg-slate-950">
                            <button @click="if(optionQty > 1) optionQty--"
                                    class="flex h-9 w-9 items-center justify-center rounded-full bg-white text-slate-700 shadow-sm dark:bg-slate-800 dark:text-slate-200">
                                <i class="bi bi-dash"></i>
                            </button>
                            <span class="px-4 text-base font-black text-slate-900 dark:text-white" x-text="optionQty"></span>
                            <button @click="optionQty++"
                                    class="flex h-9 w-9 items-center justify-center rounded-full bg-emerald-800 text-white shadow-sm dark:bg-emerald-400 dark:text-slate-950">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </div>
                    <button @click="confirmOption"
                            class="flex w-full items-center justify-between rounded-2xl bg-gradient-to-r from-emerald-800 to-emerald-600 p-3 text-sm font-black text-white shadow-sm transition hover:scale-[1.01] dark:from-emerald-500 dark:to-emerald-400 dark:text-slate-950"
                            style="border: none;"
                            :disabled="optionProduct && optionProduct.has_variants && optionSelected.length === 0">
                        <span><i class="bi bi-cart-plus me-2"></i>Tambahkan ke Keranjang</span>
                        <span x-text="'Rp ' + formatRupiah(optionTotalPrice)"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
