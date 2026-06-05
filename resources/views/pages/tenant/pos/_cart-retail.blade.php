{{-- ===== CART PANEL: RETAIL MODE ===== --}}
{{-- Fitur: Diskon per-item, Stok ketat, Langsung bayar --}}

<div class="card d-flex flex-column h-100 border shadow-sm bg-body text-body"
     style="border-radius: 1.5rem; border-color: var(--bs-border-color-translucent) !important;">

    {{-- Header --}}
    <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-body"
         style="border-radius: 1.5rem 1.5rem 0 0; border-color: var(--bs-border-color-translucent) !important;">
        <div class="d-flex align-items-center gap-2">
            <!-- Tombol Kembali Khusus HP -->
            <button @click="isMobileCartOpen = false"
                    class="btn btn-sm btn-secondary d-lg-none rounded-circle shadow-sm d-flex align-items-center justify-content-center bg-body border"
                    style="width: 36px; height: 36px;">
                <i class="bi bi-arrow-left fs-5 text-body"></i>
            </button>
            <h5 class="fw-bold mb-0 text-truncate" style="max-width: 140px;">
                <i class="bi bi-cart3 text-primary me-1 d-none d-lg-inline-block"></i>Pesanan
            </h5>
        </div>
        <div id="tour-cart-actions" class="d-flex gap-2">
            <button @click="holdOrder" class="btn btn-sm btn-outline-warning d-flex align-items-center justify-content-center rounded-circle bg-body shadow-sm"
                    x-show="cart.length > 0" title="Simpan Sementara (F8)" style="width: 36px; height: 36px;">
                <i class="bi bi-pause-circle fs-6"></i>
            </button>
            <button @click="openHeldOrdersModal" class="btn btn-sm btn-outline-info d-flex align-items-center justify-content-center rounded-circle bg-body position-relative shadow-sm" 
                    title="Daftar Tunda" style="width: 36px; height: 36px;">
                <i class="bi bi-card-list fs-6"></i>
                <span x-show="heldOrders.length > 0" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-white" style="font-size: 0.55rem; padding: 0.35em 0.5em;" x-text="heldOrders.length"></span>
            </button>
            <button @click="clearCart" class="btn btn-sm btn-outline-danger d-flex align-items-center justify-content-center rounded-circle bg-body shadow-sm"
                    x-show="cart.length > 0" title="Bersihkan (F4)" style="width: 36px; height: 36px;">
                <i class="bi bi-trash3 fs-6"></i>
            </button>
        </div>
    </div>

    {{-- Cart Items --}}
    <div id="tour-cart-items" class="card-body p-3 overflow-y-auto flex-grow-1 bg-body-tertiary">
        <template x-if="cart.length === 0">
            <div class="d-flex flex-column justify-content-center align-items-center h-100 text-muted opacity-50">
                <i class="bi bi-bag-dash mb-3" style="font-size: 3.5rem;"></i>
                <p class="fw-bold mb-0">Keranjang Kosong</p>
                <small>Pilih produk untuk memulai</small>
            </div>
        </template>

        <div class="d-flex flex-column gap-3">
            <template x-for="(item, index) in cart" :key="index">
                <div class="card p-3 border shadow-sm bg-body"
                     style="border-radius: 1rem; border-color: var(--bs-border-color-translucent) !important;">

                    {{-- Judul & Tombol Hapus --}}
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="pe-2">
                            <h6 class="fw-bold mb-1 text-body" x-text="item.name"></h6>
                            <template x-if="item.sku">
                                <div class="text-secondary mb-1" style="font-size: 0.7rem;">
                                    <i class="bi bi-upc-scan me-1"></i><span x-text="item.sku"></span>
                                </div>
                            </template>
                            <template x-if="item.variant_name">
                                <span class="badge bg-body-tertiary text-secondary border rounded-pill mb-1"
                                      style="font-size: 0.7rem; white-space: normal;" x-text="item.variant_name"></span>
                            </template>
                        </div>
                        <button @click="removeFromCart(index)"
                                class="btn btn-sm btn-secondary bg-body text-danger p-1 shadow-sm rounded-circle border">
                            <i
                                class="bi bi-x fs-5"></i></button>
                    </div>

                    {{-- Harga & QTY Control --}}
                    <div class="d-flex justify-content-between align-items-center mt-2 mb-2">
                        <div class="d-flex flex-column">
                            <template x-if="item.itemDiscount > 0">
                                <small class="text-secondary opacity-50 text-decoration-line-through"
                                       style="font-size: 0.75rem;"
                                       x-text="'Rp ' + formatRupiah(item.price * item.quantity)"></small>
                            </template>
                            <span class="fw-bold text-primary" x-text="'Rp ' + formatRupiah(item.subtotal)"></span>
                        </div>
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

                    {{-- Per-item discount + stock info --}}
                    <div class="pt-2 border-top d-flex gap-2 align-items-center mt-2 border-dashed"
                         style="border-color: var(--bs-border-color-translucent) !important;">
                        <div class="input-group input-group-sm flex-grow-1 shadow-sm" style="border-radius: 0.5rem;">
                            <span class="input-group-text bg-body-tertiary border text-secondary"
                                  style="font-size: 0.75rem; border-radius: 0.5rem 0 0 0.5rem; border-color: var(--bs-border-color-translucent) !important;">
                                <i class="bi bi-tag me-1"></i>Diskon
                            </span>
                            <input type="number" class="form-control bg-body-tertiary border text-body fw-bold"
                                   style="border-radius: 0 0.5rem 0.5rem 0; border-color: var(--bs-border-color-translucent) !important;"
                                   x-model.number="item.itemDiscount"
                                   @input="recalcItemSubtotal(index)"
                                   placeholder="0" min="0">
                        </div>
                        <small class="text-secondary fw-bold text-nowrap badge bg-body border"
                               style="font-size: 0.7rem; border-color: var(--bs-border-color-translucent) !important;">
                            Sisa Stok: <span class="text-body" x-text="item.stock - item.quantity"></span>
                        </small>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- Bottom Section (Action Buttons) --}}
    <div class="p-3 border-top bg-body"
         style="border-radius: 0 0 1.5rem 1.5rem; border-color: var(--bs-border-color-translucent) !important;">

        {{-- Customer Info --}}
        <div class="row g-2 mb-3">
            <div class="col-6">
                <div class="position-relative">
                    <i class="bi bi-person position-absolute text-secondary" style="left: 12px; top: 50%; transform: translateY(-50%); font-size: 1.1rem;"></i>
                    <input type="text" class="form-control bg-body-tertiary text-body border" x-model="customerName"
                           placeholder="Nama Pelanggan"
                           style="border-radius: 0.75rem; border-color: var(--bs-border-color-translucent) !important; padding-left: 2.25rem;">
                </div>
            </div>
            <div class="col-6">
                <div class="position-relative">
                    <i class="bi bi-whatsapp position-absolute text-success" style="left: 12px; top: 50%; transform: translateY(-50%); font-size: 1.1rem;"></i>
                    <input type="text" class="form-control bg-body-tertiary text-body border" x-model="customerPhone"
                           placeholder="No WA Pelanggan"
                           style="border-radius: 0.75rem; border-color: var(--bs-border-color-translucent) !important; padding-left: 2.25rem;">
                </div>
            </div>
        </div>

        {{-- Global Discount --}}
        <div class="mb-3">
            <div class="input-group shadow-sm" style="border-radius: 0.75rem;">
                <span class="input-group-text bg-body-tertiary border text-secondary fw-bold"
                      style="border-top-left-radius: 0.75rem; border-bottom-left-radius: 0.75rem; font-size: 0.85rem; border-color: var(--bs-border-color-translucent) !important;">
                    <i class="bi bi-tag-fill text-warning me-1"></i> Diskon Ekstra (Rp)
                </span>
                <input type="number" class="form-control bg-body-tertiary border text-body fw-bold text-end pe-3"
                       x-model.number="globalDiscount" placeholder="0"
                       style="border-top-right-radius: 0.75rem; border-bottom-right-radius: 0.75rem; border-color: var(--bs-border-color-translucent) !important;">
            </div>
        </div>

        <div x-show="stockError" class="text-danger small fw-bold mb-2 text-center" x-text="stockError"></div>

        <button id="tour-retail-pay" @click="openPaymentModal"
                class="btn w-100 fw-bold shadow-sm d-flex justify-content-between align-items-center py-3 text-white border-0"
                :disabled="cart.length === 0 || stockError !== ''"
                style="border-radius: 1rem; background: #F97316;">
            <span><i class="bi bi-cart-check me-2"></i> Lanjut Bayar</span>
            <span x-text="'Rp ' + formatRupiah(grandTotal)"></span>
        </button>

        {{-- Keyboard Shortcuts Legend --}}
        <div class="mt-3 text-center border-top pt-2 d-none d-xl-block" style="border-color: var(--bs-border-color-translucent) !important;">
            <div class="d-flex justify-content-center flex-wrap gap-2 text-secondary" style="font-size: 0.7rem;">
                <span class="badge bg-body-tertiary border text-secondary px-2 py-1"><kbd class="bg-dark text-white px-1 rounded small" style="font-size: 0.65rem;">F2</kbd> Bayar</span>
                <span class="badge bg-body-tertiary border text-secondary px-2 py-1"><kbd class="bg-dark text-white px-1 rounded small" style="font-size: 0.65rem;">F4</kbd> Batal</span>
                <span class="badge bg-body-tertiary border text-secondary px-2 py-1"><kbd class="bg-dark text-white px-1 rounded small" style="font-size: 0.65rem;">F8</kbd> Tunda / Daftar</span>
            </div>
        </div>
    </div>
</div>
