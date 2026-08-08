<div class="relative w-full max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mt-6 sm:mt-10 font-sans pb-28">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 pb-6 border-b border-slate-200 dark:border-slate-800">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white">Pesanan Terjadwal</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola pesanan pre-order berdasarkan tanggal pengiriman.</p>
        </div>
        <a href="{{ route('pre-order.recap') }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
            <i class="ph ph-chart-bar text-base"></i>
            Rekap Belanja Pasar
        </a>
    </div>

    {{-- Date Picker + Stats --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 mb-6">
        <div class="flex items-center gap-2">
            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Tanggal Kirim:</label>
            <input type="date" wire:model.live="selectedDate"
                   class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 transition-shadow">
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold bg-yellow-100 dark:bg-yellow-500/10 text-yellow-700 dark:text-yellow-400 rounded-full">
                <i class="ph ph-clock"></i> Pending: {{ $pendingCount }}
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold bg-green-100 dark:bg-green-500/10 text-green-700 dark:text-green-400 rounded-full">
                <i class="ph ph-check-circle"></i> Selesai: {{ $paidCount }}
            </span>
        </div>
    </div>

    {{-- Order List --}}
    <div class="space-y-3">
        @forelse($orders as $order)
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-[0_2px_10px_rgb(0,0,0,0.04)]">
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            <span class="text-sm font-black text-slate-900 dark:text-white">{{ $order->customer_name }}</span>
                            <span class="text-xs font-mono text-slate-400">{{ $order->invoice_code }}</span>
                            @if($order->status === 'pending')
                                <span class="px-2 py-0.5 text-[10px] font-bold bg-yellow-100 dark:bg-yellow-500/10 text-yellow-700 dark:text-yellow-400 rounded-full uppercase">Pending</span>
                            @else
                                <span class="px-2 py-0.5 text-[10px] font-bold bg-green-100 dark:bg-green-500/10 text-green-700 dark:text-green-400 rounded-full uppercase">Selesai</span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 truncate">
                            <i class="ph ph-map-pin"></i> {{ $order->customer_address }}
                        </p>
                        <div class="flex items-center gap-3 mt-2 flex-wrap">
                            <span class="text-xs text-slate-500 dark:text-slate-400">
                                <i class="ph ph-clock"></i> {{ $order->deliverySlot?->name ?? '-' }}
                            </span>
                            <span class="text-xs text-slate-500 dark:text-slate-400">
                                <i class="ph ph-map-trifold"></i> {{ $order->deliveryZone?->name ?? '-' }}
                            </span>
                            <span class="text-xs text-slate-500 dark:text-slate-400">
                                <i class="ph ph-credit-card"></i>
                                {{ strtolower($order->payment_method) === 'cash' ? 'COD' : 'QRIS' }}
                            </span>
                        </div>
                        @if($order->items->isNotEmpty())
                            <div class="mt-2 space-y-0.5">
                                @foreach($order->items as $item)
                                    <p class="text-xs text-slate-600 dark:text-slate-400">
                                        {{ $item->product_name }}
                                        @if($item->variant_name) <span class="text-slate-400">({{ $item->variant_name }})</span> @endif
                                        × {{ $item->quantity }}
                                    </p>
                                @endforeach
                            </div>
                        @endif
                        @if($order->notes)
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 italic">
                                <i class="ph ph-note"></i> {{ $order->notes }}
                            </p>
                        @endif
                    </div>
                    <div class="flex flex-col items-end gap-2 shrink-0">
                        <p class="text-base font-black text-slate-900 dark:text-white">
                            Rp {{ number_format($order->total_price, 0, ',', '.') }}
                        </p>
                        @if($order->shipping_cost > 0)
                            <p class="text-xs text-slate-400">+ Ongkir Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</p>
                        @else
                            <p class="text-xs text-green-500 font-semibold">Gratis Ongkir</p>
                        @endif
                        @if($order->status === 'pending')
                            <button wire:click="cancelOrder({{ $order->id }})"
                                    wire:confirm="Batalkan pesanan {{ $order->invoice_code }}?"
                                    class="text-xs font-bold text-red-500 hover:text-red-700 transition-colors">
                                Batalkan
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-16 text-slate-400 dark:text-slate-600">
                <i class="ph ph-package text-5xl mb-3 block"></i>
                <p class="text-sm font-semibold">Belum ada pesanan untuk tanggal ini.</p>
            </div>
        @endforelse
    </div>

    {{-- Floating Complete All Button --}}
    @if($pendingCount > 0)
        <div class="fixed bottom-0 left-0 right-0 z-[100] xl:left-64 pointer-events-none">
            <div class="px-4 pb-4 sm:pb-6 pt-8 bg-gradient-to-t from-slate-50 via-slate-50/90 to-transparent dark:from-[#0B1120] dark:via-[#0B1120]/90 pointer-events-auto">
                <div class="max-w-6xl mx-auto">
                    <button wire:click="completeAll"
                            wire:confirm="Tandai semua {{ $pendingCount }} pesanan pending sebagai selesai?"
                            wire:loading.attr="disabled"
                            class="w-full sm:w-auto px-8 py-3 text-sm font-black bg-green-500 hover:bg-green-600 active:scale-95 text-white rounded-2xl shadow-lg hover:shadow-green-500/30 transition-all flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="completeAll" class="flex items-center gap-2">
                            <i class="ph-fill ph-check-circle text-lg"></i>
                            Selesaikan Semua ({{ $pendingCount }} Pesanan)
                        </span>
                        <span wire:loading wire:target="completeAll" class="flex items-center gap-2">
                            <i class="ph ph-spinner animate-spin text-lg"></i> Memproses...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
