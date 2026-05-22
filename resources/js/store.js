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
                    body: JSON.stringify({ invoices: invoiceCodes })
                });
                
                const data = await res.json();
                if (data.success) {
                    this.orderHistory = data.data;
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
                this.orderHistory = JSON.parse(localStorage.getItem('pakaiapp_order_history') || '[]');
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
                items: orderData.items.map(i => ({
                    name: i.cartName || i.name,
                    qty: i.qty,
                    price: i.price
                }))
            };
            this.orderHistory.unshift(order);
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

        addToCart(item, selectedVariants = '', quantity = 1) {
            const cartName = selectedVariants
                ? `${item.name} (${selectedVariants})`
                : item.name;
            const existing = this.cart.find((i) => i.cartName === cartName);
            if (existing) {
                existing.qty += quantity;
            } else {
                this.cart.push({...item, cartName, qty: quantity});
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

            // Jika modal checkout sedang terbuka, fetch ulang payment methods karena nominal berubah
            if (this.checkoutOpen) {
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
                'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' +
                encodeURIComponent(window.location.href) +
                '&bgcolor=ffffff&color=000000&margin=10'
            );
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
                    product.variants && product.variants.length > 0
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
            return this.optionProduct?.variants?.length
                ? this.optionSelected.length > 0
                : true;
        },
        get optionTotalPrice() {
            if (!this.optionProduct) return 0;
            let basePrice;
            if (!this.optionProduct.variants?.length) {
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

            // Hitung harga variant
            let price;
            let variantLabel = '';
            if (!this.optionProduct.variants?.length) {
                price = parseFloat(this.optionProduct.price) || 0;
            } else if (this.isMulti) {
                price = parseFloat(this.optionProduct.price) || 0;
                variantLabel = this.optionSelected.join(', ');
            } else {
                const v = this.optionProduct.variants.find(
                    (v) => v.name === this.optionSelected[0]
                );
                price = v ? (parseFloat(v.price) || 0) : (parseFloat(this.optionProduct.price) || 0);
                variantLabel = this.optionSelected[0] || '';
            }

            // Tambahkan harga extras
            price += this.extrasTotal;

            // Bangun label lengkap untuk cartName
            const extrasLabel = this.extrasSelected.length
                ? this.extrasSelected.join(', ')
                : '';
            const selectedLabel = [variantLabel, extrasLabel].filter(Boolean).join(' + ');

            this.addToCart(
                {...this.optionProduct, price},
                selectedLabel,
                this.optionQty
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
        // Default: 'cash' = non-Duitku.
        selectedPaymentMethod: 'cash',
        duitkuPaymentMethods: [],
        duitkuEnabled: false,

        get isDuitkuMethod() {
            return this.selectedPaymentMethod !== 'cash';
        },

        async fetchDuitkuMethods() {
            if (!this.duitkuEnabled) {
                this.duitkuPaymentMethods = [];
                return;
            }
            if (this.totalCart <= 0) return;
            try {
                const res = await fetch(`/api/duitku/payment-methods?amount=${this.totalCart}`);
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
            this._waNumber = root.dataset.waNumber || '6281234567890';
            this.duitkuEnabled = root.dataset.duitkuEnabled === '1';
            this.loadHistory();
            
            this.$watch('historyOpen', (val) => {
                if (val) {
                    this.fetchHistoryFromServer();
                }
            });
        },

        openCheckout() {
            this.orderSuccess = null;
            this.checkoutStep = 1; // Reset to step 1
            this.checkoutOpen = true;
            document.body.style.overflow = 'hidden';
            this.selectedPaymentMethod = 'cash'; // Default ke cash
            this.fetchDuitkuMethods().then(() => {
                // Set default selectedPaymentMethod ke QRIS jika ada, agar user-friendly
                if (this.duitkuPaymentMethods.length > 0) {
                    const hasQris = this.duitkuPaymentMethods.find(m => ['NQ', 'SP', 'QRIS', 'QRISC'].includes(m.paymentMethod));
                    if (hasQris) {
                        this.selectedPaymentMethod = hasQris.paymentMethod;
                    }
                }
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
            // Validasi email wajib jika metode Duitku
            if (this.isDuitkuMethod && !this.customerEmail.trim()) {
                this.showToast('Email wajib diisi untuk pembayaran digital!');
                return;
            }
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (this.isDuitkuMethod && !emailRegex.test(this.customerEmail.trim())) {
                this.showToast('Format email tidak valid!');
                return;
            }

            this.checkoutLoading = true;

            const payload = {
                customer_name: this.customerName.trim(),
                // Email — wajib untuk Duitku, optional untuk cash
                customer_email: this.customerEmail.trim() || null,
                order_type: this.orderType,
                order_info: this.customerInfo.trim() || null,
                total_price: this.totalCart,
                // Kirim kode Duitku jika bukan cash, undefined jika cash
                payment_method: this.isDuitkuMethod ? this.selectedPaymentMethod : 'cash',
                items: this.cart.map((item) => ({
                    product_id: item.id,
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
                    const paymentUrl = data.data?.payment_url || null;

                    // Jika Duitku: arahkan langsung ke halaman invoice untuk menampilkan instruksi pembayaran premium
                    if (this.isDuitkuMethod) {
                        // Simpan ke riwayat belanja lokal
                        this.addOrderToHistory({
                            invoiceCode: invoiceCode,
                            totalRaw: this.totalCart,
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
                        this.selectedPaymentMethod = 'cash'; // reset ke default
                        this.showToast('Order berhasil dibuat! Membuka halaman pembayaran...');
                        setTimeout(() => {
                            window.location.href = `/invoice/${invoiceCode}`;
                        }, 800);
                        return;
                    }

                    // Fallback / cash: kirim WA seperti biasa
                    const waText = [
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
                        `*Total Tagihan:* ${this.formatPrice(this.totalCart)}`
                    ]
                        .filter((l) => l !== null)
                        .join('\n');

                    // 2. Bikin URL WA-nya
                    const finalWaUrl = `https://wa.me/${this._waNumber}?text=${encodeURIComponent(waText)}`;

                    // 3. Simpan URL-nya ke state orderSuccess (biar tombol di HTML bisa pake link ini buat RESEND)
                    this.orderSuccess = {
                        invoiceCode: invoiceCode,
                        total: this.formatPrice(this.totalCart),
                        waUrl: finalWaUrl
                    };

                    // Simpan ke riwayat belanja lokal
                    this.addOrderToHistory({
                        invoiceCode: invoiceCode,
                        totalRaw: this.totalCart,
                        orderType: this.orderType,
                        paymentMethod: 'cash',
                        paymentName: 'Bayar Manual / Di Kasir',
                        items: this.cart
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
