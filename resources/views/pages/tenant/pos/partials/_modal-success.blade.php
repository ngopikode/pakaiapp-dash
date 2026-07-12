<div x-show="isSuccessModalOpen" x-cloak class="fixed inset-0 z-[1060] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-end justify-center p-4 text-center sm:items-center sm:p-0">
        {{-- Backdrop --}}
        <div x-show="isSuccessModalOpen"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"
             @click="closeSuccessModal" aria-hidden="true"></div>

        {{-- Modal Dialog --}}
        <div x-show="isSuccessModalOpen"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative w-full max-w-5xl transform overflow-hidden rounded-[1.5rem] border border-emerald-800/15 bg-white shadow-xl transition-all dark:border-slate-700 dark:bg-slate-900 sm:my-8">

            <div class="flex flex-col lg:flex-row">

                {{-- LEFT PANEL --}}
                <div class="flex w-full flex-col p-5 sm:p-6 lg:w-5/12">
                    {{-- Close --}}
                    <div class="flex justify-end">
                        <button type="button" @click="closeSuccessModal" aria-label="Tutup" class="flex h-8 w-8 items-center justify-center rounded-full text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-600 focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:hover:bg-slate-800 dark:hover:text-slate-300">
                            <i class="ph-bold ph-x text-lg"></i>
                        </button>
                    </div>

                    {{-- Success Icon --}}
                    <div class="mb-3 flex justify-center">
                        <div class="flex h-20 w-20 items-center justify-center rounded-full border-2 border-emerald-200 bg-emerald-100 shadow-sm dark:border-emerald-500/30 dark:bg-emerald-500/10">
                            <i class="ph-fill ph-check-circle text-5xl text-emerald-800 dark:text-emerald-400"></i>
                        </div>
                    </div>

                    <h4 class="text-center font-black text-slate-900 dark:text-white">Pembayaran Berhasil!</h4>
                    <div class="mt-1 flex justify-center">
                        <span class="rounded-lg border border-emerald-800/15 bg-emerald-50 px-2.5 py-1 text-xs font-black tracking-wide text-emerald-800 dark:border-slate-800 dark:bg-emerald-500/10 dark:text-emerald-400" x-text="lastOrder.invoice_code"></span>
                    </div>

                    {{-- Order Summary --}}
                    <div class="mt-4 rounded-2xl border border-emerald-800/10 bg-slate-50/80 p-3.5 dark:border-slate-800 dark:bg-slate-950/80">
                        <h6 class="mb-2.5 text-[0.65rem] font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400">Ringkasan</h6>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-slate-500 dark:text-slate-400">Pelanggan</span>
                                <span class="font-bold text-slate-900 dark:text-white" x-text="lastOrder.customer_name || 'Umum'"></span>
                            </div>
                            <template x-if="lastOrder.table_number">
                                <div class="flex justify-between">
                                    <span class="text-slate-500 dark:text-slate-400">Meja</span>
                                    <span class="font-bold text-slate-900 dark:text-white" x-text="lastOrder.table_number"></span>
                                </div>
                            </template>
                            <template x-if="lastOrder.discount && lastOrder.discount > 0">
                                <div class="flex justify-between">
                                    <span class="text-slate-500 dark:text-slate-400">Diskon</span>
                                    <span class="font-bold text-red-600 dark:text-red-400" x-text="'-Rp ' + formatRupiah(lastOrder.discount)"></span>
                                </div>
                            </template>
                            <div class="flex justify-between border-t border-slate-200 pt-2 dark:border-slate-800">
                                <span class="font-bold text-slate-900 dark:text-white">Total</span>
                                <span class="font-black text-emerald-800 dark:text-emerald-400" x-text="'Rp ' + formatRupiah(lastOrder.total_price)"></span>
                            </div>
                            <template x-if="lastOrder.amount_paid">
                                <div class="flex justify-between">
                                    <span class="text-slate-500 dark:text-slate-400">Dibayar</span>
                                    <span class="font-bold text-slate-900 dark:text-white" x-text="'Rp ' + formatRupiah(lastOrder.amount_paid)"></span>
                                </div>
                            </template>
                            <template x-if="lastOrder.change_amount && lastOrder.change_amount > 0">
                                <div class="flex justify-between">
                                    <span class="text-slate-500 dark:text-slate-400">Kembalian</span>
                                    <span class="font-bold text-emerald-800 dark:text-emerald-400" x-text="'Rp ' + formatRupiah(lastOrder.change_amount)"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- WhatsApp --}}
                    <div class="mt-4">
                        <label class="mb-1.5 text-xs font-bold text-slate-600 dark:text-slate-400">Kirim Struk WhatsApp (Opsional)</label>
                        <div class="flex overflow-hidden rounded-xl border border-slate-200 shadow-sm dark:border-slate-700">
                            <span class="flex items-center border-r border-slate-200 bg-slate-50 px-3 text-slate-400 dark:border-slate-700 dark:bg-slate-950">
                                <i class="ph-bold ph-phone text-sm"></i>
                            </span>
                            <input type="text" class="w-full border-0 bg-white px-2.5 py-2.5 text-sm font-bold text-slate-900 outline-none focus:ring-2 focus:ring-emerald-500 dark:bg-slate-900 dark:text-white"
                                   x-model="lastOrder.customer_phone" placeholder="Cth: 0812...">
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="mt-4 flex flex-col gap-2">
                        <template x-if="lastOrder.customer_phone && lastOrder.customer_phone.length >= 9">
                            <button type="button" @click="sendWa"
                                    class="flex items-center justify-center gap-2 rounded-xl bg-[#25D366] p-3 text-sm font-black text-white shadow-sm transition-colors hover:bg-[#128C7E] focus:outline-none focus:ring-2 focus:ring-[#25D366] focus:ring-offset-2 dark:focus:ring-offset-slate-900">
                                <i class="ph-bold ph-whatsapp-logo text-lg"></i> Kirim Struk via WA
                            </button>
                        </template>

                        <div class="flex gap-2">
                            <a :href="'/receipt/' + lastOrder.invoice_code" target="_blank"
                               class="flex flex-1 items-center justify-center gap-2 rounded-xl bg-emerald-800 p-3 text-sm font-black text-white shadow-sm transition-colors hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:bg-emerald-500 dark:text-slate-950 dark:hover:bg-emerald-400 dark:focus:ring-offset-slate-900">
                                <i class="ph-bold ph-printer text-lg"></i> Cetak Struk
                            </a>
                            <button type="button" @click="closeSuccessModal"
                                    class="flex flex-1 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white p-3 text-sm font-black text-slate-700 shadow-sm transition-colors hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200 dark:hover:bg-slate-800 dark:focus:ring-offset-slate-900">
                                <i class="ph-bold ph-plus text-lg"></i> Pesanan Baru
                            </button>
                        </div>
                    </div>
                </div>

                {{-- RIGHT PANEL: Receipt Preview (desktop only) --}}
                <div class="hidden border-t border-slate-100 bg-slate-50/50 dark:border-slate-800 dark:bg-slate-950/50 lg:flex lg:w-7/12 lg:flex-col lg:border-l lg:border-t-0">
                    <div class="flex flex-1 flex-col p-4">
                        <div class="mb-2 flex items-center justify-between">
                            <h6 class="text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400">Preview Struk</h6>
                        </div>
                        <iframe
                            x-ref="receiptFrame"
                            x-show="lastOrder.invoice_code"
                            :src="lastOrder.invoice_code ? '/receipt/' + lastOrder.invoice_code : 'about:blank'"
                            title="Preview Struk"
                            class="flex-1 rounded-xl border border-slate-200 bg-white dark:border-slate-800"
                            style="min-height: 500px;">
                        </iframe>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
