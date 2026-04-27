<div class="container-fluid py-4" x-data="{ viewMode: 'grid' }">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold font-serif text-dark mb-1">Manajemen Produk</h2>
            <p class="text-muted small mb-0">Atur kategori, harga, dan produk yang tersedia dengan cepat.</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <div class="btn-group p-1 rounded-pill border shadow-sm">
                <button @click="viewMode = 'grid'"
                        :class="viewMode === 'grid' ? 'btn-brand shadow-sm' : 'btn-light border-0 text-muted'"
                        class="btn btn-sm rounded-pill px-3 transition-all">
                    <i class="bi bi-grid-fill"></i>
                </button>
                <button @click="viewMode = 'list'"
                        :class="viewMode === 'list' ? 'btn-brand shadow-sm' : 'btn-light border-0 text-muted'"
                        class="btn btn-sm rounded-pill px-3 transition-all">
                    <i class="bi bi-list-ul"></i>
                </button>
            </div>

            <button wire:click="$dispatch('openModal', { type: 'category', mode: 'create' })"
                    class="btn btn-outline-dark rounded-pill px-3 py-2 shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-folder-plus"></i> <span class="d-none d-sm-inline">Kategori</span>
            </button>

            <a href="{{ route('product.create') }}" wire:navigate
               class="btn btn-brand rounded-pill px-3 py-2 shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-plus-lg"></i> <span class="d-none d-sm-inline">Tambah Produk</span>
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="accordion" id="menuAccordion">
                @forelse($categories as $category)
                    <div class="accordion-item border-0 shadow-sm rounded-4 mb-3 overflow-hidden"
                         wire:key="cat-{{ $category->id }}">

                        <h2 class="accordion-header">
                            <button wire:click="loadProducts({{ $category->id }})"
                                    class="accordion-button {{ $activeCategoryId == $category->id ? '' : 'collapsed' }} px-3 px-md-4 py-3 fw-bold text-dark shadow-none border-bottom"
                                    type="button">
                                <div class="w-100 me-3 d-flex align-items-center">
                                    <i class="bi bi-grid-1x2 me-2 me-md-3 text-brand"></i>
                                    <span class="text-truncate">{{ $category->name }}</span>
                                    <small class="badge bg-light text-muted border rounded-pill ms-auto">
                                        {{ $category->products_count }} Produk
                                    </small>
                                </div>
                            </button>
                        </h2>

                        <div wire:loading wire:target="loadProducts({{ $category->id }})"
                             class="w-100 p-4 bg-light text-center">
                            <div class="spinner-border spinner-border-sm text-brand" role="status"></div>
                            <span class="text-muted small ms-2">Memuat produk...</span>
                        </div>

                        @if($activeCategoryId == $category->id)
                            <div class="accordion-collapse show" wire:loading.remove
                                 wire:target="loadProducts({{ $category->id }})">
                                <div class="accordion-body bg-light p-3 p-md-4">

                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <div class="btn-group">
                                            <button
                                                wire:click="$dispatch('openModal', { type: 'category', mode: 'edit', id: {{ $category->id }} })"
                                                class="btn btn-sm border rounded-pill shadow-sm px-3 me-2">
                                                <i class="bi bi-pencil me-1"></i> Edit Kategori
                                            </button>
                                            @if($category->products_count == 0)
                                                <button wire:click="deleteCategory({{ $category->id }})"
                                                        wire:confirm="Hapus kategori ini?"
                                                        class="btn btn-sm border rounded-pill shadow-sm text-danger px-3">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>

                                    <div x-show="viewMode === 'grid'" class="row g-3">
                                        @forelse($loadedProducts as $product)
                                            <div class="col-6 col-md-4 col-lg-3"
                                                 wire:key="prod-grid-{{ $product->id }}">
                                                <div
                                                    class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden {{ !$product->is_active ? 'opacity-75 bg-secondary-subtle' : '' }}">

                                                    <div
                                                        class="ratio ratio-1x1 border-bottom bg-light position-relative">
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
                                                                class="position-absolute bottom-0 start-0 m-2 badge bg-dark opacity-75 rounded-pill"
                                                                style="font-size: 0.65rem;">
                                                                    {{ $product->variants->count() }} Varian
                                                                </span>
                                                        @endif
                                                    </div>

                                                    <div class="card-body p-3">
                                                        <h6 class="fw-bold text-dark mb-1 text-truncate small">{{ $product->name }}</h6>

                                                        <p class="text-brand fw-bold mb-1"
                                                           style="font-size: 0.85rem;">{{ $product->formatted_price }}</p>
                                                        <p class="text-muted mb-3" style="font-size: 0.7rem;">Total
                                                            Stok: {{ $product->total_stock }}</p>

                                                        <div class="d-flex gap-2 mt-auto">
                                                            <a href="{{ route('product.create', $product->id) }}"
                                                               wire:navigate
                                                               class="btn btn-sm btn-light border flex-grow-1 rounded-3">
                                                                <i class="bi bi-pencil"></i>
                                                            </a>
                                                            <button
                                                                wire:click="toggleAvailability({{ $product->id }})"
                                                                class="btn btn-sm {{ $product->is_active ? 'btn-light border text-success' : 'btn-danger' }} rounded-3 px-2">
                                                                <i class="bi {{ $product->is_active ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i>
                                                            </button>
                                                            <button wire:click="deleteProduct({{ $product->id }})"
                                                                    wire:confirm="Hapus {{ $product->name }}?"
                                                                    class="btn btn-sm btn-light border text-danger rounded-3 px-2">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="col-12 py-5 text-center rounded-4 border"><small
                                                    class="text-muted">Kategori ini belum memiliki produk.</small>
                                            </div>
                                        @endforelse
                                    </div>

                                    <div x-show="viewMode === 'list'" class="d-flex flex-column gap-2" x-cloak>
                                        @forelse($loadedProducts as $product)
                                            <div
                                                class="d-flex align-items-center p-2 rounded-4 shadow-sm border {{ !$product->is_active ? 'opacity-75 bg-light' : '' }}"
                                                wire:key="prod-list-{{ $product->id }}">

                                                @if($product->image)
                                                    <img src="{{ asset('storage/' . $product->image) }}"
                                                         class="rounded-3 object-fit-cover me-3"
                                                         style="width: 60px; height: 60px;" alt="">
                                                @else
                                                    <div
                                                        class="rounded-3 bg-light border d-flex align-items-center justify-content-center me-3 text-muted"
                                                        style="width: 60px; height: 60px;">
                                                        <i class="bi bi-image"></i>
                                                    </div>
                                                @endif

                                                <div class="flex-grow-1 min-w-0 me-2">
                                                    <h6 class="fw-bold text-dark mb-0 text-truncate">{{ $product->name }}</h6>
                                                    <span class="text-brand fw-bold me-2"
                                                          style="font-size: 0.8rem;">{{ $product->formatted_price }}</span>
                                                    <span class="badge bg-light text-muted border rounded-pill"
                                                          style="font-size: 0.65rem;">Stok: {{ $product->total_stock }}</span>
                                                </div>

                                                <div class="d-flex gap-1 me-2">
                                                    <a href="{{ route('product.create', $product->id) }}"
                                                       wire:navigate
                                                       class="btn btn-sm btn-light border rounded-3"><i
                                                            class="bi bi-pencil"></i></a>
                                                    <button wire:click="toggleAvailability({{ $product->id }})"
                                                            class="btn btn-sm {{ $product->is_active ? 'btn-light border text-success' : 'btn-danger' }} rounded-3 px-3 fw-bold"
                                                            style="font-size: 0.7rem;">
                                                        {{ $product->is_active ? 'READY' : 'HABIS' }}
                                                    </button>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-center py-4 rounded-4 border"><small
                                                    class="text-muted">Kategori ini belum memiliki produk.</small>
                                            </div>
                                        @endforelse
                                    </div>

                                </div>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="card border-0 shadow-sm rounded-4 py-5 text-center">
                        <div class="card-body py-5">
                            <i class="bi bi-cup-hot text-muted mb-3 fs-1 opacity-25"></i>
                            <h5 class="fw-bold text-dark font-serif">Produk Masih Kosong</h5>
                            <p class="text-muted small">Mulai buat kategori untuk mengisi daftar menu restoranmu.</p>
                            <button wire:click="$dispatch('openModal', { type: 'category', mode: 'create' })"
                                    class="btn btn-brand rounded-pill px-4">Buat Kategori
                            </button>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
