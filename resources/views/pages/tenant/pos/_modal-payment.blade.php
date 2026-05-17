{{-- ===== PAYMENT MODAL (Shared between Resto & Retail) ===== --}}
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1.25rem;">
            <div class="modal-header border-bottom bg-body-tertiary px-4 py-3"
                 style="border-top-left-radius: 1.25rem; border-top-right-radius: 1.25rem;">
                <h4 class="fw-bold font-serif text-primary mb-0">Pembayaran</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4 bg-body">
                <div class="row g-4">
                    <div class="col-md-5 border-end pe-md-4">
                        <h6 class="fw-bold text-muted mb-3">Total Tagihan</h6>
                        <h2 class="fw-bolder mb-1" style="color: var(--brand-caramel);"
                            x-text="'Rp ' + formatRupiah(payTotal)"></h2>
                        <template x-if="payDiscount > 0">
                            <p class="text-danger small fw-bold mb-4"
                               x-text="'Termasuk Diskon: -Rp ' + formatRupiah(payDiscount)"></p>
                        </template>
                        <template x-if="!payDiscount">
                            <div class="mb-4"></div>
                        </template>

                        <h6 class="fw-bold text-muted mb-3">Metode Pembayaran</h6>
                        <div class="d-flex flex-column gap-2">
                            <label class="btn btn-outline-primary fw-bold text-start p-3 rounded-4"
                                   :class="paymentMethod === 'cash' ? 'active' : ''">
                                <input type="radio" x-model="paymentMethod" value="cash" class="d-none"> <i
                                    class="bi bi-cash-stack me-2"></i> Tunai (Cash)
                            </label>
                            <label class="btn btn-outline-primary fw-bold text-start p-3 rounded-4"
                                   :class="paymentMethod === 'qris' ? 'active' : ''">
                                <input type="radio" x-model="paymentMethod" value="qris" class="d-none"> <i
                                    class="bi bi-qr-code-scan me-2"></i> QRIS
                            </label>
                            <label class="btn btn-outline-primary fw-bold text-start p-3 rounded-4"
                                   :class="paymentMethod === 'transfer' ? 'active' : ''">
                                <input type="radio" x-model="paymentMethod" value="transfer" class="d-none"> <i
                                    class="bi bi-bank me-2"></i> Transfer Bank
                            </label>
                        </div>
                    </div>

                    <div class="col-md-7">
                        <template x-if="paymentMethod !== 'cash'">
                            <div class="d-flex flex-column justify-content-center align-items-center h-100 text-center">
                                <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                                <h5 class="fw-bold mt-3">Pembayaran Non-Tunai</h5>
                                <p class="text-muted small">Pastikan saldo pelanggan sudah masuk sebelum klik Proses.</p>
                            </div>
                        </template>

                        <template x-if="paymentMethod === 'cash'">
                            <div>
                                <div class="p-3 border border-primary bg-body mb-3 d-flex justify-content-between align-items-center shadow-sm"
                                     style="border-radius: 1rem;">
                                    <span class="fw-bold text-primary">Diterima:</span>
                                    <h4 class="fw-bolder text-primary mb-0"
                                        x-text="amountPaid ? 'Rp ' + formatRupiah(amountPaid) : 'Rp 0'"></h4>
                                </div>
                                <div class="row g-2 mb-2">
                                    <div class="col-4">
                                        <button type="button" @click="amountPaid = payTotal"
                                                class="btn btn-outline-primary w-100 fw-bold" style="border-radius: 0.75rem;">Pas</button>
                                    </div>
                                    <div class="col-4">
                                        <button type="button" @click="amountPaid = 50000"
                                                class="btn btn-outline-secondary w-100 fw-bold bg-body" style="border-radius: 0.75rem;">50k</button>
                                    </div>
                                    <div class="col-4">
                                        <button type="button" @click="amountPaid = 100000"
                                                class="btn btn-outline-secondary w-100 fw-bold bg-body" style="border-radius: 0.75rem;">100k</button>
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <template x-for="n in [1, 2, 3, 4, 5, 6, 7, 8, 9]" :key="n">
                                        <div class="col-4">
                                            <button type="button" @click="appendNumber(n)"
                                                    class="btn btn-light border w-100 fs-4 fw-bold py-2 text-dark bg-body shadow-sm"
                                                    style="border-radius: 0.75rem;" x-text="n"></button>
                                        </div>
                                    </template>
                                    <div class="col-4">
                                        <button type="button" @click="appendNumber('000')"
                                                class="btn btn-light border w-100 fs-4 fw-bold py-2 text-dark bg-body shadow-sm"
                                                style="border-radius: 0.75rem;">000</button>
                                    </div>
                                    <div class="col-4">
                                        <button type="button" @click="appendNumber('0')"
                                                class="btn btn-light border w-100 fs-4 fw-bold py-2 text-dark bg-body shadow-sm"
                                                style="border-radius: 0.75rem;">0</button>
                                    </div>
                                    <div class="col-4">
                                        <button type="button" @click="deleteNumber()"
                                                class="btn btn-light border w-100 fs-4 fw-bold py-2 text-danger bg-body shadow-sm d-flex justify-content-center align-items-center"
                                                style="border-radius: 0.75rem; height: 100%;"><i class="bi bi-backspace-fill"></i></button>
                                    </div>
                                </div>

                                <template x-if="amountPaid && getChange >= 0">
                                    <div class="d-flex justify-content-between p-3 bg-success bg-opacity-10 rounded-4 border border-success border-opacity-25 shadow-sm">
                                        <span class="text-success fw-bold">Kembalian:</span>
                                        <h5 class="fw-bolder text-success mb-0" x-text="'Rp ' + formatRupiah(getChange)"></h5>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-body-tertiary border-top p-3"
                 style="border-bottom-left-radius: 1.25rem; border-bottom-right-radius: 1.25rem;">
                <button type="button" class="btn btn-outline-secondary fw-bold" data-bs-dismiss="modal"
                        style="border-radius: 1rem; padding: 0.75rem 1.5rem;">Batal</button>
                <button @click="submitPayment"
                        class="btn btn-primary fw-bold shadow-sm d-flex align-items-center gap-2"
                        :disabled="isSubmitting || (paymentMethod === 'cash' && (!amountPaid || getChange < 0))"
                        style="border-radius: 1rem; padding: 0.75rem 2rem;">
                    <i class="bi bi-check2-circle" x-show="!isSubmitting"></i>
                    <span class="spinner-border spinner-border-sm" x-show="isSubmitting"></span>
                    <span x-text="isSubmitting ? 'Memproses...' : 'Proses Transaksi'"></span>
                </button>
            </div>
        </div>
    </div>
</div>
