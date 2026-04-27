<div class="d-flex flex-column h-100">

    <div class="mb-4">
        <div class="position-relative">
            <i class="bi bi-search position-absolute text-muted"
               style="top: 50%; left: 1.25rem; transform: translateY(-50%);"></i>
            <input type="text" class="form-control form-control-lg border ps-5"
                   style="border-radius: 1rem; font-size: 0.95rem;"
                   wire:model.live.debounce.300ms="search" placeholder="Cari nama menu / produk...">
        </div>
    </div>

    <div class="d-flex gap-2 mb-4 overflow-auto pb-2" style="white-space: nowrap;">
        <button wire:click="$set('categoryFilter', 'all')"
                class="btn px-4 fw-bold transition-all {{ $categoryFilter === 'all' ? 'btn-primary' : 'btn-outline-secondary bg-body-tertiary' }}"
                style="border-radius: 1rem;">
            Semua Menu
        </button>
        @foreach($categories as $category)
            <button wire:click="$set('categoryFilter', '{{ $category->id }}')"
                    class="btn px-4 fw-bold transition-all {{ $categoryFilter == $category->id ? 'btn-primary' : 'btn-outline-secondary bg-body-tertiary' }}"
                    style="border-radius: 1rem;">
                {{ $category->name }}
            </button>
        @endforeach
    </div>

    <div class="product-grid pb-4 position-relative h-100">

        <div wire:loading wire:target="search, categoryFilter"
             class="position-absolute w-100 h-100 start-0 top-0 bg-body z-2" style="padding-right: 10px;">
            <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-3">
                @for($i = 0; $i < 8; $i++)
                    <div class="col">
                        <div class="card h-100 overflow-hidden placeholder-glow" aria-hidden="true">
                            <div class="ratio ratio-1x1 bg-secondary opacity-25 placeholder-wave border-bottom"></div>
                            <div class="card-body p-3 text-center d-flex flex-column align-items-center">
                                <span class="placeholder col-8 mb-2" style="border-radius: 0.5rem;"></span>
                                <span class="placeholder col-6" style="border-radius: 0.5rem;"></span>
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
                            class="card h-100 overflow-hidden cursor-pointer user-select-none {{ !$product['has_variants'] && $product['stock'] <= 0 ? 'opacity-50' : '' }}"
                            style="transition: transform 0.2s ease, box-shadow 0.2s ease;"
                            onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 12px 24px rgba(50, 30, 20, 0.1)';"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='var(--bs-card-box-shadow, 0 8px 24px rgba(50, 30, 20, 0.04))';"
                            x-data
                            @click="$dispatch('add-product', { product: {{ json_encode($product) }} })">

                            @if($product['has_variants'])
                                <span
                                    class="position-absolute top-0 end-0 m-2 badge bg-primary opacity-75 shadow-sm rounded-pill"
                                    style="z-index: 2; font-size: 0.65rem; font-weight: 600;">Ada Varian</span>
                            @endif

                            @if(!$product['has_variants'] && $product['stock'] <= 0)
                                <span
                                    class="position-absolute top-0 start-0 m-2 badge bg-danger opacity-75 shadow-sm rounded-pill"
                                    style="z-index: 2; font-size: 0.65rem; font-weight: 600;">Stok Habis</span>
                            @endif

                            <div class="ratio ratio-1x1 bg-body-secondary position-relative border-bottom">
                                @if($product['image_url'])
                                    <img src="{{ $product['image_url'] }}" class="w-100 h-100 object-fit-cover"
                                         loading="lazy" alt="">
                                @else
                                    <div
                                        class="d-flex align-items-center justify-content-center text-muted opacity-25 h-100 w-100">
                                        <i class="bi bi-cup-hot fs-1"></i>
                                    </div>
                                @endif
                            </div>

                            <div class="card-body p-3 text-center d-flex flex-column justify-content-between">
                                <div>
                                    <h6 class="fw-bold font-serif mb-1 text-truncate"
                                        style="font-size: 0.9rem; color: var(--bs-body-color);">
                                        {{ $product['name'] }}
                                    </h6>
                                </div>
                                <div class="mt-2">
                                    @if(!$product['has_variants'])
                                        <p class="fw-bold mb-1"
                                           style="color: var(--brand-caramel); font-size: 0.95rem;">
                                            Rp {{ number_format($product['price'], 0, ',', '.') }}
                                        </p>
                                        <p class="text-muted mb-0" style="font-size: 0.7rem;">
                                            Sisa Stok: {{ $product['stock'] }}
                                        </p>
                                    @else
                                        <p class="text-muted fw-bold mb-0" style="font-size: 0.75rem;">Mulai
                                            <span class="fw-bold"
                                                  style="color: var(--brand-caramel); font-size: 0.95rem;">
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
                        <div class="card p-5 border-0 bg-transparent">
                            <i class="bi bi-search fs-1 mb-3 text-muted opacity-25"></i>
                            <h5 class="fw-bold font-serif text-primary">Produk tidak ditemukan</h5>
                            <p class="text-muted small">Coba cari dengan kata kunci lain atau pilih semua kategori.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            @if($hasMore)
                <div x-intersect.full="$wire.loadMore()"
                     class="d-flex justify-content-center py-4 text-muted small fw-bold">
                    <div class="spinner-border spinner-border-sm me-2 text-primary" role="status"></div>
                    Mengambil menu lainnya...
                </div>
            @else
                <div class="text-center py-4 text-muted small fw-bold opacity-50 border-top mt-4">
                    <i class="bi bi-check2-all"></i> Semua menu telah dimuat
                </div>
            @endif
        </div>
    </div>
</div>
