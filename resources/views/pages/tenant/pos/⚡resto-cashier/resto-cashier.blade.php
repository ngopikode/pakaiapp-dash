<div
    class="pos-shell m-2 h-full min-h-0 max-lg:h-[calc(100dvh-4rem)] overflow-hidden rounded-[2rem] bg-[#F5F2EA] px-0 pb-5 pt-3 text-slate-900 dark:bg-slate-950 dark:text-slate-100 lg:h-[90dvh] lg:px-4 lg:pb-6 lg:pt-4"
    x-data='restoPos({
        currentTab: $wire.entangle("activeTab").live,
        customerName: window.posInitialData?.customerName || "",
        tableNumber: window.posInitialData?.tableNumber || "",
        orderType: window.posInitialData?.orderType || "dinein",
        isEditingOrder: window.posInitialData?.isEditingOrder || false,
        editInvoiceCode: window.posInitialData?.editInvoiceCode || null,
        taxRate: @json($taxRate),
        serviceChargeRate: @json($serviceChargeRate),
        isTaxActive: @json($isTaxActive),
        isServiceActive: @json($isServiceChargeActive),
        duitkuEnabled: {{ config("duitku.enabled") ? "true" : "false" }}
    })'
    @add-product.window="handleProductClick($event.detail.product, $event.detail.variantId)"
    @barcode-not-found.window="showIslandToast('Barcode tidak ditemukan', 'danger')"
    @keydown.window="handleKeydown($event)"
    @open-mobile-cart.window="isMobileCartOpen = true"
    @close-mobile-cart.window="isMobileCartOpen = false"
    @toggle-desktop-cart.window="isDesktopCartOpen = !isDesktopCartOpen; isDesktopQueueDetailOpen = isDesktopCartOpen"
    @force-cashier-tab.window="currentTab = 'cashier'"
    @pos-change-tab.window="if($event.detail === 'cashier' && isEditingOrder) window.location.href='/cashier'; else { currentTab = $event.detail; if(currentTab === 'queue') { $wire.$island('queue').$refresh() } }"
    @open-payment-modal.window="openPayForOrder($event.detail)"
    @start-editing-order.window="isEditingOrder = true; editInvoiceCode = $event.detail.invoice_code; customerName = $event.detail.customer; tableNumber = $event.detail.table; orderType = $event.detail.type"
    x-cloak>

    <script>
        window.posInitialData = {
            customerName: @json($existingOrder ? $existingOrder->customer_name : ""),
            tableNumber: @json($existingOrder ? ($existingOrder->table_number ?? $existingOrder->notes) : ""),
            orderType: @json($existingOrder ? $existingOrder->order_type : ($restoOrderTypes[0]["id"] ?? "dinein")),
            isEditingOrder: @json((bool)$existingOrder),
            editInvoiceCode: @json($existingOrder ? $existingOrder->invoice_code : null)
        };
    </script>

    <div x-show="currentTab === 'cashier'"
         class="flex h-full min-h-0 flex-col gap-5 overflow-hidden lg:flex-row" x-transition.opacity.duration.150ms>
        {{-- Product list — always fills viewport --}}
        <div class="flex min-h-0 min-w-0 flex-1 flex-col overflow-hidden">
            <livewire:tenant.pos.product-list/>
        </div>

        {{-- Desktop cart (sidebar) --}}
        <div class="hidden lg:block shrink-0 overflow-hidden transition-all duration-300 ease-in-out"
             :class="isDesktopCartOpen ? 'w-[390px] xl:w-[430px] opacity-100 translate-x-0' : 'w-0 opacity-0 translate-x-8'">
            <div class="min-h-0 h-full w-[390px] xl:w-[430px] cart-mobile-wrapper">
                @include('pages.tenant.pos.partials._cart-resto', ['orderTypes' => $restoOrderTypes, 'isSheet' => false])
            </div>
        </div>

        {{-- Mobile bottom sheet overlay --}}
        <div x-show="isMobileCartOpen" x-cloak class="fixed inset-0 z-[1029]" :class="{'lg:hidden': isDesktopCartOpen}"
             x-transition.opacity>
            <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" @click="isMobileCartOpen = false"></div>

            <div
                class="absolute inset-x-0 bottom-0 z-[1030] flex max-h-[85dvh] flex-col rounded-t-[2rem] bg-white shadow-2xl dark:bg-slate-900 lg:bottom-1/2 lg:translate-y-1/2 lg:w-[450px] lg:mx-auto lg:rounded-[2rem]"
                x-show="isMobileCartOpen"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="translate-y-full"
                x-transition:enter-end="translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="translate-y-0"
                x-transition:leave-end="translate-y-full">
                {{-- Drag handle --}}
                <div class="flex shrink-0 justify-center pt-3 pb-1">
                    <div class="h-1.5 w-10 rounded-full bg-slate-300 dark:bg-slate-700"></div>
                </div>
                @include('pages.tenant.pos.partials._cart-resto', ['orderTypes' => $restoOrderTypes, 'isSheet' => true])
            </div>
        </div>
    </div>

    <div x-show="currentTab === 'queue'" class="flex h-full min-h-0 flex-col overflow-hidden" x-transition.opacity.duration.150ms>
        <div class="flex min-h-0 min-w-0 flex-1 flex-col overflow-hidden relative">
            @island(name: 'queue')
            <div x-data="{ activeFilter: 'all', searchQuery: '' }" class="h-full flex flex-col min-h-0 w-full">
                @include('pages.tenant.pos.partials._queue-header-resto')
                
                <div class="flex flex-1 min-h-0 overflow-hidden gap-5 lg:flex-row">
                    <div class="flex-1 overflow-y-auto min-h-0 relative pr-1">
                        @include('pages.tenant.pos.partials._queue-resto')
                    </div>
                    
                    {{-- Desktop queue detail (sidebar) --}}
                    <div wire:ignore class="hidden lg:block shrink-0 overflow-hidden transition-all duration-300 ease-in-out"
                         :class="isDesktopQueueDetailOpen && selectedQueueOrder ? 'w-[390px] xl:w-[430px] opacity-100 translate-x-0' : 'w-0 opacity-0 translate-x-8'">
                        <div class="min-h-0 h-full w-[390px] xl:w-[430px] cart-mobile-wrapper pb-1">
                            @include('pages.tenant.pos.partials._queue-detail-resto', ['isSheet' => false])
                        </div>
                    </div>
                </div>

                @include('pages.tenant.pos.partials._modal-merge-resto')
            </div>
            @endisland
        </div>

        {{-- Mobile queue detail bottom sheet --}}
        <div x-show="isMobileQueueDetailOpen" x-cloak class="fixed inset-0 z-[1029]" :class="{'lg:hidden': isDesktopQueueDetailOpen}"
             x-transition.opacity>
            <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" @click="isMobileQueueDetailOpen = false"></div>

            <div
                class="absolute inset-x-0 bottom-0 z-[1030] flex max-h-[85dvh] flex-col rounded-t-[2rem] bg-white shadow-2xl dark:bg-slate-900 lg:bottom-1/2 lg:translate-y-1/2 lg:w-[450px] lg:mx-auto lg:rounded-[2rem]"
                x-show="isMobileQueueDetailOpen"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="translate-y-full"
                x-transition:enter-end="translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="translate-y-0"
                x-transition:leave-end="translate-y-full">
                {{-- Drag handle --}}
                <div class="flex shrink-0 justify-center pt-3 pb-1">
                    <div class="h-1.5 w-10 rounded-full bg-slate-300 dark:bg-slate-700"></div>
                </div>
                @include('pages.tenant.pos.partials._queue-detail-resto', ['isSheet' => true])
            </div>
        </div>
    </div>

    {{-- Floating Cart Button for Mobile (Safe Template Destructive DOM Toggle) --}}
    <template x-if="currentTab === 'cashier' && !isMobileCartOpen && cart.length > 0">
        <button
            class="floating-cart-btn fixed bottom-5 left-1/2 z-[1030] flex w-[90%] max-w-[400px] -translate-x-1/2 items-center justify-between rounded-2xl bg-emerald-800 p-4 text-sm font-black text-white shadow-xl transition-all"
            :class="{'lg:hidden': isDesktopCartOpen}"
            @click="isMobileCartOpen = true">
            <span><i class="ph-bold ph-shopping-cart me-2"></i>Lihat Keranjang (<span
                    x-text="cart.length"></span>)</span>
            <span x-text="'Rp ' + formatRupiah(subTotal)"></span>
        </button>
    </template>

    {{-- Shared Modals --}}
    @include('pages.tenant.pos.partials._modal-payment')
    @include('pages.tenant.pos.partials._modal-success')
    {{-- @include('pages.tenant.pos.partials._pos-tour-guide', ['mode' => 'resto']) --}}

    @include('pages.tenant.pos.partials._modal-option')
    @include('pages.tenant.order.⚡order-list._modal-split-bill')

    {{-- Cancel Modal Component --}}
    <div>
        <x-tenant.order.cancel-modal/>
    </div>

    @script
    <script>
        Alpine.data('restoPos', (config) => ({

            cart: [],
            isMobileCartOpen: false,
            isDesktopCartOpen: true,
            isMobileQueueDetailOpen: false,
            isDesktopQueueDetailOpen: true,
            selectedQueueOrder: null,

            currentTab: config.currentTab,

            isPaymentModalOpen: false,
            isSuccessModalOpen: false,
            isOptionModalOpen: false,
            showSplitModalState: false,
            isMergeModalOpen: false,

            optionProduct: null,
            optionSelected: [],
            extrasSelected: [],
            optionQty: 1,

            customerName: config.customerName,
            tableNumber: config.tableNumber,
            orderType: config.orderType,
            isEditingOrder: config.isEditingOrder,
            editInvoiceCode: config.editInvoiceCode,
            paymentMethod: 'cash',
            amountPaid: '',
            payDiscount: 0,
            isSubmitting: false,
            // Duitku — state untuk opsi digital payment di payment modal
            duitkuMethod: null,           // Kode metode Duitku: 'QRIS', 'BV', 'I1', dll
            duitkuCustomerEmail: '',      // Email customer wajib untuk Duitku
            duitkuPaymentMethods: [],     // Daftar metode pembayaran Duitku dinamis

            async fetchDuitkuMethods() {
                if (!config.duitkuEnabled) return;
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

            taxRate: config.taxRate,
            serviceChargeRate: config.serviceChargeRate,
            isTaxActive: config.isTaxActive,
            isServiceActive: config.isServiceActive,

            init() {
                this.$watch('cart', () => this.validateStock(), {deep: true});
            },

            openQueueDetail(order) {
                this.selectedQueueOrder = order;
                if (window.innerWidth < 1024) {
                    this.isMobileQueueDetailOpen = true;
                } else {
                    this.isDesktopQueueDetailOpen = true;
                }
            },

            splittingOrder: null,
            splitItems: [],
            get splitTotalItems() {
                return this.splitItems.reduce((acc, curr) => acc + curr.qtyToSplit, 0);
            },
            openSplitModal(order) {
                this.splittingOrder = order;
                this.splitItems = order.items.map(i => ({
                    id: i.id,
                    name: i.product_name,
                    variant_name: i.variant_name,
                    price: parseFloat(i.price),
                    maxQty: parseInt(i.quantity),
                    qtyToSplit: 0
                }));
                this.showSplitModalState = true;
            },
            submitSplitOrder() {
                if (this.splitTotalItems === 0) {
                    showIslandToast('Pilih minimal 1 item untuk dipisah.', 'warning');
                    return;
                }
                const totalOriginalItems = this.splitItems.reduce((acc, curr) => acc + curr.maxQty, 0);
                if (this.splitTotalItems === totalOriginalItems) {
                    showIslandToast('Anda memilih semua item. Gunakan Bayar biasa saja.', 'warning');
                    return;
                }

                const dataToSend = this.splitItems.filter(i => i.qtyToSplit > 0).map(i => ({
                    id: i.id,
                    qty: i.qtyToSplit
                }));

                this.$wire.splitOrder(this.splittingOrder.id, dataToSend);
            },

            mergeTargetId: null,
            mergeTargetInvoice: '',
            mergeSourceId: '',
            openMergeModal(order) {
                this.mergeTargetId = order.id;
                this.mergeTargetInvoice = order.invoice_code;
                this.mergeSourceId = '';
                this.isMergeModalOpen = true;
            },
            submitMergeOrder() {
                if (!this.mergeSourceId) {
                    showIslandToast('Pilih pesanan yang akan digabungkan.', 'warning');
                    return;
                }
                this.$wire.mergeOrder(this.mergeSourceId, this.mergeTargetId);
                this.isMergeModalOpen = false;
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
                let t = this.payingOrder ? (parseFloat(this.payingOrder.total_price) || parseFloat(this.payingOrder.subtotal)) : this.subTotalWithCharges;
                let p = this.payingOrder ? (parseFloat(this.payingOrder.amount_paid) || 0) : 0;
                let d = this.payDiscount || 0;
                return Math.max(0, t - p - d);
            },
            get getChange() {
                return Math.max(0, (parseFloat(this.amountPaid) || 0) - this.payTotal);
            },

            handleProductClick(product, variantId = null) {
                if (product.stock <= 0) {
                    showIslandToast('Stok habis!', 'warning');
                    return;
                }
                if (!product.variants || product.variants.length === 0) {
                    showIslandToast('Produk ini belum memiliki varian harga yang valid.', 'danger');
                    return;
                }
                if (variantId) {
                    let variant = product.variants.find(v => v.id === variantId);
                    if (variant) {
                        this.addToCart(product, variant);
                        return;
                    }
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
                this.isOptionModalOpen = true;
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
                        basePrice = baseVariant ? parseFloat(baseVariant.active_discount_price || baseVariant.price) : 0;
                    } else {
                        const variant = this.optionProduct.variants.find(v => v.name === this.optionSelected[0]);
                        basePrice = variant ? parseFloat(variant.active_discount_price || variant.price) : 0;
                    }
                } else {
                    basePrice = parseFloat(this.optionProduct.variants[0]?.active_discount_price || this.optionProduct.variants[0]?.price) || 0;
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

                const basePrice = parseFloat(variant.active_discount_price || variant.price) || 0;
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
                        note: '',
                        image_url: this.optionProduct.image_url || null
                    });
                }

                this.isOptionModalOpen = false;
                setTimeout(() => this.optionProduct = null, 300);
            },

            addToCart(product, variant, qty = 1) {
                if (!variant) {
                    showIslandToast('Varian produk tidak ditemukan.', 'danger');
                    return;
                }
                let finalPrice = parseFloat(variant.active_discount_price || variant.price) || 0;
                let existing = this.cart.find(i => i.variant_id === variant.id);
                if (existing) {
                    if (existing.quantity + qty <= variant.stock) {
                        existing.quantity += qty;
                        existing.subtotal = existing.quantity * finalPrice;
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
                        price: finalPrice, quantity: qty, subtotal: finalPrice * qty,
                        stock: variant.stock, note: '',
                        image_url: product.image_url || null
                    });
                }
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
                    const result = await this.$wire.createOrder(this.cart, this.customerName, this.tableNumber, this.orderType, this.isTaxActive, this.isServiceActive);
                    if (result && result.success) {
                        showIslandToast(this.isEditingOrder ? `Tambahan disimpan ke ${result.invoice_code}!` : `Pesanan ${result.invoice_code} berhasil dibuat!`, 'success');
                        this.$wire.$island('queue').$refresh();
                        this.clearCart();
                        this.customerName = '';
                        this.tableNumber = '';
                        Livewire.dispatch('stock-updated');

                        if (this.isEditingOrder) {
                            setTimeout(() => {
                                this.isEditingOrder = false;
                                this.editInvoiceCode = null;
                                this.$wire.cancelEditOrder();
                                this.currentTab = 'queue';
                            }, 500);
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
                this.isPaymentModalOpen = true;
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
                this.isPaymentModalOpen = true;
                this.fetchDuitkuMethods();
            },

            async submitPayment() {
                if (this.paymentMethod === 'cash' && !this.amountPaid) {
                    showIslandToast('Masukkan nominal pembayaran untuk Cash.', 'warning');
                    return;
                }
                if (this.paymentMethod === 'cash' && parseFloat(this.amountPaid) < this.payTotal) {
                    showIslandToast('Nominal pembayaran kurang dari total tagihan.', 'warning');
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
                                result = await this.$wire.generateDuitkuPayment(
                                    this.payingOrder.id, this.duitkuMethod, custEmail
                                );
                                if (result && result.success && result.payment_url) {
                                    this.isPaymentModalOpen = false;
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
                                result = await this.$wire.generateMidtransPayment(
                                    this.payingOrder.id, custEmail
                                );
                                if (result && result.success && result.snap_token) {
                                    this.isPaymentModalOpen = false;
                                    window.snap.pay(result.snap_token, {
                                        onSuccess: () => {
                                            showIslandToast(`Pembayaran berhasil!`, 'success');
                                            this.payingOrder = null;
                                        },
                                        onPending: () => {
                                            showIslandToast(`Menunggu pembayaran...`, 'warning');
                                        },
                                        onError: () => {
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
                            this.isPaymentModalOpen = false;
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
                            this.isPaymentModalOpen = false;
                            window.snap.pay(data.data.snap_token, {
                                onSuccess: () => {
                                    showIslandToast(`Pembayaran berhasil!`, 'success');
                                    if (isDirect) {
                                        this.clearCart();
                                        this.customerName = '';
                                        this.tableNumber = '';
                                    }
                                },
                                onPending: () => {
                                    showIslandToast(`Menunggu pembayaran...`, 'warning');
                                    if (isDirect) {
                                        this.clearCart();
                                        this.customerName = '';
                                        this.tableNumber = '';
                                    }
                                },
                                onError: () => {
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
                        result = await this.$wire.processPayment(
                            this.payingOrder.id, this.paymentMethod, this.payDiscount || 0, this.amountPaid
                        );
                    } else {
                        result = await this.$wire.processDirectCheckout(
                            this.cart, this.customerName, this.tableNumber, this.orderType, this.paymentMethod, this.payDiscount || 0, this.amountPaid, this.isTaxActive, this.isServiceActive
                        );
                    }

                    if (result && result.success) {
                        this.lastOrder = result;
                        this.isPaymentModalOpen = false;
                        this.payingOrder = null;
                        this.$wire.$island('queue').$refresh();
                        if (isDirect) {
                            this.clearCart();
                            this.customerName = '';
                            this.tableNumber = '';
                        }
                        Livewire.dispatch('stock-updated');
                        setTimeout(() => this.isSuccessModalOpen = true, 300);
                    } else if (result && result.error) {
                        this.isPaymentModalOpen = false;
                        showIslandToast(result.error, 'danger');
                    }
                } catch (e) {
                    this.isPaymentModalOpen = false;
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
                    if (this.currentTab === 'queue') {
                        this.$wire.$island('queue').$refresh();
                    }
                    this.$wire.changeTab(this.currentTab);
                    return;
                }

                if (e.key === 'Enter' && this.isPaymentModalOpen) {
                    e.preventDefault();
                    this.submitPayment();
                    return;
                }

                if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
                    // return;
                }
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
                    this.$wire.updateCustomerPhone(this.lastOrder.invoice_code, this.lastOrder.customer_phone);
                    let phone = this.formatPhoneForWA(this.lastOrder.customer_phone);
                    let url = `${window.location.origin}/receipt/${this.lastOrder.invoice_code}`;
                    let msg = `Halo Kak *${this.lastOrder.customer_name}*,\n\nTerima kasih!\nStruk: ${url}\nTotal: Rp ${this.formatRupiah(this.lastOrder.total_price)}`;
                    window.open(`https://wa.me/${phone}?text=${encodeURIComponent(msg)}`, '_blank');
                    this.closeSuccessModal();
                }
            },
            closeSuccessModal() {
                this.isSuccessModalOpen = false;
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

</div>
