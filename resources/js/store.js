import '@phosphor-icons/web/bold';
import '@phosphor-icons/web/fill';
import '@phosphor-icons/web/regular';

/**
 * Store front-end logic (Alpine.js components).
 *
 * Server-side values are passed via data-* attributes on the root element:
 *   data-default-order-type  - e.g. "dinein" | "takeaway" | "delivery"
 *   data-wa-number           - WhatsApp number (already formatted, e.g. "6281234567890")
 *
 * Loading spinner (#app-loader) is fully Alpine-controlled via Livewire window
 * events (@livewire:initialized, @livewire:navigating, @livewire:navigated).
 * No extra JS needed — see _loader.blade.php.
 */
document.addEventListener('alpine:init', () => {
    // -------------------------------------------------------------------------
    // UI PREFERENCES STORE
    // Persisted in localStorage. Changing viewMode NEVER triggers a Livewire
    // round-trip — it's pure client-side state.
    // -------------------------------------------------------------------------
    Alpine.store('ui', {
        viewMode: localStorage.getItem('ezmenu-viewmode') || 'grid',

        setViewMode(mode) {
            this.viewMode = mode;
            localStorage.setItem('ezmenu-viewmode', mode);
        }
    });

    // -------------------------------------------------------------------------
    // STORE APP — global cart + modals state
    // -------------------------------------------------------------------------
    Alpine.data('storeApp', () => ({
        /* ===== THEME ===== */
        theme: localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'),

        toggleTheme() {
            this.theme = this.theme === 'dark' ? 'light' : 'dark';
            localStorage.setItem('theme', this.theme);
            if (this.theme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        },

        /* ===== CART ===== */
        toast: {show: false, message: ''},
        qrOpen: false,
        cart: JSON.parse(localStorage.getItem('ezmenu-cart') || '[]'),

        /* ===== ORDER HISTORY ===== */
        historyOpen: false,
        historyLoading: false,
        orderHistory: [],

        get historyCount() {
            return this.orderHistory.length;
        },

        async fetchHistoryFromServer() {
            try {
                const localHistory = JSON.parse(localStorage.getItem('pakaiapp_order_history') || '[]');
                const invoiceCodes = localHistory.map(h => h.invoiceCode);

                if (invoiceCodes.length === 0) {
                    this.orderHistory = [];
                    return;
                }

                this.historyLoading = true;

                const res = await fetch('/api/orders/history', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || ''
                    },
                    body: JSON.stringify({invoices: invoiceCodes})
                });

                const data = await res.json();
                if (data.success) {
                    this.orderHistory = data.data.map(order => ({
                        ...order,
                        total: this.formatPrice(order.totalRaw)
                    }));
                    this.saveHistory();
                }
            } catch (e) {
                console.error('[History] Gagal fetch history server', e);
                this.loadHistory();
            } finally {
                this.historyLoading = false;
            }
        },

        loadHistory() {
            try {
                const history = JSON.parse(localStorage.getItem('pakaiapp_order_history') || '[]');
                this.orderHistory = history.map(order => ({
                    ...order,
                    total: order.total || this.formatPrice(order.totalRaw)
                }));
            } catch (e) {
                console.error('[History] Gagal memuat riwayat pesanan', e);
                this.orderHistory = [];
            }
        },

        saveHistory() {
            try {
                localStorage.setItem('pakaiapp_order_history', JSON.stringify(this.orderHistory));
            } catch (e) {
                console.error('[History] Gagal menyimpan riwayat pesanan', e);
            }
        },

        addOrderToHistory(orderData) {
            const order = {
                invoiceCode: orderData.invoiceCode,
                date: new Date().toISOString(),
                total: this.formatPrice(orderData.totalRaw),
                totalRaw: orderData.totalRaw,
                orderType: orderData.orderType,
                paymentMethod: orderData.paymentMethod,
                paymentName: orderData.paymentName,
                status: orderData.status || 'pending',
                waUrl: orderData.waUrl || null,
                items: orderData.items.map(i => ({
                    name: i.cartName || i.name,
                    qty: i.qty,
                    price: i.price
                }))
            };
            this.orderHistory.unshift(order);
            if (this.orderHistory.length > 50) {
                this.orderHistory = this.orderHistory.slice(0, 50);
            }
            this.saveHistory();
        },

        clearHistory() {
            if (confirm('Apakah Anda yakin ingin menghapus semua riwayat pesanan?')) {
                this.orderHistory = [];
                localStorage.removeItem('pakaiapp_order_history');
                this.showToast('Riwayat pesanan berhasil dibersihkan.');
            }
        },

        saveCart() {
            localStorage.setItem('ezmenu-cart', JSON.stringify(this.cart));
        },

        addToCart(item, selectedVariants = '', quantity = 1, variantId = null) {
            const cartName = selectedVariants
                ? `${item.name} (${selectedVariants})`
                : item.name;
            const existing = this.cart.find((i) => i.cartName === cartName);
            if (existing) {
                existing.qty += quantity;
            } else {
                this.cart.push({...item, cartName, qty: quantity, variant_id: variantId});
            }
            this.saveCart();
            this.showToast('Berhasil ditambahkan ke keranjang!');
        },

        updateQty(cartName, delta) {
            const existing = this.cart.find((i) => i.cartName === cartName);
            if (!existing) return;
            existing.qty += delta;
            if (existing.qty <= 0) {
                this.cart = this.cart.filter((i) => i.cartName !== cartName);
            }
            this.saveCart();

            // Jika modal checkout sedang terbuka, recalculate (tidak perlu fetch duitku)
            if (this.checkoutOpen && this.midtransEnabled) {
                // midtrans recalculation if needed
            }
            if (this.checkoutOpen && this.duitkuEnabled) {
                this.fetchDuitkuMethods();
            }
        },

        get totalQty() {
            return this.cart.reduce((acc, item) => acc + item.qty, 0);
        },

        get totalCart() {
            return this.cart.reduce(
                (acc, item) => acc + item.price * item.qty,
                0
            );
        },

        formatPrice(price) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(price);
        },

        /* ===== TOAST ===== */
        showToast(msg, duration = 3000) {
            this.toast = {show: true, message: msg};
            clearTimeout(this._toastTimer);
            this._toastTimer = setTimeout(
                () => (this.toast.show = false),
                duration
            );
        },

        /* ===== QR MODAL ===== */
        get qrUrl() {
            return (
                'https://api.qrserver.com/v1/create-qr-code/?size=500x500&data=' +
                encodeURIComponent(window.location.href) +
                '&bgcolor=ffffff&color=000000&margin=10'
            );
        },

        async downloadQr() {
            try {
                // Fetch the image as a blob
                const response = await fetch(this.qrUrl);
                const blob = await response.blob();

                // Create an Image object from the blob
                const qrImage = new Image();
                const blobUrl = URL.createObjectURL(blob);

                await new Promise((resolve, reject) => {
                    qrImage.onload = resolve;
                    qrImage.onerror = reject;
                    qrImage.src = blobUrl;
                });

                // Create Canvas for the Poster
                const canvas = document.createElement('canvas');
                canvas.width = 1080;
                canvas.height = 1440; // 3:4 ratio, great for standees
                const ctx = canvas.getContext('2d');

                // Draw background (Deep dark premium)
                ctx.fillStyle = '#09090b'; // zinc-950
                ctx.fillRect(0, 0, canvas.width, canvas.height);

                // Radial gradient highlight behind QR
                const radial = ctx.createRadialGradient(canvas.width / 2, 720, 0, canvas.width / 2, 720, 800);
                radial.addColorStop(0, '#27272a'); // zinc-800
                radial.addColorStop(1, '#09090b'); // zinc-950
                ctx.fillStyle = radial;
                ctx.fillRect(0, 0, canvas.width, canvas.height);

                // Gold Top Border Accent
                const grad = ctx.createLinearGradient(0, 0, canvas.width, 0);
                grad.addColorStop(0, '#d97706'); // amber-600
                grad.addColorStop(0.5, '#fbbf24'); // amber-400
                grad.addColorStop(1, '#d97706');
                ctx.fillStyle = grad;
                ctx.fillRect(0, 0, canvas.width, 24);

                // Store Name
                const rawTitle = document.title || 'Menu';
                const storeName = rawTitle.split('|')[0].trim();

                // Subtitle
                ctx.fillStyle = '#fbbf24'; // amber-400
                ctx.font = '800 36px "Inter", system-ui, sans-serif';
                ctx.fillText('S C A N   U N T U K   P E S A N', canvas.width / 2, 320);

                // Draw white rounded box for QR Code
                const qrSize = 640;
                const qrX = (canvas.width - qrSize) / 2;
                const qrY = 440;
                const r = 48; // border radius
                const pad = 64; // padding

                ctx.fillStyle = '#ffffff';
                ctx.shadowColor = 'rgba(0, 0, 0, 0.6)';
                ctx.shadowBlur = 60;
                ctx.shadowOffsetY = 30;

                const boxX = qrX - pad;
                const boxY = qrY - pad;
                const boxW = qrSize + pad * 2;
                const boxH = qrSize + pad * 2;

                ctx.beginPath();
                ctx.moveTo(boxX + r, boxY);
                ctx.lineTo(boxX + boxW - r, boxY);
                ctx.arcTo(boxX + boxW, boxY, boxX + boxW, boxY + r, r);
                ctx.lineTo(boxX + boxW, boxY + boxH - r);
                ctx.arcTo(boxX + boxW, boxY + boxH, boxX + boxW - r, boxY + boxH, r);
                ctx.lineTo(boxX + r, boxY + boxH);
                ctx.arcTo(boxX, boxY + boxH, boxX, boxY + boxH - r, r);
                ctx.lineTo(boxX, boxY + r);
                ctx.arcTo(boxX, boxY, boxX + r, boxY, r);
                ctx.closePath();
                ctx.fill();

                // Reset shadow
                ctx.shadowColor = 'transparent';
                ctx.shadowBlur = 0;
                ctx.shadowOffsetY = 0;

                // Draw the actual QR Code Image
                ctx.drawImage(qrImage, qrX, qrY, qrSize, qrSize);

                // Draw Footer (Powered by)
                ctx.fillStyle = '#71717a'; // zinc-500
                ctx.font = '500 28px "Inter", system-ui, sans-serif';
                ctx.fillText('Platform pemesanan digital didukung oleh', canvas.width / 2, canvas.height - 120);

                ctx.fillStyle = '#fbbf24'; // amber-400
                ctx.font = '800 34px "Inter", system-ui, sans-serif';
                ctx.fillText('pakaiapp.online', canvas.width / 2, canvas.height - 70);

                // Export & Download
                const finalDataUrl = canvas.toDataURL('image/png');
                const a = document.createElement('a');
                a.href = finalDataUrl;
                const cleanName = storeName.toLowerCase().replace(/[^a-z0-9]/g, '-');
                a.download = `qr-standee-${cleanName}.png`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);

                URL.revokeObjectURL(blobUrl);
            } catch (e) {
                console.error('Failed to download QR code', e);
                // Fallback for CORS or fetch issues: open in new tab
                window.open(this.qrUrl, '_blank');
            }
        },

        /* ===== OPTION MODAL (Variants: Single or Multi-select) ===== */
        optionOpen: false,
        optionProduct: null,
        optionSelected: [],
        extrasSelected: [],
        optionQty: 1,

        get isMulti() {
            return this.optionProduct?.selection_type === 'multiple';
        },
        get maxSel() {
            return this.optionProduct?.max_selections || 1;
        },

        openOption(product) {
            this.optionProduct = product;
            this.optionQty = 1;
            this.extrasSelected = [];
            if (product.selection_type === 'multiple') {
                this.optionSelected = [];
            } else {
                // Single: pre-select first variant for better UX
                this.optionSelected =
                    product.variants && product.variants.length > 0 && product.has_variants
                        ? [product.variants[0].name]
                        : [];
            }
            this.optionOpen = true;
            document.body.style.overflow = 'hidden';
        },
        closeOption() {
            this.optionOpen = false;
            setTimeout(() => {
                this.optionProduct = null;
                document.body.style.overflow = '';
            }, 300);
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

        /* ===== EXTRAS (Add-ons) ===== */
        toggleExtra(extraName) {
            const idx = this.extrasSelected.indexOf(extraName);
            if (idx > -1) {
                this.extrasSelected.splice(idx, 1);
            } else {
                this.extrasSelected.push(extraName);
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
        get optionValid() {
            // Valid kalau ada variant yang dipilih (atau produk tidak punya variant)
            return (this.optionProduct?.variants?.length && this.optionProduct.has_variants)
                ? this.optionSelected.length > 0
                : true;
        },
        get optionTotalPrice() {
            if (!this.optionProduct) return 0;
            let basePrice;
            if (!this.optionProduct.variants?.length || !this.optionProduct.has_variants) {
                basePrice = parseFloat(this.optionProduct.price) || 0;
            } else if (this.isMulti) {
                basePrice = parseFloat(this.optionProduct.price) || 0;
            } else {
                const v = this.optionProduct.variants.find(
                    (v) => v.name === this.optionSelected[0]
                );
                basePrice = v ? (parseFloat(v.price) || 0) : (parseFloat(this.optionProduct.price) || 0);
            }
            return (basePrice + this.extrasTotal) * this.optionQty;
        },
        confirmOption() {
            if (!this.optionValid || !this.optionProduct) return;

            let finalPrice = 0;
            let finalVariantLabel = '';
            let variantId = null;
            let variantIds = [];

            if (!this.optionProduct.variants?.length || !this.optionProduct.has_variants) {
                // No variant product
                finalPrice = parseFloat(this.optionProduct.price) || 0;
                variantId = this.optionProduct.default_variant_id || (this.optionProduct.variants?.[0]?.id || null);
                if (variantId) variantIds.push(variantId);
            } else if (this.isMulti) {
                // Multi selection (Checkbox)
                finalPrice = parseFloat(this.optionProduct.price) || 0;
                finalVariantLabel = this.optionSelected.join(', ');
                variantIds = this.optionProduct.variants
                    .filter(v => this.optionSelected.includes(v.name))
                    .map(v => v.id);
            } else {
                // Single selection (Radio)
                const selectedVariant = this.optionProduct.variants.find(
                    (v) => v.name === this.optionSelected[0]
                );
                finalPrice = selectedVariant
                    ? parseFloat(selectedVariant.price) || 0
                    : parseFloat(this.optionProduct.price) || 0;
                finalVariantLabel = this.optionSelected[0] || '';
                variantId = selectedVariant ? selectedVariant.id : null;
                if (variantId) variantIds.push(variantId);
            }

            finalPrice += this.extrasTotal;

            const finalExtraLabel = this.extrasSelected.length
                ? this.extrasSelected.join(', ')
                : '';

            let extraIds = [];
            if (this.optionProduct.extras && this.extrasSelected.length > 0) {
                extraIds = this.optionProduct.extras
                    .filter(e => this.extrasSelected.includes(e.name))
                    .map(e => e.id);
            }

            const combinedLabel = [finalVariantLabel, finalExtraLabel]
                .filter(Boolean)
                .join(' + ');

            this.addToCart(
                {...this.optionProduct, price: finalPrice, variant_ids: variantIds, extra_ids: extraIds},
                combinedLabel,
                this.optionQty,
                variantId
            );

            this.closeOption();
        },

        /* ===== CHECKOUT MODAL ===== */
        checkoutOpen: false,
        checkoutStep: 1,
        customerName: '',
        customerEmail: '',
        customerInfo: '',
        orderType: '',
        checkoutLoading: false,
        orderSuccess: null,
        // Default: 'cash'
        selectedPaymentMethod: 'cash',
        midtransEnabled: false,
        duitkuEnabled: false,
        duitkuPaymentMethods: [],

        // Tax & Service Charge Settings
        isTaxActive: false,
        taxRate: 0,
        isServiceActive: false,
        serviceRate: 0,

        get serviceChargeAmount() {
            if (!this.isServiceActive) return 0;
            return Math.round((this.serviceRate / 100) * this.totalCart);
        },

        get taxAmount() {
            if (!this.isTaxActive) return 0;
            return Math.round((this.taxRate / 100) * (this.totalCart + this.serviceChargeAmount));
        },

        get totalOrderPrice() {
            return this.totalCart + this.serviceChargeAmount + this.taxAmount;
        },

        get isDigitalMethod() {
            return this.selectedPaymentMethod === 'digital';
        },

        get isDuitkuMethod() {
            return this.selectedPaymentMethod !== 'cash' && this.selectedPaymentMethod !== 'digital';
        },

        async fetchDuitkuMethods() {
            if (!this.duitkuEnabled) {
                this.duitkuPaymentMethods = [];
                return;
            }
            if (this.totalOrderPrice <= 0) return;
            try {
                const res = await fetch(`/api/duitku/payment-methods?amount=${this.totalOrderPrice}`);
                const data = await res.json();
                if (data.success && Array.isArray(data.data)) {
                    this.duitkuPaymentMethods = data.data;
                }
            } catch (e) {
                console.error('[Duitku] Gagal mengambil metode pembayaran dinamis', e);
            }
        },

        init() {
            const root = this.$el;
            this.orderType = root.dataset.defaultOrderType || 'takeaway';
            this.waNumber = root.dataset.waNumber || '';
            this.midtransEnabled = root.dataset.midtransEnabled === '1';
            this.duitkuEnabled = root.dataset.duitkuEnabled === '1';

            // Parse settings
            this.isTaxActive = root.dataset.taxActive === '1';
            this.taxRate = parseFloat(root.dataset.taxRate) || 0;
            this.isServiceActive = root.dataset.serviceActive === '1';
            this.serviceRate = parseFloat(root.dataset.serviceRate) || 0;

            // Apply theme reliably on every component init (solves Livewire navigation reset)
            if (this.theme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }

            // Connection state handlers
            window.addEventListener('offline', () => {
                this.showToast('Koneksi terputus. Anda sedang offline.', 5000);
            });
            window.addEventListener('online', () => {
                this.showToast('Koneksi kembali terhubung.', 3000);
            });

            this.loadHistory();

            this.$watch('historyOpen', (val) => {
                if (val) {
                    this.fetchHistoryFromServer();
                }
            });
        },

        async fetchLatestSettings() {
            try {
                const res = await fetch('/api/restaurant');
                const data = await res.json();
                if (data.success && data.data) {
                    this.isTaxActive = !!data.data.is_tax_active;
                    this.taxRate = parseFloat(data.data.tax_rate) || 0;
                    this.isServiceActive = !!data.data.is_service_charge_active;
                    this.serviceRate = parseFloat(data.data.service_charge_rate) || 0;
                }
            } catch (e) {
                console.error('[Settings] Gagal mengambil pengaturan terbaru', e);
            }
        },

        openCheckout() {
            this.orderSuccess = null;
            this.checkoutStep = 1; // Reset to step 1
            this.checkoutOpen = true;
            document.body.style.overflow = 'hidden';
            this.selectedPaymentMethod = 'cash'; // Default ke cash

            // Fetch latest settings dynamically in background to prevent stale configuration bugs!
            this.fetchLatestSettings().then(() => {
                this.fetchDuitkuMethods();
            });
        },
        closeCheckout() {
            this.checkoutOpen = false;
            setTimeout(() => {
                this.orderSuccess = null;
                this.checkoutStep = 1;
                document.body.style.overflow = '';
            }, 300);
        },
        nextStep() {
            if (this.cart.length === 0) {
                this.showToast('Keranjang Anda kosong!');
                return;
            }
            if (this.cart.some(i => i.unavailable)) {
                this.showToast('Hapus item tidak tersedia dulu!');
                return;
            }
            this.checkoutStep = 2;
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
            // Validasi email wajib jika metode Digital
            if ((this.isDigitalMethod || this.isDuitkuMethod) && !this.customerEmail.trim()) {
                this.showToast('Email wajib diisi untuk pembayaran digital!');
                return;
            }
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if ((this.isDigitalMethod || this.isDuitkuMethod) && !emailRegex.test(this.customerEmail.trim())) {
                this.showToast('Format email tidak valid!');
                return;
            }

            this.checkoutLoading = true;

            const payload = {
                customer_name: this.customerName.trim(),
                customer_email: this.customerEmail.trim() || null,
                order_type: this.orderType,
                order_info: this.customerInfo.trim() || null,
                total_price: this.totalOrderPrice,
                payment_method: this.isDigitalMethod
                    ? 'digital'
                    : this.isDuitkuMethod ? this.selectedPaymentMethod : 'cash',
                items: this.cart.map((item) => ({
                    product_id: item.id,
                    variant_id: item.variant_id || null,
                    variant_ids: item.variant_ids || [],
                    extra_ids: item.extra_ids || [],
                    name: item.cartName,
                    quantity: item.qty,
                    price: parseFloat(item.price)
                }))
            };

            try {
                const res = await fetch('/api/orders', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN':
                            document.querySelector('meta[name=csrf-token]')
                                ?.content || ''
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();

                if (res.ok) {
                    const invoiceCode = data.data?.invoice_code || 'OK';
                    const snapToken = data.data?.snap_token || null;
                    const paymentUrl = data.data?.payment_url || null;

                    // Jika Duitku
                    if (this.isDuitkuMethod && paymentUrl) {
                        this.addOrderToHistory({
                            invoiceCode: invoiceCode,
                            totalRaw: this.totalOrderPrice,
                            orderType: this.orderType,
                            paymentMethod: this.selectedPaymentMethod,
                            paymentName: this.duitkuPaymentMethods.find(m => m.paymentMethod === this.selectedPaymentMethod)?.paymentName || this.selectedPaymentMethod,
                            items: this.cart
                        });

                        this.cart = [];
                        this.saveCart();
                        this.customerName = '';
                        this.customerEmail = '';
                        this.customerInfo = '';
                        this.selectedPaymentMethod = 'cash';
                        this.showToast('Order berhasil dibuat! Membuka halaman pembayaran...');
                        setTimeout(() => {
                            window.location.href = `/invoice/${invoiceCode}`;
                        }, 800);
                        return;
                    }

                    // Jika Midtrans
                    if (this.isDigitalMethod && snapToken) {
                        this.addOrderToHistory({
                            invoiceCode: invoiceCode,
                            totalRaw: this.totalOrderPrice,
                            orderType: this.orderType,
                            paymentMethod: 'digital',
                            paymentName: 'Online Payment',
                            items: this.cart
                        });

                        this.cart = [];
                        this.saveCart();
                        this.customerName = '';
                        this.customerEmail = '';
                        this.customerInfo = '';
                        this.selectedPaymentMethod = 'cash';

                        this.showToast('Membuka halaman pembayaran aman...');

                        this.checkoutLoading = false;

                        setTimeout(() => {
                            window.snap.pay(snapToken, {
                                onSuccess: (result) => {
                                    window.location.href = `/invoice/${invoiceCode}`;
                                },
                                onPending: (result) => {
                                    window.location.href = `/invoice/${invoiceCode}`;
                                },
                                onError: (result) => {
                                    console.error('Midtrans Error:', result);
                                    let errorMsg = 'Pembayaran gagal diproses.';
                                    if (result && result.status_message) {
                                        errorMsg = `Gagal: ${result.status_message}`;
                                    }
                                    this.showToast(errorMsg);

                                    setTimeout(() => {
                                        window.location.href = `/invoice/${invoiceCode}`;
                                    }, 2000);
                                },
                                onClose: () => {
                                    // Jika user tutup modal sebelum bayar
                                    window.location.href = `/invoice/${invoiceCode}`;
                                }
                            });
                        }, 300);
                        return;
                    }

                    // Fallback / cash: kirim WA seperti biasa
                    const waLines = [
                        'Halo admin, pesanan baru nih!',
                        `*Invoice:* ${invoiceCode}`,
                        `*Nama:* ${this.customerName.trim()}`,
                        `*Tipe:* ${this.orderType}`,
                        '*Status Pembayaran:* Belum Dibayar',
                        this.customerInfo.trim()
                            ? `*Catatan/Meja:* ${this.customerInfo.trim()}`
                            : null,
                        '',
                        '*Daftar Pesanan:*',
                        ...this.cart.map((i) => `- ${i.qty}x ${i.cartName}`),
                        '',
                        `*Subtotal:* ${this.formatPrice(this.totalCart)}`
                    ];

                    if (this.isServiceActive && this.serviceChargeAmount > 0) {
                        waLines.push(`*Biaya Layanan (${this.serviceRate}%):* ${this.formatPrice(this.serviceChargeAmount)}`);
                    }

                    if (this.isTaxActive && this.taxAmount > 0) {
                        waLines.push(`*Pajak PB1 (${this.taxRate}%):* ${this.formatPrice(this.taxAmount)}`);
                    }

                    waLines.push(`*Total Tagihan:* ${this.formatPrice(this.totalOrderPrice)}`);

                    const waText = waLines.filter((l) => l !== null).join('\n');

                    // 2. Bikin URL WA-nya
                    const finalWaUrl = `https://wa.me/${this.waNumber}?text=${encodeURIComponent(waText)}`;

                    // 3. Simpan URL-nya ke state orderSuccess (biar tombol di HTML bisa pake link ini buat RESEND)
                    this.orderSuccess = {
                        invoiceCode: invoiceCode,
                        total: this.formatPrice(this.totalOrderPrice),
                        waUrl: finalWaUrl
                    };

                    // Simpan ke riwayat belanja lokal
                    this.addOrderToHistory({
                        invoiceCode: invoiceCode,
                        totalRaw: this.totalOrderPrice,
                        orderType: this.orderType,
                        paymentMethod: 'cash',
                        paymentName: 'Bayar Manual / Di Kasir',
                        items: this.cart,
                        waUrl: finalWaUrl
                    });

                    // 4. BUKA OTOMATIS KE WA (Persis kayak fitur asli lu)
                    window.open(finalWaUrl, '_blank');

                    // 5. Baru deh aman buat kosongin cart dan form
                    this.cart = [];
                    this.saveCart();
                    this.customerName = '';
                    this.customerEmail = '';
                    this.customerInfo = '';
                    this.selectedPaymentMethod = 'cash'; // reset ke default
                    this.showToast('Pesanan berhasil dikirim!');
                } else if (res.status === 422 && data.unavailable_ids?.length) {
                    // Tandai item di cart yang produknya tidak tersedia
                    const ids = data.unavailable_ids.map(Number);
                    this.cart = this.cart.map((item) => ({
                        ...item,
                        unavailable: ids.includes(Number(item.id))
                    }));
                    this.saveCart();
                    this.showToast(data.message || 'Beberapa produk tidak tersedia.');
                } else {
                    this.showToast(
                        data.message || 'Gagal mengirim pesanan. Coba lagi.'
                    );
                }
            } catch (err) {
                console.error('Order error:', err);
                this.showToast('Koneksi bermasalah. Coba lagi ya.');
            } finally {
                this.checkoutLoading = false;
            }
        }
    }));

    Alpine.store('utils', {

        // 1. Fungsi untuk generate text share
        generateShareText(product, restaurantName, url) {
            const generalHooks = [
                '✨ Barangkali lagi kepikiran',
                '🌟 Salah satu menu favorit dari',
                '📍 Jangan sampai kelewat nikmatnya',
                '🍃 Pilihan pas buat nemenin harimu:',
                '🤍 Rekomendasi spesial untukmu:',
                '💡 Wajib cobain menu andalan ini:'
            ];

            const randomHook = generalHooks[Math.floor(Math.random() * generalHooks.length)];
            const priceFormatted = 'Rp' + Number(product.price).toLocaleString('id-ID');

            let shareText = `${randomHook} ${product.name} di ${restaurantName}. `;

            if (product.description && product.description.trim() !== '') {
                let desc = product.description.trim();
                let shortDesc = desc.length > 50 ? desc.substring(0, 50) + '...' : desc;
                shareText += `(${shortDesc}) `;
            }

            shareText += `Harganya cuma ${priceFormatted} aja. Order praktis di sini: ${url}`;

            return shareText;
        },

        shareProduct(product, restaurantName = null) {
            let cleanName = restaurantName ?? document.title;
            cleanName = cleanName.split(/[—|,-]/)[0].trim();

            cleanName = cleanName.split(/[—|,-]/)[0].trim();

            const createSlug = (text) => text.toString().toLowerCase()
                .replace(/\s+/g, '-')           // ganti spasi jadi -
                .replace(/[^\w\-]+/g, '')       // hapus karakter aneh
                .replace(/-\-+/g, '-')          // ganti multiple dash jadi satu
                .replace(/^-+/, '')             // hapus dash di awal
                .replace(/-+$/, '');            // hapus dash di akhir

            const slugName = createSlug(product.name);
            const shareUrl = window.location.origin + '/menu/' + slugName + '-' + product.id;
            const shareText = this.generateShareText(product, cleanName, shareUrl);

            try {
                // Eksekusi Web Share API
                if (navigator.share) {
                    navigator.share({
                        title: product.name,
                        text: shareText
                    }).catch((error) => console.log('Share dibatalkan/gagal', error));
                } else {
                    // Fallback kalau browser nggak support share (misal di PC)
                    navigator.clipboard.writeText(shareText).then(() => {
                        showIslandToast('Teks dan link menu berhasil disalin!');
                    });
                }
            } catch (e) {
                showIslandToast('Tidak Bisa Menyalin Teks dan Link Menu!');
            }
        }

    });
});

let storeNavLoaderHtml = `
<div id="dynamic-nav-loader" class="fixed inset-0 z-[9999] bg-[var(--background)]/80 backdrop-blur-md flex flex-col items-center justify-center gap-6 pointer-events-auto" style="display: flex;">
    <div class="relative">
        <div class="w-20 h-20 rounded-3xl bg-gradient-to-br from-zinc-900 to-zinc-800 flex items-center justify-center shadow-2xl shadow-zinc-900/20 border border-zinc-800 animate-bounce">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[var(--primary-color,#f59e0b)]">
                <path d="m16 2-2.3 2.3a3 3 0 0 0 0 4.2l1.8 1.8a3 3 0 0 0 4.2 0L22 8"/>
                <path d="M15 15 3.3 3.3a4.2 4.2 0 0 0 0 6l7.3 7.3c.9.9 2.1 1.4 3.4 1.4H20v-3.5c0-1.3-.5-2.5-1.4-3.4z"/>
                <path d="m2 22 7.5-7.5"/>
            </svg>
        </div>
        <div class="absolute -inset-2 rounded-[2rem] border-2 border-dashed border-[var(--primary-color)]/50 animate-spin" style="animation-duration:3s"></div>
    </div>
    <div class="text-center space-y-1.5">
        <p class="text-[var(--foreground)] text-sm font-black tracking-tight">Menyiapkan Menu</p>
        <div class="flex items-center justify-center gap-1.5">
            <div class="w-1.5 h-1.5 rounded-full bg-[var(--primary-color)] animate-dot-1"></div>
            <div class="w-1.5 h-1.5 rounded-full bg-[var(--primary-color)] animate-dot-2"></div>
            <div class="w-1.5 h-1.5 rounded-full bg-[var(--primary-color)] animate-dot-3"></div>
        </div>
    </div>
</div>
`;

let storeNavLoaderStartTime = 0;
let storeNavShowTimeout = null;

window.showStoreLoader = function () {
    if (!document.getElementById('dynamic-nav-loader')) {
        document.body.insertAdjacentHTML('beforeend', storeNavLoaderHtml);
        storeNavLoaderStartTime = Date.now();
    }
};

window.hideStoreLoader = function () {
    clearTimeout(storeNavShowTimeout); // Cancel if it hasn't shown yet

    const dynamicLoader = document.getElementById('dynamic-nav-loader');

    const removeLoaders = () => {
        if (dynamicLoader) {
            dynamicLoader.remove();
        }
        const initialLoader = document.getElementById('app-loader');
        if (initialLoader) {
            initialLoader.style.setProperty('display', 'none', 'important');
        }
    };

    // If the dynamic loader was never injected (fast network), just clean up and exit
    if (!dynamicLoader) {
        removeLoaders();
        return;
    }

    // If it WAS injected, enforce minimum display time so it doesn't flash awkwardly
    const elapsedTime = Date.now() - storeNavLoaderStartTime;
    const minDisplayTime = 400; // Force loader to show for at least 400ms once it appears

    if (elapsedTime < minDisplayTime) {
        setTimeout(removeLoaders, minDisplayTime - elapsedTime);
    } else {
        removeLoaders();
    }
};

document.addEventListener('click', (e) => {
    // Ignore right clicks or clicks with modifier keys (new tab/window)
    if (e.button !== 0 || e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) return;

    const link = e.target.closest('a');
    if (link && Array.from(link.attributes).some(attr => attr.name.startsWith('wire:navigate'))) {
        // Threshold: 150ms. If network is faster than this, NO loader will appear.
        clearTimeout(storeNavShowTimeout);
        storeNavShowTimeout = setTimeout(() => {
            window.showStoreLoader();
        }, 150);
    }
});

document.addEventListener('livewire:navigated', () => {
    window.hideStoreLoader();

    // Fix Midtrans Snap.js in Livewire SPA
    // Livewire replaces the <body> during navigation, which destroys the hidden
    // iframe container that snap.js creates on initial load. We must force snap.js
    // to re-evaluate so it injects its container into the new <body>.
    const snapScript = document.querySelector('script[src*="snap.js"]');
    if (snapScript) {
        const newScript = document.createElement('script');
        newScript.src = snapScript.src;
        if (snapScript.hasAttribute('data-client-key')) {
            newScript.setAttribute('data-client-key', snapScript.getAttribute('data-client-key'));
        }
        snapScript.remove();
        document.head.appendChild(newScript);
    }
});

document.addEventListener('livewire:initialized', () => {
    window.hideStoreLoader();
});

// Emergency fallbacks so the loader doesn't spin forever
window.addEventListener('offline', () => {
    window.hideStoreLoader();
});

document.addEventListener('livewire:navigate.error', () => {
    window.hideStoreLoader();
});
