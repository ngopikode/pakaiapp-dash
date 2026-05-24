<x-layouts::app>

    <livewire:pages::tenant.product.index/>
    <livewire:pages::tenant.product.category-modal/>

    <!-- Floating Help Menu -->
    <div class="position-fixed" style="bottom: 2rem; right: 2rem; z-index: 1040;" x-data="{ openHelp: false }">
        
        <!-- Dropdown Menu -->
        <div x-show="openHelp" 
             @click.outside="openHelp = false"
             x-transition.opacity.duration.200ms
             class="bg-white shadow-lg rounded-4 p-2 mb-3"
             style="display: none; position: absolute; bottom: 100%; right: 0; min-width: 250px; border: 1px solid var(--bs-border-color); transform-origin: bottom right;">
            <h6 class="px-3 pt-2 pb-1 mb-0 fw-bold text-muted small">Pusat Panduan</h6>
            <hr class="my-2" style="border-color: var(--bs-border-color);">
            
            <button type="button" @click="openHelp = false; window.dispatchEvent(new CustomEvent('start-product-tour'))"
                    class="btn btn-light w-100 text-start border-0 mb-1 d-flex align-items-center gap-2 py-2 rounded-3 transition-all"
                    style="font-size: 0.9rem;">
                <div class="bg-primary bg-opacity-10 text-primary rounded p-1 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                    <i class="bi bi-layout-text-window"></i>
                </div>
                Panduan Halaman Utama
            </button>
            
            <button type="button" @click="openHelp = false; window.dispatchEvent(new CustomEvent('start-accordion-tour', { detail: { force: true } }))"
                    class="btn btn-light w-100 text-start border-0 d-flex align-items-center gap-2 py-2 rounded-3 transition-all"
                    style="font-size: 0.9rem;">
                <div class="rounded p-1 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; background-color: #fff3e0; color: var(--brand-caramel, #d97706);">
                    <i class="bi bi-box-seam"></i>
                </div>
                Panduan Kelola Produk
            </button>
        </div>

        <!-- Floating Button -->
        <button type="button" id="tour-help-button" @click="openHelp = !openHelp"
                class="btn btn-primary rounded-circle shadow-lg d-flex align-items-center justify-content-center"
                style="width: 56px; height: 56px; transition: transform 0.2s;"
                :style="openHelp ? 'transform: scale(1.1);' : ''"
                onmouseover="if(!this.__x || !this.__x.$data.openHelp) this.style.transform='scale(1.1)'"
                onmouseout="if(!this.__x || !this.__x.$data.openHelp) this.style.transform='scale(1)'"
                title="Pusat Panduan">
            <i class="bi fs-4 transition-all" :class="openHelp ? 'bi-x-lg' : 'bi-question-lg'"></i>
        </button>
    </div>

    <!-- Tour Guide Component -->
    <x-tour-guide />
    <x-tour-guide-accordion />
</x-layouts::app>
