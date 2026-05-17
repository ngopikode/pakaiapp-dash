{{-- ===== CART PANEL: RETAIL MODE ===== --}}
{{-- Fitur: Diskon per-item, Stok ketat, Langsung bayar --}}

<div class="card d-flex flex-column h-100 border-0 shadow-sm" style="border-radius: 1.5rem;">

    {{-- Header --}}
    <div class="p-3 p-lg-4 border-bottom d-flex justify-content-between align-items-center bg-white"
         style="border-radius: 1.5rem 1.5rem 0 0;">
        <div class="d-flex align-items-center gap-2">
            <!-- Tombol Kembali Khusus HP -->
            <button @click="isMobileCartOpen = false"
                    class="btn btn-sm btn-light d-lg-none rounded-circle shadow-sm d-flex align-items-center justify-content-center"
                    style="width: 36px; height: 36px;">
                <i class="bi bi-arrow-left fs-5 text-dark"></i>
            </button>
            <h5 class="fw-bold text-dark mb-0"><i
                    class="bi bi-shop-window text-primary me-2 d-none d-lg-inline-block"></i>Pesanan Baru</h5>
        </div>
        <button @click="clearCart" class="btn btn-sm btn-light text-danger fw-bold rounded-pill px-3"
                x-show="cart.length > 0">
            <i class="bi bi-trash3 d-lg-none"></i> <span class="d-none d-lg-inline">Bersihkan</span>
        </button>
    </div>

    {{-- Cart Items --}}
    <div class="card-body p-3 overflow-y-auto flex-grow-1 bg-light">
        <template x-if="cart.length === 0">
            <div class="d-flex flex-column justify-content-center align-items-center h-100 text-muted opacity-50">
                <i class="bi bi-bag-dash mb-3" style="font-size: 3.5rem;"></i>
                <p class="fw-bold mb-0">Keranjang Kosong</p>
                <small>Pilih produk untuk memulai</small>
            </div>
        </template>

        <div class="d-flex flex-column gap-3">
            <template x-for="(item, index) in cart" :key="index">
                <div class="card bg-white p-3 border-0 shadow-sm" style="border-radius: 1rem;">

                    {{-- Judul & Tombol Hapus --}}
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="pe-2">
                            <h6 class="fw-bold mb-1 text-dark" x-text="item.name"></h6>
                            <template x-if="item.variant_name">
                                <span class="badge bg-light text-secondary border rounded-pill mb-1"
                                      style="font-size: 0.7rem; white-space: normal;" x-text="item.variant_name"></span>
                            </template>
                        </div>
                        <button @click="removeFromCart(index)"
                                class="btn btn-sm btn-white text-danger p-1 shadow-sm rounded-circle"><i
                                class="bi bi-x fs-5"></i></button>
                    </div>

                    {{-- Harga & QTY Control --}}
                    <div class="d-flex justify-content-between align-items-center mt-2 mb-2">
                        <div class="d-flex flex-column">
                            <template x-if="item.itemDiscount > 0">
                                <small class="text-muted text-decoration-line-through" style="font-size: 0.75rem;"
                                       x-text="'Rp ' + formatRupiah(item.price * item.quantity)"></small>
                            </template>
                            <span class="fw-bold text-primary" x-text="'Rp ' + formatRupiah(item.subtotal)"></span>
                        </div>
                        <div class="d-flex align-items-center bg-light rounded-pill border" style="padding: 0.2rem;">
                            <button @click="decreaseQty(index)"
                                    class="btn btn-sm btn-white rounded-circle p-1 shadow-sm"
                                    style="width: 28px; height: 28px;"><i class="bi bi-dash"></i></button>
                            <span class="fw-bold px-3 small" x-text="item.quantity"></span>
                            <button @click="increaseQty(index)"
                                    class="btn btn-sm btn-primary rounded-circle p-1 shadow-sm"
                                    style="width: 28px; height: 28px;" :disabled="item.quantity >= item.stock"><i
                                    class="bi bi-plus"></i></button>
                        </div>
                    </div>

                    {{-- Per-item discount + stock info --}}
                    <div class="pt-2 border-top d-flex gap-2 align-items-center mt-2 border-dashed">
                        <div class="input-group input-group-sm flex-grow-1 shadow-sm" style="border-radius: 0.5rem;">
                            <span class="input-group-text bg-light border-0 text-muted"
                                  style="font-size: 0.75rem; border-radius: 0.5rem 0 0 0.5rem;">
                                <i class="bi bi-tag me-1"></i>Diskon
                            </span>
                            <input type="number" class="form-control bg-light border-0 fw-bold text-dark"
                                   style="border-radius: 0 0.5rem 0.5rem 0;"
                                   x-model.number="item.itemDiscount"
                                   @input="recalcItemSubtotal(index)"
                                   placeholder="0" min="0">
                        </div>
                        <small class="text-muted fw-bold text-nowrap badge bg-light border" style="font-size: 0.7rem;">
                            Sisa Stok: <span x-text="item.stock - item.quantity"></span>
                        </small>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- Bottom Section (Action Buttons) --}}
    <div class="p-3 border-top bg-white" style="border-radius: 0 0 1.5rem 1.5rem;">

        {{-- Customer Info --}}
        <div class="row g-2 mb-3">
            <div class="col-6">
                <input type="text" class="form-control bg-light border-0 px-3 py-2" x-model="customerName"
                       placeholder="Nama Pelanggan" style="border-radius: 0.75rem;">
            </div>
            <div class="col-6">
                <input type="text" class="form-control bg-light border-0 px-3 py-2" x-model="customerPhone"
                       placeholder="No WA Pelanggan" style="border-radius: 0.75rem;">
            </div>
        </div>

        {{-- Global Discount --}}
        <div class="mb-3">
            <div class="input-group shadow-sm" style="border-radius: 0.75rem;">
                <span class="input-group-text bg-light border-0 text-muted fw-bold"
                      style="border-top-left-radius: 0.75rem; border-bottom-left-radius: 0.75rem; font-size: 0.85rem;">
                    <i class="bi bi-tag-fill text-warning me-1"></i> Diskon Ekstra (Rp)
                </span>
                <input type="number" class="form-control bg-light border-0 fw-bold text-end pe-3"
                       x-model.number="globalDiscount" placeholder="0"
                       style="border-top-right-radius: 0.75rem; border-bottom-right-radius: 0.75rem;">
            </div>
        </div>

        <div x-show="stockError" class="text-danger small fw-bold mb-2 text-center" x-text="stockError"></div>

        <button @click="openPaymentModal"
                class="btn w-100 fw-bold shadow-sm d-flex justify-content-between align-items-center py-3 text-white"
                :disabled="cart.length === 0 || stockError !== ''"
                style="border-radius: 1rem; background: linear-gradient(135deg, #ca8a04, #b45309); border: none;">
            <span><i class="bi bi-cart-check me-2"></i> Lanjut Bayar</span>
            <span x-text="'Rp ' + formatRupiah(grandTotal)"></span>
        </button>
    </div>
</div>
