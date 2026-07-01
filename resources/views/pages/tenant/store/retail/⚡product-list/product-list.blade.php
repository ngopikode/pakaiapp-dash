@php use App\Tenant\Models\Core\Product; @endphp
<div
    x-data="{
        isRefreshing: false,
        pullY: 0,
        pullStartY: 0,

        handleTouchStart(e) {
            if (window.scrollY === 0) this.pullStartY = e.touches[0].clientY;
            else this.pullStartY = 0;
        },
        handleTouchMove(e) {
            if (!this.pullStartY || this.isRefreshing) return;
            const dist = e.touches[0].clientY - this.pullStartY;
            if (dist > 0 && window.scrollY <= 0) {
                this.pullY = Math.min(dist, 100);
            }
        },
        async handleTouchEnd() {
            if (!this.pullStartY) return;
            if (this.pullY > 60) {
                this.isRefreshing = true;
                this.pullY = 60;
                await $wire.setCategory($wire.category);
                await new Promise(r => setTimeout(r, 400));
                this.isRefreshing = false;
            }
            this.pullY = 0;
            this.pullStartY = 0;
        }
    }"
    @touchstart="handleTouchStart($event)"
    @touchmove="handleTouchMove($event)"
    @touchend="handleTouchEnd()"
    class="min-h-screen pb-24"
>
    {{-- Pull-to-Refresh Indicator --}}
    <div
        class="max-w-xl mx-auto flex justify-center items-end overflow-hidden transition-all duration-200 ease-out"
        :style="`height: ${isRefreshing ? 60 : Math.min(pullY, 60)}px; opacity: ${isRefreshing ? 1 : Math.min(pullY / 60, 1)}`"
    >
        <div class="flex items-center gap-2 text-zinc-500 pb-3">
            <template x-if="isRefreshing">
                <div class="w-5 h-5 border-2 border-[var(--primary-color)] border-t-transparent rounded-full animate-spin"></div>
            </template>
            <template x-if="!isRefreshing">
                <div class="w-5 h-5 flex items-center justify-center transition-transform"
                     :style="`transform: rotate(${pullY * 3}deg)`">↓
                </div>
            </template>
            <span class="text-xs font-bold" x-text="isRefreshing ? 'Memuat ulang...' : 'Tarik untuk refresh'"></span>
        </div>
    </div>

    {{-- E-commerce Header / Search --}}
    <div class="sticky top-[57px] z-40 bg-white/90 backdrop-blur-xl border-b border-zinc-100 shadow-sm pt-3 pb-3">
        <div class="max-w-xl mx-auto px-5">
            <div class="relative flex items-center">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari produk..."
                    class="w-full bg-zinc-100/80 border-none rounded-2xl py-3 pl-11 pr-4 text-sm focus:ring-2 focus:ring-[var(--primary-color)]/50 focus:bg-white transition-all shadow-inner"
                >
                <div class="absolute left-4 text-zinc-400">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" x2="16.65" y1="21" y2="16.65"></line>
                    </svg>
                </div>
            </div>
            
            {{-- Category Scroller --}}
            <div class="flex gap-2 overflow-x-auto no-scrollbar py-3 mt-1 -mx-2 px-2">
                <button
                    wire:click="setCategory('all')"
                    class="px-5 py-2 rounded-full text-[11px] font-bold tracking-wide whitespace-nowrap transition-all duration-300 {{ $category === 'all' ? 'bg-zinc-900 text-white shadow-md' : 'bg-white text-zinc-500 border border-zinc-200 hover:border-zinc-300 hover:text-zinc-800' }}"
                >
                    Semua
                </button>
                @foreach($categories as $cat)
                    <button
                        wire:click="setCategory('{{ $cat }}')"
                        class="px-5 py-2 rounded-full text-[11px] font-bold tracking-wide whitespace-nowrap transition-all duration-300 {{ $category === $cat ? 'bg-[var(--primary-color)] text-zinc-900 shadow-md' : 'bg-white text-zinc-500 border border-zinc-200 hover:border-zinc-300 hover:text-zinc-800' }}"
                    >
                        {{ $cat }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Loading Skeleton --}}
    <div wire:loading.class.remove="hidden" wire:target="setCategory,lazy,search" class="hidden">
        <main class="max-w-xl mx-auto px-5 mt-4 grid grid-cols-2 gap-3 sm:gap-4">
            @for($s = 0; $s < 6; $s++)
                <div class="bg-white rounded-2xl border border-zinc-100/80 shadow-sm overflow-hidden flex flex-col h-full">
                    <div class="bg-zinc-200 animate-pulse w-full aspect-square"></div>
                    <div class="p-3 flex-1 flex flex-col">
                        <div class="h-4 bg-zinc-200 animate-pulse rounded w-3/4 mb-2"></div>
                        <div class="h-3 bg-zinc-200 animate-pulse rounded w-1/2 mb-3"></div>
                        <div class="mt-auto h-8 bg-zinc-200 animate-pulse rounded-xl"></div>
                    </div>
                </div>
            @endfor
        </main>
    </div>

    {{-- Product Grid --}}
    <div wire:loading.remove wire:target="setCategory,search">
        <main class="max-w-xl mx-auto px-5 mt-4 grid grid-cols-2 gap-3 sm:gap-4">
            @forelse($this->products as $index => $item)
                @php $delay = $index < 20 ? $index * 50 : 0; @endphp
                <div
                    wire:key="product-{{ $item['id'] }}"
                    x-data="{
                        item: {{ json_encode($item) }},
                        get qtyInCart() {
                            const i = cart.find(x => x.cartName === this.item.name);
                            return i ? i.qty : 0;
                        },
                        get showStepper() {
                            return this.item.is_active && !this.item.has_variants && this.qtyInCart > 0;
                        }
                    }"
                    class="bg-white rounded-2xl border border-zinc-100 shadow-sm group overflow-hidden relative flex flex-col h-full transition-all duration-300 hover:shadow-xl hover:-translate-y-1 animate-slide-up"
                    :class="[!item.is_active ? 'opacity-70' : '', showStepper ? 'border-[var(--primary-color)]/50 ring-2 ring-[var(--primary-color)]/20' : '']"
                    style="animation-delay: {{ $delay }}ms"
                >
                    {{-- Detail Overlay Link --}}
                    <a href="{{ route('product.show', new Product($item)) }}" wire:navigate.hover class="absolute inset-0 z-10"></a>

                    {{-- Image --}}
                    <div class="bg-zinc-50 overflow-hidden relative w-full aspect-square">
                        @if($item['image'])
                            <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" loading="lazy" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110 {{ ! $item['is_active'] ? 'grayscale' : '' }}" />
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-zinc-100 to-zinc-200 flex flex-col items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-300">
                                    <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/>
                                    <circle cx="9" cy="9" r="2"/>
                                    <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                                </svg>
                            </div>
                        @endif

                        {{-- Category Badge --}}
                        @if($item['category'])
                            <div class="absolute top-2 left-2 z-20">
                                <span class="bg-white/90 backdrop-blur text-[9px] font-bold px-2 py-1 rounded-md text-zinc-600 shadow-sm">
                                    {{ $item['category'] }}
                                </span>
                            </div>
                        @endif

                        @if(! $item['is_active'])
                            <div class="absolute inset-0 bg-black/20 flex items-center justify-center pointer-events-none z-20">
                                <span class="bg-zinc-900/90 text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-lg">Habis</span>
                            </div>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="p-3 flex-1 flex flex-col justify-between">
                        <div class="mb-3 pointer-events-none z-20">
                            <h3 class="font-bold text-[13px] text-zinc-900 leading-tight mb-1 line-clamp-2">{{ $item['name'] }}</h3>
                            <p class="text-sm font-black text-[var(--primary-color)]">{{ $item['formatted_price'] }}</p>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="mt-auto relative z-20">
                            <template x-if="showStepper">
                                <div class="flex items-center justify-between bg-zinc-900 rounded-xl p-1 shadow-md">
                                    <button @click="updateQty(item.name, -1)" class="w-8 h-8 flex items-center justify-center text-white hover:bg-zinc-700 rounded-lg transition-colors active:scale-90">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="5" x2="19" y1="12" y2="12"/>
                                        </svg>
                                    </button>
                                    <span class="font-black text-white text-sm tabular-nums" x-text="qtyInCart"></span>
                                    <button @click="updateQty(item.name, 1)" class="w-8 h-8 flex items-center justify-center text-zinc-900 bg-[var(--primary-color)] rounded-lg transition-all active:scale-90 hover:brightness-110">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="12" x2="12" y1="5" y2="19"/>
                                            <line x1="5" x2="19" y1="12" y2="12"/>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                            <template x-if="!showStepper">
                                <button
                                    {{ ! $item['is_active'] ? 'disabled' : '' }}
                                    @click="(item.has_variants || (item.extras && item.extras.length > 0)) ? openOption(item) : addToCart(item)"
                                    class="w-full py-2.5 rounded-xl text-[11px] font-bold transition-all duration-300 flex items-center justify-center gap-1.5 {{ $item['is_active'] ? 'bg-[var(--primary-color)]/10 text-zinc-900 hover:bg-[var(--primary-color)] hover:shadow-md active:scale-95' : 'bg-zinc-100 text-zinc-400 cursor-not-allowed' }}"
                                >
                                    @if($item['is_active'])
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="9" cy="21" r="1"></circle>
                                            <circle cx="20" cy="21" r="1"></circle>
                                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                        </svg>
                                        Beli
                                    @else
                                        Habis
                                    @endif
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-2 text-center py-20 flex flex-col items-center justify-center">
                    <div class="w-16 h-16 bg-zinc-100 rounded-full flex items-center justify-center mb-4 text-zinc-400">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" x2="16.65" y1="21" y2="16.65"></line></svg>
                    </div>
                    <h3 class="font-bold text-zinc-800 text-lg mb-1">Produk tidak ditemukan</h3>
                    <p class="text-zinc-500 text-sm">Coba gunakan kata kunci lain atau pilih kategori lain.</p>
                </div>
            @endforelse
        </main>

        {{-- Infinite Scroll Sentinel --}}
        @if($this->hasMore)
            <div wire:key="scroll-sentinel-{{ $category }}-{{ $perPage }}" x-intersect.once="$wire.loadMore()" class="max-w-xl mx-auto px-5 pt-4 pb-8">
                <div wire:loading.class.remove="hidden" wire:target="loadMore" class="hidden">
                    <div class="grid grid-cols-2 gap-3 sm:gap-4">
                        @for($s = 0; $s < 2; $s++)
                            <div class="bg-white rounded-2xl border border-zinc-100/80 shadow-sm overflow-hidden flex flex-col h-full animate-pulse">
                                <div class="bg-zinc-200 w-full aspect-square"></div>
                                <div class="p-3 flex-1 flex flex-col">
                                    <div class="h-4 bg-zinc-200 rounded w-3/4 mb-2"></div>
                                    <div class="h-3 bg-zinc-200 rounded w-1/2 mb-3"></div>
                                    <div class="mt-auto h-8 bg-zinc-200 rounded-xl"></div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Floating Cart Button --}}
    <template x-if="totalQty > 0">
        <div class="fixed bottom-6 left-0 right-0 px-5 z-50 animate-slide-up pointer-events-none">
            <div class="max-w-xl mx-auto pointer-events-auto">
                <button
                    @click="openCheckout()"
                    class="w-full bg-zinc-900 text-white p-4 rounded-2xl shadow-2xl flex justify-between items-center border border-zinc-800 relative overflow-hidden group transition-all duration-300 hover:scale-[1.02] active:scale-[0.98]"
                >
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:animate-[shimmer_1.5s_infinite]"></div>
                    <div class="relative flex items-center gap-3.5">
                        <div class="bg-white text-zinc-900 w-11 h-11 rounded-xl flex items-center justify-center font-black text-sm shadow-md shadow-white/10" x-text="totalQty"></div>
                        <div class="text-left">
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-zinc-400 mb-0.5">Total Keranjang</span>
                            <span class="font-bold text-lg text-white font-mono leading-none" x-text="formatPrice(totalCart)"></span>
                        </div>
                    </div>
                    <div class="relative flex items-center gap-2 pr-2">
                        <span class="text-[11px] font-black uppercase tracking-widest">Bayar</span>
                        <div class="bg-white/10 p-2 rounded-full group-hover:bg-white group-hover:text-zinc-900 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14"></path>
                                <path d="m12 5 7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>
                </button>
            </div>
        </div>
    </template>
</div>
