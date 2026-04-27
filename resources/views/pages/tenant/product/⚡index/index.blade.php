<div class="container-fluid py-4" x-data="{ viewMode: 'grid' }">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold font-serif text-primary mb-1">Manajemen Produk</h2>
            <p class="text-muted small mb-0">Atur kategori, harga, dan produk yang tersedia dengan cepat.</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <div class="btn-group p-1 card flex-row shadow-sm" style="border-radius: 1rem;">
                <button @click="viewMode = 'grid'"
                        :class="viewMode === 'grid' ? 'btn-primary' : 'btn-link text-muted text-decoration-none'"
                        class="btn btn-sm px-3 transition-all" style="border-radius: 0.75rem;">
                    <i class="bi bi-grid-fill"></i>
                </button>
                <button @click="viewMode = 'list'"
                        :class="viewMode === 'list' ? 'btn-primary' : 'btn-link text-muted text-decoration-none'"
                        class="btn btn-sm px-3 transition-all" style="border-radius: 0.75rem;">
                    <i class="bi bi-list-ul"></i>
                </button>
            </div>

            <button wire:click="$dispatch('openModal', { type: 'category', mode: 'create' })"
                    class="btn btn-outline-secondary px-3 py-2 d-flex align-items-center gap-2"
                    style="border-radius: 0.75rem; font-weight: 600;">
                <i class="bi bi-folder-plus"></i> <span class="d-none d-sm-inline">Kategori</span>
            </button>

            <a href="{{ route('product.create') }}" wire:navigate
               class="btn btn-primary px-3 py-2 d-flex align-items-center gap-2">
                <i class="bi bi-plus-lg"></i> <span class="d-none d-sm-inline">Tambah Produk</span>
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="accordion" id="menuAccordion">
                @forelse($categories as $category)
                    <div class="accordion-item card mb-3 overflow-hidden" wire:key="cat-{{ $category->id }}">

                        <h2 class="accordion-header">
                            <button wire:click="loadProducts({{ $category->id }})"
                                    class="accordion-button {{ $activeCategoryId == $category->id ? '' : 'collapsed' }} px-3 px-md-4 py-3 fw-bold font-serif shadow-none"
                                    style="background-color: transparent;" type="button">
                                <div class="w-100 me-3 d-flex align-items-center">
                                    <i class="bi bi-grid-1x2 me-2 me-md-3 text-primary"></i>
                                    <span class="text-truncate">{{ $category->name }}</span>
                                    <small class="badge bg-body-tertiary text-muted border rounded-pill ms-auto">
                                        {{ $category->products_count }} Produk
                                    </small>
                                </div>
                            </button>
                        </h2>

                        <div wire:loading wire:target="loadProducts({{ $category->id }})"
                             class="w-100 bg-body-tertiary p-3 p-md-4 border-top">
                            <div
                                class="mb-4 card p-2 border-0 bg-transparent placeholder-glow d-flex justify-content-between">
                                <span class="placeholder col-3 col-md-2"
                                      style="border-radius: 1rem; height: 30px;"></span>
                            </div>

                            <div x-show="viewMode === 'grid'" x-cloak>
                                <div class="row g-3">
                                    @for($i = 0; $i < 4; $i++)
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <div class="card h-100 overflow-hidden" aria-hidden="true">
                                                <div
                                                    class="ratio ratio-1x1 bg-secondary opacity-25 placeholder-wave border-bottom"></div>
                                                <div class="card-body p-3 placeholder-glow d-flex flex-column">
                                                    <span class="placeholder col-8 mb-2"
                                                          style="border-radius: 0.5rem;"></span>
                                                    <span class="placeholder col-5 mb-3"
                                                          style="border-radius: 0.5rem;"></span>
                                                    <div class="d-flex gap-2 mt-auto">
                                                        <span class="placeholder col-6"
                                                              style="border-radius: 0.5rem; height: 32px;"></span>
                                                        <span class="placeholder col-3"
                                                              style="border-radius: 0.5rem; height: 32px;"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endfor
                                </div>
                            </div>

                            <div x-show="viewMode === 'list'" x-cloak>
                                <div class="d-flex flex-column gap-2">
                                    @for($i = 0; $i < 3; $i++)
                                        <div class="card flex-row align-items-center p-2 placeholder-glow"
                                             aria-hidden="true">
                                            <div class="bg-secondary opacity-25 placeholder me-3"
                                                 style="border-radius: 0.75rem; width: 60px; height: 60px;"></div>
                                            <div class="flex-grow-1">
                                                <span class="placeholder col-5 mb-1 d-block"
                                                      style="border-radius: 0.5rem;"></span>
                                                <span class="placeholder col-3" style="border-radius: 0.5rem;"></span>
                                            </div>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>

                        @if($activeCategoryId == $category->id)
                            <div class="accordion-collapse show" wire:loading.remove
                                 wire:target="loadProducts({{ $category->id }})">
                                <div class="accordion-body bg-body-tertiary p-3 p-md-4 border-top">

                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <div class="btn-group">
                                            <button
                                                wire:click="$dispatch('openModal', { type: 'category', mode: 'edit', id: {{ $category->id }} })"
                                                class="btn btn-sm btn-outline-secondary px-3 me-2"
                                                style="border-radius: 0.75rem;">
                                                <i class="bi bi-pencil me-1"></i> Edit Kategori
                                            </button>
                                            @if($category->products_count == 0)
                                                <button wire:click="deleteCategory({{ $category->id }})"
                                                        wire:confirm="Hapus kategori ini?"
                                                        class="btn btn-sm btn-outline-danger px-3"
                                                        style="border-radius: 0.75rem;">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>

                                    <div x-show="viewMode === 'grid'" x-cloak>
                                        <div class="row g-3">
                                            @forelse($loadedProducts as $product)
                                                <div class="col-6 col-md-4 col-lg-3"
                                                     wire:key="prod-grid-{{ $product->id }}">
                                                    <div
                                                        class="card h-100 overflow-hidden {{ !$product->is_active ? 'opacity-75' : '' }}">

                                                        <div
                                                            class="ratio ratio-1x1 bg-body-secondary position-relative border-bottom">
                                                            @if($product->image)
                                                                <img src="{{ asset('storage/' . $product->image) }}"
                                                                     class="object-fit-cover w-100 h-100" alt="">
                                                            @else
                                                                <div
                                                                    class="d-flex align-items-center justify-content-center text-muted opacity-25 h-100 w-100">
                                                                    <i class="bi bi-image fs-1"></i>
                                                                </div>
                                                            @endif

                                                            @if($product->has_variants)
                                                                <span
                                                                    class="position-absolute bottom-0 start-0 m-2 badge bg-primary opacity-75 rounded-pill"
                                                                    style="font-size: 0.65rem;">
                                                                    {{ $product->variants->count() }} Varian
                                                                </span>
                                                            @endif
                                                        </div>

                                                        <div class="card-body p-3 d-flex flex-column">
                                                            <h6 class="fw-bold font-serif mb-1 text-truncate small"
                                                                style="color: var(--bs-body-color);">{{ $product->name }}</h6>

                                                            <p class="fw-bold mb-1"
                                                               style="color: var(--brand-caramel); font-size: 0.9rem;">{{ $product->formatted_price }}</p>
                                                            <p class="text-muted mb-3" style="font-size: 0.75rem;">Total
                                                                Stok: {{ $product->total_stock }}</p>

                                                            <div class="d-flex gap-2 mt-auto">
                                                                <a href="{{ route('product.edit', $product->id) }}"
                                                                   wire:navigate
                                                                   class="btn btn-sm btn-outline-secondary flex-grow-1"
                                                                   style="border-radius: 0.5rem;">
                                                                    <i class="bi bi-pencil"></i>
                                                                </a>
                                                                <button
                                                                    wire:click="toggleAvailability({{ $product->id }})"
                                                                    class="btn btn-sm {{ $product->is_active ? 'btn-outline-secondary text-success border-success' : 'btn-outline-danger' }} px-2"
                                                                    style="border-radius: 0.5rem;">
                                                                    <i class="bi {{ $product->is_active ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i>
                                                                </button>
                                                                <button wire:click="deleteProduct({{ $product->id }})"
                                                                        wire:confirm="Hapus {{ $product->name }}?"
                                                                        class="btn btn-sm btn-outline-danger px-2"
                                                                        style="border-radius: 0.5rem;">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="col-12 py-5 text-center card border-0 bg-transparent">
                                                    <small class="text-muted">Kategori ini belum memiliki
                                                        produk.</small>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>

                                    <div x-show="viewMode === 'list'" x-cloak>
                                        <div class="d-flex flex-column gap-2">
                                            @forelse($loadedProducts as $product)
                                                <div
                                                    class="card flex-row align-items-center p-2 {{ !$product->is_active ? 'opacity-75' : '' }}"
                                                    wire:key="prod-list-{{ $product->id }}">

                                                    @if($product->image)
                                                        <img src="{{ asset('storage/' . $product->image) }}"
                                                             class="object-fit-cover me-3"
                                                             style="border-radius: 0.75rem; width: 60px; height: 60px;"
                                                             alt="">
                                                    @else
                                                        <div
                                                            class="bg-body-secondary border d-flex align-items-center justify-content-center me-3 text-muted"
                                                            style="border-radius: 0.75rem; width: 60px; height: 60px;">
                                                            <i class="bi bi-image"></i>
                                                        </div>
                                                    @endif

                                                    <div class="flex-grow-1 min-w-0 me-2">
                                                        <h6 class="fw-bold font-serif mb-0 text-truncate">{{ $product->name }}</h6>
                                                        <span class="fw-bold me-2"
                                                              style="color: var(--brand-caramel); font-size: 0.85rem;">{{ $product->formatted_price }}</span>
                                                        <span
                                                            class="badge bg-body-tertiary text-muted border rounded-pill"
                                                            style="font-size: 0.65rem;">Stok: {{ $product->total_stock }}</span>
                                                    </div>

                                                    <div class="d-flex gap-2 me-2">
                                                        <a href="{{ route('product.create', $product->id) }}"
                                                           wire:navigate class="btn btn-sm btn-outline-secondary"
                                                           style="border-radius: 0.5rem;">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <button wire:click="toggleAvailability({{ $product->id }})"
                                                                class="btn btn-sm {{ $product->is_active ? 'btn-outline-secondary text-success border-success' : 'btn-outline-danger' }} px-3 fw-bold"
                                                                style="border-radius: 0.5rem; font-size: 0.7rem;">
                                                            {{ $product->is_active ? 'READY' : 'HABIS' }}
                                                        </button>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="text-center py-4 card border-0 bg-transparent">
                                                    <small class="text-muted">Kategori ini belum memiliki
                                                        produk.</small>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>

                                </div>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="card py-5 text-center">
                        <div class="card-body py-5">
                            <i class="bi bi-cup-hot text-muted mb-3 fs-1 opacity-25"></i>
                            <h5 class="fw-bold font-serif text-primary">Kategori Masih Kosong</h5>
                            <p class="text-muted small">Mulai buat kategori untuk mengisi daftar menu restoranmu.</p>
                            <button wire:click="$dispatch('openModal', { type: 'category', mode: 'create' })"
                                    class="btn btn-primary px-4 mt-2">
                                Buat Kategori
                            </button>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <livewire:pages::tenant.product.category-modal/>
</div>
