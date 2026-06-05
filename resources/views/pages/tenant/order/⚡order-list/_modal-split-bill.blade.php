<div class="modal fade" id="splitBillModal" tabindex="-1" aria-hidden="true" wire:ignore>
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg"
             style="border-radius: 1.5rem; background-color: var(--bs-card-bg);">

            {{-- Header --}}
            <div class="modal-header border-bottom px-4 py-3 flex-shrink-0 align-items-center justify-content-between"
                 style="border-radius: 1.5rem 1.5rem 0 0; background-color: var(--bs-body-bg); border-color: var(--bs-border-color-translucent) !important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                        <i class="bi bi-layout-split fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-body" style="letter-spacing: -0.5px;">Pisah Bill</h5>
                        <div class="text-secondary small fw-medium" x-text="splittingOrder ? '#' + splittingOrder.invoice_code : ''"></div>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- Body --}}
            <div class="modal-body p-0 bg-body overflow-y-auto">
                {{-- Info Banner --}}
                <div class="bg-warning bg-opacity-10 border-bottom px-4 py-3 d-flex gap-3 align-items-start" style="border-color: rgba(245, 158, 11, 0.2) !important;">
                    <i class="bi bi-info-circle-fill text-warning mt-1 fs-5"></i>
                    <p class="mb-0 text-dark small fw-medium" style="line-height: 1.5;">
                        Item yang dipilih akan <b class="text-warning">dicabut</b> dari pesanan saat ini dan dibuatkan <b>Nomor Tagihan Baru</b> secara otomatis.
                    </p>
                </div>

                {{-- Item List --}}
                <div class="list-group list-group-flush mt-2">
                    <template x-for="(item, index) in splitItems" :key="item.id">
                        <div class="list-group-item bg-transparent border-bottom px-4 py-3 d-flex justify-content-between align-items-center gap-3"
                             style="border-color: var(--bs-border-color-translucent) !important;">
                            
                            {{-- Item Info --}}
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-1 text-body" x-text="item.name"></h6>
                                <template x-if="item.variant_name">
                                    <div class="small text-secondary mb-1 fw-medium" x-text="item.variant_name"></div>
                                </template>
                                <div class="fw-bold text-primary small" x-text="'Rp ' + item.price.toLocaleString('id-ID')"></div>
                            </div>

                            {{-- Item Controls --}}
                            <div class="d-flex flex-column align-items-end gap-2">
                                <div class="d-flex align-items-center bg-body-tertiary rounded-pill border shadow-sm"
                                     style="padding: 0.25rem; border-color: var(--bs-border-color) !important;">
                                    <button @click="item.qtyToSplit > 0 ? item.qtyToSplit-- : null"
                                            class="btn btn-sm btn-secondary bg-body rounded-circle p-1 shadow-sm border d-flex align-items-center justify-content-center"
                                            style="width: 32px; height: 32px; color: var(--bs-body-color);"
                                            :class="item.qtyToSplit === 0 ? 'opacity-50' : ''"
                                            :disabled="item.qtyToSplit === 0">
                                        <i class="bi bi-dash fs-6"></i>
                                    </button>
                                    
                                    <span class="fw-bold px-3 small text-body" style="min-width: 30px; text-align: center;" x-text="item.qtyToSplit"></span>
                                    
                                    <button @click="item.qtyToSplit < item.maxQty ? item.qtyToSplit++ : null"
                                            class="btn btn-sm btn-warning rounded-circle p-1 shadow-sm border-0 d-flex align-items-center justify-content-center"
                                            style="width: 32px; height: 32px; color: #fff;" 
                                            :class="item.qtyToSplit >= item.maxQty ? 'opacity-50' : ''"
                                            :disabled="item.qtyToSplit >= item.maxQty">
                                        <i class="bi bi-plus fs-6 text-dark"></i>
                                    </button>
                                </div>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border rounded-pill px-2" style="font-size: 0.65rem;" x-text="'Maks: ' + item.maxQty"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Footer --}}
            <div class="modal-footer border-top p-3 bg-body flex-nowrap align-items-center" style="border-radius: 0 0 1.5rem 1.5rem; border-color: var(--bs-border-color-translucent) !important;">
                <div class="d-flex flex-column me-auto ps-2">
                    <small class="text-secondary fw-medium" style="font-size: 0.75rem;">Total Item Dipisah</small>
                    <span class="fw-bold text-body fs-5" x-text="splitTotalItems"></span>
                </div>
                
                <button type="button" class="btn btn-outline-secondary fw-bold rounded-pill px-4" data-bs-dismiss="modal">
                    Batal
                </button>
                <button type="button" class="btn btn-warning fw-bold rounded-pill px-4 text-dark d-flex align-items-center justify-content-center gap-2 shadow-sm hover-lift"
                        @click="submitSplitOrder()" :disabled="splitTotalItems === 0">
                    <i class="bi bi-arrow-right-circle-fill"></i> Lanjut Pisah
                </button>
            </div>
        </div>
    </div>
</div>
