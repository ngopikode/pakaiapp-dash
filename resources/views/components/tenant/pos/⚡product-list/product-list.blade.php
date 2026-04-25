<div class="d-flex flex-column h-100">

    <div class="mb-3">
        <div class="position-relative">
            <i class="bi bi-search position-absolute text-muted"
               style="top: 50%; left: 1rem; transform: translateY(-50%);"></i>
            <input type="text" class="form-control form-control-lg rounded-pill border-0 shadow-sm ps-5 text-sm"
                   wire:model.live.debounce.300ms="search" placeholder="Cari nama produk ke server...">
        </div>
    </div>

    <div class="d-flex gap-2 mb-3 overflow-auto pb-2" style="white-space: nowrap;">
        <button wire:click="$set('categoryFilter', 'all')"
                class="btn rounded-pill px-4 fw-bold shadow-sm border-0 transition-all {{ $categoryFilter === 'all' ? 'btn-dark' : 'btn-white bg-white text-secondary' }}">
            Semua
        </button>
        @foreach($categories as $category)
            <button wire:click="$set('categoryFilter', '{{ $category->id }}')"
                    class="btn rounded-pill px-4 fw-bold shadow-sm border-0 transition-all {{ $categoryFilter == $category->id ? 'btn-dark' : 'btn-white bg-white text-secondary' }}">
                {{ $category->name }}
            </button>
        @endforeach
    </div>

    <div class="product-grid pb-4 position-relative h-100">

        <div wire:loading wire:target="search, categoryFilter"
             class="position-absolute w-100 h-100 start-0 top-0 bg-light z-2" style="padding-right: 10px;">
            <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-3">
                @for($i = 0; $i < 8; $i++)
                    <div class="col">
                        <div class="card h-100 border-0 shadow-sm rounded-4 placeholder-glow">
                            <div class="bg-secondary bg-opacity-25 rounded-top-4 placeholder"
                                 style="height: 120px; width: 100%;"></div>
                            <div class="card-body p-3 text-center d-flex flex-column align-items-center">
                                <span class="placeholder col-8 rounded mb-2"></span>
                                <span class="placeholder col-5 rounded bg-primary"></span>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        <div wire:loading.remove wire:target="search, categoryFilter">
            <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-3">
                @forelse($products as $product)
                    <div class="col">
                        <div
                            class="card h-100 border-0 shadow-sm rounded-4 product-card user-select-none position-relative {{ !$product['has_variants'] && $product['stock'] <= 0 ? 'opacity-50' : '' }}"
                            x-data
                            @click="$dispatch('add-product', { product: {{ json_encode($product) }} })">

                            @if($product['has_variants'])
                                <span class="badge bg-dark position-absolute top-0 end-0 m-2 rounded-pill shadow-sm"
                                      style="z-index: 2;">Ada Varian</span>
                            @endif

                            @if(!$product['has_variants'] && $product['stock'] <= 0)
                                <span
                                    class="badge bg-danger position-absolute top-0 start-0 m-2 rounded-pill shadow-sm"
                                    style="z-index: 2;">Habis</span>
                            @endif

                            <div class="bg-light d-flex justify-content-center align-items-center rounded-top-4"
                                 style="height: 120px;">
                                @if($product['image_url'])
                                    <img src="{{ $product['image_url'] }}"
                                         class="w-100 h-100 object-fit-cover rounded-top-4" loading="lazy" alt="">
                                @else
                                    <i class="bi bi-box text-muted fs-1 opacity-25"></i>
                                @endif
                            </div>
                            <div class="card-body p-3 text-center">
                                <h6 class="fw-bold mb-1 text-truncate small">{{ $product['name'] }}</h6>
                                @if(!$product['has_variants'])
                                    <p class="text-primary fw-bolder mb-1 small">
                                        Rp {{ number_format($product['price'], 0, ',', '.') }}</p>
                                    <p class="text-muted small mb-0" style="font-size: 0.65rem;">
                                        Stok: {{ $product['stock'] }}</p>
                                @else
                                    <p class="text-secondary fw-bold mb-0 small" style="font-size: 0.75rem;">Mulai
                                        <span
                                            class="text-primary fw-bolder">Rp {{ number_format($product['price'], 0, ',', '.') }}</span>
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-box-seam fs-1 mb-2 opacity-50"></i>
                        <p>Produk tidak ditemukan.</p>
                    </div>
                @endforelse
            </div>

            @if($hasMore)
                <div x-intersect.full="$wire.loadMore()"
                     class="d-flex justify-content-center py-4 text-muted small fw-bold">
                    <div class="spinner-border spinner-border-sm me-2 text-primary" role="status"></div>
                    Mengambil data...
                </div>
            @else
                <div class="text-center py-4 text-muted small fw-bold opacity-50">
                    <i class="bi bi-check2-all"></i> Semua produk telah dimuat
                </div>
            @endif
        </div>
    </div>
</div>
