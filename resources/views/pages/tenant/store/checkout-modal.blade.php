{{-- Checkout Modal (Save to DB) - 100% Client-Side, Uses Global Scope --}}
<div
    x-show="checkoutOpen"
    class="relative z-[150]"
    style="display: none;"
>
    {{-- Backdrop --}}
    <div
        x-show="checkoutOpen"
        x-transition:enter="transition-opacity ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm"
        @click="closeCheckout"
    ></div>

    {{-- Modal Panel --}}
    <div
        x-show="checkoutOpen"
        x-transition:enter="transition-transform ease-out duration-300"
        x-transition:enter-start="translate-y-full"
        x-transition:enter-end="translate-y-0"
        x-transition:leave="transition-transform ease-in duration-200"
        x-transition:leave-start="translate-y-0"
        x-transition:leave-end="translate-y-full"
        class="fixed inset-0 bg-white z-[110] flex flex-col mt-10 rounded-t-[2rem] overflow-hidden"
    >
        {{-- Header --}}
        <div class="p-5 flex justify-between items-center border-b border-zinc-100 bg-white sticky top-0 z-10 pt-6">
            <div>
                <h2 class="text-lg font-black tracking-tight text-zinc-900 uppercase">Pesanan Kamu</h2>
                <p class="text-[10px] text-zinc-400 font-medium">Pastikan pesanan sudah sesuai ya</p>
            </div>
            <button @click="closeCheckout" class="p-2 bg-zinc-50 rounded-full hover:bg-zinc-100 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     class="text-zinc-900">
                    <path d="M18 6 6 18"/>
                    <path d="m6 6 12 12"/>
                </svg>
            </button>
        </div>

        {{-- ===== ORDER SUCCESS STATE (UX UPGRADED) ===== --}}
        <template x-if="orderSuccess">
            {{-- Kita inject x-data lokal khusus di screen success untuk handle state copy text --}}
            <div
                x-data="{ copied: false }"
                class="flex-1 flex flex-col items-center justify-center p-6 text-center max-w-sm mx-auto w-full animate-fade-in-scale"
            >
                {{-- Success Icon Animated --}}
                <div
                    class="w-16 h-16 rounded-full bg-emerald-50 flex items-center justify-center mb-5 ring-8 ring-emerald-50/50">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                         class="text-emerald-500">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <path d="m9 11 3 3L22 4"/>
                    </svg>
                </div>

                <h3 class="text-xl font-black text-zinc-900 mb-1">Pesanan Terkirim! 🎉</h3>
                <p class="text-xs font-medium text-zinc-400 mb-1">Pesanan kamu sudah masuk ke sistem toko.</p>
                <p class="text-xs font-medium text-zinc-500 mb-6">
                    Yuk, langsung ke kasir untuk konfirmasi dan selesaikan pembayaranmu.
                </p>

                {{-- Interactive Invoice Card --}}
                <div
                    class="bg-zinc-50 rounded-2xl p-4 border border-zinc-100 w-full mb-3 flex items-center justify-between gap-4">
                    <div class="text-left min-w-0">
                        <span class="text-[9px] font-black text-zinc-400 uppercase tracking-widest block mb-0.5">Kode Invoice</span>
                        <span class="text-sm font-mono font-black text-zinc-900 block truncate"
                              x-text="orderSuccess.invoiceCode"></span>
                    </div>
                    <button
                        @click="
                            navigator.clipboard.writeText(orderSuccess.invoiceCode);
                            copied = true;
                            setTimeout(() => copied = false, 2000);
                        "
                        class="shrink-0 flex items-center gap-1 px-3 py-2 bg-white rounded-xl border border-zinc-200/60 text-[10px] font-black uppercase tracking-wider transition-all duration-200 active:scale-95 shadow-sm shadow-zinc-100"
                        :class="copied ? 'text-emerald-600 border-emerald-200 bg-emerald-50/30' : 'text-zinc-600 hover:bg-zinc-50'"
                    >
                        <span x-text="copied ? 'Tersalin!' : 'Salin'"></span>
                        <svg x-show="!copied" xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <rect width="14" height="14" x="8" y="8" rx="2" ry="2"/>
                            <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/>
                        </svg>
                        <svg x-show="copied" xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round" class="text-emerald-500"
                             style="display: none;">
                            <path d="M20 6 9 17l-5-5"/>
                        </svg>
                    </button>
                </div>

                {{-- Total Bayar Badge --}}
                <div class="bg-zinc-900 text-white rounded-2xl p-4 w-full mb-8 flex items-center justify-between">
                    <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Total Tagihan</span>
                    <span class="text-base font-black font-mono text-[var(--primary-color)]"
                          x-text="orderSuccess.total"></span>
                </div>

                {{-- Action Buttons Container --}}
                <div class="w-full space-y-2">
                    {{-- 1. Primary Action: Lihat Detail Nota (_blank) --}}
                    <a
                        :href="'/invoice/' + orderSuccess.invoiceCode"
                        target="_blank"
                        class="w-full bg-[var(--primary-color)] text-zinc-900 py-4 mb-2 rounded-xl font-black text-xs uppercase tracking-widest hover:brightness-105 transition-all active:scale-[0.98] flex items-center justify-center gap-2 shadow-lg shadow-[var(--primary-color)]/20"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/>
                            <path d="M14 2v4a2 2 0 0 0 2 2h4"/>
                            <path d="M10 9H8"/>
                            <path d="M16 13H8"/>
                            <path d="M16 17H8"/>
                        </svg>
                        Lihat Invoice Detail
                    </a>

                    {{-- 2. Secondary Action: Kembali Browsing Menu --}}
                    <button
                        @click="closeCheckout"
                        class="w-full bg-zinc-50 text-zinc-500 py-3.5 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-zinc-100 border border-zinc-200/40 transition-all active:scale-[0.98]"
                    >
                        Kembali ke Menu
                    </button>
                </div>
            </div>
        </template>

        {{-- ===== CART & FORM STATE ===== --}}
        <template x-if="!orderSuccess">
            <div class="flex-1 min-h-0 flex flex-col overflow-hidden">
                {{-- Cart Items --}}
                <div class="flex-1 min-h-0 overflow-y-auto overscroll-contain p-5 pb-4">
                    <template x-if="cart.length === 0">
                        <div class="text-center py-10 text-zinc-400 text-sm font-bold">Keranjang masih kosong nih</div>
                    </template>

                    <template x-if="cart.length > 0">
                        <div>
                            <template x-for="item in cart" :key="item.cartName">
                                <div
                                    class="flex justify-between items-start mb-6 pb-6 border-b border-zinc-50 last:border-0 animate-slide-up">
                                    <div class="flex-1 pr-4">
                                        <h4 class="font-bold text-sm text-zinc-900 leading-tight"
                                            x-text="item.cartName"></h4>
                                        <p class="text-xs font-medium text-[var(--primary-color)] mt-1"
                                           x-text="formatPrice(item.price * item.qty)"></p>
                                    </div>
                                    <div
                                        class="flex items-center gap-3 bg-zinc-50 p-1 rounded-lg border border-zinc-100">
                                        <button @click="updateQty(item.cartName, -1)"
                                                class="w-7 h-7 rounded-md bg-white text-zinc-900 flex items-center justify-center shadow-sm hover:bg-zinc-100 font-bold text-lg leading-none">
                                            -
                                        </button>
                                        <span class="font-black text-xs w-4 text-center tabular-nums"
                                              x-text="item.qty"></span>
                                        <button @click="updateQty(item.cartName, 1)"
                                                class="w-7 h-7 rounded-md bg-zinc-900 text-white flex items-center justify-center shadow-sm hover:bg-zinc-800 font-bold text-lg leading-none">
                                            +
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                {{-- Bottom Form --}}
                <div class="shrink-0 p-6 bg-white border-t border-zinc-100 shadow-[0_-10px_40px_rgba(0,0,0,0.05)]">
                    {{-- Order Type Options --}}
                    <div class="grid grid-cols-{{ count($orderTypes ?? [['id'=>'takeaway']]) }} gap-2 mb-5">
                        @foreach($orderTypes ?? [['id'=>'takeaway', 'label'=>'Takeaway']] as $type)
                            <button
                                @click="orderType = '{{ $type['id'] }}'"
                                class="py-3 rounded-xl border-2 text-xs font-extrabold transition-all flex flex-col items-center justify-center gap-1.5"
                                :class="orderType === '{{ $type['id'] }}' ? 'bg-[var(--primary-color)] text-black border-[var(--primary-color)]' : 'bg-zinc-50 text-zinc-400 border-transparent'"
                            >
                                {{ $type['label'] }}
                            </button>
                        @endforeach
                    </div>

                    {{-- Inputs --}}
                    <div class="space-y-3 mb-4">
                        <div class="relative">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="absolute left-4 top-1/2 -translate-y-1/2 text-zinc-400">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                            <input
                                x-model="customerName"
                                type="text"
                                placeholder="Nama Pemesan"
                                class="w-full pl-10 pr-4 py-3.5 rounded-xl bg-zinc-50 border border-zinc-100 text-sm font-bold outline-none focus:border-[var(--primary-color)] focus:ring-1 focus:ring-[var(--primary-color)] transition-all"
                            />
                        </div>
                        <div class="relative">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="absolute left-4 top-1/2 -translate-y-1/2 text-zinc-400">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            </svg>
                            <input
                                x-model="customerInfo"
                                type="text"
                                :placeholder="orderType === 'dinein' ? 'Nomor Meja' : (orderType === 'takeaway' ? 'Catatan (misal: bungkus pisah)' : 'Alamat Lengkap Pengantaran')"
                                class="w-full pl-10 pr-4 py-3.5 rounded-xl bg-zinc-50 border border-zinc-100 text-sm font-bold outline-none focus:border-[var(--primary-color)] focus:ring-1 focus:ring-[var(--primary-color)] transition-all"
                            />
                        </div>
                    </div>

                    {{-- Total & Submit --}}
                    <div class="flex items-center justify-between mb-3 px-1">
                        <span class="text-xs font-bold text-zinc-400 uppercase tracking-wider">Total</span>
                        <span class="text-lg font-black text-zinc-900" x-text="formatPrice(totalCart)"></span>
                    </div>

                    <button
                        @click="processOrder"
                        :disabled="cart.length === 0 || checkoutLoading"
                        class="w-full py-4 rounded-xl font-black text-xs uppercase flex items-center justify-center gap-2 transition-all active:scale-95 disabled:cursor-not-allowed"
                        :class="cart.length === 0 ? 'bg-zinc-200 text-zinc-500' : 'bg-zinc-900 hover:bg-zinc-800 text-white shadow-lg shadow-zinc-900/20'"
                    >
                        <span x-show="!checkoutLoading" class="flex items-center gap-2">
                            <template x-if="cart.length === 0">
                                <span class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                         stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21"
                                                                                                       r="1"/><path
                                            d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                                    Keranjang Kosong
                                </span>
                            </template>
                            <template x-if="cart.length > 0">
                                <span class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                         stroke-linejoin="round"><path
                                            d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path
                                            d="M16 10a4 4 0 0 1-8 0"/></svg>
                                    Pesan Sekarang
                                </span>
                            </template>
                        </span>
                        <span x-show="checkoutLoading" class="flex items-center gap-2" style="display: none;">
                            <svg class="animate-spin w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                 viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10"
                                                             stroke="currentColor" stroke-width="4"></circle><path
                                    class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            Memproses...
                        </span>
                    </button>
                </div>
            </div>
        </template>
    </div>
</div>
