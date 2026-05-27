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
                    const isMobile = window.innerWidth <= 991;
                    if (this.mode === 'retail') {
                        let arr = [
                            {
                                target: '#tour-pos-search',
                                title: 'Pencarian & Barcode',
                                content: 'Arahkan scanner barcode produk kapan saja tanpa perlu mengklik kolom pencarian, atau ketik nama produk secara manual di sini.',
                                position: 'bottom'
                            },
                            {
                                target: '.tour-product-item',
                                title: 'Pilih Produk & Varian',
                                content: 'Klik produk pada daftar ini untuk memasukkannya ke keranjang. Jika produk memiliki varian, popup pilihan akan muncul secara otomatis.',
                                position: 'right'
                            }
                        ];

                        if (isMobile) {
                            arr.push({
                                target: '.floating-cart-btn',
                                title: 'Tombol Keranjang',
                                content: 'Di layar HP, keranjang belanja disembunyikan di sini. Kami akan otomatis membukanya untuk Anda di langkah selanjutnya. Klik Lanjut.',
                                position: 'top'
                            });
                        }

                        return arr.concat([
                            {
                                target: '#tour-cart-items',
                                title: 'Kelola Keranjang Belanja',
                                content: 'Di sini Anda bisa mengubah kuantitas (+/-), menghapus produk, atau memberikan diskon khusus per-item sesuai kebutuhan pelanggan.',
                                position: 'left'
                            },
                            {
                                target: '[title="Daftar Tunda"]',
                                title: 'Tunda Pesanan (F8)',
                                content: 'Pelanggan belum selesai memilih? Klik Tunda untuk menyimpan keranjang secara aman di memori lokal, lalu panggil kembali melalui tombol Daftar.',
                                position: 'left'
                            },
                            {
                                target: '[x-model="customerPhone"]',
                                title: 'Struk Digital WhatsApp',
                                content: 'Kirimkan struk belanja secara digital ke WhatsApp pelanggan. Masukkan nomor WhatsApp di keranjang atau langsung setelah transaksi selesai.',
                                position: 'left'
                            },
                            {
                                target: '[x-model\\.number="globalDiscount"]',
                                title: 'Diskon Global',
                                content: 'Berikan diskon tambahan untuk keseluruhan pesanan secara mudah sebelum lanjut ke pembayaran.',
                                position: 'left'
                            },
                            {
                                target: '#tour-retail-pay',
                                title: 'Selesaikan Pembayaran',
                                content: 'Klik tombol ini atau tekan F2 di keyboard untuk menyelesaikan transaksi. Pastikan stok dan total pesanan sudah sesuai.',
                                position: 'left'
                            },
                            {
                                target: '#tour-pos-help',
                                title: 'Butuh Panduan Lagi?',
                                content: 'Anda bisa mengulangi panduan interaktif ini kapan saja dengan menekan tombol ini. Selamat berjualan!',
                                position: 'bottom'
                            }
                        ]);
                    } else {
                        let arr = [
                            {
                                target: '#tour-pos-search',
                                title: 'Cari Produk & Kitchen Notes',
                                content: 'Cari menu di sini. Anda juga bisa menulis catatan khusus per menu (seperti "tanpa es" atau "tidak pedas") langsung di bawah setiap item keranjang.',
                                position: 'bottom'
                            },
                            {
                                target: '.tour-product-item',
                                title: 'Pilih Menu & Catatan',
                                content: 'Klik menu pada daftar ini untuk memasukkannya ke pesanan. Setelah masuk keranjang, Anda bisa menambahkan catatan dapur khusus.',
                                position: 'right'
                            }
                        ];

                        if (isMobile) {
                            arr.push({
                                target: '.floating-cart-btn',
                                title: 'Tombol Keranjang',
                                content: 'Di layar HP, pesanan pelanggan disembunyikan di sini. Kami akan otomatis membukanya untuk Anda di langkah selanjutnya. Klik Lanjut.',
                                position: 'top'
                            });
                        }

                        return arr.concat([
                            {
                                target: '#tour-cart-items',
                                title: 'Keranjang Pesanan',
                                content: 'Kelola pesanan meja di sini. Anda dapat mengubah jumlah pesanan, menghapus menu, dan mengatur harga khusus.',
                                position: 'left'
                            },
                            {
                                target: '[x-model="isTaxActive"]',
                                title: 'Pajak PB1 & Service Charge',
                                content: 'Pajak resto (PB1 10%) dan biaya pelayanan (5%) dapat dikendalikan dengan sakelar (switch) di keranjang belanja secara praktis.',
                                position: 'left'
                            },
                            {
                                target: '#tour-resto-save',
                                title: 'Simpan Antrean (F3)',
                                content: 'Gunakan ini untuk pesanan "Makan di Tempat" (Dine In) yang belum lunas. Pesanan akan dikirim ke dapur dan masuk Daftar Antrean.',
                                position: 'left'
                            },
                            {
                                target: '#tour-resto-pay',
                                title: 'Bayar Langsung (F2)',
                                content: 'Gunakan ini untuk pelanggan "Bungkus" (Takeaway) atau pelanggan yang langsung membayar lunas di kasir.',
                                position: 'left'
                            },
                            {
                                target: '[title="Daftar Antrean"]',
                                title: 'Kelola Antrean',
                                content: 'Untuk pesanan yang disimpan tadi, buka tab ini saat pelanggan siap untuk melunasi pesanannya.',
                                position: 'bottom'
                            },
                            {
                                target: '#tour-pos-help',
                                title: 'Butuh Panduan Lagi?',
                                content: 'Anda bisa mengulangi panduan interaktif ini kapan saja dengan menekan tombol ini. Selamat berjualan!',
                                position: 'left'
                            }
                        ]);
                    }
                },
                
                highlightStyle: '',
                tooltipStyle: '',

                init() {
                },

                trackingInterval: null,

                handleResize() {
                },

                cleanup() {
                    if (this.isActive) {
                        document.body.style.overflow = '';
                        this.isActive = false;
                        this.isPositioned = false;
                        this.stopTracking();
                    }
                },

                startTracking() {
                    if (this.trackingInterval) clearInterval(this.trackingInterval);
                    this.trackingInterval = setInterval(() => {
                        if (this.isActive) this.calculatePositions();
                    }, 16); 
                },

                stopTracking() {
                    if (this.trackingInterval) clearInterval(this.trackingInterval);
                    this.trackingInterval = null;
                },

                get currentStepData() {
                    return this.steps[this.currentStep] || {};
                },

                startTour() {
                    if (this.isActive) return;
                    
                    this.currentStep = 0;
                    this.isActive = true;
                    this.isPositioned = false;
                    this.updatePositions();
                    this.startTracking();
                },

                endTour() {
                    this.isActive = false;
                    this.isPositioned = false;
                    this.stopTracking();
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

                    const cartTargets = ['#tour-cart-items', '[title="Daftar Tunda"]', '[x-model="customerPhone"]', '[x-model\\.number="globalDiscount"]', '#tour-retail-pay', '[x-model="isTaxActive"]', '#tour-resto-save', '#tour-resto-pay'];
                    const isCartStep = cartTargets.includes(step.target);
                    const isProductGridStep = step.target === '.tour-product-item' || step.target === '#tour-pos-search';
                    
                    if (window.innerWidth <= 991) {
                        if (isCartStep) {
                            window.dispatchEvent(new CustomEvent('open-mobile-cart'));
                            await new Promise(resolve => setTimeout(resolve, 350));
                        } else if (isProductGridStep) {
                            window.dispatchEvent(new CustomEvent('close-mobile-cart'));
                            await new Promise(resolve => setTimeout(resolve, 350));
                        }
                    }

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

                    if (shouldScroll) {
                        targetElement.scrollIntoView({behavior: 'auto', block: 'center'});
                    }

                    setTimeout(() => {
                        this.calculatePositions();
                        this.isPositioned = true;
                    }, 50);
                },

                calculatePositions() {
                    const step = this.currentStepData;
                    if (!step) return;
                    const targetElement = document.querySelector(step.target);
                    if (!targetElement) return;

                    const freshRect = targetElement.getBoundingClientRect();
                    const padding = 12;

                    this.highlightStyle = `
                        top: ${freshRect.top - padding}px;
                        left: ${freshRect.left - padding}px;
                        width: ${freshRect.width + (padding * 2)}px;
                        height: ${freshRect.height + (padding * 2)}px;
                    `;

                    const tooltipEl = this.$refs.tooltipCard;
                    if (!tooltipEl) return;
                    
                    const tooltipRect = tooltipEl.getBoundingClientRect();
                    const tooltipWidth = tooltipRect.width || 340;
                    const tooltipHeight = tooltipRect.height || 200;

                    let tooltipLeft = freshRect.left + (freshRect.width / 2) - (tooltipWidth / 2);

                    if (tooltipLeft < 15) tooltipLeft = 15;
                    if (tooltipLeft + tooltipWidth > window.innerWidth - 15) {
                        tooltipLeft = window.innerWidth - tooltipWidth - 15;
                    }

                    const isMobile = window.innerWidth <= 768;
                    let actualPosition = isMobile ? 'bottom' : step.position;

                    if (actualPosition === 'bottom' && (window.innerHeight - freshRect.bottom - padding) < tooltipHeight + 20) {
                        actualPosition = 'top';
                    } else if (actualPosition === 'top' && (freshRect.top - padding) < tooltipHeight + 20) {
                        actualPosition = 'bottom';
                    }

                    let tooltipTop;
                    if (actualPosition === 'top') {
                        tooltipTop = freshRect.top - padding - tooltipHeight - 15;
                    } else if (actualPosition === 'bottom') {
                        tooltipTop = freshRect.bottom + padding + 15;
                    } else if (actualPosition === 'left') {
                        tooltipTop = freshRect.top + (freshRect.height / 2) - (tooltipHeight / 2);
                        tooltipLeft = freshRect.left - padding - tooltipWidth - 15;
                        if (tooltipLeft < 15) { 
                            tooltipLeft = freshRect.right + padding + 15; 
                        }
                        if (tooltipLeft + tooltipWidth > window.innerWidth - 15) {
                            actualPosition = 'bottom';
                            tooltipTop = freshRect.bottom + padding + 15;
                            tooltipLeft = freshRect.left + (freshRect.width / 2) - (tooltipWidth / 2);
                        }
                    } else if (actualPosition === 'right') {
                        tooltipTop = freshRect.top + (freshRect.height / 2) - (tooltipHeight / 2);
                        tooltipLeft = freshRect.right + padding + 15;
                        if (tooltipLeft + tooltipWidth > window.innerWidth - 15) {
                            actualPosition = 'bottom';
                            tooltipTop = freshRect.bottom + padding + 15;
                            tooltipLeft = freshRect.left + (freshRect.width / 2) - (tooltipWidth / 2);
                        }
                    }

                    if (tooltipLeft < 15) tooltipLeft = 15;
                    if (tooltipLeft + tooltipWidth > window.innerWidth - 15) tooltipLeft = window.innerWidth - tooltipWidth - 15;
                    
                    if (tooltipTop < 15) tooltipTop = 15;
                    if (tooltipTop + tooltipHeight > window.innerHeight - 15) tooltipTop = window.innerHeight - tooltipHeight - 15;

                    this.tooltipStyle = `
                        top: ${tooltipTop}px;
                        left: ${tooltipLeft}px;
                    `;
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
                        
                        <div class="d-flex flex-column gap-3 pt-3 border-top"
                             style="border-color: var(--bs-border-color) !important;">
                            
                            <div class="d-flex justify-content-center gap-1 w-100">
                                <template x-for="(step, index) in steps" :key="index">
                                    <div class="rounded-pill transition-all"
                                         :class="index === currentStep ? 'bg-primary' : 'bg-secondary bg-opacity-25'"
                                         :style="index === currentStep ? 'width: 16px; height: 6px; background-color: var(--brand-caramel) !important;' : 'width: 6px; height: 6px;'">
                                    </div>
                                </template>
                            </div>

                            <div class="d-flex justify-content-between align-items-center w-100">
                                <button type="button" class="btn btn-sm fw-bold text-muted transition-all hover-opacity px-0"
                                        @click="endTour()">
                                    Lewati
                                </button>
                                <div class="d-flex gap-2">
                                    <button type="button" x-show="currentStep > 0"
                                            class="btn btn-sm btn-light fw-bold rounded-pill px-3 transition-all border"
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
        </div>
    </template>
</div>

<style>
    .tour-guide-container {
        position: fixed;
        inset: 0;
        z-index: 1050;
        pointer-events: none;
    }

    .tour-backdrop {
        position: absolute;
        inset: 0;
        background: transparent;
        pointer-events: auto;
    }

    .tour-highlight {
        position: absolute;
        background: transparent;
        box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.6);
        border-radius: 12px;
        border: 2px solid #fff;
        transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.3s ease, visibility 0.3s ease;
        pointer-events: none;
        z-index: 1051;
    }

    .tour-tooltip {
        position: absolute;
        width: 340px;
        z-index: 1052;
        pointer-events: auto;
        transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.3s ease, visibility 0.3s ease;
        background: var(--bs-body-bg);
        border-radius: 1rem;
        will-change: transform, top, left, opacity;
    }

    .opacity-0 { opacity: 0; }
    .opacity-100 { opacity: 1; }
    .invisible { visibility: hidden; }
    .visible { visibility: visible; }
    .hover-opacity:hover { opacity: 0.7; }

    @media (max-width: 576px) {
        .tour-tooltip {
            width: calc(100vw - 40px);
        }
    }
</style>
