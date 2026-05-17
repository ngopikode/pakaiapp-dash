{{-- Product Detail Modal — uses global scope from store.blade.php --}}
<div
    x-show="detailOpen"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-full scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
    x-transition:leave-end="opacity-0 translate-y-full scale-95"
    class="fixed inset-0 z-[100] bg-zinc-100 overflow-y-auto overflow-x-hidden"
    @scroll="handleDetailScroll($event)"
    style="display: none;"
>
    <template x-if="detailProduct">
        <div>
            {{-- Dynamic Header --}}
            <header
                class="fixed top-0 left-0 right-0 z-[110] transition-all duration-300 px-4 py-3 flex items-center justify-between max-w-xl mx-auto"
                :class="detailScrolled ? 'bg-white/90 backdrop-blur-xl border-b border-zinc-100 shadow-sm' : 'bg-transparent'"
            >
                <button
                    @click="closeDetail"
                    class="p-2.5 rounded-full transition-all duration-300 active:scale-90 border"
                    :class="detailScrolled ? 'bg-white text-zinc-900 border-zinc-200' : 'bg-black/20 backdrop-blur-md text-white border-white/20'"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
                </button>

                <h2
                    class="text-sm font-black text-zinc-900 truncate max-w-[200px] transition-all duration-300"
                    :class="detailScrolled ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-2'"
                    x-text="detailProduct.name"
                ></h2>

                <div class="flex items-center gap-2">
                    <a
                        :href="'/menu/' + detailProduct.id + '/story'"
                        target="_blank"
                        rel="noreferrer"
                        class="p-2.5 rounded-full transition-all duration-300 active:scale-90 border hover:bg-[#25D366] hover:text-white hover:border-[#25D366]"
                        :class="detailScrolled ? 'bg-white text-zinc-900 border-zinc-200' : 'bg-black/20 backdrop-blur-md text-white border-white/20'"
                        aria-label="Share ke Status WA"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.888-.788-1.489-1.761-1.663-2.06-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                    </a>
                    <button
                        @click="navigator.share ? navigator.share({title: detailProduct.name, url: window.location.origin + '/menu/' + detailProduct.id}) : navigator.clipboard.writeText(window.location.origin + '/menu/' + detailProduct.id)"
                        class="p-2.5 rounded-full transition-all duration-300 active:scale-90 border"
                        :class="detailScrolled ? 'bg-white text-zinc-900 border-zinc-200' : 'bg-black/20 backdrop-blur-md text-white border-white/20'"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" x2="15.42" y1="13.51" y2="17.49"/><line x1="15.41" x2="8.59" y1="6.51" y2="10.49"/></svg>
                    </button>
                </div>
            </header>

            {{-- Parallax Hero Image --}}
            <div class="sticky top-0 w-full h-[45vh] z-0">
                <template x-if="detailProduct.image">
                    <div class="relative w-full h-full overflow-hidden bg-zinc-900">
                        <div class="absolute inset-0 bg-zinc-800 animate-pulse"></div>
                        <img
                            :src="detailProduct.image"
                            :alt="detailProduct.name"
                            class="absolute inset-0 w-full h-full object-cover transition-opacity duration-700"
                            onload="this.previousElementSibling.style.display='none'; this.style.opacity=1"
                            style="opacity:0"
                            loading="eager"
                        />
                    </div>
                </template>
                <template x-if="!detailProduct.image">
                    <div class="w-full h-full bg-zinc-100 flex flex-col items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-300"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                        <span class="text-[10px] font-bold text-zinc-300 uppercase tracking-widest">No Image</span>
                    </div>
                </template>
                <div class="absolute inset-0 bg-gradient-to-b from-black/40 via-transparent to-transparent opacity-60"></div>
            </div>

            {{-- Scrollable Content --}}
            <main class="relative z-10 max-w-xl mx-auto bg-white min-h-[60vh] rounded-t-[2rem] -mt-8 pb-40 shadow-[0_-10px_40px_rgba(0,0,0,0.1)]">
                <div class="w-full flex justify-center pt-3 pb-5">
                    <div class="w-12 h-1.5 bg-zinc-200 rounded-full"></div>
                </div>

                <div class="px-6">
                    <div class="flex justify-between items-start gap-4">
                        <div class="flex-1">
                            <template x-if="detailProduct.category">
                                <span class="inline-block px-3 py-1.5 rounded-xl bg-zinc-100 text-zinc-600 text-[10px] font-black uppercase tracking-widest mb-3 border border-zinc-200/60" x-text="detailProduct.category"></span>
                            </template>
                            <h1 class="text-[1.75rem] font-black text-zinc-900 leading-tight tracking-tight" x-text="detailProduct.name"></h1>
                        </div>
                        <div class="text-right pt-1">
                            <div class="text-2xl font-black text-[var(--primary-color)] font-mono tracking-tighter" x-text="detailProduct.formatted_price"></div>
                        </div>
                    </div>

                    <div class="h-px bg-zinc-100 my-6"></div>

                    <div class="space-y-3">
                        <h3 class="text-xs font-black uppercase tracking-widest text-zinc-900 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400"><line x1="3" x2="21" y1="6" y2="6"/><line x1="3" x2="21" y1="12" y2="12"/><line x1="3" x2="21" y1="18" y2="18"/></svg>
                            Deskripsi Menu
                        </h3>
                        <p class="text-sm text-zinc-500 leading-relaxed" x-text="detailProduct.description || 'Tidak ada deskripsi untuk menu ini.'"></p>
                    </div>
                </div>
            </main>

            {{-- Fixed Bottom Action Bar --}}
            <div class="fixed bottom-0 left-0 right-0 z-[120] bg-white border-t border-zinc-100 px-5 py-4 shadow-[0_-10px_30px_rgba(0,0,0,0.05)]">
                <div class="max-w-xl mx-auto flex flex-col gap-2.5">

                    {{-- Stepper for items already in cart (non-variant) --}}
                    <template x-if="detailQtyInCart > 0 && !detailProduct.has_variants">
                        <div class="flex items-center justify-between rounded-2xl p-1.5 w-full bg-zinc-900 shadow-xl">
                            <button @click="updateQty(detailProduct.name, -1)" class="w-12 h-12 flex items-center justify-center rounded-xl text-white hover:bg-zinc-700 transition-all active:scale-90">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" x2="19" y1="12" y2="12"/></svg>
                            </button>
                            <span class="font-black text-lg text-white tabular-nums" x-text="detailQtyInCart"></span>
                            <button @click="addToCart(detailProduct)" class="w-12 h-12 flex items-center justify-center text-zinc-900 bg-[var(--primary-color)] hover:brightness-110 rounded-xl transition-all active:scale-90">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg>
                            </button>
                        </div>
                    </template>

                    {{-- Add / Variant button --}}
                    <template x-if="detailQtyInCart === 0 || detailProduct.has_variants">
                        <button
                            @click="detailProduct.has_variants ? openOption(detailProduct) : addToCart(detailProduct)"
                            :disabled="!detailProduct.is_active"
                            class="w-full py-4 rounded-2xl text-sm font-black uppercase tracking-widest transition-all active:scale-95 flex items-center justify-center gap-2"
                            :class="detailProduct.is_active ? 'bg-zinc-900 text-white shadow-xl hover:bg-zinc-800' : 'bg-zinc-200 text-zinc-400 cursor-not-allowed'"
                        >
                            <template x-if="!detailProduct.is_active">
                                <span>Produk Habis</span>
                            </template>
                            <template x-if="detailProduct.is_active && detailProduct.has_variants">
                                <span class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[var(--primary-color)]"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" x2="21" y1="6" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                                    Pilih Opsi Varian
                                </span>
                            </template>
                            <template x-if="detailProduct.is_active && !detailProduct.has_variants">
                                <span class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[var(--primary-color)]"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg>
                                    Tambah ke Keranjang &bull; <span x-text="detailProduct.formatted_price"></span>
                                </span>
                            </template>
                        </button>
                    </template>

                    {{-- Global Checkout Button (appears if cart has items) --}}
                    <template x-if="totalQty > 0">
                        <button
                            @click="closeDetail(); setTimeout(() => openCheckout(), 150)"
                            class="w-full bg-gradient-to-r from-zinc-900 to-zinc-800 text-white p-4 rounded-2xl shadow-lg flex justify-between items-center border border-white/10 relative overflow-hidden group hover:shadow-xl transition-all duration-300 active:scale-[0.98] animate-slide-up"
                        >
                            <div class="absolute inset-0 bg-[var(--primary-color)]/5 group-hover:bg-[var(--primary-color)]/10 transition-colors duration-500"></div>
                            <div class="relative flex items-center gap-3.5">
                                <div class="bg-[var(--primary-color)] text-zinc-900 w-11 h-11 rounded-xl flex items-center justify-center font-black text-sm shadow-md" x-text="totalQty"></div>
                                <div class="text-left">
                                    <span class="block text-[9px] font-bold uppercase tracking-widest text-zinc-400 mb-0.5">Total Estimasi</span>
                                    <span class="font-bold text-lg text-white font-mono leading-none" x-text="formatPrice(totalCart)"></span>
                                </div>
                            </div>
                            <div class="relative flex items-center gap-2 pr-1">
                                <span class="text-[10px] font-black uppercase tracking-widest">Checkout</span>
                                <div class="bg-white/10 p-1.5 rounded-full group-hover:bg-[var(--primary-color)]/20 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                                </div>
                            </div>
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </template>
</div>