{{-- Modal Held Orders --}}
<div class="modal fade" id="heldOrdersModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1.5rem;">
            <div class="modal-header border-bottom bg-body-tertiary px-4 py-3" style="border-radius: 1.5rem 1.5rem 0 0;">
                <h5 class="modal-title fw-bold">Daftar Pesanan Tertunda</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-body">
                <template x-if="heldOrders.length === 0">
                    <div class="text-center py-5 text-muted opacity-50">
                        <i class="bi bi-inbox fs-1 mb-3"></i>
                        <p class="fw-bold mb-0">Tidak ada pesanan tertunda</p>
                    </div>
                </template>
                <div class="d-flex flex-column gap-3">
                    <template x-for="(order, index) in heldOrders" :key="index">
                        <div class="card p-3 border shadow-sm bg-body" style="border-radius: 1rem;">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="fw-bold mb-1" x-text="order.customerName || 'Pelanggan ' + (index + 1)"></h6>
                                    <small class="text-secondary" x-text="order.cart.length + ' item | Rp ' + formatRupiah(order.grandTotal)"></small>
                                </div>
                                <span class="badge bg-body-tertiary text-secondary border rounded-pill" x-text="new Date(order.time).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'})"></span>
                            </div>
                            <div class="d-flex gap-2 mt-2">
                                <button @click="recallOrder(index)" class="btn btn-sm btn-primary flex-grow-1 fw-bold rounded-pill">
                                    <i class="bi bi-arrow-return-right"></i> Lanjutkan
                                </button>
                                <button @click="removeHeldOrder(index)" class="btn btn-sm btn-outline-danger fw-bold rounded-pill px-3">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>
