@if($this->pendingOrders->isEmpty())
    <div
        class="flex min-h-[40vh] flex-col items-center justify-center rounded-3xl border border-emerald-800/15 bg-white/80 py-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div
            class="mb-4 flex h-24 w-24 items-center justify-center rounded-full bg-slate-100 shadow-sm dark:bg-slate-800">
            <i class="ph-bold ph-receipt text-4xl text-slate-400 dark:text-slate-600"></i>
        </div>
        <h4 class="mb-2 font-black text-slate-900 dark:text-white">Antrian Kosong</h4>
        <p class="mb-0 max-w-sm text-center text-sm font-semibold text-slate-500 dark:text-slate-400">
            Belum ada pesanan yang tertahan. Semua pesanan sudah diselesaikan dengan baik!
        </p>
    </div>
@else
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach($this->pendingOrders as $order)
            <div class="queue-card flex h-full flex-col overflow-hidden rounded-2xl">
                {{-- Header --}}
                <div class="flex items-start justify-between p-4 queue-header">
                    <div>
                        <div class="mb-1 flex items-center gap-2">
                            <h6 class="mb-0 text-base font-black text-slate-900 dark:text-white">{{ $order->invoice_code }}</h6>
                        </div>
                        <small class="flex items-center gap-1 text-[11px] font-bold text-slate-500 dark:text-slate-400">
                            <i class="ph-bold ph-clock"></i> {{ $order->created_at->diffForHumans() }}
                        </small>
                    </div>
                    <div class="flex flex-col items-end gap-2">
                        <span
                            class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-[11px] font-extrabold uppercase tracking-wider text-amber-800 shadow-sm dark:bg-amber-500/20 dark:text-amber-400">
                            <i class="ph-bold ph-hourglass-high me-1"></i>PENDING
                        </span>
                        @if($order->is_online)
                            <span
                                class="inline-flex items-center rounded-full border border-emerald-800/20 bg-emerald-50 px-3 py-1 text-[11px] font-extrabold text-emerald-800 shadow-sm dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-400">
                                <i class="ph-bold ph-device-mobile me-1"></i>Digital Menu
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Body --}}
                <div class="flex flex-1 flex-col">
                    {{-- Meta Tags --}}
                    <div class="flex flex-wrap gap-2 px-4 py-3 queue-meta-area">
                        <span
                            class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-700 shadow-sm dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200">
                            <i class="ph-bold ph-user text-slate-400"></i> {{ $order->customer_name }}
                        </span>
                        @if($order->table_number)
                            <span
                                class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-700 shadow-sm dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200">
                                <i class="ph-bold ph-hash text-slate-400"></i> Meja {{ $order->table_number }}
                            </span>
                        @endif
                        <span
                            class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-white px-3 py-2 text-xs font-bold capitalize text-slate-700 shadow-sm dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200">
                            <i class="ph-bold ph-tote text-slate-400"></i> {{ $order->order_type }}
                        </span>
                        @if($order->notes)
                            <span
                                class="inline-flex items-center gap-1 rounded-full border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700 shadow-sm dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-400"
                                title="Catatan: {{ $order->notes }}">
                                <i class="ph-bold ph-article"></i> Catatan
                            </span>
                        @endif
                    </div>

                    {{-- Items List --}}
                    <div class="flex-1 px-4 py-3" style="font-size: 0.85rem;">
                        @foreach($order->items as $item)
                            <div class="item-row mb-1 flex items-start justify-between rounded-2xl px-3 py-2.5">
                                <div class="pe-2 text-slate-900 dark:text-slate-100">
                                    <div class="flex items-start gap-2">
                                        <span
                                            class="inline-flex items-center rounded-lg bg-emerald-800 px-2 py-1 text-[11px] font-extrabold text-white dark:bg-emerald-500 dark:text-slate-950">{{ $item->quantity }}x</span>
                                        <div>
                                            <span class="font-bold">{{ $item->product_name }}</span>
                                            @if($item->variant_name)
                                                <span class="ms-1 text-sm text-slate-500 dark:text-slate-400">({{ $item->variant_name }})</span>
                                            @endif
                                            @if($item->discount > 0)
                                                <div
                                                    class="mt-1 text-xs font-bold text-emerald-600 dark:text-emerald-400">
                                                    <i class="ph-fill ph-tag me-1"></i>Hemat
                                                    Rp {{ number_format($item->discount * $item->quantity, 0, ',', '.') }}
                                                </div>
                                            @endif
                                            @if($item->note)
                                                <div class="mt-1 text-xs italic text-amber-600 dark:text-amber-400"><i
                                                        class="ph-bold ph-chat-circle-dots me-1"></i>{{ $item->note }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 shrink-0">
                                    <div class="text-end">
                                        @if($item->discount > 0)
                                            <small class="mb-1 block text-xs text-red-500 line-through opacity-70">
                                                Rp {{ number_format($item->subtotal + ($item->discount * $item->quantity), 0, ',', '.') }}
                                            </small>
                                        @endif
                                        <span
                                            class="font-black text-slate-900 dark:text-white">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                    </div>
                                    @if($order->status !== 'completed' && $order->status !== 'paid' && $item->kitchen_status === 'waiting')
                                        <button wire:click="voidItem({{ $item->id }})"
                                                wire:confirm="Yakin ingin membatalkan item ini? Stok akan dikembalikan otomatis."
                                                class="flex h-7 w-7 items-center justify-center rounded-full border-0 bg-red-50 text-red-500 hover:bg-red-100 dark:bg-red-500/10 dark:text-red-400"
                                                title="Batal (Void) Item">
                                            <i class="ph-fill ph-x-circle text-sm"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        <hr class="my-3 border-emerald-800/10 dark:border-slate-800">

                        {{-- Fees & Discounts --}}
                        <div class="px-2">
                            @if(($order->service_charge_amount ?? 0) > 0)
                                <div
                                    class="flex items-center justify-between py-1 text-xs font-semibold text-slate-500 dark:text-slate-400">
                                    <span>Biaya Layanan ({{ number_format($order->service_charge_percentage ?? 5) }}%)</span>
                                    <span
                                        class="font-bold text-slate-900 dark:text-white">Rp {{ number_format($order->service_charge_amount, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            @if(($order->tax_amount ?? 0) > 0)
                                <div
                                    class="flex items-center justify-between py-1 text-xs font-semibold text-slate-500 dark:text-slate-400">
                                    <span>Pajak PB1 ({{ number_format($order->tax_percentage ?? 10) }}%)</span>
                                    <span
                                        class="font-bold text-slate-900 dark:text-white">Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            @if(($order->discount ?? 0) > 0)
                                <div
                                    class="flex items-center justify-between py-1 text-xs font-bold text-emerald-600 dark:text-emerald-400">
                                    <span><i class="ph-bold ph-scissors me-1"></i>Diskon</span>
                                    <span>- Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Total Box --}}
                    <div
                        class="mx-4 mb-4 mt-auto flex items-center justify-between rounded-2xl border p-3 shadow-sm queue-total-box">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300">Total Pembayaran</span>
                        <h4 class="mb-0 font-black tracking-tight text-emerald-800 dark:text-emerald-400">
                            Rp {{ number_format($order->total_price ?? $order->subtotal, 0, ',', '.') }}</h4>
                    </div>
                </div>

                {{-- Actions Footer --}}
                <div class="border-t border-emerald-800/10 bg-white p-3 dark:border-slate-800 dark:bg-slate-900">
                    <div class="mb-3 flex gap-2">
                        @if($order->items->whereIn('kitchen_status', ['processing', 'ready', 'completed'])->count() === 0)
                            <button @click="$dispatch('open-cancel-modal', { orderId: {{ $order->id }} })"
                                    class="flex h-11 flex-1 items-center justify-center rounded-2xl border border-red-200 bg-red-50 font-black text-red-600 transition hover:bg-red-100 dark:border-red-500/20 dark:bg-red-500/10 dark:text-red-400"
                                    title="Batalkan Pesanan">
                                <i class="ph-bold ph-trash"></i>
                            </button>
                        @endif
                        <button wire:click="setEditOrder({{ $order->id }})"
                                class="flex h-11 flex-1 items-center justify-center rounded-2xl border border-emerald-800/20 bg-white font-black text-slate-700 shadow-sm transition hover:bg-emerald-50 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200"
                                title="Tambah Pesanan ke Meja Ini">
                            <i class="ph-bold ph-plus text-lg"></i>
                        </button>
                        @if($order->items->count() > 1)
                            <button @click="openSplitModal({{ json_encode([
                                    'id' => $order->id,
                                    'invoice_code' => $order->invoice_code,
                                    'items' => $order->items
                                ]) }})"
                                    class="flex h-11 flex-1 items-center justify-center rounded-2xl border border-emerald-800/20 bg-white font-black text-slate-700 shadow-sm transition hover:bg-emerald-50 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200"
                                    title="Pisah Bill (Bayar Sebagian)">
                                <i class="ph-bold ph-split-horizontal text-lg"></i>
                            </button>
                        @endif
                        @if($this->pendingOrders->where('id', '!=', $order->id)->where('amount_paid', 0)->count() > 0 && $order->amount_paid == 0)
                            <button
                                @click="openMergeModal({{ json_encode(['id' => $order->id, 'invoice_code' => $order->invoice_code]) }})"
                                class="flex h-11 flex-1 items-center justify-center rounded-2xl border border-emerald-800/20 bg-white font-black text-slate-700 shadow-sm transition hover:bg-emerald-50 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200"
                                title="Gabung Struk / Merge Bill">
                                <i class="ph-bold ph-intersect text-lg"></i>
                            </button>
                        @endif
                    </div>
                    <button @click="openPayForOrder({{ json_encode([
                                        'id' => $order->id,
                                        'invoice_code' => $order->invoice_code,
                                        'customer_name' => $order->customer_name,
                                        'subtotal' => $order->subtotal,
                                        'total_price' => $order->total_price ?? $order->subtotal,
                                    ]) }})"
                            class="queue-cta-btn flex h-12 w-full items-center justify-center gap-2 rounded-2xl font-black text-white shadow-sm"
                            style="border: none;">
                        <i class="ph-bold ph-coins text-lg"></i> Bayar Sekarang
                    </button>
                </div>
            </div>
        @endforeach
    </div>
@endif
