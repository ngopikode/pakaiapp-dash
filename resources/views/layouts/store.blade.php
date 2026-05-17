@php
    // ====== SINGLE DB QUERY for entire layout ======
    $storeSetting = \App\Models\StoreSetting::first();
    $waNumber = preg_replace('/\D/', '', $storeSetting?->whatsapp_number ?: '6281234567890');
    if (str_starts_with($waNumber, '0')) $waNumber = '62' . substr($waNumber, 1);
    $storeName = $storeSetting?->name ?: 'EzMenu';
    $storeType = $storeSetting?->store_type ?: 'resto';
    $orderTypes = [];
    if ($storeType === 'resto') {
        if ($storeSetting?->is_dinein_active)   $orderTypes[] = ['id' => 'dinein', 'label' => 'Makan Sini'];
        if ($storeSetting?->is_takeaway_active) $orderTypes[] = ['id' => 'takeaway', 'label' => 'Bungkus'];
        if ($storeSetting?->is_delivery_active) $orderTypes[] = ['id' => 'delivery', 'label' => 'Diantar'];
    } else {
        if ($storeSetting?->is_takeaway_active) $orderTypes[] = ['id' => 'takeaway', 'label' => 'Ambil Sendiri'];
        if ($storeSetting?->is_delivery_active) $orderTypes[] = ['id' => 'delivery', 'label' => 'Diantar'];
    }
    if (empty($orderTypes)) {
        $orderTypes[] = ['id' => 'takeaway', 'label' => 'Takeaway'];
    }
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $storeSetting?->seo_title ?: ($storeName . ' | Menu Digital') }}</title>
    <meta name="description" content="{{ $storeSetting?->seo_description ?: 'Platform menu digital untuk semua jenis usaha. Buat menu online dengan mudah, cepat, dan modern.' }}"/>
    <meta name="keywords" content="{{ $storeSetting?->seo_keywords ?: 'menu digital, QR menu, pesan online' }}"/>
    <meta name="theme-color" content="#18181b">

    <meta property="og:title" content="{{ $storeSetting?->og_title ?: ($storeName . ' | Menu Digital') }}"/>
    <meta property="og:description" content="{{ $storeSetting?->og_description ?: 'Buat menu online untuk usaha Anda dengan mudah, cepat, dan modern.' }}"/>
    @if($storeSetting?->og_image)
        <meta property="og:image" content="{{ Storage::url($storeSetting->og_image) }}"/>
    @else
        <meta property="og:image" content="/logo.png"/>
    @endif
    <meta property="og:type" content="website"/>

    @vite(['resources/css/store.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body>
<div
    class="bg-zinc-50 min-h-screen text-zinc-900 pb-28 font-sans antialiased relative selection:bg-[var(--primary-color)] selection:text-black"
    x-data="{
        /* ===== GLOBAL CART STATE ===== */
        toast: { show: false, message: '' },
        qrOpen: false,
        cart: JSON.parse(localStorage.getItem('ezmenu-cart') || '[]'),

        saveCart() {
            localStorage.setItem('ezmenu-cart', JSON.stringify(this.cart));
        },

        addToCart(item, selectedVariants = '', quantity = 1) {
            const cartName = selectedVariants ? `${item.name} (${selectedVariants})` : item.name;
            const existing = this.cart.find(i => i.cartName === cartName);
            if (existing) {
                existing.qty += quantity;
            } else {
                this.cart.push({ ...item, cartName, qty: quantity });
            }
            this.saveCart();
            this.showToast('Berhasil ditambahkan ke keranjang!');
        },

        updateQty(cartName, delta) {
            const existing = this.cart.find(i => i.cartName === cartName);
            if (!existing) return;
            existing.qty += delta;
            if (existing.qty <= 0) {
                this.cart = this.cart.filter(i => i.cartName !== cartName);
            }
            this.saveCart();
        },

        get totalQty() {
            return this.cart.reduce((acc, item) => acc + item.qty, 0);
        },

        get totalCart() {
            return this.cart.reduce((acc, item) => acc + (item.price * item.qty), 0);
        },

        formatPrice(price) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(price);
        },

        /* ===== TOAST ===== */
        showToast(msg, duration = 3000) {
            this.toast = { show: true, message: msg };
            clearTimeout(this._toastTimer);
            this._toastTimer = setTimeout(() => this.toast.show = false, duration);
        },

        /* ===== QR Modal ===== */
        get qrUrl() {
            return 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' + encodeURIComponent(window.location.href) + '&bgcolor=ffffff&color=000000&margin=10';
        },

        /* ===== PRODUCT DETAIL MODAL ===== */
        detailOpen: false,
        detailProduct: null,
        detailScrolled: false,

        get detailQtyInCart() {
            if (!this.detailProduct) return 0;
            const i = this.cart.find(x => x.cartName === this.detailProduct.name);
            return i ? i.qty : 0;
        },

        openDetail(product) {
            this.detailProduct = product;
            this.detailOpen = true;
            this.detailScrolled = false;
            document.body.style.overflow = 'hidden';
        },
        closeDetail() {
            this.detailOpen = false;
            setTimeout(() => {
                this.detailProduct = null;
                document.body.style.overflow = '';
            }, 300);
        },
        handleDetailScroll(e) {
            this.detailScrolled = e.target.scrollTop > window.innerHeight * 0.3;
        },

        /* ===== OPTION MODAL (Variants — Single or Multi) ===== */
        optionOpen: false,
        optionProduct: null,
        optionSelected: [],
        optionQty: 1,

        get isMulti() { return this.optionProduct?.selection_type === 'multiple'; },
        get maxSel()  { return this.optionProduct?.max_selections || 1; },

        openOption(product) {
            this.optionProduct = product;
            this.optionQty = 1;
            if (product.selection_type === 'multiple') {
                this.optionSelected = [];
            } else {
                // single: pre-select first variant
                this.optionSelected = (product.variants && product.variants.length > 0) ? [product.variants[0].name] : [];
            }
            this.optionOpen = true;
            document.body.style.overflow = 'hidden';
        },
        closeOption() {
            this.optionOpen = false;
            setTimeout(() => { this.optionProduct = null; document.body.style.overflow = ''; }, 300);
        },
        toggleOption(variantName) {
            if (this.isMulti) {
                const idx = this.optionSelected.indexOf(variantName);
                if (idx > -1) {
                    this.optionSelected.splice(idx, 1);
                } else {
                    if (this.optionSelected.length >= this.maxSel) {
                        this.showToast(`Maksimal pilih ${this.maxSel} varian!`);
                        return;
                    }
                    this.optionSelected.push(variantName);
                }
            } else {
                this.optionSelected = [variantName];
            }
        },
        isOptionSelected(name) {
            return this.optionSelected.includes(name);
        },
        get optionValid() { return this.optionSelected.length > 0; },
        get optionTotalPrice() {
            if (!this.optionProduct) return 0;
            if (this.isMulti) {
                // multi: base price * qty (flavors don't change price)
                return this.optionProduct.price * this.optionQty;
            } else {
                // single: use selected variant price
                const v = this.optionProduct.variants.find(v => v.name === this.optionSelected[0]);
                return (v ? v.price : this.optionProduct.price) * this.optionQty;
            }
        },
        confirmOption() {
            if (!this.optionValid || !this.optionProduct) return;
            const selectedLabel = this.optionSelected.join(', ');
            let price;
            if (this.isMulti) {
                price = this.optionProduct.price;
            } else {
                const v = this.optionProduct.variants.find(v => v.name === this.optionSelected[0]);
                price = v ? v.price : this.optionProduct.price;
            }
            const itemForCart = { ...this.optionProduct, price };
            this.addToCart(itemForCart, selectedLabel, this.optionQty);
            this.closeOption();
        },

        /* ===== CHECKOUT MODAL ===== */
        checkoutOpen: false,
        customerName: '',
        customerInfo: '',
        orderType: '{{ $orderTypes[0]['id'] }}',
        checkoutLoading: false,
        orderSuccess: null, // { invoiceCode, total }

        openCheckout() {
            this.orderSuccess = null;
            this.checkoutOpen = true;
            document.body.style.overflow = 'hidden';
        },
        closeCheckout() {
            this.checkoutOpen = false;
            setTimeout(() => {
                this.orderSuccess = null;
                document.body.style.overflow = '';
            }, 300);
        },
        async processOrder() {
            if (this.cart.length === 0) {
                this.showToast('Keranjang Anda kosong!');
                return;
            }
            if (!this.customerName.trim()) {
                this.showToast('Masukkan nama pemesan dulu ya!');
                return;
            }

            this.checkoutLoading = true;

            const payload = {
                customer_name: this.customerName.trim(),
                order_type: this.orderType,
                order_info: this.customerInfo.trim() || null,
                total_price: this.totalCart,
                items: this.cart.map(item => ({
                    product_id: item.id,
                    name: item.cartName,
                    quantity: item.qty,
                    price: parseFloat(item.price),
                })),
            };

            try {
                const res = await fetch('/api/orders', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '',
                    },
                    body: JSON.stringify(payload),
                });

                const data = await res.json();

                if (res.ok) {
                    this.orderSuccess = {
                        invoiceCode: data.data?.invoice_code || 'OK',
                        total: this.formatPrice(this.totalCart),
                    };

                    const waText = `Halo admin, pesanan baru nih!
*Invoice:* ${this.orderSuccess.invoiceCode}
*Nama:* ${this.customerName.trim()}
*Tipe:* ${this.orderType}
*Status Pembayaran:* Belum Dibayar ⏳
${this.customerInfo.trim() ? '*Catatan/Meja:* ' + this.customerInfo.trim() + '\n' : ''}
*Daftar Pesanan:*
${this.cart.map(i => `- ${i.qty}x ${i.cartName}`).join('\n')}

*Total Tagihan:* ${this.orderSuccess.total}`;
                    const waUrl = `https://wa.me/{{ $waNumber }}?text=${encodeURIComponent(waText)}`;
                    window.open(waUrl, '_blank');

                    this.cart = [];
                    this.saveCart();
                    this.customerName = '';
                    this.customerInfo = '';
                    this.showToast('Pesanan berhasil dikirim!');
                } else {
                    this.showToast(data.message || 'Gagal mengirim pesanan. Coba lagi.');
                }
            } catch (err) {
                console.error('Order error:', err);
                this.showToast('Koneksi bermasalah. Coba lagi ya.');
            } finally {
                this.checkoutLoading = false;
            }
        }
    }"
    @open-qr-modal.window="qrOpen = true"
    @show-toast.window="showToast($event.detail.message)"
    @open-product-detail.window="openDetail($event.detail.product)"
    @open-options-modal.window="openOption($event.detail.product)"
    @open-checkout-modal.window="openCheckout()"
    @keydown.escape.window="
        if (checkoutOpen) { closeCheckout(); }
        else if (optionOpen) { closeOption(); }
        else if (detailOpen) { closeDetail(); }
        else if (qrOpen) { qrOpen = false; }
    "
>
    <livewire:layouts.store.navbar/>

    {{ $slot }}

    {{-- ===== GLOBAL TOAST ===== --}}
    <div
        class="fixed top-4 left-4 right-4 z-[300] sm:left-1/2 sm:right-auto sm:-translate-x-1/2 sm:top-6 sm:w-auto sm:min-w-[280px] bg-zinc-900 text-white px-5 py-3.5 rounded-2xl sm:rounded-full shadow-2xl shadow-zinc-900/30 transition-all duration-500 ease-out flex items-center justify-center sm:justify-start gap-3 border border-white/5 backdrop-blur-xl pointer-events-none"
        :class="toast.show ? 'translate-y-0 opacity-100 scale-100' : '-translate-y-8 opacity-0 scale-95'"
    >
        <div class="bg-emerald-500 rounded-full p-1 text-white shrink-0 shadow-lg shadow-emerald-500/30">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
        </div>
        <span class="text-xs font-bold tracking-wide text-center sm:text-left" x-text="toast.message"></span>
    </div>

    {{-- ===== QR MODAL ===== --}}
    <div
        x-show="qrOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="qrOpen = false"
        class="fixed inset-0 bg-zinc-900/80 backdrop-blur-sm z-[200] flex items-center justify-center p-6"
        style="display:none"
    >
        <div @click.stop class="bg-white w-full max-w-xs rounded-2xl p-8 text-center relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-32 bg-[var(--primary-color)]/10 rounded-b-[50%] -translate-y-1/2"></div>
            <div class="relative z-10">
                <div class="bg-[var(--primary-color)] w-14 h-14 rounded-2xl rotate-3 flex items-center justify-center mx-auto mb-4 border-4 border-white shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-900"><rect width="5" height="5" x="3" y="3" rx="1"/><rect width="5" height="5" x="16" y="3" rx="1"/><rect width="5" height="5" x="3" y="16" rx="1"/><path d="M21 16h-3a2 2 0 0 0-2 2v3"/><path d="M21 21v.01"/><path d="M12 7v3a2 2 0 0 1-2 2H7"/><path d="M3 12h.01"/><path d="M12 3h.01"/><path d="M12 16v.01"/><path d="M16 12h1"/><path d="M21 12v.01"/><path d="M12 21v-1"/></svg>
                </div>
                <h2 class="text-xl font-black text-zinc-900 mb-1">SCAN MENU</h2>
                <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest mb-6">Buka di Ponsel Anda</p>
                <div class="bg-white p-2 rounded-xl border-2 border-dashed border-zinc-200 mb-6">
                    <img :src="qrUrl" alt="QR Code Menu" class="w-full aspect-square rounded-lg opacity-90" />
                </div>
                <button @click="qrOpen = false" class="w-full bg-zinc-900 text-white py-3.5 rounded-xl text-xs font-black uppercase tracking-wider hover:bg-zinc-800 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    {{-- ===== PRODUCT DETAIL OVERLAY (100% Client-Side) ===== --}}
    @include('pages.tenant.store.product-detail')

    {{-- ===== OPTION MODAL (100% Client-Side) ===== --}}
    @include('pages.tenant.store.option-modal')

    {{-- ===== CHECKOUT MODAL (100% Client-Side) ===== --}}
    @include('pages.tenant.store.checkout-modal', ['orderTypes' => $orderTypes])

</div>

@livewireScripts
</body>
</html>
