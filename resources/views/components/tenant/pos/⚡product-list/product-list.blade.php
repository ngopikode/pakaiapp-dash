<div class="d-flex flex-column h-100">

    {{-- 1. Search Bar --}}
    <div class="mb-4">
        <div class="position-relative">
            <i class="bi bi-search position-absolute text-muted fs-5"
               style="top: 50%; left: 1.25rem; transform: translateY(-50%); pointer-events: none;"></i>
            <input type="text" id="tour-pos-search" class="form-control form-control-lg glass-search ps-5 py-2.5"
                   style="border-radius: 2rem; font-size: 0.95rem; padding-right: 3rem;"
                   wire:model.live.debounce.300ms="search" 
                   wire:keydown.enter="handleEnter($event.target.value)"
                   placeholder="Cari menu atau produk jualan...">
                   
            @if(strlen(trim($search)) > 0)
                <button type="button" wire:click="$set('search', '')" class="btn btn-link position-absolute text-muted p-0 border-0 shadow-none d-flex align-items-center justify-content-center"
                        style="top: 50%; right: 1.25rem; transform: translateY(-50%); z-index: 5;" title="Bersihkan Pencarian">
                    <i class="bi bi-x-circle-fill fs-5 opacity-50 hover-opacity-100 transition-all"></i>
                </button>
            @endif
        </div>
    </div>

    {{-- 2. Category Filter Segmented Tabs --}}
    <div class="cat-scroll mb-4">
        <button type="button" wire:click="$set('categoryFilter', 'all')"
                class="cat-btn {{ $categoryFilter === 'all' ? 'active' : '' }}">
            Semua Menu
        </button>
        @foreach($categories as $category)
            <button type="button" wire:click="$set('categoryFilter', '{{ $category->id }}')"
                    class="cat-btn {{ $categoryFilter == $category->id ? 'active' : '' }}">
                {{ $category->name }}
            </button>
        @endforeach
    </div>

    {{-- 3. Product Grid Canvas --}}
    <div id="tour-product-grid" class="product-grid pb-4">

        {{-- KONDISI LOADING: Layer Skeleton Muncul --}}
        <div wire:loading wire:target="search, categoryFilter" class="w-100">
            <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-3">
                @for($i = 0; $i < 8; $i++)
                    <div class="col">
                        <div class="card h-100 border p-2 bg-body-tertiary" style="border-radius: 1rem;">
                            <!-- Aspect Ratio Image Skeleton -->
                            <div class="ratio ratio-1x1 skeleton-shimmer mb-3" style="border-radius: 0.75rem;"></div>
                            <div class="card-body p-2 d-flex flex-column align-items-center">
                                <!-- Title Skeleton -->
                                <div class="skeleton-shimmer mb-2" style="width: 75%; height: 16px;"></div>
                                <!-- Price Skeleton -->
                                <div class="skeleton-shimmer" style="width: 45%; height: 14px;"></div>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        {{-- KONDISI NORMAL: Live Data Layer --}}
        <div wire:loading.remove wire:target="search, categoryFilter" class="w-100">
            <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-3">
                @forelse($products as $product)
                    <div class="col tour-product-item">
                        <div
                            class="card h-100 overflow-hidden cursor-pointer user-select-none bg-body border {{ !$product['has_variants'] && $product['stock'] <= 0 ? 'opacity-50' : '' }}"
                            style="border-radius: 1.25rem; border-color: var(--bs-border-color-translucent) !important; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.02); transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s ease;"
                            onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 15px 30px rgba(180, 83, 9, 0.15)';"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 24px rgba(0, 0, 0, 0.02)';"
                            x-data
                            @click="$dispatch('add-product', { product: {{ json_encode($product) }} })">

                            <!-- Layer Badges -->
                            @if($product['has_variants'] || (!empty($product['extras']) && count($product['extras']) > 0))
                                <span
                                    class="position-absolute top-0 end-0 m-2 badge bg-primary bg-opacity-90 shadow-sm rounded-pill py-1.5 px-2.5"
                                    style="z-index: 2; font-size: 0.65rem; font-weight: 700;">Ada Opsi</span>
                            @endif

                            @if(!$product['has_variants'] && $product['stock'] <= 0)
                                <span
                                    class="position-absolute top-0 start-0 m-2 badge bg-danger bg-opacity-90 shadow-sm rounded-pill py-1.5 px-2.5"
                                    style="z-index: 2; font-size: 0.65rem; font-weight: 700;">Stok Habis</span>
                            @endif

                            {{-- Image Container --}}
                            <div class="ratio ratio-1x1 bg-body-tertiary position-relative border-bottom"
                                 style="border-color: var(--bs-border-color-translucent) !important;">
                                @if($product['image_url'])
                                    <img src="{{ $product['image_url'] }}" class="w-100 h-100 object-fit-cover"
                                         loading="lazy" alt="{{ $product['name'] }}">
                                @else
                                    <div
                                        class="d-flex align-items-center justify-content-center text-muted opacity-25 h-100 w-100">
                                        <i class="bi bi-cup-hot-fill fs-2"></i>
                                    </div>
                                @endif
                            </div>

                            {{-- Info Content --}}
                            <div class="card-body p-3 text-center d-flex flex-column justify-content-between bg-body">
                                <div>
                                    <h6 class="fw-bold font-serif mb-1 text-truncate text-body"
                                        style="font-size: 0.9rem;">
                                        {{ $product['name'] }}
                                    </h6>
                                    @if(tenant('store_type') === 'retail' && count($product['variants']) > 0)
                                        <div class="text-secondary mb-1 text-truncate" style="font-size: 0.75rem;" title="{{ collect($product['variants'])->pluck('sku')->filter()->join(', ') }}">
                                            <i class="bi bi-upc-scan me-1"></i>
                                            {{ collect($product['variants'])->pluck('sku')->filter()->join(', ') ?: 'No SKU' }}
                                        </div>
                                    @endif
                                </div>
                                <div class="mt-2">
                                    @if(!$product['has_variants'] && (!isset($product['extras']) || count($product['extras']) === 0))
                                        <p class="fw-bold mb-0 text-caramel-solid" style="font-size: 1rem;">
                                            Rp {{ number_format($product['price'], 0, ',', '.') }}
                                        </p>
                                        <small class="text-muted d-block mt-1"
                                               style="font-size: 0.7rem; font-weight: 500;">
                                            Sisa Stok: <span class="fw-bold text-body">{{ $product['stock'] }}</span>
                                        </small>
                                    @else
                                        <p class="text-secondary mb-0 small"
                                           style="font-size: 0.75rem; font-weight: 500;">
                                            Mulai
                                            <span class="text-caramel-solid d-block d-md-inline mt-1 mt-md-0"
                                                  style="font-size: 1rem;">
                                                Rp {{ number_format($product['price'], 0, ',', '.') }}
                                            </span>
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="card p-5 border border-dashed rounded-4 bg-body-tertiary"
                             style="border-width: 2px; border-color: var(--bs-border-color) !important;">
                            <i class="bi bi-search fs-1 mb-3 text-muted opacity-25"></i>
                            <h5 class="fw-bold text-body">Produk tidak ditemukan</h5>
                            <p class="text-muted small mb-0">Coba cari dengan kata kunci lain atau pilih semua
                                kategori.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- Pagination / Infinite Scroll Bottom --}}
            @if($hasMore)
                <div x-intersect.full="$wire.loadMore()"
                     class="d-flex justify-content-center align-items-center py-4 text-muted small fw-bold">
                    <div class="spinner-border text-secondary spinner-border-sm me-2" role="status"></div>
                    Memuat item lainnya...
                </div>
            @else
                <div class="text-center py-4 text-muted small fw-bold opacity-50 border-top mt-4"
                     style="border-color: var(--bs-border-color-translucent) !important;">
                    <i class="bi bi-check2-all"></i> Semua menu telah dimuat
                </div>
            @endif
        </div>
    </div>
</div>
