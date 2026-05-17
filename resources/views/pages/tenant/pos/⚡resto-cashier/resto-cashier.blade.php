<div class="pos-container d-flex flex-column h-100" x-data="restoPos()" @add-product.window="handleProductClick($event.detail.product)" x-cloak>

    {{-- Tab Navigation (Backend-driven) --}}
    <div class="d-flex gap-2 mb-3 flex-shrink-0">
        <button wire:click="changeTab('cashier')"
                class="btn fw-bold px-4 py-2 d-flex align-items-center gap-2 transition-all"
                :class="$wire.activeTab === 'cashier' ? 'btn-primary shadow-sm' : 'btn-outline-secondary bg-body'"
                style="border-radius: 1rem;">
            <i class="bi bi-plus-circle"></i> Kasir Baru
        </button>
        <button wire:click="changeTab('queue')"
                class="btn fw-bold px-4 py-2 d-flex align-items-center gap-2 transition-all position-relative"
                :class="$wire.activeTab === 'queue' ? 'btn-warning shadow-sm text-dark' : 'btn-outline-secondary bg-body'"
                style="border-radius: 1rem;">
            <i class="bi bi-hourglass-split"></i> Antrian
            @if($pendingOrders->count() > 0)
                <span class="badge bg-danger rounded-pill">{{ $pendingOrders->count() }}</span>
            @endif
        </button>
    </div>

    {{-- ===== TAB 1: KASIR BARU ===== --}}
    <div x-show="$wire.activeTab === 'cashier'" class="row g-4 flex-grow-1" style="min-height: 0;" x-transition.opacity.duration.150ms>
        <div class="col-lg-7 col-xl-8 d-flex flex-column h-100">
            <livewire:tenant.pos.product-list/>
        </div>
        <div class="col-lg-5 col-xl-4 h-100">
            @include('pages.tenant.pos._cart-resto', ['orderTypes' => $restoOrderTypes])
        </div>
    </div>

    {{-- ===== TAB 2: ANTRIAN (Pesanan Pending) ===== --}}
    <div x-show="$wire.activeTab === 'queue'" class="flex-grow-1 overflow-y-auto" style="min-height: 0;" x-transition.opacity.duration.150ms>
        @if($pendingOrders->isEmpty())
            <div class="card border-0 shadow-sm p-5 text-center" style="border-radius: 1.25rem;">
                <i class="bi bi-check-circle text-success" style="font-size: 4rem; opacity: 0.3;"></i>
                <h5 class="fw-bold font-serif text-muted mt-3">Tidak ada antrian</h5>
                <p class="text-muted small">Semua pesanan sudah dibayar 🎉</p>
            </div>
        @else
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3">
                @foreach($pendingOrders as $order)
                    <div class="col">
                        <div class="card border-0 shadow-sm h-100 overflow-hidden" style="border-radius: 1.25rem;">
                            {{-- Order Header --}}
                            <div class="p-3 bg-warning bg-opacity-10 border-bottom d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark">{{ $order->invoice_code }}</h6>
                                    <small class="text-muted fw-bold" style="font-size: 0.7rem;">
                                        {{ $order->created_at->diffForHumans() }}
                                    </small>
                                </div>
                                <span class="badge bg-warning text-dark rounded-pill fw-bold px-3 py-2">
                                    <i class="bi bi-hourglass-split me-1"></i>Pending
                                </span>
                            </div>

                            {{-- Order Info --}}
                            <div class="card-body p-3">
                                <div class="d-flex gap-2 mb-3 flex-wrap">
                                    <span class="badge bg-body-tertiary text-dark border rounded-pill">
                                        <i class="bi bi-person me-1"></i>{{ $order->customer_name }}
                                    </span>
                                    @if($order->table_number)
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill">
                                            <i class="bi bi-hash"></i>Meja {{ $order->table_number }}
                                        </span>
                                    @endif
                                    <span class="badge bg-body-tertiary text-muted border rounded-pill text-capitalize">
                                        {{ $order->order_type }}
                                    </span>
                                </div>

                                {{-- Items --}}
                                <div class="mb-3">
                                    @foreach($order->items as $item)
                                        <div class="d-flex justify-content-between align-items-center py-1 border-bottom border-dashed"
                                             style="font-size: 0.85rem;">
                                            <span class="text-dark">
                                                <span class="fw-bold text-primary">{{ $item->quantity }}x</span>
                                                {{ $item->product_name }}
                                                @if($item->variant_name)
                                                    <small class="text-muted">({{ $item->variant_name }})</small>
                                                @endif
                                                @if($item->note)
                                                    <br><small class="text-muted fst-italic"><i class="bi bi-chat-dots me-1"></i>{{ $item->note }}</small>
                                                @endif
                                            </span>
                                            <span class="fw-bold text-nowrap" style="color: var(--brand-caramel);">
                                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Total --}}
                                <div class="d-flex justify-content-between align-items-center p-2 bg-body-tertiary rounded-3">
                                    <span class="fw-bold text-muted small">TOTAL</span>
                                    <h5 class="fw-bolder mb-0" style="color: var(--brand-caramel);">
                                        Rp {{ number_format($order->subtotal, 0, ',', '.') }}
                                    </h5>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="p-3 border-top bg-body-tertiary d-flex gap-2">
                                <button @click="$dispatch('open-cancel-modal', { orderId: {{ $order->id }} })"
                                        class="btn btn-outline-danger fw-bold flex-shrink-0"
                                        style="border-radius: 0.75rem;">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                                <button @click="openPayForOrder({{ json_encode([
                                            'id' => $order->id,
                                            'invoice_code' => $order->invoice_code,
                                            'customer_name' => $order->customer_name,
                                            'subtotal' => $order->subtotal,
                                        ]) }})"
                                        class="btn btn-primary fw-bold flex-grow-1 d-flex align-items-center justify-content-center gap-2"
                                        style="border-radius: 0.75rem;">
                                    <i class="bi bi-cash-coin"></i> Bayar Sekarang
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Shared Modals --}}
    @include('pages.tenant.pos._modal-payment')
    @include('pages.tenant.pos._modal-variant')
    @include('pages.tenant.pos._modal-success')

    {{-- ===== MULTI-SELECT OPTION MODAL (selection_type = multiple) ===== --}}
    <div class="modal fade" id="optionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 1.25rem;">
                <div class="modal-header border-bottom pb-3 pt-4 px-4 bg-body-tertiary"
                     style="border-top-left-radius: 1.25rem; border-top-right-radius: 1.25rem;">
                    <div>
                        <h4 class="fw-bold font-serif text-primary mb-1">Pilih Varian</h4>
                        <p class="text-muted small mb-0" x-text="optionProduct ? optionProduct.name : ''"></p>
                        <template x-if="optionProduct && optionProduct.selection_type === 'multiple'">
                            <small class="text-primary fw-bold" x-text="'Pilih maks ' + optionProduct.max_selections + ' pilihan'"></small>
                        </template>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-body" style="border-bottom-left-radius: 1.25rem; border-bottom-right-radius: 1.25rem;">
                    <div class="d-flex flex-column gap-2">
                        <template x-if="optionProduct">
                            <template x-for="variant in optionProduct.variants" :key="variant.id">
                                <button type="button"
                                        class="card flex-row justify-content-between align-items-center p-3 text-start w-100 border transition-all"
                                        :class="{
                                            'opacity-50 bg-body-tertiary': variant.stock <= 0,
                                            'border-primary shadow-sm bg-primary bg-opacity-10': isOptionSelected(variant.name),
                                            'border-secondary': variant.stock > 0 && !isOptionSelected(variant.name)
                                        }"
                                        :disabled="variant.stock <= 0"
                                        @click="if(variant.stock > 0) toggleOption(variant)"
                                        style="border-radius: 1rem;">
                                    <div class="d-flex align-items-center gap-2">
                                        <template x-if="optionProduct.selection_type === 'multiple'">
                                            <i class="bi fs-5"
                                               :class="isOptionSelected(variant.name) ? 'bi-check-square-fill text-primary' : 'bi-square text-muted'"></i>
                                        </template>
                                        <template x-if="optionProduct.selection_type !== 'multiple'">
                                            <i class="bi fs-5"
                                               :class="isOptionSelected(variant.name) ? 'bi-record-circle-fill text-primary' : 'bi-circle text-muted'"></i>
                                        </template>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1" x-text="variant.name"></h6>
                                            <span class="small badge rounded-pill"
                                                  :class="variant.stock > 0 ? 'bg-body-tertiary text-muted border' : 'bg-danger text-white'"
                                                  x-text="variant.stock > 0 ? 'Tersedia: ' + variant.stock : 'Stok Habis'"></span>
                                        </div>
                                    </div>
                                    <h5 class="fw-bold mb-0" style="color: var(--brand-caramel);"
                                        x-text="'Rp ' + formatRupiah(variant.price)"></h5>
                                </button>
                            </template>
                        </template>
                    </div>

                    {{-- Qty + Confirm --}}
                    <div class="mt-4 pt-3 border-top" x-show="optionSelected.length > 0">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fw-bold text-muted small">JUMLAH</span>
                            <div class="d-flex align-items-center bg-body-tertiary rounded-pill border" style="padding: 0.15rem;">
                                <button @click="if(optionQty > 1) optionQty--" class="btn btn-sm btn-light rounded-circle p-1"
                                        style="width: 32px; height: 32px;"><i class="bi bi-dash"></i></button>
                                <span class="fw-bold px-3" x-text="optionQty"></span>
                                <button @click="optionQty++" class="btn btn-sm btn-primary rounded-circle p-1"
                                        style="width: 32px; height: 32px;"><i class="bi bi-plus"></i></button>
                            </div>
                        </div>
                        <button @click="confirmOption"
                                class="btn btn-primary fw-bold w-100 py-3 d-flex justify-content-between align-items-center"
                                style="border-radius: 1rem;"
                                :disabled="optionSelected.length === 0">
                            <span><i class="bi bi-cart-plus me-2"></i>Tambahkan</span>
                            <span x-text="'Rp ' + formatRupiah(optionTotalPrice)"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Cancel Modal Component --}}
    <div @cancel-confirmed.window="$wire.cancelOrder($event.detail)">
        <x-tenant.order.cancel-modal />
    </div>

</div>

@script
<script>
    Alpine.data('restoPos', () => ({
        cart: [],
        selectedProduct: null,
        variantModalInstance: null,
        paymentModalInstance: null,
        successModalInstance: null,
        optionModalInstance: null,

        // Option/Multi-select state
        optionProduct: null,
        optionSelected: [],  // array of variant names
        optionQty: 1,

        customerName: '',
        tableNumber: '',
        orderType: @json($restoOrderTypes[0]['id'] ?? 'dinein'),
        paymentMethod: 'cash',
        amountPaid: '',
        payDiscount: 0,
        isSubmitting: false,
        stockError: '',
        lastOrder: {},
        payingOrder: null,

        init() {
            this.variantModalInstance = new bootstrap.Modal(document.getElementById('variantModal'));
            this.paymentModalInstance = new bootstrap.Modal(document.getElementById('paymentModal'));
            this.successModalInstance = new bootstrap.Modal(document.getElementById('successModal'));
            this.optionModalInstance = new bootstrap.Modal(document.getElementById('optionModal'));
            this.$watch('cart', () => this.validateStock(), {deep: true});
        },

        get subTotal() {
            return this.cart.reduce((t, i) => t + i.subtotal, 0);
        },
        get payTotal() {
            if (this.payingOrder) return Math.max(0, this.payingOrder.subtotal - (parseFloat(this.payDiscount) || 0));
            return this.subTotal;
        },
        get getChange() {
            return Math.max(0, (parseFloat(this.amountPaid) || 0) - this.payTotal);
        },

        // === Product handling with selection_type support ===
        handleProductClick(product) {
            if (product.stock <= 0) { showIslandToast('Stok habis!', 'warning'); return; }

            // Multi-select (toppings, flavors) or single with >1 variant → open option modal
            if (product.selection_type === 'multiple' || (product.has_variants && product.variants.length > 1)) {
                this.openOptionModal(product);
            } else {
                // Simple product: 1 variant, langsung masuk cart
                this.addToCart(product, product.variants[0]);
            }
        },

        // === Option Modal (supports both single & multiple selection) ===
        openOptionModal(product) {
            this.optionProduct = product;
            this.optionQty = 1;

            if (product.selection_type === 'multiple') {
                this.optionSelected = [];
            } else {
                // Single: pre-select first variant with stock
                const firstAvailable = product.variants.find(v => v.stock > 0);
                this.optionSelected = firstAvailable ? [firstAvailable.name] : [];
            }
            this.optionModalInstance.show();
        },

        toggleOption(variant) {
            if (!this.optionProduct) return;

            if (this.optionProduct.selection_type === 'multiple') {
                const idx = this.optionSelected.indexOf(variant.name);
                if (idx > -1) {
                    this.optionSelected.splice(idx, 1);
                } else {
                    if (this.optionSelected.length >= this.optionProduct.max_selections) {
                        showIslandToast(`Maksimal pilih ${this.optionProduct.max_selections} varian!`, 'warning');
                        return;
                    }
                    this.optionSelected.push(variant.name);
                }
            } else {
                // Single select: replace
                this.optionSelected = [variant.name];
            }
        },

        isOptionSelected(name) {
            return this.optionSelected.includes(name);
        },

        get optionTotalPrice() {
            if (!this.optionProduct) return 0;
            // Sum prices of all selected variants × qty
            let total = 0;
            for (const name of this.optionSelected) {
                const v = this.optionProduct.variants.find(v => v.name === name);
                if (v) total += v.price;
            }
            return total * this.optionQty;
        },

        confirmOption() {
            if (!this.optionProduct || this.optionSelected.length === 0) return;

            // Each selected variant → its own cart line item (correct stock tracking)
            for (const variantName of this.optionSelected) {
                const variant = this.optionProduct.variants.find(v => v.name === variantName);
                if (!variant) continue;

                const existing = this.cart.find(i => i.variant_id === variant.id);
                if (existing) {
                    if (existing.quantity + this.optionQty <= variant.stock) {
                        existing.quantity += this.optionQty;
                        existing.subtotal = existing.quantity * existing.price;
                    } else {
                        showIslandToast(`Stok ${variant.name} sisa ${variant.stock}!`, 'warning');
                    }
                } else {
                    if (this.optionQty > variant.stock) {
                        showIslandToast(`Stok ${variant.name} sisa ${variant.stock}!`, 'warning');
                        continue;
                    }
                    this.cart.push({
                        id: this.optionProduct.id,
                        variant_id: variant.id,
                        name: this.optionProduct.name,
                        variant_name: variant.name,
                        price: variant.price,
                        quantity: this.optionQty,
                        subtotal: variant.price * this.optionQty,
                        stock: variant.stock,
                        note: ''
                    });
                }
            }

            this.optionModalInstance.hide();
            setTimeout(() => this.optionProduct = null, 300);
        },

        // === Standard cart operations ===
        addToCart(product, variant) {
            let existing = this.cart.find(i => i.variant_id === variant.id);
            if (existing) {
                if (existing.quantity < variant.stock) {
                    existing.quantity++;
                    existing.subtotal = existing.quantity * variant.price;
                } else { showIslandToast(`Mentok! Stok sisa ${variant.stock}.`, 'warning'); }
            } else {
                this.cart.push({
                    id: product.id, variant_id: variant.id, name: product.name,
                    variant_name: product.has_variants ? variant.name : null,
                    price: variant.price, quantity: 1, subtotal: variant.price,
                    stock: variant.stock, note: ''
                });
            }
        },

        addVariantToCart(variant) {
            this.addToCart(this.selectedProduct || this.optionProduct, variant);
            this.variantModalInstance.hide();
            setTimeout(() => this.selectedProduct = null, 300);
        },

        increaseQty(i) {
            let item = this.cart[i];
            if (item.quantity < item.stock) { item.quantity++; item.subtotal = item.quantity * item.price; }
            else { showIslandToast(`Mentok! Stok sisa ${item.stock}.`, 'warning'); }
        },
        decreaseQty(i) {
            if (this.cart[i].quantity > 1) {
                this.cart[i].quantity--;
                this.cart[i].subtotal = this.cart[i].quantity * this.cart[i].price;
            } else { this.removeFromCart(i); }
        },
        removeFromCart(i) { this.cart.splice(i, 1); },
        clearCart() { this.cart = []; },
        validateStock() {
            this.stockError = '';
            for (let item of this.cart) {
                if (item.quantity > item.stock) { this.stockError = `Batas stok ${item.name} dilewati!`; break; }
            }
        },

        // === Submit new order (PENDING, no payment) ===
        async submitNewOrder() {
            if (this.cart.length === 0) { showIslandToast('Keranjang kosong!', 'warning'); return; }
            this.isSubmitting = true;
            try {
                const result = await $wire.createOrder(this.cart, this.customerName, this.tableNumber, this.orderType);
                if (result && result.success) {
                    showIslandToast(`Pesanan ${result.invoice_code} berhasil dibuat!`, 'success');
                    this.clearCart();
                    this.customerName = '';
                    this.tableNumber = '';
                    Livewire.dispatch('stock-updated');
                } else if (result && result.error) {
                    showIslandToast(result.error, 'danger');
                    Livewire.dispatch('stock-updated');
                }
            } catch (e) { showIslandToast('Kesalahan sistem.', 'danger'); }
            this.isSubmitting = false;
        },

        // === Pay pending order from queue ===
        openPayForOrder(order) {
            this.payingOrder = order;
            this.payDiscount = 0;
            this.amountPaid = '';
            this.paymentMethod = 'cash';
            this.paymentModalInstance.show();
        },

        // === Pay direct from cart (Direct checkout) ===
        openDirectPaymentModal() {
            this.validateStock();
            if (this.cart.length === 0 || this.stockError !== '') {
                showIslandToast(this.stockError || 'Keranjang kosong!', 'warning'); return;
            }
            this.payingOrder = null; // null means direct checkout from cart
            this.payDiscount = 0;
            this.amountPaid = '';
            this.paymentMethod = 'cash';
            this.paymentModalInstance.show();
        },

        async submitPayment() {
            if (this.paymentMethod === 'cash' && (this.amountPaid < this.payTotal || !this.amountPaid)) {
                showIslandToast('Uang tidak cukup!', 'warning'); return;
            }
            this.isSubmitting = true;
            try {
                let result;
                let isDirect = !this.payingOrder;

                if (this.payingOrder) {
                    result = await $wire.processPayment(
                        this.payingOrder.id, this.paymentMethod, this.payDiscount || 0, this.amountPaid
                    );
                } else {
                    result = await $wire.processDirectCheckout(
                        this.cart, this.customerName, this.tableNumber, this.orderType, this.paymentMethod, this.payDiscount || 0, this.amountPaid
                    );
                }

                if (result && result.success) {
                    this.lastOrder = result;
                    this.paymentModalInstance.hide();
                    this.payingOrder = null;
                    if (isDirect) {
                        this.clearCart();
                        this.customerName = '';
                        this.tableNumber = '';
                    }
                    Livewire.dispatch('stock-updated');
                    setTimeout(() => this.successModalInstance.show(), 300);
                } else if (result && result.error) {
                    showIslandToast(result.error, 'danger');
                }
            } catch (e) { showIslandToast('Kesalahan sistem.', 'danger'); }
            this.isSubmitting = false;
        },

        // === Helpers ===
        formatRupiah(n) { return new Intl.NumberFormat('id-ID').format(n); },
        appendNumber(num) {
            let c = String(this.amountPaid || '');
            if (c.length < 12) this.amountPaid = parseInt(c + num);
        },
        deleteNumber() {
            let c = String(this.amountPaid || '');
            this.amountPaid = c.length > 1 ? parseInt(c.slice(0, -1)) : '';
        },
        formatPhoneForWA(phone) {
            let c = ('' + phone).replace(/\D/g, '');
            if (c.startsWith('0')) return '62' + c.substring(1);
            if (!c.startsWith('62')) return '62' + c;
            return c;
        },
        sendWa() {
            if (this.lastOrder.customer_phone) {
                $wire.updateCustomerPhone(this.lastOrder.invoice_code, this.lastOrder.customer_phone);
                let phone = this.formatPhoneForWA(this.lastOrder.customer_phone);
                let url = `${window.location.origin}/invoice/${this.lastOrder.invoice_code}`;
                let msg = `Halo Kak *${this.lastOrder.customer_name}*,\n\nTerima kasih!\nStruk: ${url}\nTotal: Rp ${this.formatRupiah(this.lastOrder.total_price)}`;
                window.open(`https://wa.me/${phone}?text=${encodeURIComponent(msg)}`, '_blank');
                this.closeSuccessModal();
            }
        },
        closeSuccessModal() {
            this.successModalInstance.hide();
            this.payingOrder = null;
            this.lastOrder = {};
            this.payDiscount = 0;
            this.amountPaid = '';
        },
    }));
</script>
@endscript
