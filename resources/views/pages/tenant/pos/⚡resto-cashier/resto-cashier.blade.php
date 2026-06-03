<div class="pos-container d-flex flex-column h-100 bg-transparent position-relative" x-data="restoPos()"
     @add-product.window="handleProductClick($event.detail.product)"
     @barcode-not-found.window="showIslandToast('Barcode tidak ditemukan', 'danger')"
     @keydown.window="handleKeydown($event)"
     @open-mobile-cart.window="isMobileCartOpen = true"
     @close-mobile-cart.window="isMobileCartOpen = false"
     x-cloak>

    {{-- Premium Glassmorphism Loading Screen --}}
    <div wire:loading wire:target="changeTab"
         class="position-absolute top-0 start-0 w-100 h-100"
         style="z-index: 2000; background: rgba(var(--bs-body-bg-rgb), 0.7); backdrop-filter: blur(8px); border-radius: 1.5rem; transition: all 0.3s ease;">
        <div class="w-100 h-100 d-flex justify-content-center align-items-center">
            <div class="text-center bg-body p-4 rounded-4 shadow border"
                 style="border-color: var(--bs-border-color-translucent) !important; min-width: 180px;">
                <div class="spinner-border text-warning mb-3" role="status"
                     style="width: 2.5rem; height: 2.5rem; border-width: 4px;">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <h6 class="fw-bold mb-1 text-body">Sinkronisasi...</h6>
                <small class="text-secondary" style="font-size: 0.75rem;">Mengambil data terbaru</small>
            </div>
        </div>
    </div>

    {{-- Tab Navigation (Safe Context Colors) --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-shrink-0 px-3 px-lg-0 mt-3 mt-lg-0">
        <div class="d-flex gap-2">
            <button wire:click="changeTab('cashier')" @click="if(isEditingOrder) window.location.href='/cashier'"
                    class="btn fw-bold px-4 py-2 d-flex align-items-center gap-2 transition-all"
                    :class="currentTab === 'cashier' ? 'btn-primary shadow' : 'btn-outline-secondary bg-body-tertiary border text-secondary'"
                    style="border-radius: 1rem;">
                <i class="bi bi-plus-circle"></i> Kasir Baru
            </button>
            <button wire:click="changeTab('queue')" title="Daftar Antrean"
                    class="btn fw-bold px-4 py-2 d-flex align-items-center gap-2 transition-all"
                    :class="currentTab === 'queue' ? 'btn-warning shadow text-dark' : 'btn-outline-secondary bg-body-tertiary border text-secondary'"
                    style="border-radius: 1rem;">
                <i class="bi bi-hourglass-split"></i>
                <span>Antrian</span>

                @if($pendingOrders->count() > 0)
                    <!-- Badge digeser masuk secara inline (sejajar teks), dijamin gak bakal mentok ujung layar luar lagi -->
                    <small class="bg-danger text-white fw-bold d-flex align-items-center justify-content-center px-2"
                           style="min-width: 20px; height: 20px; font-size: 0.7rem; border-radius: 10px; margin-left: 2px;">
                        {{ $pendingOrders->count() }}
                    </small>
                @endif
            </button>
        </div>

        {{-- Premium Help Button (Dynamic FAB on Mobile, Standard Circle on Desktop) --}}
        <button id="tour-pos-help" @click="window.dispatchEvent(new CustomEvent('start-pos-tour'))"
                class="btn btn-outline-secondary bg-body-tertiary border text-secondary rounded-circle shadow-sm d-flex align-items-center justify-content-center transition-all me-3 me-lg-0 mobile-help-fab"
                :class="currentTab === 'cashier' && !isMobileCartOpen && cart.length > 0 ? 'active-cart' : ''"
                style="width: 40px; height: 40px; border-radius: 50% !important;"
                title="Panduan & Tutorial Penggunaan">
            <i class="bi bi-question-circle fs-5"></i>
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
            @include('pages.tenant.pos._cart-resto', ['orderTypes' => $restoOrderTypes])
        </div>
    </div>

    {{-- ===== TAB 2: ANTRIAN (Pesanan Pending) ===== --}}
    <div x-show="currentTab === 'queue'" class="flex-grow-1 overflow-y-auto bg-transparent" style="min-height: 0;"
         x-transition.opacity.duration.150ms>
        @include('pages.tenant.post._queue-resto')
    </div>

    {{-- Floating Cart Button for Mobile (Safe Template Destructive DOM Toggle) --}}
    <template x-if="currentTab === 'cashier' && !isMobileCartOpen">
        <button
            class="btn btn-primary fw-bold p-3 floating-cart-btn d-lg-none d-flex justify-content-between align-items-center text-white"
            @click="isMobileCartOpen = true"
            style="position: fixed; bottom: 65px; left: 50%; transform: translateX(-50%); width: 90%; z-index: 1030; border-radius: 1rem; background: #F97316; border: none; box-shadow: 0 10px 25px rgba(249, 115, 22, 0.25);">
            <span><i class="bi bi-cart3 me-2"></i>Lihat Keranjang (<span x-text="cart.length"></span>)</span>
            <span x-text="'Rp ' + formatRupiah(subTotal)"></span>
        </button>
    </template>

    {{-- Shared Modals --}}
    @include('pages.tenant.pos._modal-payment')
    @include('pages.tenant.pos._modal-variant')
    @include('pages.tenant.pos._modal-success')
    @include('pages.tenant.pos._pos-tour-guide', ['mode' => 'resto'])

    {{-- ===== OPTION MODAL ===== --}}
    @include('pages.tenant.pos._modal-option')

    {{-- Cancel Modal Component --}}
    <div @cancel-confirmed.window="$wire.cancelOrder($event.detail)">
        <x-tenant.order.cancel-modal/>
    </div>

</div>

@script
<script>
    Alpine.data('restoPos', () => ({
        cart: [],
        isMobileCartOpen: false,

        currentTab: $wire.entangle('activeTab').live,

        selectedProduct: null,
        variantModalInstance: null,
        paymentModalInstance: null,
        successModalInstance: null,
        optionModalInstance: null,

        optionProduct: null,
        optionSelected: [],
        extrasSelected: [],
        optionQty: 1,

        customerName: @json($existingOrder ? $existingOrder->customer_name : ''),
        tableNumber: @json($existingOrder ? ($existingOrder->table_number ?? $existingOrder->notes) : ''),
        orderType: @json($existingOrder ? $existingOrder->order_type : ($restoOrderTypes[0]['id'] ?? 'dinein')),
        isEditingOrder: @json($existingOrder ? true : false),
        editInvoiceCode: @json($existingOrder ? $existingOrder->invoice_code : null),
        paymentMethod: 'cash',
        amountPaid: '',
        payDiscount: 0,
        isSubmitting: false,
        // Duitku — state untuk opsi digital payment di payment modal
        duitkuMethod: null,           // Kode metode Duitku: 'QRIS', 'BV', 'I1', dll
        duitkuCustomerEmail: '',      // Email customer wajib untuk Duitku
        duitkuPaymentMethods: [],     // Daftar metode pembayaran Duitku dinamis

        async fetchDuitkuMethods() {
            if (!{{ config('duitku.enabled') ? 'true' : 'false' }}) return;
            if (this.payTotal <= 0) return;
            try {
                const res = await fetch(`/api/duitku/payment-methods?amount=${this.payTotal}`);
                const data = await res.json();
                if (data.success && Array.isArray(data.data)) {
                    this.duitkuPaymentMethods = data.data;
                    if (this.duitkuPaymentMethods.length > 0) {
                        const hasQris = this.duitkuPaymentMethods.find(m => ['NQ', 'SP', 'QRIS', 'QRISC'].includes(m.paymentMethod));
                        if (hasQris) {
                            this.duitkuMethod = hasQris.paymentMethod;
                        } else {
                            this.duitkuMethod = this.duitkuPaymentMethods[0].paymentMethod;
                        }
                    }
                }
            } catch (e) {
                console.error('[Duitku] Gagal mengambil metode pembayaran dinamis', e);
            }
        },

        stockError: '',
        lastOrder: {},
        payingOrder: null,

        taxRate: @json($taxRate),
        serviceChargeRate: @json($serviceChargeRate),
        isTaxActive: @json($isTaxActive),
        isServiceActive: @json($isServiceChargeActive),

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
        get serviceChargeAmount() {
            if (!this.isServiceActive) return 0;
            return Math.round((parseFloat(this.serviceChargeRate) / 100) * this.subTotal);
        },
        get taxAmount() {
            if (!this.isTaxActive) return 0;
            return Math.round((parseFloat(this.taxRate) / 100) * (this.subTotal + this.serviceChargeAmount));
        },
        get subTotalWithCharges() {
            return this.subTotal + this.serviceChargeAmount + this.taxAmount;
        },
        get payTotal() {
            return this.payingOrder ? Math.max(0, (parseFloat(this.payingOrder.total_price) || parseFloat(this.payingOrder.subtotal)) - (parseFloat(this.payDiscount) || 0)) : this.subTotalWithCharges;
        },
        get getChange() {
            return Math.max(0, (parseFloat(this.amountPaid) || 0) - this.payTotal);
        },

        handleProductClick(product) {
            if (product.stock <= 0) {
                showIslandToast('Stok habis!', 'warning');
                return;
            }
            if (!product.variants || product.variants.length === 0) {
                showIslandToast('Produk ini belum memiliki varian harga yang valid.', 'danger');
                return;
            }
            if (product.selection_type === 'multiple' || (product.has_variants && product.variants.length > 1) || (product.extras && product.extras.length > 0)) {
                this.openOptionModal(product);
            } else {
                this.addToCart(product, product.variants[0]);
            }
        },

        openOptionModal(product) {
            this.optionProduct = product;
            this.optionQty = 1;
            this.optionSelected = product.selection_type === 'multiple' ? [] : (product.variants.find(v => v.stock > 0) ? [product.variants.find(v => v.stock > 0).name] : []);
            this.extrasSelected = [];
            this.optionModalInstance.show();
        },

        toggleExtra(extra) {
            const idx = this.extrasSelected.indexOf(extra.name);
            if (idx > -1) {
                this.extrasSelected.splice(idx, 1);
            } else {
                this.extrasSelected.push(extra.name);
            }
        },

        isExtraSelected(name) {
            return this.extrasSelected.includes(name);
        },

        get extrasTotal() {
            if (!this.optionProduct?.extras?.length) return 0;
            return this.optionProduct.extras
                .filter(e => this.extrasSelected.includes(e.name))
                .reduce((sum, e) => sum + (parseFloat(e.price) || 0), 0);
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
                this.optionSelected = [variant.name];
            }
        },

        isOptionSelected(name) {
            return this.optionSelected.includes(name);
        },

        get optionTotalPrice() {
            if (!this.optionProduct) return 0;
            let basePrice = 0;
            if (this.optionSelected.length > 0) {
                if (this.optionProduct.selection_type === 'multiple') {
                    const baseVariant = this.optionProduct.variants.find(v => v.name === this.optionSelected[0]);
                    basePrice = baseVariant ? parseFloat(baseVariant.price) : 0;
                } else {
                    const variant = this.optionProduct.variants.find(v => v.name === this.optionSelected[0]);
                    basePrice = variant ? parseFloat(variant.price) : 0;
                }
            } else {
                basePrice = parseFloat(this.optionProduct.price) || 0;
            }
            return (basePrice + this.extrasTotal) * this.optionQty;
        },

        confirmOption() {
            if (!this.optionProduct) return;

            // Validasi jika ada variant, harus pilih variant
            if (this.optionProduct.has_variants && this.optionSelected.length === 0) {
                showIslandToast('Silakan pilih varian terlebih dahulu!', 'warning');
                return;
            }

            let variant;
            let combinedVariantName = '';

            if (this.optionProduct.has_variants) {
                if (this.optionProduct.selection_type === 'multiple') {
                    combinedVariantName = this.optionSelected.join(', ');
                    variant = this.optionProduct.variants.find(v => v.name === this.optionSelected[0]);
                } else {
                    variant = this.optionProduct.variants.find(v => v.name === this.optionSelected[0]);
                    combinedVariantName = variant ? variant.name : '';
                }
            } else {
                // Non-variant product, gunakan default variant
                variant = this.optionProduct.variants[0];
            }

            if (!variant) {
                showIslandToast('Varian tidak ditemukan!', 'warning');
                return;
            }

            // Gabungkan label variant & extras
            const extrasLabel = this.extrasSelected.length ? this.extrasSelected.join(', ') : '';
            const finalVariantLabel = [combinedVariantName, extrasLabel].filter(Boolean).join(' + ');

            const basePrice = parseFloat(variant.price) || 0;
            const finalUnitPrice = basePrice + this.extrasTotal;

            // Cek stok variant
            const minStock = variant.stock;

            const existing = this.cart.find(i => i.variant_id === variant.id && i.variant_name === finalVariantLabel);
            if (existing) {
                if (existing.quantity + this.optionQty <= minStock) {
                    existing.quantity += this.optionQty;
                    existing.subtotal = existing.quantity * finalUnitPrice;
                } else {
                    showIslandToast(`Stok tidak mencukupi!`, 'warning');
                }
            } else {
                if (this.optionQty > minStock) {
                    showIslandToast(`Stok tidak mencukupi!`, 'warning');
                    return;
                }
                this.cart.push({
                    id: this.optionProduct.id,
                    variant_id: variant.id,
                    name: this.optionProduct.name,
                    variant_name: finalVariantLabel || null,
                    price: finalUnitPrice,
                    quantity: this.optionQty,
                    subtotal: finalUnitPrice * this.optionQty,
                    stock: minStock,
                    note: ''
                });
            }

            this.optionModalInstance.hide();
            setTimeout(() => this.optionProduct = null, 300);
        },

        addToCart(product, variant, qty = 1) {
            if (!variant) {
                showIslandToast('Varian produk tidak ditemukan.', 'danger');
                return;
            }
            let existing = this.cart.find(i => i.variant_id === variant.id);
            if (existing) {
                if (existing.quantity + qty <= variant.stock) {
                    existing.quantity += qty;
                    existing.subtotal = existing.quantity * variant.price;
                } else {
                    showIslandToast(`Stok sisa ${variant.stock}.`, 'warning');
                }
            } else {
                if (qty > variant.stock) {
                    showIslandToast(`Stok sisa ${variant.stock}.`, 'warning');
                    return;
                }
                this.cart.push({
                    id: product.id, variant_id: variant.id, name: product.name,
                    variant_name: product.has_variants ? variant.name : null,
                    price: variant.price, quantity: qty, subtotal: variant.price * qty,
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
            if (item.quantity < item.stock) {
                item.quantity++;
                item.subtotal = item.quantity * item.price;
            } else {
                showIslandToast(`Mentok! Stok sisa ${item.stock}.`, 'warning');
            }
        },
        decreaseQty(i) {
            if (this.cart[i].quantity > 1) {
                this.cart[i].quantity--;
                this.cart[i].subtotal = this.cart[i].quantity * this.cart[i].price;
            } else {
                this.removeFromCart(i);
            }
        },
        removeFromCart(i) {
            this.cart.splice(i, 1);
        },
        clearCart() {
            this.cart = [];
            this.isMobileCartOpen = false;
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

        async submitNewOrder() {
            if (this.cart.length === 0) {
                showIslandToast('Keranjang kosong!', 'warning');
                return;
            }
            this.isSubmitting = true;
            try {
                const result = await $wire.createOrder(this.cart, this.customerName, this.tableNumber, this.orderType, this.isTaxActive, this.isServiceActive);
                if (result && result.success) {
                    showIslandToast(this.isEditingOrder ? `Tambahan disimpan ke ${result.invoice_code}!` : `Pesanan ${result.invoice_code} berhasil dibuat!`, 'success');
                    this.clearCart();
                    this.customerName = '';
                    this.tableNumber = '';
                    Livewire.dispatch('stock-updated');
                    
                    if (this.isEditingOrder) {
                        setTimeout(() => { window.location.href = '/cashier'; }, 500);
                    }
                } else if (result && result.error) {
                    showIslandToast(result.error, 'danger');
                    Livewire.dispatch('stock-updated');
                }
            } catch (e) {
                showIslandToast('Kesalahan sistem.', 'danger');
            }
            this.isSubmitting = false;
        },

        openPayForOrder(order) {
            this.payingOrder = order;
            this.payDiscount = 0;
            this.amountPaid = '';
            this.paymentMethod = 'cash';
            this.duitkuMethod = null;
            this.duitkuCustomerEmail = '';
            this.paymentModalInstance.show();
            this.fetchDuitkuMethods();
        },

        openDirectPaymentModal() {
            this.validateStock();
            if (this.cart.length === 0 || this.stockError !== '') {
                showIslandToast(this.stockError || 'Keranjang kosong!', 'warning');
                return;
            }
            this.payingOrder = null;
            this.payDiscount = 0;
            this.amountPaid = '';
            this.paymentMethod = 'cash';
            this.duitkuMethod = null;
            this.duitkuCustomerEmail = '';
            this.paymentModalInstance.show();
            this.fetchDuitkuMethods();
        },

        async submitPayment() {
            if (this.paymentMethod === 'cash' && (this.amountPaid < this.payTotal || !this.amountPaid)) {
                showIslandToast('Uang tidak cukup!', 'warning');
                return;
            }

            // Validasi Duitku: cukup metode yang wajib ada, email opsional
            if (this.paymentMethod === 'duitku') {
                if (!this.duitkuMethod) {
                    showIslandToast('Pilih metode Duitku dulu!', 'warning');
                    return;
                }
                // Email opsional di kasir — kalau diisi, validasi formatnya saja
                const email = this.duitkuCustomerEmail.trim();
                if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                    showIslandToast('Format email tidak valid!', 'warning');
                    return;
                }
            }


            this.isSubmitting = true;
            try {
                let result;
                let isDirect = !this.payingOrder;

                // Duitku & Midtrans: gunakan Livewire action jika payingOrder (menghindari duplikasi order),
                // atau gunakan OrderApiController via /api/orders jika pesanan baru
                if (this.paymentMethod === 'duitku' || this.paymentMethod === 'digital') {
                    const custEmail = this.duitkuCustomerEmail ? this.duitkuCustomerEmail.trim() : '';
                    if (this.payingOrder) {
                        // Bayar pesanan yang ada (antrean) tanpa duplikasi
                        if (this.paymentMethod === 'duitku') {
                            result = await $wire.generateDuitkuPayment(
                                this.payingOrder.id, this.duitkuMethod, custEmail
                            );
                            if (result && result.success && result.payment_url) {
                                this.paymentModalInstance.hide();
                                window.open(result.payment_url, '_blank');
                                showIslandToast(`Link Duitku dibuka! Invoice: ${this.payingOrder.invoice_code}`, 'success');
                                this.payingOrder = null;
                                this.duitkuMethod = null;
                                this.duitkuCustomerEmail = '';
                            } else {
                                showIslandToast(result?.error || 'Gagal membuat invoice Duitku.', 'danger');
                            }
                            this.isSubmitting = false;
                            return;
                        } else if (this.paymentMethod === 'digital') {
                            result = await $wire.generateMidtransPayment(
                                this.payingOrder.id, custEmail
                            );
                            if (result && result.success && result.snap_token) {
                                this.paymentModalInstance.hide();
                                window.snap.pay(result.snap_token, {
                                    onSuccess: (res) => {
                                        showIslandToast(`Pembayaran berhasil!`, 'success');
                                        this.payingOrder = null;
                                    },
                                    onPending: (res) => {
                                        showIslandToast(`Menunggu pembayaran...`, 'warning');
                                    },
                                    onError: (res) => {
                                        showIslandToast(`Pembayaran gagal.`, 'danger');
                                    },
                                    onClose: () => {
                                        showIslandToast(`Popup ditutup sebelum pembayaran selesai.`, 'warning');
                                    }
                                });
                            } else {
                                showIslandToast(result?.error || 'Gagal membuat token Midtrans.', 'danger');
                            }
                            this.isSubmitting = false;
                            return;
                        }
                    }

                    // Pesanan Baru (Direct)
                    const cartItems = this.cart.map(i => ({
                        product_id: i.id,
                        name: i.name + (i.variant_name ? ` (${i.variant_name})` : ''),
                        quantity: i.quantity,
                        price: parseFloat(i.price)
                    }));

                    const payload = {
                        customer_name: (this.customerName || '').trim() || 'Pelanggan POS',
                        customer_email: custEmail || 'noreply@pakaiapp.online',
                        total_price: this.payTotal,
                        payment_method: this.paymentMethod === 'digital' ? 'digital' : this.duitkuMethod,
                        order_type: this.orderType || 'retail',
                        items: cartItems
                    };

                    const res = await fetch('/api/orders', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || ''
                        },
                        body: JSON.stringify(payload)
                    });
                    const data = await res.json();

                    if (res.ok && data.data?.payment_url) {
                        this.paymentModalInstance.hide();
                        window.open(data.data.payment_url, '_blank');
                        showIslandToast(`Link Duitku dibuka! Invoice: ${data.data.invoice_code}`, 'success');
                        if (isDirect) {
                            this.clearCart();
                            this.customerName = '';
                            this.tableNumber = '';
                        }
                        this.duitkuMethod = null;
                        this.duitkuCustomerEmail = '';
                    } else if (res.ok && data.data?.snap_token) {
                        this.paymentModalInstance.hide();
                        window.snap.pay(data.data.snap_token, {
                            onSuccess: (res) => {
                                showIslandToast(`Pembayaran berhasil!`, 'success');
                                if (isDirect) {
                                    this.clearCart();
                                    this.customerName = '';
                                    this.tableNumber = '';
                                }
                            },
                            onPending: (res) => {
                                showIslandToast(`Menunggu pembayaran...`, 'warning');
                                if (isDirect) {
                                    this.clearCart();
                                    this.customerName = '';
                                    this.tableNumber = '';
                                }
                            },
                            onError: (res) => {
                                showIslandToast(`Pembayaran gagal.`, 'danger');
                            },
                            onClose: () => {
                                showIslandToast(`Popup ditutup sebelum pembayaran selesai.`, 'warning');
                                if (isDirect) {
                                    this.clearCart();
                                    this.customerName = '';
                                    this.tableNumber = '';
                                }
                            }
                        });
                        this.duitkuMethod = null;
                        this.duitkuCustomerEmail = '';
                    } else {
                        let errorMsg = data.message || 'Gagal membuat invoice online.';
                        if (data.errors) {
                            errorMsg = Object.values(data.errors).flat().join(', ');
                        }
                        showIslandToast(errorMsg, 'danger');
                        console.error('API Order Error:', data);
                    }
                    this.isSubmitting = false;
                    return;
                }

                // Cash / QRIS manual / Transfer — flow Livewire seperti biasa
                if (this.payingOrder) {
                    result = await $wire.processPayment(
                        this.payingOrder.id, this.paymentMethod, this.payDiscount || 0, this.amountPaid
                    );
                } else {
                    result = await $wire.processDirectCheckout(
                        this.cart, this.customerName, this.tableNumber, this.orderType, this.paymentMethod, this.payDiscount || 0, this.amountPaid, this.isTaxActive, this.isServiceActive
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
                    this.paymentModalInstance.hide();
                    showIslandToast(result.error, 'danger');
                }
            } catch (e) {
                this.paymentModalInstance.hide();
                console.error('Kasir Sistem Error:', e);
                showIslandToast('Sistem Error: ' + e.message, 'danger');
            }
            this.isSubmitting = false;
        },

        handleKeydown(e) {
            if (e.key === 'F2') {
                e.preventDefault();
                if (this.currentTab === 'cashier') {
                    this.openDirectPaymentModal();
                }
                return;
            }
            if (e.key === 'F3') {
                e.preventDefault();
                if (this.currentTab === 'cashier' && this.cart.length > 0 && !this.isSubmitting) {
                    this.submitNewOrder();
                }
                return;
            }
            if (e.key === 'F4') {
                e.preventDefault();
                if (this.currentTab === 'cashier') {
                    this.clearCart();
                }
                return;
            }
            if (e.key === 'F8') {
                e.preventDefault();
                this.currentTab = this.currentTab === 'cashier' ? 'queue' : 'cashier';
                $wire.changeTab(this.currentTab);
                return;
            }

            if (e.key === 'Enter' && document.getElementById('paymentModal').classList.contains('show')) {
                e.preventDefault();
                this.submitPayment();
                return;
            }

            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
        },

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
                let msg = `Halo Kak *${this.lastOrder.customer_name}*,\n\nTerima kasih!\nStruk: ${url}\nTotal: Rp ${this.formatRupiah(this.lastOrder.total_price)}`;
                window.open(`https://wa.me/${phone}?text=${encodeURIComponent(msg)}`, '_blank');
                this.closeSuccessModal();
            }
        },
        closeSuccessModal() {
            this.successModalInstance.hide();
            this.clearCart();
            this.customerName = '';
            this.tableNumber = '';
            this.payDiscount = 0;
            this.amountPaid = '';
            this.lastOrder = {};
            this.payingOrder = null;
        }
    }));
</script>
@endscript
