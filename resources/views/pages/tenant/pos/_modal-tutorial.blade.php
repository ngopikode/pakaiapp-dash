{{-- ===== PREMIUM TUTORIAL & HELP MODAL ===== --}}
<div class="modal fade" id="tutorialModal" tabindex="-1" aria-hidden="true" wire:ignore>
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg d-flex flex-column bg-body text-body"
             style="border-radius: 1.5rem; max-height: 90vh; border-color: var(--bs-border-color-translucent) !important;">

            {{-- Header (Premium Gradient) --}}
            <div class="modal-header border-bottom px-4 py-3 flex-shrink-0 text-white"
                 style="border-radius: 1.5rem 1.5rem 0 0; background: linear-gradient(135deg, #ca8a04, #b45309); border: none;">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-journal-bookmark-fill fs-4"></i>
                    <h5 class="fw-bold mb-0">Panduan & Tutorial Penggunaan</h5>
                </div>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>

            {{-- Body (Premium Glassmorphism Tabs and Cards) --}}
            <div class="modal-body p-4 bg-body overflow-y-auto">

                @if($mode === 'retail')
                    {{-- ===== RETAIL MODE TUTORIAL ===== --}}
                    <div class="text-center mb-4">
                        <span
                            class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill fw-bold px-3 py-1.5 mb-2"
                            style="font-size: 0.8rem;">
                            <i class="bi bi-shop-window me-1"></i>Mode Ritel & Penjualan Cepat
                        </span>
                        <h4 class="fw-bold text-body">Mulai Transaksi Ritelmu 🚀</h4>
                        <p class="text-secondary small max-w-lg mx-auto">Ikuti langkah mudah di bawah ini untuk
                            mengoperasikan kasir ritel secara cepat, efisien, dan presisi.</p>
                    </div>

                    <div class="row g-3">
                        <!-- Step 1: Scanner Barcode -->
                        <div class="col-md-6">
                            <div class="card h-100 p-3 border shadow-sm bg-body-tertiary"
                                 style="border-radius: 1.25rem; border-color: var(--bs-border-color-translucent) !important;">
                                <div class="d-flex gap-3 align-items-start">
                                    <div
                                        class="bg-primary bg-opacity-10 text-primary rounded-4 d-flex align-items-center justify-content-center p-2.5"
                                        style="width: 48px; height: 48px;">
                                        <i class="bi bi-qr-code-scan fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1 text-body">Pindai Barcode Langsung</h6>
                                        <p class="text-secondary small mb-0" style="font-size: 0.8rem;">Arahkan scanner
                                            dan tembak barcode produk kapan saja tanpa perlu mengklik kolom pencarian.
                                            Sistem akan otomatis memasukkannya ke keranjang.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Diskon Per-item -->
                        <div class="col-md-6">
                            <div class="card h-100 p-3 border shadow-sm bg-body-tertiary"
                                 style="border-radius: 1.25rem; border-color: var(--bs-border-color-translucent) !important;">
                                <div class="d-flex gap-3 align-items-start">
                                    <div
                                        class="bg-warning bg-opacity-10 text-warning rounded-4 d-flex align-items-center justify-content-center p-2.5"
                                        style="width: 48px; height: 48px;">
                                        <i class="bi bi-tag fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1 text-body">Diskon & Stok Ketat</h6>
                                        <p class="text-secondary small mb-0" style="font-size: 0.8rem;">Masukkan nominal
                                            diskon per item secara langsung di dalam keranjang belanja. Jumlah stok akan
                                            otomatis terpotong saat transaksi selesai.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Fitur Tunda (Hold) -->
                        <div class="col-md-6">
                            <div class="card h-100 p-3 border shadow-sm bg-body-tertiary"
                                 style="border-radius: 1.25rem; border-color: var(--bs-border-color-translucent) !important;">
                                <div class="d-flex gap-3 align-items-start">
                                    <div
                                        class="bg-success bg-opacity-10 text-success rounded-4 d-flex align-items-center justify-content-center p-2.5"
                                        style="width: 48px; height: 48px;">
                                        <i class="bi bi-pause-circle fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1 text-body">Tunda Pesanan (F8)</h6>
                                        <p class="text-secondary small mb-0" style="font-size: 0.8rem;">Pelanggan belum
                                            selesai memilih? Klik <strong>Tunda</strong> untuk menyimpan keranjang
                                            secara aman di memori lokal, lalu panggil kembali melalui tombol <strong>Daftar</strong>.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 4: Struk Digital WA -->
                        <div class="col-md-6">
                            <div class="card h-100 p-3 border shadow-sm bg-body-tertiary"
                                 style="border-radius: 1.25rem; border-color: var(--bs-border-color-translucent) !important;">
                                <div class="d-flex gap-3 align-items-start">
                                    <div
                                        class="bg-info bg-opacity-10 text-info rounded-4 d-flex align-items-center justify-content-center p-2.5"
                                        style="width: 48px; height: 48px;">
                                        <i class="bi bi-whatsapp fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1 text-body">Struk Digital WhatsApp</h6>
                                        <p class="text-secondary small mb-0" style="font-size: 0.8rem;">Kirimkan struk
                                            belanja secara digital ke WhatsApp pelanggan. Masukkan nomor WhatsApp di
                                            keranjang atau langsung setelah transaksi selesai.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Keyboard Shortcuts Section --}}
                    <div class="mt-4 p-3 rounded-4"
                         style="background: rgba(var(--bs-primary-rgb), 0.05); border: 1px dashed rgba(var(--bs-primary-rgb), 0.3);">
                        <h6 class="fw-bold mb-2 text-primary"><i class="bi bi-keyboard me-2"></i>Pintasan Cepat Keyboard
                            (Keyboard Hotkeys)</h6>
                        <div class="row g-2 text-secondary" style="font-size: 0.8rem;">
                            <div class="col-md-4">
                                <span
                                    class="badge bg-body border text-secondary py-2 w-100 text-start d-flex justify-content-between align-items-center">
                                    <span><kbd
                                            class="bg-dark text-white px-1 rounded small">F2</kbd> Lanjut Bayar</span>
                                    <i class="bi bi-cash-coin text-primary"></i>
                                </span>
                            </div>
                            <div class="col-md-4">
                                <span
                                    class="badge bg-body border text-secondary py-2 w-100 text-start d-flex justify-content-between align-items-center">
                                    <span><kbd class="bg-dark text-white px-1 rounded small">F4</kbd> Batal / Bersihkan</span>
                                    <i class="bi bi-trash3 text-danger"></i>
                                </span>
                            </div>
                            <div class="col-md-4">
                                <span
                                    class="badge bg-body border text-secondary py-2 w-100 text-start d-flex justify-content-between align-items-center">
                                    <span><kbd
                                            class="bg-dark text-white px-1 rounded small">F8</kbd> Tunda / Daftar</span>
                                    <i class="bi bi-pause-circle text-warning"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- ===== RESTO MODE TUTORIAL ===== --}}
                    <div class="text-center mb-4">
                        <span
                            class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill fw-bold px-3 py-1.5 mb-2"
                            style="font-size: 0.8rem; color: #b45309 !important;">
                            <i class="bi bi-cup-hot me-1"></i>Mode Restoran & Cafe (F&B)
                        </span>
                        <h4 class="fw-bold text-body">Kelola Pesanan Meja & Dapur 🍽️</h4>
                        <p class="text-secondary small max-w-lg mx-auto">Aliran transaksi restoran dari meja pelanggan,
                            catatan pesanan ke dapur, hingga pelunasan di kasir secara teratur.</p>
                    </div>

                    <div class="row g-3">
                        <!-- Step 1: Kitchen Notes -->
                        <div class="col-md-6">
                            <div class="card h-100 p-3 border shadow-sm bg-body-tertiary"
                                 style="border-radius: 1.25rem; border-color: var(--bs-border-color-translucent) !important;">
                                <div class="d-flex gap-3 align-items-start">
                                    <div
                                        class="bg-primary bg-opacity-10 text-primary rounded-4 d-flex align-items-center justify-content-center p-2.5"
                                        style="width: 48px; height: 48px;">
                                        <i class="bi bi-chat-dots fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1 text-body">Kitchen Notes (Catatan Dapur)</h6>
                                        <p class="text-secondary small mb-0" style="font-size: 0.8rem;">Tuliskan catatan
                                            khusus per menu (seperti <em>"tanpa es"</em> atau <em>"tidak pedas"</em>)
                                            langsung dari kolom input catatan di bawah setiap item keranjang belanja.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Antrean Pending -->
                        <div class="col-md-6">
                            <div class="card h-100 p-3 border shadow-sm bg-body-tertiary"
                                 style="border-radius: 1.25rem; border-color: var(--bs-border-color-translucent) !important;">
                                <div class="d-flex gap-3 align-items-start">
                                    <div
                                        class="bg-warning bg-opacity-10 text-warning rounded-4 d-flex align-items-center justify-content-center p-2.5"
                                        style="width: 48px; height: 48px;">
                                        <i class="bi bi-hourglass-split fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1 text-body">Simpan Antrean (Dapur)</h6>
                                        <p class="text-secondary small mb-0" style="font-size: 0.8rem;">Gunakan tombol
                                            <strong>Simpan Antrian</strong> jika pelanggan memesan terlebih dahulu dan
                                            akan membayar nanti setelah selesai makan. Dapur dapat langsung melihat
                                            catatan.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Pajak & Layanan Dinamis -->
                        <div class="col-md-6">
                            <div class="card h-100 p-3 border shadow-sm bg-body-tertiary"
                                 style="border-radius: 1.25rem; border-color: var(--bs-border-color-translucent) !important;">
                                <div class="d-flex gap-3 align-items-start">
                                    <div
                                        class="bg-success bg-opacity-10 text-success rounded-4 d-flex align-items-center justify-content-center p-2.5"
                                        style="width: 48px; height: 48px;">
                                        <i class="bi bi-percent fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1 text-body">Pajak PB1 & Service Charge</h6>
                                        <p class="text-secondary small mb-0" style="font-size: 0.8rem;">Pajak resto (PB1
                                            10%) dan biaya pelayanan (5%) otomatis dihitung. Anda dapat mematikan atau
                                            menyalakan keduanya lewat sakelar (switch) di keranjang.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 4: Pelunasan Antrean -->
                        <div class="col-md-6">
                            <div class="card h-100 p-3 border shadow-sm bg-body-tertiary"
                                 style="border-radius: 1.25rem; border-color: var(--bs-border-color-translucent) !important;">
                                <div class="d-flex gap-3 align-items-start">
                                    <div
                                        class="bg-info bg-opacity-10 text-info rounded-4 d-flex align-items-center justify-content-center p-2.5"
                                        style="width: 48px; height: 48px;">
                                        <i class="bi bi-cash-coin fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1 text-body">Pelunasan Pesanan Pending</h6>
                                        <p class="text-secondary small mb-0" style="font-size: 0.8rem;">Ketika pelanggan
                                            siap membayar, buka tab <strong>Daftar Antrean</strong>, cari invoice
                                            mereka, dan klik <strong>Bayar Sekarang</strong> untuk menyelesaikan
                                            pelunasan.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Keyboard Shortcuts Section --}}
                    <div class="mt-4 p-3 rounded-4"
                         style="background: rgba(var(--bs-primary-rgb), 0.05); border: 1px dashed rgba(var(--bs-primary-rgb), 0.3);">
                        <h6 class="fw-bold mb-2 text-primary"><i class="bi bi-keyboard me-2"></i>Pintasan Cepat Keyboard
                            (Keyboard Hotkeys)</h6>
                        <div class="row g-2 text-secondary" style="font-size: 0.8rem;">
                            <div class="col-6 col-md-3">
                                <span
                                    class="badge bg-body border text-secondary py-2 w-100 text-start d-flex justify-content-between align-items-center">
                                    <span><kbd
                                            class="bg-dark text-white px-1 rounded small">F2</kbd> Bayar Langsung</span>
                                    <i class="bi bi-lightning text-danger"></i>
                                </span>
                            </div>
                            <div class="col-6 col-md-3">
                                <span
                                    class="badge bg-body border text-secondary py-2 w-100 text-start d-flex justify-content-between align-items-center">
                                    <span><kbd
                                            class="bg-dark text-white px-1 rounded small">F3</kbd> Simpan Antrean</span>
                                    <i class="bi bi-hourglass-split text-warning"></i>
                                </span>
                            </div>
                            <div class="col-6 col-md-3">
                                <span
                                    class="badge bg-body border text-secondary py-2 w-100 text-start d-flex justify-content-between align-items-center">
                                    <span><kbd class="bg-dark text-white px-1 rounded small">F4</kbd> Bersihkan</span>
                                    <i class="bi bi-trash3 text-danger"></i>
                                </span>
                            </div>
                            <div class="col-6 col-md-3">
                                <span
                                    class="badge bg-body border text-secondary py-2 w-100 text-start d-flex justify-content-between align-items-center">
                                    <span><kbd class="bg-dark text-white px-1 rounded small">F8</kbd> Pindah Tab</span>
                                    <i class="bi bi-arrow-left-right text-primary"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                @endif

            </div>

            {{-- Footer --}}
            <div class="modal-footer bg-body-tertiary border-top p-3 flex-shrink-0"
                 style="border-radius: 0 0 1.5rem 1.5rem; border-color: var(--bs-border-color-translucent) !important;">
                <button type="button"
                        class="btn btn-secondary border fw-bold rounded-pill px-4 shadow-sm bg-body text-body"
                        data-bs-dismiss="modal">
                    Mengerti, Siap! 👍
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ===== PREMIUM ONBOARDING BANNER (FIRST TIME ACCESS) ===== --}}
<div x-data="{
         showBanner: false,
         init() {
             setTimeout(() => {
                 if (!localStorage.getItem('pakaiapp_tutorial_dismissed')) {
                     this.showBanner = true;
                 }
             }, 2000);
         },
         dismiss() {
             this.showBanner = false;
             localStorage.setItem('pakaiapp_tutorial_dismissed', 'true');
         },
         openGuide() {
             this.dismiss();
             const modalEl = document.getElementById('tutorialModal');
             if (modalEl) {
                 const inst = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                 inst.show();
             }
         }
     }"
     @tutorial-opened.window="showBanner = false"
     x-show="showBanner"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-4 scale-95"
     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
     x-transition:leave-end="opacity-0 translate-y-4 scale-95"
     class="position-fixed bottom-0 start-0 m-3 m-md-4 p-3 shadow-lg border text-body"
     style="z-index: 1040; width: 320px; border-radius: 1.25rem; background: rgba(var(--bs-body-bg-rgb), 0.85); backdrop-filter: blur(12px); border-color: var(--bs-border-color-translucent) !important;">

    <div class="d-flex align-items-start gap-3">
        <div
            class="bg-warning bg-opacity-10 text-warning rounded-4 d-flex align-items-center justify-content-center flex-shrink-0"
            style="width: 42px; height: 42px; color: #ca8a04 !important;">
            <i class="bi bi-lightbulb fs-4"></i>
        </div>
        <div class="flex-grow-1">
            <div class="d-flex justify-content-between align-items-start">
                <h6 class="fw-bold mb-1 text-body small">Pertama Kali di Sini? 👋</h6>
                <button @click="dismiss()" class="btn-close shadow-none" style="font-size: 0.75rem;"
                        aria-label="Close"></button>
            </div>
            <p class="text-secondary mb-2" style="font-size: 0.75rem; line-height: 1.35;">
                Pelajari cara cepat menggunakan halaman kasir ini melalui panduan interaktif kami.
            </p>
            <button @click="openGuide()" class="btn btn-warning btn-sm fw-bold rounded-pill w-100 text-white"
                    style="background: linear-gradient(135deg, #ca8a04, #b45309); border: none; font-size: 0.75rem;">
                Buka Panduan <i class="bi bi-arrow-right ms-1"></i>
            </button>
        </div>
    </div>
</div>

{{-- Event listener untuk menampilkan toast penunjuk saat modal panduan ditutup --}}
<script>
    if (!window.hasTutorialCloseListener) {
        window.hasTutorialCloseListener = true;
        document.addEventListener('hidden.bs.modal', (event) => {
            if (event.target && event.target.id === 'tutorialModal') {
                if (typeof showIslandToast === 'function') {
                    showIslandToast('💡 Butuh panduan lagi? Klik tombol (?) di sebelah tab navigasi kasir!', 'info');
                }
            }
        });
    }
</script>
