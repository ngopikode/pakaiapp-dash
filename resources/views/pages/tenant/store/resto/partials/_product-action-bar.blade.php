{{-- Fixed Bottom Action Bar: stepper, tombol tambah/varian/addon, tombol checkout --}}
<div class="fixed bottom-0 left-0 right-0 z-[120] bg-[var(--surface)] border-t border-[var(--border)] px-5 py-4 shadow-[0_-10px_30px_rgba(0,0,0,0.05)]">
    <div class="max-w-xl mx-auto flex flex-col gap-2.5">

        {{-- Stepper: item sederhana (tanpa varian/addon) yang sudah ada di cart --}}
        <template x-if="qtyInCart > 0 && !product.has_variants && !(product.extras && product.extras.length > 0)">
            <div class="flex items-center justify-between rounded-2xl p-1.5 w-full bg-[var(--surface)] shadow-sm border border-[var(--border)]">
                <button @click="updateQty(product.name, -1)"
                        class="w-12 h-12 flex items-center justify-center rounded-xl text-[var(--foreground)] hover:bg-[var(--bg-soft)] transition-all active:scale-90">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" x2="19" y1="12" y2="12"/>
                    </svg>
                </button>
                <span class="font-black text-lg text-[var(--foreground)] tabular-nums" x-text="qtyInCart"></span>
                <button @click="addToCart(product)"
                        class="w-12 h-12 flex items-center justify-center text-black bg-[var(--primary-color)] hover:brightness-110 rounded-xl transition-all active:scale-90">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" x2="12" y1="5" y2="19"/>
                        <line x1="5" x2="19" y1="12" y2="12"/>
                    </svg>
                </button>
            </div>
        </template>

        {{-- Tombol Tambah / Pilih Varian / Pilih Add-On --}}
        <template x-if="qtyInCart === 0 || product.has_variants || (product.extras && product.extras.length > 0)">
            <button
                @click="(product.has_variants || (product.extras && product.extras.length > 0)) ? openOption(product) : addToCart(product)"
                :disabled="!product.is_active"
                class="w-full py-4 rounded-2xl text-sm font-black uppercase tracking-widest transition-all active:scale-95 flex items-center justify-center gap-2"
                :class="product.is_active ? 'bg-[var(--primary-color)] text-black shadow-xl shadow-[var(--primary-color)]/20 hover:brightness-110' : 'bg-[var(--bg-soft)] text-[var(--text-secondary)] border border-[var(--border)] cursor-not-allowed'"
            >
                <template x-if="!product.is_active"><span>Produk Habis</span></template>
                <template x-if="product.is_active && product.has_variants">
                    <span class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                            <line x1="3" x2="21" y1="6" y2="6"/>
                            <path d="M16 10a4 4 0 0 1-8 0"/>
                        </svg>
                        Pilih Opsi Varian
                    </span>
                </template>
                <template x-if="product.is_active && !product.has_variants && product.extras && product.extras.length > 0">
                    <span class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" x2="12" y1="8" y2="16"/>
                            <line x1="8" x2="16" y1="12" y2="12"/>
                        </svg>
                        Pilih Add-On
                    </span>
                </template>
                <template x-if="product.is_active && !product.has_variants && !(product.extras && product.extras.length > 0)">
                    <span class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" x2="12" y1="5" y2="19"/>
                            <line x1="5" x2="19" y1="12" y2="12"/>
                        </svg>
                        Tambah ke Keranjang
                    </span>
                </template>
            </button>
        </template>

        {{-- Tombol Checkout (tampil saat cart tidak kosong) --}}
        <template x-if="totalQty > 0">
            <button
                @click="openCheckout()"
                class="max-w-xl mx-auto w-full bg-zinc-900 text-zinc-50 p-4 rounded-2xl shadow-2xl flex justify-between items-center border border-[var(--primary-color)]/30 ring-1 ring-[var(--primary-color)]/20 relative overflow-hidden group hover:border-[var(--primary-color)] transition-all duration-300 active:scale-[0.98]"
            >
                <div class="absolute inset-0 bg-[var(--primary-color)]/5 group-hover:bg-[var(--primary-color)]/10 transition-colors duration-500"></div>
                <div class="relative flex items-center gap-3.5">
                    <div class="bg-[var(--primary-color)] text-black w-11 h-11 rounded-xl flex items-center justify-center font-black text-sm shadow-md shadow-[var(--primary-color)]/30"
                         x-text="totalQty"></div>
                    <div class="text-left">
                        <span class="block text-[9px] font-bold uppercase tracking-widest text-zinc-400 mb-0.5">Total Estimasi</span>
                        <span class="font-bold text-lg text-white font-mono leading-none" x-text="formatPrice(totalCart)"></span>
                    </div>
                </div>
                <div class="relative flex items-center gap-2 pr-1">
                    <span class="text-[10px] font-black uppercase tracking-widest text-[var(--primary-color)] group-hover:text-white transition-colors">Checkout</span>
                    <div class="bg-[var(--surface)]/10 p-1.5 rounded-full group-hover:bg-[var(--primary-color)]/20 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m9 18 6-6-6-6"/>
                        </svg>
                    </div>
                </div>
            </button>
        </template>
    </div>
</div>
