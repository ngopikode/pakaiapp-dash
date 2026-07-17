<div x-show="isMergeModalOpen" x-cloak class="fixed inset-0 z-[1050] overflow-y-auto" aria-labelledby="mergeModalLabel" role="dialog" aria-modal="true" @keydown.escape.window="isMergeModalOpen = false">
    <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
        <div x-show="isMergeModalOpen"
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"
             @click="isMergeModalOpen = false" aria-hidden="true"></div>

        <div x-show="isMergeModalOpen"
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative flex w-full max-w-lg transform flex-col overflow-hidden rounded-[24px] border border-border bg-card text-left shadow-2xl transition-all sm:my-8">
            <div class="flex items-start justify-between gap-4 border-b border-border px-5 py-4">
                <div class="flex min-w-0 items-center gap-3">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary">
                        <i class="ph-bold ph-arrows-in text-xl"></i>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-extrabold text-foreground" id="mergeModalLabel">Gabung Bill</h3>
                        <p class="mt-0.5 truncate text-xs font-medium text-muted-foreground">
                            Target <span class="font-bold text-foreground" x-text="mergeTargetInvoice"></span>
                        </p>
                    </div>
                </div>
                <button type="button" @click="isMergeModalOpen = false" aria-label="Tutup" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-muted-foreground transition-colors hover:bg-secondary hover:text-foreground">
                    <i class="ph-bold ph-x text-lg"></i>
                </button>
            </div>

            <div class="space-y-4 px-5 py-4">
                <p class="text-sm font-medium leading-relaxed text-muted-foreground">
                    Pilih bill sumber yang akan dipindahkan ke bill target. Semua item dan nominal akan digabung ke bill target.
                </p>

                <div>
                    <label class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-muted-foreground">Bill Sumber</label>
                    <select class="w-full rounded-2xl border border-border bg-card px-4 py-3 text-sm font-bold text-foreground outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20" x-model="mergeSourceId">
                        <option value="">Pilih bill yang akan digabung</option>
                        @foreach($this->activeOrders as $po)
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

                <div class="rounded-2xl border border-amber-500/20 bg-amber-500/10 p-4">
                    <div class="flex items-start gap-3">
                        <i class="ph-fill ph-warning mt-0.5 shrink-0 text-lg text-amber-500"></i>
                        <p class="text-xs font-semibold leading-relaxed text-amber-900 dark:text-amber-300">
                            Pesanan sumber akan dihapus setelah digabung. Pastikan bill target sudah benar.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 border-t border-border bg-secondary/30 px-5 py-4">
                <button type="button" class="flex-1 rounded-2xl border border-border bg-card px-5 py-3 text-sm font-bold text-foreground transition-colors hover:bg-secondary" @click="isMergeModalOpen = false">Batal</button>
                <button type="button" class="flex-[1.4] rounded-2xl bg-primary px-5 py-3 text-sm font-extrabold text-primary-foreground shadow-sm transition-colors hover:bg-primary/90 disabled:cursor-not-allowed disabled:opacity-50" @click="submitMergeOrder()" :disabled="!mergeSourceId">
                    <i class="ph-bold ph-link me-1"></i> Gabungkan
                </button>
            </div>
        </div>
    </div>
</div>
