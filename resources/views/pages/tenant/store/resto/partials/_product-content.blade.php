{{-- Scrollable content card: nama, harga, deskripsi, varian, add-on --}}
<main class="relative z-10 max-w-xl mx-auto bg-[var(--surface)] min-h-[60vh] rounded-t-[2rem] -mt-8 pb-44 shadow-[0_-10px_40px_rgba(0,0,0,0.1)]">
    <div class="w-full flex justify-center pt-3 pb-5">
        <div class="w-12 h-1.5 bg-zinc-200 rounded-full"></div>
    </div>

    <div class="px-6">
        {{-- ===== PRODUCT NAME + PRICE ===== --}}
        <div class="flex justify-between items-start gap-4">
            <div class="flex-1">
                @if($product->category)
                    <span class="inline-block px-3 py-1.5 rounded-xl bg-[var(--bg-soft)] text-[var(--foreground)] text-[10px] font-black uppercase tracking-widest mb-3 border border-[var(--border)]">{{ $product->category->name }}</span>
                @endif
                <template x-if="product.active_discount_price && product.active_discount_name">
                    <span
                        class="inline-block px-2 py-1 bg-red-50 text-red-500 border border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20 rounded-lg text-[9px] font-black uppercase tracking-widest shadow-sm mb-3 ml-1"
                        x-text="'% ' + product.active_discount_name"></span>
                </template>
                <h1 class="text-[1.75rem] font-black text-[var(--foreground)] leading-tight tracking-tight">{{ $product->name }}</h1>
            </div>
            <div class="text-right pt-1 shrink-0 min-w-0">
                <template x-if="product.active_discount_price">
                    <div class="flex flex-col items-end">
                        <span class="text-[10px] text-red-400 line-through font-bold" x-text="formatPrice(product.price)"></span>
                        <div class="text-lg md:text-2xl font-black text-[var(--primary-color)] font-mono tracking-tighter whitespace-nowrap"
                             x-text="formatPrice(product.active_discount_price)"></div>
                    </div>
                </template>
                <template x-if="!product.active_discount_price">
                    <div class="text-lg md:text-2xl font-black text-[var(--primary-color)] font-mono tracking-tighter whitespace-nowrap"
                         x-text="product.formatted_price"></div>
                </template>
            </div>
        </div>

        <div class="h-px bg-[var(--bg-soft)] my-6"></div>

        {{-- ===== DESCRIPTION ===== --}}
        <div class="space-y-3">
            <h3 class="text-xs font-black uppercase tracking-widest text-[var(--foreground)] flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     class="text-[var(--text-secondary)]">
                    <line x1="3" x2="21" y1="6" y2="6"/>
                    <line x1="3" x2="21" y1="12" y2="12"/>
                    <line x1="3" x2="21" y1="18" y2="18"/>
                </svg>
                Deskripsi Menu
            </h3>
            <p class="text-sm text-[var(--text-secondary)] leading-relaxed">{{ $product->description ?: 'Tidak ada deskripsi untuk menu ini.' }}</p>
        </div>

        {{-- ===== VARIANTS ===== --}}
        @if($product->has_variants && $product->variants->count() > 0)
            <div class="mt-8 space-y-4">
                <h3 class="text-xs font-black uppercase tracking-widest text-[var(--foreground)] flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round" class="text-[var(--text-secondary)]">
                        <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                        <line x1="3" x2="21" y1="6" y2="6"/>
                        <path d="M16 10a4 4 0 0 1-8 0"/>
                    </svg>
                    Pilihan Varian
                </h3>
                <div class="flex flex-wrap gap-2.5">
                    @foreach($product->variants as $variant)
                        <div class="px-4 py-2.5 bg-[var(--surface)] border border-[var(--border)] shadow-sm rounded-2xl text-xs font-bold text-[var(--foreground)] flex items-center gap-2 relative overflow-hidden group">
                            <span class="relative z-10">{{ $variant->name }}</span>
                            @if($product->selection_type !== 'multiple')
                                <span class="w-1 h-1 rounded-full bg-[var(--border)] relative z-10"></span>
                                @if(!empty($variant->active_discount_price))
                                    <span class="text-[10px] text-red-400 line-through relative z-10 font-bold">Rp {{ number_format($variant->price, 0, ',', '.') }}</span>
                                    <span class="text-[var(--primary-color)] relative z-10 font-mono tracking-tight">Rp {{ number_format($variant->active_discount_price, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-[var(--primary-color)] relative z-10 font-mono tracking-tight">Rp {{ number_format($variant->price, 0, ',', '.') }}</span>
                                @endif
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ===== EXTRAS / ADD-ONS ===== --}}
        @php $activeExtras = $product->extras->where('is_active', true); @endphp
        @if($activeExtras->count() > 0)
            <div class="mt-8 space-y-4">
                <h3 class="text-xs font-black uppercase tracking-widest text-[var(--foreground)] flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round" class="text-[var(--text-secondary)]">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" x2="12" y1="8" y2="16"/>
                        <line x1="8" x2="16" y1="12" y2="12"/>
                    </svg>
                    Pilihan Tambahan (Add-On)
                </h3>
                <div class="flex flex-wrap gap-2.5">
                    @foreach($activeExtras as $extra)
                        <div class="px-4 py-2.5 bg-[var(--surface)] border border-[var(--border)] shadow-sm rounded-2xl text-xs font-bold text-[var(--foreground)] flex items-center gap-2 relative overflow-hidden">
                            <span class="relative z-10">{{ $extra->name }}</span>
                            <span class="w-1 h-1 rounded-full bg-[var(--border)] relative z-10"></span>
                            <span class="text-[var(--primary-color)] relative z-10 font-mono tracking-tight">+Rp {{ number_format($extra->price, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</main>
