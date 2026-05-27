<div class="modal fade modal-bottom-mobile" id="paymentModal" tabindex="-1" aria-hidden="true" wire:ignore.self
     data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg d-flex flex-column bg-body text-body"
             style="border-radius: 1.5rem; max-height: 95vh; border-color: var(--bs-border-color-translucent) !important;"
             x-data="{
                 paymentMethod: $wire.entangle('paymentMethod'),
                 amountPaid: $wire.entangle('paymentAmount'),
                 payTotal: $wire.entangle('paymentTotal'),
                 duitkuMethod: $wire.entangle('duitkuMethod'),
                 duitkuCustomerEmail: $wire.entangle('duitkuCustomerEmail'),
                 duitkuPaymentMethods: $wire.entangle('duitkuPaymentMethods'),
                 isSubmitting: false,

                 formatRupiah(val) {
                     return new Intl.NumberFormat('id-ID').format(val || 0);
                 },
                 appendNumber(n) {
                     let current = String(this.amountPaid || '');
                     if (current === String(this.payTotal)) current = '';
                     if (current.length < 12) this.amountPaid = parseInt(current + n) || '';
                 },
                 deleteNumber() {
                     let current = String(this.amountPaid || '');
                     if (current.length > 1) this.amountPaid = parseInt(current.slice(0, -1)) || '';
                     else this.amountPaid = '';
                 },
                 get getChange() {
                     return Math.max(0, (parseFloat(this.amountPaid) || 0) - this.payTotal);
                 },
                 async submitPayment() {
                     if (this.paymentMethod === 'cash' && (this.amountPaid < this.payTotal || !this.amountPaid)) {
                         window.showIslandToast('Uang yang diterima tidak cukup!', 'warning');
                         return;
                     }
                      if (this.paymentMethod === 'duitku') {
                          if (!this.duitkuMethod) {
                              window.showIslandToast('Pilih metode Duitku dulu!', 'warning');
                              return;
                          }
                          // Email opsional — validasi format saja kalau diisi
                          const email = this.duitkuCustomerEmail.trim();
                          if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                              window.showIslandToast('Format email tidak valid!', 'warning');
                              return;
                          }
                      }
                     this.isSubmitting = true;
                     await $wire.processPayment();
                     this.isSubmitting = false;
                 }
             }">

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
                    <div class="col-md-5 border-end pe-md-4"
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
                            <!-- Option 2: QRIS (statis/manual) -->
                            <div class="col-4 col-md-12">
                                <label
                                    class="btn fw-bold w-100 h-100 p-2 p-md-3 rounded-4 transition-all d-flex flex-column flex-md-row align-items-center justify-content-center justify-content-md-start gap-1 gap-md-2 border text-body"
                                    :class="paymentMethod === 'qris' ? 'btn-primary shadow-sm text-white' : 'bg-body-tertiary'">
                                    <input type="radio" x-model="paymentMethod" value="qris" class="d-none">
                                    <i class="bi bi-qr-code-scan fs-5 fs-md-6"></i> <span style="font-size: 0.8rem;">QRIS</span>
                                </label>
                            </div>
                            <!-- Option 3: Transfer manual -->
                            <div class="col-4 col-md-12">
                                <label
                                    class="btn fw-bold w-100 h-100 p-2 p-md-3 rounded-4 transition-all d-flex flex-column flex-md-row align-items-center justify-content-center justify-content-md-start gap-1 gap-md-2 border text-body"
                                    :class="paymentMethod === 'transfer' ? 'btn-primary shadow-sm text-white' : 'bg-body-tertiary'">
                                    <input type="radio" x-model="paymentMethod" value="transfer" class="d-none">
                                    <i class="bi bi-bank fs-5 fs-md-6"></i> <span
                                        style="font-size: 0.8rem;">Transfer</span>
                                </label>
                            </div>
                            <!-- Option 4: Duitku Digital Payment -->
                            @if(config('duitku.enabled'))
                            <div class="col-12">
                                <label
                                    class="btn fw-bold w-100 p-2 p-md-3 rounded-4 transition-all d-flex flex-row align-items-center justify-content-start gap-2 border text-body"
                                    :class="paymentMethod === 'duitku' ? 'btn-warning shadow-sm text-dark' : 'bg-body-tertiary'">
                                    <input type="radio" x-model="paymentMethod" value="duitku" class="d-none">
                                    <i class="bi bi-lightning-charge-fill fs-6"></i>
                                    <span style="font-size: 0.8rem;">Duitku
                                        <span class="badge bg-warning text-dark ms-1 border border-dark border-opacity-10" style="font-size:0.6rem;">DIGITAL</span>
                                    </span>
                                </label>
                            </div>
                            @endif
                        </div>

                    </div>

                    <!-- Kolom Aksi / Numpad -->
                    <div class="col-md-7">

                        <!-- Tampilan Duitku -->
                        <template x-if="paymentMethod === 'duitku'">
                            <div class="d-flex flex-column h-100 py-2">
                                <style>
                                    .border-translucent {
                                        border-color: rgba(0, 0, 0, 0.08) !important;
                                    }
                                    .scale-active {
                                        transform: scale(0.98);
                                        border-width: 2px !important;
                                        box-shadow: 0 4px 12px rgba(202, 138, 4, 0.15) !important;
                                    }
                                    .transition-all {
                                        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
                                    }
                                </style>

                                <!-- Banner Info Duitku -->
                                <div class="bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded-4 p-3 mb-3 text-center text-sm-start d-flex flex-column flex-sm-row align-items-center gap-3">
                                    <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center p-2.5 flex-shrink-0" style="width: 48px; height: 48px;">
                                        <i class="bi bi-lightning-charge-fill fs-4 animate-pulse"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.9rem;">Generate Link Pembayaran</h6>
                                        <p class="text-secondary mb-0 small" style="font-size: 0.75rem; line-height: 1.4;">
                                            Generate link pembayaran, lalu kirimkan ke customer. Status akan update otomatis setelah customer bayar.
                                        </p>
                                    </div>
                                </div>

                                @if(config('duitku.sandbox'))
                                <div class="alert alert-warning border-0 rounded-4 mb-3 p-3 d-flex gap-2.5 shadow-none align-items-start"
                                     style="background-color: #fffbeb; border-left: 4px solid #f59e0b !important; border-radius: 12px !important;">
                                    <i class="bi bi-exclamation-triangle-fill text-warning fs-5 flex-shrink-0" style="margin-top: 1px;"></i>
                                    <div>
                                        <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.8rem; color: #78350f;">Mode Uji Coba (Sandbox)</h6>
                                        <p class="mb-0 text-muted" style="font-size: 0.7rem; line-height: 1.4; color: #92400e !important;">
                                            Website ini sedang dalam tahap uji coba pembayaran. Jangan gunakan kartu kredit atau rekening asli.
                                        </p>
                                    </div>
                                </div>
                                @endif

                                <!-- Bagian 1: Pilih Metode Pembayaran (Grid Premium) -->
                                <div class="mb-3 flex-grow-1">
                                    <label class="form-label small fw-bold text-muted mb-2 text-uppercase tracking-wider" style="font-size: 0.65rem;">
                                        1. Pilih Saluran Pembayaran Duitku <span class="text-danger">*</span>
                                    </label>
                                    
                                    <!-- Dynamic Grid: 2 columns on mobile, 3 columns on desktop -->
                                    <div class="row row-cols-2 row-cols-sm-3 g-2 overflow-y-auto" style="max-height: 240px; padding: 2px;">
                                        <template x-for="method in duitkuPaymentMethods" :key="method.paymentMethod">
                                            <div class="col">
                                                <button
                                                    @click="duitkuMethod = method.paymentMethod"
                                                    type="button"
                                                    class="btn w-100 h-100 p-2.5 rounded-3 border d-flex flex-column align-items-center justify-content-center gap-1.5 transition-all text-center"
                                                    :class="duitkuMethod === method.paymentMethod 
                                                        ? 'bg-warning bg-opacity-10 border-warning text-dark shadow-sm fw-bold scale-active' 
                                                        : 'bg-body-tertiary text-body border-translucent hover:bg-body'"
                                                    style="min-height: 72px;"
                                                >
                                                    <!-- Image Container -->
                                                    <div class="bg-white rounded p-1 d-flex align-items-center justify-content-center border" style="width: 48px; height: 26px;">
                                                        <img :src="method.paymentImage" class="img-fluid object-contain" :alt="method.paymentName" onerror="this.src='https://images.duitku.com/hotlink-ok/QRIS.PNG'">
                                                    </div>
                                                    <!-- Method Name -->
                                                    <span x-text="method.paymentName" style="font-size: 0.68rem; line-height: 1.2;" class="text-truncate w-100"></span>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- Bagian 2: Form Email Pelanggan -->
                                <div class="mt-3">
                                    <label class="form-label small fw-bold text-muted mb-1.5 text-uppercase tracking-wider" style="font-size: 0.65rem;">
                                        2. Kirim Tagihan ke Email Customer <span class="text-muted">(opsional)</span>
                                    </label>
                                    <div class="p-3 bg-light rounded-4 border">
                                        <div class="input-group">
                                            <span class="input-group-text bg-body-tertiary border border-end-0 text-muted"><i class="bi bi-envelope-fill"></i></span>
                                            <input type="email"
                                                   class="form-control bg-body border border-start-0 fw-bold py-2 text-dark"
                                                   placeholder="contoh: customer@email.com"
                                                   x-model="duitkuCustomerEmail" 
                                                   style="font-size: 0.85rem;" />
                                        </div>
                                        <div class="d-flex align-items-center gap-1.5 text-muted mt-2" style="font-size: 0.68rem;">
                                            <i class="bi bi-info-circle-fill text-warning"></i>
                                            <span>Kosongkan jika tidak ada — sistem akan pakai email toko secara otomatis.</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Tampilan QRIS / Transfer manual -->
                        <template x-if="paymentMethod !== 'cash' && paymentMethod !== 'duitku'">
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
                            class="btn btn-secondary border fw-bold flex-shrink-0 rounded-pill shadow-none bg-body text-body"
                            data-bs-dismiss="modal">Batal
                    </button>
                    <button @click="submitPayment"
                            class="btn btn-primary fw-bold flex-grow-1 rounded-pill shadow-sm d-flex align-items-center justify-content-center gap-2 text-white border-0"
                            style="background-color: #F97316;"
                            :disabled="isSubmitting
                                || (paymentMethod === 'cash' && (!amountPaid || getChange < 0))
                                || (paymentMethod === 'duitku' && !duitkuMethod)"
                    >
                        <span x-show="!isSubmitting" class="d-flex align-items-center gap-2">
                            <span x-show="paymentMethod !== 'duitku'">Selesaikan Transaksi</span>
                            <span x-show="paymentMethod === 'duitku'">⚡ Generate Link Bayar</span>
                        </span>
                        <span x-show="isSubmitting" style="display: none;">Memproses...</span>
                    </button>
                </div>
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

    Livewire.on('open-duitku-link', (data) => {
        const url = data.url || data[0]?.url;
        if (url) {
            window.open(url, '_blank');
        }
    });
</script>
@endscript
