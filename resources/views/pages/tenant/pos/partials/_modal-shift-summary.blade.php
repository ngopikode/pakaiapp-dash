<div class="fixed inset-0 z-[1060] flex items-center justify-center p-4 sm:p-0"
     x-data="{ isShiftSummaryModalOpen: false }"
     x-show="isShiftSummaryModalOpen" x-cloak
     @open-shift-summary-modal.window="isShiftSummaryModalOpen = true"
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>

    <div class="relative w-full max-w-lg transform overflow-hidden rounded-[24px] bg-slate-50 text-left align-middle shadow-2xl transition-all dark:bg-slate-900 sm:my-8 border border-slate-200 dark:border-slate-800"
         x-show="isShiftSummaryModalOpen"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

        @if($closedShiftSummary)
        <div class="bg-white px-6 pb-6 pt-6 dark:bg-slate-900 sm:p-8 sm:pb-6">
            <div class="text-center mb-6">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/50 mb-4 ring-8 ring-emerald-50 dark:ring-emerald-900/20">
                    <i class="ph-fill ph-check-circle text-3xl text-emerald-600 dark:text-emerald-400"></i>
                </div>
                <h3 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">Shift Selesai</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Laporan Z-Report telah tersimpan di sistem.</p>
            </div>

            <!-- Detail Laporan mirip struk -->
            <div class="rounded-2xl border-2 border-slate-100 bg-white dark:border-slate-800 dark:bg-slate-950 p-5 font-mono text-sm shadow-sm relative overflow-hidden">
                <!-- Ornamen gerigi struk (opsional) -->
                <div class="absolute top-0 left-0 right-0 h-1 flex justify-around opacity-20">
                    @for($i=0; $i<20; $i++)
                        <div class="w-2 h-2 rounded-full bg-slate-200 -mt-1"></div>
                    @endfor
                </div>

                <div class="text-center font-bold text-slate-800 dark:text-slate-200 mb-4 pb-4 border-b-2 border-dashed border-slate-200 dark:border-slate-800">
                    <div>Z-REPORT (SHIFT SUMMARY)</div>
                    <div class="text-xs font-normal text-slate-500 mt-1">Kasir: {{ $closedShiftSummary['cashier_name'] }}</div>
                    <div class="text-xs font-normal text-slate-500">{{ $closedShiftSummary['started_at'] }} - {{ $closedShiftSummary['ended_at'] }}</div>
                </div>

                <div class="space-y-3 text-slate-600 dark:text-slate-400">
                    <div class="flex justify-between">
                        <span>Modal Awal</span>
                        <span>Rp {{ number_format($closedShiftSummary['starting_cash'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Penjualan Tunai (+)</span>
                        <span>Rp {{ number_format($closedShiftSummary['cash_sales'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Pengeluaran Kas (-)</span>
                        <span>Rp {{ number_format($closedShiftSummary['cash_expenses'], 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="my-4 border-b-2 border-dashed border-slate-200 dark:border-slate-800"></div>

                <div class="space-y-3 font-semibold text-slate-800 dark:text-slate-200">
                    <div class="flex justify-between">
                        <span>Total Uang Seharusnya</span>
                        <span>Rp {{ number_format($closedShiftSummary['expected_cash'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-blue-600 dark:text-blue-400">
                        <span>Total Uang Aktual Laci</span>
                        <span>Rp {{ number_format($closedShiftSummary['actual_cash'], 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="my-4 border-b-2 border-dashed border-slate-200 dark:border-slate-800"></div>

                <div class="flex justify-between items-center px-3 py-3 rounded-xl {{ $closedShiftSummary['difference'] < 0 ? 'bg-rose-50 dark:bg-rose-900/30' : ($closedShiftSummary['difference'] > 0 ? 'bg-amber-50 dark:bg-amber-900/30' : 'bg-emerald-50 dark:bg-emerald-900/30') }}">
                    <span class="font-bold {{ $closedShiftSummary['difference'] < 0 ? 'text-rose-700 dark:text-rose-400' : ($closedShiftSummary['difference'] > 0 ? 'text-amber-700 dark:text-amber-400' : 'text-emerald-700 dark:text-emerald-400') }}">
                        SELISIH (DIFFERENCE)
                    </span>
                    <span class="text-lg font-black tracking-tight {{ $closedShiftSummary['difference'] < 0 ? 'text-rose-600 dark:text-rose-400' : ($closedShiftSummary['difference'] > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400') }}">
                        {{ $closedShiftSummary['difference'] > 0 ? '+' : '' }}Rp {{ number_format($closedShiftSummary['difference'], 0, ',', '.') }}
                    </span>
                </div>
                
                @if($closedShiftSummary['difference'] < 0)
                    <div class="mt-2 text-[11px] text-center text-rose-500 font-sans font-medium">Uang laci kurang (tekor). Laporan ini dicatat oleh sistem.</div>
                @elseif($closedShiftSummary['difference'] > 0)
                    <div class="mt-2 text-[11px] text-center text-amber-500 font-sans font-medium">Uang laci lebih. Laporan ini dicatat oleh sistem.</div>
                @endif
            </div>
        </div>
        <div class="bg-slate-50 px-6 py-5 dark:bg-slate-800/50 sm:flex sm:flex-row-reverse sm:px-8 border-t border-slate-200 dark:border-slate-800">
            <button type="button" @click="isShiftSummaryModalOpen = false; window.dispatchEvent(new CustomEvent('shift-closed'))"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-slate-900 px-6 py-3 text-sm font-bold text-white shadow-sm transition-all hover:bg-slate-800 active:scale-[0.98] dark:bg-white dark:text-slate-900 dark:hover:bg-slate-200 sm:ml-3 sm:w-auto">
                Kunci Kasir & Selesai
            </button>
            <button type="button" onclick="window.print()"
                    class="mt-3 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-white px-6 py-3 text-sm font-bold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-300 transition-all hover:bg-slate-50 active:scale-[0.98] dark:bg-slate-800 dark:text-slate-300 dark:ring-slate-700 dark:hover:bg-slate-700 sm:mt-0 sm:w-auto">
                <i class="ph-bold ph-printer"></i> Print Z-Report
            </button>
        </div>
        @endif
    </div>
</div>
