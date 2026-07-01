@php use App\Tenant\Models\Core\Product; @endphp
<div
    x-data="{ viewMode: 'grid' }"
    @view-mode-changed.window="viewMode = $event.detail"
    @refresh-menu-data.window="$wire.$refresh().then(() => { isRefreshing = false; pullY = 0; })"
>

    {{-- Loading Skeleton Initial (Placeholder) --}}
    @placeholder
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
    @endplaceholder

    {{-- Loading Skeleton Partial (saat ganti filter) --}}
    <div wire:loading.delay.class.remove="hidden" wire:target.except="loadMore" class="hidden">
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
    <div wire:loading.delay.class="hidden" wire:target.except="loadMore">
        <main
            wire:loading.class="opacity-50 pointer-events-none"
            wire:target.except="loadMore"
            class="max-w-xl mx-auto px-5 mt-4 transition-all duration-300"
            :class="viewMode === 'grid' ? 'grid grid-cols-2 gap-3' : 'flex flex-col gap-3'"
        >
            @island(name: 'products', skip: false)
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
                        <a href="{{ route('product.show', new \App\Tenant\Models\Core\Product($item)) }}"
                           wire:navigate.hover
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
                                @click="window.open('{{ route('product.story', new \App\Tenant\Models\Core\Product($item)) }}', '_blank')"
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
                                    <span
                                        class="inline-block px-1.5 py-0.5 bg-red-50 text-red-500 border border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20 rounded text-[8px] font-black uppercase tracking-widest shadow-sm">% {{ $item['active_discount_name'] }}</span>
                                </div>
                            @endif
                            <h3 class="font-black text-sm text-[var(--foreground)] leading-snug mb-0.5 truncate">{{ $item['name'] }}</h3>
                            @if(!empty($item['active_discount_price']))
                                <div class="flex flex-col leading-tight mb-0.5">
                                    <span
                                        class="text-[10px] text-red-400 line-through font-bold">Rp {{ number_format($item['price'], 0, ',', '.') }}</span>
                                    <span
                                        class="text-sm font-extrabold text-[var(--primary-color)]">Rp {{ number_format($item['active_discount_price'], 0, ',', '.') }}</span>
                                </div>
                            @else
                                <p class="text-sm font-extrabold text-[var(--primary-color)] mb-0.5">{{ $item['formatted_price'] }}</p>
                            @endif
                            <p class="text-[10px] text-[var(--text-secondary)] line-clamp-2 leading-relaxed">{{ $item['description'] }}</p>
                        </div>

                        {{-- Cart Stepper / Add Button (z-20) --}}
                        <div class="mt-auto relative z-20">
                            <template x-if="showStepper">
                                <div
                                    class="flex items-center justify-between bg-[var(--surface)] rounded-xl p-1 shadow-sm border border-[var(--border)]">
                                    <button @click="updateQty(item.name, -1)"
                                            class="w-8 h-8 flex items-center justify-center text-[var(--foreground)] hover:bg-[var(--bg-soft)] rounded-lg transition-colors active:scale-90">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                             stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="5" x2="19" y1="12" y2="12"/>
                                        </svg>
                                    </button>
                                    <span class="font-black text-[var(--foreground)] text-sm tabular-nums"
                                          x-text="qtyInCart"></span>
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
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                             class="text-[var(--border)]">
                            <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/>
                            <path d="M3 6h18"/>
                            <path d="M16 10a4 4 0 0 1-8 0"/>
                        </svg>
                    </div>
                    <p class="text-[var(--text-secondary)] text-sm font-bold">Belum ada menu</p>
                    <p class="text-xs text-[var(--text-secondary)] opacity-60">untuk kategori ini</p>
                </div>
            @endforelse
            @endisland
        </main>

        {{-- ===== INFINITE SCROLL SENTINEL & LOAD MORE SKELETON ===== --}}
        @if($this->hasMore)
            <div
                wire:key="scroll-sentinel-{{ $category }}"
                x-data="{ loading: false, noMore: false }"
                @no-more-products.window="noMore = true"
                x-show="!noMore"
                x-intersect="if(!loading) { loading = true; $wire.$island('products', { mode: 'append' }).loadMore().then(() => loading = false) }"
                class="max-w-xl mx-auto px-5 pt-4 pb-8"
            >
                {{-- SKELETON KHUSUS LOAD MORE --}}
                <div x-show="loading" class="hidden" :class="{ 'hidden': !loading, 'block': loading }">
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
                    <span
                        class="text-[10px] font-black uppercase tracking-widest text-[var(--primary-color)] group-hover:text-white transition-colors">Checkout</span>
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



</div>
