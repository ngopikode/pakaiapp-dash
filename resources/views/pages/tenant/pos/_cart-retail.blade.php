{{-- ===== CART PANEL: RETAIL MODE ===== --}}
{{-- Fitur: Diskon per-item, Stok ketat, Langsung bayar --}}

<div class="card h-100 d-flex flex-column overflow-hidden shadow-sm" style="border-radius: 1.25rem;">

    {{-- Header --}}
    <div class="p-4 border-bottom d-flex justify-content-between align-items-center bg-body-tertiary">
        <div>
            <h4 class="fw-bold font-serif text-primary mb-0">Pesanan</h4>
            <small class="text-muted fw-bold" style="font-size: 0.7rem;">
                <i class="bi bi-shop-window me-1"></i>Mode Retail
            </small>
        </div>
        <button @click="clearCart" class="btn btn-sm btn-outline-danger"
                style="border-radius: 0.5rem; font-weight: 600;" x-show="cart.length > 0">
            <i class="bi bi-trash3 me-1"></i> Kosongkan
        </button>
    </div>

    {{-- Cart Items --}}
    <div class="card-body p-3 overflow-y-auto flex-grow-1 bg-body">
        <template x-if="cart.length === 0">
            <div class="d-flex flex-column justify-content-center align-items-center h-100 text-muted opacity-50">
                <i class="bi bi-bag-x" style="font-size: 4rem; margin-bottom: 1rem;"></i>
                <p class="fw-bold font-serif mb-0">Belum ada pesanan</p>
                <small>Klik produk di sebelah kiri</small>
            </div>
        </template>

        <div class="d-flex flex-column gap-2">
            <template x-for="(item, index) in cart" :key="index">
                <div class="card bg-body-tertiary p-3 border-0" style="border-radius: 1rem;">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="pe-3">
                            <h6 class="fw-bold mb-1 text-dark" x-text="item.name"></h6>
                            <template x-if="item.variant_name">
                                <span class="badge bg-secondary opacity-75 rounded-pill mb-1"
                                      x-text="item.variant_name"></span>
                            </template>
                        </div>
                        <button @click="removeFromCart(index)" class="btn btn-sm text-danger p-1">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <div class="d-flex justify-content-between align-items-end mt-2">
                        <div class="d-flex align-items-center bg-body rounded-pill border" style="padding: 0.15rem;">
                            <button @click="decreaseQty(index)" class="btn btn-sm btn-light rounded-circle p-1"
                                    style="width: 28px; height: 28px;"><i class="bi bi-dash"></i></button>
                            <span class="fw-bold px-3" x-text="item.quantity"></span>
                            <button @click="increaseQty(index)" class="btn btn-sm btn-primary rounded-circle p-1"
                                    style="width: 28px; height: 28px;" :disabled="item.quantity >= item.stock">
                                <i class="bi bi-plus"></i></button>
                        </div>
                        <div class="text-end">
                            {{-- Show original price struck-through if item has discount --}}
                            <template x-if="item.itemDiscount > 0">
                                <small class="text-muted text-decoration-line-through d-block" style="font-size: 0.7rem;"
                                       x-text="'Rp ' + formatRupiah(item.price * item.quantity)"></small>
                            </template>
                            <span class="fw-bold" style="color: var(--brand-caramel);"
                                  x-text="'Rp ' + formatRupiah(item.subtotal)"></span>
                        </div>
                    </div>

                    {{-- Per-item discount + stock info --}}
                    <div class="mt-3 d-flex gap-2 align-items-center">
                        <div class="input-group input-group-sm flex-grow-1" style="border-radius: 0.5rem;">
                            <span class="input-group-text bg-body border-0 text-muted" style="font-size: 0.7rem; border-radius: 0.5rem 0 0 0.5rem;">
                                <i class="bi bi-tag me-1"></i>Diskon
                            </span>
                            <input type="number" class="form-control bg-body border-0 fw-bold" style="border-radius: 0 0.5rem 0.5rem 0;"
                                   x-model.number="item.itemDiscount"
                                   @input="recalcItemSubtotal(index)"
                                   placeholder="0" min="0">
                        </div>
                        <small class="text-muted fw-bold text-nowrap" style="font-size: 0.65rem;">
                            <i class="bi bi-box-seam"></i> <span x-text="item.stock - item.quantity"></span>
                        </small>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- Bottom Section --}}
    <div class="p-4 border-top bg-body-tertiary">

        {{-- Customer Info --}}
        <div class="row g-2 mb-2">
            <div class="col-6">
                <input type="text" class="form-control bg-body border-0 shadow-sm"
                       x-model="customerName" placeholder="Nama Pelanggan"
                       style="border-radius: 0.75rem;">
            </div>
            <div class="col-6">
                <input type="text" class="form-control bg-body border-0 shadow-sm"
                       x-model="customerPhone" placeholder="No WA (0812...)"
                       style="border-radius: 0.75rem;">
            </div>
        </div>

        {{-- Global Discount --}}
        <div class="mb-3">
            <div class="input-group shadow-sm" style="border-radius: 0.75rem;">
                <span class="input-group-text bg-body border-0 text-muted fw-bold"
                      style="border-top-left-radius: 0.75rem; border-bottom-left-radius: 0.75rem;">
                    <i class="bi bi-tag-fill me-1"></i> Diskon Extra (Rp)
                </span>
                <input type="number" class="form-control bg-body border-0 fw-bold" x-model.number="globalDiscount"
                       placeholder="0"
                       style="border-top-right-radius: 0.75rem; border-bottom-right-radius: 0.75rem;">
            </div>
        </div>

        <div x-show="stockError" class="text-danger small fw-bold mb-2 text-center" x-text="stockError"></div>

        {{-- Pay Button --}}
        <button @click="openPaymentModal"
                class="btn btn-primary btn-lg w-100 fw-bold shadow-sm d-flex justify-content-between align-items-center"
                :disabled="cart.length === 0 || stockError !== ''"
                style="padding: 1rem; font-size: 1.1rem; border-radius: 1rem;">
            <span><i class="bi bi-cart-check me-2"></i>Lanjut Bayar</span>
            <span x-text="'Rp ' + formatRupiah(grandTotal)"></span>
        </button>
    </div>
</div>
