@props(['categories', 'hasPromoItems'])


    {{-- ===== STICKY SEARCH & CATEGORY BAR ===== --}}
    <div id="menu-start" class="scroll-mt-0 sticky top-0 z-40 bg-[var(--surface)]/90 backdrop-blur-xl border-b border-[var(--border)] shadow-sm shadow-[var(--border)]">
        <div class="max-w-xl mx-auto px-4 py-3 flex gap-2">
            <div class="relative flex-1">
                <input placeholder="Cari apa hari ini?" 
                       :value="search" 
                       @input.debounce.500ms="search = $event.target.value; apply()"
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl text-sm focus:outline-none shadow-sm transition-all border border-[var(--border)] bg-[var(--surface)] text-[var(--foreground)]"
                       type="text">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     class="w-4 h-4 absolute left-3.5 top-1/2 transform -translate-y-1/2 text-[var(--text-secondary)]"
                     aria-hidden="true">
                    <path d="m21 21-4.34-4.34"></path>
                    <circle cx="11" cy="11" r="8"></circle>
                </svg>
            </div>
            <button @click="showFilter = true"
                    class="p-2.5 rounded-xl border border-[var(--border)] bg-[var(--surface)] text-[var(--foreground)] shadow-sm active:scale-95 transition-all relative">
                <template x-if="sort !== 'popular' || minPrice || maxPrice">
                    <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-[var(--surface)]"></span>
                </template>
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                </svg>
            </button>
        </div>
        <div class="max-w-xl mx-auto px-5 pb-3 flex items-center justify-between gap-3">
            <div class="flex gap-2 overflow-x-auto no-scrollbar flex-1 py-1">
                <button @click="category = 'all'; apply()"
                        class="px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-wider whitespace-nowrap transition-all duration-300 active:scale-95 border cursor-pointer"
                        :class="category === 'all' ? 'bg-[var(--primary-color)] text-black border-[var(--primary-color)] shadow-lg shadow-[var(--primary-color)]/20' : 'bg-[var(--surface)] text-[var(--text-secondary)] border-[var(--border)] hover:bg-[var(--bg-soft)]'">
                    Semua
                </button>
                @if($hasPromoItems)
                    <button @click="category = 'promo'; apply()"
                            class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-wider whitespace-nowrap transition-all duration-300 active:scale-95 border cursor-pointer"
                            :class="category === 'promo' ? 'bg-red-500 text-white shadow-lg border-red-500' : 'bg-red-50 text-red-600 border-red-200 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:border-red-900/50 dark:hover:bg-red-900/40'">
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24"
                             fill="currentColor">
                            <path
                                d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/>
                        </svg>
                        Promo
                    </button>
                @endif
                @foreach($categories as $cat)
                    <button @click="category = '{{ $cat }}'; apply()"
                            class="px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-wider whitespace-nowrap transition-all duration-300 active:scale-95 border cursor-pointer"
                            :class="category === '{{ $cat }}' ? 'bg-[var(--primary-color)] text-black border-[var(--primary-color)] shadow-lg shadow-[var(--primary-color)]/20' : 'bg-[var(--surface)] text-[var(--text-secondary)] border-[var(--border)] hover:bg-[var(--bg-soft)]'">{{ $cat }}</button>
                @endforeach
            </div>
            <div class="flex bg-[var(--surface)] p-1 rounded-xl border border-[var(--border)] shadow-sm shrink-0">
                <button @click="viewMode = 'list'"
                        :class="viewMode === 'list' ? 'bg-[var(--foreground)] text-[var(--background)] shadow-sm' : 'text-[var(--text-secondary)] hover:text-[var(--foreground)]'"
                        class="p-2 rounded-lg transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" x2="21" y1="6" y2="6"/>
                        <line x1="3" x2="21" y1="12" y2="12"/>
                        <line x1="3" x2="21" y1="18" y2="18"/>
                    </svg>
                </button>
                <button @click="viewMode = 'grid'"
                        :class="viewMode === 'grid' ? 'bg-[var(--foreground)] text-[var(--background)] shadow-sm' : 'text-[var(--text-secondary)] hover:text-[var(--foreground)]'"
                        class="p-2 rounded-lg transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="7" height="7" x="3" y="3" rx="1"/>
                        <rect width="7" height="7" x="14" y="3" rx="1"/>
                        <rect width="7" height="7" x="14" y="14" rx="1"/>
                        <rect width="7" height="7" x="3" y="14" rx="1"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- ===== FILTER & SORT DRAWER ===== --}}
    <div x-show="showFilter" class="relative z-[150]" style="display: none;">
        <div x-show="showFilter" x-transition.opacity.duration.300ms @click="showFilter = false"
             class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>
        <div x-show="showFilter" x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-y-0"
             x-transition:leave-end="translate-y-full"
             class="fixed bottom-0 left-0 right-0 mx-auto w-full max-w-md z-[151] rounded-t-[2.5rem] shadow-2xl flex flex-col max-h-[85vh] bg-[var(--background)] border-t border-[var(--border)]">
            <div class="p-2 flex justify-center shrink-0" @click="showFilter = false">
                <div class="w-14 h-1.5 rounded-full cursor-pointer opacity-50 hover:opacity-100 transition-opacity bg-[var(--border)]"></div>
            </div>
            <div class="px-6 pb-4 border-b border-[var(--border)] flex justify-between items-center rounded-t-[2.5rem] sticky top-0 z-10 bg-[var(--background)]">
                <h2 class="text-xl font-bold text-[var(--foreground)]">Filter &amp; Sort</h2>
                <button @click="reset()" class="text-xs font-semibold text-[var(--primary)] px-3 py-1.5 rounded-lg hover:bg-[var(--primary)]/10 transition-colors">
                    Reset
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-6 scrollbar-hide space-y-8">
                <section>
                    <h3 class="text-sm font-bold text-[var(--foreground)] mb-3">Sort By</h3>
                    <div class="grid grid-cols-2 gap-3">
                        <button @click="sort = 'popular'"
                                class="px-4 py-3.5 rounded-2xl text-left font-bold border transition-all active:scale-[0.98] flex items-center gap-2.5 relative overflow-hidden shadow-sm"
                                :class="sort === 'popular' ? 'bg-[var(--primary-color)] text-black border-[var(--primary-color)]' : 'bg-[var(--surface)] text-[var(--foreground)] border-[var(--border)] hover:bg-[var(--bg-soft)]'">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                 stroke-linejoin="round">
                                <path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/>
                            </svg>
                            <span class="text-sm">Popular</span>
                        </button>
                        <button @click="sort = 'newest'"
                                class="px-4 py-3.5 rounded-2xl text-left font-bold border transition-all active:scale-[0.98] flex items-center gap-2.5 relative overflow-hidden shadow-sm"
                                :class="sort === 'newest' ? 'bg-[var(--primary-color)] text-black border-[var(--primary-color)]' : 'bg-[var(--surface)] text-[var(--foreground)] border-[var(--border)] hover:bg-[var(--bg-soft)]'">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                 stroke-linejoin="round">
                                <path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/>
                            </svg>
                            <span class="text-sm">Newest</span>
                        </button>
                        <button @click="sort = 'lowest_price'"
                                class="px-4 py-3.5 rounded-2xl text-left font-bold border transition-all active:scale-[0.98] flex items-center gap-2.5 relative overflow-hidden shadow-sm"
                                :class="sort === 'lowest_price' ? 'bg-[var(--primary-color)] text-black border-[var(--primary-color)]' : 'bg-[var(--surface)] text-[var(--foreground)] border-[var(--border)] hover:bg-[var(--bg-soft)]'">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                 stroke-linejoin="round">
                                <polyline points="22 17 13.5 8.5 8.5 13.5 2 7"/>
                                <polyline points="16 17 22 17 22 11"/>
                            </svg>
                            <span class="text-sm">Terendah</span>
                        </button>
                        <button @click="sort = 'highest_price'"
                                class="px-4 py-3.5 rounded-2xl text-left font-bold border transition-all active:scale-[0.98] flex items-center gap-2.5 relative overflow-hidden shadow-sm"
                                :class="sort === 'highest_price' ? 'bg-[var(--primary-color)] text-black border-[var(--primary-color)]' : 'bg-[var(--surface)] text-[var(--foreground)] border-[var(--border)] hover:bg-[var(--bg-soft)]'">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                 stroke-linejoin="round">
                                <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/>
                                <polyline points="16 7 22 7 22 13"/>
                            </svg>
                            <span class="text-sm">Tertinggi</span>
                        </button>
                    </div>
                </section>
                <section>
                    <h3 class="text-sm font-bold text-[var(--foreground)] mb-3">Price Range</h3>
                    <div class="flex gap-4 items-center">
                        <div class="relative flex-1 group">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-[var(--text-secondary)]">Rp</span>
                            <input x-model="minPrice" inputmode="numeric" placeholder="Min"
                                class="w-full pl-9 pr-3 py-3 rounded-2xl border border-[var(--border)] bg-[var(--surface)] text-[var(--foreground)] text-sm focus:outline-none transition-all"
                                type="number">
                        </div>
                        <span class="text-[var(--text-secondary)] font-bold">-</span>
                        <div class="relative flex-1 group">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-[var(--text-secondary)]">Rp</span>
                            <input x-model="maxPrice" inputmode="numeric" placeholder="Max"
                                class="w-full pl-9 pr-3 py-3 rounded-2xl border border-[var(--border)] bg-[var(--surface)] text-[var(--foreground)] text-sm focus:outline-none transition-all"
                                type="number">
                        </div>
                    </div>
                </section>
            </div>
            <div class="p-4 border-t border-[var(--border)] bg-[var(--background)] rounded-t-[2rem] shadow-[0_-10px_40px_rgba(0,0,0,0.1)]">
                <button @click="apply(); showFilter = false"
                        class="w-full py-4 rounded-2xl font-bold text-lg shadow-xl active:scale-[0.98] transition-all bg-[var(--primary)] text-[var(--primary-foreground)]">
                    Apply Filters
                </button>
            </div>
        </div>
    </div>
