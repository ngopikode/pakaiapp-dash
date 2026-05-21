<div class="modal fade modal-bottom-mobile" id="optionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg bg-body text-body"
             style="border-radius: 1.5rem; border-color: var(--bs-border-color-translucent) !important;">

            {{-- Header Modal --}}
            <div class="modal-header border-bottom pb-3 pt-4 px-4 bg-body-tertiary"
                 style="border-radius: 1.5rem 1.5rem 0 0; border-color: var(--bs-border-color-translucent) !important;">
                <div>
                    <h5 class="fw-bold mb-1">Pilih Varian</h5>
                    <p class="text-secondary small mb-0" x-text="optionProduct ? optionProduct.name : ''"></p>
                    <template x-if="optionProduct && optionProduct.selection_type === 'multiple'">
                        <span class="badge bg-warning text-dark mt-1 fw-bold"
                              x-text="'Pilih maks ' + optionProduct.max_selections + ' pilihan'"></span>
                    </template>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- Body Modal --}}
            <div class="modal-body p-4 bg-body" style="border-radius: 0 0 1.5rem 1.5rem;">
                <div class="d-flex flex-column gap-2 overflow-y-auto" style="max-height: 50vh;">
                    <template x-if="optionProduct">
                        <div>
                            {{-- ----- VARIANTS ----- --}}
                            <template x-if="optionProduct.variants && optionProduct.variants.length > 0 && optionProduct.has_variants">
                                <div class="d-flex flex-column gap-2 mb-3">
                                    <template x-for="variant in optionProduct.variants" :key="variant.id">
                                        <button type="button"
                                                class="card flex-row justify-content-between align-items-center p-3 text-start w-100 border transition-all bg-body"
                                                :class="{
                                                        'opacity-50 bg-body-tertiary': variant.stock <= 0,
                                                        'border-warning bg-warning bg-opacity-10': isOptionSelected(variant.name),
                                                        'border-secondary border-opacity-25': variant.stock > 0 && !isOptionSelected(variant.name)
                                                    }"
                                                :disabled="variant.stock <= 0"
                                                @click="if(variant.stock > 0) toggleOption(variant)"
                                                style="border-radius: 1rem; border-color: var(--bs-border-color-translucent) !important;">
                                            <div class="d-flex align-items-center gap-3">
                                                <template x-if="optionProduct.selection_type === 'multiple'">
                                                    <i class="bi fs-4"
                                                       :class="isOptionSelected(variant.name) ? 'bi-check-square-fill text-warning' : 'bi-square text-secondary opacity-50'"></i>
                                                </template>
                                                <template x-if="optionProduct.selection_type !== 'multiple'">
                                                    <i class="bi fs-4"
                                                       :class="isOptionSelected(variant.name) ? 'bi-record-circle-fill text-warning' : 'bi-circle text-secondary opacity-50'"></i>
                                                </template>
                                                <div>
                                                    <h6 class="fw-bold text-body mb-0" x-text="variant.name"></h6>
                                                </div>
                                            </div>
                                            <h6 class="fw-bold text-secondary mb-0"
                                                x-text="optionProduct.selection_type === 'multiple' ? '' : '+ Rp ' + formatRupiah(variant.price)"></h6>
                                        </button>
                                    </template>
                                </div>
                            </template>

                            {{-- ----- ADD-ONS / EXTRAS ----- --}}
                            <template x-if="optionProduct.extras && optionProduct.extras.length > 0">
                                <div>
                                    <template x-if="optionProduct.has_variants">
                                        <div class="d-flex align-items-center gap-3 my-3">
                                            <div class="flex-1 border-bottom"></div>
                                            <span class="text-secondary small fw-bold text-uppercase">Tambahan / Extra</span>
                                            <div class="flex-1 border-bottom"></div>
                                        </div>
                                    </template>

                                    <div class="d-flex flex-column gap-2">
                                        <template x-for="extra in optionProduct.extras" :key="extra.id">
                                            <button type="button"
                                                    class="card flex-row justify-content-between align-items-center p-3 text-start w-100 border transition-all bg-body"
                                                    :class="{
                                                            'border-warning bg-warning bg-opacity-10': isExtraSelected(extra.name),
                                                            'border-secondary border-opacity-25': !isExtraSelected(extra.name)
                                                        }"
                                                    @click="toggleExtra(extra)"
                                                    style="border-radius: 1rem; border-color: var(--bs-border-color-translucent) !important;">
                                                <div class="d-flex align-items-center gap-3">
                                                    <i class="bi fs-4"
                                                       :class="isExtraSelected(extra.name) ? 'bi-check-square-fill text-warning' : 'bi-square text-secondary opacity-50'"></i>
                                                    <div>
                                                        <h6 class="fw-bold text-body mb-0" x-text="extra.name"></h6>
                                                    </div>
                                                </div>
                                                <h6 class="fw-bold text-secondary mb-0"
                                                    x-text="'+ Rp ' + formatRupiah(extra.price)"></h6>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                {{-- Qty + Confirm --}}
                <div class="mt-4 pt-3 border-top" x-show="optionProduct && (optionSelected.length > 0 || !optionProduct.has_variants || (optionProduct.extras && optionProduct.extras.length > 0))"
                     style="border-color: var(--bs-border-color-translucent) !important;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fw-bold text-secondary small">Jumlah Pesanan</span>
                        <div class="d-flex align-items-center bg-body-tertiary rounded-pill border"
                             style="padding: 0.25rem; border-color: var(--bs-border-color) !important;">
                            <button @click="if(optionQty > 1) optionQty--"
                                    class="btn btn-sm btn-secondary bg-body rounded-circle p-1 shadow-sm border"
                                    style="width: 36px; height: 36px; color: var(--bs-body-color);"><i
                                    class="bi bi-dash"></i></button>
                            <span class="fw-bold px-4 fs-5 text-body" x-text="optionQty"></span>
                            <button @click="optionQty++"
                                    class="btn btn-sm btn-primary rounded-circle p-1 shadow-sm border-0"
                                    style="width: 36px; height: 36px; color: white;"><i class="bi bi-plus"></i></button>
                        </div>
                    </div>
                    <button @click="confirmOption"
                            class="btn btn-primary fw-bold w-100 py-3 d-flex justify-content-between align-items-center shadow-sm text-white border-0"
                            style="border-radius: 1rem; background: linear-gradient(135deg, #ca8a04, #b45309);"
                            :disabled="optionProduct && optionProduct.has_variants && optionSelected.length === 0">
                        <span><i class="bi bi-cart-plus me-2"></i>Tambahkan ke Keranjang</span>
                        <span x-text="'Rp ' + formatRupiah(optionTotalPrice)"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
