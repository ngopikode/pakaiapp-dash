{{-- ===== VARIANT MODAL (Shared between Resto & Retail) ===== --}}
<div class="modal fade" id="variantModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1.25rem;">
            <div class="modal-header border-bottom pb-3 pt-4 px-4 bg-body-tertiary"
                 style="border-top-left-radius: 1.25rem; border-top-right-radius: 1.25rem;">
                <div>
                    <h4 class="fw-bold font-serif text-primary mb-1">Pilih Varian</h4>
                    <p class="text-muted small mb-0" x-text="selectedProduct ? selectedProduct.name : ''"></p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-body"
                 style="border-bottom-left-radius: 1.25rem; border-bottom-right-radius: 1.25rem;">
                <div class="d-flex flex-column gap-2">
                    <template x-if="selectedProduct">
                        <template x-for="variant in selectedProduct.variants" :key="variant.id">
                            <button type="button"
                                    class="card flex-row justify-content-between align-items-center p-3 text-start w-100 border transition-all"
                                    :class="{'opacity-50 bg-body-tertiary': variant.stock <= 0, 'border-primary shadow-sm': variant.stock > 0}"
                                    :disabled="variant.stock <= 0"
                                    @click="if(variant.stock > 0) addVariantToCart(variant)"
                                    style="border-radius: 1rem;">
                                <div>
                                    <h6 class="fw-bold text-dark mb-1" x-text="variant.name"></h6>
                                    <span class="small badge rounded-pill"
                                          :class="variant.stock > 0 ? 'bg-body-tertiary text-muted border' : 'bg-danger text-white'"
                                          x-text="variant.stock > 0 ? 'Tersedia: ' + variant.stock : 'Stok Habis'"></span>
                                </div>
                                <h5 class="fw-bold mb-0" style="color: var(--brand-caramel);"
                                    x-text="'Rp ' + formatRupiah(variant.price)"></h5>
                            </button>
                        </template>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>
