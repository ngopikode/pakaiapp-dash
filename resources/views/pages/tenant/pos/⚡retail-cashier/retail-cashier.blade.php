<div class="pos-container d-flex flex-column h-100 bg-transparent position-relative"
     x-data="retailPos()"
     @add-product.window="handleProductClick($event.detail.product)"
     @barcode-scanned.window="handleBarcodeScan($event.detail.product, $event.detail.variant)"
     @barcode-not-found.window="showIslandToast('Barcode tidak ditemukan', 'danger')"
     @keydown.window="handleKeydown($event)"
     x-cloak>

<style>
    @media (max-width: 767.98px) {
        .mobile-help-fab {
            position: fixed !important;
            bottom: 24px !important;
            right: 24px !important;
            width: 48px !important;
            height: 48px !important;
            z-index: 1040 !important;
            background: linear-gradient(135deg, var(--brand-caramel, #B67332), var(--brand-mocha, #846A58)) !important;
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
            box-shadow: 0 8px 24px rgba(0,0,0,0.2) !important;
            margin: 0 !important;
            display: flex !important;
            transition: bottom 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), transform 0.2s ease, box-shadow 0.2s ease !important;
        }
        
        .mobile-help-fab.active-cart {
            bottom: 96px !important; /* Raised to float above the bottom "View Cart" checkout button */
        }
        
        .mobile-help-fab:hover, .mobile-help-fab:active {
            transform: scale(1.08) !important;
            box-shadow: 0 10px 28px rgba(0,0,0,0.25) !important;
        }

        .mobile-help-fab i {
            font-size: 1.3rem !important; /* Slightly larger icon for comfortable mobile tapping */
        }
    }
</style>

    {{-- Premium Glassmorphism Loading Screen --}}
    <div wire:loading wire:target="changeTab" 
         class="position-absolute top-0 start-0 w-100 h-100"
         style="z-index: 2000; background: rgba(var(--bs-body-bg-rgb), 0.7); backdrop-filter: blur(8px); border-radius: 1.5rem; transition: all 0.3s ease;">
        <div class="w-100 h-100 d-flex justify-content-center align-items-center">
            <div class="text-center bg-body p-4 rounded-4 shadow border" style="border-color: var(--bs-border-color-translucent) !important; min-width: 180px;">
                <div class="spinner-border text-warning mb-3" role="status" style="width: 2.5rem; height: 2.5rem; border-width: 4px;">
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

        {{-- Premium Help Button (Dynamic FAB on Mobile, Standard Circle on Desktop) --}}
        <button @click="showTutorialModal()"
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
            @include('pages.tenant.pos._cart-retail')
        </div>
    </div>

    {{-- ===== TAB 2: RIWAYAT TRANSAKSI ===== --}}
    <div x-show="currentTab === 'history'" class="flex-grow-1 overflow-y-auto bg-transparent" style="min-height: 0;"
         x-transition.opacity.duration.150ms>
        @include('pages.tenant.post._history-retail')
    </div>

    {{-- Floating Cart Button for Mobile (Safe Template Destructive DOM Toggle) --}}
    <template x-if="currentTab === 'cashier' && !isMobileCartOpen">
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
    @include('pages.tenant.pos._modal-held-orders')
    @include('pages.tenant.pos._modal-tutorial', ['mode' => 'retail'])

    {{-- Cancel Modal Component --}}
    <div @cancel-confirmed.window="$wire.cancelOrder($event.detail)">
        <x-tenant.order.cancel-modal/>
    </div>

</div>

@if(config('midtrans.client_key'))
    @push('scripts')
        <script src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
    @endpush
@endif

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
        heldOrdersModalInstance: null,
        tutorialModalInstance: null,

        customerName: '',
        customerPhone: '',
        globalDiscount: '',
        paymentMethod: 'cash',
        amountPaid: '',

        isSubmitting: false,
        payingOrder: null,            // Pesanan pending yang sedang dibayar dari riwayat
        payDiscount: 0,
        // Duitku — state untuk opsi digital payment di payment modal
        duitkuMethod: null,           // Kode metode Duitku: 'QRIS', 'BV', 'I1', dll
        duitkuCustomerEmail: '',      // Email customer wajib untuk Duitku
        duitkuPaymentMethods: [],     // Daftar metode pembayaran Duitku dinamis

        async fetchDuitkuMethods() {
            @if(!config('duitku.enabled'))
            return;
            @endif
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

        heldOrders: JSON.parse(localStorage.getItem('posHeldOrders') || '[]'),
        barcodeBuffer: '',
        barcodeTimeout: null,

        init() {
            this.variantModalInstance = new bootstrap.Modal(document.getElementById('variantModal'));
            this.paymentModalInstance = new bootstrap.Modal(document.getElementById('paymentModal'));
            this.successModalInstance = new bootstrap.Modal(document.getElementById('successModal'));
            this.heldOrdersModalInstance = new bootstrap.Modal(document.getElementById('heldOrdersModal'));
            this.tutorialModalInstance = new bootstrap.Modal(document.getElementById('tutorialModal'));
            this.$watch('cart', () => this.validateStock(), {deep: true});
            this.$watch('heldOrders', (val) => localStorage.setItem('posHeldOrders', JSON.stringify(val)), {deep: true});
        },

        // === Barcode & Keyboard ===
        handleKeydown(e) {
            if (e.key === 'F2') { e.preventDefault(); this.openPaymentModal(); return; }
            if (e.key === 'F4') { e.preventDefault(); this.clearCart(); return; }
            if (e.key === 'F8') { 
                e.preventDefault(); 
                if (this.cart.length > 0) this.holdOrder(); 
                else this.openHeldOrdersModal();
                return; 
            }

            if (e.key === 'Enter' && document.getElementById('paymentModal').classList.contains('show')) {
                e.preventDefault();
                this.submitPayment();
                return;
            }

            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;

            if (e.key === 'Enter') {
                if (this.barcodeBuffer.length > 2) {
                    $wire.scanBarcode(this.barcodeBuffer);
                }
                this.barcodeBuffer = '';
                return;
            }

            if (e.key.length === 1) {
                this.barcodeBuffer += e.key;
                clearTimeout(this.barcodeTimeout);
                this.barcodeTimeout = setTimeout(() => {
                    this.barcodeBuffer = '';
                }, 50);
            }
        },
        handleBarcodeScan(product, variant) {
            showIslandToast('Barcode dipindai: ' + variant.name, 'success');
            this.addToCart(product, variant);
        },

        // === Hold Orders ===
        holdOrder() {
            if (this.cart.length === 0) return;
            this.heldOrders.push({
                cart: JSON.parse(JSON.stringify(this.cart)),
                customerName: this.customerName,
                customerPhone: this.customerPhone,
                globalDiscount: this.globalDiscount,
                subTotal: this.subTotal,
                grandTotal: this.grandTotal,
                time: new Date().toISOString()
            });
            this.clearCart();
            showIslandToast('Pesanan disimpan sementara (Hold)', 'warning');
        },
        openHeldOrdersModal() {
            this.heldOrdersModalInstance.show();
        },
        recallOrder(index) {
            let order = this.heldOrders[index];
            this.cart = order.cart;
            this.customerName = order.customerName;
            this.customerPhone = order.customerPhone;
            this.globalDiscount = order.globalDiscount;
            this.heldOrders.splice(index, 1);
            this.heldOrdersModalInstance.hide();
            showIslandToast('Pesanan dilanjutkan', 'info');
        },
        removeHeldOrder(index) {
            this.heldOrders.splice(index, 1);
        },

        // === Totals (with per-item discount) ===
        get subTotal() {
            return this.cart.reduce((t, i) => t + i.subtotal, 0);
        },
        get grandTotal() {
            return Math.max(0, this.subTotal - (parseFloat(this.globalDiscount) || 0));
        },
        get payTotal() {
            if (this.payingOrder) {
                return Math.max(0, parseFloat(this.payingOrder.total_price) || 0);
            }
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
            this.payingOrder = null;
            this.payDiscount = 0;
            this.amountPaid = '';
            this.paymentMethod = 'cash';
            this.duitkuMethod = null;
            this.duitkuCustomerEmail = '';
            this.paymentModalInstance.show();
            this.fetchDuitkuMethods();
        },

        openPayForOrder(order) {
            this.payingOrder = order;
            this.payDiscount = parseFloat(order.discount) || 0;
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
                        customer_name:  (this.customerName || '').trim() || 'Pelanggan POS',
                        customer_email: custEmail || 'noreply@pakaiapp.online',
                        total_price:    this.payTotal,
                        payment_method: this.paymentMethod === 'digital' ? 'digital' : this.duitkuMethod,
                        order_type:     this.orderType || 'retail',
                        items:          cartItems,
                    };

                    const res  = await fetch('/api/orders', {
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
                        if (isDirect) { this.clearCart(); this.customerName = ''; this.tableNumber = ''; }
                        this.duitkuMethod = null;
                        this.duitkuCustomerEmail = '';
                    } else if (res.ok && data.data?.snap_token) {
                        this.paymentModalInstance.hide();
                        window.snap.pay(data.data.snap_token, {
                            onSuccess: (res) => {
                                showIslandToast(`Pembayaran berhasil!`, 'success');
                                if (isDirect) { this.clearCart(); this.customerName = ''; this.tableNumber = ''; }
                            },
                            onPending: (res) => {
                                showIslandToast(`Menunggu pembayaran...`, 'warning');
                                if (isDirect) { this.clearCart(); this.customerName = ''; this.tableNumber = ''; }
                            },
                            onError: (res) => {
                                showIslandToast(`Pembayaran gagal.`, 'danger');
                            },
                            onClose: () => {
                                showIslandToast(`Popup ditutup sebelum pembayaran selesai.`, 'warning');
                                if (isDirect) { this.clearCart(); this.customerName = ''; this.tableNumber = ''; }
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
                        console.error("API Order Error:", data);
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
                    result = await $wire.processCheckout(
                        this.cart, this.customerName, this.customerPhone,
                        this.globalDiscount || 0, this.paymentMethod, this.amountPaid
                    );
                }

                if (result && result.success) {
                    this.lastOrder = result;
                    this.paymentModalInstance.hide();
                    this.payingOrder = null;
                    if (isDirect) {
                        this.clearCart();
                        this.customerName = '';
                        this.customerPhone = '';
                        this.globalDiscount = '';
                    }
                    Livewire.dispatch('stock-updated');
                    setTimeout(() => this.successModalInstance.show(), 300);
                } else if (result && result.error) {
                    this.paymentModalInstance.hide();
                    showIslandToast(result.error, 'danger');
                    Livewire.dispatch('stock-updated');
                }
            } catch (e) {
                this.paymentModalInstance.hide();
                console.error("Kasir Sistem Error:", e);
                showIslandToast('Sistem Error: ' + e.message, 'danger');
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
        },
        showTutorialModal() {
            localStorage.setItem('pakaiapp_tutorial_dismissed', 'true');
            window.dispatchEvent(new CustomEvent('tutorial-opened'));
            this.tutorialModalInstance.show();
        }
    }));
</script>
@endscript
