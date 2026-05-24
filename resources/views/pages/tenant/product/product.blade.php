<x-layouts::app>

    <livewire:pages::tenant.product.index/>
    <livewire:pages::tenant.product.category-modal/>

    <!-- Floating Help Button -->
    <button type="button" id="tour-help-button" onclick="window.dispatchEvent(new CustomEvent('start-product-tour'))"
            class="btn btn-primary rounded-circle shadow-lg d-flex align-items-center justify-content-center position-fixed"
            style="bottom: 2rem; right: 2rem; width: 56px; height: 56px; z-index: 1040; transition: transform 0.2s;"
            onmouseover="this.style.transform='scale(1.1)'"
            onmouseout="this.style.transform='scale(1)'"
            title="Mulai Panduan">
        <i class="bi bi-question-lg fs-4"></i>
    </button>

    <!-- Tour Guide Component -->
    <x-tour-guide />
</x-layouts::app>
