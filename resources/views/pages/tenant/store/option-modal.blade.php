{{-- Option Modal (Variants + Add-Ons) - 100% Client-Side AlpineJS --}}
<div
    x-show="optionOpen"
    class="relative z-[150]"
    style="display: none;"
>
    {{-- Backdrop --}}
    <div
        x-show="optionOpen"
        x-transition:enter="transition-opacity ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm"
        @click="closeOption"
    ></div>

    {{-- Modal Panel (Slide up from bottom) --}}
    <div
        x-show="optionOpen"
        x-transition:enter="transition-transform ease-out duration-300"
        x-transition:enter-start="translate-y-full"
        x-transition:enter-end="translate-y-0"
        x-transition:leave="transition-transform ease-in duration-200"
        x-transition:leave-start="translate-y-0"
        x-transition:leave-end="translate-y-full"
        class="fixed inset-x-0 bottom-0 max-w-xl mx-auto bg-white rounded-t-[2rem] shadow-2xl flex flex-col max-h-[85vh] overflow-hidden"
    >
        <template x-if="optionProduct">
            <div class="flex flex-col flex-1 min-h-0">

                {{-- ===== HEADER ===== --}}
                <div class="px-5 pt-3 pb-4 border-b border-zinc-100 bg-white rounded-t-[2rem] flex flex-col items-center shrink-0">
                    <div class="w-12 h-1.5 bg-zinc-200 rounded-full mb-4"></div>
                    <div class="w-full flex justify-between items-start">
                        <div>
                            <h3 class="font-black text-lg text-zinc-900 leading-tight" x-text="optionProduct.name"></h3>
                            <div class="flex items-center gap-2 mt-2">
                                <span class="text-xs font-bold text-zinc-900 bg-[var(--primary-color)]/20 px-2 py-1 rounded-md"
                                      x-text="optionProduct.formatted_price"></span>
                                <span class="text-[10px] font-medium text-zinc-400 uppercase tracking-wide">
                                    <template x-if="optionProduct.variants && optionProduct.variants.length > 0 && isMulti">
                                        <span x-text="`Pilih Maks ${maxSel} Varian`"></span>
                                    </template>
                                    <template x-if="optionProduct.variants && optionProduct.variants.length > 0 && !isMulti">
                                        <span>Pilih 1 Varian</span>
                                    </template>
                                    <template x-if="!optionProduct.variants || optionProduct.variants.length === 0">
                                        <span>Pilih Add-On</span>
                                    </template>
                                </span>
                            </div>
                        </div>
                        <button @click="closeOption"
                                class="p-2 bg-zinc-50 hover:bg-zinc-100 rounded-full transition-colors active:scale-95">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="text-zinc-600">
                                <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Multi-select progress bar --}}
                    <template x-if="isMulti && optionProduct.variants && optionProduct.variants.length > 0">
                        <div class="w-full mt-3 flex items-center gap-2">
                            <div class="flex-1 h-2 bg-zinc-100 rounded-full overflow-hidden">
                                <div class="h-full bg-[var(--primary-color)] rounded-full transition-all duration-300"
                                     :style="`width: ${(optionSelected.length / maxSel) * 100}%`"></div>
                            </div>
                            <span class="text-[10px] font-black text-zinc-500 tabular-nums whitespace-nowrap"
                                  x-text="`${optionSelected.length}/${maxSel}`"></span>
                        </div>
                    </template>
                </div>

                {{-- ===== SCROLLABLE LIST ===== --}}
                <div class="flex-1 min-h-0 overflow-y-auto overscroll-contain px-5 py-4">
                    <div class="flex flex-col gap-2.5">

                        {{-- ----- VARIANTS ----- --}}
                        <template x-if="optionProduct.variants && optionProduct.variants.length > 0">
                            <div class="flex flex-col gap-2.5">
                                <template x-for="variant in optionProduct.variants" :key="variant.id">
                                    <div
                                        @click="toggleOption(variant.name)"
                                        class="relative flex items-center justify-between p-3.5 rounded-xl border-2 cursor-pointer transition-all active:scale-[0.98]"
                                        :class="isOptionSelected(variant.name)
                                            ? 'border-[var(--primary-color)] bg-[var(--primary-color)]/10 shadow-sm shadow-[var(--primary-color)]/10'
                                            : 'border-zinc-100 bg-zinc-50 hover:bg-zinc-100'"
                                    >
                                        <div>
                                            <span class="block font-bold text-sm"
                                                  :class="isOptionSelected(variant.name) ? 'text-zinc-900' : 'text-zinc-700'"
                                                  x-text="variant.name"></span>
                                            <template x-if="!isMulti">
                                                <span class="block text-xs font-black text-[var(--primary-color)] mt-0.5"
                                                      x-text="formatPrice(variant.price)"></span>
                                            </template>
                                        </div>
                                        {{-- Radio (single) --}}
                                        <template x-if="!isMulti">
                                            <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-colors shrink-0"
                                                 :class="isOptionSelected(variant.name) ? 'border-[var(--primary-color)]' : 'border-zinc-300'">
                                                <div class="w-3 h-3 rounded-full bg-[var(--primary-color)] transition-transform"
                                                     :class="isOptionSelected(variant.name) ? 'scale-100' : 'scale-0'"></div>
                                            </div>
                                        </template>
                                        {{-- Checkbox (multi) --}}
                                        <template x-if="isMulti">
                                            <div class="w-6 h-6 rounded-lg border-2 flex items-center justify-center transition-all shrink-0"
                                                 :class="isOptionSelected(variant.name) ? 'border-[var(--primary-color)] bg-[var(--primary-color)]' : 'border-zinc-300 bg-white'">
                                                <svg x-show="isOptionSelected(variant.name)"
                                                     xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                     stroke-width="3" stroke-linecap="round" stroke-linejoin="round"
                                                     class="text-white">
                                                    <polyline points="20 6 9 17 4 12"/>
                                                </svg>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </template>

                        {{-- ----- ADD-ONS / EXTRAS ----- --}}
                        <template x-if="optionProduct.extras && optionProduct.extras.length > 0">
                            <div>
                                {{-- Divider hanya jika ada variants di atas --}}
                                <template x-if="optionProduct.variants && optionProduct.variants.length > 0">
                                    <div class="flex items-center gap-3 my-3">
                                        <div class="flex-1 h-px bg-zinc-100"></div>
                                        <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Tambahan</span>
                                        <div class="flex-1 h-px bg-zinc-100"></div>
                                    </div>
                                </template>

                                <div class="flex flex-col gap-2.5">
                                    <template x-for="extra in optionProduct.extras" :key="extra.id">
                                        <div
                                            @click="toggleExtra(extra.name)"
                                            class="relative flex items-center justify-between p-3.5 rounded-xl border-2 cursor-pointer transition-all active:scale-[0.98]"
                                            :class="isExtraSelected(extra.name)
                                                ? 'border-[var(--primary-color)] bg-[var(--primary-color)]/10 shadow-sm shadow-[var(--primary-color)]/10'
                                                : 'border-zinc-100 bg-zinc-50 hover:bg-zinc-100'"
                                        >
                                            <div>
                                                <span class="block font-bold text-sm"
                                                      :class="isExtraSelected(extra.name) ? 'text-zinc-900' : 'text-zinc-700'"
                                                      x-text="extra.name"></span>
                                                <span class="block text-xs font-black mt-0.5"
                                                      :class="isExtraSelected(extra.name) ? 'text-[var(--primary-color)]' : 'text-zinc-400'"
                                                      x-text="'+' + formatPrice(extra.price)"></span>
                                            </div>
                                            {{-- Checkbox --}}
                                            <div class="w-6 h-6 rounded-lg border-2 flex items-center justify-center transition-all shrink-0"
                                                 :class="isExtraSelected(extra.name) ? 'border-[var(--primary-color)] bg-[var(--primary-color)]' : 'border-zinc-300 bg-white'">
                                                <svg x-show="isExtraSelected(extra.name)"
                                                     xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                     stroke-width="3" stroke-linecap="round" stroke-linejoin="round"
                                                     class="text-white" style="display:none">
                                                    <polyline points="20 6 9 17 4 12"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                    </div>
                </div>

                {{-- ===== BOTTOM: QTY + SUBMIT ===== --}}
                <div class="shrink-0 p-5 bg-white border-t border-zinc-100 shadow-[0_-10px_30px_rgba(0,0,0,0.05)]">
                    {{-- Qty --}}
                    <div class="flex items-center justify-between mb-4 bg-zinc-50 p-3 rounded-xl border border-zinc-100">
                        <span class="text-xs font-bold text-zinc-500 uppercase tracking-wider ml-2">Jumlah</span>
                        <div class="flex items-center gap-4">
                            <button type="button" @click="optionQty = Math.max(1, optionQty - 1)"
                                    class="w-8 h-8 rounded-lg bg-white border border-zinc-200 flex items-center justify-center hover:bg-zinc-100 transition-all active:scale-90">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                     stroke-linejoin="round" class="text-zinc-600">
                                    <line x1="5" x2="19" y1="12" y2="12"/>
                                </svg>
                            </button>
                            <span class="font-black text-lg w-6 text-center tabular-nums" x-text="optionQty"></span>
                            <button type="button" @click="optionQty++"
                                    class="w-8 h-8 rounded-lg bg-zinc-900 text-white flex items-center justify-center hover:bg-zinc-800 transition-all active:scale-90">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <button
                        @click="confirmOption"
                        :disabled="!optionValid"
                        class="w-full py-4 rounded-xl font-black text-xs uppercase tracking-widest text-zinc-900 flex justify-center items-center gap-2 transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed shadow-xl shadow-[var(--primary-color)]/20"
                        :class="optionValid ? 'bg-[var(--primary-color)] hover:brightness-110' : 'bg-zinc-200 text-zinc-500 shadow-none'"
                    >
                        <template x-if="!optionValid">
                            <span x-text="isMulti ? `Pilih ${maxSel} Varian` : 'Pilih Varian'"></span>
                        </template>
                        <template x-if="optionValid">
                            <span class="flex items-center gap-1.5">
                                <span x-text="`Tambahkan — ${formatPrice(optionTotalPrice)}`"></span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/>
                                </svg>
                            </span>
                        </template>
                    </button>
                </div>

            </div>
        </template>
    </div>
</div>
