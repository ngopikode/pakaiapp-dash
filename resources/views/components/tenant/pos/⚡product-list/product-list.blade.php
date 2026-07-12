<div class="flex h-full min-h-0 flex-col overflow-hidden px-2 pb-4 lg:px-4">
    <div class="mb-4">
        <div class="relative">
            <i class="ph-bold ph-magnifying-glass pointer-events-none absolute left-5 top-1/2 -translate-y-1/2 text-lg text-emerald-800/70 dark:text-emerald-400"></i>
            <input
                type="text"
                id="tour-pos-search"
                class="w-full rounded-full border border-emerald-800 bg-white py-4 pl-12 pr-14 text-sm font-bold text-slate-900 shadow-sm outline-none transition focus:border-emerald-800 focus:ring-4 focus:ring-emerald-800/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white dark:focus:border-emerald-400"
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
                    <i class="ph-fill ph-x-circle text-lg"></i>
                </button>
            @else
                <i class="ph-bold ph-command absolute right-5 top-1/2 -translate-y-1/2 text-lg text-emerald-800 dark:text-emerald-400"></i>
            @endif
        </div>
    </div>

    <div class="mb-3 flex gap-2 overflow-x-auto pb-1 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
        <button
            type="button"
            wire:click="$set('categoryFilter', 'all')"
            class="group relative flex h-28 min-w-[150px] shrink-0 flex-col justify-between overflow-hidden rounded-[1.5rem] border px-4 py-3 text-left shadow-sm transition {{ $categoryFilter === 'all' ? 'border-emerald-800 bg-emerald-800 text-white dark:border-emerald-500 dark:bg-emerald-500 dark:text-slate-950' : 'border-slate-200 bg-white text-slate-900 hover:border-emerald-800/40 dark:border-slate-800 dark:bg-slate-900 dark:text-white' }}"
        >
            <span class="w-fit rounded-full border px-2.5 py-0.5 text-[10px] font-bold {{ $categoryFilter === 'all' ? 'border-white/70 text-white dark:border-slate-950/40 dark:text-slate-950' : 'border-emerald-800 text-emerald-800 dark:border-emerald-400 dark:text-emerald-400' }}">Available</span>
            <div>
                <div class="text-xl font-black leading-none">Semua</div>
                <div class="mt-0.5 text-xs font-bold opacity-80">All Menu</div>
            </div>
            <i class="ph-fill ph-squares-four absolute -bottom-4 right-2 text-6xl opacity-20"></i>
        </button>

        @if($hasPromoItems)
            <button
                type="button"
                wire:click="$set('categoryFilter', 'promo')"
                class="group relative flex h-28 min-w-[150px] shrink-0 flex-col justify-between overflow-hidden rounded-[1.5rem] border px-4 py-3 text-left shadow-sm transition {{ $categoryFilter === 'promo' ? 'border-red-500 bg-red-500 text-white' : 'border-slate-200 bg-white text-slate-900 hover:border-red-400 dark:border-slate-800 dark:bg-slate-900 dark:text-white' }}"
            >
                <span class="w-fit rounded-full border px-2.5 py-0.5 text-[10px] font-bold {{ $categoryFilter === 'promo' ? 'border-white/70 text-white' : 'border-red-300 bg-red-50 text-red-600 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-400' }}">Promo</span>
                <div>
                    <div class="text-xl font-black leading-none">Promo</div>
                    <div class="mt-0.5 text-xs font-bold opacity-80">Diskon aktif</div>
                </div>
                <i class="ph-fill ph-tags absolute -bottom-4 right-2 text-6xl opacity-20"></i>
            </button>
        @endif

        @foreach($categories as $category)
            <button
                type="button"
                wire:click="$set('categoryFilter', '{{ $category->id }}')"
                class="group relative flex h-28 min-w-[150px] shrink-0 flex-col justify-between overflow-hidden rounded-[1.5rem] border px-4 py-3 text-left shadow-sm transition {{ $categoryFilter == $category->id ? 'border-emerald-800 bg-emerald-800 text-white dark:border-emerald-500 dark:bg-emerald-500 dark:text-slate-950' : 'border-slate-200 bg-white text-slate-900 hover:border-emerald-800/40 dark:border-slate-800 dark:bg-slate-900 dark:text-white' }}"
            >
                <span class="w-fit rounded-full border px-2.5 py-0.5 text-[10px] font-bold {{ $categoryFilter == $category->id ? 'border-white/70 text-white dark:border-slate-950/40 dark:text-slate-950' : 'border-slate-300 text-slate-600 dark:border-slate-700 dark:text-slate-300' }}">Available</span>
                <div>
                    <div class="max-w-[100px] truncate text-xl font-black leading-none">{{ $category->name }}</div>
                    <div class="mt-0.5 text-xs font-bold opacity-80">Menu</div>
                </div>
                <i class="ph-fill ph-coffee absolute -bottom-5 right-3 text-8xl opacity-10"></i>
            </button>
        @endforeach
    </div>

    <div id="tour-product-grid" class="min-h-0 flex-1 overflow-y-auto overscroll-contain pb-4 pr-1 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
        <div wire:loading wire:target="search, categoryFilter" class="w-full">
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-4">
                @for($i = 0; $i < 8; $i++)
                    <div class="rounded-[1.5rem] border border-emerald-800/20 bg-white p-3 dark:border-slate-800 dark:bg-slate-950">
                        <div class="skeleton-shimmer h-32 rounded-[1rem]"></div>
                        <div class="mt-4 space-y-2">
                            <div class="skeleton-shimmer h-4 w-3/4"></div>
                            <div class="skeleton-shimmer h-4 w-1/2"></div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        <div wire:loading.remove wire:target="search, categoryFilter" class="w-full">
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-4">
                @forelse($products as $product)
                    <div class="tour-product-item h-full">
                        <div
                            class="relative flex h-[270px] cursor-pointer select-none flex-col overflow-hidden rounded-2xl border border-emerald-800 bg-white transition duration-200 hover:-translate-y-1 hover:shadow-md active:scale-[0.98] dark:border-slate-700 dark:bg-slate-900 {{ !$product['has_variants'] && $product['stock'] <= 0 ? 'opacity-50' : '' }}"
                            x-data
                            @click="$dispatch('add-product', { product: {{ json_encode($product) }} })"
                        >
                            @if($product['has_variants'] || (!empty($product['extras']) && count($product['extras']) > 0))
                                <span class="absolute left-3 top-3 z-[2] rounded-full bg-blue-600 px-2.5 py-1 text-[10px] font-extrabold text-white shadow-sm">Ada Opsi</span>
                            @endif

                            @if(!$product['has_variants'] && $product['stock'] <= 0)
                                <span class="absolute left-3 top-3 z-[2] rounded-full bg-red-600 px-2.5 py-1 text-[10px] font-extrabold text-white shadow-sm">Stok Habis</span>
                            @endif

                            @if(!empty($product['active_discount_price']) && !empty($product['active_discount_name']))
                                <span class="absolute right-3 top-3 z-[2] rounded-full bg-red-500 px-2.5 py-1 text-[10px] font-extrabold text-white shadow-sm">%</span>
                            @endif

                            @if($product['image_url'])
                                <img src="{{ $product['image_url'] }}" class="h-[200px] w-full shrink-0 object-cover" loading="lazy" alt="{{ $product['name'] }}">
                            @else
                                <div class="flex h-[200px] w-full shrink-0 items-center justify-center bg-slate-100 dark:bg-slate-800">
                                    <i class="ph-bold ph-image text-4xl text-slate-300 dark:text-slate-600"></i>
                                </div>
                            @endif

                            <div class="flex min-h-0 flex-1 flex-col justify-between px-3 pb-3 pt-2">
                                <div>
                                    <h6 class="line-clamp-2 text-sm font-black leading-snug text-slate-950 dark:text-white">{{ $product['name'] }}</h6>
                                </div>
                                <div class="mt-auto flex items-center justify-between gap-1">
                                    <div class="min-w-0">
                                        @php $hasDiscount = !empty($product['active_discount_price']) && $product['active_discount_price'] < $product['price']; @endphp
                                        @if($hasDiscount)
                                            <p class="mb-0 truncate whitespace-nowrap text-[10px] font-bold text-slate-400 line-through dark:text-slate-500">Rp {{ number_format($product['price'], 0, ',', '.') }}</p>
                                            <p class="mb-0 truncate whitespace-nowrap text-xs font-bold text-red-600 dark:text-red-400">Rp {{ number_format($product['active_discount_price'], 0, ',', '.') }}</p>
                                        @else
                                            <p class="mb-0 truncate whitespace-nowrap text-xs font-bold text-slate-700 dark:text-slate-300">Rp {{ number_format($product['price'], 0, ',', '.') }}</p>
                                        @endif
                                    </div>
                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full border border-emerald-800 bg-white text-lg text-emerald-800 shadow-sm transition-transform hover:scale-105 active:scale-95 dark:border-emerald-400 dark:bg-slate-950 dark:text-emerald-400">
                                        <i class="ph-bold ph-plus"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-10 text-center">
                        <div class="rounded-3xl border-2 border-dashed border-emerald-800/20 bg-white/70 p-8 dark:border-slate-800 dark:bg-slate-900/60">
                            <i class="ph-bold ph-magnifying-glass mb-3 block text-4xl text-slate-300 dark:text-slate-700"></i>
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
                    <i class="ph-bold ph-checks"></i> Semua menu telah dimuat
                </div>
            @endif
        </div>
    </div>
</div>
