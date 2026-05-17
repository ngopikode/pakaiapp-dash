{{-- ===== SUCCESS MODAL (Shared between Resto & Retail) ===== --}}
<div class="modal fade" id="successModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg text-center p-4" style="border-radius: 1.25rem;">
            <div class="d-flex justify-content-center mb-4 mt-2">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center"
                     style="width: 80px; height: 80px;">
                    <i class="bi bi-check2-circle" style="font-size: 3.5rem;"></i>
                </div>
            </div>
            <h4 class="fw-bold font-serif text-primary mb-2">Berhasil!</h4>
            <p class="text-muted small mb-4">No: <span class="fw-bold text-dark" x-text="lastOrder.invoice_code"></span></p>

            <div class="d-flex flex-column gap-2 mb-2">
                <div class="mb-3 text-start">
                    <label class="small fw-bold text-muted mb-1">Kirim Struk (Opsional)</label>
                    <input type="text" class="form-control bg-body-tertiary shadow-sm"
                           x-model="lastOrder.customer_phone" placeholder="Ketik No WA Pelanggan..."
                           style="border-radius: 0.75rem;">
                </div>

                <template x-if="lastOrder.customer_phone && lastOrder.customer_phone.length >= 9">
                    <button type="button" @click="sendWa"
                            class="btn btn-success fw-bold p-3 mb-2 d-flex align-items-center justify-content-center gap-2 shadow-sm"
                            style="border-radius: 1rem;">
                        <i class="bi bi-whatsapp fs-5"></i> Kirim Struk ke WA
                    </button>
                </template>

                <button type="button" @click="closeSuccessModal"
                        class="btn btn-outline-secondary fw-bold p-3 mt-1 shadow-sm" style="border-radius: 1rem;">
                    Tutup & Pesanan Baru
                </button>
            </div>
        </div>
    </div>
</div>
