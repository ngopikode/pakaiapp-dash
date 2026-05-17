<div class="row g-4 pos-container h-100"
     x-data="retailPos()"
     @add-product.window="handleProductClick($event.detail.product)"
     x-cloak>

    <div class="col-lg-7 col-xl-8 d-flex flex-column h-100">
        <livewire:tenant.pos.product-list/>
    </div>

    <div class="col-lg-5 col-xl-4 h-100">
        @include('pages.tenant.pos._cart-retail')
    </div>

    {{-- Shared Modals --}}
    @include('pages.tenant.pos._modal-payment')
    @include('pages.tenant.pos._modal-variant')
    @include('pages.tenant.pos._modal-success')

</div>

@script
<script>
    Alpine.data('retailPos', () => ({
        cart: [],
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
        get payTotal() { return this.grandTotal; },
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
            if (product.stock <= 0) { showIslandToast('Stok habis!', 'warning'); return; }
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
                } else { showIslandToast(`Mentok! Stok sisa ${variant.stock}.`, 'warning'); }
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
            } else { showIslandToast(`Mentok! Stok sisa ${item.stock}.`, 'warning'); }
        },
        decreaseQty(i) {
            if (this.cart[i].quantity > 1) {
                this.cart[i].quantity--;
                let item = this.cart[i];
                item.subtotal = Math.max(0, (item.quantity * item.price) - (parseFloat(item.itemDiscount) || 0));
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

        // === Payment ===
        openPaymentModal() {
            this.validateStock();
            if (this.cart.length === 0 || this.stockError !== '') {
                showIslandToast(this.stockError || 'Keranjang kosong!', 'warning'); return;
            }
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
                const result = await $wire.processCheckout(
                    this.cart, this.customerName, this.customerPhone,
                    this.globalDiscount || 0, this.paymentMethod, this.amountPaid
                );
                if (result && result.success) {
                    this.lastOrder = result;
                    this.paymentModalInstance.hide();
                    Livewire.dispatchTo('tenant.pos.product-list', '$refresh');
                    setTimeout(() => this.successModalInstance.show(), 300);
                } else if (result && result.error) {
                    showIslandToast(result.error, 'danger');
                    Livewire.dispatchTo('tenant.pos.product-list', '$refresh');
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
        },
    }));
</script>
@endscript
