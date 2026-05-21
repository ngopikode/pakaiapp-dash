<div class="container-fluid p-1 p-md-3" x-data="{ viewMode: 'grid' }">

    {{-- Header Section --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="brand-title mb-1">Menu &amp; Produk</h2>
            <p class="text-secondary small mb-0 fw-semibold">Kelola kategori, varian harga, dan ketersediaan stok menu
                jualan Anda.</p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap flex-md-nowrap w-100 w-md-auto">
            <!-- View Mode Toggles (Fully visible on all screens!) -->
            <div class="p-1 rounded-pill view-mode-pill d-flex align-items-center shadow-sm">
                <button @click="viewMode = 'grid'"
                        :class="viewMode === 'grid' ? 'view-mode-btn active shadow-sm' : 'view-mode-btn text-muted'"
                        class="btn btn-sm rounded-pill px-3 transition-all">
                    <i class="bi bi-grid-fill"></i>
                </button>
                <button @click="viewMode = 'list'"
                        :class="viewMode === 'list' ? 'view-mode-btn active shadow-sm' : 'view-mode-btn text-muted'"
                        class="btn btn-sm rounded-pill px-3 transition-all">
                    <i class="bi bi-list-ul"></i>
                </button>
            </div>

            <!-- Action Buttons -->
            <button wire:click="$dispatch('openModal', { type: 'category', mode: 'create' })"
                    class="btn btn-outline-secondary fw-bold px-3 py-2.5 rounded-pill shadow-sm transition-all d-flex align-items-center justify-content-center gap-1.5 flex-grow-1 flex-md-grow-0"
                    style="border-color: var(--bs-border-color) !important; color: var(--bs-body-color); font-size: 0.9rem;">
                <i class="bi bi-folder-plus text-warning"
                   style="color: var(--brand-caramel) !important;margin-right: 0.25rem !important;"></i>
                <span>Kategori</span>
            </button>

            <a href="{{ route('product.create') }}" wire:navigate
               class="btn brand-gradient-btn fw-bold px-4 py-2.5 rounded-pill shadow-sm flex-grow-1 flex-md-grow-0 d-flex justify-content-center align-items-center gap-1.5"
               style="font-size: 0.9rem;">
                <i class="bi bi-plus-lg"></i> <span>Tambah Produk</span>
            </a>
        </div>
    </div>

    {{-- Categories & Products List --}}
    <div class="row">
        <div class="col-12">
            @forelse($categories as $category)
                <div class="cat-accordion-item" wire:key="cat-{{ $category->id }}">

                    {{-- Accordion Header --}}
                    <button wire:click="loadProducts({{ $category->id }})"
                            class="cat-accordion-btn {{ $activeCategoryId == $category->id ? '' : 'collapsed' }}"
                            type="button">
                        <div class="d-flex align-items-center flex-grow-1 pe-3">
                            <div class="cat-icon-wrapper me-3">
                                <i class="bi bi-grid-1x2-fill"></i>
                            </div>
                            <span class="text-truncate fs-5 fw-bold">{{ $category->name }}</span>
                            <span class="badge cat-count-badge rounded-pill ms-auto"
                                  style="font-size: 0.75rem;">
                                {{ $category->products_count }} Item
                            </span>
                        </div>
                    </button>

                    {{-- Accordion Body --}}
                    @if($activeCategoryId == $category->id)
                        <div class="p-3 p-md-4" wire:loading.remove
                             wire:target="loadProducts({{ $category->id }})">

                            {{-- Category Actions --}}
                            <div class="d-flex justify-content-end mb-3 gap-2 border-bottom pb-3"
                                 style="border-color: var(--bs-border-color) !important;">
                                <button
                                    wire:click="$dispatch('openModal', { type: 'category', mode: 'edit', id: {{ $category->id }} })"
                                    class="btn btn-sm btn-outline-secondary fw-bold rounded-pill px-3 transition-all d-flex align-items-center gap-1.5"
                                    style="border-color: var(--bs-border-color) !important; color: var(--bs-body-color); font-size: 0.8rem;">
                                    <i class="bi bi-pencil-square" style="color: var(--brand-caramel);"></i>
                                    <span>Edit Kategori</span>
                                </button>
                                @if($category->products_count == 0)
                                    <button wire:click="deleteCategory({{ $category->id }})"
                                            wire:confirm="Hapus kategori ini secara permanen?"
                                            class="btn btn-sm btn-outline-danger fw-bold rounded-pill px-3 transition-all d-flex align-items-center gap-1.5"
                                            style="font-size: 0.8rem;">
                                        <i class="bi bi-trash"></i>
                                        <span>Hapus</span>
                                    </button>
                                @endif
                            </div>

                            {{-- Grid View --}}
                            <div x-show="viewMode === 'grid'" x-cloak>
                                <div class="row g-3">
                                    @forelse($loadedProducts as $product)
                                        <div class="col-6 col-md-4 col-xl-3" wire:key="prod-grid-{{ $product->id }}">
                                            <div
                                                class="card premium-prod-card h-100 overflow-hidden d-flex flex-column animate-premium-in {{ !$product->is_active ? 'opacity-85' : '' }}">

                                                <!-- Floating Badges / Actions -->
                                                <div
                                                    class="floating-action-badge d-flex flex-column gap-1 align-items-end">
                                                    {{-- Delete Button Floating --}}
                                                    <button wire:click="deleteProduct({{ $product->id }})"
                                                            wire:confirm="Hapus {{ $product->name }}?"
                                                            class="floating-delete-btn shadow-sm animate-all"
                                                            title="Hapus Produk">
                                                        <i class="bi bi-trash3-fill" style="font-size: 0.85rem;"></i>
                                                    </button>
                                                </div>

                                                <div class="ratio ratio-1x1 position-relative prod-img-container">
                                                    @if($product->image)
                                                        <img src="{{ Storage::url($product->image) }}"
                                                             alt="{{ $product->name }}"
                                                             class="object-fit-cover w-100 h-100 prod-img {{ !$product->is_active ? 'grayscale' : '' }}"
                                                             style="filter: {{ !$product->is_active ? 'grayscale(80%)' : 'none' }};">
                                                    @else
                                                        <div
                                                            class="d-flex align-items-center justify-content-center text-muted opacity-25 h-100 w-100 bg-body-tertiary">
                                                            <i class="bi bi-egg-fried"
                                                               style="font-size: 2.5rem; color: var(--brand-mocha);"></i>
                                                        </div>
                                                    @endif

                                                    <div class="prod-img-overlay"></div>

                                                    {{-- Stock / Active Badge using status-badge-absolute and premium-tag --}}
                                                    <div class="status-badge-absolute">
                                                        @if(!$product->is_active)
                                                            <span
                                                                class="badge bg-danger text-white rounded-pill px-2.5 py-1 shadow-sm premium-tag">Tidak Aktif</span>
                                                        @elseif($product->has_variants)
                                                            <span
                                                                class="badge text-white rounded-pill px-2.5 py-1 shadow-sm premium-tag"
                                                                style="background-color: var(--brand-caramel) !important;">
                                                                {{ $product->variants->count() }} Varian
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="card-body p-3 d-flex flex-column flex-grow-1">
                                                    <h6 class="fw-bold text-body mb-1 text-truncate"
                                                        style="font-size: 0.95rem;" title="{{ $product->name }}">
                                                        {{ $product->name }}
                                                    </h6>

                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <p class="fw-bold mb-0"
                                                           style="color: var(--brand-caramel); font-size: 0.9rem;">
                                                            {{ $product->formatted_price }}
                                                        </p>
                                                        <span class="text-secondary fw-semibold"
                                                              style="font-size: 0.75rem;">
                                                            Stok: <span
                                                                class="text-body fw-bold">{{ $product->total_stock }}</span>
                                                        </span>
                                                    </div>

                                                    {{-- Beautiful Clean Action Bar --}}
                                                    <div class="card-action-bar mt-auto">
                                                        <a href="{{ route('product.edit', $product->id) }}"
                                                           wire:navigate
                                                           class="btn-action-primary">
                                                            <i class="bi bi-pencil-square"></i>
                                                            <span>Edit</span>
                                                        </a>

                                                        <button wire:click="toggleAvailability({{ $product->id }})"
                                                                class="btn-action-secondary {{ $product->is_active ? 'text-success' : 'text-muted bg-danger bg-opacity-10 border-danger border-opacity-25' }}"
                                                                title="{{ $product->is_active ? 'Matikan Produk' : 'Aktifkan Produk' }}">
                                                            <i class="bi {{ $product->is_active ? 'bi-check-circle-fill' : 'bi-pause-circle-fill' }}"
                                                               style="font-size: 1.15rem;"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12 py-5 text-center">
                                            <div class="empty-state-icon-container">
                                                <i class="bi bi-box-seam fs-2"></i>
                                            </div>
                                            <h6 class="fw-bold text-body">Belum ada produk</h6>
                                            <p class="small text-secondary mb-0">Klik tombol Tambah Produk untuk mengisi
                                                kategori ini.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            {{-- List View --}}
                            <div x-show="viewMode === 'list'" x-cloak>
                                <div class="d-flex flex-column gap-2.5">
                                    @forelse($loadedProducts as $product)
                                        <div
                                            class="list-product-row d-flex align-items-center p-3 animate-premium-in {{ !$product->is_active ? 'opacity-85' : '' }}"
                                            wire:key="prod-list-{{ $product->id }}">

                                            <!-- Left side: Image and Text Info in flex-grow-1 wrapper -->
                                            <div class="list-img-wrapper d-flex align-items-center flex-grow-1 min-w-0">
                                                <div class="position-relative me-3 flex-shrink-0">
                                                    @if($product->image)
                                                        <img src="{{ Storage::url($product->image) }}"
                                                             class="object-fit-cover shadow-sm {{ !$product->is_active ? 'grayscale' : '' }}"
                                                             style="border-radius: 0.75rem; width: 56px; height: 56px; filter: {{ !$product->is_active ? 'grayscale(80%)' : 'none' }};"
                                                             alt="{{ $product->name }}">
                                                    @else
                                                        <div
                                                            class="border d-flex align-items-center justify-content-center text-muted shadow-sm bg-body-tertiary"
                                                            style="border-radius: 0.75rem; width: 56px; height: 56px;">
                                                            <i class="bi bi-egg-fried fs-4 opacity-50 text-secondary"></i>
                                                        </div>
                                                    @endif
                                                    @if(!$product->is_active)
                                                        <span
                                                            class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                                                    @endif
                                                </div>

                                                <div class="list-product-info flex-grow-1 min-w-0 me-3">
                                                    <h6 class="fw-bold mb-1 text-truncate text-body"
                                                        style="font-size: 0.95rem;">{{ $product->name }}</h6>
                                                    <div class="d-flex align-items-center flex-wrap gap-2">
                                                        <span class="fw-bold"
                                                              style="color: var(--brand-caramel); font-size: 0.85rem;">{{ $product->formatted_price }}</span>
                                                        <span class="text-muted"
                                                              style="font-size: 0.75rem;">• Stok: <span
                                                                class="fw-bold text-body">{{ $product->total_stock }}</span></span>
                                                        @if($product->has_variants)
                                                            <span
                                                                class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2 py-0.5 border"
                                                                style="font-size: 0.65rem; border-color: rgba(var(--bs-warning-rgb), 0.2) !important;">
                                                                {{ $product->variants->count() }} Varian
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Right side: Action buttons -->
                                            <div class="list-product-actions d-flex gap-2">
                                                <button wire:click="toggleAvailability({{ $product->id }})"
                                                        class="btn btn-sm btn-action-secondary {{ $product->is_active ? 'text-success' : 'text-muted bg-danger bg-opacity-10 border-danger border-opacity-25' }}"
                                                        style="width: 36px; height: 36px;"
                                                        title="{{ $product->is_active ? 'Matikan Produk' : 'Aktifkan Produk' }}">
                                                    <i class="bi {{ $product->is_active ? 'bi-check-circle-fill' : 'bi-pause-circle-fill' }}"></i>
                                                </button>

                                                <a href="{{ route('product.edit', $product->id) }}" wire:navigate
                                                   class="btn btn-sm btn-action-secondary"
                                                   style="width: 36px; height: 36px;"
                                                   title="Edit">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>

                                                <button wire:click="deleteProduct({{ $product->id }})"
                                                        wire:confirm="Hapus {{ $product->name }}?"
                                                        class="btn btn-sm btn-action-secondary text-danger"
                                                        style="width: 36px; height: 36px;"
                                                        title="Hapus">
                                                    <i class="bi bi-trash3-fill"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-4">
                                            <small class="text-secondary fw-semibold">Kategori ini belum memiliki
                                                produk.</small>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Loading Skeleton (Muncul saat klik accordion) --}}
                    <div wire:loading wire:target="loadProducts({{ $category->id }})"
                         class="p-4 w-100">
                        <div class="row g-3">
                            @for($i = 0; $i < 4; $i++)
                                <div class="col-6 col-md-4 col-xl-3">
                                    <div class="card border overflow-hidden shadow-sm skeleton-shimmer-card"
                                         style="border-radius: 1.25rem; border-color: var(--bs-border-color) !important; background-color: var(--bs-card-bg);">
                                        <div class="ratio ratio-1x1 placeholder-wave"
                                             style="background-color: var(--bs-tertiary-bg); opacity: 0.5;"></div>
                                        <div class="card-body p-3 placeholder-glow d-flex flex-column">
                                            <span class="placeholder col-8 mb-2 rounded-pill"
                                                  style="background-color: var(--bs-tertiary-bg); opacity: 0.7;"></span>
                                            <span class="placeholder col-5 mb-3 rounded-pill"
                                                  style="background-color: var(--bs-tertiary-bg); opacity: 0.7;"></span>
                                            <div class="d-flex gap-2 mt-auto">
                                                <span class="placeholder col-9 rounded-3"
                                                      style="height: 36px; background-color: var(--bs-tertiary-bg); opacity: 0.7;"></span>
                                                <span class="placeholder col-3 rounded-3"
                                                      style="height: 36px; background-color: var(--bs-tertiary-bg); opacity: 0.7;"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>

                </div>
            @empty
                <div class="card border-0 shadow-sm rounded-4 py-5 text-center bg-body-tertiary animate-premium-in"
                     style="border: 1px solid var(--bs-border-color) !important;">
                    <div class="card-body py-5">
                        <div class="empty-state-icon-container">
                            <i class="bi bi-folder-x fs-1"></i>
                        </div>
                        <h4 class="fw-bolder text-body">Kategori Masih Kosong</h4>
                        <p class="text-secondary small mb-4">Mulai buat kategori untuk mengelompokkan menu atau barang
                            daganganmu.</p>
                        <button wire:click="$dispatch('openModal', { type: 'category', mode: 'create' })"
                                class="btn brand-gradient-btn fw-bold px-4 py-2.5 rounded-pill shadow-sm">
                            <i class="bi bi-folder-plus me-1"></i> Buat Kategori Pertama
                        </button>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
