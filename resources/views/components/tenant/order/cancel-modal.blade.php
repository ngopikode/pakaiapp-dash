<div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-hidden="true"
     x-data="{
         orderId: null,
         note: '',
         customMode: false,
         isSubmitting: false,
         modalInstance: null,
         reasons: [
             'Pelanggan tidak jadi / berubah pikiran',
             'Uang pembayaran tidak cukup',
             'Stok bahan baku/produk habis',
             'Kesalahan input oleh kasir',
             'Waktu tunggu pesanan terlalu lama'
         ],

         init() {
             this.modalInstance = new bootstrap.Modal(document.getElementById('cancelOrderModal'));
             window.addEventListener('close-cancel-modal', () => {
                 this.isSubmitting = false;
                 this.modalInstance.hide();
             });
         },

         openModal(orderId) {
             this.orderId = orderId;
             this.note = '';
             this.customMode = false;
             this.isSubmitting = false;
             this.modalInstance.show();
         },

         confirmCancel() {
             if (!this.note.trim()) {
                 if (typeof showIslandToast !== 'undefined') {
                     showIslandToast('Silakan pilih atau isi alasan pembatalan!', 'warning');
                 } else {
                     alert('Silakan pilih atau isi alasan pembatalan!');
                 }
                 return;
             }
             this.isSubmitting = true;
             this.$dispatch('cancel-confirmed', { orderId: this.orderId, note: this.note });
         }
     }"
     @open-cancel-modal.window="openModal($event.detail.orderId)">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg bg-body text-body"
             style="border-radius: 1.25rem; border: 1px solid var(--bs-border-color-translucent) !important;">

            {{-- Header Modal - Menggunakan merah transparan agar tetap terlihat alert di mode gelap --}}
            <div class="modal-header border-bottom-0 p-4" style="background: rgba(220, 53, 69, 0.1);">
                <h5 class="modal-title fw-bold text-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Batalkan Pesanan
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4 bg-body">
                <p class="text-secondary small mb-4 fw-medium opacity-75">Silakan pilih alasan pembatalan pesanan ini.
                    Aksi ini bersifat permanen dan stok akan dikembalikan ke inventaris.</p>

                <div class="d-flex flex-column gap-2 mb-3">
                    <template x-for="reason in reasons" :key="reason">
                        <button type="button" @click="note = reason; customMode = false"
                                :class="note === reason && !customMode ? 'btn-danger shadow-sm text-white' : 'btn-outline-secondary bg-body-tertiary text-body'"
                                class="btn text-start rounded-3 fw-medium px-3 py-2 transition-all border"
                                style="border-color: var(--bs-border-color-translucent) !important;">
                            <i class="bi bi-check-circle-fill me-2" x-show="note === reason && !customMode"></i>
                            <i class="bi bi-circle me-2" x-show="note !== reason || customMode"></i>
                            <span x-text="reason"></span>
                        </button>
                    </template>

                    <button type="button" @click="customMode = true; note = ''"
                            :class="customMode ? 'btn-danger shadow-sm text-white' : 'btn-outline-secondary bg-body-tertiary text-body'"
                            class="btn text-start rounded-3 fw-medium px-3 py-2 transition-all border"
                            style="border-color: var(--bs-border-color-translucent) !important;">
                        <i class="bi bi-pencil-fill me-2" x-show="customMode"></i>
                        <i class="bi bi-circle me-2" x-show="!customMode"></i>
                        Alasan Lainnya...
                    </button>
                </div>

                <div x-show="customMode" x-collapse x-cloak>
                    <textarea x-model="note"
                              class="form-control rounded-3 mt-2 border-danger border-opacity-25 shadow-sm bg-body-tertiary text-body"
                              rows="3"
                              placeholder="Ketik alasan pembatalan di sini..."></textarea>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top"
                     style="border-color: var(--bs-border-color-translucent) !important;">
                    <button type="button" class="btn btn-secondary border bg-body text-body fw-bold px-4 rounded-3"
                            data-bs-dismiss="modal">Kembali
                    </button>
                    <button type="button" @click="confirmCancel()"
                            class="btn btn-danger fw-bold px-4 rounded-3 shadow-sm d-flex align-items-center gap-2 text-white">
                        <span x-show="!isSubmitting">Batalkan Sekarang</span>
                        <span x-show="isSubmitting" class="spinner-border spinner-border-sm"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
