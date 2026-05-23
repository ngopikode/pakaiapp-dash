<div class="px-5 w-full max-w-5xl mx-auto">
    
    {{-- Search (Pure AlpineJS filtering) --}}
    <div class="sticky top-[72px] z-30 bg-zinc-50/95 backdrop-blur py-3 mb-2 flex items-center justify-between gap-3">
        <div class="relative flex-1">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                 class="absolute left-4 top-1/2 -translate-y-1/2 text-zinc-400">
                <circle cx="11" cy="11" r="8"/>
                <path d="m21 21-4.3-4.3"/>
            </svg>
            <input
                x-model="searchQuery"
                type="text"
                placeholder="Cari produk..."
                class="w-full pl-11 pr-4 py-3 rounded-2xl bg-white border border-zinc-200 text-sm font-bold outline-none focus:border-[var(--primary-color)] focus:ring-2 focus:ring-[var(--primary-color)]/20 transition-all shadow-sm"
            >
            <button
                x-show="searchQuery"
                @click="searchQuery = ''"
                class="absolute right-3 top-1/2 -translate-y-1/2 p-1 bg-zinc-100 rounded-full text-zinc-500 hover:text-zinc-800 transition-colors"
                style="display: none;"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                </svg>
            </button>
        </div>
        
        {{-- Tombol Checkout Mengambang di Navbar Search --}}
        <button
            @click="openCheckout()"
            x-show="cart.length > 0"
            x-transition:enter="transition-all duration-300"
            x-transition:enter-start="opacity-0 scale-90 translate-x-4"
            x-transition:enter-end="opacity-100 scale-100 translate-x-0"
            x-transition:leave="transition-all duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-x-0"
            x-transition:leave-end="opacity-0 scale-90 translate-x-4"
            class="h-11 px-4 bg-[var(--primary-color)] text-white rounded-2xl font-black text-xs uppercase tracking-wider flex items-center justify-center gap-2 shadow-lg shadow-[var(--primary-color)]/20 active:scale-95 transition-all shrink-0"
            style="display: none;"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
            <span x-text="cart.length"></span>
        </button>
    </div>

    {{-- Kategori Horizontal Scroll --}}
    @if(count($categories) > 0)
        <div class="overflow-x-auto no-scrollbar py-1 mb-6 -mx-5 px-5">
            <div class="flex items-center gap-2 w-max">
                <button
                    wire:click="setCategory('all')"
                    class="px-4 py-2 rounded-xl text-xs font-black tracking-wide whitespace-nowrap transition-all border-2"
                    @class([
                        'border-[var(--primary-color)] bg-[var(--primary-color)]/10 text-zinc-900 shadow-sm shadow-[var(--primary-color)]/10' => $category === 'all',
                        'border-zinc-200 bg-white text-zinc-500 hover:border-zinc-300' => $category !== 'all'
                    ])
                >
                    Semua
                </button>
                @foreach($categories as $cat)
                    <button
                        wire:click="setCategory('{{ $cat }}')"
                        class="px-4 py-2 rounded-xl text-xs font-black tracking-wide whitespace-nowrap transition-all border-2"
                        @class([
                            'border-[var(--primary-color)] bg-[var(--primary-color)]/10 text-zinc-900 shadow-sm shadow-[var(--primary-color)]/10' => $category === $cat,
                            'border-zinc-200 bg-white text-zinc-500 hover:border-zinc-300' => $category !== $cat
                        ])
                    >
                        {{ $cat }}
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Grid Produk --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 pb-10" id="product-list">
        @forelse($this->products as $p)
            <div
                x-data="{ p: {{ Js::from($p) }} }"
                x-show="!searchQuery || p.name.toLowerCase().includes(searchQuery.toLowerCase()) || p.description.toLowerCase().includes(searchQuery.toLowerCase())"
                class="bg-white rounded-2xl border border-zinc-100 flex flex-col overflow-hidden transition-all duration-300 hover:shadow-xl hover:shadow-[var(--primary-color)]/10 group relative"
                :class="p.is_active ? '' : 'opacity-70 grayscale'"
            >
                {{-- Product Image --}}
                <div class="aspect-square w-full bg-zinc-100 relative overflow-hidden">
                    <template x-if="!p.is_active">
                        <div class="absolute inset-0 bg-white/60 backdrop-blur-[2px] z-10 flex items-center justify-center">
                            <span class="px-3 py-1 bg-zinc-900 text-white rounded-lg text-[10px] font-black uppercase tracking-widest shadow-lg">Habis</span>
                        </div>
                    </template>
                    @if($p['image'])
                        <img src="{{ $p['image'] }}" alt="{{ $p['name'] }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-zinc-300">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                        </div>
                    @endif
                </div>

                {{-- Product Info --}}
                <div class="p-3.5 flex flex-col flex-1">
                    <h3 class="font-extrabold text-sm text-zinc-900 leading-tight mb-1 line-clamp-2" x-text="p.name"></h3>
                    <p class="text-[10px] text-zinc-500 leading-relaxed line-clamp-2 mb-3 flex-1" x-text="p.description"></p>
                    
                    <div class="flex flex-col gap-2 mt-auto">
                        <span class="text-xs font-black text-[var(--primary-color)]" x-text="p.formatted_price"></span>
                        
                        <template x-if="p.is_active">
                            <template x-if="getCartQty(p.name) > 0">
                                <div class="flex items-center justify-between bg-zinc-50 rounded-xl p-1 border border-zinc-100">
                                    <button @click="updateQty(p.name, -1)" class="w-7 h-7 rounded-lg bg-white border border-zinc-200 flex items-center justify-center shadow-sm text-zinc-900 active:scale-90 transition-transform">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" x2="19" y1="12" y2="12"/></svg>
                                    </button>
                                    <span class="text-xs font-black tabular-nums" x-text="getCartQty(p.name)"></span>
                                    <button @click="updateQty(p.name, 1)" class="w-7 h-7 rounded-lg bg-zinc-900 text-white flex items-center justify-center shadow-sm active:scale-90 transition-transform">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg>
                                    </button>
                                </div>
                            </template>
                        </template>
                        
                        <template x-if="p.is_active">
                            <template x-if="getCartQty(p.name) === 0">
                                <button @click="p.has_variants ? openOption(p) : addToCart(p)" class="w-full py-2.5 rounded-xl border border-[var(--primary-color)] text-[var(--primary-color)] text-[10px] font-black uppercase tracking-wider hover:bg-[var(--primary-color)] hover:text-white transition-all active:scale-95 text-center">
                                    <span x-text="p.has_variants ? 'Pilih Opsi' : 'Tambah'"></span>
                                </button>
                            </template>
                        </template>

                        <template x-if="!p.is_active">
                            <button disabled class="w-full py-2.5 rounded-xl border border-zinc-200 text-zinc-400 bg-zinc-50 text-[10px] font-black uppercase tracking-wider text-center cursor-not-allowed">Habis</button>
                        </template>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-2 md:col-span-3 lg:col-span-4 py-16 text-center">
                <div class="w-16 h-16 rounded-3xl bg-zinc-100 flex items-center justify-center mx-auto mb-4 border border-zinc-200/50">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                </div>
                <h3 class="text-sm font-black text-zinc-900 mb-1">Kategori Kosong</h3>
                <p class="text-[10px] text-zinc-500">Belum ada produk di kategori ini.</p>
            </div>
        @endforelse
    </div>

    {{-- Load More (Sama seperti resto) --}}
    @if($this->hasMore)
        <div x-intersect.margin.bottom.100px="$wire.loadMore()" class="flex justify-center py-6 pb-20">
            <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-full shadow-sm border border-zinc-100">
                <div class="flex items-center gap-1">
                    <div class="w-1.5 h-1.5 rounded-full bg-[var(--primary-color)] animate-bounce" style="animation-delay: 0ms"></div>
                    <div class="w-1.5 h-1.5 rounded-full bg-[var(--primary-color)] animate-bounce" style="animation-delay: 150ms"></div>
                    <div class="w-1.5 h-1.5 rounded-full bg-[var(--primary-color)] animate-bounce" style="animation-delay: 300ms"></div>
                </div>
                <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">Memuat lebih...</span>
            </div>
        </div>
    @endif
</div>
