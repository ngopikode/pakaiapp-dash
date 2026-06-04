<script>
    if (typeof window.accordionTourFn === 'undefined') {
        window.accordionTourFn = function() {
            return {
                isActive: false,
                isPositioned: false,
                currentStep: 0,
                positioningTimeout: null,
                hasProducts: true,
                steps: [
                    {
                        target: '#tour-accordion-edit-category',
                        title: 'Aksi Kategori',
                        content: 'Gunakan tombol ini untuk mengubah nama kategori atau menghapusnya jika kategori sedang kosong.',
                        position: 'bottom'
                    },
                    {
                        target: '.tour-accordion-product-actions',
                        title: 'Aksi Produk Cepat',
                        content: 'Edit produk atau matikan sementara (nonaktifkan) produk ini jika stok sedang kosong atau tidak tersedia.',
                        position: 'top'
                    }
                ],
                highlightStyle: '',
                tooltipStyle: '',

                init() {
                    // Start is triggered by custom event
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

                startTour(force = false) {
                    if (!force) {
                        const hasSeenTour = localStorage.getItem('hasSeenAccordionTour');
                        if (hasSeenTour) return;
                    }

                    // Pastikan ada setidaknya satu accordion yang terbuka (target tersedia)
                    let targetExists = document.querySelector('#tour-accordion-edit-category');
                    if (!targetExists) {
                        // Cari tombol accordion pertama yang belum terbuka
                        let firstAccordionBtn = document.querySelector('.cat-accordion-btn.collapsed');
                        if (firstAccordionBtn) {
                            firstAccordionBtn.click();
                        } else {
                            // Kategori / produk masih kosong
                            Swal.fire({
                                title: 'Kategori Masih Kosong',
                                html: 'Silakan buat <b>Kategori baru</b> dan tambahkan <b>Produk pertama</b> Anda terlebih dahulu untuk memulai panduan ini.',
                                icon: 'info',
                                confirmButtonColor: '#F97316',
                                confirmButtonText: 'Saya Mengerti'
                            });
                            return;
                        }
                    }

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
                    localStorage.setItem('hasSeenAccordionTour', 'true');
                },

                nextStep() {
                    if (this.currentStep === 0 && !this.hasProducts) {
                        this.endTour();
                        return;
                    }

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

                    let targetElements = document.querySelectorAll(step.target);
                    let targetElement = Array.from(targetElements).find(el => el.offsetParent !== null);
                    let retries = 10; 

                    while (!targetElement && retries > 0) {
                        await new Promise(resolve => setTimeout(resolve, 200)); 
                        targetElements = document.querySelectorAll(step.target);
                        targetElement = Array.from(targetElements).find(el => el.offsetParent !== null);
                        retries--;
                    }

                    if (!targetElement) {
                        console.warn('Accordion Tour target not found, skipping:', step.target);
                        if (this.currentStep < this.steps.length - 1) {
                            this.currentStep++;
                            this.updatePositions(shouldScroll);
                        } else {
                            this.endTour();
                        }
                        return;
                    }

                    if (this.currentStep === 0) {
                        this.hasProducts = !!(document.querySelector('.premium-prod-card') || document.querySelector('.list-product-row'));
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

<div x-data="accordionTourFn()" 
     x-on:start-accordion-tour.window="startTour($event.detail?.force)"
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

                        <div class="d-flex justify-content-between align-items-center pt-3 border-top"
                             style="border-color: var(--bs-border-color) !important;">
                            <div class="d-flex gap-1">
                                <template x-for="(step, idx) in steps" :key="index">
                                    <div x-show="index === 0 || hasProducts"
                                         class="rounded-pill transition-all"
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
                                        x-text="(currentStep === steps.length - 1 || (currentStep === 0 && !hasProducts)) ? 'Selesai' : 'Lanjut'"></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
