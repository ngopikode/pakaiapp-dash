{{-- ===== PREMIUM ONBOARDING BANNER & TOUR GUIDE (FIRST TIME ACCESS) ===== --}}

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
             window.dispatchEvent(new CustomEvent('start-pos-tour'));
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
     class="tour-guide-toast position-fixed bottom-0 start-0 m-3 m-md-4 p-3 shadow-lg border text-body"
     style="z-index: 1040; width: 320px; border-radius: 1.25rem; background: rgba(var(--bs-body-bg-rgb), 0.85); backdrop-filter: blur(12px); border-color: var(--bs-border-color-translucent) !important;">
     
    <style>
        @media (max-width: 768px) {
            .tour-guide-toast {
                bottom: calc(var(--bottom-nav-height, 65px) + env(safe-area-inset-bottom, 0px)) !important;
            }
        }
    </style>

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
                    style="background: #F97316; border: none; font-size: 0.75rem;">
                Buka Panduan <i class="bi bi-arrow-right ms-1"></i>
            </button>
        </div>
    </div>
</div>

<script>
    if (typeof window.posTourFn === 'undefined') {
        window.posTourFn = function(mode) {
            return {
                isActive: false,
                isPositioned: false,
                currentStep: 0,
                positioningTimeout: null,
                mode: mode,
                
                get steps() {
                    if (this.mode === 'retail') {
                        return [
                            {
                                target: '#tour-pos-search',
                                title: 'Pindai Barcode Langsung',
                                content: 'Arahkan scanner dan tembak barcode produk kapan saja tanpa perlu mengklik kolom pencarian. Sistem akan otomatis memasukkannya ke keranjang.',
                                position: 'bottom'
                            },
                            {
                                target: '[x-model.number="globalDiscount"]',
                                title: 'Diskon & Stok Ketat',
                                content: 'Masukkan nominal diskon per item secara langsung di dalam keranjang belanja. Jumlah stok akan otomatis terpotong saat transaksi selesai.',
                                position: 'left'
                            },
                            {
                                target: '[title="Daftar Tunda"]',
                                title: 'Tunda Pesanan (F8)',
                                content: 'Pelanggan belum selesai memilih? Klik Tunda untuk menyimpan keranjang secara aman di memori lokal, lalu panggil kembali melalui tombol Daftar.',
                                position: 'bottom'
                            },
                            {
                                target: '[x-model="customerPhone"]',
                                title: 'Struk Digital WhatsApp',
                                content: 'Kirimkan struk belanja secara digital ke WhatsApp pelanggan. Masukkan nomor WhatsApp di keranjang atau langsung setelah transaksi selesai.',
                                position: 'top'
                            }
                        ];
                    } else {
                        return [
                            {
                                target: '#tour-pos-search',
                                title: 'Cari Menu',
                                content: 'Gunakan kolom pencarian ini untuk menemukan menu dengan cepat saat pelanggan memesan.',
                                position: 'bottom'
                            },
                            {
                                target: '[title="Daftar Antrean"]',
                                title: 'Simpan & Pelunasan Antrean',
                                content: 'Simpan pesanan dengan tombol Simpan Sementara. Saat pelanggan siap membayar, buka Daftar Antrean dan lunasi.',
                                position: 'bottom'
                            },
                            {
                                target: '[x-model="isTaxActive"]',
                                title: 'Pajak PB1 & Service Charge',
                                content: 'Pajak resto (PB1 10%) dan biaya pelayanan (5%) dapat dikendalikan dengan sakelar (switch) di keranjang belanja.',
                                position: 'left'
                            }
                        ];
                    }
                },
                
                highlightStyle: '',
                tooltipStyle: '',

                init() {
                },

                handleResize() {
                    if (this.isActive) {
                        this.updatePositions(false);
                    }
                },

                cleanup() {
                    if (this.isActive) {
                        document.body.style.overflow = '';
                        this.isActive = false;
                        this.isPositioned = false;
                    }
                },

                get currentStepData() {
                    return this.steps[this.currentStep] || {};
                },

                startTour() {
                    if (this.isActive) return;
                    
                    this.currentStep = 0;
                    this.isActive = true;
                    this.isPositioned = false;
                    document.body.style.overflow = 'hidden';
                    
                    this.updatePositions();
                },

                endTour() {
                    this.isActive = false;
                    this.isPositioned = false;
                    document.body.style.overflow = '';
                    localStorage.setItem('pakaiapp_tutorial_dismissed', 'true');
                },

                nextStep() {
                    if (this.currentStep < this.steps.length - 1) {
                        this.isPositioned = false;
                        this.currentStep++;
                        this.updatePositions();
                    } else {
                        this.endTour();
                    }
                },

                prevStep() {
                    if (this.currentStep > 0) {
                        this.isPositioned = false;
                        this.currentStep--;
                        this.updatePositions();
                    }
                },

                async updatePositions(shouldScroll = true) {
                    const step = this.currentStepData;
                    if (!step) return;

                    let targetElement = document.querySelector(step.target);
                    let retries = 10; 

                    while (!targetElement && retries > 0) {
                        await new Promise(resolve => setTimeout(resolve, 200)); 
                        targetElement = document.querySelector(step.target);
                        retries--;
                    }

                    if (!targetElement) {
                        console.warn('POS Tour target not found, skipping:', step.target);
                        if (this.currentStep < this.steps.length - 1) {
                            this.currentStep++;
                            this.updatePositions(shouldScroll);
                        } else {
                            this.endTour();
                        }
                        return;
                    }

                    const rect = targetElement.getBoundingClientRect();
                    const isInViewport = (
                        rect.top >= 0 &&
                        rect.left >= 0 &&
                        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
                        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
                    );

                    if (!isInViewport && shouldScroll) {
                        targetElement.scrollIntoView({behavior: 'smooth', block: 'center'});
                    }

                    if (this.positioningTimeout) {
                        clearTimeout(this.positioningTimeout);
                    }

                    this.positioningTimeout = setTimeout(() => {
                        this.$nextTick(() => {
                            const freshRect = targetElement.getBoundingClientRect();
                            const padding = 12;

                            this.highlightStyle = `
                                top: ${freshRect.top - padding}px;
                                left: ${freshRect.left - padding}px;
                                width: ${freshRect.width + (padding * 2)}px;
                                height: ${freshRect.height + (padding * 2)}px;
                            `;

                            const tooltipEl = this.$refs.tooltipCard;
                            const tooltipRect = tooltipEl.getBoundingClientRect();
                            const tooltipWidth = tooltipRect.width || 340;
                            const tooltipHeight = tooltipRect.height || 200;

                            let tooltipTop, tooltipLeft;

                            tooltipLeft = freshRect.left + (freshRect.width / 2) - (tooltipWidth / 2);

                            if (tooltipLeft < 15) tooltipLeft = 15;
                            if (tooltipLeft + tooltipWidth > window.innerWidth - 15) {
                                tooltipLeft = window.innerWidth - tooltipWidth - 15;
                            }

                            const spaceBelow = window.innerHeight - freshRect.bottom - padding;
                            const spaceAbove = freshRect.top - padding;

                            let actualPosition = step.position;

                            if (actualPosition === 'bottom' && spaceBelow < tooltipHeight + 20) {
                                actualPosition = 'top';
                            } else if (actualPosition === 'top' && spaceAbove < tooltipHeight + 20) {
                                actualPosition = 'bottom';
                            }

                            if (actualPosition === 'top') {
                                tooltipTop = freshRect.top - padding - tooltipHeight - 15;
                            } else {
                                tooltipTop = freshRect.bottom + padding + 15;
                            }
                            
                            // Prevent overflowing right
                            if (actualPosition === 'left') {
                                tooltipTop = freshRect.top + (freshRect.height / 2) - (tooltipHeight / 2);
                                tooltipLeft = freshRect.left - padding - tooltipWidth - 15;
                                if (tooltipLeft < 15) { // Not enough space on left
                                    tooltipLeft = freshRect.right + padding + 15; // Move to right
                                }
                            }

                            this.tooltipStyle = `
                                top: ${tooltipTop}px;
                                left: ${tooltipLeft}px;
                            `;

                            this.isPositioned = true;
                        });
                    }, isInViewport ? 50 : 400); 
                }
            };
        };
    }
</script>

<div x-data="posTourFn('{{ $mode }}')" 
     x-on:start-pos-tour.window="startTour()"
     x-on:resize.window="handleResize()"
     x-on:livewire:navigating.window="cleanup()">
     
    <template x-teleport="body">
        <div x-show="isActive" 
             class="tour-guide-container" 
             style="display: none;">
             
            <!-- Backdrop -->
            <div class="tour-backdrop" x-show="isActive" x-transition.opacity @click="endTour()"></div>

            <!-- Highlight Hole -->
            <div class="tour-highlight"
                 :style="highlightStyle"
                 :class="isPositioned ? 'opacity-100 visible' : 'opacity-0 invisible'"></div>

            <!-- Tooltip Card -->
            <div x-ref="tooltipCard" class="tour-tooltip card shadow-lg border-0"
                 :style="tooltipStyle"
                 :class="isPositioned ? 'opacity-100 visible' : 'opacity-0 invisible'">
                <div class="card-body p-4 position-relative">
                    <div class="position-absolute top-0 start-0 w-100 h-100 overflow-hidden"
                         style="border-radius: 1rem; pointer-events: none; z-index: 0;">
                        <div class="position-absolute opacity-10"
                             style="background-color: var(--brand-caramel); width: 150px; height: 150px; border-radius: 50%; top: -50px; right: -50px; filter: blur(30px);"></div>
                    </div>

                    <div class="position-relative" style="z-index: 1;">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3"
                                 style="width: 40px; height: 40px; background-color: rgba(var(--brand-caramel-rgb), 0.1); color: var(--brand-caramel);">
                                <i class="bi bi-info-circle-fill fs-5"></i>
                            </div>
                            <h5 class="fw-bold text-body mb-0" x-text="currentStepData.title"
                                style="font-size: 1.1rem;"></h5>
                        </div>

                        <p class="text-secondary mb-4" x-text="currentStepData.content"
                           style="font-size: 0.95rem; line-height: 1.5;"></p>

                        <div class="d-flex justify-content-between align-items-center pt-3 border-top"
                             style="border-color: var(--bs-border-color) !important;">
                            <div class="d-flex gap-1">
                                <template x-for="(step, idx) in steps" :key="index">
                                    <div class="rounded-pill transition-all"
                                         :class="idx === currentStep ? 'bg-primary' : 'bg-secondary bg-opacity-25'"
                                         :style="idx === currentStep ? 'width: 16px; height: 6px; background-color: var(--brand-caramel) !important;' : 'width: 6px; height: 6px;'">
                                    </div>
                                </template>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm fw-bold text-muted transition-all hover-opacity"
                                        @click="endTour()">
                                    Mengerti
                                </button>
                                <button type="button" x-show="currentStep > 0"
                                        class="btn btn-sm btn-light fw-bold rounded-pill px-3 transition-all"
                                        @click="prevStep()">Kembali
                                </button>
                                <button type="button"
                                        class="btn btn-sm btn-primary fw-bold rounded-pill px-4 transition-all"
                                        style="background-color: var(--brand-caramel) !important; border-color: var(--brand-caramel) !important;"
                                        @click="nextStep()"
                                        x-text="currentStep === steps.length - 1 ? 'Selesai' : 'Lanjut'"></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
<div class="modal fade" id="tutorialModal" tabindex="-1" aria-hidden="true" wire:ignore>
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg d-flex flex-column bg-body text-body"
             style="border-radius: 1.5rem; max-height: 90vh; border-color: var(--bs-border-color-translucent) !important;">

            {{-- Header (Premium Gradient) --}}
            <div class="modal-header border-bottom px-4 py-3 flex-shrink-0 text-white"
                 style="border-radius: 1.5rem 1.5rem 0 0; background: #F97316; border: none;">
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
                        <!-- Step 1: Meja & Pelanggan -->
                        <div class="col-md-6">
                            <div class="card h-100 p-3 border shadow-sm bg-body-tertiary"
                                 style="border-radius: 1.25rem; border-color: var(--bs-border-color-translucent) !important;">
                                <div class="d-flex gap-3 align-items-start">
                                    <div
                                        class="bg-primary bg-opacity-10 text-primary rounded-4 d-flex align-items-center justify-content-center p-2.5"
                                        style="width: 48px; height: 48px;">
                                        <i class="bi bi-people fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1 text-body">Nomor Meja & Pelanggan</h6>
                                        <p class="text-secondary small mb-0" style="font-size: 0.8rem;">Jangan lupa isi nomor meja pelanggan saat mereka makan di tempat (Dine-in). Anda juga bisa menyimpan nomor HP pelanggan untuk keperluan keanggotaan.
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
                    style="background: #F97316; border: none; font-size: 0.75rem;">
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
