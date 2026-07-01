<aside id="{{ $elementId }}">

    @if($elementId !== 'mobile-sidebar-wrapper')
    <div class="sidebar-heading px-4 py-3 border-bottom d-flex align-items-center"
         style="border-color: var(--bs-border-color) !important; min-height: 70px;">
        <h5 class="m-0 font-serif fw-bolder fs-5" style="letter-spacing: -0.02em; color: var(--brand-caramel, #B67332);">
            {{ \App\Tenant\Models\Core\StoreSetting::value('navbar_brand_text') ?? 'Navigasi Toko' }}
        </h5>
    </div>
    @endif

    <nav class="list-group list-group-flush my-3 flex-grow-1">
        @foreach($this->menuSections as $section)
            <div class="small text-secondary fw-bold px-4 mb-2 mt-3 text-uppercase"
                 style="font-size: 0.65rem; letter-spacing: 0.8px; color: var(--brand-caramel, #B67332) !important;">
                {{ $section['title'] }}
            </div>

            @foreach($section['items'] as $item)
                <x-layouts.sidebar-item
                    :route="$item['route']"
                    :icon="$item['icon']"
                    :label="$item['label']"
                    :active-route="request()->route()->getName()"
                />
            @endforeach
        @endforeach
    </nav>

    <div class="p-3 border-top" style="border-color: var(--bs-border-color) !important;">

        <button type="button" onclick="if(window.installPwa) window.installPwa()"
                class="sidebar-pwa-install-btn btn btn-outline-success w-100 border-0 align-items-center justify-content-center gap-2 py-2.5 rounded-3 fw-bold transition-all shadow-sm mb-2"
                style="display: none; background-color: rgba(34, 197, 94, 0.08); border: 1px solid rgba(34, 197, 94, 0.2) !important; color: #16a34a; font-size: 0.88rem;">
            <i class="bi bi-download"></i> Install App
        </button>

        <button type="button" wire:click="logout"
                class="btn btn-outline-danger w-100 border-0 d-flex align-items-center justify-content-center gap-2 py-2.5 rounded-3 fw-bold transition-all shadow-sm"
                style="background-color: rgba(220, 53, 69, 0.04); border: 1px solid rgba(220, 53, 69, 0.1) !important; color: #dc3545; font-size: 0.88rem;">
            <i class="bi bi-box-arrow-left"></i> Log Out
        </button>
    </div>
</aside>