<div class="modal fade" id="successModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 text-center p-4 shadow"
             style="border-radius: 1.5rem; background-color: var(--bs-body-bg); font-family: 'Open Sans', sans-serif;">
             
            {{-- Success Icon with subtle pulse animation effect --}}
            <div class="d-flex justify-content-center mb-4 mt-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                     style="width: 88px; height: 88px; background: linear-gradient(135deg, rgba(var(--bs-primary-rgb), 0.15), rgba(var(--bs-primary-rgb), 0.05)); border: 2px solid rgba(var(--bs-primary-rgb), 0.2);">
                    <i class="bi bi-check-circle-fill" style="font-size: 3.5rem; color: var(--bs-primary);"></i>
                </div>
            </div>
            
            <h4 class="fw-bolder mb-1" style="font-family: 'Poppins', sans-serif; color: var(--bs-body-color);">Pembayaran Berhasil!</h4>
            <p class="text-secondary small mb-4">No. Invoice: <span class="fw-bold px-2 py-1 rounded" style="background-color: var(--bs-tertiary-bg); color: var(--bs-body-color);" x-text="lastOrder.invoice_code"></span></p>

            <div class="d-flex flex-column gap-3 mb-2">
                {{-- Phone Input area for Whatsapp --}}
                <div class="text-start">
                    <label class="small fw-semibold mb-2" style="color: var(--bs-secondary-color);">Kirim Struk WhatsApp (Opsional)</label>
                    <div class="input-group shadow-sm" style="border-radius: 0.75rem; overflow: hidden; border: 1px solid var(--bs-border-color-translucent);">
                        <span class="input-group-text bg-body border-0" style="color: var(--bs-primary);">
                            <i class="bi bi-telephone"></i>
                        </span>
                        <input type="text" class="form-control bg-body border-0 shadow-none px-2"
                               x-model="lastOrder.customer_phone" placeholder="Cth: 0812..."
                               style="color: var(--bs-body-color); font-size: 0.95rem;">
                    </div>
                </div>

                <template x-if="lastOrder.customer_phone && lastOrder.customer_phone.length >= 9">
                    <button type="button" @click="sendWa"
                            class="btn fw-bold p-3 d-flex align-items-center justify-content-center gap-2 shadow-sm text-white"
                            style="border-radius: 1rem; background: linear-gradient(135deg, #25D366, #128C7E); border: none; transition: all 0.2s ease;"
                            onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                        <i class="bi bi-whatsapp fs-5"></i> Kirim Struk via WA
                    </button>
                </template>

                <div class="d-flex gap-2">
                    <button type="button" @click="window.open('/invoice/' + lastOrder.invoice_code, '_blank')"
                            class="btn flex-grow-1 fw-bold p-3 d-flex align-items-center justify-content-center gap-2"
                            style="border-radius: 1rem; background-color: var(--bs-tertiary-bg); color: var(--bs-body-color); border: 1px solid var(--bs-border-color-translucent); transition: all 0.2s ease;"
                            onmouseover="this.style.backgroundColor='var(--bs-secondary-bg)'; this.style.transform='scale(1.02)'" 
                            onmouseout="this.style.backgroundColor='var(--bs-tertiary-bg)'; this.style.transform='scale(1)'">
                        <i class="bi bi-printer fs-5"></i> Cetak Struk
                    </button>
                </div>

                <button type="button" @click="closeSuccessModal"
                        class="btn fw-bold p-3 mt-2 text-white shadow-sm"
                        style="border-radius: 1rem; background: linear-gradient(135deg, var(--bs-primary), var(--brand-accent-dark)); font-family: 'Poppins', sans-serif; letter-spacing: 0.5px; transition: all 0.2s ease;"
                        onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                    Tutup & Pesanan Baru
                </button>
            </div>
        </div>
    </div>
</div>
