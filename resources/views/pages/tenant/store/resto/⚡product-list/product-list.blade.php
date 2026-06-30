@php use App\Tenant\Models\Core\Product; @endphp
<div
    {{--    wire:init="$set('lazy', false)"--}}
    x-data="{
        viewMode: 'grid',
        showFilter: false
    }"
    @refresh-menu-data.window="$wire.setCategory($wire.category).then(() => { isRefreshing = false; pullY = 0; })"
>

    {{-- ===== STICKY SEARCH & CATEGORY BAR ===== --}}
    <div id="menu-start" class="scroll-mt-0 sticky top-0 z-40 bg-[var(--surface)]/90 backdrop-blur-xl border-b border-[var(--border)] shadow-sm shadow-[var(--border)]">
        <div class="max-w-xl mx-auto px-4 py-3 flex gap-2">
            <div class="relative flex-1">
                <input placeholder="Cari apa hari ini?" wire:model.live.debounce.500ms="search" class="w-full pl-10 pr-4 py-2.5 rounded-xl text-sm focus:outline-none shadow-sm transition-all border border-[var(--border)] bg-[var(--surface)] text-[var(--foreground)]" type="text" value="">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 absolute left-3.5 top-1/2 transform -translate-y-1/2 text-[var(--text-secondary)]" aria-hidden="true"><path d="m21 21-4.34-4.34"></path><circle cx="11" cy="11" r="8"></circle></svg>
            </div>
            <button @click="showFilter = true" class="p-2.5 rounded-xl border border-[var(--border)] bg-[var(--surface)] text-[var(--foreground)] shadow-sm active:scale-95 transition-all relative">
                @if($sort !== 'popular' || $minPrice || $maxPrice)
                    <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-[var(--surface)]"></span>
                @endif
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
            </button>
        </div>
        <div class="max-w-xl mx-auto px-5 pb-3 flex items-center justify-between gap-3">
            <div class="flex gap-2 overflow-x-auto no-scrollbar flex-1 py-1">
                <button wire:click="setCategory('all')" class="px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-wider whitespace-nowrap transition-all duration-300 active:scale-95 border cursor-pointer {{ $category === 'all' ? 'bg-[var(--primary-color)] text-black border-[var(--primary-color)] shadow-lg shadow-[var(--primary-color)]/20' : 'bg-[var(--surface)] text-[var(--text-secondary)] border-[var(--border)] hover:bg-[var(--bg-soft)]' }}">Semua</button>
                @if($this->hasPromoItems)
                    <button wire:click="setCategory('promo')" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-wider whitespace-nowrap transition-all duration-300 active:scale-95 border cursor-pointer {{ $category === 'promo' ? 'bg-red-500 text-white shadow-lg border-red-500' : 'bg-red-50 text-red-600 border-red-200 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:border-red-900/50 dark:hover:bg-red-900/40' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/></svg>
                        Promo
                    </button>
                @endif
                @foreach($categories as $cat)
                    <button wire:click="setCategory('{{ $cat }}')" class="px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-wider whitespace-nowrap transition-all duration-300 active:scale-95 border cursor-pointer {{ $category === $cat ? 'bg-[var(--primary-color)] text-black border-[var(--primary-color)] shadow-lg shadow-[var(--primary-color)]/20' : 'bg-[var(--surface)] text-[var(--text-secondary)] border-[var(--border)] hover:bg-[var(--bg-soft)]' }}">{{ $cat }}</button>
                @endforeach
            </div>
            <div class="flex bg-[var(--surface)] p-1 rounded-xl border border-[var(--border)] shadow-sm shrink-0">
                <button @click="viewMode = 'list'" :class="viewMode === 'list' ? 'bg-[var(--foreground)] text-[var(--background)] shadow-sm' : 'text-[var(--text-secondary)] hover:text-[var(--foreground)]'" class="p-2 rounded-lg transition-all duration-200"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" x2="21" y1="6" y2="6"/><line x1="3" x2="21" y1="12" y2="12"/><line x1="3" x2="21" y1="18" y2="18"/></svg></button>
                <button @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-[var(--foreground)] text-[var(--background)] shadow-sm' : 'text-[var(--text-secondary)] hover:text-[var(--foreground)]'" class="p-2 rounded-lg transition-all duration-200"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg></button>
            </div>
        </div>
    </div>

    {{-- Loading Skeleton (Pake class bg-skeleton dari store.css) --}}
    <div wire:loading.class.remove="hidden" wire:target="setCategory,lazy" class="hidden">
        <main
            class="max-w-xl mx-auto px-5 mt-4"
            :class="viewMode === 'grid' ? 'grid grid-cols-2 gap-3' : 'flex flex-col gap-3'"
        >
            @for($s = 0; $s < 6; $s++)
                <div
                    class="bg-[var(--surface)] rounded-2xl border border-[var(--border)] shadow-sm flex overflow-hidden"
                    :class="viewMode === 'grid' ? 'flex-col h-full' : 'flex-row items-center gap-4 p-3'"
                >
                    {{-- Image Skeleton --}}
                    <div
                        class="bg-skeleton shrink-0"
                        :class="viewMode === 'grid' ? 'w-full aspect-[4/3]' : 'w-20 h-20 rounded-xl'"
                    ></div>

                    {{-- Content Skeleton --}}
                    <div
                        class="flex-1 flex flex-col justify-between min-h-0"
                        :class="viewMode === 'grid' ? 'p-3.5 pt-2.5' : ''"
                    >
                        <div>
                            <div class="h-4 bg-skeleton rounded-md w-3/4 mb-2"></div>

                            {{-- Menggunakan x-show Alpine menggantikan @if PHP --}}
                            <div x-show="viewMode === 'list'" class="h-3.5 bg-skeleton rounded-md w-1/3 mb-2"></div>

                            <div class="h-2.5 bg-skeleton rounded-md w-full mb-1.5"></div>
                            <div class="h-2.5 bg-skeleton rounded-md w-2/3"></div>
                        </div>
                        <div class="mt-3">
                            <div class="w-full h-8 bg-skeleton rounded-xl"></div>
                        </div>
                    </div>
                </div>
            @endfor
        </main>
    </div>

    {{-- ===== PRODUCT LIST ===== --}}
    <div wire:loading.remove wire:target="setCategory">
        <main
            class="max-w-xl mx-auto px-5 mt-4"
            :class="viewMode === 'grid' ? 'grid grid-cols-2 gap-3' : 'flex flex-col gap-3'"
        >
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
                    class="bg-[var(--surface)] rounded-2xl border border-[var(--border)] flex flex-col h-full group overflow-hidden relative transition-all duration-500 hover:shadow-2xl hover:shadow-[var(--primary-color)]/10 hover:-translate-y-1 animate-slide-up"
                    :class="[
                        viewMode === 'list' ? '!flex-row !h-auto items-center gap-3 p-3' : '',
                        !item.is_active ? 'opacity-70 grayscale-[30%]' : '',
                        showStepper ? 'border-[var(--primary-color)] ring-4 ring-[var(--primary-color)]/10 bg-gradient-to-b from-[var(--primary-color)]/5 to-transparent' : 'shadow-lg shadow-black/5 dark:shadow-none'
                    ]"
                    style="animation-delay: {{ $delay }}ms"
                >

                    {{-- OVERLAY LINK TRANSPARAN UNTUK DETAIL (z-10) --}}
                    @if($item['is_active'])
                        <a href="{{ route('product.show', new Product($item)) }}" wire:navigate.hover
                           class="absolute inset-0 z-10"></a>
                    @endif

                    {{-- 1. Image Wrapper --}}
                    <div
                        class="bg-[var(--bg-soft)] overflow-hidden shrink-0 relative transition-all duration-500 group-hover:shadow-inner w-full aspect-[5/4]"
                        :class="viewMode === 'list' ? '!w-24 !h-24 !aspect-square rounded-2xl' : ''"
                    >


                        @if($item['image'])
                            <img
                                src="{{ $item['image'] }}"
                                alt="{{ $item['name'] }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 {{ ! $item['is_active'] ? 'grayscale' : '' }}"
                                loading="lazy"
                            />
                        @else
                            <div
                                class="w-full h-full bg-gradient-to-br from-[var(--surface)] to-[var(--bg-soft)] flex flex-col items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                     stroke-linejoin="round" class="text-[var(--border)]">
                                    <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/>
                                    <circle cx="9" cy="9" r="2"/>
                                    <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                                </svg>
                                <span
                                    class="text-[9px] font-bold text-[var(--border)] uppercase tracking-widest">No Image</span>
                            </div>
                        @endif

                        {{-- Share Buttons: Di atas area foto (z-20) --}}
                        <div class="absolute top-2.5 left-2.5 flex flex-col gap-2 z-20">
                            <button
                                @click.prevent.stop="$store.utils.shareProduct(item)"
                                class="bg-[var(--surface)]/80 backdrop-blur-md p-1.5 rounded-full shadow-sm hover:bg-[var(--primary-color)] hover:shadow-md transition-all duration-300 hover:scale-110 active:scale-90"
                                aria-label="Bagikan link">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round" class="text-[var(--foreground)]">
                                    <circle cx="18" cy="5" r="3"/>
                                    <circle cx="6" cy="12" r="3"/>
                                    <circle cx="18" cy="19" r="3"/>
                                    <line x1="8.59" x2="15.42" y1="13.51" y2="17.49"/>
                                    <line x1="15.41" x2="8.59" y1="6.51" y2="10.49"/>
                                </svg>
                            </button>
                            <button
                                @click="window.open('{{ route('product.story', new Product($item)) }}', '_blank')"
                                class="bg-[var(--surface)]/80 backdrop-blur-md p-1.5 rounded-full shadow-sm hover:bg-[#25D366] hover:text-[var(--background)] hover:shadow-md transition-all duration-300 hover:scale-110 active:scale-90 group/story"
                                aria-label="Share ke Status WA">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                                     fill="currentColor"
                                     class="text-[var(--foreground)] group-hover/story:text-[var(--background)] transition-colors">
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.888-.788-1.489-1.761-1.663-2.06-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                </svg>
                            </button>
                        </div>

                        @if(! $item['is_active'])
                            <div
                                class="absolute inset-0 bg-black/10 flex items-center justify-center pointer-events-none">
                                <span
                                    class="bg-[var(--foreground)]/90 text-[var(--background)] text-[10px] font-bold px-2 py-1 rounded-lg backdrop-blur-sm shadow-sm">Habis</span>
                            </div>
                        @endif
                    </div>

                    <div
                        class="flex-1 flex flex-col justify-between min-w-0 min-h-0 p-3"
                        :class="viewMode === 'list' ? '!py-1 !px-0' : ''"
                    >
                        {{-- FIX HARGA: Digabung di bawah, baik Grid maupun List dapet posisi yang sama --}}
                        <div class="mb-2 pointer-events-none">
                            @if(!empty($item['active_discount_price']) && !empty($item['active_discount_name']))
                                <div class="mb-1">
                                    <span class="inline-block px-1.5 py-0.5 bg-red-50 text-red-500 border border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20 rounded text-[8px] font-black uppercase tracking-widest shadow-sm">% {{ $item['active_discount_name'] }}</span>
                                </div>
                            @endif
                            <h3 class="font-black text-sm text-[var(--foreground)] leading-snug mb-0.5 truncate">{{ $item['name'] }}</h3>
                            @if(!empty($item['active_discount_price']))
                                <div class="flex flex-col leading-tight mb-0.5">
                                    <span class="text-[10px] text-red-400 line-through font-bold">Rp {{ number_format($item['price'], 0, ',', '.') }}</span>
                                    <span class="text-sm font-extrabold text-[var(--primary-color)]">Rp {{ number_format($item['active_discount_price'], 0, ',', '.') }}</span>
                                </div>
                            @else
                                <p class="text-sm font-extrabold text-[var(--primary-color)] mb-0.5">{{ $item['formatted_price'] }}</p>
                            @endif
                            <p class="text-[10px] text-[var(--text-secondary)] line-clamp-2 leading-relaxed">{{ $item['description'] }}</p>
                        </div>

                        {{-- Cart Stepper / Add Button (z-20) --}}
                        <div class="mt-auto relative z-20">
                            <template x-if="showStepper">
                                <div class="flex items-center justify-between bg-[var(--surface)] rounded-xl p-1 shadow-sm border border-[var(--border)]">
                                    <button @click="updateQty(item.name, -1)"
                                            class="w-8 h-8 flex items-center justify-center text-[var(--foreground)] hover:bg-[var(--bg-soft)] rounded-lg transition-colors active:scale-90">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                             stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="5" x2="19" y1="12" y2="12"/>
                                        </svg>
                                    </button>
                                    <span class="font-black text-[var(--foreground)] text-sm tabular-nums" x-text="qtyInCart"></span>
                                    <button @click="updateQty(item.name, 1)"
                                            class="w-8 h-8 flex items-center justify-center text-black bg-[var(--primary-color)] rounded-lg transition-all active:scale-90 hover:brightness-110">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                             stroke-linecap="round" stroke-linejoin="round">
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
                                    class="w-full py-2.5 rounded-xl text-[10px] font-black uppercase tracking-wider transition-all duration-300 flex items-center justify-center gap-1.5 {{ $item['is_active'] ? 'bg-[var(--primary-color)]/10 text-[var(--primary-color)] border border-[var(--primary-color)]/30 hover:bg-[var(--primary-color)] hover:text-black hover:border-[var(--primary-color)] hover:shadow-md hover:shadow-[var(--primary-color)]/20 active:scale-95 cursor-pointer' : 'bg-[var(--bg-soft)] text-[var(--text-secondary)] border border-[var(--border)] cursor-not-allowed' }}"
                                >
                                    @if($item['is_active'])
                                        Tambah
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                             stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="12" x2="12" y1="5" y2="19"/>
                                            <line x1="5" x2="19" y1="12" y2="12"/>
                                        </svg>
                                    @else
                                        Habis
                                    @endif
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-2 text-center py-16 flex flex-col items-center gap-3">
                    <div class="w-14 h-14 rounded-2xl bg-[var(--bg-soft)] flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-[var(--border)]"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                    </div>
                    <p class="text-[var(--text-secondary)] text-sm font-bold">Belum ada menu</p>
                    <p class="text-xs text-[var(--text-secondary)] opacity-60">untuk kategori ini</p>
                </div>
            @endforelse
        </main>

        {{-- ===== INFINITE SCROLL SENTINEL & LOAD MORE SKELETON ===== --}}
        @if($this->hasMore)
            <div
                wire:key="scroll-sentinel-{{ $category }}-{{ $perPage }}"
                x-intersect.once="$wire.loadMore()"
                {{-- FIX: Tambahin pembatas lebar dan margin biar presisi sama list di atasnya --}}
                class="max-w-xl mx-auto px-5 pt-4 pb-8"
            >
                {{-- SKELETON KHUSUS LOAD MORE --}}
                <div wire:loading.class.remove="hidden" wire:target="loadMore" class="hidden">
                    <div :class="viewMode === 'grid' ? 'grid grid-cols-2 gap-3' : 'flex flex-col gap-3'">
                        @for($s = 0; $s < 2; $s++)
                            <div
                                class="bg-[var(--surface)] rounded-2xl border border-[var(--border)] shadow-sm flex overflow-hidden animate-slide-up"
                                :class="viewMode === 'grid' ? 'flex-col h-full' : 'flex-row items-center gap-4 p-3'"
                            >
                                <div
                                    class="bg-skeleton shrink-0"
                                    :class="viewMode === 'grid' ? 'w-full aspect-[4/3]' : 'w-20 h-20 rounded-xl'"
                                ></div>
                                <div
                                    class="flex-1 flex flex-col justify-between min-h-0"
                                    :class="viewMode === 'grid' ? 'p-3.5 pt-2.5' : ''"
                                >
                                    <div>
                                        <div class="h-4 bg-skeleton rounded-md w-3/4 mb-2"></div>
                                        <div x-show="viewMode === 'list'"
                                             class="h-3.5 bg-skeleton rounded-md w-1/3 mb-2"></div>
                                        <div class="h-2.5 bg-skeleton rounded-md w-full mb-1.5"></div>
                                        <div class="h-2.5 bg-skeleton rounded-md w-2/3"></div>
                                    </div>
                                    <div class="mt-3">
                                        <div class="w-full h-8 bg-skeleton rounded-xl"></div>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- ===== FLOATING CART BUTTON ===== --}}
    <template x-if="totalQty > 0">
        <div class="fixed bottom-6 left-0 right-0 px-5 z-50 animate-slide-up">
            <button
                @click="openCheckout()"
                class="max-w-xl mx-auto w-full bg-zinc-900 text-zinc-50 p-4 rounded-2xl shadow-2xl flex justify-between items-center border border-[var(--primary-color)]/30 ring-1 ring-[var(--primary-color)]/20 relative overflow-hidden group hover:border-[var(--primary-color)] transition-all duration-300 active:scale-[0.98]"
            >
                <div
                    class="absolute inset-0 bg-gradient-to-r from-[var(--primary-color)]/5 to-[var(--primary-color)]/10 group-hover:from-[var(--primary-color)]/10 group-hover:to-[var(--primary-color)]/20 transition-colors duration-500"></div>
                <div
                    class="absolute inset-0 animate-shimmer opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="relative flex items-center gap-3.5">
                    <div
                        class="bg-[var(--primary-color)] text-black w-11 h-11 rounded-xl flex items-center justify-center font-black text-sm shadow-md shadow-[var(--primary-color)]/30"
                        x-text="totalQty"></div>
                    <div class="text-left">
                        <span class="block text-[9px] font-bold uppercase tracking-widest text-zinc-400 mb-0.5">Total Estimasi</span>
                        <span class="font-bold text-lg text-white font-mono leading-none"
                              x-text="formatPrice(totalCart)"></span>
                    </div>
                </div>
                <div class="relative flex items-center gap-2 pr-1">
                    <span class="text-[10px] font-black uppercase tracking-widest text-[var(--primary-color)] group-hover:text-white transition-colors">Checkout</span>
                    <div
                        class="bg-[var(--primary-color)]/10 text-[var(--primary-color)] p-1.5 rounded-full group-hover:bg-[var(--primary-color)] transition-colors group-hover:text-black">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m9 18 6-6-6-6"/>
                        </svg>
                    </div>
                </div>
            </button>
        </div>
    </template>

    {{-- ===== FILTER & SORT DRAWER ===== --}}
    <div x-show="showFilter" class="relative z-[150]" style="display: none;">
        <div x-show="showFilter" x-transition.opacity.duration.300ms @click="showFilter = false" class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>
        <div x-show="showFilter" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full" class="fixed bottom-0 left-0 right-0 mx-auto w-full max-w-md z-[151] rounded-t-[2.5rem] shadow-2xl flex flex-col max-h-[85vh] bg-[var(--background)] border-t border-[var(--border)]">
            <div class="p-2 flex justify-center shrink-0" @click="showFilter = false"><div class="w-14 h-1.5 rounded-full cursor-pointer opacity-50 hover:opacity-100 transition-opacity bg-[var(--border)]"></div></div>
            <div class="px-6 pb-4 border-b border-[var(--border)] flex justify-between items-center rounded-t-[2.5rem] sticky top-0 z-10 bg-[var(--background)]">
                <h2 class="text-xl font-bold text-[var(--foreground)]">Filter &amp; Sort</h2>
                <button wire:click="resetFilters" @click="showFilter = false" class="text-xs font-semibold text-[var(--primary)] px-3 py-1.5 rounded-lg hover:bg-[var(--primary)]/10 transition-colors">Reset</button>
            </div>
            <div class="flex-1 overflow-y-auto p-6 scrollbar-hide space-y-8">
                <section>
                    <h3 class="text-sm font-bold text-[var(--foreground)] mb-3">Sort By</h3>
                    <div class="grid grid-cols-2 gap-3">
                        <button wire:click="setSort('popular')" class="px-4 py-3.5 rounded-2xl text-left font-bold border transition-all active:scale-[0.98] flex items-center gap-2.5 relative overflow-hidden shadow-sm {{ $sort === 'popular' ? 'bg-[var(--primary-color)] text-black border-[var(--primary-color)]' : 'bg-[var(--surface)] text-[var(--foreground)] border-[var(--border)] hover:bg-[var(--bg-soft)]' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/></svg>
                            <span class="text-sm">Popular</span>
                        </button>
                        <button wire:click="setSort('newest')" class="px-4 py-3.5 rounded-2xl text-left font-bold border transition-all active:scale-[0.98] flex items-center gap-2.5 relative overflow-hidden shadow-sm {{ $sort === 'newest' ? 'bg-[var(--primary-color)] text-black border-[var(--primary-color)]' : 'bg-[var(--surface)] text-[var(--foreground)] border-[var(--border)] hover:bg-[var(--bg-soft)]' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
                            <span class="text-sm">Newest</span>
                        </button>
                        <button wire:click="setSort('lowest_price')" class="px-4 py-3.5 rounded-2xl text-left font-bold border transition-all active:scale-[0.98] flex items-center gap-2.5 relative overflow-hidden shadow-sm {{ $sort === 'lowest_price' ? 'bg-[var(--primary-color)] text-black border-[var(--primary-color)]' : 'bg-[var(--surface)] text-[var(--foreground)] border-[var(--border)] hover:bg-[var(--bg-soft)]' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 17 13.5 8.5 8.5 13.5 2 7"/><polyline points="16 17 22 17 22 11"/></svg>
                            <span class="text-sm">Terendah</span>
                        </button>
                        <button wire:click="setSort('highest_price')" class="px-4 py-3.5 rounded-2xl text-left font-bold border transition-all active:scale-[0.98] flex items-center gap-2.5 relative overflow-hidden shadow-sm {{ $sort === 'highest_price' ? 'bg-[var(--primary-color)] text-black border-[var(--primary-color)]' : 'bg-[var(--surface)] text-[var(--foreground)] border-[var(--border)] hover:bg-[var(--bg-soft)]' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>
                            <span class="text-sm">Tertinggi</span>
                        </button>
                    </div>
                </section>
                <section>
                    <h3 class="text-sm font-bold text-[var(--foreground)] mb-3">Price Range</h3>
                    <div class="flex gap-4 items-center">
                        <div class="relative flex-1 group"><span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-[var(--text-secondary)]">Rp</span><input wire:model.defer="minPrice" inputmode="numeric" placeholder="Min" class="w-full pl-9 pr-3 py-3 rounded-2xl border border-[var(--border)] bg-[var(--surface)] text-[var(--foreground)] text-sm focus:outline-none transition-all" type="number"></div>
                        <span class="text-[var(--text-secondary)] font-bold">-</span>
                        <div class="relative flex-1 group"><span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-[var(--text-secondary)]">Rp</span><input wire:model.defer="maxPrice" inputmode="numeric" placeholder="Max" class="w-full pl-9 pr-3 py-3 rounded-2xl border border-[var(--border)] bg-[var(--surface)] text-[var(--foreground)] text-sm focus:outline-none transition-all" type="number"></div>
                    </div>
                </section>
            </div>
            <div class="p-4 border-t border-[var(--border)] bg-[var(--background)] rounded-t-[2rem] shadow-[0_-10px_40px_rgba(0,0,0,0.1)]"><button wire:click="applyFilters" @click="showFilter = false" class="w-full py-4 rounded-2xl font-bold text-lg shadow-xl active:scale-[0.98] transition-all bg-[var(--primary)] text-[var(--primary-foreground)]">Apply Filters</button></div>
        </div>
    </div>

</div>
