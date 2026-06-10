<div class="modal fade" id="mergeModal" tabindex="-1" aria-labelledby="mergeModalLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-info bg-opacity-10 border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-info" id="mergeModalLabel">
                    <i class="bi bi-arrows-collapse me-2"></i>Gabung Struk (Merge Bill)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3 pb-4 px-4">
                <p class="text-secondary small mb-3">
                    Pilih pesanan (struk) lain yang ingin Anda masukkan ke dalam pesanan <strong class="text-body" x-text="mergeTargetInvoice"></strong>. Pesanan yang dipilih akan digabungkan itemnya dan tagihannya ke pesanan target ini.
                </p>

                <div class="mb-4">
                    <label class="form-label fw-bold text-body">Pilih Pesanan Sumber</label>
                    <select class="form-select form-select-lg shadow-sm" x-model="mergeSourceId" style="border-radius: 12px;">
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
                
                <div class="alert alert-warning border-0 rounded-3 mb-0" style="background: var(--bs-warning-bg-subtle);">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill fs-4 text-warning me-3"></i>
                        <p class="mb-0 small text-dark">
                            <strong>Perhatian:</strong> Pesanan sumber yang dipilih akan <strong>Dihapus Permanen</strong> setelah digabungkan, dan seluruh isinya akan pindah ke pesanan target.
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top-0 pt-0 pb-3 px-4">
                <button type="button" class="btn btn-outline-secondary fw-bold rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-info fw-bold rounded-pill px-4 text-white shadow-sm" @click="submitMergeOrder()">
                    <i class="bi bi-link-45deg me-1"></i> Gabungkan Sekarang
                </button>
            </div>
        </div>
    </div>
</div>
