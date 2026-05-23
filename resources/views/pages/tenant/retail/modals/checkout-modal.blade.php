{{-- Checkout Modal (Retail) - 100% Client-Side --}}
<div x-show="checkoutOpen" x-data="{ showPaymentSelector: false }" class="relative z-[150]" style="display: none;">
    <div x-show="checkoutOpen" x-transition:enter="transition-opacity ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="closeCheckout"></div>

    <div x-show="checkoutOpen" x-transition:enter="transition-transform ease-out duration-300" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0" x-transition:leave="transition-transform ease-in duration-200" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full" class="fixed inset-0 bg-white z-[110] flex flex-col mt-10 rounded-t-[2rem] overflow-hidden">
        
        <div class="p-5 flex justify-between items-center border-b border-zinc-100 bg-white sticky top-0 z-10 pt-6">
            <div class="flex items-center gap-3">
                <button x-show="checkoutStep === 2" @click="showPaymentSelector ? showPaymentSelector = false : checkoutStep = 1" class="p-2 bg-zinc-50 rounded-full hover:bg-zinc-100 transition-all shrink-0 active:scale-90" style="display: none;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-900"><path d="m15 18-6-6 6-6"/></svg>
                </button>
                <div>
                    <h2 x-show="checkoutStep === 1" class="text-lg font-black tracking-tight text-zinc-900 uppercase">Keranjang Belanja</h2>
                    <p x-show="checkoutStep === 1" class="text-[10px] text-zinc-400 font-semibold">Cek kembali barang bawaanmu</p>
                    <h2 x-show="checkoutStep === 2" class="text-lg font-black tracking-tight text-zinc-900 uppercase" style="display: none;" x-text="showPaymentSelector ? 'Pilih Metode Bayar' : 'Pengiriman & Pembayaran'"></h2>
                    <p x-show="checkoutStep === 2" class="text-[10px] text-zinc-400 font-semibold" style="display: none;" x-text="showPaymentSelector ? 'Pilih metode pembayaran yang kamu inginkan' : 'Lengkapi detail tujuan'"></p>
                </div>
            </div>
            <button @click="closeCheckout" class="p-2 bg-zinc-50 rounded-full hover:bg-zinc-100 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-900"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
        </div>

        <template x-if="orderSuccess">
            <div x-data="{ copied: false }" class="flex-1 flex flex-col items-center justify-center p-6 text-center max-w-sm mx-auto w-full animate-fade-in-scale">
                <div class="w-16 h-16 rounded-full bg-blue-50 flex items-center justify-center mb-5 ring-8 ring-blue-50/50">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-blue-500"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
                </div>
                <h3 class="text-xl font-black text-zinc-900 mb-1">Pesanan Berhasil! 🎉</h3>
                <p class="text-xs font-medium text-zinc-500 mb-6">Terima kasih telah berbelanja di toko kami.</p>

                <div class="bg-zinc-50 rounded-2xl p-4 border border-zinc-100 w-full mb-3 flex items-center justify-between gap-4">
                    <div class="text-left min-w-0">
                        <span class="text-[9px] font-black text-zinc-400 uppercase tracking-widest block mb-0.5">Kode Invoice</span>
                        <span class="text-sm font-mono font-black text-zinc-900 block truncate" x-text="orderSuccess.invoiceCode"></span>
                    </div>
                    <button @click="navigator.clipboard.writeText(orderSuccess.invoiceCode); copied = true; setTimeout(() => copied = false, 2000);" class="shrink-0 flex items-center gap-1 px-3 py-2 bg-white rounded-xl border border-zinc-200/60 text-[10px] font-black uppercase tracking-wider transition-all duration-200 active:scale-95 shadow-sm shadow-zinc-100" :class="copied ? 'text-emerald-600 border-emerald-200 bg-emerald-50/30' : 'text-zinc-600 hover:bg-zinc-50'">
                        <span x-text="copied ? 'Tersalin!' : 'Salin'"></span>
                        <svg x-show="!copied" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                        <svg x-show="copied" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-500" style="display: none;"><path d="M20 6 9 17l-5-5"/></svg>
                    </button>
                </div>

                <div class="bg-zinc-900 text-white rounded-2xl p-4 w-full mb-8 flex items-center justify-between">
                    <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Total Tagihan</span>
                    <span class="text-base font-black font-mono text-[var(--primary-color)]" x-text="orderSuccess.total"></span>
                </div>

                <div class="w-full space-y-2">
                    <a :href="'/invoice/' + orderSuccess.invoiceCode" target="_blank" class="w-full bg-[var(--primary-color)] text-white py-4 rounded-xl font-black text-xs uppercase tracking-widest hover:brightness-105 transition-all active:scale-[0.98] flex items-center justify-center gap-2 shadow-lg shadow-[var(--primary-color)]/20">
                        Lihat Invoice
                    </a>
                    <button @click="window.open(orderSuccess.waUrl, '_blank')" type="button" class="w-full bg-[#25D366] text-white py-4 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-[#20bd5a] transition-all active:scale-[0.98] flex items-center justify-center gap-2 shadow-lg shadow-[#25D366]/20">
                        Konfirmasi via WA
                    </button>
                    <button @click="closeCheckout" class="w-full bg-zinc-50 text-zinc-500 py-3.5 mt-2 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-zinc-100 border border-zinc-200/40 transition-all active:scale-[0.98]">
                        Kembali Belanja
                    </button>
                </div>
            </div>
        </template>

        <template x-if="!orderSuccess">
            <div class="flex-1 min-h-0 flex flex-col overflow-hidden">
                <div x-show="checkoutStep === 1" class="flex-1 min-h-0 flex flex-col overflow-hidden" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                    <div class="flex-1 min-h-0 overflow-y-auto overscroll-contain p-5 pb-4">
                        <template x-if="cart.length === 0">
                            <div class="text-center py-10 text-zinc-400 text-sm font-bold">Keranjang masih kosong</div>
                        </template>

                        <template x-if="cart.length > 0">
                            <div>
                                <template x-for="item in cart" :key="item.cartName">
                                    <div class="flex justify-between items-start mb-6 pb-6 border-b border-zinc-50 last:border-0 animate-slide-up rounded-xl transition-all" :class="item.unavailable ? 'bg-red-50/60 border border-red-100 px-3 pt-3 -mx-1' : ''">
                                        <div class="flex-1 pr-4 min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap mb-0.5">
                                                <h4 class="font-bold text-sm leading-tight" :class="item.unavailable ? 'text-zinc-400 line-through' : 'text-zinc-900'" x-text="item.cartName"></h4>
                                                <template x-if="item.unavailable"><span class="inline-flex items-center gap-1 bg-red-100 text-red-500 text-[9px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full shrink-0">Tidak Tersedia</span></template>
                                            </div>
                                            <p class="text-xs font-medium mt-1" :class="item.unavailable ? 'text-zinc-300' : 'text-blue-600'" x-text="formatPrice(item.price * item.qty)"></p>
                                        </div>
                                        <template x-if="item.unavailable">
                                            <button @click="updateQty(item.cartName, -item.qty)" class="w-8 h-8 rounded-lg bg-red-50 border border-red-100 text-red-400 flex items-center justify-center hover:bg-red-100 transition-colors active:scale-90 shrink-0"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="m19 6-.867 12.142A2 2 0 0 1 16.138 20H7.862a2 2 0 0 1-1.995-1.858L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg></button>
                                        </template>
                                        <template x-if="!item.unavailable">
                                            <div class="flex items-center gap-3 bg-zinc-50 p-1 rounded-lg border border-zinc-100">
                                                <button @click="updateQty(item.cartName, -1)" class="w-7 h-7 rounded-md bg-white text-zinc-900 flex items-center justify-center shadow-sm hover:bg-zinc-100 font-bold text-lg leading-none">-</button>
                                                <span class="font-black text-xs w-4 text-center tabular-nums" x-text="item.qty"></span>
                                                <button @click="updateQty(item.cartName, 1)" class="w-7 h-7 rounded-md bg-zinc-900 text-white flex items-center justify-center shadow-sm hover:bg-zinc-800 font-bold text-lg leading-none">+</button>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>

                    <div class="shrink-0 p-6 bg-white border-t border-zinc-100 shadow-[0_-10px_40px_rgba(0,0,0,0.05)]">
                        <div class="grid grid-cols-{{ count($orderTypes ?? [['id'=>'delivery']]) }} gap-2 mb-5">
                            @foreach($orderTypes ?? [['id'=>'delivery', 'label'=>'Dikirim']] as $type)
                                <button @click="orderType = '{{ $type['id'] }}'" class="py-3 rounded-xl border-2 text-xs font-extrabold transition-all flex flex-col items-center justify-center gap-1.5" :class="orderType === '{{ $type['id'] }}' ? 'bg-[var(--primary-color)] text-white border-[var(--primary-color)]' : 'bg-zinc-50 text-zinc-400 border-transparent'">
                                    {{ $type['label'] }}
                                </button>
                            @endforeach
                        </div>

                        <div class="flex items-center justify-between mb-4 px-1">
                            <span class="text-xs font-bold text-zinc-400 uppercase tracking-wider">Subtotal</span>
                            <span class="text-base font-black text-zinc-900" x-text="formatPrice(totalCart)"></span>
                        </div>

                        <button @click="nextStep" :disabled="cart.length === 0 || cart.some(i => i.unavailable)" class="w-full py-4 rounded-xl font-black text-xs uppercase flex items-center justify-center gap-2 transition-all active:scale-95 disabled:cursor-not-allowed shadow-md hover:shadow-lg" :class="cart.length === 0 || cart.some(i => i.unavailable) ? 'bg-zinc-200 text-zinc-400' : 'bg-zinc-900 hover:bg-zinc-800 text-white shadow-zinc-900/10'">
                            <span>Lanjut Checkout</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>

                <div x-show="checkoutStep === 2" class="flex-1 min-h-0 flex flex-col overflow-hidden" style="display: none;" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                    <div x-show="!showPaymentSelector" class="flex-1 min-h-0 flex flex-col overflow-hidden">
                        <div class="flex-1 min-h-0 overflow-y-auto overscroll-contain p-5 pb-4">
                            <div class="mb-5">
                                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-2">Informasi Penerima</p>
                                <div class="space-y-3">
                                    <div class="relative">
                                        <input x-model="customerName" type="text" placeholder="Nama Lengkap" class="w-full px-4 py-3.5 rounded-xl bg-zinc-50 border border-zinc-100 text-sm font-bold outline-none focus:border-[var(--primary-color)] transition-all"/>
                                    </div>
                                    <div x-show="selectedPaymentMethod !== 'cash'" class="relative" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                                        <input x-model="customerEmail" type="email" placeholder="Email (opsional)" class="w-full px-4 py-3.5 rounded-xl bg-zinc-50 border border-zinc-100 text-sm font-bold outline-none focus:border-[var(--primary-color)] transition-all"/>
                                    </div>
                                    <div class="relative">
                                        <textarea x-model="customerInfo" rows="3" :placeholder="orderType === 'takeaway' ? 'Catatan Tambahan (Opsional)' : 'Alamat Lengkap Pengiriman'" class="w-full px-4 py-3.5 rounded-xl bg-zinc-50 border border-zinc-100 text-sm font-bold outline-none focus:border-[var(--primary-color)] transition-all resize-none"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-2">Metode Pembayaran</p>
                                <div @click="showPaymentSelector = true" class="p-4 bg-zinc-50 border border-zinc-100 rounded-2xl flex items-center justify-between hover:bg-zinc-100 transition-all cursor-pointer">
                                    <div class="flex items-center gap-3">
                                        <template x-if="selectedPaymentMethod === 'cash'">
                                            <div class="text-left">
                                                <h4 class="text-xs font-black text-zinc-950 uppercase tracking-wide leading-none mb-1">Bayar Manual / COD</h4>
                                                <p class="text-[9px] text-zinc-400 font-semibold">Bayar langsung atau transfer manual</p>
                                            </div>
                                        </template>
                                        <template x-if="selectedPaymentMethod === 'digital'">
                                            <div class="text-left">
                                                <h4 class="text-xs font-black text-zinc-950 uppercase tracking-wide leading-none mb-1">Pembayaran Digital</h4>
                                                <p class="text-[9px] text-zinc-400 font-semibold">QRIS / E-Wallet / Virtual Account</p>
                                            </div>
                                        </template>
                                        <template x-if="selectedPaymentMethod !== 'cash' && selectedPaymentMethod !== 'digital'">
                                            <div class="text-left">
                                                <h4 class="text-xs font-black text-zinc-950 uppercase tracking-wide leading-none mb-1" x-text="duitkuPaymentMethods.find(m => m.paymentMethod === selectedPaymentMethod)?.paymentName || 'Digital Payment'"></h4>
                                                <p class="text-[9px] text-zinc-400 font-semibold">Metode Pembayaran Otomatis</p>
                                            </div>
                                        </template>
                                    </div>
                                    <span class="text-[10px] font-black uppercase tracking-wider bg-zinc-200/50 text-zinc-600 px-2.5 py-1 rounded-lg">Ubah</span>
                                </div>
                            </div>
                        </div>

                        <div class="shrink-0 p-6 bg-white border-t border-zinc-100 shadow-[0_-10px_40px_rgba(0,0,0,0.05)]">
                            <div class="space-y-2 mb-4 px-1 text-xs font-semibold text-zinc-500">
                                <div class="flex items-center justify-between"><span>Subtotal</span><span class="text-zinc-900 font-black" x-text="formatPrice(totalCart)"></span></div>
                                <div class="flex items-center justify-between" x-show="isServiceActive && serviceChargeAmount > 0" style="display: none;"><span>Biaya Layanan (<span x-text="serviceRate"></span>%)</span><span class="text-zinc-900 font-black" x-text="formatPrice(serviceChargeAmount)"></span></div>
                                <div class="flex items-center justify-between" x-show="isTaxActive && taxAmount > 0" style="display: none;"><span>Pajak (<span x-text="taxRate"></span>%)</span><span class="text-zinc-900 font-black" x-text="formatPrice(taxAmount)"></span></div>
                                <hr class="border-zinc-100 my-1">
                            </div>
                            <div class="flex items-center justify-between mb-3 px-1">
                                <span class="text-xs font-black text-zinc-900 uppercase tracking-wider">Total Tagihan</span>
                                <span class="text-lg font-black text-zinc-900" x-text="formatPrice(totalOrderPrice)"></span>
                            </div>

                            <button @click="processOrder" :disabled="cart.length === 0 || checkoutLoading || cart.some(i => i.unavailable)" class="w-full py-4 rounded-xl font-black text-xs uppercase flex items-center justify-center gap-2 transition-all active:scale-95 shadow-md" :class="cart.length === 0 || cart.some(i => i.unavailable) ? 'bg-zinc-200 text-zinc-400' : 'bg-zinc-900 hover:bg-zinc-800 text-white'">
                                <span x-show="!checkoutLoading" x-text="selectedPaymentMethod === 'cash' ? 'Buat Pesanan' : 'Bayar Sekarang'"></span>
                                <span x-show="checkoutLoading" style="display:none">Memproses...</span>
                            </button>
                        </div>
                    </div>

                    <div x-show="showPaymentSelector" class="flex-1 flex flex-col min-h-0 overflow-hidden bg-white animate-fade-in" style="display: none;">
                        <div class="flex-1 min-h-0 overflow-y-auto overscroll-contain p-5 space-y-4">
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-2">Manual / COD</p>
                                <div @click="selectedPaymentMethod = 'cash'" class="p-4 rounded-2xl border-2 transition-all cursor-pointer" :class="selectedPaymentMethod === 'cash' ? 'border-[var(--primary-color)] bg-[var(--primary-color)]/[0.04]' : 'bg-zinc-50 border-transparent hover:border-zinc-200'">
                                    <h4 class="text-xs font-black text-zinc-900 uppercase">Bayar Manual / COD</h4>
                                    <p class="text-[9px] text-zinc-400 mt-1">Pembayaran melalui kasir atau transfer manual</p>
                                </div>
                            </div>
                            
                            <template x-if="midtransEnabled && !duitkuEnabled">
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-2">Pembayaran Digital</p>
                                    <div @click="selectedPaymentMethod = 'digital'" class="p-4 rounded-2xl border-2 transition-all cursor-pointer" :class="selectedPaymentMethod === 'digital' ? 'border-[var(--primary-color)] bg-[var(--primary-color)]/[0.04]' : 'bg-zinc-50 border-transparent hover:border-zinc-200'">
                                        <h4 class="text-xs font-black text-zinc-900 uppercase">Semua Metode Digital</h4>
                                        <p class="text-[9px] text-zinc-400 mt-1">QRIS, E-Wallet, Virtual Account</p>
                                    </div>
                                </div>
                            </template>

                            <template x-if="duitkuEnabled">
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-2">Pilih Metode Pembayaran</p>
                                    <div class="space-y-2">
                                        <template x-if="duitkuLoading">
                                            <div class="py-4 text-center text-xs font-bold text-zinc-400">Memuat metode pembayaran...</div>
                                        </template>
                                        <template x-for="method in duitkuPaymentMethods" :key="method.paymentMethod">
                                            <div @click="selectedPaymentMethod = method.paymentMethod" class="p-4 rounded-2xl border-2 transition-all cursor-pointer flex items-center gap-3" :class="selectedPaymentMethod === method.paymentMethod ? 'border-[var(--primary-color)] bg-[var(--primary-color)]/[0.04]' : 'bg-zinc-50 border-transparent'">
                                                <div class="w-10 h-10 bg-white p-1 rounded-xl border border-zinc-200 flex items-center justify-center shrink-0">
                                                    <img :src="method.paymentImage" class="max-w-full max-h-full object-contain" alt="">
                                                </div>
                                                <h4 class="text-xs font-black text-zinc-900" x-text="method.paymentName"></h4>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>
