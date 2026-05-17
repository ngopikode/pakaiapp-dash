{{-- ===== CART PANEL: RESTO / F&B MODE ===== --}}
{{-- Flow: Buat pesanan (pending) → Bayar nanti dari Antrian --}}

<div class="card d-flex flex-column overflow-hidden shadow-sm" style="border-radius: 1.25rem;">

    {{-- Header --}}
    <div class="p-4 border-bottom d-flex justify-content-between align-items-center bg-body-tertiary">
        <div>
            <h4 class="fw-bold font-serif text-primary mb-0">Pesanan Baru</h4>
            <small class="text-muted fw-bold" style="font-size: 0.7rem;">
                <i class="bi bi-cup-hot-fill me-1"></i>Buat pesanan → bayar nanti
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
                <small>Klik menu di sebelah kiri</small>
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
                        <span class="fw-bold" style="color: var(--brand-caramel);"
                              x-text="'Rp ' + formatRupiah(item.subtotal)"></span>
                    </div>

                    {{-- Catatan per-item (khusus resto) --}}
                    <div class="mt-3">
                        <input type="text" class="form-control form-control-sm bg-body" x-model="item.note"
                               placeholder="Catatan: pedas, no ice, dll..."
                               style="border-radius: 0.5rem; border-style: dashed;">
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- Bottom Section --}}
    <div class="p-4 border-top bg-body-tertiary">

        {{-- Order Type Selector --}}
        <div class="row g-2 mb-3">
            @foreach($orderTypes as $type)
                <div class="col">
                    <button @click="orderType = '{{ $type['id'] }}'; if('{{ $type['id'] }}' !== 'dinein') tableNumber = ''"
                            class="btn w-100 fw-bold py-2 transition-all"
                            :class="orderType === '{{ $type['id'] }}' ? 'btn-primary shadow-sm' : 'btn-outline-secondary bg-body'"
                            style="border-radius: 0.75rem; font-size: 0.85rem;">
                        @if($type['id'] === 'dinein')
                            <i class="bi bi-shop me-1"></i>
                        @elseif($type['id'] === 'takeaway')
                            <i class="bi bi-bag me-1"></i>
                        @elseif($type['id'] === 'delivery')
                            <i class="bi bi-truck me-1"></i>
                        @endif
                        {{ $type['label'] }}
                    </button>
                </div>
            @endforeach
        </div>

        {{-- Inputs --}}
        <div class="row g-2 mb-3">
            <div :class="orderType === 'dinein' ? 'col-6' : 'col-12'">
                <input type="text" class="form-control bg-body border-0 shadow-sm"
                       x-model="customerName" placeholder="Nama Pelanggan"
                       style="border-radius: 0.75rem;">
            </div>
            <div class="col-6" x-show="orderType === 'dinein'" x-transition>
                <input type="text" class="form-control bg-body border-0 shadow-sm"
                       x-model="tableNumber" placeholder="No Meja"
                       style="border-radius: 0.75rem;">
            </div>
        </div>

        <div x-show="stockError" class="text-danger small fw-bold mb-2 text-center" x-text="stockError"></div>

        <div class="row g-2">
            <div class="col-6">
                <button @click="submitNewOrder"
                        class="btn btn-warning btn-lg w-100 fw-bold shadow-sm d-flex justify-content-center align-items-center text-dark"
                        :disabled="cart.length === 0 || stockError !== '' || isSubmitting"
                        style="padding: 1rem; font-size: 0.95rem; border-radius: 1rem;">
                    <i class="bi bi-hourglass-split me-2" x-show="!isSubmitting"></i>
                    <span class="spinner-border spinner-border-sm me-2" x-show="isSubmitting"></span>
                    <span x-text="isSubmitting ? 'Menyimpan...' : 'Antrikan'"></span>
                </button>
            </div>
            <div class="col-6">
                <button @click="openDirectPaymentModal"
                        class="btn btn-primary btn-lg w-100 fw-bold shadow-sm d-flex justify-content-between align-items-center"
                        :disabled="cart.length === 0 || stockError !== '' || isSubmitting"
                        style="padding: 1rem; font-size: 0.95rem; border-radius: 1rem;">
                    <span>Bayar</span>
                    <span x-text="formatRupiah(subTotal)"></span>
                </button>
            </div>
        </div>
    </div>
</div>
