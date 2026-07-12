<div class="w-full pb-10" x-data="{
    activeFilter: $wire.entangle('statusFilter').live,
    showGuideModalState: false,
    showGuideModal() {
        localStorage.setItem('pakaiapp_order_guide_dismissed', 'true');
        this.showGuideModalState = true;
    },
    showSplitModalState: false,
    splittingOrder: null,
    splitItems: [],
    get splitTotalItems() {
        return this.splitItems.reduce((acc, curr) => acc + curr.qtyToSplit, 0);
    },
    openSplitModal(order) {
        this.splittingOrder = order;
        this.splitItems = order.items.map(i => ({
            id: i.id,
            name: i.product_name,
            variant_name: i.variant_name,
            price: parseFloat(i.price),
            maxQty: parseInt(i.quantity),
            qtyToSplit: 0
        }));

        this.showSplitModalState = true;
    },
    submitSplitOrder() {
        if (this.splitTotalItems === 0) {
            window.dispatchEvent(new CustomEvent('notify', { detail: { type: 'warning', message: 'Pilih minimal 1 item untuk dipisah.' } }));
            return;
        }
        const totalOriginalItems = this.splitItems.reduce((acc, curr) => acc + curr.maxQty, 0);
        if (this.splitTotalItems === totalOriginalItems) {
            window.dispatchEvent(new CustomEvent('notify', { detail: { type: 'warning', message: 'Anda memilih semua item. Gunakan Bayar biasa saja.' } }));
            return;
        }

        const dataToSend = this.splitItems.filter(i => i.qtyToSplit > 0).map(i => ({
            id: i.id,
            qty: i.qtyToSplit
        }));

        @this.splitOrder(this.splittingOrder.id, dataToSend);
    }
}" @if($storeType !== 'resto') wire:poll.15s @endif>

    {{-- Header Section (Clean & Minimalist like Product List) --}}
    <div class="flex flex-col md:flex-row justify-between md:items-end mb-6 gap-4 pt-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight mb-2 text-slate-800 dark:text-slate-200">
                {{ $storeType === 'resto' ? 'Riwayat Transaksi' : 'Dashboard Pesanan' }}
            </h2>
            <div class="flex items-center gap-2 flex-wrap">
                <span class="bg-emerald-500/10 text-emerald-600 rounded-full px-3 py-1.5 font-bold border border-emerald-500/20 flex items-center gap-2 text-xs">
                    <span class="active-glow-dot w-1.5 h-1.5"></span> Live Update Aktif
                </span>
                <p class="text-slate-500 dark:text-slate-400 text-sm mb-0 font-medium">Pantau dan kelola semua transaksi masuk secara instan.</p>
            </div>
        </div>
        <div>
            <button type="button" @click="showGuideModal()" class="bg-white dark:bg-slate-800 border border-border hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 font-bold rounded-full px-5 py-2.5 flex items-center gap-2 shadow-sm transition-all text-sm">
                <i class="ph-bold ph-question"></i> Panduan Alur
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1">
        <div class="min-w-0">
            <div class="bg-white dark:bg-slate-900 border border-border rounded-2xl shadow-sm h-full flex flex-col">

                {{-- Controls: Filters & Search --}}
                <div class="p-4 md:p-6 pb-2 md:pb-4 flex flex-col lg:flex-row justify-between gap-4 border-b border-border">
                    @php
                        $filters = [
                            ['id' => 'all', 'label' => 'Semua', 'count' => $allCount],
                            ['id' => 'pending', 'label' => 'Menunggu', 'count' => $pendingCount],
                            ['id' => 'paid', 'label' => 'Baru Masuk', 'count' => $paidCount],
                            ['id' => 'progress', 'label' => 'Diproses', 'count' => $progressCount],
                            ['id' => 'completed', 'label' => 'Selesai', 'count' => $completedCount],
                            ['id' => 'cancelled', 'label' => 'Batal', 'count' => $cancelledCount]
                        ];
                    @endphp

                    {{-- Mobile Filter Dropdown --}}
                    <div class="relative w-full lg:hidden" x-data="{ open: false }">
                        <button @click="open = !open" @click.outside="open = false" class="w-full flex justify-between items-center bg-white dark:bg-slate-800 border border-border rounded-full px-5 py-3 font-bold text-slate-700 dark:text-slate-200 shadow-sm transition-all">
                            <div class="flex items-center gap-3">
                                <i class="ph-bold ph-funnel text-xl text-slate-500 dark:text-slate-400"></i>
                                <span x-text="
                                    activeFilter === 'all' ? 'Filter: Semua Pesanan' :
                                    (activeFilter === 'pending' ? 'Filter: Menunggu' :
                                    (activeFilter === 'paid' ? 'Filter: Baru Masuk' :
                                    (activeFilter === 'progress' ? 'Filter: Diproses' :
                                    (activeFilter === 'completed' ? 'Filter: Selesai' : 'Filter: Batal'))))
                                ">Filter</span>
                            </div>
                            <i class="ph-bold ph-caret-down text-slate-400"></i>
                        </button>
                        
                        <div x-show="open" x-transition class="absolute top-full mt-2 w-full bg-white dark:bg-slate-800 border border-border shadow-lg rounded-2xl p-2 z-20" style="display: none;">
                            @foreach($filters as $filter)
                                <button type="button"
                                        class="w-full text-left rounded-xl py-3 px-4 font-medium flex justify-between items-center mb-1 transition-all"
                                        @click="activeFilter = '{{ $filter['id'] }}'; open = false"
                                        :class="activeFilter === '{{ $filter['id'] }}' ? 'bg-brand-accent/10 text-brand-accent font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50'">
                                    <div class="flex items-center gap-2">
                                        <i class="ph-bold ph-check text-xl text-brand-accent" x-show="activeFilter === '{{ $filter['id'] }}'" x-cloak></i>
                                        <div class="w-5 h-5" x-show="activeFilter !== '{{ $filter['id'] }}'" x-cloak></div>
                                        <span>{{ $filter['label'] }}</span>
                                    </div>
                                    <span class="rounded-full px-2 py-0.5 text-[10px] font-bold"
                                          :class="activeFilter === '{{ $filter['id'] }}' ? 'bg-brand-accent text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400'">
                                        {{ $filter['count'] }}
                                    </span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Desktop Nav Pills Filter --}}
                    <div class="hidden lg:flex flex-1 overflow-x-auto overflow-y-hidden scrollbar-hide pb-2">
                        <div class="flex gap-2 whitespace-nowrap min-w-max">
                            @foreach($filters as $filter)
                                <button type="button"
                                        @click="activeFilter = '{{ $filter['id'] }}'"
                                        class="inline-flex items-center gap-2 rounded-full px-5 py-2.5 font-bold transition-all border shrink-0 text-sm"
                                        :class="activeFilter === '{{ $filter['id'] }}' ? 'bg-brand-accent border-brand-accent text-white shadow-sm' : 'bg-slate-50 dark:bg-slate-800/50 border-border text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'">
                                    {{ $filter['label'] }}
                                    <span class="rounded-full px-2 py-0.5 text-[10px]"
                                          :class="activeFilter === '{{ $filter['id'] }}' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-400'">
                                        {{ $filter['count'] }}
                                    </span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Search Bar --}}
                    <div class="relative shrink-0 lg:w-[300px]">
                        <i class="ph-bold ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-lg"></i>
                        <input type="text"
                               class="w-full bg-slate-50 dark:bg-slate-800/50 border border-border text-slate-800 dark:text-slate-200 rounded-full pl-11 pr-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-brand-accent/50 focus:border-brand-accent transition-all text-sm"
                               wire:model.live.debounce.300ms="search"
                               placeholder="Cari pesanan...">
                    </div>
                </div>

                <div class="p-4 md:p-6 pt-0 bg-transparent border-0 flex-1">
                    <div class="relative mt-2">

                        {{-- Loading Overlay --}}
                        <div wire:loading wire:target="statusFilter, search" class="absolute inset-0 z-10 bg-white/70 dark:bg-slate-900/70 backdrop-blur-sm rounded-xl">
                            <div class="flex justify-center pt-20">
                                <i class="ph-bold ph-spinner animate-spin text-brand-accent text-4xl"></i>
                            </div>
                        </div>

                        @if($orders->isEmpty())
                            <div class="text-center py-16">
                                <div class="w-20 h-20 mx-auto rounded-full flex items-center justify-center mb-4 text-slate-400 bg-slate-50 dark:bg-slate-800/50 border border-border">
                                    <i class="ph-fill ph-receipt text-4xl"></i>
                                </div>
                                <h6 class="font-bold mb-1 text-slate-800 dark:text-slate-200">Data Tidak Ditemukan</h6>
                                <p class="text-slate-500 dark:text-slate-400 text-sm mb-0">Belum ada transaksi masuk di filter ini.</p>
                            </div>
                        @else
                            @foreach($orders as $order)
                                @php
                                    $typeBadges = [
                                        'dinein' => ['label' => 'Dine In', 'icon' => 'ph-coffee', 'class' => 'bg-brand-accent/10 text-brand-accent border-brand-accent/20'],
                                        'takeaway' => ['label' => 'Takeaway', 'icon' => 'ph-package', 'class' => 'bg-orange-500/10 text-orange-600 border-orange-500/20'],
                                        'delivery' => ['label' => 'Delivery', 'icon' => 'ph-moped', 'class' => 'bg-blue-500/10 text-blue-600 border-blue-500/20']
                                    ];
                                    $typeInfo = $typeBadges[$order->order_type] ?? ['label' => $order->order_type, 'icon' => 'ph-receipt', 'class' => 'bg-slate-500/10 text-slate-600 border-slate-500/20'];
                                @endphp

                                <div class="p-4 md:p-6 mb-4 rounded-2xl border border-border bg-white dark:bg-slate-800/50 hover:border-brand-accent/30 transition-colors shadow-sm">
                                    <div class="flex flex-col md:flex-row md:items-center gap-4">
                                        {{-- Left Side: Avatar & Info --}}
                                        <div class="flex-1 flex items-start gap-4 min-w-0">
                                            <div class="w-12 h-12 rounded-full text-white flex items-center justify-center font-bold shrink-0 mt-1"
                                                 style="background: linear-gradient(135deg, #10B981, #059669); font-size: 1.25rem;">
                                                {{ strtoupper(substr($order->customer_name, 0, 1)) }}
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center flex-wrap gap-2 mb-1.5">
                                                    <h6 class="font-bold mb-0 text-slate-800 dark:text-slate-200 truncate max-w-[200px]">{{ $order->customer_name }}</h6>
                                                    @if($order->table_number)
                                                        <span class="border border-border bg-slate-50 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-full px-2.5 py-0.5 font-bold text-xs">
                                                            Meja {{ $order->table_number }}
                                                        </span>
                                                    @endif
                                                    @if($order->notes)
                                                        <span class="bg-orange-500/10 text-orange-600 border border-orange-500/20 rounded-full px-2.5 py-0.5 font-bold truncate text-xs max-w-[150px] flex items-center"
                                                              title="Catatan: {{ $order->notes }}">
                                                            <i class="ph-bold ph-note text-sm mr-1"></i>{{ $order->notes }}
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="text-slate-500 dark:text-slate-400 text-sm font-medium flex items-center flex-wrap gap-2 mb-2.5">
                                                    <span class="font-bold text-brand-accent px-2 py-1 rounded-lg bg-brand-accent/10 text-xs tracking-wide">#{{ $order->invoice_code }}</span>
                                                    <span class="hidden sm:inline">&bull;</span>

                                                    <span class="border {{ $typeInfo['class'] }} rounded-full px-2.5 py-0.5 text-xs flex items-center font-bold">
                                                        <i class="ph-bold {{ $typeInfo['icon'] }} mr-1 text-sm"></i>{{ $typeInfo['label'] }}
                                                    </span>
                                                    <span class="hidden sm:inline">&bull;</span>

                                                    @if($order->is_online)
                                                        <span class="text-emerald-500 font-bold flex items-center text-xs"><i class="ph-bold ph-globe mr-1 text-sm"></i>Online</span>
                                                    @else
                                                        <span class="flex items-center text-xs"><i class="ph-bold ph-desktop mr-1 text-sm"></i>POS Kasir</span>
                                                    @endif
                                                    <span class="hidden sm:inline">&bull;</span>

                                                    @if($storeType === 'resto')
                                                        @if($order->kitchen_status === 'waiting')
                                                            <span class="border bg-orange-500/10 text-orange-600 border-orange-500/20 rounded-full px-2.5 py-0.5 text-xs font-bold flex items-center"><i class="ph-bold ph-hourglass-high mr-1"></i>Dapur: Nunggu</span>
                                                        @elseif($order->kitchen_status === 'processing')
                                                            <span class="border bg-brand-accent/10 text-brand-accent border-brand-accent/20 rounded-full px-2.5 py-0.5 text-xs font-bold flex items-center"><i class="ph-bold ph-fire mr-1"></i>Dapur: Dimasak</span>
                                                        @elseif($order->kitchen_status === 'ready')
                                                            <span class="border bg-emerald-500/10 text-emerald-600 border-emerald-500/20 rounded-full px-2.5 py-0.5 text-xs font-bold flex items-center"><i class="ph-bold ph-check-circle mr-1"></i>Dapur: Siap</span>
                                                        @elseif($order->kitchen_status === 'completed')
                                                            <span class="border bg-slate-500/10 text-slate-600 border-slate-500/20 rounded-full px-2.5 py-0.5 text-xs font-bold flex items-center"><i class="ph-bold ph-checks mr-1"></i>Dapur: Selesai</span>
                                                        @endif
                                                        <span class="hidden sm:inline">&bull;</span>
                                                    @endif

                                                    <span class="text-xs">{{ $order->created_at->format('d M, H:i') }}</span>
                                                </div>

                                                @if($order->items->isNotEmpty())
                                                    @php
                                                        $summary = $order->items->map(function($item) {
                                                            return $item->product_name . ($item->variant_name ? ' (' . $item->variant_name . ')' : '') . ($item->quantity > 1 ? ' (x' . $item->quantity . ')' : '');
                                                        })->join(', ');
                                                    @endphp
                                                    <div class="bg-slate-50 dark:bg-slate-900 rounded-lg p-2.5 text-sm text-slate-600 dark:text-slate-400 border border-border truncate max-w-full">
                                                        <span class="font-bold text-slate-800 dark:text-slate-200 mr-1">{{ $order->items->sum('quantity') }} Item:</span>
                                                        {{ $summary }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Right Side: Price & Actions --}}
                                        <div class="w-full md:w-auto pt-4 md:pt-0 border-t md:border-t-0 border-border md:pl-4">
                                            <div class="flex flex-row md:flex-col items-center md:items-end justify-between h-full gap-4">
                                                <div class="text-left md:text-right">
                                                    <div class="font-bold text-slate-800 dark:text-slate-200 text-xl tracking-tight">
                                                        Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                                    </div>
                                                    <div class="mt-1.5">
                                                        @if($order->status == 'pending')
                                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-orange-500/10 text-orange-600 border border-orange-500/20">
                                                                <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span> Menunggu
                                                            </span>
                                                        @elseif($order->status == 'paid')
                                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-sky-500/10 text-sky-500 border border-sky-500/20 shadow-[0_0_10px_rgba(14,165,233,0.2)]">
                                                                <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span> Baru Masuk
                                                            </span>
                                                        @elseif($order->status == 'progress')
                                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-500/10 text-blue-500 border border-blue-500/20 shadow-[0_0_10px_rgba(59,130,246,0.2)]">
                                                                <i class="ph-bold ph-arrows-clockwise animate-spin"></i> Diproses
                                                            </span>
                                                        @elseif($order->status == 'completed')
                                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-600 border border-emerald-500/20">
                                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Selesai
                                                            </span>
                                                        @elseif($order->status == 'cancelled')
                                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-red-500/10 text-red-600 border border-red-500/20">
                                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Batal
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                {{-- Actions --}}
                                                <div class="flex flex-wrap gap-2 justify-end">
                                                    <button wire:click="$dispatch('openModal', { orderId: {{ $order->id }} })"
                                                            class="bg-white dark:bg-slate-800 border border-border hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 font-bold rounded-full px-4 py-1.5 shadow-sm transition-transform hover:-translate-y-0.5 text-sm"
                                                            title="Lihat Detail Pesanan">
                                                        Detail
                                                    </button>

                                                    @if($storeType !== 'resto')
                                                        @if($order->status == 'pending')
                                                            <button wire:click="$dispatch('trigger-payment-modal', { orderId: {{ $order->id }} })"
                                                                    class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-full px-4 py-1.5 shadow-sm transition-transform hover:-translate-y-0.5 text-sm">
                                                                Bayar
                                                            </button>
                                                            <button @click="$dispatch('open-cancel-modal', { orderId: {{ $order->id }} })"
                                                                    class="border border-red-500 text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 font-bold rounded-full px-4 py-1.5 shadow-sm transition-transform hover:-translate-y-0.5 text-sm">
                                                                Batal
                                                            </button>
                                                        @elseif($order->status == 'paid')
                                                            <button wire:click="updateStatus({{ $order->id }}, 'progress')"
                                                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-full px-4 py-1.5 shadow-sm transition-transform hover:-translate-y-0.5 text-sm">
                                                                Proses
                                                            </button>
                                                        @elseif($order->status == 'progress')
                                                            <button wire:click="updateStatus({{ $order->id }}, 'completed')"
                                                                    class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-full px-4 py-1.5 shadow-sm transition-transform hover:-translate-y-0.5 text-sm">
                                                                Selesai
                                                            </button>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                {{-- Infinite Scroll Bottom Loader --}}
                @if($orders->hasMorePages())
                    <div x-intersect.full="$wire.loadMore()" class="flex justify-center items-center py-6 border-t border-border">
                        <i class="ph-bold ph-spinner animate-spin text-slate-400 text-xl mr-2"></i>
                        <span class="font-bold text-slate-500 dark:text-slate-400 text-sm">Memuat data selanjutnya...</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ===== SPLIT BILL MODAL ===== --}}
    @include('pages.tenant.order.⚡order-list._modal-split-bill')

    {{-- ===== PREMIUM TUTORIAL & HELP MODAL ===== --}}
    <div x-show="showGuideModalState"
         style="display: none;"
         x-transition.opacity
         class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4"
         @keydown.escape.window="showGuideModalState = false">
         
         <div x-show="showGuideModalState"
              x-transition:enter="transition ease-out duration-300"
              x-transition:enter-start="opacity-0 scale-95"
              x-transition:enter-end="opacity-100 scale-100"
              x-transition:leave="transition ease-in duration-200"
              x-transition:leave-start="opacity-100 scale-100"
              x-transition:leave-end="opacity-0 scale-95"
              class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-lg shadow-2xl overflow-hidden relative"
              @click.outside="showGuideModalState = false">
              
                 {{-- Top Hero Graphic Area --}}
                 <div class="relative h-40 bg-slate-50 dark:bg-slate-800 overflow-hidden border-b border-border">
                     <div class="absolute inset-0 bg-gradient-to-br from-orange-500/10 to-orange-500/5"></div>
                     <!-- Decorative circles -->
                     <div class="absolute rounded-full bg-orange-400/20 w-48 h-48 -top-12 -right-12"></div>
                     <div class="absolute rounded-full bg-brand-accent/10 w-24 h-24 -bottom-5 left-[10%]"></div>
                     
                     <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-center w-full z-10">
                         <div class="inline-flex items-center justify-center bg-white dark:bg-slate-900 border border-border rounded-full shadow-sm mb-3 w-16 h-16">
                             <i class="ph-fill ph-clipboard-text text-orange-500 text-3xl"></i>
                         </div>
                         <h5 class="font-bold mb-0 text-slate-800 dark:text-slate-200 font-serif text-lg">Alur Transaksi</h5>
                     </div>
                     <button type="button" 
                             @click="showGuideModalState = false"
                             class="absolute top-4 right-4 bg-white/80 dark:bg-slate-800/80 hover:bg-white dark:hover:bg-slate-800 rounded-full p-2 text-slate-500 hover:text-slate-800 transition-all z-20">
                        <i class="ph-bold ph-x text-lg"></i>
                     </button>
                 </div>

                 {{-- Body --}}
                 <div class="p-6 md:p-8 pt-6">
                    <p class="text-slate-500 dark:text-slate-400 text-center text-sm mb-6 pb-4 border-b border-border">
                        Pahami alur status pesanan agar operasional toko Anda berjalan lancar dan pelanggan puas.
                    </p>

                    <div class="relative pl-6">
                        <!-- Connecting Line -->
                        <div class="absolute bg-slate-200 dark:bg-slate-700 w-1 rounded-full top-6 bottom-6 left-[21px]"></div>

                        <!-- Step 1 -->
                        <div class="flex gap-4 relative mb-6">
                            <div class="w-10 h-10 rounded-full bg-sky-500/10 text-sky-500 flex items-center justify-center shrink-0 z-10 border-4 border-white dark:border-slate-900 shadow-[0_0_0_1px_rgba(14,165,233,0.2)] -ml-5">
                                <i class="ph-fill ph-bag text-lg"></i>
                            </div>
                            <div class="pt-1">
                                <h6 class="font-bold mb-1 text-slate-800 dark:text-slate-200">1. Baru Masuk (Lunas)</h6>
                                <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed">Pesanan baru saja dibayar. Segera klik <strong class="text-slate-700 dark:text-slate-300">"Proses"</strong> untuk mulai menyiapkan barang/makanan.</p>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="flex gap-4 relative mb-6">
                            <div class="w-10 h-10 rounded-full bg-blue-500/10 text-blue-500 flex items-center justify-center shrink-0 z-10 border-4 border-white dark:border-slate-900 shadow-[0_0_0_1px_rgba(59,130,246,0.2)] -ml-5">
                                <i class="ph-bold ph-arrows-clockwise animate-spin text-lg"></i>
                            </div>
                            <div class="pt-1">
                                <h6 class="font-bold mb-1 text-slate-800 dark:text-slate-200">2. Sedang Diproses</h6>
                                <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed">Pesanan sedang disiapkan. Jika barang sudah siap diserahkan, klik <strong class="text-slate-700 dark:text-slate-300">"Selesai"</strong>.</p>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="flex gap-4 relative">
                            <div class="w-10 h-10 rounded-full bg-emerald-500/10 text-emerald-600 flex items-center justify-center shrink-0 z-10 border-4 border-white dark:border-slate-900 shadow-[0_0_0_1px_rgba(16,185,129,0.2)] -ml-5">
                                <i class="ph-fill ph-check-circle text-lg"></i>
                            </div>
                            <div class="pt-1">
                                <h6 class="font-bold mb-1 text-slate-800 dark:text-slate-200">3. Selesai</h6>
                                <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed">Pesanan telah diserahkan. Transaksi sukses tercatat di sistem! 🎉</p>
                            </div>
                        </div>
                    </div>
                 </div>

                 {{-- Footer --}}
                 <div class="p-6 pt-0 border-t-0 flex justify-center">
                     <button type="button" 
                             @click="showGuideModalState = false"
                             class="bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-full px-12 py-3 shadow-sm transition-transform hover:-translate-y-0.5 w-full">
                         Oke, Paham!
                     </button>
                 </div>
         </div>
    </div>
</div>


