<div class="flex h-[calc(100vh-4rem)] lg:h-[calc(100vh-72px)] min-h-0 flex-1 flex-col bg-slate-50 dark:bg-[#0B1120] w-full mx-auto"
     x-data="{
         selected: [],
         selectAll: false,
         showMobileFilters: false,
         statusDropdownOpen: false,
         viewMode: localStorage.getItem('productViewMode') || 'list',

         setView(mode) {
             this.viewMode = mode;
             localStorage.setItem('productViewMode', mode);
         },

         toggleSort(field) {
             if ($wire.sortField === field + '_asc') {
                 $wire.sortField = field + '_desc';
             } else {
                 $wire.sortField = field + '_asc';
             }
         },

         toggleSelect(id) {
             id = String(id);
             const idx = this.selected.indexOf(id);
             if (idx > -1) {
                 this.selected.splice(idx, 1);
             } else {
                 this.selected.push(id);
             }
         },

         toggleSelectAll() {
             if (this.selectAll) {
                 this.selected = [];
                 this.selectAll = false;
             } else {
                 const ids = [...document.querySelectorAll('.product-row-checkbox')].map(el => el.value);
                 this.selected = ids;
                 this.selectAll = true;
             }
         },

         clearSelection() {
             this.selected = [];
             this.selectAll = false;
         },

         init() {
             this.$watch('viewMode', val => {
                 if (val === 'list') {
                     this.$nextTick(() => {
                         if (window.livewireInitialized) Livewire.rescan?.();
                     });
                 }
             });

             this.$watch('selected', () => {
                 const total = document.querySelectorAll('.product-row-checkbox').length;
                 this.selectAll = total > 0 && this.selected.length === total;
             });

             $wire.on('clear-selection', () => {
                 this.selected = [];
                 this.selectAll = false;
             });
         }
     }">

    <div class="shrink-0 px-4 md:px-6 pt-1 md:pt-2 pb-0 w-full mx-auto" style="max-width:1536px">

        {{-- 1. Top Action Bar --}}
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-2 md:gap-3 mb-1 md:mb-2">

            {{-- Bagian Kiri: Search & Toggle Filter Mobile --}}
            <div class="flex items-center gap-2 w-full md:w-auto min-w-0">
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

                <div class="relative flex-1 md:w-64 h-10">
                    <i class="ph-bold ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm md:text-base"></i>
                    <input type="text" wire:model.live.debounce.500ms="search" placeholder="Search..."
                           class="w-full h-full pl-8 md:pl-9 pr-7 py-2 rounded-full border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-sm focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none text-slate-900 dark:text-white shadow-sm transition-shadow">
                    @if($search)
                        <button type="button" wire:click="$set('search', '')" @click="clearSelection()" class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-red-500">
                            <i class="ph-bold ph-x text-sm"></i>
                        </button>
                    @endif
                </div>

                {{-- Toggle Filter untuk Mobile --}}
                <button @click="showMobileFilters = !showMobileFilters"
                        class="md:hidden flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                    <i class="ph-bold ph-faders text-base"></i>
                </button>
            </div>

            {{-- Bagian Kanan: Aksi Cepat Desktop & Tambah Produk --}}
            <div class="flex items-center justify-between w-full md:w-auto md:justify-end gap-2 shrink-0">
                <div class="hidden lg:flex items-center gap-2 bg-slate-100 dark:bg-slate-800/50 px-3 py-1.5 rounded-full text-sm font-medium text-slate-600 dark:text-slate-300">
                    <span class="text-slate-400">Sort by:</span>
                    <select wire:model.live="sortField" class="bg-transparent border-none p-0 focus:ring-0 cursor-pointer font-bold outline-none text-sm">
                        <option value="newest">Default</option>
                        <option value="name_asc">Name (A-Z)</option>
                        <option value="name_desc">Name (Z-A)</option>
                        <option value="price_asc">Price (Low-High)</option>
                        <option value="price_desc">Price (High-Low)</option>
                        <option value="stock_desc">Highest Stock</option>
                    </select>
                </div>

                <div class="flex gap-2 w-full md:w-auto">
                    <button @click="window.dispatchEvent(new CustomEvent('open-category-modal'))"
                            class="flex-1 md:flex-none px-4 py-2.5 bg-white dark:bg-slate-900 border border-orange-500/30 text-orange-600 dark:text-orange-400 font-bold rounded-full hover:bg-orange-50 dark:hover:bg-orange-500/10 transition flex items-center justify-center gap-2 text-sm shadow-sm h-10">
                        <i class="ph-bold ph-folders text-base"></i> <span class="hidden lg:inline">Categories</span>
                    </button>
                    <button @click="$dispatch('open-product-form', { productId: null })"
                       class="flex-1 md:flex-none px-5 py-2.5 bg-[#E65C2C] hover:bg-[#D44A1A] text-white font-bold rounded-full shadow-sm transition-all duration-200 flex items-center justify-center gap-2 text-sm h-10 whitespace-nowrap">
                        <i class="ph-bold ph-plus text-base"></i> <span class="hidden md:inline">Add new product</span><span class="md:hidden">Add</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- 2. Secondary Filter Bar (Responsive Collapse) --}}
        <div :class="showMobileFilters ? '!flex' : ''"
             class="hidden md:flex flex-col md:flex-row md:flex-wrap items-stretch md:items-end gap-2 md:gap-4 mt-0 md:mt-2 pb-1 border-b border-slate-200 dark:border-slate-800 relative z-20">

            {{-- Sort (Mobile only) --}}
            <div class="flex lg:hidden flex-col gap-1 min-w-[130px] flex-1">
                <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400">Sort</label>
                <div class="relative">
                    <select wire:model.live="sortField" class="w-full bg-slate-100 dark:bg-slate-800 border-0 rounded-lg px-3 py-1.5 text-xs font-bold text-slate-700 dark:text-slate-200 appearance-none focus:ring-2 focus:ring-orange-500/20 cursor-pointer h-9">
                        <option value="newest">Default</option>
                        <option value="name_asc">Name (A-Z)</option>
                        <option value="name_desc">Name (Z-A)</option>
                        <option value="price_asc">Price (Low-High)</option>
                        <option value="price_desc">Price (High-Low)</option>
                        <option value="stock_desc">Highest Stock</option>
                    </select>
                    <i class="ph-bold ph-caret-down absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[10px]"></i>
                </div>
            </div>

            <div class="flex flex-col gap-1 min-w-[130px] flex-1 md:flex-none">
                <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400">Category</label>
                <div class="relative">
                    <select wire:model.live="filterCategory" @change="clearSelection()" class="w-full bg-slate-100 dark:bg-slate-800 border-0 rounded-lg px-3 py-1.5 text-xs font-bold text-slate-700 dark:text-slate-200 appearance-none focus:ring-2 focus:ring-orange-500/20 cursor-pointer h-9">
                        <option value="">All</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <i class="ph-bold ph-caret-down absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[10px]"></i>
                </div>
            </div>

            <div class="flex flex-col gap-1 min-w-[130px] flex-1 md:flex-none">
                <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400">Price</label>
                <div class="relative">
                    <select wire:model.live="filterPrice" @change="clearSelection()" class="w-full bg-slate-100 dark:bg-slate-800 border-0 rounded-lg px-3 py-1.5 text-xs font-bold text-slate-700 dark:text-slate-200 appearance-none focus:ring-2 focus:ring-orange-500/20 cursor-pointer h-9">
                        <option value="">All</option>
                        <option value="0-50000">Under 50k</option>
                        <option value="50000-100000">50k-100k</option>
                        <option value="above-100k">Above 100k</option>
                    </select>
                    <i class="ph-bold ph-caret-down absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[10px]"></i>
                </div>
            </div>

            <div class="flex flex-col gap-1 min-w-[130px] flex-1 md:flex-none relative">
                <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400">Status</label>
                <button type="button" @click="statusDropdownOpen = !statusDropdownOpen"
                        class="w-full flex items-center justify-between bg-orange-50 dark:bg-orange-500/10 border border-orange-200 dark:border-orange-500/30 rounded-lg px-3 py-1.5 text-xs font-bold text-orange-600 dark:text-orange-400 h-9 text-left transition-colors">
                    <span>{{ $filterStatus === 'active' ? 'Active' : ($filterStatus === 'inactive' ? 'Inactive' : 'All') }}</span>
                    <i class="ph-bold ph-caret-down text-[10px] transition-transform shrink-0" :class="statusDropdownOpen ? 'rotate-180' : ''"></i>
                </button>

                <div x-show="statusDropdownOpen" @click.outside="statusDropdownOpen = false" style="display: none"
                     x-transition.opacity.duration.200ms
                     class="absolute top-[calc(100%+4px)] left-0 w-full md:w-[160px] bg-white dark:bg-slate-800 border border-orange-100 dark:border-orange-500/20 rounded-xl shadow-xl p-1.5 z-50">
                    <button wire:click="$set('filterStatus', ''); statusDropdownOpen = false" @click="clearSelection()" class="w-full text-left px-3 py-2 rounded-lg text-xs font-bold transition-colors" :class="$wire.filterStatus === '' ? 'bg-orange-50 text-orange-600 dark:bg-orange-500/10' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-700'">All</button>
                    <button wire:click="$set('filterStatus', 'active'); statusDropdownOpen = false" @click="clearSelection()" class="w-full text-left px-3 py-2 rounded-lg text-xs font-bold transition-colors" :class="$wire.filterStatus === 'active' ? 'bg-orange-50 text-orange-600 dark:bg-orange-500/10' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-700'">Active</button>
                    <button wire:click="$set('filterStatus', 'inactive'); statusDropdownOpen = false" @click="clearSelection()" class="w-full text-left px-3 py-2 rounded-lg text-xs font-bold transition-colors" :class="$wire.filterStatus === 'inactive' ? 'bg-orange-50 text-orange-600 dark:bg-orange-500/10' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-700'">Inactive</button>
                </div>
            </div>

            {{-- Mobile View Toggle --}}
            <div class="flex sm:hidden items-center justify-center rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-0.5 shadow-sm h-9 w-full mt-1">
                <button @click="setView('list')"
                        class="flex-1 h-full flex items-center justify-center rounded-md transition-colors text-[10px] font-bold"
                        :class="viewMode === 'list' ? 'bg-slate-100 dark:bg-slate-700 text-slate-900 dark:text-white' : 'text-slate-400 hover:text-slate-700 dark:hover:text-slate-300'">
                    <i class="ph-bold ph-list mr-1"></i> List
                </button>
                <button @click="setView('grid')"
                        class="flex-1 h-full flex items-center justify-center rounded-md transition-colors text-[10px] font-bold"
                        :class="viewMode === 'grid' ? 'bg-slate-100 dark:bg-slate-700 text-slate-900 dark:text-white' : 'text-slate-400 hover:text-slate-700 dark:hover:text-slate-300'">
                    <i class="ph-bold ph-grid-four mr-1"></i> Grid
                </button>
            </div>
        </div>

        {{-- Bulk Actions Toolbar --}}
        <div x-show="selected.length > 0" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="flex items-center gap-2 md:gap-3 mt-1 p-2 md:p-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-100 dark:border-blue-800/50 overflow-x-auto [scrollbar-width:none]">
            <span class="text-xs md:text-sm font-bold text-blue-800 dark:text-blue-400 px-2 whitespace-nowrap" x-text="selected.length + ' items selected'"></span>
            <div class="flex gap-1.5 md:gap-2">
                <button @click="$wire.bulkToggleStatus(selected, true); clearSelection()" class="px-3 py-1.5 bg-white dark:bg-slate-800 text-emerald-600 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-bold shadow-sm hover:bg-slate-50 whitespace-nowrap">Set Active</button>
                <button @click="$wire.bulkToggleStatus(selected, false); clearSelection()" class="px-3 py-1.5 bg-white dark:bg-slate-800 text-slate-500 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-bold shadow-sm hover:bg-slate-50 whitespace-nowrap">Set Inactive</button>
                <button @click="confirm('Delete ' + selected.length + ' items?') && $wire.bulkDelete(selected).then(() => clearSelection())" class="px-3 py-1.5 bg-red-500 text-white border border-red-600 rounded-lg text-xs font-bold shadow-sm hover:bg-red-600 whitespace-nowrap">Delete Selected</button>
            </div>
        </div>
    </div>

    {{-- Scrollable Inner Content Area --}}
    <div class="min-h-0 flex-1 overflow-y-auto overscroll-contain pb-6 px-4 md:px-6 w-full mx-auto [scrollbar-width:none] [&::-webkit-scrollbar]:hidden relative z-10" style="max-width:1536px" id="product-list-scroll">

        {{-- Skeleton Loading: muncul hanya setelah 500ms loading (Google-style) --}}
        <div wire:loading.delay.longer wire:target="search, filterCategory, filterStatus, filterPrice, sortField" class="w-full pb-10">
            <div x-show="viewMode === 'list'" class="space-y-3">
                @foreach(range(1, 6) as $i)
                    <div class="h-20 rounded-xl bg-slate-200 dark:bg-slate-800 animate-pulse"></div>
                @endforeach
            </div>
            <div x-show="viewMode === 'grid'" class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-5">
                @foreach(range(1, 8) as $i)
                    <div class="h-56 rounded-2xl bg-slate-200 dark:bg-slate-800 animate-pulse"></div>
                @endforeach
            </div>
        </div>

        {{-- Data Container: opacity 40% saat loading, skeleton tidak muncul jika cepat. Sembunyikan total jika masuk delay longer --}}
        <div wire:loading.class="opacity-40 pointer-events-none" wire:loading.class.delay.longer="hidden" wire:target="search, filterCategory, filterStatus, filterPrice, sortField" style="transition: opacity .2s">

            {{-- LIST VIEW --}}
            <div x-show="viewMode === 'list'" x-cloak
                 x-transition.opacity.duration.200ms
                 class="w-full pb-10">
                <table class="w-full text-sm text-left">
                    <thead class="sticky top-0 bg-slate-50/95 dark:bg-[#0B1120]/95 backdrop-blur z-10 border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="py-3 pl-2 pr-4 w-10">
                                <div class="w-5 h-5 rounded-full border border-slate-300 dark:border-slate-600 flex items-center justify-center cursor-pointer hover:border-orange-500 transition-colors"
                                     :class="selectAll ? 'bg-emerald-500 border-emerald-500' : 'bg-transparent'"
                                     @click="toggleSelectAll()">
                                    <input type="checkbox" class="opacity-0 absolute w-5 h-5 cursor-pointer" :checked="selectAll" @click.stop="toggleSelectAll()">
                                    <i x-show="selectAll" class="ph-bold ph-check text-white text-[10px]"></i>
                                </div>
                            </th>
                            <th class="py-3 px-4 font-medium text-slate-500 cursor-pointer hover:text-slate-800" @click="toggleSort('name')">
                                <div class="flex items-center gap-1">Product info <i class="ph-bold ph-arrows-down-up text-[10px] text-slate-300"></i></div>
                            </th>
                            <th class="py-3 px-4 font-medium text-slate-500 cursor-pointer hover:text-slate-800" @click="toggleSort('price')">
                                <div class="flex items-center gap-1">Price <i class="ph-bold ph-arrows-down-up text-[10px] text-slate-300"></i></div>
                            </th>
                            <th class="py-3 px-4 font-medium text-slate-500 cursor-pointer hover:text-slate-800 hidden md:table-cell" @click="toggleSort('stock')">
                                <div class="flex items-center gap-1">Stock <i class="ph-bold ph-arrows-down-up text-[10px] text-slate-300"></i></div>
                            </th>
                            <th class="py-3 px-4 font-medium text-slate-500 text-right w-20">Active</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                        @forelse($products as $product)
                            <tr class="group hover:bg-white dark:hover:bg-slate-900 transition-colors {{ !$product->is_active ? 'opacity-60' : '' }}" wire:key="row-{{ $product->id }}">
                                <td class="py-2.5 pl-2 pr-4">
                                    <div class="w-5 h-5 rounded-full border border-slate-300 dark:border-slate-600 flex items-center justify-center cursor-pointer hover:border-emerald-500 transition-colors"
                                         :class="selected.includes('{{ $product->id }}') ? 'bg-emerald-500 border-emerald-500' : 'bg-transparent'"
                                         @click="toggleSelect('{{ $product->id }}')">
                                        <input type="checkbox" value="{{ $product->id }}" class="product-row-checkbox opacity-0 absolute w-5 h-5 cursor-pointer" :checked="selected.includes('{{ $product->id }}')" @click.stop="toggleSelect('{{ $product->id }}')">
                                        <i x-show="selected.includes('{{ $product->id }}')" class="ph-bold ph-check text-white text-[10px]"></i>
                                    </div>
                                </td>
                                <td class="py-2.5 px-4">
                                    <div class="flex items-center gap-3">
                                        <button @click="$dispatch('open-product-form', { productId: {{ $product->id }} })" class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 overflow-hidden shrink-0">
                                            @if($product->image)
                                                <img src="{{ Storage::url($product->image) }}" class="w-full h-full object-cover" alt="{{ $product->name }}">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-slate-300"><i class="ph-fill ph-image"></i></div>
                                            @endif
                                        </button>
                                        <div class="min-w-0">
                                            <button @click="$dispatch('open-product-form', { productId: {{ $product->id }} })" class="font-bold text-slate-900 dark:text-white truncate block hover:text-orange-500 transition-colors text-sm leading-tight text-left">{{ $product->name }}</button>
                                            <p class="text-[10px] text-slate-400 mt-0.5">ID : {{ str_pad($product->id, 6, '0', STR_PAD_LEFT) }}{{ strtoupper(substr($product->name, 0, 2)) }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-2.5 px-4 text-xs font-bold text-slate-700 dark:text-slate-300">{{ $product->formatted_price }}</td>
                                <td class="py-2.5 px-4 hidden md:table-cell">
                                    <span class="text-xs font-bold"
                                          @if($product->total_stock > 10) class="text-emerald-600"
                                          @elseif($product->total_stock > 0) class="text-amber-600"
                                          @else class="text-red-500"
                                          @endif>{{ $product->total_stock }}</span>
                                </td>
                                <td class="py-2.5 px-4 text-right">
                                    <label class="relative inline-flex items-center cursor-pointer select-none touch-manipulation h-6 w-11 rounded-full bg-slate-200 dark:bg-slate-700 transition-colors ml-auto shadow-inner">
                                        <input type="checkbox" class="sr-only peer"
                                               wire:click="toggleAvailability({{ $product->id }})"
                                               @checked($product->is_active)>
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
            <div x-show="viewMode === 'grid'" x-cloak
                 x-transition.opacity.duration.200ms>
                @forelse($products->groupBy(fn($p) => $p->category?->name ?? 'Tanpa Kategori') as $categoryName => $categoryProducts)
                    <div class="mb-8" wire:key="cat-group-{{ str()->slug($categoryName) }}">
                        <div class="flex items-center justify-between mb-4 sticky top-0 bg-slate-50/95 dark:bg-[#0B1120]/95 backdrop-blur z-10 py-2">
                            <h2 class="text-lg font-black text-slate-900 dark:text-white flex items-center gap-2">
                                {{ $categoryName }}
                                <span class="text-[11px] font-bold text-slate-500 bg-slate-200/60 dark:bg-slate-800 px-2 py-0.5 rounded-full">{{ $categoryProducts->count() }}</span>
                            </h2>
                        </div>

                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-5">
                            @foreach($categoryProducts as $product)
                                <div class="relative flex h-full flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white transition duration-200 hover:-translate-y-1 hover:shadow-md dark:border-slate-700 dark:bg-slate-900 {{ !$product->is_active ? 'opacity-60' : '' }}" wire:key="prod-{{ $product->id }}">

                                    <div class="absolute right-3 top-3 z-10">
                                        <div class="w-5 h-5 rounded-full border border-white/50 bg-black/20 backdrop-blur flex items-center justify-center cursor-pointer hover:border-emerald-500 hover:bg-emerald-500/80 transition-colors"
                                             :class="selected.includes('{{ $product->id }}') ? 'bg-emerald-500 border-emerald-500' : ''"
                                             @click="toggleSelect('{{ $product->id }}')">
                                            <input type="checkbox" value="{{ $product->id }}" class="product-row-checkbox opacity-0 absolute w-5 h-5 cursor-pointer" :checked="selected.includes('{{ $product->id }}')" @click.stop="toggleSelect('{{ $product->id }}')">
                                            <i x-show="selected.includes('{{ $product->id }}')" class="ph-bold ph-check text-white text-[10px]"></i>
                                        </div>
                                    </div>

                                    @if($product->has_variants)
                                        <span class="absolute left-3 top-3 z-[2] rounded-full bg-blue-600 px-2.5 py-1 text-[10px] font-extrabold text-white shadow-sm">{{ $product->variants->count() }} Varian</span>
                                    @endif

                                    @if(!$product->is_active)
                                        <span class="absolute left-3 top-3 z-[2] rounded-full bg-slate-800 px-2.5 py-1 text-[10px] font-extrabold text-white shadow-sm">Habis</span>
                                    @endif

                                    @if($product->image)
                                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="mx-2 mt-2 aspect-[4/3] w-[calc(100%-1rem)] shrink-0 rounded-xl object-cover">
                                    @else
                                        <div class="mx-2 mt-2 flex aspect-[4/3] w-[calc(100%-1rem)] shrink-0 items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-800">
                                            <i class="ph-bold ph-image text-4xl text-slate-300 dark:text-slate-600"></i>
                                        </div>
                                    @endif

                                    <div class="flex min-h-0 flex-1 flex-col px-3 pb-3 pt-2">
                                        <h6 class="line-clamp-2 text-sm font-black leading-snug text-slate-950 dark:text-white mb-1" title="{{ $product->name }}">{{ $product->name }}</h6>
                                        <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 mb-2 truncate">
                                            Stok: <span class="@if($product->total_stock > 10) text-emerald-500 @elseif($product->total_stock > 0) @else @endif">{{ $product->total_stock }}</span>
                                        </p>

                                        <div class="mt-auto flex items-center justify-between gap-1 pt-2 border-t border-slate-100 dark:border-slate-800/60">
                                            <div class="min-w-0">
                                                <p class="mb-0 truncate whitespace-nowrap text-xs font-bold text-orange-500">{{ $product->formatted_price }}</p>
                                            </div>
                                            <div class="flex items-center gap-1 shrink-0">
                                                <label class="relative inline-flex items-center justify-center cursor-pointer select-none touch-manipulation h-7 w-9 rounded-lg bg-slate-50 hover:bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 transition-colors">
                                                    <input type="checkbox" class="sr-only peer"
                                                           wire:click="toggleAvailability({{ $product->id }})"
                                                           @checked($product->is_active)>
                                                    <div class="w-5 h-2.5 bg-slate-300 peer-focus:outline-none rounded-full peer dark:bg-slate-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[9px] after:left-[9px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-2.5 after:w-2.5 after:transition-all dark:border-slate-500 peer-checked:bg-emerald-500"></div>
                                                </label>

                                                <button @click="$dispatch('open-product-form', { productId: {{ $product->id }} })"
                                                   class="flex h-7 w-7 items-center justify-center rounded-lg border border-slate-200 bg-white text-sm text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-orange-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:text-orange-400">
                                                    <i class="ph-bold ph-pencil-simple"></i>
                                                </button>
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
