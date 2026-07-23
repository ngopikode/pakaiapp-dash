<div class="flex h-[calc(100vh-4rem)] lg:h-[calc(100vh-72px)] min-h-0 flex-1 flex-col bg-[#FFF8F0] dark:bg-[#0B1120] w-full mx-auto" x-data="{
    viewMode: localStorage.getItem('productViewMode') || 'list',
    setView(mode) { this.viewMode = mode; localStorage.setItem('productViewMode', mode); },
    toggleSort(field) {
        if ($wire.sortField === field + '_asc') {
            $wire.sortField = field + '_desc';
        } else {
            $wire.sortField = field + '_asc';
        }
    }
}" x-init="$watch('viewMode', val => { if (val === 'list') $nextTick(() => { if (window.livewireInitialized) Livewire.rescan?.() }) })">

    <div class="shrink-0 px-4 md:px-6 pt-6 pb-2 w-full mx-auto" style="max-width:1536px">
        
        {{-- 1. Top Action Bar --}}
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 mb-4">
            
            <!-- Left Side: View Switcher & Search -->
            <div class="flex items-center gap-3 w-full lg:w-auto">
                <div class="hidden sm:flex items-center rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-0.5 shadow-sm h-10 shrink-0">
<button @click="setView('list')"
        class="w-8 h-8 flex items-center justify-center rounded-lg transition-colors"
        :class="viewMode === 'list' ? 'bg-slate-100 dark:bg-slate-700 text-slate-900 dark:text-white' : 'text-slate-400 hover:text-slate-700 dark:hover:text-slate-300'" title="List View">
    <i class="ph-bold ph-list text-base"></i>
</button>
<button @click="setView('grid')"
        class="w-8 h-8 flex items-center justify-center rounded-lg transition-colors"
        :class="viewMode === 'grid' ? 'bg-slate-100 dark:bg-slate-700 text-slate-900 dark:text-white' : 'text-slate-400 hover:text-slate-700 dark:hover:text-slate-300'" title="Grid View">
    <i class="ph-bold ph-grid-four text-base"></i>
</button>
                </div>

                <div class="relative w-full lg:w-64 h-10 shrink-0">
                    <i class="ph-bold ph-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search..."
                           class="w-full h-full pl-9 pr-8 py-2 rounded-full border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-sm focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none text-slate-900 dark:text-white shadow-sm transition-shadow">
                    @if($search)
                        <button type="button" wire:click="$set('search', '')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-red-500">
                            <i class="ph-bold ph-x text-sm"></i>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Center Side: Quick Filters -->
            <div class="hidden lg:flex items-center gap-3 shrink-0">
                <div class="flex items-center gap-2 bg-slate-100 dark:bg-slate-800/50 px-3 py-1.5 rounded-full text-sm font-medium text-slate-600 dark:text-slate-300">
                    <span class="text-slate-400">Show:</span>
                    <select wire:model.live="filterCategory" class="bg-transparent border-none p-0 focus:ring-0 cursor-pointer font-bold outline-none">
                        <option value="">All Products</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-2 bg-slate-100 dark:bg-slate-800/50 px-3 py-1.5 rounded-full text-sm font-medium text-slate-600 dark:text-slate-300">
                    <span class="text-slate-400">Sort by:</span>
                    <select wire:model.live="sortField" class="bg-transparent border-none p-0 focus:ring-0 cursor-pointer font-bold outline-none">
                        <option value="newest">Default</option>
                        <option value="name_asc">Name (A-Z)</option>
                        <option value="name_desc">Name (Z-A)</option>
                        <option value="price_asc">Price (Low-High)</option>
                        <option value="price_desc">Price (High-Low)</option>
                        <option value="stock_desc">Highest Stock</option>
                    </select>
                </div>
            </div>

            <!-- Right Side: Filter & Add Product -->
            <div class="flex items-center justify-between lg:justify-end gap-2 shrink-0 w-full lg:w-auto">
                <button wire:click="$dispatch('openModal', { type: 'category', mode: 'create' })"
                        class="px-4 py-2.5 bg-white dark:bg-slate-900 border border-orange-500/30 text-orange-600 dark:text-orange-400 font-bold rounded-full hover:bg-orange-50 dark:hover:bg-orange-500/10 transition flex items-center gap-2 text-sm shadow-sm h-10">
                    <i class="ph-bold ph-funnel text-base"></i> <span class="hidden sm:inline">Categories</span>
                </button>
                <a href="{{ route('product.create') }}" wire:navigate.hover
                   class="px-5 py-2.5 bg-[#E65C2C] hover:bg-[#D44A1A] text-white font-bold rounded-full shadow-sm transition-all duration-200 flex items-center justify-center gap-2 text-sm h-10">
                    <i class="ph-bold ph-plus text-base"></i> <span>Add new product</span>
                </a>
            </div>
        </div>

        {{-- 2. Secondary Filter Bar --}}
        <div class="flex flex-wrap items-end gap-4 md:gap-6 mt-6 pb-2 border-b border-slate-200 dark:border-slate-800 relative z-20">
            <!-- Category -->
            <div class="flex flex-col gap-1.5 min-w-[140px] flex-1 sm:flex-none">
                <label class="text-[11px] font-bold text-slate-500 dark:text-slate-400">Category</label>
                <div class="relative">
                    <select wire:model.live="filterCategory" class="w-full bg-slate-100 dark:bg-slate-800 border-0 rounded-xl px-4 py-2 text-sm font-bold text-slate-700 dark:text-slate-200 appearance-none focus:ring-2 focus:ring-orange-500/20 cursor-pointer h-10">
                        <option value="">All Collection</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <i class="ph-bold ph-caret-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                </div>
            </div>

            <!-- Price -->
            <div class="flex flex-col gap-1.5 min-w-[140px] flex-1 sm:flex-none">
                <label class="text-[11px] font-bold text-slate-500 dark:text-slate-400">Price</label>
                <div class="relative">
                    <select wire:model.live="filterPrice" class="w-full bg-slate-100 dark:bg-slate-800 border-0 rounded-xl px-4 py-2 text-sm font-bold text-slate-700 dark:text-slate-200 appearance-none focus:ring-2 focus:ring-orange-500/20 cursor-pointer h-10">
                        <option value="">All Prices</option>
                        <option value="0-50000">Under 50k</option>
                        <option value="50000-100000">50k - 100k</option>
                        <option value="above-100k">Above 100k</option>
                    </select>
                    <i class="ph-bold ph-caret-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                </div>
            </div>

            <!-- Status (Floating Dropdown Overlay style) -->
            <div class="flex flex-col gap-1.5 min-w-[140px] flex-1 sm:flex-none relative" x-data="{ open: false }">
                <label class="text-[11px] font-bold text-slate-500 dark:text-slate-400">Status</label>
                <button type="button" @click="open = !open"
                        class="w-full flex items-center justify-between bg-orange-50 dark:bg-orange-500/10 border border-orange-200 dark:border-orange-500/30 rounded-xl px-4 py-2 text-sm font-bold text-orange-600 dark:text-orange-400 h-10 text-left transition-colors">
                    <span>{{ $filterStatus === 'active' ? 'Active' : ($filterStatus === 'inactive' ? 'No Active' : 'All Status') }}</span>
                    <i class="ph-bold ph-caret-down text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
                </button>
                
                {{-- 3. Floating Dropdown Overlay --}}
                <div x-show="open" @click.outside="open = false" style="display: none"
                     x-transition.opacity.duration.200ms
                     class="absolute top-[calc(100%+4px)] left-0 w-full bg-white dark:bg-slate-800 border border-orange-100 dark:border-orange-500/20 rounded-xl shadow-xl p-1.5 z-50">
                    <button wire:click="$set('filterStatus', ''); open = false" class="w-full text-left px-3 py-2 rounded-lg text-sm font-bold transition-colors {{ $filterStatus === '' ? 'bg-orange-50 text-orange-600 dark:bg-orange-500/10' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-700' }}">All Status</button>
                    <button wire:click="$set('filterStatus', 'active'); open = false" class="w-full text-left px-3 py-2 rounded-lg text-sm font-bold transition-colors {{ $filterStatus === 'active' ? 'bg-orange-50 text-orange-600 dark:bg-orange-500/10' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-700' }}">Active</button>
                    <button wire:click="$set('filterStatus', 'inactive'); open = false" class="w-full text-left px-3 py-2 rounded-lg text-sm font-bold transition-colors {{ $filterStatus === 'inactive' ? 'bg-orange-50 text-orange-600 dark:bg-orange-500/10' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-700' }}">No Active</button>
                </div>
            </div>
        </div>

        {{-- Bulk Actions Toolbar (Only shows when items selected) --}}
        @if(count($selected) > 0)
            <div class="flex items-center gap-3 mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-100 dark:border-blue-800/50">
                <span class="text-sm font-bold text-blue-800 dark:text-blue-400 px-2">{{ count($selected) }} items selected</span>
                <div class="flex gap-2">
                    <button wire:click="bulkToggleStatus(true)" class="px-3 py-1.5 bg-white dark:bg-slate-800 text-emerald-600 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-bold shadow-sm hover:bg-slate-50">Set Active</button>
                    <button wire:click="bulkToggleStatus(false)" class="px-3 py-1.5 bg-white dark:bg-slate-800 text-slate-500 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-bold shadow-sm hover:bg-slate-50">Set Inactive</button>
                    <button wire:click="bulkDelete" wire:confirm="Are you sure you want to delete {{ count($selected) }} items?" class="px-3 py-1.5 bg-red-500 text-white border border-red-600 rounded-lg text-xs font-bold shadow-sm hover:bg-red-600">Delete Selected</button>
                </div>
            </div>
        @endif
    </div>

    {{-- Scrollable Inner Content Area --}}
    <div class="min-h-0 flex-1 overflow-y-auto overscroll-contain pb-6 px-4 md:px-6 w-full mx-auto [scrollbar-width:none] [&::-webkit-scrollbar]:hidden relative z-10" style="max-width:1536px" id="product-list-scroll">

        <div wire:loading.delay wire:target="search, filterCategory, filterStatus, filterPrice, sortField" class="w-full flex justify-center py-12">
            <i class="ph ph-spinner-gap animate-spin text-5xl text-orange-500"></i>
        </div>

        <div wire:loading.class.delay="hidden" wire:target="search, filterCategory, filterStatus, filterPrice, sortField">

            {{-- 5. Main Data Table (LIST VIEW) --}}
            <div x-show="viewMode === 'list'" class="w-full pb-10">
                <table class="w-full text-sm text-left">
                    {{-- 4. Table Header --}}
                    <thead class="sticky top-0 bg-[#FFF8F0]/95 dark:bg-[#0B1120]/95 backdrop-blur z-10 border-b border-slate-200 dark:border-slate-800">
                        <tr>
                                <th class="py-4 pl-2 pr-4 w-10">
                                    <div class="w-5 h-5 rounded-full border border-slate-300 dark:border-slate-600 flex items-center justify-center cursor-pointer hover:border-orange-500 {{ $selectAll ? 'bg-emerald-500 border-emerald-500' : 'bg-transparent' }}">
                                        <input type="checkbox" wire:model.live="selectAll" class="opacity-0 absolute w-5 h-5 cursor-pointer">
                                        @if($selectAll)<i class="ph-bold ph-check text-white text-[10px]"></i>@endif
                                    </div>
                                </th>
                                <th class="py-4 px-4 font-medium text-slate-500 cursor-pointer hover:text-slate-800" @click="toggleSort('name')">
                                    <div class="flex items-center gap-1">Product info <i class="ph-bold ph-arrows-down-up text-[10px] text-slate-300"></i></div>
                                </th>
                                <th class="py-4 px-4 font-medium text-slate-500 cursor-pointer hover:text-slate-800" @click="toggleSort('price')">
                                    <div class="flex items-center gap-1">Price <i class="ph-bold ph-arrows-down-up text-[10px] text-slate-300"></i></div>
                                </th>
                                <th class="py-4 px-4 font-medium text-slate-500 cursor-pointer hover:text-slate-800 hidden sm:table-cell" @click="toggleSort('stock')">
                                    <div class="flex items-center gap-1">Stock <i class="ph-bold ph-arrows-down-up text-[10px] text-slate-300"></i></div>
                                </th>
                                <th class="py-4 px-4 font-medium text-slate-500 text-right w-24">
                                    Active
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                            @forelse($products as $product)
                                <tr class="group hover:bg-white dark:hover:bg-slate-900 transition-colors {{ !$product->is_active ? 'opacity-70 grayscale-[20%]' : '' }}" wire:key="row-{{ $product->id }}">
                                    <td class="py-4 pl-2 pr-4">
                                        <div class="w-5 h-5 rounded-full border border-slate-300 dark:border-slate-600 flex items-center justify-center cursor-pointer hover:border-emerald-500 transition-colors {{ in_array($product->id, $selected) ? 'bg-emerald-500 border-emerald-500' : 'bg-transparent' }}">
                                            <input type="checkbox" value="{{ $product->id }}" wire:model.live="selected" class="opacity-0 absolute w-5 h-5 cursor-pointer">
                                            @if(in_array($product->id, $selected))<i class="ph-bold ph-check text-white text-[10px]"></i>@endif
                                        </div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="flex items-center gap-4">
                                            <a href="{{ route('product.edit', $product->id) }}" wire:navigate.hover class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 overflow-hidden shrink-0">
                                                @if($product->image)
                                                    <img src="{{ Storage::url($product->image) }}" class="w-full h-full object-cover" alt="{{ $product->name }}">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center text-slate-300"><i class="ph-fill ph-image"></i></div>
                                                @endif
                                            </a>
                                            <div class="min-w-0">
                                                <a href="{{ route('product.edit', $product->id) }}" wire:navigate.hover class="font-bold text-slate-900 dark:text-white truncate block hover:text-orange-500 transition-colors">{{ $product->name }}</a>
                                                <p class="text-[11px] text-slate-400 mt-0.5">ID : {{ str_pad($product->id, 6, '0', STR_PAD_LEFT) }}{{ strtoupper(substr($product->name, 0, 2)) }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 text-[13px] font-bold text-slate-700 dark:text-slate-300">
                                        {{ $product->formatted_price }}
                                    </td>
                                    <td class="py-4 px-4 hidden sm:table-cell">
                                        <span class="text-xs font-bold text-slate-600 dark:text-slate-300">{{ $product->total_stock }}</span>
                                    </td>
                                    <td class="py-4 px-4 text-right">
                                        <label class="relative inline-flex items-center cursor-pointer select-none touch-manipulation h-6 w-11 rounded-full bg-slate-200 dark:bg-slate-700 transition-colors ml-auto shadow-inner">
                                            <input type="checkbox" class="sr-only peer" wire:click="toggleAvailability({{ $product->id }})" @checked($product->is_active)>
                                            <div class="w-5 h-5 bg-white rounded-full peer peer-checked:translate-x-[20px] transition-transform shadow-sm absolute left-[2px] peer-checked:bg-emerald-500 border border-slate-300 dark:border-slate-600 peer-checked:border-emerald-600"></div>
                                        </label>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-20 text-center">
                                        <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                                            <i class="ph-bold ph-package text-2xl"></i>
                                        </div>
                                        <p class="text-sm font-bold text-slate-500">Produk tidak ditemukan</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            {{-- GRID VIEW --}}
            <div x-show="viewMode === 'grid'">
                @forelse($products->groupBy(fn($p) => $p->category?->name ?? 'Tanpa Kategori') as $categoryName => $categoryProducts)
                    <div class="mb-8" wire:key="cat-group-{{ str()->slug($categoryName) }}">
                        <div class="flex items-center justify-between mb-4 sticky top-0 bg-[#FFF8F0]/95 dark:bg-[#0B1120]/95 backdrop-blur z-10 py-2">
                            <h2 class="text-lg font-black text-slate-900 dark:text-white flex items-center gap-2">
                                {{ $categoryName }}
                                <span class="text-[11px] font-bold text-slate-500 bg-slate-200/60 dark:bg-slate-800 px-2 py-0.5 rounded-full">{{ $categoryProducts->count() }}</span>
                            </h2>
                        </div>

                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-5">
                            @foreach($categoryProducts as $product)
                                <div class="relative flex h-full flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white transition duration-200 hover:-translate-y-1 hover:shadow-md dark:border-slate-700 dark:bg-slate-900 {{ !$product->is_active ? 'opacity-60 grayscale-[30%]' : '' }}" wire:key="prod-{{ $product->id }}">
                                    
                                    {{-- Checkbox overlay in grid --}}
                                    <div class="absolute right-3 top-3 z-10">
                                        <div class="w-5 h-5 rounded-full border border-white/50 bg-black/20 backdrop-blur flex items-center justify-center cursor-pointer hover:border-emerald-500 hover:bg-emerald-500/80 transition-colors {{ in_array($product->id, $selected) ? 'bg-emerald-500 border-emerald-500' : '' }}">
                                            <input type="checkbox" value="{{ $product->id }}" wire:model.live="selected" class="opacity-0 absolute w-5 h-5 cursor-pointer">
                                            @if(in_array($product->id, $selected))<i class="ph-bold ph-check text-white text-[10px]"></i>@endif
                                        </div>
                                    </div>

                                    @if($product->has_variants)
                                        <span class="absolute left-3 top-3 z-[2] rounded-full bg-blue-600 px-2.5 py-1 text-[10px] font-extrabold text-white shadow-sm">
                                            {{ $product->variants->count() }} Varian
                                        </span>
                                    @endif

                                    @if(!$product->is_active)
                                        <span class="absolute left-3 top-3 z-[2] rounded-full bg-slate-800 px-2.5 py-1 text-[10px] font-extrabold text-white shadow-sm">
                                            Habis
                                        </span>
                                    @endif

                                    @if($product->image)
                                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="mx-2 mt-2 aspect-[4/3] w-[calc(100%-1rem)] shrink-0 rounded-xl object-cover">
                                    @else
                                        <div class="mx-2 mt-2 flex aspect-[4/3] w-[calc(100%-1rem)] shrink-0 items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-800">
                                            <i class="ph-bold ph-image text-4xl text-slate-300 dark:text-slate-600"></i>
                                        </div>
                                    @endif

                                    <div class="flex min-h-0 flex-1 flex-col px-3 pb-3 pt-2">
                                        <h6 class="line-clamp-2 text-sm font-black leading-snug text-slate-950 dark:text-white mb-1" title="{{ $product->name }}">
                                            {{ $product->name }}
                                        </h6>
                                        <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 mb-2 truncate">
                                            Stok: <span class="@if($product->total_stock > 10) text-emerald-500 @elseif($product->total_stock > 0) text-amber-500 @else text-red-500 @endif">{{ $product->total_stock }}</span>
                                        </p>

                                        <div class="mt-auto flex items-center justify-between gap-1 pt-2 border-t border-slate-100 dark:border-slate-800/60">
                                            <div class="min-w-0">
                                                <p class="mb-0 truncate whitespace-nowrap text-xs font-bold text-orange-500">
                                                    {{ $product->formatted_price }}
                                                </p>
                                            </div>
                                            <div class="flex items-center gap-1 shrink-0">
                                                <label class="relative inline-flex items-center justify-center cursor-pointer select-none touch-manipulation h-7 w-9 rounded-lg bg-slate-50 hover:bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 transition-colors" title="Status">
                                                    <input type="checkbox" class="sr-only peer" wire:click="toggleAvailability({{ $product->id }})" @checked($product->is_active)>
                                                    <div class="w-5 h-2.5 bg-slate-300 peer-focus:outline-none rounded-full peer dark:bg-slate-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[9px] after:left-[9px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-2.5 after:w-2.5 after:transition-all dark:border-slate-500 peer-checked:bg-emerald-500"></div>
                                                </label>

                                                <a href="{{ route('product.edit', $product->id) }}" wire:navigate.hover
                                                   class="flex h-7 w-7 items-center justify-center rounded-lg border border-slate-200 bg-white text-sm text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-orange-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:text-orange-400">
                                                    <i class="ph-bold ph-pencil-simple"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 text-center">
                        <div class="w-24 h-24 bg-orange-50 dark:bg-slate-800/50 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="ph-fill ph-package text-5xl text-orange-300 dark:text-slate-600"></i>
                        </div>
                        <h3 class="text-xl font-black text-slate-800 dark:text-white mb-2">Belum ada menu</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-8 max-w-sm mx-auto">Menu atau produk yang Anda cari tidak ditemukan atau belum ditambahkan.</p>
                    </div>
                @endforelse
            </div>

            {{-- Infinite Scroll Trigger --}}
            @if($products->isNotEmpty())
                @if($hasMore)
                    <div wire:intersect="loadMore" class="flex items-center justify-center gap-2 py-6 text-sm font-bold text-slate-500 dark:text-slate-400">
                        <div class="h-4 w-4 animate-spin rounded-full border-2 border-slate-300 border-t-slate-600 dark:border-slate-700 dark:border-t-slate-200"></div>
                        Memuat item lainnya...
                    </div>
                @else
                    <div class="mt-4 border-t border-slate-200/60 dark:border-slate-800/60 py-6 text-center text-sm font-bold text-slate-400 dark:text-slate-500">
                        <i class="ph-bold ph-checks text-base relative top-0.5 mr-1"></i> Semua menu telah dimuat
                    </div>
                @endif
            @endif

        </div>
    </div>
</div>