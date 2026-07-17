<div x-data="{
         orderId: null,
         note: '',
         customMode: false,
         isSubmitting: false,
         isOpen: false,
         reasons: [
             'Pelanggan berubah pikiran',
             'Pembayaran tidak cukup',
             'Stok bahan/produk habis',
             'Kesalahan input kasir',
             'Waktu tunggu terlalu lama'
         ],
         init() {
             window.addEventListener('close-cancel-modal', () => {
                 this.isSubmitting = false;
                 this.isOpen = false;
             });
         },
         openModal(orderId) {
             this.orderId = orderId;
             this.note = '';
             this.customMode = false;
             this.isSubmitting = false;
             this.isOpen = true;
         },
         confirmCancel() {
             if (!this.note.trim()) {
                 if (typeof showIslandToast !== 'undefined') {
                     showIslandToast('Pilih atau ketik alasan pembatalan!', 'warning');
                 } else {
                     alert('Pilih atau ketik alasan pembatalan!');
                 }
                 return;
             }
             this.isSubmitting = true;
             this.$dispatch('cancel-confirmed', { orderId: this.orderId, note: this.note });
         }
     }"
     @open-cancel-modal.window="openModal($event.detail.orderId)"
     x-show="isOpen"
     style="display: none;"
     class="relative z-[9999]"
     aria-labelledby="modal-title"
     role="dialog"
     aria-modal="true">

    <!-- Backdrop -->
    <div x-show="isOpen"
         x-transition:enter="ease-out duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>

    <!-- Modal Container -->
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            
            <!-- Modal Panel -->
            <div x-show="isOpen"
                 x-transition:enter="ease-out duration-150"
                 x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-100"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-[24px] bg-card border border-border shadow-2xl transition-all sm:my-8 w-full max-w-[420px] flex flex-col p-6 sm:p-8">
                
                <!-- Icon Header -->
                <div class="mx-auto flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-full bg-destructive/10 ring-8 ring-destructive/5 mb-6">
                    <i class="ph-fill ph-warning-circle text-3xl text-destructive"></i>
                </div>

                <!-- Text Header -->
                <div class="text-center mb-6">
                    <h3 class="text-xl font-extrabold text-foreground tracking-tight mb-2" id="modal-title">Batalkan Pesanan?</h3>
                    <p class="text-sm text-muted-foreground font-medium px-2">
                        Aksi ini tidak dapat dikembalikan. Stok akan dipulihkan secara otomatis.
                    </p>
                </div>

                <!-- Reasons Options -->
                <div class="flex flex-col gap-2.5 mb-6">
                    <template x-for="reason in reasons" :key="reason">
                        <label class="relative flex cursor-pointer items-center gap-3 rounded-2xl border-2 p-3.5 transition-all duration-200 group"
                               :class="note === reason && !customMode ? 'border-destructive bg-destructive/5 shadow-sm' : 'border-transparent bg-secondary/60 hover:bg-secondary'">
                            <input type="radio" name="cancel_reason" :value="reason" class="peer sr-only" @click="note = reason; customMode = false">
                            
                            <!-- Custom Radio Box -->
                            <div class="flex h-5 w-5 items-center justify-center rounded-full border-2 transition-colors"
                                 :class="note === reason && !customMode ? 'border-destructive bg-destructive' : 'border-muted-foreground/30 bg-background group-hover:border-muted-foreground/50'">
                                <i class="ph-bold ph-check text-xs text-white scale-0 transition-transform"
                                   :class="note === reason && !customMode ? 'scale-100' : 'scale-0'"></i>
                            </div>
                            
                            <span class="text-[14px] font-semibold transition-colors"
                                  :class="note === reason && !customMode ? 'text-destructive' : 'text-foreground'">
                                <span x-text="reason"></span>
                            </span>
                        </label>
                    </template>

                    <!-- Custom Option -->
                    <label class="relative flex cursor-pointer items-center gap-3 rounded-2xl border-2 p-3.5 transition-all duration-200 group"
                           :class="customMode ? 'border-destructive bg-destructive/5 shadow-sm' : 'border-transparent bg-secondary/60 hover:bg-secondary'">
                        <input type="radio" name="cancel_reason" value="custom" class="peer sr-only" @click="customMode = true; note = ''">
                        
                        <!-- Custom Radio Box -->
                        <div class="flex h-5 w-5 items-center justify-center rounded-full border-2 transition-colors"
                             :class="customMode ? 'border-destructive bg-destructive' : 'border-muted-foreground/30 bg-background group-hover:border-muted-foreground/50'">
                            <i class="ph-bold ph-check text-xs text-white scale-0 transition-transform"
                               :class="customMode ? 'scale-100' : 'scale-0'"></i>
                        </div>
                        
                        <span class="text-[14px] font-semibold transition-colors"
                              :class="customMode ? 'text-destructive' : 'text-foreground'">
                            Alasan Lainnya...
                        </span>
                    </label>
                </div>

                <!-- Custom Textarea (Animated) -->
                <div x-show="customMode" x-collapse x-cloak class="mb-6">
                    <textarea x-model="note"
                              class="w-full rounded-2xl border-2 border-transparent bg-secondary/60 px-4 py-3.5 text-sm font-medium text-foreground placeholder:text-muted-foreground/60 transition-all outline-none resize-none focus:border-destructive/30 focus:bg-background focus:ring-4 focus:ring-destructive/10"
                              rows="3"
                              placeholder="Ketik alasan pembatalan..."></textarea>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3 w-full">
                    <button type="button" @click="isOpen = false"
                            class="flex-1 rounded-2xl border border-border bg-card px-4 py-3.5 text-[14px] font-bold text-muted-foreground transition-colors hover:bg-secondary hover:text-foreground">
                        Batal
                    </button>
                    <button type="button" @click="confirmCancel()" :disabled="isSubmitting"
                            class="flex-[2] flex items-center justify-center gap-2 rounded-2xl bg-destructive px-4 py-3.5 text-[14px] font-bold text-destructive-foreground shadow-lg shadow-destructive/20 transition-all hover:bg-destructive/90 active:scale-[0.98]">
                        <span x-show="!isSubmitting">Batalkan Pesanan</span>
                        <i x-show="isSubmitting" class="ph-bold ph-spinner animate-spin text-lg"></i>
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>
