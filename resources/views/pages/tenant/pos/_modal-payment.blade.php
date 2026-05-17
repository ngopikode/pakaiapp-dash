{{-- ===== PAYMENT MODAL (Shared between Resto & Retail) ===== --}}
<div class="modal fade modal-bottom-mobile" id="paymentModal" tabindex="-1" aria-hidden="true"
     data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg d-flex flex-column bg-body text-body"
             style="border-radius: 1.5rem; max-height: 95vh; border-color: var(--bs-border-color-translucent) !important;">

            {{-- Header (Sticky) --}}
            <div class="modal-header border-bottom bg-body-tertiary px-4 py-3 flex-shrink-0"
                 style="border-radius: 1.5rem 1.5rem 0 0; border-color: var(--bs-border-color-translucent) !important;">
                <h5 class="fw-bold mb-0">Pembayaran</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- Body (Scrollable only if content overflows) --}}
            <div class="modal-body p-3 p-md-4 bg-body overflow-y-auto">
                <div class="row g-3 g-md-4">

                    <!-- Kolom Total & Metode -->
                    <div class="col-md-5 border-end-md pe-md-4"
                         style="border-color: var(--bs-border-color-translucent) !important;">
                        <div class="p-3 bg-body-tertiary rounded-4 mb-3 text-center border"
                             style="border-color: var(--bs-border-color-translucent) !important;">
                            <h6 class="fw-bold text-muted mb-1 small">Total Tagihan</h6>
                            <h2 class="fw-bolder mb-0 text-primary" x-text="'Rp ' + formatRupiah(payTotal)"></h2>
                        </div>

                        <!-- Metode Pembayaran: Grid 3 kolom di HP, Stack vertikal di Desktop -->
                        <div class="row g-2">
                            <!-- Option 1: Cash -->
                            <div class="col-4 col-md-12">
                                <label
                                    class="btn fw-bold w-100 h-100 p-2 p-md-3 rounded-4 transition-all d-flex flex-column flex-md-row align-items-center justify-content-center justify-content-md-start gap-1 gap-md-2 border text-body"
                                    :class="paymentMethod === 'cash' ? 'btn-primary shadow-sm text-white' : 'bg-body-tertiary'">
                                    <input type="radio" x-model="paymentMethod" value="cash" class="d-none">
                                    <i class="bi bi-cash-stack fs-5 fs-md-6"></i> <span
                                        style="font-size: 0.8rem;">Tunai</span>
                                </label>
                            </div>
                            <!-- Option 2: QRIS -->
                            <div class="col-4 col-md-12">
                                <label
                                    class="btn fw-bold w-100 h-100 p-2 p-md-3 rounded-4 transition-all d-flex flex-column flex-md-row align-items-center justify-content-center justify-content-md-start gap-1 gap-md-2 border text-body"
                                    :class="paymentMethod === 'qris' ? 'btn-primary shadow-sm text-white' : 'bg-body-tertiary'">
                                    <input type="radio" x-model="paymentMethod" value="qris" class="d-none">
                                    <i class="bi bi-qr-code-scan fs-5 fs-md-6"></i> <span style="font-size: 0.8rem;">QRIS</span>
                                </label>
                            </div>
                            <!-- Option 3: Transfer -->
                            <div class="col-4 col-md-12">
                                <label
                                    class="btn fw-bold w-100 h-100 p-2 p-md-3 rounded-4 transition-all d-flex flex-column flex-md-row align-items-center justify-content-center justify-content-md-start gap-1 gap-md-2 border text-body"
                                    :class="paymentMethod === 'transfer' ? 'btn-primary shadow-sm text-white' : 'bg-body-tertiary'">
                                    <input type="radio" x-model="paymentMethod" value="transfer" class="d-none">
                                    <i class="bi bi-bank fs-5 fs-md-6"></i> <span
                                        style="font-size: 0.8rem;">Transfer</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Kolom Aksi / Numpad -->
                    <div class="col-md-7">

                        <!-- Tampilan QRIS / Transfer -->
                        <template x-if="paymentMethod !== 'cash'">
                            <div
                                class="d-flex flex-column justify-content-center align-items-center h-100 py-4 py-md-5 text-center">
                                <i class="bi text-primary mb-2"
                                   :class="paymentMethod === 'qris' ? 'bi-qr-code-scan' : 'bi-bank'"
                                   style="font-size: 4rem;"></i>
                                <h5 class="fw-bold"
                                    x-text="paymentMethod === 'qris' ? 'Pembayaran QRIS' : 'Transfer Bank'"></h5>
                                <p class="text-secondary small px-2 opacity-75">Pastikan pelanggan sudah berhasil
                                    transfer sebelum menekan tombol proses.</p>
                            </div>
                        </template>

                        <!-- Tampilan Numpad Cash -->
                        <template x-if="paymentMethod === 'cash'">
                            <div class="d-flex flex-column h-100 justify-content-end">
                                <!-- Input Uang -->
                                <div class="form-floating mb-2">
                                    <input type="text"
                                           class="form-control fw-bold text-primary bg-body-tertiary border-0"
                                           readonly :value="amountPaid ? 'Rp ' + formatRupiah(amountPaid) : 'Rp 0'"
                                           style="border-radius: 1rem; font-size: 1.25rem;">
                                    <label class="fw-bold text-muted small">Uang Diterima</label>
                                </div>

                                <!-- Quick Amounts -->
                                <div class="row g-2 mb-2">
                                    <div class="col-4">
                                        <button @click="amountPaid = payTotal"
                                                class="btn btn-outline-primary w-100 fw-bold py-1 py-md-2 rounded-3 small bg-body">
                                            Pas
                                        </button>
                                    </div>
                                    <div class="col-4">
                                        <button @click="amountPaid = 50000"
                                                class="btn btn-secondary border w-100 fw-bold py-1 py-md-2 rounded-3 small bg-body text-body">
                                            50k
                                        </button>
                                    </div>
                                    <div class="col-4">
                                        <button @click="amountPaid = 100000"
                                                class="btn btn-secondary border w-100 fw-bold py-1 py-md-2 rounded-3 small bg-body text-body">
                                            100k
                                        </button>
                                    </div>
                                </div>

                                <!-- Numpad Grid -->
                                <div class="row g-2 mb-2">
                                    <template x-for="n in [1, 2, 3, 4, 5, 6, 7, 8, 9]" :key="n">
                                        <div class="col-4">
                                            <button @click="appendNumber(n)"
                                                    class="btn btn-secondary border w-100 fs-5 fw-bold py-2 shadow-sm rounded-3 bg-body text-body"
                                                    x-text="n"></button>
                                        </div>
                                    </template>
                                    <div class="col-4">
                                        <button @click="appendNumber('000')"
                                                class="btn btn-secondary border w-100 fs-5 fw-bold py-2 shadow-sm rounded-3 bg-body text-body">
                                            000
                                        </button>
                                    </div>
                                    <div class="col-4">
                                        <button @click="appendNumber('0')"
                                                class="btn btn-secondary border w-100 fs-5 fw-bold py-2 shadow-sm rounded-3 bg-body text-body">
                                            0
                                        </button>
                                    </div>
                                    <div class="col-4">
                                        <button @click="deleteNumber()"
                                                class="btn btn-secondary border w-100 fs-5 fw-bold py-2 shadow-sm rounded-3 text-danger bg-body">
                                            <i class="bi bi-backspace-fill"></i></button>
                                    </div>
                                </div>

                                <!-- Kembalian (Muncul HANYA jika ada kembalian) -->
                                <template x-if="amountPaid && getChange >= 0">
                                    <div
                                        class="d-flex justify-content-between align-items-center p-2 px-3 bg-success bg-opacity-10 rounded-3 border border-success border-opacity-25 mt-1">
                                        <span class="text-success fw-bold small">Kembalian:</span>
                                        <h5 class="fw-bolder text-success mb-0"
                                            x-text="'Rp ' + formatRupiah(getChange)"></h5>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Footer (Sticky) --}}
            <div class="modal-footer bg-body-tertiary border-top p-3 flex-shrink-0"
                 style="border-radius: 0 0 1.5rem 1.5rem; border-color: var(--bs-border-color-translucent) !important;">
                <div class="d-flex w-100 gap-2">
                    <button type="button"
                            class="btn btn-secondary border fw-bold flex-shrink-0 rounded-pill shadow-sm bg-body text-body"
                            data-bs-dismiss="modal">Batal
                    </button>
                    <button @click="submitPayment"
                            class="btn btn-primary fw-bold flex-grow-1 rounded-pill shadow-sm d-flex align-items-center justify-content-center gap-2 text-white"
                            style="background: linear-gradient(135deg, #ca8a04, #b45309); border: none;"
                            :disabled="isSubmitting || (paymentMethod === 'cash' && (!amountPaid || getChange < 0))">
                        <span x-text="isSubmitting ? 'Memproses...' : 'Selesaikan Transaksi'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
