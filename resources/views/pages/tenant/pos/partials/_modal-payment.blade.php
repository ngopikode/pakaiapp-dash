{{-- ===== PAYMENT MODAL (Shared between Resto & Retail) ===== --}}
<div x-show="isPaymentModalOpen" x-cloak class="fixed inset-0 z-[1050] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-end justify-center p-4 text-center sm:items-center sm:p-0">
        {{-- Backdrop --}}
        <div x-show="isPaymentModalOpen" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" 
             @click="isPaymentModalOpen = false" aria-hidden="true"></div>

        {{-- Modal Dialog --}}
        <div x-show="isPaymentModalOpen" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
              class="relative w-full max-w-4xl transform overflow-hidden rounded-[1.5rem] border border-emerald-800/15 bg-white text-left shadow-xl transition-all dark:border-slate-700 dark:bg-slate-900 sm:my-8 flex flex-col max-h-[90dvh]">

            {{-- Header --}}
            <div class="flex shrink-0 items-center justify-between rounded-t-[1.5rem] border-b border-emerald-800/10 bg-white px-4 py-3 dark:border-slate-800 dark:bg-slate-900">
                <h5 class="mb-0 font-black text-slate-900 dark:text-white">Pembayaran</h5>
                <button type="button" @click="isPaymentModalOpen = false" aria-label="Tutup" class="flex h-8 w-8 items-center justify-center rounded-full text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-600 focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:hover:bg-slate-800 dark:hover:text-slate-300"><i class="ph-bold ph-x text-lg"></i></button>
            </div>

            {{-- Body --}}
            <div class="overflow-y-auto bg-white p-3 dark:bg-slate-900 md:p-4">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-5">
                    {{-- Left: Total & Payment Method --}}
                    <div class="md:col-span-2 md:border-r md:border-emerald-800/10 md:pe-4 md:dark:border-slate-800">
                        <div class="mb-3 rounded-2xl border border-emerald-800/15 bg-emerald-50/60 p-4 text-center dark:border-slate-800 dark:bg-slate-950">
                            <h6 class="mb-1 text-xs font-bold text-slate-600 dark:text-slate-400">Total Tagihan</h6>
                            <h2 class="mb-0 font-black text-emerald-800 dark:text-emerald-400" x-text="'Rp ' + formatRupiah(payTotal)"></h2>
                        </div>

                        {{-- Payment Methods --}}
                        <div class="flex flex-col gap-2">
                            <label class="flex cursor-pointer items-center gap-3 rounded-2xl border bg-white p-3 text-sm font-black transition-colors focus-within:outline-none focus-within:ring-2 focus-within:ring-emerald-500 focus-within:ring-offset-2 dark:focus-within:ring-offset-slate-900"
                                   :class="paymentMethod === 'cash' ? 'border-emerald-700 bg-emerald-100 shadow-sm ring-2 ring-emerald-700/25 dark:border-emerald-400 dark:bg-emerald-500/10 dark:ring-emerald-400/25' : 'border-slate-200 bg-white hover:border-emerald-500 hover:bg-emerald-50/60 dark:border-slate-700 dark:bg-slate-950 dark:hover:border-emerald-400 dark:hover:bg-emerald-500/10'">
                                <input type="radio" x-model="paymentMethod" value="cash" class="hidden">
                                <i class="ph-bold ph-money text-lg" :class="paymentMethod === 'cash' ? 'text-emerald-800 dark:text-emerald-400' : 'text-slate-400'"></i>
                                <span :class="paymentMethod === 'cash' ? 'text-slate-900 dark:text-white' : 'text-slate-600 dark:text-slate-300'">Tunai</span>
                            </label>

                            <label class="flex cursor-pointer items-center gap-3 rounded-2xl border bg-white p-3 text-sm font-black transition-colors focus-within:outline-none focus-within:ring-2 focus-within:ring-emerald-500 focus-within:ring-offset-2 dark:focus-within:ring-offset-slate-900"
                                   :class="paymentMethod === 'qris' ? 'border-emerald-700 bg-emerald-100 shadow-sm ring-2 ring-emerald-700/25 dark:border-emerald-400 dark:bg-emerald-500/10 dark:ring-emerald-400/25' : 'border-slate-200 bg-white hover:border-emerald-500 hover:bg-emerald-50/60 dark:border-slate-700 dark:bg-slate-950 dark:hover:border-emerald-400 dark:hover:bg-emerald-500/10'">
                                <input type="radio" x-model="paymentMethod" value="qris" class="hidden">
                                <i class="ph-bold ph-qr-code text-lg" :class="paymentMethod === 'qris' ? 'text-emerald-800 dark:text-emerald-400' : 'text-slate-400'"></i>
                                <span :class="paymentMethod === 'qris' ? 'text-slate-900 dark:text-white' : 'text-slate-600 dark:text-slate-300'">QRIS</span>
                            </label>

                            <label class="flex cursor-pointer items-center gap-3 rounded-2xl border bg-white p-3 text-sm font-black transition-colors focus-within:outline-none focus-within:ring-2 focus-within:ring-emerald-500 focus-within:ring-offset-2 dark:focus-within:ring-offset-slate-900"
                                   :class="paymentMethod === 'transfer' ? 'border-emerald-700 bg-emerald-100 shadow-sm ring-2 ring-emerald-700/25 dark:border-emerald-400 dark:bg-emerald-500/10 dark:ring-emerald-400/25' : 'border-slate-200 bg-white hover:border-emerald-500 hover:bg-emerald-50/60 dark:border-slate-700 dark:bg-slate-950 dark:hover:border-emerald-400 dark:hover:bg-emerald-500/10'">
                                <input type="radio" x-model="paymentMethod" value="transfer" class="hidden">
                                <i class="ph-bold ph-bank text-lg" :class="paymentMethod === 'transfer' ? 'text-emerald-800 dark:text-emerald-400' : 'text-slate-400'"></i>
                                <span :class="paymentMethod === 'transfer' ? 'text-slate-900 dark:text-white' : 'text-slate-600 dark:text-slate-300'">Transfer</span>
                            </label>

                            @if(config('duitku.enabled'))
                            <label class="flex cursor-pointer items-center gap-3 rounded-2xl border bg-white p-3 text-sm font-black transition-colors focus-within:outline-none focus-within:ring-2 focus-within:ring-emerald-500 focus-within:ring-offset-2 dark:focus-within:ring-offset-slate-900"
                                   :class="paymentMethod === 'duitku' ? 'border-emerald-700 bg-emerald-100 shadow-sm ring-2 ring-emerald-700/25 dark:border-emerald-400 dark:bg-emerald-500/10 dark:ring-emerald-400/25' : 'border-slate-200 bg-white hover:border-emerald-500 hover:bg-emerald-50/60 dark:border-slate-700 dark:bg-slate-950 dark:hover:border-emerald-400 dark:hover:bg-emerald-500/10'">
                                <input type="radio" x-model="paymentMethod" value="duitku" class="hidden">
                                <i class="ph-fill ph-lightning text-lg" :class="paymentMethod === 'duitku' ? 'text-amber-500' : 'text-slate-400'"></i>
                                <span :class="paymentMethod === 'duitku' ? 'text-slate-900 dark:text-white' : 'text-slate-600 dark:text-slate-300'">
                                    Duitku
                                    <span class="ml-1 inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-extrabold text-amber-800 dark:bg-amber-500/20 dark:text-amber-400">DIGITAL</span>
                                </span>
                            </label>
                            @endif

                            @if(config('midtrans.server_key'))
                            <label class="flex cursor-pointer items-center gap-3 rounded-2xl border bg-white p-3 text-sm font-black transition-colors focus-within:outline-none focus-within:ring-2 focus-within:ring-emerald-500 focus-within:ring-offset-2 dark:focus-within:ring-offset-slate-900"
                                   :class="paymentMethod === 'digital' ? 'border-emerald-700 bg-emerald-100 shadow-sm ring-2 ring-emerald-700/25 dark:border-emerald-400 dark:bg-emerald-500/10 dark:ring-emerald-400/25' : 'border-slate-200 bg-white hover:border-emerald-500 hover:bg-emerald-50/60 dark:border-slate-700 dark:bg-slate-950 dark:hover:border-emerald-400 dark:hover:bg-emerald-500/10'">
                                <input type="radio" x-model="paymentMethod" value="digital" class="hidden">
                                <i class="ph-fill ph-credit-card text-lg" :class="paymentMethod === 'digital' ? 'text-emerald-800 dark:text-emerald-400' : 'text-slate-400'"></i>
                                <span :class="paymentMethod === 'digital' ? 'text-slate-900 dark:text-white' : 'text-slate-600 dark:text-slate-300'">
                                    Midtrans
                                    <span class="ml-1 inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-extrabold text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-400">DIGITAL</span>
                                </span>
                            </label>
                            @endif
                        </div>
                    </div>

                    {{-- Right: Aksi / Numpad --}}
                    <div class="md:col-span-3">
                        {{-- Duitku --}}
                        <template x-if="paymentMethod === 'duitku'">
                            <div class="flex h-full flex-col py-2">
                                <div class="mb-3 flex flex-col items-center gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-center dark:border-amber-500/30 dark:bg-amber-500/10 sm:flex-row sm:text-start">
                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-amber-500 text-white shadow-sm">
                                        <i class="ph-fill ph-lightning text-xl"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1 text-sm font-black text-amber-900 dark:text-amber-300">Generate Link Pembayaran</h6>
                                        <p class="mb-0 text-xs font-semibold text-amber-800/80 dark:text-amber-400/80">
                                            Generate link pembayaran, lalu kirimkan ke customer. Status akan update otomatis setelah customer bayar.
                                        </p>
                                    </div>
                                </div>

                                @if(config('duitku.sandbox'))
                                <div class="mb-3 flex items-start gap-2 rounded-2xl border-l-4 border-amber-500 bg-amber-50 p-3 dark:bg-amber-500/10">
                                    <i class="ph-fill ph-warning mt-0.5 shrink-0 text-amber-500"></i>
                                    <div>
                                        <h6 class="mb-1 text-xs font-black text-amber-900 dark:text-amber-300">Mode Uji Coba (Sandbox)</h6>
                                        <p class="mb-0 text-xs font-semibold text-amber-800/80 dark:text-amber-400/80">
                                            Website ini sedang dalam tahap uji coba pembayaran. Jangan gunakan kartu kredit atau rekening asli.
                                        </p>
                                    </div>
                                </div>
                                @endif

                                <div class="mb-3 flex-1">
                                    <label class="mb-2 text-[10px] font-extrabold uppercase tracking-wider text-slate-600 dark:text-slate-400">
                                        1. Pilih Saluran Pembayaran Duitku <span class="text-red-500">*</span>
                                    </label>
                                    <div class="grid grid-cols-2 gap-2 overflow-y-auto sm:grid-cols-3 max-h-60">
                                        <template x-for="method in duitkuPaymentMethods" :key="method.paymentMethod">
                                            <div>
                                                <button @click="duitkuMethod = method.paymentMethod" type="button"
                                                        class="flex h-full w-full flex-col items-center justify-center gap-1 rounded-2xl border p-2 text-center transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 min-h-[72px]"
                                                        :class="duitkuMethod === method.paymentMethod ? 'border-emerald-700 bg-emerald-100 shadow-sm ring-2 ring-emerald-700/25 dark:border-emerald-400 dark:bg-emerald-500/10 dark:ring-emerald-400/25' : 'border-slate-200 bg-white text-slate-600 hover:border-emerald-500 hover:bg-emerald-50/60 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-300 dark:hover:border-emerald-400 dark:hover:bg-emerald-500/10'">
                                                    <div class="flex h-7 w-12 items-center justify-center rounded border border-slate-100 bg-white p-1 dark:border-slate-800 dark:bg-slate-900">
                                                        <img :src="method.paymentImage" class="max-h-full max-w-full object-contain" :alt="method.paymentName" onerror="this.src='https://images.duitku.com/hotlink-ok/QRIS.PNG'">
                                                    </div>
                                                    <span x-text="method.paymentName" class="w-full truncate text-[11px] font-bold leading-tight"></span>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <div class="mt-auto">
                                    <label class="mb-1.5 text-[10px] font-extrabold uppercase tracking-wider text-slate-600 dark:text-slate-400">
                                        2. Kirim Tagihan ke Email Customer <span class="font-semibold text-slate-400">(opsional)</span>
                                    </label>
                                    <div class="rounded-2xl border border-emerald-800/20 bg-white p-3 shadow-sm dark:border-slate-700 dark:bg-slate-950">
                                        <div class="flex overflow-hidden rounded-xl border border-emerald-800/20 transition-colors focus-within:border-emerald-500 focus-within:ring-2 focus-within:ring-emerald-500 focus-within:ring-offset-2 dark:border-slate-700 dark:focus-within:ring-offset-slate-900">
                                            <span class="flex items-center border-r border-emerald-800/10 bg-slate-50 px-3 text-slate-500 dark:border-slate-800 dark:bg-slate-950">
                                                <i class="ph-fill ph-envelope"></i>
                                            </span>
                                            <input type="email" class="w-full border-0 bg-white px-3 py-2 text-sm font-bold text-slate-900 outline-none dark:bg-slate-900 dark:text-white"
                                                   placeholder="contoh: customer@email.com" x-model="duitkuCustomerEmail">
                                        </div>
                                        <div class="mt-2 flex items-center gap-1 text-[11px] text-slate-500 dark:text-slate-400">
                                            <i class="ph-fill ph-info text-amber-500"></i>
                                            <span>Kosongkan jika tidak ada &mdash; sistem akan pakai email toko secara otomatis.</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        {{-- QRIS / Transfer --}}
                        <template x-if="paymentMethod !== 'cash' && paymentMethod !== 'duitku' && paymentMethod !== 'digital'">
                            <div class="flex h-full flex-col items-center justify-center py-5 text-center">
                                <i class="mb-3 text-5xl text-emerald-800 dark:text-emerald-400"
                                   :class="paymentMethod === 'qris' ? 'ph-bold ph-qr-code' : 'ph-bold ph-bank'"></i>
                                <h5 class="mb-1 font-black text-slate-900 dark:text-white" x-text="paymentMethod === 'qris' ? 'Pembayaran QRIS' : 'Transfer Bank'"></h5>
                                <p class="text-sm font-semibold text-slate-500 opacity-70 dark:text-slate-400">Pastikan pelanggan sudah berhasil transfer sebelum menekan tombol proses.</p>
                            </div>
                        </template>

                        {{-- Midtrans --}}
                        <template x-if="paymentMethod === 'digital'">
                            <div class="flex h-full flex-col items-center justify-center py-5 text-center">
                                <i class="ph-bold ph-device-mobile-speaker mb-3 text-5xl text-emerald-800 dark:text-emerald-400"></i>
                                <h5 class="mb-1 font-black text-slate-900 dark:text-white">Pembayaran Midtrans</h5>
                                <p class="text-sm font-semibold text-slate-500 opacity-70 dark:text-slate-400">
                                    Pilih opsi ini untuk memunculkan QRIS Dinamis atau pop-up pembayaran di layar pelanggan/kasir.
                                </p>
                            </div>
                        </template>

                        {{-- Cash Numpad --}}
                        <template x-if="paymentMethod === 'cash'">
                            <div class="flex h-full flex-col justify-end">
                                <div class="mb-2">
                                    <div class="rounded-2xl border border-emerald-800/20 bg-slate-50 px-4 py-3 text-center dark:border-slate-700 dark:bg-slate-950">
                                        <label class="mb-1 text-xs font-bold text-slate-600 dark:text-slate-400">Uang Diterima</label>
                                        <div class="text-xl font-black text-emerald-800 dark:text-emerald-400"
                                             x-text="amountPaid ? 'Rp ' + formatRupiah(amountPaid) : 'Rp 0'"></div>
                                    </div>
                                </div>

                                <div class="mb-2 grid grid-cols-3 gap-2">
                                    <button @click="amountPaid = payTotal"
                                            class="min-h-[44px] rounded-2xl border border-emerald-800/30 bg-white py-2 text-sm font-black text-emerald-800 shadow-sm transition-colors hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:border-emerald-500/30 dark:bg-slate-950 dark:text-emerald-400">
                                        Pas
                                    </button>
                                    <button @click="amountPaid = 50000"
                                            class="min-h-[44px] rounded-2xl border border-slate-200 bg-white py-2 text-sm font-black text-slate-700 shadow-sm transition-colors hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200">
                                        50k
                                    </button>
                                    <button @click="amountPaid = 100000"
                                            class="min-h-[44px] rounded-2xl border border-slate-200 bg-white py-2 text-sm font-black text-slate-700 shadow-sm transition-colors hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200">
                                        100k
                                    </button>
                                </div>

                                <div class="mb-2 grid grid-cols-3 gap-2">
                                    <template x-for="n in [1, 2, 3, 4, 5, 6, 7, 8, 9]" :key="n">
                                        <button @click="appendNumber(n)"
class="min-h-[44px] rounded-2xl border border-slate-200 bg-white py-3 text-lg font-black text-slate-700 shadow-sm transition-colors hover:bg-slate-50 active:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200"
                                                x-text="n"></button>
                                    </template>
                                    <button @click="appendNumber('000')"
                                            class="min-h-[44px] rounded-2xl border border-slate-200 bg-white py-3 text-lg font-black text-slate-700 shadow-sm transition-colors hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200">
                                        000
                                    </button>
                                    <button @click="appendNumber('0')"
                                            class="min-h-[44px] rounded-2xl border border-slate-200 bg-white py-3 text-lg font-black text-slate-700 shadow-sm transition-colors hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200">
                                        0
                                    </button>
                                    <button @click="deleteNumber()"
                                            class="min-h-[44px] rounded-2xl border border-slate-200 bg-white py-3 text-lg font-black text-red-500 shadow-sm transition-colors hover:bg-red-50 active:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 dark:border-slate-700 dark:bg-slate-950">
                                        <i class="ph-fill ph-backspace"></i>
                                    </button>
                                </div>

                                {{-- Kembalian --}}
                                <template x-if="amountPaid && getChange >= 0">
                                    <div class="mt-1 flex items-center justify-between rounded-2xl border border-emerald-800/20 bg-emerald-50 px-4 py-3 dark:border-emerald-500/30 dark:bg-emerald-500/10">
                                        <span class="text-sm font-bold text-emerald-800 dark:text-emerald-400">Kembalian:</span>
                                        <h5 class="mb-0 font-black text-emerald-800 dark:text-emerald-400" x-text="'Rp ' + formatRupiah(getChange)"></h5>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="shrink-0 rounded-b-[1.5rem] border-t border-emerald-800/10 bg-slate-50 p-3 dark:border-slate-800 dark:bg-slate-950">
                <div class="flex w-full gap-2">
                    <button type="button"
                            class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-700 shadow-sm transition-colors hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:focus:ring-offset-slate-950"
                            @click="isPaymentModalOpen = false">Batal</button>
                    <button @click="submitPayment"
                            class="flex flex-1 items-center justify-center gap-2 rounded-2xl bg-emerald-800 px-5 py-3 text-sm font-black text-white shadow-sm transition-colors hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60 dark:bg-emerald-500 dark:text-slate-950 dark:hover:bg-emerald-400 dark:focus:ring-offset-slate-900"
                            :disabled="isSubmitting
                                || (paymentMethod === 'cash' && !amountPaid)
                                || (paymentMethod === 'duitku' && !duitkuMethod)">
                        <span x-show="!isSubmitting" class="flex items-center gap-2">
                            <span x-show="paymentMethod !== 'duitku' && paymentMethod !== 'digital'">Selesaikan Transaksi</span>
                            <span x-show="paymentMethod === 'duitku' || paymentMethod === 'digital'">Proses Pembayaran Online</span>
                        </span>
                        <span x-show="isSubmitting" x-cloak class="flex items-center gap-2"><i class="ph-bold ph-spinner animate-spin"></i> Memproses...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
