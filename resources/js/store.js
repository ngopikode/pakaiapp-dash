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
document.addEventListener("alpine:init", () => {
    // -------------------------------------------------------------------------
    // UI PREFERENCES STORE
    // Persisted in localStorage. Changing viewMode NEVER triggers a Livewire
    // round-trip — it's pure client-side state.
    // -------------------------------------------------------------------------
    Alpine.store("ui", {
        viewMode: localStorage.getItem("ezmenu-viewmode") || "grid",

        setViewMode(mode) {
            this.viewMode = mode;
            localStorage.setItem("ezmenu-viewmode", mode);
        },
    });

    // -------------------------------------------------------------------------
    // STORE APP — global cart + modals state
    // -------------------------------------------------------------------------
    Alpine.data("storeApp", () => ({
        /* ===== CART ===== */
        toast: { show: false, message: "" },
        qrOpen: false,
        cart: JSON.parse(localStorage.getItem("ezmenu-cart") || "[]"),

        saveCart() {
            localStorage.setItem("ezmenu-cart", JSON.stringify(this.cart));
        },

        addToCart(item, selectedVariants = "", quantity = 1) {
            const cartName = selectedVariants
                ? `${item.name} (${selectedVariants})`
                : item.name;
            const existing = this.cart.find((i) => i.cartName === cartName);
            if (existing) {
                existing.qty += quantity;
            } else {
                this.cart.push({ ...item, cartName, qty: quantity });
            }
            this.saveCart();
            this.showToast("Berhasil ditambahkan ke keranjang!");
        },

        updateQty(cartName, delta) {
            const existing = this.cart.find((i) => i.cartName === cartName);
            if (!existing) return;
            existing.qty += delta;
            if (existing.qty <= 0) {
                this.cart = this.cart.filter((i) => i.cartName !== cartName);
            }
            this.saveCart();
        },

        get totalQty() {
            return this.cart.reduce((acc, item) => acc + item.qty, 0);
        },

        get totalCart() {
            return this.cart.reduce(
                (acc, item) => acc + item.price * item.qty,
                0,
            );
        },

        formatPrice(price) {
            return new Intl.NumberFormat("id-ID", {
                style: "currency",
                currency: "IDR",
                minimumFractionDigits: 0,
            }).format(price);
        },

        /* ===== TOAST ===== */
        showToast(msg, duration = 3000) {
            this.toast = { show: true, message: msg };
            clearTimeout(this._toastTimer);
            this._toastTimer = setTimeout(
                () => (this.toast.show = false),
                duration,
            );
        },

        /* ===== QR MODAL ===== */
        get qrUrl() {
            return (
                "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" +
                encodeURIComponent(window.location.href) +
                "&bgcolor=ffffff&color=000000&margin=10"
            );
        },

        /* ===== OPTION MODAL (Variants: Single or Multi-select) ===== */
        optionOpen: false,
        optionProduct: null,
        optionSelected: [],
        optionQty: 1,

        get isMulti() {
            return this.optionProduct?.selection_type === "multiple";
        },
        get maxSel() {
            return this.optionProduct?.max_selections || 1;
        },

        openOption(product) {
            this.optionProduct = product;
            this.optionQty = 1;
            if (product.selection_type === "multiple") {
                this.optionSelected = [];
            } else {
                // Single: pre-select first variant for better UX
                this.optionSelected =
                    product.variants && product.variants.length > 0
                        ? [product.variants[0].name]
                        : [];
            }
            this.optionOpen = true;
            document.body.style.overflow = "hidden";
        },
        closeOption() {
            this.optionOpen = false;
            setTimeout(() => {
                this.optionProduct = null;
                document.body.style.overflow = "";
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
        get optionValid() {
            return this.optionSelected.length > 0;
        },
        get optionTotalPrice() {
            if (!this.optionProduct) return 0;
            if (this.isMulti) {
                return this.optionProduct.price * this.optionQty;
            }
            const v = this.optionProduct.variants.find(
                (v) => v.name === this.optionSelected[0],
            );
            return (v ? v.price : this.optionProduct.price) * this.optionQty;
        },
        confirmOption() {
            if (!this.optionValid || !this.optionProduct) return;
            const selectedLabel = this.optionSelected.join(", ");
            let price;
            if (this.isMulti) {
                price = this.optionProduct.price;
            } else {
                const v = this.optionProduct.variants.find(
                    (v) => v.name === this.optionSelected[0],
                );
                price = v ? v.price : this.optionProduct.price;
            }
            this.addToCart(
                { ...this.optionProduct, price },
                selectedLabel,
                this.optionQty,
            );
            this.closeOption();
        },

        /* ===== CHECKOUT MODAL ===== */
        checkoutOpen: false,
        customerName: "",
        customerInfo: "",
        orderType: "",
        checkoutLoading: false,
        orderSuccess: null,

        init() {
            const root = this.$el;
            this.orderType = root.dataset.defaultOrderType || "takeaway";
            this._waNumber = root.dataset.waNumber || "6281234567890";
        },

        openCheckout() {
            this.orderSuccess = null;
            this.checkoutOpen = true;
            document.body.style.overflow = "hidden";
        },
        closeCheckout() {
            this.checkoutOpen = false;
            setTimeout(() => {
                this.orderSuccess = null;
                document.body.style.overflow = "";
            }, 300);
        },

        async processOrder() {
            if (this.cart.length === 0) {
                this.showToast("Keranjang Anda kosong!");
                return;
            }
            if (!this.customerName.trim()) {
                this.showToast("Masukkan nama pemesan dulu ya!");
                return;
            }

            this.checkoutLoading = true;

            const payload = {
                customer_name: this.customerName.trim(),
                order_type: this.orderType,
                order_info: this.customerInfo.trim() || null,
                total_price: this.totalCart,
                items: this.cart.map((item) => ({
                    product_id: item.id,
                    name: item.cartName,
                    quantity: item.qty,
                    price: parseFloat(item.price),
                })),
            };

            try {
                const res = await fetch("/api/orders", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        "X-CSRF-TOKEN":
                            document.querySelector("meta[name=csrf-token]")
                                ?.content || "",
                    },
                    body: JSON.stringify(payload),
                });

                const data = await res.json();

                if (res.ok) {
                    this.orderSuccess = {
                        invoiceCode: data.data?.invoice_code || "OK",
                        total: this.formatPrice(this.totalCart),
                    };

                    const waText = [
                        "Halo admin, pesanan baru nih!",
                        `*Invoice:* ${this.orderSuccess.invoiceCode}`,
                        `*Nama:* ${this.customerName.trim()}`,
                        `*Tipe:* ${this.orderType}`,
                        "*Status Pembayaran:* Belum Dibayar",
                        this.customerInfo.trim()
                            ? `*Catatan/Meja:* ${this.customerInfo.trim()}`
                            : null,
                        "",
                        "*Daftar Pesanan:*",
                        ...this.cart.map((i) => `- ${i.qty}x ${i.cartName}`),
                        "",
                        `*Total Tagihan:* ${this.orderSuccess.total}`,
                    ]
                        .filter((l) => l !== null)
                        .join("\n");

                    window.open(
                        `https://wa.me/${this._waNumber}?text=${encodeURIComponent(waText)}`,
                        "_blank",
                    );

                    this.cart = [];
                    this.saveCart();
                    this.customerName = "";
                    this.customerInfo = "";
                    this.showToast("Pesanan berhasil dikirim!");
                } else {
                    this.showToast(
                        data.message || "Gagal mengirim pesanan. Coba lagi.",
                    );
                }
            } catch (err) {
                console.error("Order error:", err);
                this.showToast("Koneksi bermasalah. Coba lagi ya.");
            } finally {
                this.checkoutLoading = false;
            }
        },
    }));
});
