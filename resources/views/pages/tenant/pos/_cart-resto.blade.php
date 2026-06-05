<div class="card d-flex flex-column h-100 border shadow-sm bg-body text-body"
     style="border-radius: 1.5rem; border-color: var(--bs-border-color-translucent) !important;">
     
    <template x-if="isEditingOrder">
        <div class="bg-primary bg-opacity-10 text-primary px-3 py-2 border-bottom fw-medium text-center small position-relative" style="border-radius: 1.5rem 1.5rem 0 0; border-color: var(--bs-border-color-translucent) !important;">
            <i class="bi bi-info-circle-fill me-1"></i> Menambah ke <span class="fw-bold" x-text="editInvoiceCode"></span> (<span x-text="customerName || tableNumber"></span>)
            <button type="button" class="btn-close position-absolute end-0 top-50 translate-middle-y me-3" style="font-size: 0.6rem;" @click="window.location.href='/cashier'" title="Batal Edit"></button>
        </div>
    </template>

    {{-- Header (Safe Context Light/Dark) --}}
    <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-body"
         :style="isEditingOrder ? 'border-radius: 0;' : 'border-radius: 1.5rem 1.5rem 0 0;'" style="border-color: var(--bs-border-color-translucent) !important;">
        <div class="d-flex align-items-center gap-2">
            <!-- Tombol Kembali Khusus HP -->
            <button @click="isMobileCartOpen = false"
                    class="btn btn-sm btn-secondary d-lg-none rounded-circle shadow-sm d-flex align-items-center justify-content-center bg-body border"
                    style="width: 36px; height: 36px;">
                <i class="bi bi-arrow-left fs-5 text-body"></i>
            </button>
            <h5 class="fw-bold mb-0 text-truncate" style="max-width: 140px;">
                <i class="bi bi-shop-window text-warning me-1 d-none d-lg-inline-block"></i>Pesanan
            </h5>
        </div>
        <button @click="clearCart" class="btn btn-sm btn-outline-danger d-flex align-items-center justify-content-center rounded-circle bg-body shadow-sm"
                x-show="cart.length > 0" title="Bersihkan (F4)" style="width: 36px; height: 36px;">
            <i class="bi bi-trash3 fs-6"></i>
        </button>
    </div>

    {{-- Cart Items --}}
    <div id="tour-cart-items" class="card-body p-3 overflow-y-auto flex-grow-1 bg-body-tertiary">
        <template x-if="cart.length === 0">
            <div class="d-flex flex-column justify-content-center align-items-center h-100 text-muted opacity-50">
                <i class="bi bi-bag-dash mb-3" style="font-size: 3.5rem;"></i>
                <p class="fw-bold mb-0">Keranjang Kosong</p>
                <small>Pilih menu untuk memulai</small>
            </div>
        </template>

        <div class="d-flex flex-column gap-3">
            <template x-for="(item, index) in cart" :key="index">
                <div class="card p-3 border shadow-sm bg-body"
                     style="border-radius: 1rem; border-color: var(--bs-border-color-translucent) !important;">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="pe-2">
                            <h6 class="fw-bold mb-1 text-body" x-text="item.name"></h6>
                            <template x-if="item.variant_name">
                                <span class="badge bg-body-tertiary text-secondary border rounded-pill mb-1"
                                      style="font-size: 0.7rem; white-space: normal;" x-text="item.variant_name"></span>
                            </template>
                        </div>
                        <button @click="removeFromCart(index)"
                                class="btn btn-sm btn-secondary bg-body text-danger p-0 ratio-1x1 rounded-circle border-1">
                            <i class="bi bi-x fs-5 p-1"></i>
                        </button>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <span class="fw-bold text-primary" x-text="'Rp ' + formatRupiah(item.subtotal)"></span>
                        <div class="d-flex align-items-center bg-body-tertiary rounded-pill border"
                             style="padding: 0.2rem; border-color: var(--bs-border-color) !important;">
                            <button @click="decreaseQty(index)"
                                    class="btn btn-sm btn-secondary bg-body rounded-circle p-1 shadow-sm border"
                                    style="width: 28px; height: 28px; color: var(--bs-body-color);"><i
                                    class="bi bi-dash"></i></button>
                            <span class="fw-bold px-3 small text-body" x-text="item.quantity"></span>
                            <button @click="increaseQty(index)"
                                    class="btn btn-sm btn-primary rounded-circle p-1 shadow-sm border-0"
                                    style="width: 28px; height: 28px;" :disabled="item.quantity >= item.stock"><i
                                    class="bi bi-plus"></i></button>
                        </div>
                    </div>

                    <div class="mt-3">
                        <input type="text"
                               class="form-control form-control-sm bg-body-tertiary text-body border-0"
                               x-model="item.note"
                               placeholder="Catatan (opsional)..."
                               style="border-radius: 0.5rem; border-color: var(--bs-border-color-translucent) !important;">
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- Bottom Section (Action Buttons) --}}
    <div class="p-3 border-top bg-body pb-mobile-nav"
         style="border-radius: 0 0 1.5rem 1.5rem; border-color: var(--bs-border-color-translucent) !important;">
        <style>
            @media (max-width: 767.98px) {
                .pb-mobile-nav {
                    padding-bottom: 4.5rem !important;
                }
            }
        </style>
        {{-- Order Type Selector --}}
        <div class="d-flex gap-2 overflow-x-auto hide-scrollbar mb-3 pb-1">
            @foreach($orderTypes as $type)
                <button @click="orderType = '{{ $type['id'] }}'; if('{{ $type['id'] }}' !== 'dinein') tableNumber = ''"
                        class="btn fw-bold py-2 px-3 flex-shrink-0 transition-all rounded-pill"
                        :class="orderType === '{{ $type['id'] }}' ? 'btn-primary text-white' : 'btn-outline-secondary bg-body-tertiary border text-secondary'"
                        style="font-size: 0.85rem;"
                        :disabled="isEditingOrder">
                    {{ $type['label'] }}
                </button>
            @endforeach
        </div>

        {{-- Inputs --}}
        <div class="row g-2 mb-3">
            <div :class="orderType === 'dinein' ? 'col-7' : 'col-12'">
                <input type="text" class="form-control bg-body-tertiary text-body border" x-model="customerName"
                       placeholder="Nama Pelanggan" :disabled="isEditingOrder"
                       style="border-radius: 0.75rem; border-color: var(--bs-border-color-translucent) !important;">
            </div>
            <div class="col-5" x-show="orderType === 'dinein'">
                <input type="text" class="form-control bg-body-tertiary text-body border" x-model="tableNumber"
                       placeholder="Meja" :disabled="isEditingOrder"
                       style="border-radius: 0.75rem; border-color: var(--bs-border-color-translucent) !important;">
            </div>
        </div>

        <div x-show="stockError" class="text-danger small fw-bold mb-2 text-center" x-text="stockError"></div>

        {{-- Ringkasan Biaya & Pajak --}}
        <div class="card p-3 border mb-3 bg-body-tertiary"
             style="border-radius: 0.75rem; border-color: var(--bs-border-color-translucent) !important;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-secondary small">Subtotal</span>
                <span class="fw-semibold text-body small" x-text="'Rp ' + formatRupiah(subTotal)"></span>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="text-secondary small">Biaya Layanan (<span x-text="serviceChargeRate"></span>%)</span>
                    <div class="form-check form-switch mb-0 min-height-0">
                        <input class="form-check-input" type="checkbox" role="switch" x-model="isServiceActive"
                               style="cursor: pointer;">
                    </div>
                </div>
                <span class="fw-semibold text-body small" x-text="'Rp ' + formatRupiah(serviceChargeAmount)"></span>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="text-secondary small">Pajak PB1 (<span x-text="taxRate"></span>%)</span>
                    <div class="form-check form-switch mb-0 min-height-0">
                        <input class="form-check-input" type="checkbox" role="switch" x-model="isTaxActive"
                               style="cursor: pointer;">
                    </div>
                </div>
                <span class="fw-semibold text-body small" x-text="'Rp ' + formatRupiah(taxAmount)"></span>
            </div>

            <div class="border-top my-2" style="border-color: var(--bs-border-color) !important;"></div>

            <div class="d-flex justify-content-between align-items-center">
                <span class="fw-bold text-body">Total</span>
                <span class="fw-bold text-primary" x-text="'Rp ' + formatRupiah(subTotalWithCharges)"></span>
            </div>
        </div>

        <div class="row g-2">
            <div class="col-12 col-xl-6">
                <button id="tour-resto-save" @click="submitNewOrder"
                        class="btn btn-warning w-100 fw-bold shadow-sm d-flex justify-content-center align-items-center text-dark py-3"
                        :disabled="cart.length === 0 || stockError !== '' || isSubmitting" style="border-radius: 1rem;">
                    <span x-text="isSubmitting ? 'Memproses...' : (isEditingOrder ? 'Simpan Tambahan' : 'Simpan Bill')"></span>
                </button>
            </div>
            <div class="col-12 col-xl-6">
                <button id="tour-resto-pay" @click="openDirectPaymentModal"
                        class="btn w-100 fw-bold shadow-sm d-flex justify-content-between align-items-center py-3 text-white"
                        :disabled="cart.length === 0 || stockError !== '' || isSubmitting"
                        style="border-radius: 1rem; background: #F97316; border: none;">
                    <span>Bayar</span>
                    <span x-text="formatRupiah(subTotalWithCharges)"></span>
                </button>
            </div>
        </div>

        {{-- Keyboard Shortcuts Legend --}}
        <div class="mt-3 text-center border-top pt-2 d-none d-xl-block"
             style="border-color: var(--bs-border-color-translucent) !important;">
            <div class="d-flex justify-content-center flex-wrap gap-2 text-secondary" style="font-size: 0.7rem;">
                <span class="badge bg-body-tertiary border text-secondary px-2 py-1"><kbd
                        class="bg-dark text-white px-1 rounded small" style="font-size: 0.65rem;">F2</kbd> Bayar</span>
                <span class="badge bg-body-tertiary border text-secondary px-2 py-1"><kbd
                        class="bg-dark text-white px-1 rounded small" style="font-size: 0.65rem;">F3</kbd> Simpan Bill</span>
                <span class="badge bg-body-tertiary border text-secondary px-2 py-1"><kbd
                        class="bg-dark text-white px-1 rounded small"
                        style="font-size: 0.65rem;">F4</kbd> Bersihkan</span>
                <span class="badge bg-body-tertiary border text-secondary px-2 py-1"><kbd
                        class="bg-dark text-white px-1 rounded small"
                        style="font-size: 0.65rem;">F8</kbd> Toggle Tab</span>
            </div>
        </div>
    </div>
</div>
