<div class="modal fade" id="mergeModal" tabindex="-1" aria-labelledby="mergeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-[1.5rem] border border-emerald-800/15 bg-white shadow-lg dark:border-slate-700 dark:bg-slate-900">
            <div class="modal-header rounded-t-[1.5rem] border-b border-emerald-800/10 bg-emerald-50/80 pb-0 dark:border-slate-800 dark:bg-slate-900">
                <h5 class="modal-title font-black text-emerald-800 dark:text-emerald-400" id="mergeModalLabel">
                    <i class="bi bi-arrows-collapse me-2"></i>Gabung Struk (Merge Bill)
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 pb-4 pt-3">
                <p class="mb-3 text-sm font-semibold text-slate-600 dark:text-slate-400">
                    Pilih pesanan (struk) lain yang ingin Anda masukkan ke dalam pesanan <strong class="text-slate-900 dark:text-white" x-text="mergeTargetInvoice"></strong>. Pesanan yang dipilih akan digabungkan itemnya dan tagihannya ke pesanan target ini.
                </p>

                <div class="mb-4">
                    <label class="form-label font-black text-slate-900 dark:text-white">Pilih Pesanan Sumber</label>
                    <select class="form-select rounded-2xl border-emerald-800/20 bg-white text-sm font-bold text-slate-900 shadow-sm dark:border-slate-800 dark:bg-slate-950 dark:text-white" x-model="mergeSourceId">
                        <option value="">-- Pilih Pesanan --</option>
                        @foreach($pendingOrders as $po)
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
                        <i class="bi bi-exclamation-triangle-fill mt-0.5 text-lg text-amber-500"></i>
                        <p class="mb-0 text-xs font-semibold text-amber-900 dark:text-amber-300">
                            <strong>Perhatian:</strong> Pesanan sumber yang dipilih akan <strong>Dihapus Permanen</strong> setelah digabungkan, dan seluruh isinya akan pindah ke pesanan target.
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-footer flex items-center justify-between border-t border-emerald-800/10 bg-slate-50 px-4 pb-3 pt-0 dark:border-slate-800 dark:bg-slate-950">
                <button type="button" class="rounded-2xl border border-emerald-800/20 bg-white px-5 py-2.5 text-sm font-black text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="rounded-2xl bg-emerald-800 px-5 py-2.5 text-sm font-black text-white shadow-sm transition hover:bg-emerald-700 dark:bg-emerald-500 dark:text-slate-950" @click="submitMergeOrder()">
                    <i class="bi bi-link-45deg me-1"></i> Gabungkan Sekarang
                </button>
            </div>
        </div>
    </div>
</div>
