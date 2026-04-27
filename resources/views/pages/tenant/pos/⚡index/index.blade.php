<div class="row g-4 pos-container h-100"
     x-data="posSystem()"
     @add-product.window="handleProductClick($event.detail.product)"
     x-cloak>

    <div class="col-lg-7 col-xl-8 d-flex flex-column h-100">
        <livewire:tenant.pos.product-list/>
    </div>

    <div class="col-lg-5 col-xl-4 h-100">
        <div class="card h-100 d-flex flex-column overflow-hidden shadow-sm" style="border-radius: 1.25rem;">

            <div class="p-4 border-bottom d-flex justify-content-between align-items-center bg-body-tertiary">
                <h4 class="fw-bold font-serif text-primary mb-0">Pesanan</h4>
                <button @click="clearCart" class="btn btn-sm btn-outline-danger"
                        style="border-radius: 0.5rem; font-weight: 600;" x-show="cart.length > 0">
                    <i class="bi bi-trash3 me-1"></i> Kosongkan
                </button>
            </div>

            <div class="card-body p-3 overflow-y-auto flex-grow-1 bg-body">
                <template x-if="cart.length === 0">
                    <div
                        class="d-flex flex-column justify-content-center align-items-center h-100 text-muted opacity-50">
                        <i class="bi bi-bag-x" style="font-size: 4rem; margin-bottom: 1rem;"></i>
                        <p class="fw-bold font-serif mb-0">Belum ada pesanan</p>
                        <small>Klik menu di sebelah kiri</small>
                    </div>
                </template>

                <div class="d-flex flex-column gap-2">
                    <template x-for="(item, index) in cart" :key="index">
                        <div class="card bg-body-tertiary p-3 border-0" style="border-radius: 1rem;">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="pe-3">
                                    <h6 class="fw-bold mb-1 text-dark" x-text="item.name"></h6>
                                    <template x-if="item.variant_name">
                                        <span class="badge bg-secondary opacity-75 rounded-pill mb-1"
                                              x-text="item.variant_name"></span>
                                    </template>
                                </div>
                                <button @click="removeFromCart(index)" class="btn btn-sm text-danger p-1"><i
                                        class="bi bi-x-lg"></i></button>
                            </div>
                            <div class="d-flex justify-content-between align-items-end mt-2">
                                <div class="d-flex align-items-center bg-body rounded-pill border"
                                     style="padding: 0.15rem;">
                                    <button @click="decreaseQty(index)" class="btn btn-sm btn-light rounded-circle p-1"
                                            style="width: 28px; height: 28px;"><i class="bi bi-dash"></i></button>
                                    <span class="fw-bold px-3" x-text="item.quantity"></span>
                                    <button @click="increaseQty(index)"
                                            class="btn btn-sm btn-primary rounded-circle p-1"
                                            style="width: 28px; height: 28px;" :disabled="item.quantity >= item.stock">
                                        <i class="bi bi-plus"></i></button>
                                </div>
                                <span class="fw-bold" style="color: var(--brand-caramel);"
                                      x-text="'Rp ' + formatRupiah(item.subtotal)"></span>
                            </div>
                            <div class="mt-3">
                                <input type="text" class="form-control form-control-sm bg-body" x-model="item.note"
                                       placeholder="Catatan (opsional)..."
                                       style="border-radius: 0.5rem; border-style: dashed;">
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="p-4 border-top bg-body-tertiary">
                <div class="row g-2 mb-2">
                    <div class="col-6"><input type="text" class="form-control bg-body border-0 shadow-sm"
                                              x-model="customerName" placeholder="Nama Pelanggan"
                                              style="border-radius: 0.75rem;"></div>
                    <div class="col-6"><input type="text" class="form-control bg-body border-0 shadow-sm"
                                              x-model="customerPhone" placeholder="No WA (0812...)"
                                              style="border-radius: 0.75rem;"></div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <select class="form-select bg-body border-0 shadow-sm fw-bold text-dark" x-model="orderType"
                                style="border-radius: 0.75rem;">
                            <option value="retail">Retail (Baju/Barang)</option>
                            <option value="dinein">Dine In (Makan Sini)</option>
                            <option value="takeaway">Takeaway (Bungkus)</option>
                            <option value="online">Order Online</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <input type="text" class="form-control bg-body border-0 shadow-sm" x-model="tableNumber"
                               placeholder="No Meja" x-bind:disabled="orderType !== 'dinein'"
                               :class="orderType !== 'dinein' ? 'opacity-50' : ''" style="border-radius: 0.75rem;">
                    </div>
                </div>

                <div class="mb-3">
                    <div class="input-group shadow-sm" style="border-radius: 0.75rem;">
                        <span class="input-group-text bg-body border-0 text-muted fw-bold"
                              style="border-top-left-radius: 0.75rem; border-bottom-left-radius: 0.75rem;">
                            <i class="bi bi-tag-fill me-1"></i> Diskon (Rp)
                        </span>
                        <input type="number" class="form-control bg-body border-0 fw-bold" x-model.number="discount"
                               placeholder="0"
                               style="border-top-right-radius: 0.75rem; border-bottom-right-radius: 0.75rem;">
                    </div>
                </div>

                <div x-show="stockError" class="text-danger small fw-bold mb-2 text-center" x-text="stockError"></div>

                <button @click="openPaymentModal"
                        class="btn btn-primary btn-lg w-100 fw-bold shadow-sm d-flex justify-content-between align-items-center"
                        :disabled="cart.length === 0 || stockError !== ''"
                        style="padding: 1rem; font-size: 1.1rem; border-radius: 1rem;">
                    <span>Lanjut Bayar</span>
                    <span x-text="'Rp ' + formatRupiah(grandTotal)"></span>
                </button>
            </div>
        </div>
    </div>

    <div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 1.25rem;">
                <div class="modal-header border-bottom bg-body-tertiary px-4 py-3"
                     style="border-top-left-radius: 1.25rem; border-top-right-radius: 1.25rem;">
                    <h4 class="fw-bold font-serif text-primary mb-0">Pembayaran</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4 bg-body">
                    <div class="row g-4">
                        <div class="col-md-5 border-end pe-md-4">
                            <h6 class="fw-bold text-muted mb-3">Total Tagihan</h6>
                            <h2 class="fw-bolder mb-1" style="color: var(--brand-caramel);"
                                x-text="'Rp ' + formatRupiah(grandTotal)"></h2>
                            <template x-if="discount > 0">
                                <p class="text-danger small fw-bold mb-4"
                                   x-text="'Termasuk Diskon: -Rp ' + formatRupiah(discount)"></p>
                            </template>
                            <template x-if="!discount">
                                <div class="mb-4"></div>
                            </template>

                            <h6 class="fw-bold text-muted mb-3">Metode Pembayaran</h6>
                            <div class="d-flex flex-column gap-2">
                                <label class="btn btn-outline-primary fw-bold text-start p-3 rounded-4"
                                       :class="paymentMethod === 'cash' ? 'active' : ''">
                                    <input type="radio" x-model="paymentMethod" value="cash" class="d-none"> <i
                                        class="bi bi-cash-stack me-2"></i> Tunai (Cash)
                                </label>
                                <label class="btn btn-outline-primary fw-bold text-start p-3 rounded-4"
                                       :class="paymentMethod === 'qris' ? 'active' : ''">
                                    <input type="radio" x-model="paymentMethod" value="qris" class="d-none"> <i
                                        class="bi bi-qr-code-scan me-2"></i> QRIS
                                </label>
                                <label class="btn btn-outline-primary fw-bold text-start p-3 rounded-4"
                                       :class="paymentMethod === 'transfer' ? 'active' : ''">
                                    <input type="radio" x-model="paymentMethod" value="transfer" class="d-none"> <i
                                        class="bi bi-bank me-2"></i> Transfer Bank
                                </label>
                            </div>
                        </div>

                        <div class="col-md-7">
                            <template x-if="paymentMethod !== 'cash'">
                                <div
                                    class="d-flex flex-column justify-content-center align-items-center h-100 text-center">
                                    <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                                    <h5 class="fw-bold mt-3">Pembayaran Non-Tunai</h5>
                                    <p class="text-muted small">Pastikan saldo pelanggan sudah masuk sebelum klik
                                        Proses.</p>
                                </div>
                            </template>

                            <template x-if="paymentMethod === 'cash'">
                                <div>
                                    <div
                                        class="p-3 border border-primary bg-body mb-3 d-flex justify-content-between align-items-center shadow-sm"
                                        style="border-radius: 1rem;">
                                        <span class="fw-bold text-primary">Diterima:</span>
                                        <h4 class="fw-bolder text-primary mb-0"
                                            x-text="amountPaid ? 'Rp ' + formatRupiah(amountPaid) : 'Rp 0'"></h4>
                                    </div>
                                    <div class="row g-2 mb-2">
                                        <div class="col-4">
                                            <button type="button" @click="amountPaid = grandTotal"
                                                    class="btn btn-outline-primary w-100 fw-bold"
                                                    style="border-radius: 0.75rem;">Pas
                                            </button>
                                        </div>
                                        <div class="col-4">
                                            <button type="button" @click="amountPaid = 50000"
                                                    class="btn btn-outline-secondary w-100 fw-bold bg-body"
                                                    style="border-radius: 0.75rem;">50k
                                            </button>
                                        </div>
                                        <div class="col-4">
                                            <button type="button" @click="amountPaid = 100000"
                                                    class="btn btn-outline-secondary w-100 fw-bold bg-body"
                                                    style="border-radius: 0.75rem;">100k
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row g-2 mb-3">
                                        <template x-for="n in [1, 2, 3, 4, 5, 6, 7, 8, 9]" :key="n">
                                            <div class="col-4">
                                                <button type="button" @click="appendNumber(n)"
                                                        class="btn btn-light border w-100 fs-4 fw-bold py-2 text-dark bg-body shadow-sm"
                                                        style="border-radius: 0.75rem;" x-text="n"></button>
                                            </div>
                                        </template>
                                        <div class="col-4">
                                            <button type="button" @click="appendNumber('000')"
                                                    class="btn btn-light border w-100 fs-4 fw-bold py-2 text-dark bg-body shadow-sm"
                                                    style="border-radius: 0.75rem;">000
                                            </button>
                                        </div>
                                        <div class="col-4">
                                            <button type="button" @click="appendNumber('0')"
                                                    class="btn btn-light border w-100 fs-4 fw-bold py-2 text-dark bg-body shadow-sm"
                                                    style="border-radius: 0.75rem;">0
                                            </button>
                                        </div>
                                        <div class="col-4">
                                            <button type="button" @click="deleteNumber()"
                                                    class="btn btn-light border w-100 fs-4 fw-bold py-2 text-danger bg-body shadow-sm d-flex justify-content-center align-items-center"
                                                    style="border-radius: 0.75rem; height: 100%;"><i
                                                    class="bi bi-backspace-fill"></i></button>
                                        </div>
                                    </div>

                                    <template x-if="amountPaid && getChange >= 0">
                                        <div
                                            class="d-flex justify-content-between p-3 bg-success bg-opacity-10 rounded-4 border border-success border-opacity-25 shadow-sm">
                                            <span class="text-success fw-bold">Kembalian:</span>
                                            <h5 class="fw-bolder text-success mb-0"
                                                x-text="'Rp ' + formatRupiah(getChange)"></h5>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-body-tertiary border-top p-3"
                     style="border-bottom-left-radius: 1.25rem; border-bottom-right-radius: 1.25rem;">
                    <button type="button" class="btn btn-outline-secondary fw-bold" data-bs-dismiss="modal"
                            style="border-radius: 1rem; padding: 0.75rem 1.5rem;">Batal
                    </button>
                    <button @click="submitOrder"
                            class="btn btn-primary fw-bold shadow-sm d-flex align-items-center gap-2"
                            :disabled="isSubmitting || (paymentMethod === 'cash' && (!amountPaid || getChange < 0))"
                            style="border-radius: 1rem; padding: 0.75rem 2rem;">
                        <i class="bi bi-check2-circle" x-show="!isSubmitting"></i>
                        <span class="spinner-border spinner-border-sm" x-show="isSubmitting"></span>
                        <span x-text="isSubmitting ? 'Memproses...' : 'Proses Transaksi'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="variantModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 1.25rem;">
                <div class="modal-header border-bottom pb-3 pt-4 px-4 bg-body-tertiary"
                     style="border-top-left-radius: 1.25rem; border-top-right-radius: 1.25rem;">
                    <div>
                        <h4 class="fw-bold font-serif text-primary mb-1">Pilih Varian</h4>
                        <p class="text-muted small mb-0" x-text="selectedProduct ? selectedProduct.name : ''"></p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-body"
                     style="border-bottom-left-radius: 1.25rem; border-bottom-right-radius: 1.25rem;">
                    <div class="d-flex flex-column gap-2">
                        <template x-if="selectedProduct">
                            <template x-for="variant in selectedProduct.variants" :key="variant.id">
                                <button type="button"
                                        class="card flex-row justify-content-between align-items-center p-3 text-start w-100 border transition-all"
                                        :class="{'opacity-50 bg-body-tertiary': variant.stock <= 0, 'border-primary shadow-sm': variant.stock > 0}"
                                        :disabled="variant.stock <= 0"
                                        @click="if(variant.stock > 0) addVariantToCart(variant)"
                                        style="border-radius: 1rem;">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-1" x-text="variant.name"></h6>
                                        <span class="small badge rounded-pill"
                                              :class="variant.stock > 0 ? 'bg-body-tertiary text-muted border' : 'bg-danger text-white'"
                                              x-text="variant.stock > 0 ? 'Tersedia: ' + variant.stock : 'Stok Habis'"></span>
                                    </div>
                                    <h5 class="fw-bold mb-0" style="color: var(--brand-caramel);"
                                        x-text="'Rp ' + formatRupiah(variant.price)"></h5>
                                </button>
                            </template>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="successModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg text-center p-4" style="border-radius: 1.25rem;">
                <div class="d-flex justify-content-center mb-4 mt-2">
                    <div
                        class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 80px; height: 80px;">
                        <i class="bi bi-check2-circle" style="font-size: 3.5rem;"></i>
                    </div>
                </div>
                <h4 class="fw-bold font-serif text-primary mb-2">Berhasil!</h4>
                <p class="text-muted small mb-4">No: <span class="fw-bold text-dark"
                                                           x-text="lastOrder.invoice_code"></span></p>

                <div class="d-flex flex-column gap-2 mb-2">
                    <div class="mb-3 text-start">
                        <label class="small fw-bold text-muted mb-1">Kirim Struk (Opsional)</label>
                        <input type="text" class="form-control bg-body-tertiary shadow-sm"
                               x-model="lastOrder.customer_phone" placeholder="Ketik No WA Pelanggan..."
                               style="border-radius: 0.75rem;">
                    </div>

                    <template x-if="lastOrder.customer_phone && lastOrder.customer_phone.length >= 9">
                        <button type="button" @click="sendWa"
                                class="btn btn-success fw-bold p-3 mb-2 d-flex align-items-center justify-content-center gap-2 shadow-sm"
                                style="border-radius: 1rem;">
                            <i class="bi bi-whatsapp fs-5"></i> Kirim Struk ke WA
                        </button>
                    </template>

                    <button type="button" @click="closeSuccessModal"
                            class="btn btn-outline-secondary fw-bold p-3 mt-1 shadow-sm" style="border-radius: 1rem;">
                        Tutup & Pesanan Baru
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

@script
<script>
    Alpine.data('posSystem', () => ({
        cart: [],
        selectedProduct: null,
        variantModalInstance: null,
        paymentModalInstance: null,
        successModalInstance: null,

        customerName: '',
        customerPhone: '',
        tableNumber: '',
        orderType: 'retail', // Default dibalikin ke retail
        paymentMethod: 'cash',
        discount: '',
        amountPaid: '',

        isSubmitting: false,
        stockError: '',
        lastOrder: {},

        init() {
            this.variantModalInstance = new bootstrap.Modal(document.getElementById('variantModal'));
            this.paymentModalInstance = new bootstrap.Modal(document.getElementById('paymentModal'));
            this.successModalInstance = new bootstrap.Modal(document.getElementById('successModal'));
            this.$watch('cart', () => {
                this.validateStock();
            }, {deep: true});
        },

        get subTotal() {
            return this.cart.reduce((total, item) => total + item.subtotal, 0);
        },
        get grandTotal() {
            return Math.max(0, this.subTotal - (parseFloat(this.discount) || 0));
        },
        get getChange() {
            return Math.max(0, (parseFloat(this.amountPaid) || 0) - this.grandTotal);
        },

        // UX: Buka modal pembayaran untuk membersihkan sidebar dari numpad
        openPaymentModal() {
            this.validateStock();
            if (this.cart.length === 0 || this.stockError !== '') {
                showIslandToast(this.stockError || 'Keranjang masih kosong!', 'warning');
                return;
            }
            this.amountPaid = ''; // Reset bayaran saat modal dibuka
            this.paymentMethod = 'cash';
            this.paymentModalInstance.show();
        },

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
            let existing = this.cart.find(item => item.variant_id === variant.id);
            if (existing) {
                if (existing.quantity < variant.stock) {
                    existing.quantity++;
                    existing.subtotal = existing.quantity * variant.price;
                } else {
                    showIslandToast(`Mentok! Stok sisa ${variant.stock}.`, 'warning');
                }
            } else {
                this.cart.push({
                    id: product.id,
                    variant_id: variant.id,
                    name: product.name,
                    variant_name: product.has_variants ? variant.name : null,
                    price: variant.price,
                    quantity: 1,
                    subtotal: variant.price,
                    stock: variant.stock,
                    note: ''
                });
            }
        },

        addVariantToCart(variant) {
            this.addToCart(this.selectedProduct, variant);
            this.variantModalInstance.hide();
            setTimeout(() => {
                this.selectedProduct = null;
            }, 300);
        },

        increaseQty(index) {
            let item = this.cart[index];
            if (item.quantity < item.stock) {
                item.quantity++;
                item.subtotal = item.quantity * item.price;
            } else {
                showIslandToast(`Mentok! Stok sisa ${item.stock}.`, 'warning');
            }
        },

        decreaseQty(index) {
            if (this.cart[index].quantity > 1) {
                this.cart[index].quantity--;
                this.cart[index].subtotal = this.cart[index].quantity * this.cart[index].price;
            } else {
                this.removeFromCart(index);
            }
        },

        removeFromCart(index) {
            this.cart.splice(index, 1);
        },
        clearCart() {
            this.cart = [];
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

        formatRupiah(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        },

        appendNumber(num) {
            let current = String(this.amountPaid || '');
            if (current.length < 12) {
                this.amountPaid = parseInt(current + num);
            }
        },
        deleteNumber() {
            let current = String(this.amountPaid || '');
            if (current.length > 1) {
                this.amountPaid = parseInt(current.slice(0, -1));
            } else {
                this.amountPaid = '';
            }
        },

        formatPhoneForWA(phone) {
            let cleaned = ('' + phone).replace(/\D/g, '');
            if (cleaned.startsWith('0')) return '62' + cleaned.substring(1);
            if (!cleaned.startsWith('62')) return '62' + cleaned;
            return cleaned;
        },

        // Trigger buka WA sekaligus update DB
        sendWa() {
            if (this.lastOrder.customer_phone) {
                // Background update ke Livewire tanpa menunggu selesai
                $wire.updateCustomerPhone(this.lastOrder.invoice_code, this.lastOrder.customer_phone);

                let phone = this.formatPhoneForWA(this.lastOrder.customer_phone);
                let invoiceUrl = `${window.location.origin}/invoice/${this.lastOrder.invoice_code}`;
                let message = `Halo Kak *${this.lastOrder.customer_name}*,\n\nTerima kasih telah berbelanja di *${this.lastOrder.store_name}*.\nBerikut adalah struk pesanan kakak:\n${invoiceUrl}\n\nTotal Belanja: Rp ${this.formatRupiah(this.lastOrder.total_price)}`;

                window.open(`https://wa.me/${phone}?text=${encodeURIComponent(message)}`, '_blank');
                this.closeSuccessModal();
            }
        },

        closeSuccessModal() {
            this.successModalInstance.hide();
            this.clearCart();
            this.customerName = '';
            this.customerPhone = '';
            this.tableNumber = '';
            this.orderType = 'retail';
            this.paymentMethod = 'cash';
            this.discount = '';
            this.amountPaid = '';
            this.lastOrder = {};
        },

        async submitOrder() {
            if (this.paymentMethod === 'cash' && (this.amountPaid < this.grandTotal || !this.amountPaid)) {
                showIslandToast('Uang tidak cukup!', 'warning');
                return;
            }
            this.isSubmitting = true;

            try {
                const result = await $wire.processCheckout(
                    this.cart, this.customerName, this.customerPhone, this.tableNumber,
                    this.orderType, this.paymentMethod, this.discount || 0, this.amountPaid
                );

                if (result && result.success) {
                    this.lastOrder = result;
                    this.paymentModalInstance.hide(); // Tutup modal bayar
                    Livewire.dispatchTo('tenant.pos.product-list', '$refresh'); // Refresh stok

                    // Beri jeda animasi sebelum buka modal sukses
                    setTimeout(() => {
                        this.successModalInstance.show();
                    }, 300);
                } else if (result && result.error) {
                    showIslandToast(result.error, 'danger');
                    Livewire.dispatchTo('tenant.pos.product-list', '$refresh');
                }
            } catch (error) {
                showIslandToast('Kesalahan sistem memproses transaksi.', 'danger');
            }

            this.isSubmitting = false;
        }
    }));
</script>
@endscript
