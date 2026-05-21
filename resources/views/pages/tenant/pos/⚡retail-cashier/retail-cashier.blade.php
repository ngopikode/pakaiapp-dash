<div class="pos-container d-flex flex-column h-100 bg-transparent"
     x-data="retailPos()"
     @add-product.window="handleProductClick($event.detail.product)"
     x-cloak>

    {{-- Tab Navigation (Safe Context Colors) --}}
    <div class="d-flex gap-2 mb-3 flex-shrink-0 px-3 px-lg-0 mt-3 mt-lg-0">
        <button wire:click="changeTab('cashier')"
                class="btn fw-bold px-4 py-2 d-flex align-items-center gap-2 transition-all"
                :class="currentTab === 'cashier' ? 'btn-primary shadow' : 'btn-outline-secondary bg-body-tertiary border text-secondary'"
                style="border-radius: 1rem;">
            <i class="bi bi-plus-circle"></i> Kasir Baru
        </button>
        <button wire:click="changeTab('history')"
                class="btn fw-bold px-4 py-2 d-flex align-items-center gap-2 transition-all"
                :class="currentTab === 'history' ? 'btn-success shadow text-white' : 'btn-outline-secondary bg-body-tertiary border text-secondary'"
                style="border-radius: 1rem;">
            <i class="bi bi-clock-history"></i>
            <span>Riwayat Transaksi</span>

            @if($todayOrders->count() > 0)
                <small class="bg-light text-success fw-bold d-flex align-items-center justify-content-center px-2"
                       style="min-width: 20px; height: 20px; font-size: 0.7rem; border-radius: 10px; margin-left: 2px;">
                    {{ $todayOrders->count() }}
                </small>
            @endif
        </button>
    </div>

    {{-- ===== TAB 1: KASIR BARU ===== --}}
    <div x-show="currentTab === 'cashier'" class="row g-3 g-lg-4 flex-grow-1 mx-0" style="min-height: 0;"
         x-transition.opacity.duration.150ms>

        <!-- KOLOM PRODUK (Sembunyi di HP kalau keranjang dibuka) -->
        <div class="col-lg-7 col-xl-8 flex-column h-100 px-2 px-lg-3"
             :class="isMobileCartOpen ? 'd-none d-lg-flex' : 'd-flex'">
            <livewire:tenant.pos.product-list/>
        </div>

        <!-- KOLOM KERANJANG (Sembunyi di HP kalau belum pencet tombol keranjang) -->
        <div class="col-lg-5 col-xl-4 h-100 px-2 px-lg-3 cart-mobile-wrapper"
             :class="isMobileCartOpen ? 'd-block' : 'd-none d-lg-block'">
            @include('pages.tenant.pos._cart-retail')
        </div>
    </div>

    {{-- ===== TAB 2: RIWAYAT TRANSAKSI ===== --}}
    <div x-show="currentTab === 'history'" class="flex-grow-1 overflow-y-auto bg-transparent" style="min-height: 0;"
         x-transition.opacity.duration.150ms>
        @include('pages.tenant.post._history-retail')
    </div>

    {{-- Floating Cart Button for Mobile (Safe Template Destructive DOM Toggle) --}}
    <template x-if="currentTab === 'cashier' && !isMobileCartOpen && cart.length > 0">
        <button
            class="btn btn-primary fw-bold p-3 floating-cart-btn d-lg-none d-flex justify-content-between align-items-center text-white"
            @click="isMobileCartOpen = true"
            style="position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); width: 90%; z-index: 1030; border-radius: 1rem; background: linear-gradient(135deg, #ca8a04, #b45309); border: none; box-shadow: 0 10px 25px rgba(180, 83, 9, 0.4);">
            <span><i class="bi bi-cart3 me-2"></i>Lihat Keranjang (<span x-text="cart.length"></span>)</span>
            <span x-text="'Rp ' + formatRupiah(subTotal)"></span>
        </button>
    </template>

    {{-- Shared Modals --}}
    @include('pages.tenant.pos._modal-payment')
    @include('pages.tenant.pos._modal-variant')
    @include('pages.tenant.pos._modal-success')

</div>

@script
<script>
    Alpine.data('retailPos', () => ({
        cart: [],
        isMobileCartOpen: false,

        currentTab: $wire.entangle('activeTab').live,

        selectedProduct: null,
        variantModalInstance: null,
        paymentModalInstance: null,
        successModalInstance: null,

        customerName: '',
        customerPhone: '',
        globalDiscount: '',
        paymentMethod: 'cash',
        amountPaid: '',

        isSubmitting: false,
        stockError: '',
        lastOrder: {},

        init() {
            this.variantModalInstance = new bootstrap.Modal(document.getElementById('variantModal'));
            this.paymentModalInstance = new bootstrap.Modal(document.getElementById('paymentModal'));
            this.successModalInstance = new bootstrap.Modal(document.getElementById('successModal'));
            this.$watch('cart', () => this.validateStock(), {deep: true});
        },

        // === Totals (with per-item discount) ===
        get subTotal() {
            return this.cart.reduce((t, i) => t + i.subtotal, 0);
        },
        get grandTotal() {
            return Math.max(0, this.subTotal - (parseFloat(this.globalDiscount) || 0));
        },
        get payTotal() {
            return this.grandTotal;
        },
        get payDiscount() {
            let itemDiscounts = this.cart.reduce((t, i) => t + (parseFloat(i.itemDiscount) || 0), 0);
            return itemDiscounts + (parseFloat(this.globalDiscount) || 0);
        },
        get getChange() {
            return Math.max(0, (parseFloat(this.amountPaid) || 0) - this.payTotal);
        },

        // === Per-item discount recalculation ===
        recalcItemSubtotal(index) {
            let item = this.cart[index];
            let discount = parseFloat(item.itemDiscount) || 0;
            item.subtotal = Math.max(0, (item.price * item.quantity) - discount);
        },

        // === Product handling ===
        handleProductClick(product) {
            if (product.stock <= 0) {
                showIslandToast('Stok habis!', 'warning');
                return;
            }
            if (product.has_variants && product.variants.length > 1) {
                this.selectedProduct = product;
                this.variantModalInstance.show();
            } else {
                this.addToCart(product, product.variants[0]);
            }
        },
        addToCart(product, variant) {
            let existing = this.cart.find(i => i.variant_id === variant.id);
            if (existing) {
                if (existing.quantity < variant.stock) {
                    existing.quantity++;
                    existing.subtotal = (existing.quantity * variant.price) - (parseFloat(existing.itemDiscount) || 0);
                } else {
                    showIslandToast(`Mentok! Stok sisa ${variant.stock}.`, 'warning');
                }
            } else {
                this.cart.push({
                    id: product.id, variant_id: variant.id, name: product.name,
                    variant_name: product.has_variants ? variant.name : null,
                    price: variant.price, quantity: 1, subtotal: variant.price,
                    stock: variant.stock, itemDiscount: 0
                });
            }
        },
        addVariantToCart(variant) {
            this.addToCart(this.selectedProduct, variant);
            this.variantModalInstance.hide();
            setTimeout(() => this.selectedProduct = null, 300);
        },
        increaseQty(i) {
            let item = this.cart[i];
            if (item.quantity < item.stock) {
                item.quantity++;
                item.subtotal = Math.max(0, (item.quantity * item.price) - (parseFloat(item.itemDiscount) || 0));
            } else {
                showIslandToast(`Mentok! Stok sisa ${item.stock}.`, 'warning');
            }
        },
        decreaseQty(i) {
            if (this.cart[i].quantity > 1) {
                this.cart[i].quantity--;
                let item = this.cart[i];
                item.subtotal = Math.max(0, (item.quantity * item.price) - (parseFloat(item.itemDiscount) || 0));
            } else {
                this.removeFromCart(i);
            }
        },
        removeFromCart(i) {
            this.cart.splice(i, 1);
        },

        clearCart() {
            this.cart = [];
            this.isMobileCartOpen = false; // Otomatis balik ke menu di HP
        },

        validateStock() {
            this.stockError = '';
            for (let item of this.cart) {
                if (item.quantity > item.stock) {
                    this.stockError = `Batas stok ${item.name} dilewati!`;
                    break;
                }
            }
        },

        // === Payment ===
        openPaymentModal() {
            this.validateStock();
            if (this.cart.length === 0 || this.stockError !== '') {
                showIslandToast(this.stockError || 'Keranjang kosong!', 'warning');
                return;
            }
            this.amountPaid = '';
            this.paymentMethod = 'cash';
            this.paymentModalInstance.show();
        },

        async submitPayment() {
            if (this.paymentMethod === 'cash' && (this.amountPaid < this.payTotal || !this.amountPaid)) {
                showIslandToast('Uang tidak cukup!', 'warning');
                return;
            }
            this.isSubmitting = true;
            try {
                const result = await $wire.processCheckout(
                    this.cart, this.customerName, this.customerPhone,
                    this.globalDiscount || 0, this.paymentMethod, this.amountPaid
                );
                if (result && result.success) {
                    this.lastOrder = result;
                    this.paymentModalInstance.hide();
                    Livewire.dispatch('stock-updated');
                    setTimeout(() => this.successModalInstance.show(), 300);
                } else if (result && result.error) {
                    showIslandToast(result.error, 'danger');
                    Livewire.dispatch('stock-updated');
                }
            } catch (e) {
                showIslandToast('Kesalahan sistem.', 'danger');
            }
            this.isSubmitting = false;
        },

        // === Helpers ===
        formatRupiah(n) {
            return new Intl.NumberFormat('id-ID').format(n);
        },
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
                let msg = `Halo Kak *${this.lastOrder.customer_name}*,\n\nTerima kasih telah berbelanja di *${this.lastOrder.store_name}*.\nStruk: ${url}\n\nTotal: Rp ${this.formatRupiah(this.lastOrder.total_price)}`;
                window.open(`https://wa.me/${phone}?text=${encodeURIComponent(msg)}`, '_blank');
                this.closeSuccessModal();
            }
        },
        closeSuccessModal() {
            this.successModalInstance.hide();
            this.clearCart();
            this.customerName = '';
            this.customerPhone = '';
            this.globalDiscount = '';
            this.amountPaid = '';
            this.lastOrder = {};
        }
    }));
</script>
@endscript
