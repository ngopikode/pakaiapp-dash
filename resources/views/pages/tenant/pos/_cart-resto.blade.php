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
            <h5 class="fw-bold text-dark mb-0"><i class="bi bi-cart3 text-warning me-2 d-none d-lg-inline-block"></i>Pesanan
                Baru</h5>
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
                <small>Pilih menu untuk memulai</small>
            </div>
        </template>

        <div class="d-flex flex-column gap-3">
            <template x-for="(item, index) in cart" :key="index">
                <div class="card bg-white p-3 border-0 shadow-sm" style="border-radius: 1rem;">
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

                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <span class="fw-bold text-primary" x-text="'Rp ' + formatRupiah(item.subtotal)"></span>
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

                    <div class="mt-3">
                        <input type="text" class="form-control form-control-sm bg-light border-0" x-model="item.note"
                               placeholder="Catatan (opsional)..." style="border-radius: 0.5rem;">
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- Bottom Section (Action Buttons) --}}
    <div class="p-3 border-top bg-white" style="border-radius: 0 0 1.5rem 1.5rem;">
        {{-- Order Type Selector --}}
        <div class="d-flex gap-2 overflow-x-auto hide-scrollbar mb-3 pb-1">
            @foreach($orderTypes as $type)
                <button @click="orderType = '{{ $type['id'] }}'; if('{{ $type['id'] }}' !== 'dinein') tableNumber = ''"
                        class="btn fw-bold py-2 px-3 flex-shrink-0 transition-all rounded-pill"
                        :class="orderType === '{{ $type['id'] }}' ? 'btn-primary' : 'btn-light border'"
                        style="font-size: 0.85rem;">
                    {{ $type['label'] }}
                </button>
            @endforeach
        </div>

        {{-- Inputs --}}
        <div class="row g-2 mb-3">
            <div :class="orderType === 'dinein' ? 'col-7' : 'col-12'">
                <input type="text" class="form-control bg-light border-0 px-3 py-2" x-model="customerName"
                       placeholder="Nama Pelanggan" style="border-radius: 0.75rem;">
            </div>
            <div class="col-5" x-show="orderType === 'dinein'">
                <input type="text" class="form-control bg-light border-0 px-3 py-2" x-model="tableNumber"
                       placeholder="Meja" style="border-radius: 0.75rem;">
            </div>
        </div>

        <div x-show="stockError" class="text-danger small fw-bold mb-2 text-center" x-text="stockError"></div>

        <div class="row g-2">
            <div class="col-12 col-xl-6">
                <button @click="submitNewOrder"
                        class="btn btn-warning w-100 fw-bold shadow-sm d-flex justify-content-center align-items-center text-dark py-3"
                        :disabled="cart.length === 0 || stockError !== '' || isSubmitting" style="border-radius: 1rem;">
                    <span x-text="isSubmitting ? 'Memproses...' : 'Simpan Antrian'"></span>
                </button>
            </div>
            <div class="col-12 col-xl-6">
                <button @click="openDirectPaymentModal"
                        class="btn w-100 fw-bold shadow-sm d-flex justify-content-between align-items-center py-3 text-white"
                        :disabled="cart.length === 0 || stockError !== '' || isSubmitting"
                        style="border-radius: 1rem; background: linear-gradient(135deg, #ca8a04, #b45309); border: none;">
                    <span>Bayar</span>
                    <span x-text="formatRupiah(subTotal)"></span>
                </button>
            </div>
        </div>
    </div>
</div>
