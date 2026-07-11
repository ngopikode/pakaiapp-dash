<div class="flex h-full min-h-0 flex-col rounded-[2rem] border border-emerald-800/15 bg-white/70 p-4 shadow-sm backdrop-blur dark:border-slate-800 dark:bg-slate-900/70 lg:p-5">
    <div class="mb-4">
        <div class="relative">
            <i class="bi bi-search pointer-events-none absolute left-5 top-1/2 -translate-y-1/2 text-lg text-emerald-800/70 dark:text-emerald-400"></i>
            <input
                type="text"
                id="tour-pos-search"
                class="w-full rounded-full border border-emerald-800/30 bg-white py-4 pl-12 pr-12 text-sm font-bold text-slate-900 shadow-sm outline-none transition focus:border-emerald-800 focus:ring-4 focus:ring-emerald-800/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white dark:focus:border-emerald-400"
                wire:model.live.debounce.300ms="search"
                wire:keydown.enter="handleEnter($event.target.value)"
                placeholder="Cari menu atau produk jualan..."
            >

            @if(strlen(trim($search)) > 0)
                <button
                    type="button"
                    wire:click="$set('search', '')"
                    class="absolute right-5 top-1/2 z-[5] -translate-y-1/2 text-slate-400 transition hover:text-slate-600 dark:hover:text-slate-200"
                    title="Bersihkan Pencarian"
                >
                    <i class="bi bi-x-circle-fill text-lg"></i>
                </button>
            @endif
        </div>
    </div>

    <div class="mb-4 flex gap-3 overflow-x-auto pb-1 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
        <button
            type="button"
            wire:click="$set('categoryFilter', 'all')"
            class="shrink-0 rounded-2xl border px-5 py-3 text-sm font-black transition {{ $categoryFilter === 'all' ? 'border-emerald-800 bg-emerald-800 text-white dark:border-emerald-400 dark:bg-emerald-400 dark:text-slate-950' : 'border-emerald-800/15 bg-white text-slate-600 hover:border-emerald-800/40 hover:text-emerald-800 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-400 dark:hover:text-white' }}"
        >
            Semua Menu
        </button>
        @if($hasPromoItems)
            <button
                type="button"
                wire:click="$set('categoryFilter', 'promo')"
                class="shrink-0 rounded-2xl border px-5 py-3 text-sm font-black transition {{ $categoryFilter === 'promo' ? 'border-red-500 bg-red-500 text-white' : 'border-red-200 bg-red-50 text-red-500 hover:bg-red-100 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-400 dark:hover:bg-red-500/20' }}"
            >
                Promo
            </button>
        @endif
        @foreach($categories as $category)
            <button
                type="button"
                wire:click="$set('categoryFilter', '{{ $category->id }}')"
                class="shrink-0 rounded-2xl border px-5 py-3 text-sm font-black transition {{ $categoryFilter == $category->id ? 'border-emerald-800 bg-emerald-800 text-white dark:border-emerald-400 dark:bg-emerald-400 dark:text-slate-950' : 'border-emerald-800/15 bg-white text-slate-600 hover:border-emerald-800/40 hover:text-emerald-800 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-400 dark:hover:text-white' }}"
            >
                {{ $category->name }}
            </button>
        @endforeach
    </div>

    <div id="tour-product-grid" class="min-h-0 flex-1 overflow-y-auto pb-4 pr-1 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
        <div wire:loading wire:target="search, categoryFilter" class="w-full">
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                @for($i = 0; $i < 8; $i++)
                    <div class="rounded-3xl border border-emerald-800/15 bg-white p-3 dark:border-slate-800 dark:bg-slate-950">
                        <div class="skeleton-shimmer h-28 rounded-2xl md:h-32"></div>
                        <div class="mt-4 space-y-2">
                            <div class="skeleton-shimmer h-4 w-3/4"></div>
                            <div class="skeleton-shimmer h-4 w-1/2"></div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        <div wire:loading.remove wire:target="search, categoryFilter" class="w-full">
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                @forelse($products as $product)
                    <div class="tour-product-item">
                        <div
                            class="relative flex h-full min-h-[190px] cursor-pointer select-none flex-col overflow-hidden rounded-3xl border border-emerald-800/30 bg-white p-3 shadow-sm transition duration-200 hover:-translate-y-1 hover:border-emerald-800 hover:shadow-xl active:scale-[0.98] dark:border-slate-700 dark:bg-slate-950 dark:hover:border-emerald-400 {{ !$product['has_variants'] && $product['stock'] <= 0 ? 'opacity-50' : '' }}"
                            x-data
                            @click="$dispatch('add-product', { product: {{ json_encode($product) }} })"
                        >
                            @if($product['has_variants'] || (!empty($product['extras']) && count($product['extras']) > 0))
                                <span class="absolute left-3 top-3 z-[2] rounded-full bg-blue-600 px-2.5 py-1 text-[10px] font-extrabold text-white shadow-sm">Ada Opsi</span>
                            @endif

                            @if(!$product['has_variants'] && $product['stock'] <= 0)
                                <span class="absolute left-3 top-3 z-[2] rounded-full bg-red-600 px-2.5 py-1 text-[10px] font-extrabold text-white shadow-sm">Stok Habis</span>
                            @endif

                            <div class="flex h-28 items-center justify-center rounded-2xl bg-[#f6f2e8] dark:bg-slate-900 md:h-32">
                                @if($product['image_url'])
                                    <img src="{{ $product['image_url'] }}" class="h-full w-full object-contain p-2" loading="lazy" alt="{{ $product['name'] }}">
                                @else
                                    <i class="bi bi-cup-hot-fill text-4xl text-emerald-800/30 dark:text-slate-700"></i>
                                @endif
                            </div>

                            <div class="mt-3 flex flex-1 flex-col justify-between">
                                <div>
                                    @if(!empty($product['active_discount_price']) && !empty($product['active_discount_name']))
                                        <span class="mb-1 inline-flex rounded-full border border-red-200 bg-red-50 px-2 py-1 text-[10px] font-extrabold tracking-wide text-red-600 dark:border-red-500/20 dark:bg-red-500/10 dark:text-red-400">
                                            % {{ $product['active_discount_name'] }}
                                        </span>
                                    @endif
                                    <h6 class="truncate text-sm font-black text-slate-950 dark:text-white">{{ $product['name'] }}</h6>
                                    @if(tenant('store_type') === 'retail' && count($product['variants']) > 0)
                                        <div class="mt-1 truncate text-xs font-medium text-slate-500 dark:text-slate-400" title="{{ collect($product['variants'])->pluck('sku')->filter()->join(', ') }}">
                                            <i class="bi bi-upc-scan mr-1"></i>
                                            {{ collect($product['variants'])->pluck('sku')->filter()->join(', ') ?: 'No SKU' }}
                                        </div>
                                    @endif
                                </div>
                                <div class="mt-3 flex items-end justify-between gap-2">
                                    <div>
                                        @if(!$product['has_variants'] && (!isset($product['extras']) || count($product['extras']) === 0))
                                            @if(!empty($product['active_discount_price']) && $product['active_discount_price'] < $product['price'])
                                                <span class="block text-xs font-semibold text-red-500 line-through">Rp {{ number_format($product['price'], 0, ',', '.') }}</span>
                                                <p class="mb-0 text-sm font-black text-emerald-800 dark:text-emerald-400">Rp {{ number_format($product['active_discount_price'], 0, ',', '.') }}</p>
                                            @else
                                                <p class="mb-0 text-sm font-black text-emerald-800 dark:text-emerald-400">Rp {{ number_format($product['price'], 0, ',', '.') }}</p>
                                            @endif
                                            <small class="mt-1 block text-[10px] font-bold text-slate-500 dark:text-slate-400">Stok: <span class="text-slate-900 dark:text-white">{{ $product['stock'] }}</span></small>
                                        @else
                                            <p class="mb-0 text-xs font-bold text-slate-500 dark:text-slate-400">Mulai <span class="block text-sm font-black text-emerald-800 dark:text-emerald-400">Rp {{ number_format(!empty($product['active_discount_price']) && $product['active_discount_price'] < $product['price'] ? $product['active_discount_price'] : $product['price'], 0, ',', '.') }}</span></p>
                                        @endif
                                    </div>
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-emerald-800 text-xl text-emerald-800 dark:border-emerald-400 dark:text-emerald-400">+</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-10 text-center">
                        <div class="rounded-3xl border-2 border-dashed border-emerald-800/20 bg-white/70 p-8 dark:border-slate-800 dark:bg-slate-900/60">
                            <i class="bi bi-search mb-3 block text-4xl text-slate-300 dark:text-slate-700"></i>
                            <h5 class="text-lg font-bold text-slate-900 dark:text-white">Produk tidak ditemukan</h5>
                            <p class="mb-0 text-sm text-slate-500 dark:text-slate-400">Coba cari dengan kata kunci lain atau pilih semua kategori.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            @if($hasMore)
                <div x-intersect.full="$wire.loadMore()" class="flex items-center justify-center gap-2 py-4 text-sm font-bold text-slate-500 dark:text-slate-400">
                    <div class="h-4 w-4 animate-spin rounded-full border-2 border-slate-300 border-t-slate-600 dark:border-slate-700 dark:border-t-slate-200"></div>
                    Memuat item lainnya...
                </div>
            @else
                <div class="mt-4 border-t border-emerald-800/10 py-4 text-center text-sm font-bold text-slate-400 dark:border-slate-800 dark:text-slate-500">
                    <i class="bi bi-check2-all"></i> Semua menu telah dimuat
                </div>
            @endif
        </div>
    </div>
</div>
