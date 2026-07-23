<div class="p-4 md:p-6 w-full max-w-7xl mx-auto">
    
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Menu & Produk</h2>
            <p class="text-sm text-slate-500 font-medium">Kelola kategori, harga, dan ketersediaan stok menu jualan Anda.</p>
        </div>

        @if($categories->isEmpty())
            <button @click="Swal.fire({
                        title: 'Buat Kategori Terlebih Dahulu',
                        html: 'Anda belum memiliki kategori produk. Silakan buat <b>Kategori baru</b> terlebih dahulu sebelum menambahkan produk.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#F97316',
                        cancelButtonColor: '#6B7280',
                        confirmButtonText: 'Buat Kategori',
                        cancelButtonText: 'Batal',
                        customClass: { popup: 'border-0 shadow-lg rounded-2xl' }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $dispatch('openModal', { type: 'category', mode: 'create' });
                        }
                    })"
                    class="w-full md:w-auto px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-full shadow-sm transition flex items-center justify-center gap-2">
                <i class="ph-bold ph-plus"></i> Tambah Menu Baru
            </button>
        @else
            <a href="{{ route('product.create') }}" wire:navigate.hover
               class="w-full md:w-auto px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-full shadow-sm transition flex items-center justify-center gap-2">
                <i class="ph-bold ph-plus"></i> Tambah Menu Baru
            </a>
        @endif
    </div>

    {{-- Control Bar (Search & Filter) --}}
    <div class="dash-card p-4 mb-6 sticky top-4 z-20">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
            
            {{-- Search Bar --}}
            <div class="md:col-span-5 relative">
                <i class="ph ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-lg"></i>
                <input type="text" wire:model.live.debounce.300ms="search" 
                       class="w-full pl-11 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-full focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none text-sm transition" 
                       placeholder="Cari nama menu, deskripsi...">
            </div>

            {{-- Category Filter --}}
            <div class="md:col-span-3">
                <select wire:model.live="filterCategory" 
                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-full focus:ring-2 focus:ring-orange-500/20 outline-none text-sm cursor-pointer appearance-none">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Status Filter --}}
            <div class="md:col-span-2">
                <select wire:model.live="filterStatus" 
                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-full focus:ring-2 focus:ring-orange-500/20 outline-none text-sm cursor-pointer appearance-none">
                    <option value="">Semua Status</option>
                    <option value="active">Tersedia</option>
                    <option value="inactive">Habis / Inaktif</option>
                </select>
            </div>

            {{-- Category Manager --}}
            <div class="md:col-span-2 flex justify-end">
                <button wire:click="$dispatch('openModal', { type: 'category', mode: 'create' })"
                        class="w-full md:w-auto px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-semibold rounded-full hover:bg-slate-50 dark:hover:bg-slate-700 transition flex items-center justify-center gap-2 text-sm shadow-sm">
                    <i class="ph-fill ph-folder text-orange-500"></i> Kategori
                </button>
            </div>
        </div>
    </div>

    {{-- Loading State --}}
    <div wire:loading.delay.longer class="w-full flex justify-center py-6">
        <i class="ph ph-spinner-gap animate-spin text-4xl text-orange-500"></i>
    </div>

    {{-- Product Grid --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6" wire:loading.class="opacity-50 pointer-events-none">
        @forelse($products as $product)
            <div class="dash-card flex flex-col overflow-hidden group hover:-translate-y-1 hover:shadow-md transition-all duration-300 {{ !$product->is_active ? 'opacity-75 grayscale-[50%]' : '' }}" wire:key="prod-{{ $product->id }}">
                
                {{-- Image Area --}}
                <div class="aspect-square relative bg-slate-100 dark:bg-slate-800 overflow-hidden">
                    @if($product->image)
                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" 
                             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <i class="ph-fill ph-image text-5xl text-slate-300 dark:text-slate-600"></i>
                        </div>
                    @endif

                    {{-- Badges --}}
                    <div class="absolute top-2 left-2 z-10">
                        <span class="px-2.5 py-1 text-[0.65rem] font-semibold rounded-full bg-black/60 text-white backdrop-blur-sm shadow-sm">
                            {{ $product->category?->name ?? 'Tanpa Kategori' }}
                        </span>
                    </div>

                    @if($product->has_variants)
                        <div class="absolute bottom-2 left-2 z-10">
                            <span class="px-2 py-1 text-[0.65rem] font-bold rounded bg-orange-500 text-white shadow-sm">
                                {{ $product->variants->count() }} Varian
                            </span>
                        </div>
                    @endif
                </div>

                {{-- Content Area --}}
                <div class="p-3 md:p-4 flex flex-col flex-grow">
                    <h3 class="font-bold text-slate-800 dark:text-slate-100 text-sm md:text-base leading-tight line-clamp-2 mb-2 min-h-[2.5rem]" title="{{ $product->name }}">
                        {{ $product->name }}
                    </h3>
                    
                    <div class="mb-4">
                        <p class="font-bold text-orange-500">{{ $product->formatted_price }}</p>
                        <p class="text-xs text-slate-500 mt-1">Stok: <span class="font-bold {{ $product->total_stock > 0 ? 'text-slate-700 dark:text-slate-300' : 'text-red-500' }}">{{ $product->total_stock }}</span></p>
                    </div>

                    {{-- Action Footer --}}
                    <div class="mt-auto pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        
                        {{-- Toggle --}}
                        <label class="relative inline-flex items-center cursor-pointer touch-manipulation" title="{{ $product->is_active ? 'Tersedia' : 'Habis' }}">
                            <input type="checkbox" class="sr-only peer" wire:click="toggleAvailability({{ $product->id }})" @checked($product->is_active)>
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-green-500"></div>
                            <span class="ml-2 text-xs font-semibold select-none {{ $product->is_active ? 'text-green-600 dark:text-green-400' : 'text-slate-400' }}">
                                {{ $product->is_active ? 'Aktif' : 'Habis' }}
                            </span>
                        </label>

                        {{-- Edit/Delete Icons --}}
                        <div class="flex items-center gap-1">
                            <a href="{{ route('product.edit', $product->id) }}" wire:navigate.hover
                               class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-400 transition"
                               title="Edit">
                                <i class="ph-fill ph-pencil-simple text-sm"></i>
                            </a>
                            <button wire:click="deleteProduct({{ $product->id }})"
                                    wire:confirm="Hapus {{ $product->name }} secara permanen?"
                                    class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 hover:bg-red-100 dark:bg-slate-800 dark:hover:bg-red-900/30 text-slate-600 hover:text-red-600 dark:text-slate-400 dark:hover:text-red-400 transition"
                                    title="Hapus">
                                <i class="ph-fill ph-trash text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-2 md:col-span-3 lg:col-span-4 py-12">
                <div class="dash-card py-16 text-center flex flex-col items-center">
                    <div class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4">
                        <i class="ph-fill ph-package text-4xl text-slate-400"></i>
                    </div>
                    <h5 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-2">Belum ada menu</h5>
                    <p class="text-sm text-slate-500 mb-6 max-w-sm mx-auto">Menu atau produk yang Anda cari tidak ditemukan atau belum ditambahkan.</p>
                    @if(empty($search) && empty($filterCategory))
                        <button wire:click="$dispatch('openModal', { type: 'category', mode: 'create' })"
                                class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-full shadow-sm transition flex items-center gap-2">
                            <i class="ph-bold ph-plus"></i> Mulai Buat Kategori
                        </button>
                    @endif
                </div>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($products->hasPages())
        <div class="mt-8 flex justify-center">
            {{ $products->links(data: ['scrollTo' => false]) }}
        </div>
    @endif
</div>
