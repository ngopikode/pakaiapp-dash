<div x-show="isMergeModalOpen" x-cloak class="fixed inset-0 z-[1050] overflow-y-auto" aria-labelledby="mergeModalLabel" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
        {{-- Backdrop --}}
        <div x-show="isMergeModalOpen" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" 
             @click="isMergeModalOpen = false" aria-hidden="true"></div>

        {{-- Modal Dialog --}}
        <div x-show="isMergeModalOpen" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             class="relative w-full max-w-lg transform overflow-hidden rounded-[1.5rem] border border-emerald-800/15 bg-white text-left shadow-xl transition-all dark:border-slate-700 dark:bg-slate-900 sm:my-8 flex flex-col">
            <div class="flex items-center justify-between rounded-t-[1.5rem] border-b border-emerald-800/10 bg-emerald-50/80 px-4 py-3 dark:border-slate-800 dark:bg-slate-900">
                <h5 class="mb-0 font-black text-emerald-800 dark:text-emerald-400" id="mergeModalLabel">
                    <i class="ph-bold ph-arrows-in me-2"></i>Gabung Struk (Merge Bill)
                </h5>
                <button type="button" @click="isMergeModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300"><i class="ph-bold ph-x text-lg"></i></button>
            </div>
            <div class="modal-body px-4 pb-4 pt-3">
                <p class="mb-3 text-sm font-semibold text-slate-600 dark:text-slate-400">
                    Pilih pesanan (struk) lain yang ingin Anda masukkan ke dalam pesanan <strong class="text-slate-900 dark:text-white" x-text="mergeTargetInvoice"></strong>. Pesanan yang dipilih akan digabungkan itemnya dan tagihannya ke pesanan target ini.
                </p>

                <div class="mb-4">
                    <label class="form-label font-black text-slate-900 dark:text-white">Pilih Pesanan Sumber</label>
                    <select class="form-select rounded-2xl border-emerald-800/20 bg-white text-sm font-bold text-slate-900 shadow-sm dark:border-slate-800 dark:bg-slate-950 dark:text-white" x-model="mergeSourceId">
                        <option value="">-- Pilih Pesanan --</option>
                        @foreach($this->pendingOrders as $po)
                            @if($po->amount_paid == 0)
                                <option value="{{ $po->id }}" x-show="mergeTargetId != {{ $po->id }}">
                                    {{ $po->invoice_code }} - {{ $po->customer_name ?: 'Pelanggan' }} 
                                    ({{ $po->table_number ? 'Meja '.$po->table_number : ($po->notes ?: 'Tanpa Meja') }})
                                    - Rp {{ number_format($po->total_price, 0, ',', '.') }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="rounded-2xl border-0 bg-amber-50 p-4 dark:bg-amber-500/10">
                    <div class="flex items-start gap-3">
                        <i class="ph-fill ph-warning mt-0.5 text-lg text-amber-500"></i>
                        <p class="mb-0 text-xs font-semibold text-amber-900 dark:text-amber-300">
                            <strong>Perhatian:</strong> Pesanan sumber yang dipilih akan <strong>Dihapus Permanen</strong> setelah digabungkan, dan seluruh isinya akan pindah ke pesanan target.
                        </p>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-between rounded-b-[1.5rem] border-t border-emerald-800/10 bg-slate-50 px-4 py-3 dark:border-slate-800 dark:bg-slate-950">
                <button type="button" class="rounded-2xl border border-emerald-800/20 bg-white px-5 py-2.5 text-sm font-black text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200" @click="isMergeModalOpen = false">Batal</button>
                <button type="button" class="rounded-2xl bg-emerald-800 px-5 py-2.5 text-sm font-black text-white shadow-sm transition hover:bg-emerald-700 dark:bg-emerald-500 dark:text-slate-950" @click="submitMergeOrder()">
                    <i class="ph-bold ph-link me-1"></i> Gabungkan Sekarang
                </button>
            </div>
        </div>
    </div>
</div>
