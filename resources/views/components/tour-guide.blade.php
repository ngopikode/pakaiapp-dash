<script>
    if (typeof window.productTourFn === 'undefined') {
        window.productTourFn = function() {
            return {
                isActive: false,
                isPositioned: false,
                currentStep: 0,
                positioningTimeout: null,
                steps: [
                    {
                        target: '#tour-view-mode',
                        title: 'Tampilan Fleksibel',
                        content: 'Ganti tampilan daftar produk Anda antara mode Grid (kotak) atau List (baris) untuk kenyamanan melihat.',
                        position: 'bottom'
                    },
                    {
                        target: '#tour-add-category',
                        title: 'Kategori Produk',
                        content: 'Gunakan tombol ini untuk membuat kategori baru. Kategori membantu Anda mengelompokkan produk dengan lebih rapi.',
                        position: 'bottom'
                    },
                    {
                        target: '#tour-add-product',
                        title: 'Tambah Produk Baru',
                        content: 'Klik di sini untuk mulai menambahkan menu atau produk dagangan baru ke dalam sistem kasir Anda.',
                        position: 'bottom'
                    },
                    {
                        target: '#tour-category-list',
                        title: 'Manajemen Produk',
                        content: 'Di sinilah semua produk Anda ditampilkan. Anda bisa mengatur stok, mengubah harga, atau menonaktifkan produk kapan saja.',
                        position: 'top'
                    },
                    {
                        target: '#tour-help-button',
                        title: 'Butuh Panduan Lagi?',
                        content: 'Anda bisa melihat tur panduan ini lagi kapan saja dengan menekan tombol ini. Selamat berjualan!',
                        position: 'top'
                    }
                ],
                highlightStyle: '',
                tooltipStyle: '',

                init() {
                    // Penundaan sedikit memastikan DOM benar-benar siap
                    setTimeout(() => {
                        const hasSeenTour = localStorage.getItem('hasSeenProductTour');
                        
                        // HANYA jalankan jika user belum pernah melihat tour
                        if (!hasSeenTour) {
                            this.startTour();
                        }
                    }, 800); // 800ms cukup aman untuk menunggu rendering selesai
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

                    let delay = 0;
                    // Cari accordion yang sedang terbuka (tidak memiliki class collapsed)
                    let openAccordionBtn = document.querySelector('.cat-accordion-btn:not(.collapsed)');
                    if (openAccordionBtn) {
                        openAccordionBtn.click(); // Klik untuk menutup
                        delay = 600; // Tunggu sebentar sampai Livewire selesai render penutupan
                    }

                    setTimeout(() => {
                        this.currentStep = 0;
                        this.isActive = true;
                        this.isPositioned = false;
                        document.body.style.overflow = 'hidden';

                        this.updatePositions();
                    }, delay);
                },

                endTour() {
                    this.isActive = false;
                    this.isPositioned = false;
                    document.body.style.overflow = '';
                    localStorage.setItem('hasSeenProductTour', 'true');
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
                    let retries = 10; // Meningkatkan jumlah retry menjadi 10x (2 detik)

                    while (!targetElement && retries > 0) {
                        await new Promise(resolve => setTimeout(resolve, 200)); 
                        targetElement = document.querySelector(step.target);
                        retries--;
                    }

                    if (!targetElement) {
                        console.warn('Tour target not found, skipping:', step.target);
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

<div x-data="productTourFn()" 
     x-on:start-product-tour.window="startTour()"
     x-on:resize.window="handleResize()"
     x-on:livewire:navigating.window="cleanup()">
     
    <template x-teleport="body">
        <div x-show="isActive" 
             class="tour-guide-container" 
             style="display: none;">
             
            <!-- Backdrop -->
            <div class="tour-backdrop" x-show="isActive" x-transition.opacity @click="endTour()"></div>

            <!-- Highlight Hole (Menggunakan class opacity agar ukurannya tetap bisa dihitung JS) -->
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
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                             style="width: 40px; height: 40px;">
                            <i class="bi bi-stars fs-5"></i>
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
                                     :style="idx === currentStep ? 'width: 16px; height: 6px;' : 'width: 6px; height: 6px;'">
                                </div>
                            </template>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm fw-bold text-muted transition-all hover-opacity"
                                    @click="endTour()">
                                Lewati
                            </button>
                            <button type="button" x-show="currentStep > 0"
                                    class="btn btn-sm btn-light fw-bold rounded-pill px-3 transition-all"
                                    @click="prevStep()">Kembali
                            </button>
                            <button type="button"
                                    class="btn btn-sm btn-primary fw-bold rounded-pill px-4 transition-all"
                                    @click="nextStep()"
                                    x-text="currentStep === steps.length - 1 ? 'Selesai' : 'Lanjut'"></button>
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