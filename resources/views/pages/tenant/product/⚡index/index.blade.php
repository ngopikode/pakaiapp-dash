<div class="container-fluid p-2" x-data="{ viewMode: 'grid' }">

    {{-- Header Section --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
        <div>
            <h2 class="fw-bolder text-dark mb-1" style="color: #451a03 !important; letter-spacing: -0.5px;">Menu &
                Produk</h2>
            <p class="text-secondary small mb-0 fw-medium">Kelola kategori, harga, dan ketersediaan stok.</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <!-- View Mode Toggles -->
            <div class="bg-white p-1 rounded-pill shadow-sm border d-none d-sm-flex">
                <button @click="viewMode = 'grid'"
                        :class="viewMode === 'grid' ? 'btn-primary shadow-sm' : 'btn-light text-muted'"
                        class="btn btn-sm rounded-pill px-3 transition-all">
                    <i class="bi bi-grid-fill"></i>
                </button>
                <button @click="viewMode = 'list'"
                        :class="viewMode === 'list' ? 'btn-primary shadow-sm' : 'btn-light text-muted'"
                        class="btn btn-sm rounded-pill px-3 transition-all">
                    <i class="bi bi-list-ul"></i>
                </button>
            </div>

            <!-- Action Buttons -->
            <button wire:click="$dispatch('openModal', { type: 'category', mode: 'create' })"
                    class="btn bg-white border fw-bold px-3 py-2 rounded-pill shadow-sm text-dark flex-grow-1 flex-md-grow-0">
                <i class="bi bi-folder-plus text-warning me-1"></i> <span class="d-none d-md-inline">Kategori</span>
            </button>

            <a href="{{ route('product.create') }}" wire:navigate
               class="btn fw-bold px-3 py-2 rounded-pill shadow-sm flex-grow-1 flex-md-grow-0 d-flex justify-content-center align-items-center"
               style="background: linear-gradient(135deg, #ca8a04, #b45309); color: white; border: none;">
                <i class="bi bi-plus-lg me-1"></i> <span class="d-none d-md-inline">Tambah Produk</span><span
                    class="d-md-none">Produk</span>
            </a>
        </div>
    </div>

    {{-- Categories & Products List --}}
    <div class="row">
        <div class="col-12">
            @forelse($categories as $category)
                <div class="accordion-item-op" wire:key="cat-{{ $category->id }}">

                    {{-- Accordion Header --}}
                    <button wire:click="loadProducts({{ $category->id }})"
                            class="accordion-button-op {{ $activeCategoryId == $category->id ? '' : 'collapsed' }}"
                            type="button">
                        <div class="d-flex align-items-center flex-grow-1 pe-3">
                            <div
                                class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center me-3"
                                style="width: 40px; height: 40px;">
                                <i class="bi bi-grid-1x2-fill"></i>
                            </div>
                            <span class="text-truncate fs-5">{{ $category->name }}</span>
                            <span class="badge bg-light text-muted border rounded-pill ms-auto"
                                  style="font-size: 0.8rem;">
                                {{ $category->products_count }} Item
                            </span>
                        </div>
                    </button>

                    {{-- Accordion Body --}}
                    @if($activeCategoryId == $category->id)
                        <div class="accordion-body-op p-3 p-md-4" wire:loading.remove
                             wire:target="loadProducts({{ $category->id }})">

                            {{-- Category Actions --}}
                            <div class="d-flex justify-content-end mb-4 gap-2 border-bottom pb-3">
                                <button
                                    wire:click="$dispatch('openModal', { type: 'category', mode: 'edit', id: {{ $category->id }} })"
                                    class="btn btn-sm btn-white border fw-bold rounded-pill px-3 shadow-sm text-secondary">
                                    <i class="bi bi-pencil me-1"></i> Edit Kategori
                                </button>
                                @if($category->products_count == 0)
                                    <button wire:click="deleteCategory({{ $category->id }})"
                                            wire:confirm="Hapus kategori ini secara permanen?"
                                            class="btn btn-sm btn-white border border-danger text-danger fw-bold rounded-pill px-3 shadow-sm">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                @endif
                            </div>

                            {{-- Grid View --}}
                            <div x-show="viewMode === 'grid'" x-cloak>
                                <div class="row g-3">
                                    @forelse($loadedProducts as $product)
                                        <div class="col-6 col-md-4 col-xl-3" wire:key="prod-grid-{{ $product->id }}">
                                            <div
                                                class="card product-card h-100 overflow-hidden {{ !$product->is_active ? 'opacity-75 bg-light' : '' }}">

                                                <div class="ratio ratio-1x1 bg-light position-relative border-bottom">
                                                    @if($product->image)
                                                        <img src="{{ Storage::url($product->image) }}"
                                                             alt="{{ $product->name }}"
                                                             class="object-fit-cover w-100 h-100 {{ !$product->is_active ? 'grayscale' : '' }}"
                                                             style="filter: {{ !$product->is_active ? 'grayscale(100%)' : 'none' }};">
                                                    @else
                                                        <div
                                                            class="d-flex align-items-center justify-content-center text-muted opacity-25 h-100 w-100">
                                                            <i class="bi bi-image" style="font-size: 3rem;"></i>
                                                        </div>
                                                    @endif

                                                    @if(!$product->is_active)
                                                        <span
                                                            class="position-absolute top-0 end-0 m-2 badge bg-danger rounded-pill shadow-sm">Habis</span>
                                                    @elseif($product->has_variants)
                                                        <span
                                                            class="position-absolute bottom-0 start-0 m-2 badge"
                                                            style="font-size: 0.65rem;">
                                                            {{ $product->variants->count() }} Varian
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="card-body p-2 p-md-3 d-flex flex-column">
                                                    <h6 class="fw-bold mb-1 text-truncate"
                                                        style="color: #451a03; font-size: 0.95rem;">{{ $product->name }}</h6>
                                                    <p class="fw-bold mb-1"
                                                       style="color: #b45309; font-size: 0.85rem;">{{ $product->formatted_price }}</p>
                                                    <p class="text-muted mb-3" style="font-size: 0.7rem;">Stok: <span
                                                            class="fw-bold">{{ $product->total_stock }}</span></p>

                                                    {{-- Mobile-friendly Action Buttons --}}
                                                    <div class="row g-1 mt-auto">
                                                        <div class="col-4">
                                                            <a href="{{ route('product.edit', $product->id) }}"
                                                               wire:navigate
                                                               class="btn btn-light border w-100 btn-action-mobile rounded-3 text-secondary">
                                                                <i class="bi bi-pencil-square"></i>
                                                            </a>
                                                        </div>
                                                        <div class="col-4">
                                                            <button wire:click="toggleAvailability({{ $product->id }})"
                                                                    class="btn w-100 btn-action-mobile rounded-3 border {{ $product->is_active ? 'btn-light text-success' : 'btn-danger text-white border-danger' }}">
                                                                <i class="bi {{ $product->is_active ? 'bi-check-circle-fill' : 'bi-x-circle' }}"></i>
                                                            </button>
                                                        </div>
                                                        <div class="col-4">
                                                            <button wire:click="deleteProduct({{ $product->id }})"
                                                                    wire:confirm="Hapus {{ $product->name }}?"
                                                                    class="btn btn-light border w-100 btn-action-mobile rounded-3 text-danger">
                                                                <i class="bi bi-trash3"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12 py-5 text-center">
                                            <i class="bi bi-box-seam text-muted fs-1 opacity-25 mb-2 d-block"></i>
                                            <h6 class="fw-bold text-muted">Belum ada produk</h6>
                                            <p class="small text-muted mb-0">Klik tombol Tambah Produk untuk mengisi
                                                kategori ini.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            {{-- List View --}}
                            <div x-show="viewMode === 'list'" x-cloak>
                                <div class="d-flex flex-column gap-2">
                                    @forelse($loadedProducts as $product)
                                        <div
                                            class="card product-card flex-row align-items-center p-2 {{ !$product->is_active ? 'opacity-75 bg-light' : '' }}"
                                            wire:key="prod-list-{{ $product->id }}">

                                            <div class="position-relative me-3 flex-shrink-0">
                                                @if($product->image)
                                                    <img src="{{ Storage::url($product->image) }}"
                                                         class="object-fit-cover shadow-sm {{ !$product->is_active ? 'grayscale' : '' }}"
                                                         style="border-radius: 0.75rem; width: 60px; height: 60px; filter: {{ !$product->is_active ? 'grayscale(100%)' : 'none' }};"
                                                         alt="">
                                                @else
                                                    <div
                                                        class="bg-light border d-flex align-items-center justify-content-center text-muted shadow-sm"
                                                        style="border-radius: 0.75rem; width: 60px; height: 60px;">
                                                        <i class="bi bi-image fs-4 opacity-50"></i>
                                                    </div>
                                                @endif
                                                @if(!$product->is_active)
                                                    <span
                                                        class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                                                @endif
                                            </div>

                                            <div class="flex-grow-1 min-w-0 me-2">
                                                <h6 class="fw-bold mb-0 text-truncate"
                                                    style="color: #451a03;">{{ $product->name }}</h6>
                                                <span class="fw-bold me-2"
                                                      style="color: #b45309; font-size: 0.85rem;">{{ $product->formatted_price }}</span>
                                                <span class="badge bg-light text-muted border rounded-pill"
                                                      style="font-size: 0.65rem;">Stok: {{ $product->total_stock }}</span>
                                            </div>

                                            <div class="d-flex gap-1 gap-md-2 me-1">
                                                <button wire:click="toggleAvailability({{ $product->id }})"
                                                        class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center {{ $product->is_active ? 'btn-light border text-success' : 'btn-danger border-danger text-white' }}"
                                                        style="width: 32px; height: 32px;">
                                                    <i class="bi {{ $product->is_active ? 'bi-check-lg' : 'bi-x-lg' }}"></i>
                                                </button>
                                                <a href="{{ route('product.edit', $product->id) }}" wire:navigate
                                                   class="btn btn-sm btn-light border text-secondary rounded-circle d-flex align-items-center justify-content-center"
                                                   style="width: 32px; height: 32px;">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button wire:click="deleteProduct({{ $product->id }})"
                                                        wire:confirm="Hapus {{ $product->name }}?"
                                                        class="btn btn-sm btn-light border text-danger rounded-circle d-flex align-items-center justify-content-center"
                                                        style="width: 32px; height: 32px;">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-4">
                                            <small class="text-muted">Kategori ini belum memiliki produk.</small>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Loading Skeleton (Muncul saat klik accordion) --}}
                    <div wire:loading wire:target="loadProducts({{ $category->id }})"
                         class="accordion-body-op p-4 w-100">
                        <div class="row g-3">
                            @for($i = 0; $i < 4; $i++)
                                <div class="col-6 col-md-4 col-xl-3">
                                    <div class="card border-0 bg-white overflow-hidden shadow-sm"
                                         style="border-radius: 1rem;">
                                        <div class="ratio ratio-1x1 bg-secondary opacity-10 placeholder-wave"></div>
                                        <div class="card-body p-3 placeholder-glow d-flex flex-column">
                                            <span
                                                class="placeholder col-8 mb-2 rounded-pill bg-secondary opacity-25"></span>
                                            <span
                                                class="placeholder col-5 mb-3 rounded-pill bg-secondary opacity-25"></span>
                                            <div class="d-flex gap-1 mt-auto">
                                                <span class="placeholder col-4 rounded-3 bg-secondary opacity-25"
                                                      style="height: 30px;"></span>
                                                <span class="placeholder col-4 rounded-3 bg-secondary opacity-25"
                                                      style="height: 30px;"></span>
                                                <span class="placeholder col-4 rounded-3 bg-secondary opacity-25"
                                                      style="height: 30px;"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>

                </div>
            @empty
                <div class="card border-0 shadow-sm rounded-4 py-5 text-center bg-white">
                    <div class="card-body py-5">
                        <div
                            class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                            style="width: 80px; height: 80px;">
                            <i class="bi bi-folder-x fs-1"></i>
                        </div>
                        <h4 class="fw-bolder" style="color: #451a03;">Kategori Masih Kosong</h4>
                        <p class="text-muted small mb-4">Mulai buat kategori untuk mengelompokkan menu atau barang
                            daganganmu.</p>
                        <button wire:click="$dispatch('openModal', { type: 'category', mode: 'create' })"
                                class="btn fw-bold px-4 py-2 rounded-pill shadow-sm"
                                style="background: linear-gradient(135deg, #ca8a04, #b45309); color: white; border: none;">
                            <i class="bi bi-folder-plus me-1"></i> Buat Kategori Pertama
                        </button>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
