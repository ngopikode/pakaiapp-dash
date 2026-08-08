<div class="relative w-full max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mt-6 sm:mt-10 font-sans pb-16">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 pb-6 border-b border-slate-200 dark:border-slate-800">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white">Rekap Belanja Pasar</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Total bahan yang perlu dibeli berdasarkan pesanan terjadwal.</p>
        </div>
        <a href="{{ route('pre-order') }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
            <i class="ph ph-arrow-left text-base"></i>
            Daftar Pesanan
        </a>
    </div>

    {{-- Date Picker --}}
    <div class="flex items-center gap-3 mb-6">
        <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Pengiriman tanggal:</label>
        <input type="date" wire:model.live="selectedDate"
               class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 transition-shadow">
    </div>

    {{-- Recap Table --}}
    @if($recap->isNotEmpty())
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden"
             x-data="{ copied: false }">

            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-bold text-slate-800 dark:text-white">
                        {{ $date->locale('id')->translatedFormat('l, d F Y') }}
                    </p>
                    <p class="text-xs text-slate-400">{{ $recap->count() }} jenis produk</p>
                </div>
                <button
                    x-on:click="
                        const lines = [...document.querySelectorAll('[data-recap-row]')].map(el => el.dataset.recapRow);
                        const header = '🛒 *Daftar Belanja Pasar - {{ $date->format('d/m/Y') }}*\n';
                        navigator.clipboard.writeText(header + lines.join('\n'));
                        copied = true;
                        setTimeout(() => copied = false, 2000);
                    "
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-white bg-orange-500 hover:bg-orange-600 active:scale-95 rounded-xl transition-all">
                    <i class="ph ph-copy" x-show="!copied"></i>
                    <i class="ph ph-check" x-show="copied" x-cloak></i>
                    <span x-show="!copied">Salin Teks WA</span>
                    <span x-show="copied" x-cloak>Tersalin!</span>
                </button>
            </div>

            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @foreach($recap as $index => $row)
                    @php
                        $label = $row->product_name . ($row->variant_name ? " ({$row->variant_name})" : '');
                        $recapRow = "- {$label}: {$row->total_qty} pcs";
                    @endphp
                    <div class="flex items-center justify-between px-5 py-3.5"
                         data-recap-row="{{ $recapRow }}">
                        <div>
                            <p class="text-sm font-semibold text-slate-800 dark:text-white">{{ $row->product_name }}</p>
                            @if($row->variant_name)
                                <p class="text-xs text-slate-400">{{ $row->variant_name }}</p>
                            @endif
                        </div>
                        <span class="text-base font-black text-orange-500 tabular-nums">
                            {{ $row->total_qty }} <span class="text-xs font-bold text-slate-400">pcs</span>
                        </span>
                    </div>
                @endforeach
            </div>

            <div class="px-5 py-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-800">
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    <i class="ph ph-info"></i>
                    Total di atas sudah menjumlahkan semua pesanan aktif (pending + selesai) untuk tanggal ini. Pesanan yang dibatalkan tidak dihitung.
                </p>
            </div>
        </div>
    @else
        <div class="text-center py-20 text-slate-400 dark:text-slate-600">
            <i class="ph ph-shopping-cart-simple text-5xl mb-3 block"></i>
            <p class="text-sm font-semibold">Belum ada pesanan aktif untuk tanggal ini.</p>
        </div>
    @endif

</div>
