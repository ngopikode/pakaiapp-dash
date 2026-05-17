<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-bottom-0 p-4 pb-3">
                <h5 class="modal-title fw-bold">Proses Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 pt-2" x-data="{ 
                method: $wire.entangle('paymentMethod'),
                amount: $wire.entangle('paymentAmount'),
                total: $wire.entangle('paymentTotal'),
                init() {
                    this.$watch('method', value => {
                        if (value !== 'cash') {
                            this.amount = this.total;
                        }
                    });
                },
                appendNumber(n) {
                    let current = String(this.amount || '');
                    if(current === String(this.total)) current = '';
                    if (current.length < 12) this.amount = parseInt(current + n);
                },
                deleteNumber() {
                    let current = String(this.amount || '');
                    if (current.length > 1) this.amount = parseInt(current.slice(0, -1));
                    else this.amount = '';
                }
            }">
                <div class="mb-4 text-center">
                    <span class="text-muted small fw-bold text-uppercase tracking-widest">Total Tagihan</span>
                    <h2 class="fw-black text-dark mb-0">Rp {{ number_format($paymentTotal, 0, ',', '.') }}</h2>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold small text-muted">Metode Pembayaran</label>
                    <div class="row g-2">
                        <div class="col-4">
                            <label class="btn btn-outline-dark w-100 rounded-3 py-2 fw-bold" :class="method === 'cash' ? 'active' : ''">
                                <input type="radio" value="cash" x-model="method" class="d-none">
                                <i class="bi bi-cash d-block mb-1 fs-4"></i> Cash
                            </label>
                        </div>
                        <div class="col-4">
                            <label class="btn btn-outline-dark w-100 rounded-3 py-2 fw-bold" :class="method === 'qris' ? 'active' : ''">
                                <input type="radio" value="qris" x-model="method" class="d-none">
                                <i class="bi bi-qr-code-scan d-block mb-1 fs-4"></i> QRIS
                            </label>
                        </div>
                        <div class="col-4">
                            <label class="btn btn-outline-dark w-100 rounded-3 py-2 fw-bold" :class="method === 'transfer' ? 'active' : ''">
                                <input type="radio" value="transfer" x-model="method" class="d-none">
                                <i class="bi bi-bank d-block mb-1 fs-4"></i> Transfer
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Numpad & Non-Tunai Display -->
                <template x-if="method === 'cash'">
                    <div>
                        <div class="p-3 border border-dark bg-body mb-3 d-flex justify-content-between align-items-center shadow-sm rounded-3">
                            <span class="fw-bold text-dark">Diterima:</span>
                            <h4 class="fw-bolder text-primary mb-0" x-text="amount ? 'Rp ' + new Intl.NumberFormat('id-ID').format(amount) : 'Rp 0'"></h4>
                        </div>
                        
                        <div class="row g-2 mb-2">
                            <div class="col-4">
                                <button type="button" @click="amount = total" class="btn btn-outline-primary w-100 fw-bold rounded-3">Pas</button>
                            </div>
                            <div class="col-4">
                                <button type="button" @click="amount = 50000" class="btn btn-outline-secondary w-100 fw-bold bg-body rounded-3">50k</button>
                            </div>
                            <div class="col-4">
                                <button type="button" @click="amount = 100000" class="btn btn-outline-secondary w-100 fw-bold bg-body rounded-3">100k</button>
                            </div>
                        </div>
                        
                        <div class="row g-2 mb-3">
                            <template x-for="n in [1, 2, 3, 4, 5, 6, 7, 8, 9]" :key="n">
                                <div class="col-4">
                                    <button type="button" @click="appendNumber(n)" class="btn btn-light border w-100 fs-5 fw-bold py-2 text-dark bg-body shadow-sm rounded-3" x-text="n"></button>
                                </div>
                            </template>
                            <div class="col-4">
                                <button type="button" @click="appendNumber('000')" class="btn btn-light border w-100 fs-5 fw-bold py-2 text-dark bg-body shadow-sm rounded-3">000</button>
                            </div>
                            <div class="col-4">
                                <button type="button" @click="appendNumber('0')" class="btn btn-light border w-100 fs-5 fw-bold py-2 text-dark bg-body shadow-sm rounded-3">0</button>
                            </div>
                            <div class="col-4">
                                <button type="button" @click="deleteNumber()" class="btn btn-light border w-100 fs-5 fw-bold py-2 text-danger bg-body shadow-sm d-flex justify-content-center align-items-center rounded-3" style="height: 100%;"><i class="bi bi-backspace-fill"></i></button>
                            </div>
                        </div>

                        <template x-if="amount && (amount - total) >= 0">
                            <div class="d-flex justify-content-between align-items-center bg-success bg-opacity-10 border border-success border-opacity-25 shadow-sm rounded-3 p-3">
                                <span class="fw-bold text-success small">Kembalian:</span>
                                <h5 class="fw-bolder text-success mb-0" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(amount - total)"></h5>
                            </div>
                        </template>
                    </div>
                </template>
                
                <template x-if="method !== 'cash'">
                    <div class="d-flex flex-column justify-content-center align-items-center h-100 text-center py-4 bg-light rounded-4 shadow-sm border mb-3">
                        <i class="bi bi-check-circle-fill text-success mb-3" style="font-size: 3rem;"></i>
                        <h5 class="fw-bold mb-2">Pembayaran Non-Tunai</h5>
                        <p class="text-muted small mb-0 px-3">Pastikan saldo pelanggan sudah masuk atau bukti transfer telah diterima sebelum klik Konfirmasi.</p>
                    </div>
                </template>
            </div>
            <div class="modal-footer border-top-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" wire:click="processPayment" class="btn btn-dark rounded-pill px-4 fw-bold">
                    Konfirmasi Lunas
                </button>
            </div>
        </div>
    </div>
</div>

@script
<script>
    Livewire.on('show-payment-modal', () => {
        setTimeout(() => {
            const el = document.getElementById('paymentModal');
            if (el) {
                const modal = bootstrap.Modal.getOrCreateInstance(el);
                modal.show();
                 $dispatch('show-bootstrap-modal');
            }
        }, 200);
    });
    
    Livewire.on('hide-payment-modal', () => {
        const el = document.getElementById('paymentModal');
        if (el) {
            const modal = bootstrap.Modal.getInstance(el);
            if (modal) modal.hide();
        }
    });
</script>
@endscript
