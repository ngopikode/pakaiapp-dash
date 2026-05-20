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
                <div class="w-5 h-5 flex items-center justify-center transition-transform" :style="`transform: rotate(${pullY * 3}deg)`">↓</div>
            </template>
            <span class="text-xs font-bold" x-text="isRefreshing ? 'Memuat ulang menu...' : 'Tarik untuk refresh'"></span>
        </div>
    </div>

    {{-- ===== STICKY CATEGORY + VIEW TOGGLE BAR ===== --}}
    <div id="menu-start" class="scroll-mt-0 sticky top-[57px] z-40 bg-zinc-50/90 backdrop-blur-xl py-2.5 border-b border-zinc-100/50 shadow-sm shadow-zinc-100/50">
        <div class="max-w-xl mx-auto px-5 flex items-center justify-between gap-3">

            {{-- Category Tabs --}}
            <div class="flex gap-2 overflow-x-auto no-scrollbar flex-1 py-1">
                <button
                    wire:click="setCategory('all')"
                    class="px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-wider whitespace-nowrap transition-all duration-300 active:scale-95 {{ $category === 'all' ? 'bg-zinc-900 text-white shadow-lg shadow-zinc-900/15' : 'bg-white text-zinc-400 border border-zinc-200/80 hover:border-zinc-300 hover:text-zinc-600' }}"
                >Semua</button>

                @foreach($categories as $cat)
                    <button
                        wire:click="setCategory('{{ $cat }}')"
                        class="px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-wider whitespace-nowrap transition-all duration-300 active:scale-95 {{ $category === $cat ? 'bg-zinc-900 text-white shadow-lg shadow-zinc-900/15' : 'bg-white text-zinc-400 border border-zinc-200/80 hover:border-zinc-300 hover:text-zinc-600' }}"
                    >{{ $cat }}</button>
                @endforeach
            </div>

            {{-- View Mode Toggle — 100% Alpine, zero Livewire round-trip --}}
            <div class="flex bg-white p-1 rounded-xl border border-zinc-200/80 shadow-sm shrink-0">
                <button
                    @click="$store.ui.setViewMode('list')"
                    :class="$store.ui.viewMode === 'list' ? 'bg-zinc-900 text-white shadow-sm' : 'text-zinc-300 hover:text-zinc-500'"
                    class="p-2 rounded-lg transition-all duration-200"
                    aria-label="List view"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" x2="21" y1="6" y2="6"/><line x1="3" x2="21" y1="12" y2="12"/><line x1="3" x2="21" y1="18" y2="18"/></svg>
                </button>
                <button
                    @click="$store.ui.setViewMode('grid')"
                    :class="$store.ui.viewMode === 'grid' ? 'bg-zinc-900 text-white shadow-sm' : 'text-zinc-300 hover:text-zinc-500'"
                    class="p-2 rounded-lg transition-all duration-200"
                    aria-label="Grid view"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- ===== LOADING SKELETON (shown only while setCategory is in-flight) ===== --}}
    <div wire:loading.class.remove="hidden" wire:target="setCategory" class="hidden">
        <main
            class="max-w-xl mx-auto px-5 mt-4"
            :class="$store.ui.viewMode === 'grid' ? 'grid grid-cols-2 gap-3' : 'flex flex-col gap-3'"
        >
            @for($s = 0; $s < 6; $s++)
                <div
                    class="bg-white rounded-2xl border border-zinc-100/80 shadow-sm flex overflow-hidden"
                    :class="$store.ui.viewMode === 'grid' ? 'flex-col h-full' : 'flex-row items-center gap-4 p-3'"
                >
                    <div
                        class="bg-zinc-200/70 animate-pulse shrink-0"
                        :class="$store.ui.viewMode === 'grid' ? 'w-full aspect-[4/3]' : 'w-20 h-20 rounded-xl'"
                    ></div>
                    <div
                        class="flex-1 flex flex-col justify-between min-h-0"
                        :class="$store.ui.viewMode === 'grid' ? 'p-3.5 pt-2.5' : ''"
                    >
                        <div>
                            <div class="h-4 bg-zinc-200/70 rounded-md w-3/4 mb-2 animate-pulse"></div>
                            <div x-show="$store.ui.viewMode === 'list'" class="h-3.5 bg-zinc-200/70 rounded-md w-1/3 mb-2 animate-pulse"></div>
                            <div class="h-2.5 bg-zinc-200/70 rounded-md w-full mb-1.5 animate-pulse"></div>
                            <div class="h-2.5 bg-zinc-200/70 rounded-md w-2/3 animate-pulse"></div>
                        </div>
                        <div class="mt-3">
                            <div class="w-full h-8 bg-zinc-200/70 rounded-xl animate-pulse"></div>
                        </div>
                    </div>
                </div>
            @endfor
        </main>
    </div>

    {{-- ===== PRODUCT LIST (hidden while setCategory is in-flight) ===== --}}
    <div wire:loading.remove wire:target="setCategory">
        <main
            class="max-w-xl mx-auto px-5 mt-4"
            :class="$store.ui.viewMode === 'grid' ? 'grid grid-cols-2 gap-3' : 'flex flex-col gap-3'"
        >
            @forelse($this->products as $index => $item)
                @php $delay = $index < 20 ? $index * 50 : 0; @endphp

                {{--
                    Each card IS the <a> tag — no wrapper needed.
                    wire:navigate gives SPA navigation to the product detail page.
                    Inner buttons use @click.stop / onclick stopPropagation+preventDefault
                    to add-to-cart / share WITHOUT triggering navigation.
                --}}
                <a
                    href="/menu/{{ $item['id'] }}"
                    wire:navigate
                    wire:key="product-{{ $item['id'] }}"
                    x-data="{
                        item: {{ json_encode($item) }},
                        get qtyInCart() {
                            const i = cart.find(x => x.cartName === this.item.name);
                            return i ? i.qty : 0;
                        },
                        get showStepper() {
                            return !this.item.has_variants && this.qtyInCart > 0;
                        }
                    }"
                    class="bg-white rounded-2xl border border-zinc-100/80 shadow-sm flex group overflow-hidden relative transition-all duration-300 hover:shadow-lg hover:-translate-y-0.5 animate-slide-up"
                    :class="{
                        'flex-col h-full':                  $store.ui.viewMode === 'grid',
                        'flex-row items-center gap-4 p-3':  $store.ui.viewMode === 'list',
                        'border-[var(--primary-color)]/40 ring-2 ring-[var(--primary-color)]/10': showStepper,
                        'opacity-90': !item.is_active
                    }"
                    style="animation-delay: {{ $delay }}ms"
                >
                    {{-- Product Image --}}
                    <div
                        class="bg-zinc-100 overflow-hidden shrink-0 relative"
                        :class="$store.ui.viewMode === 'grid' ? 'w-full aspect-[4/3]' : 'w-20 h-20 rounded-xl'"
                    >
                        @if($item['image'])
                            <img
                                src="{{ $item['image'] }}"
                                alt="{{ $item['name'] }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                                :class="item.is_active ? '' : 'grayscale'"
                                loading="lazy"
                            />
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-zinc-100 to-zinc-200 flex flex-col items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-300"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                                <span class="text-[9px] font-bold text-zinc-300 uppercase tracking-widest">No Image</span>
                            </div>
                        @endif

                        {{-- Share Buttons: stopPropagation+preventDefault so card nav doesn't fire --}}
                        <div class="absolute top-2.5 left-2.5 flex flex-col gap-2 z-20">
                            <button
                                onclick="event.stopPropagation(); event.preventDefault(); navigator.share ? navigator.share({title:'{{ addslashes($item['name']) }}', url:window.location.origin+'/menu/{{ $item['id'] }}'}) : navigator.clipboard.writeText(window.location.origin+'/menu/{{ $item['id'] }}')"
                                class="bg-white/80 backdrop-blur-md p-2 rounded-full shadow-sm hover:bg-[var(--primary-color)] hover:shadow-md transition-all duration-300 hover:scale-110 active:scale-90"
                                aria-label="Bagikan link">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-700"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" x2="15.42" y1="13.51" y2="17.49"/><line x1="15.41" x2="8.59" y1="6.51" y2="10.49"/></svg>
                            </button>
                            <a
                                href="/menu/{{ $item['id'] }}/story"
                                target="_blank" rel="noreferrer"
                                onclick="event.stopPropagation()"
                                class="bg-white/80 backdrop-blur-md p-2 rounded-full shadow-sm hover:bg-[#25D366] hover:text-white hover:shadow-md transition-all duration-300 hover:scale-110 active:scale-90 group/story"
                                aria-label="Share ke Status WA">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" class="text-zinc-700 group-hover/story:text-white transition-colors"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.888-.788-1.489-1.761-1.663-2.06-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                            </a>
                        </div>

                        {{-- Habis Badge --}}
                        @if(! $item['is_active'])
                            <div class="absolute inset-0 bg-black/10 flex items-center justify-center z-10">
                                <span class="bg-zinc-800/90 text-white text-[10px] font-bold px-2 py-1 rounded-lg backdrop-blur-sm shadow-sm">Habis</span>
                            </div>
                        @endif

                        {{-- Price Badge (Grid only) --}}
                        <div
                            x-show="$store.ui.viewMode === 'grid'"
                            class="absolute top-2.5 right-2.5 bg-white/95 backdrop-blur-sm px-3 py-1.5 rounded-lg text-xs font-black shadow-md border border-white/50 z-20"
                        >{{ $item['formatted_price'] }}</div>
                    </div>

                    {{-- Content --}}
                    <div
                        class="flex-1 flex flex-col justify-between min-h-0"
                        :class="$store.ui.viewMode === 'grid' ? 'p-3.5 pt-2.5' : ''"
                    >
                        <div>
                            <h3 class="font-bold text-sm text-zinc-900 leading-snug mb-0.5 line-clamp-1">{{ $item['name'] }}</h3>
                            {{-- Price (List only) --}}
                            <p x-show="$store.ui.viewMode === 'list'" class="text-sm font-extrabold text-[var(--primary-color)] mb-0.5">{{ $item['formatted_price'] }}</p>
                            <p class="text-[10px] text-zinc-400 line-clamp-2 leading-relaxed">{{ $item['description'] }}</p>
                        </div>

                        {{-- Cart Stepper / Add Button — stopPropagation prevents card navigation --}}
                        <div class="mt-2.5" onclick="event.stopPropagation(); event.preventDefault()">
                            <template x-if="showStepper">
                                <div class="flex items-center justify-between bg-zinc-900 rounded-xl p-1 shadow-md">
                                    <button @click.stop.prevent="updateQty(item.name, -1)" class="w-8 h-8 flex items-center justify-center text-white hover:bg-zinc-700 rounded-lg transition-colors active:scale-90">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" x2="19" y1="12" y2="12"/></svg>
                                    </button>
                                    <span class="font-black text-white text-sm tabular-nums" x-text="qtyInCart"></span>
                                    <button @click.stop.prevent="updateQty(item.name, 1)" class="w-8 h-8 flex items-center justify-center text-zinc-900 bg-[var(--primary-color)] rounded-lg transition-all active:scale-90 hover:brightness-110">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg>
                                    </button>
                                </div>
                            </template>
                            <template x-if="!showStepper">
                                <button
                                    {{ ! $item['is_active'] ? 'disabled' : '' }}
                                    @click.stop.prevent="item.has_variants ? openOption(item) : addToCart(item)"
                                    class="w-full py-2.5 rounded-xl text-[10px] font-black uppercase tracking-wider transition-all duration-300 flex items-center justify-center gap-1.5"
                                    :class="item.is_active
                                        ? 'bg-zinc-50 text-zinc-800 border border-zinc-200/80 hover:bg-[var(--primary-color)] hover:text-zinc-900 hover:border-[var(--primary-color)] active:scale-95 hover:shadow-md'
                                        : 'bg-zinc-100 text-zinc-400 border border-zinc-100 cursor-not-allowed'"
                                >
                                    @if($item['is_active'])
                                        Tambah
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg>
                                    @else
                                        Habis
                                    @endif
                                </button>
                            </template>
                        </div>
                    </div>
                </a>

            @empty
                <div class="col-span-2 py-16 text-center">
                    <p class="text-zinc-300 text-sm font-bold">Belum ada menu untuk kategori ini</p>
                </div>
            @endforelse
        </main>

        {{-- ===== INFINITE SCROLL SENTINEL ===== --}}
        @if($hasMore)
            <div
                wire:key="scroll-sentinel-{{ $perPage }}"
                x-intersect.once="$wire.loadMore()"
                class="flex justify-center py-8"
            >
                <div class="w-5 h-5 border-2 border-[var(--primary-color)] border-t-transparent rounded-full animate-spin"></div>
            </div>
        @endif

    </div>{{-- end wire:loading.remove --}}

    {{-- ===== FLOATING CART BUTTON ===== --}}
    <template x-if="totalQty > 0">
        <div class="fixed bottom-6 left-0 right-0 px-5 z-50 animate-slide-up">
            <button
                @click="openCheckout()"
                class="max-w-xl mx-auto w-full bg-gradient-to-r from-zinc-900 to-zinc-800 text-white p-4 rounded-2xl shadow-lg shadow-zinc-900/20 flex justify-between items-center border border-white/10 relative overflow-hidden group hover:shadow-xl transition-all duration-300 active:scale-[0.98]"
            >
                <div class="absolute inset-0 bg-[var(--primary-color)]/5 group-hover:bg-[var(--primary-color)]/10 transition-colors duration-500"></div>
                <div class="absolute inset-0 animate-shimmer opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="relative flex items-center gap-3.5">
                    <div class="bg-[var(--primary-color)] text-zinc-900 w-11 h-11 rounded-xl flex items-center justify-center font-black text-sm shadow-md shadow-[var(--primary-color)]/25" x-text="totalQty"></div>
                    <div class="text-left">
                        <span class="block text-[9px] font-bold uppercase tracking-widest text-zinc-500 mb-0.5">Total Estimasi</span>
                        <span class="font-bold text-lg text-white font-mono leading-none" x-text="formatPrice(totalCart)"></span>
                    </div>
                </div>
                <div class="relative flex items-center gap-2 pr-1">
                    <span class="text-[10px] font-black uppercase tracking-widest">Checkout</span>
                    <div class="bg-white/10 p-1.5 rounded-full group-hover:bg-[var(--primary-color)]/20 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                    </div>
                </div>
            </button>
        </div>
    </template>
</div>
