{{-- Checkout Modal (Save to DB) - 100% Client-Side, Uses Global Scope --}}
<div
    x-show="checkoutOpen"
    x-data="{ showPaymentSelector: false }"
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
        class="fixed inset-x-0 bottom-0 top-10 w-full sm:inset-x-auto sm:left-1/2 sm:-translate-x-1/2 sm:max-w-lg bg-[var(--surface)] z-[110] flex flex-col rounded-t-[2rem] overflow-hidden shadow-2xl sm:ring-1 sm:ring-[var(--border)]"
    >
        {{-- Header (Dynamic Multi-Step) --}}
        <div x-show="!orderSuccess" class="p-5 flex justify-between items-center border-b border-[var(--border)] bg-[var(--surface)] sticky top-0 z-10 pt-6">
            <div class="flex items-center gap-3">
                <!-- Back Button (Step 2 Only) -->
                <button
                    x-show="checkoutStep === 2"
                    @click="showPaymentSelector ? showPaymentSelector = false : checkoutStep = 1"
                    class="p-2 bg-[var(--background)] rounded-full hover:bg-[var(--bg-soft)] transition-all shrink-0 active:scale-90"
                    style="display: none;"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                         class="text-[var(--foreground)]">
                        <path d="m15 18-6-6 6-6"/>
                    </svg>
                </button>

                <div>
                    <!-- Step 1 Title -->
                    <h2 x-show="checkoutStep === 1" class="text-lg font-black tracking-tight text-[var(--foreground)] uppercase">
                        Pesanan Kamu</h2>
                    <p x-show="checkoutStep === 1" class="text-[10px] text-[var(--text-secondary)] font-semibold">Pastikan pesanan
                        sudah sesuai ya</p>

                    <!-- Step 2 Title -->
                    <h2 x-show="checkoutStep === 2" class="text-lg font-black tracking-tight text-[var(--foreground)] uppercase"
                        style="display: none;"
                        x-text="showPaymentSelector ? 'Pilih Metode Bayar' : 'Pilih Pembayaran'"></h2>
                    <p x-show="checkoutStep === 2" class="text-[10px] text-[var(--text-secondary)] font-semibold"
                       style="display: none;"
                       x-text="showPaymentSelector ? 'Pilih metode pembayaran yang kamu inginkan' : 'Lengkapi info & metode pembayaran'"></p>
                </div>
            </div>

            <button @click="closeCheckout" class="p-2 bg-[var(--background)] rounded-full hover:bg-[var(--bg-soft)] transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     class="text-[var(--foreground)]">
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

                <h3 class="text-xl font-black text-[var(--foreground)] mb-1">Pesanan Terkirim! 🎉</h3>
                <p class="text-xs font-medium text-[var(--text-secondary)] mb-1">Pesanan kamu sudah masuk ke sistem toko.</p>
                <p class="text-xs font-medium text-[var(--text-secondary)] mb-6">
                    Yuk, langsung ke kasir untuk konfirmasi dan selesaikan pembayaranmu.
                </p>

                {{-- Interactive Invoice Card --}}
                <div
                    class="bg-[var(--background)] rounded-2xl p-4 border border-[var(--border)] w-full mb-3 flex items-center justify-between gap-4">
                    <div class="text-left min-w-0">
                        <span class="text-[9px] font-black text-[var(--text-secondary)] uppercase tracking-widest block mb-0.5">Kode Invoice</span>
                        <span class="text-sm font-mono font-black text-[var(--foreground)] block truncate"
                              x-text="orderSuccess.invoiceCode"></span>
                    </div>
                    <button
                        @click="
                            navigator.clipboard.writeText(orderSuccess.invoiceCode);
                            copied = true;
                            setTimeout(() => copied = false, 2000);
                        "
                        class="shrink-0 flex items-center gap-1 px-3 py-2 bg-[var(--surface)] rounded-xl border border-[var(--border)] text-[10px] font-black uppercase tracking-wider transition-all duration-200 active:scale-95 shadow-sm shadow-[var(--border)]"
                        :class="copied ? 'text-emerald-600 border-emerald-200 bg-emerald-50/30' : 'text-[var(--foreground)] hover:bg-[var(--background)]'"
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
                <div class="bg-[var(--foreground)] text-[var(--background)] rounded-2xl p-4 w-full mb-8 flex items-center justify-between">
                    <span class="text-[10px] font-black uppercase tracking-widest text-[var(--text-secondary)]">Total Tagihan</span>
                    <span class="text-base font-black font-mono text-[var(--primary-color)]"
                          x-text="orderSuccess.total"></span>
                </div>

                {{-- Action Buttons Container --}}
                <div class="w-full space-y-2">
                    {{-- 1. Primary Action: Lihat Detail Nota (_blank) --}}
                    <a
                        :href="'/invoice/' + orderSuccess.invoiceCode"
                        target="_blank"
                        class="w-full bg-[var(--primary-color)] text-[var(--foreground)] py-4 rounded-xl font-black text-xs uppercase tracking-widest hover:brightness-105 transition-all active:scale-[0.98] flex items-center justify-center gap-2 shadow-lg shadow-[var(--primary-color)]/20"
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

                    {{-- 2. Secondary Action: Tombol WA (Pakai Button + JS biar gak mental ke /) --}}
                    <button
                        @click="window.open(orderSuccess.waUrl, '_blank')"
                        type="button"
                        class="w-full bg-[#25D366] text-[var(--background)] py-4 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-[#20bd5a] transition-all active:scale-[0.98] flex items-center justify-center gap-2 shadow-lg shadow-[#25D366]/20"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                             fill="currentColor">
                            <path
                                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.888-.788-1.489-1.761-1.663-2.06-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                        </svg>
                        Konfirmasi via WA
                    </button>

                    {{-- 3. Tertiary Action: Kembali Browsing Menu --}}
                    <button
                        @click="closeCheckout"
                        class="w-full bg-[var(--background)] text-[var(--text-secondary)] py-3.5 mt-2 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-[var(--bg-soft)] border border-[var(--border)]/40 transition-all active:scale-[0.98]"
                    >
                        Kembali ke Menu
                    </button>
                </div>
            </div>
        </template>

        {{-- ===== CART & FORM STATE ===== --}}
        <template x-if="!orderSuccess">
            <div class="flex-1 min-h-0 flex flex-col overflow-hidden">

                {{-- STEP 1: Keranjang & Tipe Pesanan --}}
                <div
                    x-show="checkoutStep === 1"
                    class="flex-1 min-h-0 flex flex-col overflow-hidden"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-x-4"
                    x-transition:enter-end="opacity-100 translate-x-0"
                >
                    {{-- Cart Items --}}
                    <div class="flex-1 min-h-0 overflow-y-auto overscroll-contain p-5 pb-4">
                        <template x-if="cart.length === 0">
                            <div class="text-center py-10 text-[var(--text-secondary)] text-sm font-bold">Keranjang masih kosong nih
                            </div>
                        </template>

                        <template x-if="cart.length > 0">
                            <div>
                                <template x-for="item in cart" :key="item.cartName">
                                    <div
                                        class="flex justify-between items-start mb-6 pb-6 border-b border-black/5 dark:border-white/5 last:border-0 animate-slide-up rounded-xl transition-all"
                                        :class="item.unavailable ? 'bg-red-50/60 border border-red-100 px-3 pt-3 -mx-1' : ''"
                                    >
                                        <div class="flex-1 pr-4 min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap mb-0.5">
                                                <h4 class="font-bold text-sm leading-tight"
                                                    :class="item.unavailable ? 'text-[var(--text-secondary)] line-through' : 'text-[var(--foreground)]'"
                                                    x-text="item.cartName"></h4>
                                                <template x-if="item.unavailable">
                                                    <span
                                                        class="inline-flex items-center gap-1 bg-red-100 text-red-500 text-[9px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full shrink-0">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="9" height="9"
                                                             viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                             stroke-width="2.5" stroke-linecap="round"
                                                             stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line
                                                                x1="15" x2="9" y1="9" y2="15"/><line x1="9" x2="15"
                                                                                                     y1="9"
                                                                                                     y2="15"/></svg>
                                                        Tidak Tersedia
                                                    </span>
                                                </template>
                                            </div>
                                            <div class="mt-1">
                                                <template x-if="item.originalPrice && item.originalPrice > item.price">
                                                    <span class="text-[10px] font-bold line-through text-red-400 block"
                                                          x-text="formatPrice(item.originalPrice * item.qty)"></span>
                                                </template>
                                                <p class="text-xs font-black"
                                                   :class="item.unavailable ? 'text-[var(--border)]' : 'text-[var(--primary-color)]'"
                                                   x-text="formatPrice(item.price * item.qty)"></p>
                                            </div>
                                        </div>

                                        {{-- Unavailable: hanya tombol hapus --}}
                                        <template x-if="item.unavailable">
                                            <button
                                                @click="updateQty(item.cartName, -item.qty)"
                                                class="w-8 h-8 rounded-lg bg-red-50 border border-red-100 text-red-400 flex items-center justify-center hover:bg-red-100 transition-colors active:scale-90 shrink-0"
                                                title="Hapus dari keranjang"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="3 6 5 6 21 6"/>
                                                    <path
                                                        d="m19 6-.867 12.142A2 2 0 0 1 16.138 20H7.862a2 2 0 0 1-1.995-1.858L5 6"/>
                                                    <path d="M10 11v6"/>
                                                    <path d="M14 11v6"/>
                                                    <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                                </svg>
                                            </button>
                                        </template>

                                        {{-- Available: stepper normal --}}
                                        <template x-if="!item.unavailable">
                                            <div
                                                class="flex items-center gap-3 bg-[var(--background)] p-1 rounded-lg border border-[var(--border)]">
                                                <button @click="updateQty(item.cartName, -1)"
                                                        class="w-7 h-7 rounded-md bg-[var(--surface)] text-[var(--foreground)] flex items-center justify-center shadow-sm hover:bg-[var(--bg-soft)] font-bold text-lg leading-none">
                                                    -
                                                </button>
                                                <span class="font-black text-xs w-4 text-center tabular-nums"
                                                      x-text="item.qty"></span>
                                                <button @click="updateQty(item.cartName, 1)"
                                                        class="w-7 h-7 rounded-md bg-[var(--foreground)] text-[var(--background)] flex items-center justify-center shadow-sm hover:bg-zinc-700 font-bold text-lg leading-none">
                                                    +
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>

                    {{-- Bottom Form Step 1 --}}
                    <div class="shrink-0 p-6 bg-[var(--surface)] border-t border-[var(--border)] shadow-[0_-10px_40px_rgba(0,0,0,0.05)]">
                        {{-- Order Type Options --}}
                        <div class="grid grid-cols-{{ count($orderTypes ?? [['id'=>'takeaway']]) }} gap-2 mb-5">
                            @foreach($orderTypes ?? [['id'=>'takeaway', 'label'=>'Takeaway']] as $type)
                                <button
                                    @click="orderType = '{{ $type['id'] }}'"
                                    class="py-3 rounded-xl border-2 text-xs font-extrabold transition-all flex flex-col items-center justify-center gap-1.5"
                                    :class="orderType === '{{ $type['id'] }}' ? 'bg-[var(--primary-color)] text-black border-[var(--primary-color)]' : 'bg-[var(--background)] text-[var(--text-secondary)] border-transparent'"
                                >
                                    {{ $type['label'] }}
                                </button>
                            @endforeach
                        </div>

                        {{-- Total Price Preview for Step 1 --}}
                        <div class="flex items-center justify-between mb-4 px-1">
                            <span class="text-xs font-bold text-[var(--text-secondary)] uppercase tracking-wider">Subtotal</span>
                            <span class="text-base font-black text-[var(--foreground)]" x-text="formatPrice(totalCart)"></span>
                        </div>

                        {{-- Lanjut ke Pembayaran Button --}}
                        <button
                            @click="nextStep"
                            :disabled="cart.length === 0 || cart.some(i => i.unavailable)"
                            class="w-full py-4 rounded-xl font-black text-xs uppercase flex items-center justify-center gap-2 transition-all active:scale-95 disabled:cursor-not-allowed shadow-md hover:shadow-lg relative overflow-hidden group"
                            :class="cart.length === 0 || cart.some(i => i.unavailable)
                                ? 'bg-[var(--bg-soft)] text-[var(--text-secondary)] border border-[var(--border)]'
                                : 'bg-[var(--primary-color)] text-black hover:brightness-110 shadow-[var(--primary-color)]/20 border border-[var(--primary-color)]'"
                        >
                            <div x-show="cart.length > 0 && !cart.some(i => i.unavailable)" class="absolute inset-0 bg-white/20 animate-shimmer opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <span class="relative z-10 flex items-center gap-2">
                                <span>Lanjut ke Pembayaran</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <path d="M5 12h14"/>
                                    <path d="m12 5 7 7-7 7"/>
                                </svg>
                            </span>
                        </button>
                    </div>
                </div>

                {{-- STEP 2: Kontak & Metode Pembayaran --}}
                <div
                    x-show="checkoutStep === 2"
                    class="flex-1 min-h-0 flex flex-col overflow-hidden"
                    style="display: none;"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-x-4"
                    x-transition:enter-end="opacity-100 translate-x-0"
                >
                    <!-- A. FORM VIEW (Inputs + Selected Method Card + Footer) -->
                    <div x-show="!showPaymentSelector" class="flex-1 min-h-0 flex flex-col overflow-hidden">
                        {{-- Scrollable Area for Inputs & Payment Selector --}}
                        <div class="flex-1 min-h-0 overflow-y-auto overscroll-contain p-5 pb-4">

                            {{-- Inputs --}}
                            <div class="mb-5">
                                <p class="text-[10px] font-black uppercase tracking-widest text-[var(--text-secondary)] mb-2">Informasi
                                    Pemesan</p>
                                <div class="space-y-3">
                                    <!-- Nama Pemesan -->
                                    <div class="relative">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                             viewBox="0 0 24 24"
                                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                             stroke-linejoin="round"
                                             class="absolute left-4 top-1/2 -translate-y-1/2 text-[var(--text-secondary)]">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                            <circle cx="12" cy="7" r="4"/>
                                        </svg>
                                        <input
                                            x-model="customerName"
                                            type="text"
                                            placeholder="Nama Pemesan"
                                            class="w-full pl-10 pr-4 py-3.5 rounded-xl bg-[var(--background)] border border-[var(--border)] text-sm font-bold outline-none focus:border-[var(--primary-color)] focus:ring-1 focus:ring-[var(--primary-color)] transition-all"
                                        />
                                    </div>

                                    <!-- Email — Tampil jika pembayaran non-tunai (Digital) -->
                                    <div x-show="selectedPaymentMethod !== 'cash'" class="relative"
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 -translate-y-2"
                                         x-transition:enter-end="opacity-100 translate-y-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                             viewBox="0 0 24 24"
                                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                             stroke-linejoin="round"
                                             class="absolute left-4 top-1/2 -translate-y-1/2 text-[var(--text-secondary)]">
                                            <rect width="20" height="16" x="2" y="4" rx="2"/>
                                            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                                        </svg>
                                        <input
                                            x-model="customerEmail"
                                            type="email"
                                            placeholder="Email (wajib)"
                                            class="w-full pl-10 pr-4 py-3.5 rounded-xl bg-[var(--background)] border border-[var(--border)] text-sm font-bold outline-none focus:border-[var(--primary-color)] focus:ring-1 focus:ring-[var(--primary-color)] transition-all"
                                        />
                                    </div>

                                    <!-- Info Dinamis: Nomor Meja / Catatan / Alamat Lengkap -->
                                    <div class="relative">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                             viewBox="0 0 24 24"
                                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                             stroke-linejoin="round"
                                             class="absolute left-4 top-1/2 -translate-y-1/2 text-[var(--text-secondary)]">
                                            <!-- Icon Meja (Dine In) -->
                                            <path x-show="orderType === 'dinein'"
                                                  d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                                            <!-- Icon Catatan (Takeaway) -->
                                            <path x-show="orderType === 'takeaway'"
                                                  d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                                            <rect x-show="orderType === 'takeaway'" x="8" y="2" width="8" height="4"
                                                  rx="1" ry="1"/>
                                            <!-- Icon Alamat (Delivery) -->
                                            <path x-show="orderType === 'delivery'"
                                                  d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                            <polyline x-show="orderType === 'delivery'" points="9 22 9 12 15 12 15 22"/>
                                        </svg>
                                        <input
                                            x-model="customerInfo"
                                            type="text"
                                            :placeholder="orderType === 'dinein' ? 'Nomor Meja' : (orderType === 'takeaway' ? 'Catatan (misal: bungkus pisah)' : 'Alamat Lengkap Pengantaran')"
                                            class="w-full pl-10 pr-4 py-3.5 rounded-xl bg-[var(--background)] border border-[var(--border)] text-sm font-bold outline-none focus:border-[var(--primary-color)] focus:ring-1 focus:ring-[var(--primary-color)] transition-all"
                                        />
                                    </div>
                                </div>
                            </div>

                            {{-- Selected Payment Method Card --}}
                            <div class="mb-4">
                                <p class="text-[10px] font-black uppercase tracking-widest text-[var(--text-secondary)] mb-2">Metode
                                    Pembayaran</p>

                                <div
                                    @click="showPaymentSelector = true"
                                    class="p-4 bg-[var(--background)] border border-[var(--border)] rounded-2xl flex items-center justify-between hover:bg-[var(--bg-soft)]/50 hover:border-[var(--border)] transition-all cursor-pointer active:scale-[0.99] select-none shadow-sm shadow-[var(--border)]"
                                >
                                    <div class="flex items-center gap-3">
                                        <!-- Cash Option Selected -->
                                        <template x-if="selectedPaymentMethod === 'cash'">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-9 h-9 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-600 shrink-0">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                         viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                         stroke-width="2.5" stroke-linecap="round"
                                                         stroke-linejoin="round">
                                                        <rect width="20" height="14" x="2" y="5" rx="2"/>
                                                        <path d="M2 10h20"/>
                                                        <circle cx="12" cy="12" r="2"/>
                                                    </svg>
                                                </div>
                                                <div class="text-left">
                                                    <h4 class="text-xs font-black text-[var(--foreground)] uppercase tracking-wide leading-none mb-1">
                                                        Bayar Manual di Kasir</h4>
                                                    <p class="text-[9px] text-[var(--text-secondary)] font-semibold">Bayar tunai/manual
                                                        di kasir outlet</p>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- Midtrans Digital Option Selected -->
                                        <template x-if="selectedPaymentMethod === 'digital'">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-9 h-9 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-600 shrink-0">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                         viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                         stroke-width="2.5" stroke-linecap="round"
                                                         stroke-linejoin="round">
                                                        <rect width="16" height="20" x="4" y="2" rx="2" ry="2"/>
                                                        <line x1="12" x2="12.01" y1="18" y2="18"/>
                                                    </svg>
                                                </div>
                                                <div class="text-left">
                                                    <h4 class="text-xs font-black text-[var(--foreground)] uppercase tracking-wide leading-none mb-1">
                                                        QRIS / Transfer / E-Wallet</h4>
                                                    <p class="text-[9px] text-[var(--text-secondary)] font-semibold">Bayar online
                                                        otomatis & instan</p>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- Duitku Specific Method Selected -->
                                        <template
                                            x-if="selectedPaymentMethod !== 'cash' && selectedPaymentMethod !== 'digital'">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-9 h-9 bg-[var(--surface)] p-1 rounded-xl border border-[var(--border)] flex items-center justify-center shrink-0">
                                                    <img
                                                        :src="duitkuPaymentMethods.find(m => m.paymentMethod === selectedPaymentMethod)?.paymentImage"
                                                        class="max-w-full max-h-full object-contain"
                                                        onerror="this.style.display='none'"
                                                        alt="">
                                                </div>
                                                <div class="text-left">
                                                    <h4
                                                        class="text-xs font-black text-[var(--foreground)] uppercase tracking-wide leading-none mb-1"
                                                        x-text="duitkuPaymentMethods.find(m => m.paymentMethod === selectedPaymentMethod)?.paymentName || 'Digital Payment'"
                                                    ></h4>
                                                    <p class="text-[9px] text-[var(--text-secondary)] font-semibold">Metode Pembayaran
                                                        Digital Duitku</p>
                                                </div>
                                            </div>
                                        </template>
                                    </div>

                                    <!-- Ubah Button & Chevron -->
                                    <div
                                        class="flex items-center gap-2 text-[var(--text-secondary)] hover:text-[var(--foreground)] transition-colors">
                                        <span
                                            class="text-[10px] font-black uppercase tracking-wider bg-zinc-200/50 text-[var(--foreground)] px-2.5 py-1 rounded-lg">Ubah</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                             stroke-linecap="round" stroke-linejoin="round" class="text-[var(--text-secondary)]">
                                            <path d="m9 18 6-6-6-6"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Total & Submit --}}
                        <div
                            class="shrink-0 p-6 bg-[var(--surface)] border-t border-[var(--border)] shadow-[0_-10px_40px_rgba(0,0,0,0.05)]">
                            <!-- Breakdown Box -->
                            <div class="space-y-2 mb-4 px-1 text-xs font-semibold text-[var(--text-secondary)]">
                                <div class="flex items-center justify-between">
                                    <span>Subtotal</span>
                                    <span class="text-[var(--foreground)] font-black" x-text="formatPrice(totalCart)"></span>
                                </div>
                                <div class="flex items-center justify-between"
                                     x-show="isServiceActive && serviceChargeAmount > 0" style="display: none;">
                                    <span>Biaya Layanan (<span x-text="serviceRate"></span>%)</span>
                                    <span class="text-[var(--foreground)] font-black"
                                          x-text="formatPrice(serviceChargeAmount)"></span>
                                </div>
                                <div class="flex items-center justify-between" x-show="isTaxActive && taxAmount > 0"
                                     style="display: none;">
                                    <span>Pajak PB1 (<span x-text="taxRate"></span>%)</span>
                                    <span class="text-[var(--foreground)] font-black" x-text="formatPrice(taxAmount)"></span>
                                </div>
                                <hr class="border-[var(--border)] my-1">
                            </div>

                            <div class="flex items-center justify-between mb-3 px-1">
                                <span
                                    class="text-xs font-black text-[var(--foreground)] uppercase tracking-wider">Total Tagihan</span>
                                <span class="text-lg font-black text-[var(--foreground)]"
                                      x-text="formatPrice(totalOrderPrice)"></span>
                            </div>

                            <button
                                @click="processOrder"
                                :disabled="cart.length === 0 || checkoutLoading || cart.some(i => i.unavailable)"
                                class="w-full py-4 rounded-xl font-black text-xs uppercase flex items-center justify-center gap-2 transition-all active:scale-95 disabled:cursor-not-allowed shadow-md hover:shadow-lg relative overflow-hidden group"
                                :class="cart.length === 0 || cart.some(i => i.unavailable)
                                    ? 'bg-[var(--bg-soft)] text-[var(--text-secondary)] border border-[var(--border)]'
                                    : 'bg-[var(--primary-color)] text-black hover:brightness-110 shadow-[var(--primary-color)]/20 border border-[var(--primary-color)]'"
                            >
                                <div x-show="cart.length > 0 && !cart.some(i => i.unavailable)" class="absolute inset-0 bg-white/20 animate-shimmer opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                <span x-show="!checkoutLoading" class="flex items-center gap-2 relative z-10">
                                    <span class="flex items-center gap-2">
                                        <!-- Cash Icon -->
                                        <span x-show="selectedPaymentMethod === 'cash'" class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                 viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                 stroke-width="2.5"
                                                 stroke-linecap="round" stroke-linejoin="round"><path
                                                    d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path
                                                    d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                                        </span>
                                        <!-- Digital Icon -->
                                        <span x-show="selectedPaymentMethod !== 'cash'" class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                 viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                 stroke-width="2.5"
                                                 stroke-linecap="round" stroke-linejoin="round"><rect width="5"
                                                                                                      height="5"
                                                                                                      x="3" y="3"
                                                                                                      rx="1"/><rect
                                                    width="5" height="5" x="16" y="3" rx="1"/><rect width="5" height="5"
                                                                                                    x="3" y="16"
                                                                                                    rx="1"/><path
                                                    d="M21 16h-3a2 2 0 0 0-2 2v3"/><path d="M21 21v.01"/><path
                                                    d="M12 7v3a2 2 0 0 1-2 2H7"/><path d="M3 12h.01"/><path
                                                    d="M12 3h.01"/><path
                                                    d="M12 16v.01"/><path d="M16 12h1"/><path d="M21 12v.01"/><path
                                                    d="M12 21v-1"/></svg>
                                        </span>

                                        <!-- Dynamic Button text -->
                                        <span
                                            x-text="selectedPaymentMethod === 'cash' ? 'Pesan & Bayar di Kasir' : (selectedPaymentMethod === 'digital' ? '⚡ Bayar via Digital' : '⚡ Bayar via ' + (duitkuPaymentMethods.find(m => m.paymentMethod === selectedPaymentMethod)?.paymentName || 'Digital'))"></span>
                                    </span>
                                </span>

                                <span x-show="checkoutLoading" class="flex items-center gap-2" style="display: none;">
                                    <svg class="animate-spin w-5 h-5 text-[var(--background)]" xmlns="http://www.w3.org/2000/svg"
                                         fill="none"
                                         viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10"
                                                                     stroke="currentColor" stroke-width="4"></circle><path
                                            class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    Memproses...
                                </span>
                            </button>
                        </div>
                    </div>

                    <!-- B. PAYMENT SELECTOR VIEW (Sub-page list) -->
                    <div
                        x-show="showPaymentSelector"
                        class="flex-1 flex flex-col min-h-0 overflow-hidden bg-[var(--surface)] animate-fade-in"
                        style="display: none;"
                    >
                        <!-- Selector List (Scrollable) -->
                        <div class="flex-1 min-h-0 overflow-y-auto overscroll-contain p-5 space-y-4">
                            <!-- 1. Cash / Manual Payment -->
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-widest text-[var(--text-secondary)] mb-2">Manual /
                                    Kasir</p>
                                <div
                                    @click="selectedPaymentMethod = 'cash'"
                                    class="relative p-4 rounded-2xl border-2 transition-all duration-200 cursor-pointer select-none flex items-center justify-between"
                                    :class="selectedPaymentMethod === 'cash' ? 'border-[var(--primary-color)] bg-[var(--primary-color)]/[0.04] shadow-sm shadow-[var(--primary-color)]/5' : 'bg-[var(--background)] border-transparent hover:border-[var(--border)]'"
                                >
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-9 h-9 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-600 shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                 viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                 stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                <rect width="20" height="14" x="2" y="5" rx="2"/>
                                                <path d="M2 10h20"/>
                                                <circle cx="12" cy="12" r="2"/>
                                            </svg>
                                        </div>
                                        <div class="text-left">
                                            <h4 class="text-xs font-black text-[var(--foreground)] uppercase tracking-wide leading-none mb-1">
                                                Bayar Manual di Kasir</h4>
                                            <p class="text-[9px] text-[var(--text-secondary)] font-semibold">Pesan online, bayar
                                                tunai/manual di kasir outlet</p>
                                        </div>
                                    </div>
                                    <!-- Radio Circle -->
                                    <div
                                        class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 transition-all duration-200"
                                        :class="selectedPaymentMethod === 'cash' ? 'border-[var(--primary-color)] bg-[var(--surface)]' : 'border-[var(--border)] bg-[var(--surface)]'">
                                        <div
                                            class="w-2.5 h-2.5 rounded-full transition-transform duration-200 scale-0 bg-[var(--primary-color)]"
                                            :class="selectedPaymentMethod === 'cash' ? 'scale-100' : 'scale-0'"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- 2. Midtrans payment method -->
                            <div x-show="midtransEnabled" style="display: none;">
                                <p class="text-[10px] font-black uppercase tracking-widest text-[var(--text-secondary)] mb-2">
                                    Pembayaran Instan (Midtrans)</p>

                                @if(!config('midtrans.is_production'))
                                    <!-- Sandbox Warning Midtrans -->
                                    <div
                                        class="mb-3 p-3 bg-amber-50/80 border-l-4 border-amber-500 rounded-r-2xl flex gap-2.5 items-start text-left">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                             viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                             stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                             class="text-amber-600 shrink-0 mt-0.5">
                                            <path
                                                d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/>
                                            <line x1="12" x2="12" y1="9" y2="13"/>
                                            <line x1="12" x2="12.01" y1="17" y2="17"/>
                                        </svg>
                                        <div>
                                            <h6 class="text-[11px] font-black text-amber-900 leading-tight mb-0.5">Mode
                                                Uji Coba (Sandbox Midtrans)</h6>
                                            <p class="text-[10px] text-amber-800 leading-normal font-semibold">
                                                Pembayaran sedang dalam tahap simulasi. Jangan gunakan data asli.
                                            </p>
                                        </div>
                                    </div>
                                @endif

                                <div
                                    @click="selectedPaymentMethod = 'digital'"
                                    class="relative p-4 rounded-2xl border-2 transition-all duration-200 cursor-pointer select-none flex items-center justify-between"
                                    :class="selectedPaymentMethod === 'digital' ? 'border-[var(--primary-color)] bg-[var(--primary-color)]/[0.04] shadow-sm shadow-[var(--primary-color)]/5' : 'bg-[var(--background)] border-transparent hover:border-[var(--border)]'"
                                >
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-9 h-9 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-600 shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                 viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                 stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                <rect width="16" height="20" x="4" y="2" rx="2" ry="2"/>
                                                <line x1="12" x2="12.01" y1="18" y2="18"/>
                                            </svg>
                                        </div>
                                        <div class="text-left">
                                            <h4 class="text-xs font-black text-[var(--foreground)] uppercase tracking-wide leading-none mb-1">
                                                QRIS, Transfer Bank, E-Wallet</h4>
                                            <p class="text-[9px] text-[var(--text-secondary)] font-semibold">Bayar online dengan metode
                                                pilihanmu secara aman</p>
                                        </div>
                                    </div>
                                    <!-- Radio Circle -->
                                    <div
                                        class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 transition-all duration-200"
                                        :class="selectedPaymentMethod === 'digital' ? 'border-[var(--primary-color)] bg-[var(--surface)]' : 'border-[var(--border)] bg-[var(--surface)]'">
                                        <div
                                            class="w-2.5 h-2.5 rounded-full transition-transform duration-200 scale-0 bg-[var(--primary-color)]"
                                            :class="selectedPaymentMethod === 'digital' ? 'scale-100' : 'scale-0'"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- 3. Duitku payment methods -->
                            @if(config('duitku.enabled'))
                                <div x-show="duitkuPaymentMethods.length > 0" style="display: none;">
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="text-[10px] font-black uppercase tracking-widest text-[var(--text-secondary)]">
                                            Transfer & E-Wallet Otomatis (Duitku)</p>
                                    </div>

                                    @if(config('duitku.sandbox'))
                                        <!-- Sandbox Warning Duitku -->
                                        <div
                                            class="mb-3 p-3 bg-amber-50/80 border-l-4 border-amber-500 rounded-r-2xl flex gap-2.5 items-start text-left">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                 viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                 stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                                 class="text-amber-600 shrink-0 mt-0.5">
                                                <path
                                                    d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/>
                                                <line x1="12" x2="12" y1="9" y2="13"/>
                                                <line x1="12" x2="12.01" y1="17" y2="17"/>
                                            </svg>
                                            <div>
                                                <h6 class="text-[11px] font-black text-amber-900 leading-tight mb-0.5">
                                                    Mode Uji Coba (Sandbox Duitku)</h6>
                                                <p class="text-[10px] text-amber-800 leading-normal font-semibold">
                                                    Pembayaran sedang dalam tahap simulasi. Jangan gunakan data asli.
                                                </p>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="pt-1">
                                        <!-- Compact list in an elegant grid -->
                                        <div class="grid grid-cols-2 gap-2">
                                            <template x-for="method in duitkuPaymentMethods"
                                                      :key="method.paymentMethod">
                                                <div
                                                    @click="selectedPaymentMethod = method.paymentMethod"
                                                    class="relative flex flex-col items-center justify-center p-3.5 bg-[var(--background)] hover:bg-[var(--bg-soft)]/80 border-2 rounded-2xl transition-all duration-200 cursor-pointer select-none text-center active:scale-[0.97]"
                                                    :class="selectedPaymentMethod === method.paymentMethod ? 'border-[var(--primary-color)] bg-[var(--primary-color)]/[0.04] ring-1 ring-[var(--primary-color)]/25' : 'border-transparent hover:border-[var(--border)]'"
                                                >
                                                    <!-- Checkmark corner badge -->
                                                    <div
                                                        class="absolute top-1.5 right-1.5 w-3.5 h-3.5 rounded-full bg-[var(--primary-color)] text-black flex items-center justify-center shadow-sm shrink-0 transition-transform duration-200 scale-0"
                                                        :class="selectedPaymentMethod === method.paymentMethod ? 'scale-100' : 'scale-0'">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="9" height="9"
                                                             viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                             stroke-width="3" stroke-linecap="round"
                                                             stroke-linejoin="round">
                                                            <path d="M20 6 9 17l-5-5"/>
                                                        </svg>
                                                    </div>

                                                    <!-- White frame for Logo -->
                                                    <div
                                                        class="w-12 h-6 bg-[var(--surface)] p-0.5 rounded-lg border border-[var(--border)] flex items-center justify-center mb-1.5 shadow-sm shrink-0">
                                                        <img :src="method.paymentImage"
                                                             class="max-w-full max-h-full object-contain"
                                                             :alt="method.paymentName"
                                                             onerror="this.style.display='none'" alt="">
                                                    </div>

                                                    <!-- Method Name -->
                                                    <span
                                                        class="text-[9px] font-black text-[var(--foreground)] tracking-tight block truncate w-full max-w-[95%]"
                                                        x-text="method.paymentName"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Sticky Footer with Confirm Button -->
                        <div
                            class="shrink-0 p-5 bg-[var(--surface)] border-t border-[var(--border)] shadow-[0_-10px_40px_rgba(0,0,0,0.05)]">
                            <button
                                @click="showPaymentSelector = false"
                                class="w-full py-4 bg-[var(--foreground)] hover:bg-zinc-700 text-[var(--background)] rounded-xl font-black text-xs uppercase tracking-widest flex items-center justify-center gap-2 transition-all active:scale-95 shadow-md shadow-zinc-900/10"
                            >
                                <span>Konfirmasi Metode</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <path d="M20 6 9 17l-5-5"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </template>
    </div>
</div>
