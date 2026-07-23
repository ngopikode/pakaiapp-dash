<div class="p-4 md:p-6 w-full max-w-7xl mx-auto">
    
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100 tracking-tight">Menu &amp; Produk</h2>
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
                    class="w-full sm:w-auto px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl shadow-sm transition-all duration-200 flex items-center justify-center gap-2 text-sm">
                <i class="ph-bold ph-plus"></i> Tambah Menu Baru
            </button>
        @else
            <a href="{{ route('product.create') }}" wire:navigate.hover
               class="w-full sm:w-auto px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl shadow-sm transition-all duration-200 flex items-center justify-center gap-2 text-sm">
                <i class="ph-bold ph-plus"></i> Tambah Menu Baru
            </a>
        @endif
    </div>

    {{-- Control Bar (Search & Filter) --}}
    <div class="dash-card p-4 mb-6 sticky top-4 z-10">
        <div class="flex flex-col lg:flex-row gap-4 items-stretch lg:items-center">
            
            {{-- Search Bar --}}
            <div class="relative flex-grow">
                <i class="ph ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-lg"></i>
                <input type="text" wire:model.live.debounce.300ms="search" 
                       class="w-full pl-11 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none text-sm transition" 
                       placeholder="Cari nama menu, deskripsi...">
            </div>

            {{-- Filter Group --}}
            <div class="grid grid-cols-2 sm:flex gap-3 shrink-0">
                <div class="relative min-w-[150px]">
                    <select wire:model.live="filterCategory" 
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-orange-500/20 outline-none text-sm cursor-pointer appearance-none">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <i class="ph ph-caret-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                </div>

                <div class="relative min-w-[150px]">
                    <select wire:model.live="filterStatus" 
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-orange-500/20 outline-none text-sm cursor-pointer appearance-none">
                        <option value="">Semua Status</option>
                        <option value="active">Tersedia</option>
                        <option value="inactive">Habis / Inaktif</option>
                    </select>
                    <i class="ph ph-caret-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                </div>
            </div>

            {{-- Category Manager --}}
            <button wire:click="$dispatch('openModal', { type: 'category', mode: 'create' })"
                    class="px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-semibold rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition flex items-center justify-center gap-2 text-sm shadow-sm">
                <i class="ph-fill ph-folder text-orange-500"></i> Kategori
            </button>
        </div>
    </div>

    {{-- Loading State --}}
    <div wire:loading.delay.longer class="w-full flex justify-center py-6">
        <i class="ph ph-spinner-gap animate-spin text-4xl text-orange-500"></i>
    </div>

    {{-- Product List Container --}}
    <div class="flex flex-col gap-3" wire:loading.class="opacity-50 pointer-events-none">
        @forelse($products as $product)
            <div class="dash-card flex flex-col sm:flex-row items-start sm:items-center justify-between p-4 gap-4 hover:shadow-md transition-all duration-300 {{ !$product->is_active ? 'opacity-75 grayscale-[50%]' : '' }}" wire:key="prod-{{ $product->id }}">
                
                {{-- Product Info & Image --}}
                <div class="flex items-center gap-4 flex-grow min-w-0">
                    {{-- Thumbnail --}}
                    <div class="w-16 h-16 shrink-0 rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 relative">
                        @if($product->image)
                            <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-300 dark:text-slate-600">
                                <i class="ph-fill ph-image text-3xl"></i>
                            </div>
                        @endif
                    </div>

                    {{-- Text Info --}}
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-1.5">
                            <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 truncate" title="{{ $product->name }}">
                                {{ $product->name }}
                            </h3>
                            <span class="px-2.5 py-0.5 text-[0.7rem] font-semibold rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                {{ $product->category?->name ?? 'Tanpa Kategori' }}
                            </span>
                            @if($product->has_variants)
                                <span class="px-2 py-0.5 text-[0.65rem] font-bold rounded-md bg-orange-500/10 text-orange-600 dark:text-orange-400">
                                    {{ $product->variants->count() }} Varian
                                </span>
                            @endif
                        </div>
                        
                        <div class="flex items-center gap-3 text-xs md:text-sm">
                            <span class="font-bold text-orange-500">{{ $product->formatted_price }}</span>
                            <span class="text-slate-300 dark:text-slate-700">•</span>
                            <span class="text-slate-500 font-medium">
                                Stok: 
                                <span class="font-bold @if($product->total_stock > 10) text-green-600 @elseif($product->total_stock > 0) text-amber-500 @else text-red-500 @endif">
                                    {{ $product->total_stock }}
                                </span>
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Action Area (Toggle Stock & Actions) --}}
                <div class="flex items-center justify-between sm:justify-end gap-6 w-full sm:w-auto shrink-0 pt-3 sm:pt-0 border-t sm:border-t-0 border-slate-100 dark:border-slate-800">
                    
                    {{-- Toggle Switch --}}
                    <label class="relative inline-flex items-center cursor-pointer select-none touch-manipulation">
                        <input type="checkbox" class="sr-only peer" wire:click="toggleAvailability({{ $product->id }})" @checked($product->is_active)>
                        <div class="w-10 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-slate-600 peer-checked:bg-green-500"></div>
                        <span class="ml-2.5 text-xs font-bold {{ $product->is_active ? 'text-green-600 dark:text-green-400' : 'text-slate-400' }}">
                            {{ $product->is_active ? 'Tersedia' : 'Habis' }}
                        </span>
                    </label>

                    {{-- Buttons --}}
                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('product.edit', $product->id) }}" wire:navigate.hover
                           class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-50 hover:bg-slate-100 dark:bg-slate-800 dark:hover:bg-slate-700/50 text-slate-600 dark:text-slate-400 transition"
                           title="Edit">
                            <i class="ph-bold ph-pencil-simple text-sm"></i>
                        </a>
                        <button wire:click="deleteProduct({{ $product->id }})"
                                wire:confirm="Hapus {{ $product->name }} secara permanen?"
                                class="w-9 h-9 flex items-center justify-center rounded-xl bg-red-50 dark:bg-red-950/20 hover:bg-red-100 dark:hover:bg-red-900/30 text-red-500 dark:text-red-400 transition"
                                title="Hapus">
                            <i class="ph-bold ph-trash text-sm"></i>
                        </button>
                    </div>
                </div>

            </div>
        @empty
            <div class="py-12">
                <div class="dash-card py-16 text-center flex flex-col items-center">
                    <div class="w-20 h-20 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4">
                        <i class="ph-fill ph-package text-4xl text-slate-400"></i>
                    </div>
                    <h5 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-2">Belum ada menu</h5>
                    <p class="text-sm text-slate-500 mb-6 max-w-sm mx-auto">Menu atau produk yang Anda cari tidak ditemukan atau belum ditambahkan.</p>
                    @if(empty($search) && empty($filterCategory))
                        <button wire:click="$dispatch('openModal', { type: 'category', mode: 'create' })"
                                class="px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl shadow-sm transition flex items-center gap-2 text-sm">
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
