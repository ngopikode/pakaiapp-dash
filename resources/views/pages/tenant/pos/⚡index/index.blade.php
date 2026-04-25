<div class="row g-4 pos-container"
     x-data="posSystem()"
     @add-product.window="handleProductClick($event.detail.product)"
     x-cloak>

    <div class="col-lg-7 col-xl-8 d-flex flex-column h-100">
        <livewire:tenant.pos.product-list/>
    </div>

    <div class="col-lg-5 col-xl-4 h-100">
        <div class="card border-0 shadow-sm rounded-4 cart-sidebar">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bolder mb-0">Pesanan</h5>
                <button @click="clearCart" class="btn btn-sm btn-outline-danger rounded-pill" x-show="cart.length > 0">
                    Reset
                </button>
            </div>

            <div class="card-body p-0 cart-items bg-light bg-opacity-50">
                <template x-if="cart.length === 0">
                    <div class="d-flex flex-column justify-content-center align-items-center h-100 text-muted p-4">
                        <i class="bi bi-cart-x fs-1 mb-2 opacity-50"></i>
                        <p class="small mb-0">Keranjang kosong</p>
                    </div>
                </template>

                <div class="list-group list-group-flush">
                    <template x-for="(item, index) in cart" :key="index">
                        <div class="list-group-item p-3 bg-white mb-1 border-0 shadow-sm">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark text-truncate" x-text="item.name"></h6>
                                    <template x-if="item.variant_name">
                                        <span
                                            class="badge bg-secondary bg-opacity-10 text-secondary border rounded-pill small mt-1"
                                            x-text="item.variant_name"></span>
                                    </template>
                                </div>
                                <button @click="removeFromCart(index)"
                                        class="btn btn-sm text-danger p-0 border-0 shadow-none">
                                    <i class="bi bi-x-circle-fill fs-6"></i>
                                </button>
                            </div>

                            <div class="mb-2 mt-2">
                                <input type="text" class="form-control form-control-sm border-0 bg-light"
                                       x-model="item.note" placeholder="Catatan...">
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-1">
                                <div class="d-flex align-items-center bg-light rounded-pill border">
                                    <button @click="decreaseQty(index)"
                                            class="btn btn-sm px-2 text-dark border-0 shadow-none"><i
                                            class="bi bi-dash"></i></button>
                                    <span class="fw-bold px-2 text-sm" x-text="item.quantity"></span>
                                    <button @click="increaseQty(index)"
                                            class="btn btn-sm px-2 text-dark border-0 shadow-none"
                                            :disabled="item.quantity >= item.stock"><i class="bi bi-plus"></i></button>
                                </div>
                                <span class="fw-bolder text-primary small"
                                      x-text="'Rp ' + formatRupiah(item.subtotal)"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="card-footer bg-white border-top p-3 pt-3">
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <input type="text" class="form-control form-control-sm bg-light border-0" x-model="customerName"
                               placeholder="Nama Pemesan">
                    </div>
                    <div class="col-6">
                        <input type="text" class="form-control form-control-sm bg-light border-0"
                               x-model="customerPhone" placeholder="No WA (0812...)">
                    </div>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <select class="form-select form-select-sm bg-light border-0" x-model="orderType">
                            <option value="retail">Retail / Takeaway</option>
                            <option value="dinein">Dine In (Makan Sini)</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <input type="text" class="form-control form-control-sm bg-light border-0" x-model="tableNumber"
                               placeholder="No Meja" x-bind:disabled="orderType !== 'dinein'">
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <select class="form-select form-select-sm bg-light border-0" x-model="paymentMethod">
                            <option value="cash">Tunai / Cash</option>
                            <option value="qris">QRIS</option>
                            <option value="transfer">Transfer Bank</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-tag"></i></span>
                            <input type="number" class="form-control bg-light border-0" x-model.number="discount"
                                   placeholder="Diskon">
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted small">Subtotal</span>
                    <span class="fw-bold text-dark small" x-text="'Rp ' + formatRupiah(subTotal)"></span>
                </div>

                <template x-if="discount > 0">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-danger small">Diskon</span>
                        <span class="fw-bold text-danger small" x-text="'- Rp ' + formatRupiah(discount)"></span>
                    </div>
                </template>

                <div class="d-flex justify-content-between align-items-center mb-2 mt-2 pt-2 border-top">
                    <span class="text-dark fw-bold">TOTAL</span>
                    <h4 class="fw-bolder text-primary mb-0" x-text="'Rp ' + formatRupiah(grandTotal)"></h4>
                </div>

                <template x-if="paymentMethod === 'cash'">
                    <div class="input-group input-group-sm mb-2">
                        <span class="input-group-text bg-white fw-bold border-primary text-primary">Bayar</span>
                        <input type="number" class="form-control border-primary" x-model.number="amountPaid"
                               placeholder="Ketik nominal bayar...">
                    </div>
                </template>

                <template x-if="paymentMethod === 'cash' && amountPaid && getChange >= 0">
                    <div
                        class="d-flex justify-content-between align-items-center mb-2 p-2 bg-success bg-opacity-10 rounded">
                        <span class="text-success small fw-bold">Kembalian</span>
                        <h6 class="fw-bold text-success mb-0" x-text="'Rp ' + formatRupiah(getChange)"></h6>
                    </div>
                </template>

                <div x-show="stockError" class="text-danger small fw-bold mb-2 text-center" x-text="stockError"></div>

                <button @click="submitOrder"
                        class="btn btn-primary btn-lg w-100 fw-bold rounded-pill shadow-sm d-flex justify-content-center align-items-center gap-2"
                        :disabled="cart.length === 0 || isSubmitting || stockError !== ''">
                    <i class="bi bi-check2-circle" x-show="!isSubmitting"></i>
                    <span class="spinner-border spinner-border-sm" x-show="isSubmitting"></span>
                    <span x-text="isSubmitting ? 'Memproses...' : 'Selesaikan Pembayaran'"></span>
                </button>
            </div>
        </div>
    </div>

    <div class="modal fade" id="variantModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 bg-light p-4 pb-3">
                    <div>
                        <h5 class="fw-bolder mb-1">Pilih Varian</h5>
                        <p class="text-muted small mb-0" x-text="selectedProduct ? selectedProduct.name : ''"></p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 pt-2">
                    <div class="list-group">
                        <template x-if="selectedProduct">
                            <template x-for="variant in selectedProduct.variants" :key="variant.id">
                                <button type="button"
                                        class="list-group-item list-group-item-action p-3 d-flex justify-content-between align-items-center rounded-3 mb-2 border shadow-sm"
                                        :class="{'opacity-50 bg-light': variant.stock <= 0}"
                                        :disabled="variant.stock <= 0"
                                        @click="if(variant.stock > 0) addVariantToCart(variant)">
                                    <div>
                                        <span class="fw-bold text-dark d-block" x-text="variant.name"></span>
                                        <span class="small"
                                              :class="variant.stock > 0 ? 'text-muted' : 'text-danger fw-bold'"
                                              x-text="'Stok: ' + variant.stock"></span>
                                    </div>
                                    <span class="fw-bolder text-primary"
                                          x-text="'Rp ' + formatRupiah(variant.price)"></span>
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
            <div class="modal-content border-0 shadow-lg rounded-4 text-center p-4">
                <div class="d-flex justify-content-center mb-3">
                    <div
                        class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 70px; height: 70px;">
                        <i class="bi bi-check-lg" style="font-size: 2.5rem;"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-dark mb-1">Transaksi Berhasil!</h4>
                <p class="text-muted small mb-3">Invoice: <span class="fw-bold text-dark"
                                                                x-text="lastOrder.invoice_code"></span></p>

                <div class="d-flex flex-column gap-2">
                    <template x-if="lastOrder.customer_phone">
                        <a :href="generateWaLink()" target="_blank"
                           class="btn btn-success fw-bold rounded-pill w-100 d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-whatsapp"></i> Kirim Struk ke WA
                        </a>
                    </template>

                    <template x-if="!lastOrder.customer_phone">
                        <div class="alert alert-light border small text-muted mb-2">
                            Isi No HP pelanggan jika ingin mengirim struk via WA.
                        </div>
                    </template>

                    <button type="button" @click="closeSuccessModal"
                            class="btn btn-light border fw-bold rounded-pill w-100">Buat Pesanan Baru
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
        successModalInstance: null,

        customerName: '',
        customerPhone: '',
        tableNumber: '',
        orderType: 'retail',
        paymentMethod: 'cash',
        discount: '',
        amountPaid: '',

        isSubmitting: false,
        stockError: '',
        lastOrder: {},

        init() {
            this.variantModalInstance = new bootstrap.Modal(document.getElementById('variantModal'));
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

        // Event listener dari komponen child
        handleProductClick(product) {
            if (!product.has_variants && product.stock <= 0) {
                showIslandToast('Stok barang ini sudah habis!', 'warning');
                return;
            }
            if (product.has_variants && product.variants.length > 0) {
                this.selectedProduct = product;
                this.variantModalInstance.show();
            } else {
                this.addToCart(product, null);
            }
        },

        addToCart(product, variant = null) {
            let variantId = variant ? variant.id : null;
            let price = variant ? variant.price : product.price;
            let maxStock = variant ? variant.stock : product.stock;

            let existing = this.cart.find(item => item.id === product.id && item.variant_id === variantId);

            if (existing) {
                if (existing.quantity < maxStock) {
                    existing.quantity++;
                    existing.subtotal = existing.quantity * price;
                } else {
                    showIslandToast(`Gagal! Sisa stok untuk item ini cuma ${maxStock}.`, 'warning');
                }
            } else {
                this.cart.push({
                    id: product.id,
                    variant_id: variantId,
                    name: product.name,
                    variant_name: variant ? variant.name : null,
                    price: price,
                    quantity: 1,
                    subtotal: price,
                    stock: maxStock, // Bawa info stok ke item keranjang
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
                    this.stockError = `Perhatian: Item ${item.name} melebihi batas stok (${item.stock}).`;
                    break;
                }
            }
        },

        // Format No HP (Ubah awalan 0 jadi 62 buat WA)
        formatPhoneForWA(phone) {
            let cleaned = ('' + phone).replace(/\D/g, ''); // Hapus semua selain angka
            if (cleaned.startsWith('0')) {
                return '62' + cleaned.substring(1);
            }
            if (!cleaned.startsWith('62')) {
                return '62' + cleaned;
            }
            return cleaned;
        },

        generateWaLink() {
            if (!this.lastOrder.customer_phone) return '#';

            let phone = this.formatPhoneForWA(this.lastOrder.customer_phone);
            // Link ini mengarah ke halaman public tracker milik tenant/toko
            let invoiceUrl = `${window.location.origin}/invoice/${this.lastOrder.invoice_code}`;

            let message = `Halo Kak *${this.lastOrder.customer_name}*,\n\nTerima kasih telah berbelanja di *${this.lastOrder.store_name}*.\nBerikut adalah link struk/invoice pesanan kakak:\n${invoiceUrl}\n\nTotal Belanja: Rp ${this.formatRupiah(this.lastOrder.total_price)}\n\nDitunggu pesanan selanjutnya ya kak!`;

            return `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;
        },

        closeSuccessModal() {
            this.successModalInstance.hide();
            // Reset Form Kasir
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
            if (this.cart.length === 0 || this.stockError !== '') return;
            this.isSubmitting = true;

            try {
                const result = await $wire.processCheckout(
                    this.cart, this.customerName, this.customerPhone, this.tableNumber,
                    this.orderType, this.paymentMethod, this.discount || 0, this.amountPaid
                );

                if (result && result.success) {
                    this.lastOrder = result;
                    Livewire.dispatchTo('tenant.pos.product-list', '$refresh');
                    this.successModalInstance.show();
                } else if (result && result.error) {
                    showIslandToast(result.error, 'danger');

                    // Refresh katalog biar layar kasir update stok terbarunya
                    Livewire.dispatchTo('tenant.pos.product-list', '$refresh');
                }
            } catch (error) {
                alert('Terjadi kesalahan sistem saat memproses transaksi.');
                console.error(error);
            }

            this.isSubmitting = false;
        },

        formatRupiah(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }
    }));
</script>
@endscript
