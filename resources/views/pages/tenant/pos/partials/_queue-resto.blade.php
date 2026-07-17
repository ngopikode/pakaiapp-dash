<div class="overflow-y-auto hide-scrollbar pb-10 relative"
     x-init="
        const ids = @js($queueOrders->pluck('id'));
        if (selectedQueueOrder) {
            if (!ids.includes(selectedQueueOrder.id)) {
                selectedQueueOrder = null;
                isMobileQueueDetailOpen = false;
                isDesktopQueueDetailOpen = false;
            } else {
                let updatedOrders = @js($queueOrders->pluck('orderData')->values());
                let updated = updatedOrders.find(o => o.id === selectedQueueOrder.id);
                selectedQueueOrder = updated || null;
            }
        }
     ">

    <!-- Skeleton Loading -->
    <div wire:loading.block class="absolute inset-0 z-10 bg-background/50 backdrop-blur-sm">
        <div class="grid gap-4" :class="isDesktopQueueDetailOpen && selectedQueueOrder ? 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-2' : 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4'">
            @for($i = 0; $i < 4; $i++)
                <div class="dash-card flex flex-col p-5 min-h-[180px]">
                    <div class="flex justify-between items-start mb-4">
                        <div class="skeleton-shimmer h-5 w-32 rounded"></div>
                        <div class="skeleton-shimmer h-4 w-12 rounded"></div>
                    </div>
                    <div class="skeleton-shimmer h-4 w-40 rounded mb-2"></div>
                    <div class="skeleton-shimmer h-4 w-24 rounded mb-6"></div>
                    <div class="border-t border-dashed border-border mb-4"></div>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <div class="skeleton-shimmer h-4 w-3/4 rounded"></div>
                            <div class="skeleton-shimmer h-4 w-10 rounded"></div>
                        </div>
                        <div class="flex justify-between">
                            <div class="skeleton-shimmer h-4 w-1/2 rounded"></div>
                            <div class="skeleton-shimmer h-4 w-10 rounded"></div>
                        </div>
                    </div>
                    <div class="mt-auto pt-4 flex gap-2">
                        <div class="skeleton-shimmer h-8 w-24 rounded-full"></div>
                        <div class="skeleton-shimmer h-8 w-8 rounded-full ml-auto"></div>
                        <div class="skeleton-shimmer h-8 w-8 rounded-full"></div>
                    </div>
                </div>
            @endfor
        </div>
    </div>

    <!-- Actual Content -->
    <div wire:loading.class="invisible">
        @if($this->activeOrders->isEmpty())
            <div class="flex min-h-[40vh] flex-col items-center justify-center rounded-2xl border border-dashed border-border bg-card/50 py-10 shadow-sm">
                <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-secondary shadow-inner">
                    <i class="ph-bold ph-receipt text-3xl text-muted-foreground"></i>
                </div>
                <h4 class="mb-1 font-bold text-foreground">Antrian Kosong</h4>
                <p class="mb-0 text-sm text-muted-foreground">Belum ada pesanan yang tertahan saat ini.</p>
            </div>
        @elseif($queueOrders->isEmpty())
            <div class="flex min-h-[40vh] flex-col items-center justify-center rounded-2xl border border-dashed border-border bg-card/50 py-10 shadow-sm">
                <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-secondary shadow-inner">
                    <i class="ph-bold ph-magnifying-glass text-3xl text-muted-foreground"></i>
                </div>
                <h4 class="mb-1 font-bold text-foreground">Tidak Ada Hasil</h4>
                <p class="mb-0 text-sm text-muted-foreground">Tidak ada pesanan yang sesuai filter/pencarian ini.</p>
            </div>
        @else
            <div class="grid gap-4" :class="isDesktopQueueDetailOpen && selectedQueueOrder ? 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-2' : 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4'">
                @foreach($queueOrders as $order)
                    <div wire:key="queue-order-{{ $order->id }}"
                         @click="openQueueDetail({{ json_encode($order->orderData) }})"
                         @keydown.enter="openQueueDetail({{ json_encode($order->orderData) }})"
                         @keydown.space.prevent="openQueueDetail({{ json_encode($order->orderData) }})"
                         role="button" tabindex="0"
                         class="dash-card flex flex-col p-5 hover:-translate-y-1 hover:shadow-md cursor-pointer group min-w-0">

                        <!-- Header -->
                        <div class="flex flex-col mb-2 gap-0.5 min-w-0">
                            <h3 class="font-bold text-foreground text-[16px] leading-tight group-hover:text-primary transition-colors truncate">{{ $order->customer_name ?: 'Guest' }}</h3>
                            <span class="text-[13px] font-medium text-muted-foreground">#{{ str_pad($order->invoice_code, 3, '0', STR_PAD_LEFT) }}</span>
                        </div>

                        <!-- Time & Type -->
                        <div class="flex flex-col gap-2 text-[12px] font-medium text-muted-foreground mb-4">
                            <div class="flex items-center gap-1.5">
                                <i class="ph-bold ph-clock"></i>
                                <span>{{ $order->created_at->diffForHumans() }}</span>
                            </div>

                            <div class="flex items-center gap-1.5 min-w-0">
                                @if($order->order_type === 'dinein')
                                    <i class="ph-bold ph-hash shrink-0"></i>
                                    <span class="truncate">Meja {{ $order->table_number ?? '-' }}</span>
                                @elseif($order->order_type === 'takeaway')
                                    <i class="ph-bold ph-bag shrink-0"></i>
                                    <span class="truncate">Bawa Pulang</span>
                                @elseif($order->order_type === 'delivery')
                                    <i class="ph-bold ph-moped shrink-0"></i>
                                    <span class="truncate">Diantar</span>
                                @else
                                    <i class="ph-bold ph-storefront shrink-0"></i>
                                    <span class="truncate">Retail</span>
                                @endif

                                @if($order->is_online)
                                    <span class="w-1 h-1 rounded-full bg-border shrink-0"></span>
                                    <span class="flex items-center gap-1 text-emerald-600 dark:text-emerald-400 shrink-0">
                                        <i class="ph-bold ph-device-mobile"></i> QR
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="border-t border-dashed border-border mb-4"></div>

                        <!-- Items Preview -->
                        <div class="flex-1 mb-4 flex flex-col gap-2 min-w-0">
                            @foreach($order->items->take(2) as $item)
                                <div class="flex justify-between items-start text-[13px] gap-2">
                                    <div class="flex gap-2 min-w-0">
                                        <span class="font-bold text-primary shrink-0">{{ $item->quantity }}x</span>
                                        <span class="text-foreground font-medium truncate">{{ $item->product_name }}</span>
                                    </div>
                                    <span class="font-bold text-foreground shrink-0">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                </div>
                            @endforeach

                            @if($order->items->count() > 2)
                                <button type="button" @click.stop
                                    class="text-[12px] text-muted-foreground hover:text-primary text-left font-medium mt-1 transition-colors">
                                    + {{ $order->items->count() - 2 }} more items...
                                </button>
                            @endif

                            @if($order->notes)
                                <div class="mt-2 text-[11px] bg-accent text-accent-foreground p-2 rounded-lg italic flex gap-1.5 items-start">
                                    <i class="ph-bold ph-article mt-0.5 shrink-0"></i>
                                    <span class="line-clamp-2">{{ $order->notes }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Total Box -->
                        <div class="flex justify-between items-end mb-4">
                            <span class="text-[12px] font-medium text-muted-foreground">Total</span>
                            <span class="text-[18px] font-black text-emerald-600 dark:text-emerald-400 truncate ml-2">
                                Rp {{ number_format($order->total_price ?? $order->subtotal, 0, ',', '.') }}
                            </span>
                        </div>

                        <!-- Footer Actions & Status Pill -->
                        <div class="mt-auto flex items-center justify-between gap-2 pt-2">
                            <span class="inline-flex items-center justify-center rounded-full px-3 py-1.5 text-[11px] font-black uppercase tracking-widest shadow-sm"
                                  :class="getStatusColor('{{ $order->kStatus }}')">
                                <i class="ph-bold me-1" :class="getStatusIcon('{{ $order->kStatus }}')"></i><span x-text="getStatusLabel('{{ $order->kStatus }}')"></span>
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
