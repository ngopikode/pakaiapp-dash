<div x-show="isSuccessModalOpen" x-cloak class="fixed inset-0 z-[1060] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-end justify-center p-4 text-center sm:items-center sm:p-0">
        {{-- Backdrop --}}
        <div x-show="isSuccessModalOpen" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" 
             aria-hidden="true"></div>

        {{-- Modal Dialog --}}
        <div x-show="isSuccessModalOpen" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             class="relative w-full max-w-sm transform overflow-hidden rounded-[1.5rem] border border-emerald-800/15 bg-white p-4 text-center shadow-xl transition-all dark:border-slate-700 dark:bg-slate-900 sm:my-8">
            <div class="mb-4 mt-3 flex justify-center">
                <div class="flex h-20 w-20 items-center justify-center rounded-full border-2 shadow-sm"
                     style="border-color: rgba(5,150,105,0.2); background: linear-gradient(135deg, rgba(5,150,105,0.15), rgba(5,150,105,0.05));">
                    <i class="ph-fill ph-check-circle text-5xl text-emerald-800 dark:text-emerald-400"></i>
                </div>
            </div>

            <h4 class="mb-1 font-black text-slate-900 dark:text-white">Pembayaran Berhasil!</h4>
            <p class="mb-4 text-sm font-semibold text-slate-500 dark:text-slate-400">
                No. Invoice:
                <span class="rounded border border-emerald-800/15 bg-slate-50 px-2 py-1 font-black text-slate-900 dark:border-slate-800 dark:bg-slate-950 dark:text-white" x-text="lastOrder.invoice_code"></span>
            </p>

            <div class="flex flex-col gap-3">
                <div class="text-start">
                    <label class="mb-2 text-xs font-bold text-slate-600 dark:text-slate-400">Kirim Struk WhatsApp (Opsional)</label>
                    <div class="flex overflow-hidden rounded-2xl border border-emerald-800/20 shadow-sm">
                        <span class="flex items-center border-r border-emerald-800/10 bg-slate-50 px-3 text-emerald-800 dark:border-slate-800 dark:bg-slate-950 dark:text-emerald-400">
                            <i class="ph-bold ph-phone"></i>
                        </span>
                        <input type="text" class="w-full border-0 bg-white px-2 text-sm font-bold text-slate-900 outline-none dark:bg-slate-900 dark:text-white"
                               x-model="lastOrder.customer_phone" placeholder="Cth: 0812...">
                    </div>
                </div>

                <template x-if="lastOrder.customer_phone && lastOrder.customer_phone.length >= 9">
                    <button type="button" @click="sendWa"
                            class="flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-[#25D366] to-[#128C7E] p-3 text-sm font-black text-white shadow-sm transition hover:scale-[1.02]"
                            style="border: none;">
                        <i class="ph-bold ph-whatsapp-logo text-lg"></i> Kirim Struk via WA
                    </button>
                </template>

                <div class="flex gap-2">
                    <button type="button" @click="window.open('/invoice/' + lastOrder.invoice_code, '_blank')"
                            class="flex flex-1 items-center justify-center gap-2 rounded-2xl border border-emerald-800/15 bg-white p-3 text-sm font-black text-slate-700 shadow-sm transition hover:bg-slate-50 hover:scale-[1.02] dark:border-slate-800 dark:bg-slate-950 dark:text-slate-200 dark:hover:bg-slate-800">
                        <i class="ph-bold ph-printer text-lg"></i> Cetak Struk
                    </button>
                </div>

                <button type="button" @click="closeSuccessModal"
                        class="mt-2 flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-emerald-800 to-emerald-600 p-3 text-sm font-black text-white shadow-sm transition hover:scale-[1.02] dark:from-emerald-500 dark:to-emerald-400 dark:text-slate-950"
                        style="border: none;">
                    Tutup & Pesanan Baru
                </button>
            </div>
        </div>
    </div>
</div>
