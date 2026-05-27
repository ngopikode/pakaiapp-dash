{{-- ===== PREMIUM INTERACTIVE INPUT-DRIVEN FORM TOUR GUIDE ===== --}}

<script>
    if (typeof window.formTourFn === 'undefined') {
        window.formTourFn = function() {
            return {
                isActive: false,
                isPositioned: false,
                currentStep: 0,
                positioningTimeout: null,
                
                // Reactive input states for dynamic tooltip button activation
                nameValue: '',
                categoryValue: '',
                priceValue: '',

                steps: [
                    {
                        target: '#productName',
                        title: 'Nama Produk / Menu 📝',
                        content: 'Ketik nama produk atau menu jualan Anda di sini (misal: "Kopi Susu Gula Aren" atau "Kaos Polos Cotton Combed").',
                        position: 'bottom',
                        validate: function() {
                            return this.nameValue.trim().length >= 3;
                        },
                        validateMsg: 'Silakan ketik minimal 3 karakter untuk nama produk.'
                    },
                    {
                        target: '#categorySelect',
                        title: 'Kategori Menu / Produk 📂',
                        content: 'Pilih kategori produk. Kategori memudahkan penyusunan menu di kasir jualan maupun di web toko online Anda.',
                        position: 'bottom',
                        validate: function() {
                            return this.categoryValue !== '';
                        },
                        validateMsg: 'Silakan pilih kategori terlebih dahulu.'
                    },
                    {
                        target: 'button[x-show="tab === \'general\'"]',
                        title: 'Melangkah ke Harga 💰',
                        content: 'Luar biasa! Sekarang ketuk tombol "Lanjut Harga" untuk melangkah ke pengisian harga dan stok.',
                        position: 'top',
                        isActionStep: true,
                        listenEvent: 'click',
                        listenTarget: 'button[x-show="tab === \'general\'"]'
                    },
                    {
                        target: 'input[wire\\:model="basePrice"]',
                        title: 'Harga Jual Produk 🪙',
                        content: 'Tentukan harga jual produk/menu Anda. Masukkan harga dasar tanpa titik atau koma (misal: 18000).',
                        position: 'bottom',
                        validate: function() {
                            return parseFloat(this.priceValue) > 0;
                        },
                        validateMsg: 'Silakan masukkan harga jual yang valid (lebih dari 0).'
                    },
                    {
                        target: 'button[x-show="tab === \'pricing\'"]',
                        title: 'Langkah Review Akhir ✨',
                        content: 'Hampir selesai! Ketuk tombol "Lanjut Add-ons" atau "Lanjut" untuk melangkah ke halaman review akhir.',
                        position: 'top',
                        isActionStep: true,
                        listenEvent: 'click',
                        listenTarget: 'button[x-show="tab === \'pricing\'"]'
                    },
                    {
                        target: 'button[type="submit"]',
                        title: 'Simpan Produk Baru 🎉',
                        content: 'Sempurna! Terakhir, klik tombol "Simpan Produk" atau "Simpan" di bagian bawah untuk mendaftarkan menu baru Anda ke sistem kasir!',
                        position: 'top'
                    }
                ],

                highlightStyle: '',
                tooltipStyle: '',

                init() {
                    // Auto-start only for new product creation, if they have never seen it
                    setTimeout(() => {
                        const hasSeenTour = localStorage.getItem('hasSeenFormTour');
                        const isCreatePage = window.location.pathname.includes('/product/create') || window.location.hash.includes('create');
                        if (!hasSeenTour && isCreatePage) {
                            this.startTour();
                        }
                    }, 1200);

                    // Event listener bindings for click-based auto-advance steps
                    this.steps.forEach((step, idx) => {
                        if (step.isActionStep) {
                            document.addEventListener(step.listenEvent, (e) => {
                                if (!this.isActive || this.currentStep !== idx) return;
                                if (e.target.closest(step.listenTarget)) {
                                    setTimeout(() => {
                                        this.nextStep();
                                    }, 400);
                                }
                            });
                        }
                    });
                },

                startTour() {
                    if (this.isActive) return;
                    this.currentStep = 0;
                    this.isActive = true;
                    this.isPositioned = false;
                    document.body.style.overflow = 'hidden';
                    
                    // Bind real-time input observers
                    this.$nextTick(() => {
                        this.bindInputObservers();
                        this.updatePositions();
                    });
                },

                endTour() {
                    this.isActive = false;
                    this.isPositioned = false;
                    document.body.style.overflow = '';
                    localStorage.setItem('hasSeenFormTour', 'true');
                },

                bindInputObservers() {
                    const nameEl = document.querySelector('#productName');
                    if (nameEl) {
                        this.nameValue = nameEl.value;
                        nameEl.addEventListener('input', (e) => { this.nameValue = e.target.value; });
                    }

                    const catEl = document.querySelector('#categorySelect');
                    if (catEl) {
                        this.categoryValue = catEl.value;
                        catEl.addEventListener('change', (e) => { this.categoryValue = e.target.value; });
                    }

                    const priceEl = document.querySelector('input[wire\\:model="basePrice"]');
                    if (priceEl) {
                        this.priceValue = priceEl.value;
                        priceEl.addEventListener('input', (e) => { this.priceValue = e.target.value; });
                    }
                },

                get currentStepData() {
                    return this.steps[this.currentStep] || {};
                },

                isStepValid() {
                    const step = this.currentStepData;
                    if (this.currentStep === 0) return this.nameValue.trim().length >= 3;
                    if (this.currentStep === 1) return this.categoryValue !== '';
                    if (this.currentStep === 3) return parseFloat(this.priceValue) > 0;
                    return true;
                },

                handleNext() {
                    const step = this.currentStepData;
                    if (step.validate && !step.validate.call(this)) {
                        window.showIslandToast(step.validateMsg, 'warning');
                        return;
                    }
                    this.nextStep();
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

                handleResize() {
                    if (this.isActive) {
                        this.updatePositions(false);
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
                        console.warn('Form Tour target not found, skipping:', step.target);
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

<div x-data="formTourFn()"
     x-on:start-form-tour.window="startTour()"
     x-on:resize.window="handleResize()"
     x-on:livewire:navigating.window="endTour()">
     
    <template x-teleport="body">
        <div x-show="isActive" class="tour-guide-container" style="display: none;">
            
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
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                                 style="width: 40px; height: 40px; background-color: rgba(249, 115, 22, 0.1) !important; color: #F97316 !important;">
                                <i class="bi bi-pencil-fill fs-5"></i>
                            </div>
                            <h5 class="fw-bold text-body mb-0" x-text="currentStepData.title"
                                style="font-size: 1.1rem;"></h5>
                        </div>

                        <p class="text-secondary mb-4" x-text="currentStepData.content"
                           style="font-size: 0.95rem; line-height: 1.5;"></p>

                        <div class="d-flex justify-content-between align-items-center pt-3 border-top"
                             style="border-color: var(--bs-border-color) !important;">
                            <div class="d-flex gap-1">
                                <template x-for="(step, index) in steps" :key="index">
                                    <div class="rounded-pill transition-all"
                                         :class="index === currentStep ? 'bg-primary' : 'bg-secondary bg-opacity-25'"
                                         :style="index === currentStep ? 'width: 16px; height: 6px; background-color: #F97316 !important;' : 'width: 6px; height: 6px;'">
                                    </div>
                                </template>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm fw-bold text-muted transition-all hover-opacity"
                                        @click="endTour()">
                                    Lewati
                                </button>
                                <button type="button" x-show="currentStep > 0 && !steps[currentStep-1].isActionStep"
                                        class="btn btn-sm btn-light fw-bold rounded-pill px-3 transition-all"
                                        @click="prevStep()">Kembali
                                </button>
                                
                                <template x-if="!currentStepData.isActionStep">
                                    <button type="button"
                                            class="btn btn-sm fw-bold rounded-pill px-4 transition-all"
                                            :class="isStepValid() ? 'btn-primary text-white' : 'btn-secondary bg-secondary bg-opacity-25 text-muted cursor-not-allowed'"
                                            :style="isStepValid() ? 'background-color: #F97316 !important; border-color: #F97316 !important;' : ''"
                                            @click="handleNext()"
                                            x-text="currentStep === steps.length - 1 ? 'Selesai' : 'Lanjut'"></button>
                                </template>
                                
                                <template x-if="currentStepData.isActionStep">
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-3 py-2 fw-bold" style="font-size: 0.72rem;">
                                        <i class="bi bi-cursor-fill animate-bounce me-1"></i> Lakukan Aksi di Atas
                                    </span>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
