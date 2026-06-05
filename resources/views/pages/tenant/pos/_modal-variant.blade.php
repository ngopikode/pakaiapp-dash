{{-- ===== VARIANT MODAL (Shared between Resto & Retail) ===== --}}
<div class="modal fade" id="variantModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg bg-body text-body"
             style="border-radius: 1.25rem; border-color: var(--bs-border-color-translucent) !important;">
            <div class="modal-header border-bottom pb-3 pt-4 px-4 bg-body-tertiary"
                 style="border-top-left-radius: 1.25rem; border-top-right-radius: 1.25rem; border-color: var(--bs-border-color-translucent) !important;">
                <div>
                    <h4 class="fw-bold font-serif mb-1" style="color: var(--brand-caramel, #b45309);">Pilih Varian</h4>
                    <p class="text-secondary small mb-0" x-text="selectedProduct ? selectedProduct.name : ''"></p>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-body"
                 style="border-bottom-left-radius: 1.25rem; border-bottom-right-radius: 1.25rem;">
                <div class="d-flex flex-column gap-2">
                    <template x-if="selectedProduct">
                        <template x-for="variant in selectedProduct.variants" :key="variant.id">
                            <button type="button"
                                    class="card flex-row justify-content-between align-items-center p-3 text-start w-100 border transition-all bg-body"
                                    :class="{'opacity-50 bg-body-tertiary': variant.stock <= 0, 'border-primary shadow-sm': variant.stock > 0}"
                                    :disabled="variant.stock <= 0"
                                    @click="if(variant.stock > 0) addVariantToCart(variant)"
                                    style="border-radius: 1rem; border-color: var(--bs-border-color-translucent) !important;">
                                <div>
                                    <h6 class="fw-bold text-body mb-1" x-text="variant.name"></h6>
                                    <template x-if="variant.sku">
                                        <div class="text-secondary mb-2" style="font-size: 0.75rem;">
                                            <i class="bi bi-upc-scan me-1"></i><span x-text="variant.sku"></span>
                                        </div>
                                    </template>
                                    <span class="small badge rounded-pill fw-medium"
                                          :class="variant.stock > 0 ? 'bg-body-tertiary text-secondary border' : 'bg-danger text-white'"
                                          x-text="variant.stock > 0 ? 'Tersedia: ' + variant.stock : 'Stok Habis'"></span>
                                </div>
                                <h5 class="fw-bold mb-0" style="color: var(--brand-caramel, #b45309);"
                                    x-text="'Rp ' + formatRupiah(variant.price)"></h5>
                            </button>
                        </template>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>
