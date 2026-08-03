<div
    class="pos-shell relative m-2 h-full min-h-0 max-lg:h-[calc(100dvh-4rem)] overflow-hidden rounded-[2rem] bg-[#F5F2EA] px-0 pb-5 pt-3 text-slate-900 dark:bg-slate-950 dark:text-slate-100 lg:h-[90dvh] lg:px-4 lg:pb-6 lg:pt-4"
    x-data='restoPos({
        currentTab: $wire.entangle("activeTab").live,
        customerName: window.posInitialData?.customerName || "",
        tableNumber: window.posInitialData?.tableNumber || "",
        orderType: window.posInitialData?.orderType || "dinein",
        isEditingOrder: window.posInitialData?.isEditingOrder || false,
        editInvoiceCode: window.posInitialData?.editInvoiceCode || null,
        isShiftLocked: window.posInitialData?.isShiftLocked ?? false,
        shiftActive: window.posInitialData?.shiftActive ?? false,
        isAppFeePassed: @json($isAppFeePassed),
        appFeeAmount: @json($appFeeAmount),
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
    @open-void-item-modal.window="voidItemId = $event.detail.itemId; isVoidItemModalOpen = true"
    @start-editing-order.window="isEditingOrder = true; editInvoiceCode = $event.detail.invoice_code; customerName = $event.detail.customer; tableNumber = $event.detail.table; orderType = $event.detail.type"
    @pos-prepare-close-shift.window="$wire.prepareCloseShift()"
    x-cloak>

    <script>
        window.posInitialData = {
            customerName: @json($existingOrder ? $existingOrder->customer_name : ""),
            tableNumber: @json($existingOrder ? ($existingOrder->table_number ?? $existingOrder->notes) : ""),
            orderType: @json($existingOrder ? $existingOrder->order_type : ($restoOrderTypes[0]["id"] ?? "dinein")),
            isEditingOrder: @json((bool)$existingOrder),
            editInvoiceCode: @json($existingOrder ? $existingOrder->invoice_code : null),
            isShiftLocked: @json((bool)($isShiftActive && !$activeShift)),
            shiftActive: @json((bool)($isShiftActive && $activeShift))
        };
    </script>

    <template x-if="isShiftLocked">
        {{-- System Lock: shift wajib dibuka sebelum berjualan --}}
        <div class="flex h-full w-full flex-col items-center justify-center gap-6 px-6 text-center">
            <div class="flex h-20 w-20 items-center justify-center rounded-3xl bg-white shadow-sm dark:bg-slate-800">
                <i class="ph ph-lock-key text-4xl text-slate-400"></i>
            </div>
            <div class="space-y-2">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">Shift Belum Dibuka</h2>
                <p class="mx-auto max-w-sm text-sm text-slate-500 dark:text-slate-400">
                    Toko kamu menggunakan sistem shift kasir. Buka shift terlebih dahulu sebelum mulai menerima pembayaran.
                </p>
            </div>
            <button type="button"
                    class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:bg-emerald-500 dark:text-slate-950"
                    @click="window.dispatchEvent(new CustomEvent('open-open-shift-modal'))">
                <i class="ph ph-door-open"></i> Buka Shift Kasir
            </button>
        </div>
    </template>

    <div x-show="!isShiftLocked && currentTab === 'cashier'"
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
            <div x-show="isMobileCartOpen" x-cloak class="fixed inset-0 z-[1029]" :class="{'lg:hidden': isDesktopCartOpen}">
                <div class="absolute inset-0 bg-slate-900/45"
                     x-show="isMobileCartOpen"
                     x-transition:enter="transition-opacity ease-out duration-150"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     @click="isMobileCartOpen = false"></div>

                <div
                    class="absolute inset-x-0 bottom-0 z-[1030] flex max-h-[85dvh] flex-col rounded-t-[2rem] bg-white shadow-xl dark:bg-slate-900 lg:bottom-1/2 lg:translate-y-1/2 lg:w-[768px] lg:mx-auto lg:rounded-[2rem]"
                    x-show="isMobileCartOpen"
                    x-transition:enter="transition-transform ease-out duration-200"
                    x-transition:enter-start="translate-y-full"
                    x-transition:enter-end="translate-y-0">

                    {{-- Drag handle --}}
                    <div class="flex shrink-0 justify-center pt-3 pb-1">
                        <div class="h-1.5 w-10 rounded-full bg-slate-300 dark:bg-slate-700"></div>
                    </div>
                    @include('pages.tenant.pos.partials._cart-resto', ['orderTypes' => $restoOrderTypes, 'isSheet' => true])
                </div>
            </div>
        </div>

        <div x-show="!isShiftLocked && currentTab === 'queue'" class="flex h-full min-h-0 flex-col overflow-hidden"
             x-transition.opacity.duration.150ms>
            <div class="flex min-h-0 min-w-0 flex-1 flex-col overflow-hidden relative">
                @island(name: 'queue')
                <div class="h-full flex flex-col min-h-0 w-full">
                    @include('pages.tenant.pos.partials._queue-header-resto')

                    <div class="flex flex-1 min-h-0 overflow-hidden gap-5 lg:flex-row">
                        <div class="flex-1 overflow-y-auto min-h-0 relative pr-1">
                            @include('pages.tenant.pos.partials._queue-resto')
                        </div>

                        {{-- Desktop queue detail (sidebar) --}}
                        <div wire:ignore
                             class="hidden lg:block shrink-0 overflow-hidden transition-all duration-300 ease-in-out"
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
            <div x-show="isMobileQueueDetailOpen" x-cloak class="fixed inset-0 z-[1029]"
                 :class="{'lg:hidden': isDesktopQueueDetailOpen}">
                <div class="absolute inset-0 bg-slate-900/45"
                     x-show="isMobileQueueDetailOpen"
                     x-transition:enter="transition-opacity ease-out duration-150"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     @click="isMobileQueueDetailOpen = false"></div>

                <div
                    class="absolute inset-x-0 bottom-0 z-[1030] flex max-h-[85dvh] flex-col rounded-t-[2rem] bg-white shadow-xl dark:bg-slate-900 lg:bottom-1/2 lg:translate-y-1/2 lg:w-[450px] lg:mx-auto lg:rounded-[2rem]"
                    x-show="isMobileQueueDetailOpen"
                    x-transition:enter="transition-transform ease-out duration-200"
                    x-transition:enter-start="translate-y-full"
                    x-transition:enter-end="translate-y-0">

                    {{-- Drag handle --}}
                    <div class="flex shrink-0 justify-center pt-3 pb-1">
                        <div class="h-1.5 w-10 rounded-full bg-slate-300 dark:bg-slate-700"></div>
                    </div>
                    @include('pages.tenant.pos.partials._queue-detail-resto', ['isSheet' => true])
                </div>
            </div>
        </div>

        {{-- Floating Cart Button for Mobile (Safe Template Destructive DOM Toggle) --}}
        <template x-if="!isShiftLocked && currentTab === 'cashier' && !isMobileCartOpen && cart.length > 0">
            <button
                class="floating-cart-btn fixed bottom-5 left-1/2 z-[1030] flex w-[90%] max-w-[400px] -translate-x-1/2 items-center justify-between rounded-2xl bg-emerald-800 p-4 text-sm font-black text-white shadow-xl transition-colors hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:bg-emerald-500 dark:text-slate-950 dark:hover:bg-emerald-400"
                :class="{'lg:hidden': isDesktopCartOpen}"
                @click="isMobileCartOpen = true">
                <span><i class="ph-bold ph-shopping-cart me-2"></i>Lihat Keranjang (<span
                        x-text="cart.length"></span>)</span>
                <span x-text="'Rp ' + formatRupiah(subTotal)"></span>
            </button>
        </template>

    {{-- Shift Indicator Pill --}}
    {{-- Moved to navbar. Dispatch event so navbar Alpine can react. --}}
    @if($isShiftActive && $activeShift)
        <span x-init="window.dispatchEvent(new CustomEvent('shift-active', { detail: { active: true } }))" class="hidden"></span>
    @endif

    {{-- Shared Modals --}}
    <div wire:ignore>
        @include('pages.tenant.pos.partials._modal-open-shift')
        @include('pages.tenant.pos.partials._modal-shift-expense')
    </div>
    @include('pages.tenant.pos.partials._modal-close-shift')
    @include('pages.tenant.pos.partials._modal-shift-summary')
    @include('pages.tenant.pos.partials._modal-payment')
    @include('pages.tenant.pos.partials._modal-success')
    {{-- @include('pages.tenant.pos.partials._pos-tour-guide', ['mode' => 'resto']) --}}

    @include('pages.tenant.pos.partials._modal-option')
    @include('pages.tenant.order.⚡order-list._modal-split-bill')

    {{-- Cancel Modal Component --}}
    <div wire:ignore>
        @include('pages.tenant.pos.partials._cancel-modal')
    </div>

    @include('pages.tenant.pos.partials._modal-void-item')

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
            isVoidItemModalOpen: false,
            voidItemId: null,
            isShiftLocked: config.isShiftLocked,
            shiftActive: config.shiftActive,

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

            isAppFeePassed: config.isAppFeePassed,
            appFeeAmount: config.appFeeAmount,
            taxRate: config.taxRate,
            serviceChargeRate: config.serviceChargeRate,
            isTaxActive: config.isTaxActive,
            isServiceActive: config.isServiceActive,

            getStatusColor(status) {
                const map = {
                    'waiting': 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400',
                    'processing': 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400',
                    'ready': 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400',
                    'completed': 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400'
                };
                return map[status] || 'bg-gray-100 text-gray-700 dark:bg-gray-500/20 dark:text-gray-400';
            },
            getStatusIcon(status) {
                const map = {
                    'waiting': 'ph-clock',
                    'processing': 'ph-cooking-pot',
                    'ready': 'ph-bell-ringing',
                    'completed': 'ph-check-circle'
                };
                return map[status] || 'ph-question';
            },
            getStatusLabel(status) {
                const map = {
                    'waiting': 'Pesanan Masuk',
                    'processing': 'Diproses Dapur',
                    'ready': 'Siap Disajikan',
                    'completed': 'Selesai'
                };
                return map[status] || 'Unknown';
            },

            init() {
                this.$watch('cart', () => this.validateStock(), {deep: true});
                window.addEventListener('shift-active', () => {
                    this.isShiftLocked = false;
                    this.shiftActive = true;
                });
                window.addEventListener('shift-closed', () => {
                    this.shiftActive = false;
                    this.isShiftLocked = true;
                });
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
                    price: parseFloat(i.price || (i.subtotal / i.quantity) || 0),
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
            get globalDiscount() {
                return parseFloat(this.payDiscount) || 0;
            },
            get subtotalAfterDiscount() {
                return Math.max(0, this.subTotal - this.globalDiscount);
            },
            get serviceChargeAmount() {
                if (!this.isServiceActive) return 0;
                return Math.round((parseFloat(this.serviceChargeRate) / 100) * this.subtotalAfterDiscount);
            },
            get applicationFeeAmount() {
                return this.isAppFeePassed ? this.appFeeAmount : 0;
            },
            get taxAmount() {
                if (!this.isTaxActive) return 0;
                // Match PHP backend: Taxable Amount (DPP) = SubtotalAfterDiscount + ServiceCharge + AppFee
                let taxableAmount = this.subtotalAfterDiscount + this.serviceChargeAmount + this.applicationFeeAmount;
                return Math.round((parseFloat(this.taxRate) / 100) * taxableAmount);
            },
            get subTotalWithCharges() {
                return this.subtotalAfterDiscount + this.serviceChargeAmount + this.taxAmount + this.applicationFeeAmount;
            },
            get payTotal() {
                let t = this.payingOrder ? (parseFloat(this.payingOrder.total_price) || parseFloat(this.payingOrder.subtotal)) : this.subTotalWithCharges;
                let p = this.payingOrder ? (parseFloat(this.payingOrder.amount_paid) || 0) : 0;
                return Math.max(0, t - p);
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
                const idx = this.extrasSelected.indexOf(extra.id);
                if (idx > -1) {
                    this.extrasSelected.splice(idx, 1);
                } else {
                    this.extrasSelected.push(extra.id);
                }
            },

            isExtraSelected(id) {
                return this.extrasSelected.includes(id);
            },

            get extrasTotal() {
                if (!this.optionProduct?.extras?.length) return 0;
                return this.optionProduct.extras
                    .filter(e => this.extrasSelected.includes(e.id))
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
                const extrasNames = this.optionProduct.extras ? this.optionProduct.extras.filter(e => this.extrasSelected.includes(e.id)).map(e => e.name) : [];
                const extrasLabel = extrasNames.length ? extrasNames.join(', ') : '';
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
                        image_url: this.optionProduct.image_url || null,
                        extra_ids: [...this.extrasSelected]
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
                        image_url: product.image_url || null,
                        extra_ids: []
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

                    const custEmail = this.duitkuCustomerEmail ? this.duitkuCustomerEmail.trim() : '';

                    if (this.payingOrder) {
                        result = await this.$wire.processPayment(
                            this.payingOrder.id,
                            this.paymentMethod,
                            this.payDiscount || 0,
                            this.amountPaid,
                            this.duitkuMethod,
                            custEmail
                        );
                    } else {
                        result = await this.$wire.processDirectCheckout(
                            this.cart,
                            this.customerName,
                            this.tableNumber,
                            this.orderType,
                            this.paymentMethod,
                            this.payDiscount || 0,
                            this.amountPaid,
                            this.isTaxActive,
                            this.isServiceActive,
                            this.duitkuMethod,
                            custEmail
                        );
                    }

                    if (result && result.success) {
                        this.isPaymentModalOpen = false;
                        this.$wire.$island('queue').$refresh();

                        if (result.payment_url) {
                            window.open(result.payment_url, '_blank');
                            showIslandToast(`Link Duitku dibuka! Invoice: ${result.invoice_code || this.payingOrder?.invoice_code || ''}`, 'success');
                            this.payingOrder = null;
                            this.duitkuMethod = null;
                            this.duitkuCustomerEmail = '';
                            if (isDirect) {
                                this.clearCart();
                                this.customerName = '';
                                this.tableNumber = '';
                            }
                            Livewire.dispatch('stock-updated');
                            return;
                        }

                        if (result.snap_token) {
                            window.snap.pay(result.snap_token, {
                                onSuccess: () => {
                                    showIslandToast(`Pembayaran berhasil!`, 'success');
                                    this.payingOrder = null;
                                    if (isDirect) {
                                        this.clearCart();
                                        this.customerName = '';
                                        this.tableNumber = '';
                                    }
                                    Livewire.dispatch('stock-updated');
                                },
                                onPending: () => {
                                    showIslandToast(`Menunggu pembayaran...`, 'warning');
                                    this.payingOrder = null;
                                    if (isDirect) {
                                        this.clearCart();
                                        this.customerName = '';
                                        this.tableNumber = '';
                                    }
                                    Livewire.dispatch('stock-updated');
                                },
                                onError: () => {
                                    showIslandToast(`Pembayaran gagal.`, 'danger');
                                },
                                onClose: () => {
                                    showIslandToast(`Popup ditutup sebelum pembayaran selesai.`, 'warning');
                                }
                            });
                            this.duitkuMethod = null;
                            this.duitkuCustomerEmail = '';
                            return;
                        }

                        this.lastOrder = result;
                        this.payingOrder = null;
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
