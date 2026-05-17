<div class="modal fade modal-bottom-mobile" id="optionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg" style="border-radius: 1.5rem;">
            <div class="modal-header border-bottom pb-3 pt-4 px-4 bg-light"
                 style="border-radius: 1.5rem 1.5rem 0 0;">
                <div>
                    <h5 class="fw-bold text-dark mb-1">Pilih Varian</h5>
                    <p class="text-muted small mb-0" x-text="optionProduct ? optionProduct.name : ''"></p>
                    <template x-if="optionProduct && optionProduct.selection_type === 'multiple'">
                            <span class="badge bg-warning text-dark mt-1"
                                  x-text="'Pilih maks ' + optionProduct.max_selections + ' pilihan'"></span>
                    </template>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-white" style="border-radius: 0 0 1.5rem 1.5rem;">
                <div class="d-flex flex-column gap-2 overflow-y-auto" style="max-height: 50vh;">
                    <template x-if="optionProduct">
                        <template x-for="variant in optionProduct.variants" :key="variant.id">
                            <button type="button"
                                    class="card flex-row justify-content-between align-items-center p-3 text-start w-100 border transition-all"
                                    :class="{
                                            'opacity-50 bg-light': variant.stock <= 0,
                                            'border-warning bg-warning bg-opacity-10': isOptionSelected(variant.name),
                                            'border-light': variant.stock > 0 && !isOptionSelected(variant.name)
                                        }"
                                    :disabled="variant.stock <= 0"
                                    @click="if(variant.stock > 0) toggleOption(variant)"
                                    style="border-radius: 1rem;">
                                <div class="d-flex align-items-center gap-3">
                                    <template x-if="optionProduct.selection_type === 'multiple'">
                                        <i class="bi fs-4"
                                           :class="isOptionSelected(variant.name) ? 'bi-check-square-fill text-warning' : 'bi-square text-muted'"></i>
                                    </template>
                                    <template x-if="optionProduct.selection_type !== 'multiple'">
                                        <i class="bi fs-4"
                                           :class="isOptionSelected(variant.name) ? 'bi-record-circle-fill text-warning' : 'bi-circle text-muted'"></i>
                                    </template>
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0" x-text="variant.name"></h6>
                                    </div>
                                </div>
                                <h6 class="fw-bold text-secondary mb-0"
                                    x-text="optionProduct.selection_type === 'multiple' ? '' : '+ Rp ' + formatRupiah(variant.price)"></h6>
                            </button>
                        </template>
                    </template>
                </div>

                {{-- Qty + Confirm --}}
                <div class="mt-4 pt-3 border-top" x-show="optionSelected.length > 0">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fw-bold text-muted">Jumlah Pesanan</span>
                        <div class="d-flex align-items-center bg-light rounded-pill border"
                             style="padding: 0.25rem;">
                            <button @click="if(optionQty > 1) optionQty--"
                                    class="btn btn-sm btn-white rounded-circle p-1 shadow-sm"
                                    style="width: 36px; height: 36px;"><i class="bi bi-dash"></i></button>
                            <span class="fw-bold px-4 fs-5" x-text="optionQty"></span>
                            <button @click="optionQty++" class="btn btn-sm btn-primary rounded-circle p-1 shadow-sm"
                                    style="width: 36px; height: 36px;"><i class="bi bi-plus"></i></button>
                        </div>
                    </div>
                    <button @click="confirmOption"
                            class="btn btn-primary fw-bold w-100 py-3 d-flex justify-content-between align-items-center shadow-sm"
                            style="border-radius: 1rem;"
                            :disabled="optionSelected.length === 0">
                        <span><i class="bi bi-cart-plus me-2"></i>Tambahkan ke Keranjang</span>
                        <span x-text="'Rp ' + formatRupiah(optionTotalPrice)"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
