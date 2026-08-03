<div x-data="{ isCloseShiftModalOpen: false }"
     @open-close-shift-modal.window="isCloseShiftModalOpen = true;"
     @close-close-shift-modal.window="isCloseShiftModalOpen = false">

    <div class="fixed inset-0 z-[1050] flex items-center justify-center p-4 sm:p-0"
         x-show="isCloseShiftModalOpen" x-cloak
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
         @click="isCloseShiftModalOpen = false"></div>

    <div class="relative w-full max-w-2xl transform overflow-hidden rounded-2xl bg-white text-left align-middle shadow-xl transition-all dark:bg-slate-900 flex flex-col max-h-[90dvh]"
         x-show="isCloseShiftModalOpen"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

        <!-- Header -->
        <div class="border-b border-slate-200 bg-white px-6 py-4 dark:border-slate-800 dark:bg-slate-900 shrink-0">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="ph-bold ph-power text-rose-500"></i> Tutup Shift (Z-Report)
            </h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Lakukan perhitungan stok dan kasir sebelum pergantian shift.
            </p>
        </div>

        <!-- Form Area (Scrollable) -->
        <div class="flex-1 overflow-y-auto bg-slate-50 p-6 dark:bg-slate-900/50">
            <form wire:submit.prevent="submitCloseShift" id="close-shift-form">
                
                @if($closeShiftStep === 1)
                <!-- STEP 1: Opname Stok Kritis -->
                <div>
                    <h4 class="font-semibold text-slate-800 dark:text-slate-200 mb-4 flex items-center gap-2">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-100 text-xs text-blue-700 dark:bg-blue-900/50 dark:text-blue-300">1</span>
                        Perhitungan Stok Bahan Kritis
                    </h4>
                    
                    @if(count($opnameItems) > 0)
                        <div class="rounded-xl border border-slate-200 bg-white overflow-hidden dark:border-slate-800 dark:bg-slate-900">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-slate-50 dark:bg-slate-800/50">
                                    <tr>
                                        <th class="px-4 py-3 font-medium text-slate-500 dark:text-slate-400">Bahan Baku</th>
                                        <th class="px-4 py-3 font-medium text-slate-500 dark:text-slate-400 text-right w-40">Stok Fisik Real</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                                    @foreach($opnameItems as $index => $item)
                                        <tr>
                                            <td class="px-4 py-3">
                                                <div class="font-medium text-slate-900 dark:text-white">{{ $item['name'] }}</div>
                                                <div class="text-xs text-slate-500">Sistem: {{ $item['system_stock'] }} {{ $item['unit'] }}</div>
                                            </td>
                                            <td class="px-4 py-3 text-right">
                                                <div class="flex items-center justify-end gap-2">
                                                    <input type="number" step="0.01" wire:model="opnameItems.{{ $index }}.physical_stock"
                                                           class="w-24 rounded-lg border-slate-300 px-3 py-1.5 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                                           required min="0" placeholder="0">
                                                    <span class="text-xs text-slate-500 w-8 text-left">{{ $item['unit'] }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="rounded-xl border border-dashed border-slate-300 p-6 text-center text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
                            Tidak ada bahan baku kritis yang ditandai untuk opname.
                        </div>
                    @endif
                </div>
                @endif

                @if($closeShiftStep === 2)
                <!-- STEP 2: Blind Cash Count -->
                <div x-data="{ 
                    displayValue: '',
                    init() { setTimeout(() => $refs.actualCashInput.focus(), 100) },
                    formatValue(val) { 
                        let num = val.toString().replace(/\D/g, ''); 
                        this.displayValue = num ? new Intl.NumberFormat('id-ID').format(num) : '';
                        $wire.set('actualCash', num);
                    }
                }">
                    <h4 class="font-bold text-slate-800 dark:text-slate-200 mb-5 flex items-center gap-3">
                        <span class="flex h-7 w-7 items-center justify-center rounded-full bg-rose-100 text-sm font-black text-rose-700 dark:bg-rose-900/50 dark:text-rose-300">2</span>
                        Perhitungan Uang Kasir (Blind Count)
                    </h4>
                    
                    <div class="rounded-2xl border-2 border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
                        <label class="block text-base font-bold text-slate-800 dark:text-slate-200 mb-2">Total Uang Fisik di Laci Saat Ini</label>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">
                            Keluarkan semua uang (kertas & koin) dari laci kasir, hitung total keseluruhannya, lalu ketik di bawah. Sistem akan mencocokkan dengan catatan penjualan otomatis.
                        </p>
                        
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                <span class="text-xl font-bold text-slate-400 dark:text-slate-500">Rp</span>
                            </div>
                            <input type="text" inputmode="numeric" 
                                   x-ref="actualCashInput"
                                   x-model="displayValue"
                                   @input="formatValue($event.target.value)"
                                   class="block w-full rounded-2xl border-2 border-slate-200 bg-slate-50 py-5 pl-12 pr-5 text-right text-4xl font-black text-slate-900 tracking-tight transition-all focus:border-rose-500 focus:bg-white focus:ring-4 focus:ring-rose-500/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white dark:focus:border-rose-500 dark:focus:bg-slate-900 placeholder:text-slate-300 dark:placeholder:text-slate-600"
                                   placeholder="0" required autocomplete="off">
                        </div>
                        @error('actualCash') <span class="mt-2 block text-sm font-medium text-rose-500">{{ $message }}</span> @enderror
                    </div>
                </div>
                @endif

            </form>
        </div>

        <!-- Footer -->
        <div class="border-t border-slate-200 bg-white px-6 py-4 dark:border-slate-800 dark:bg-slate-900 shrink-0 flex items-center justify-between">
            <button type="button" @click="isCloseShiftModalOpen = false"
                    class="rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-300 dark:ring-slate-700 dark:hover:bg-slate-700">
                Batal
            </button>
            
            <div class="flex items-center gap-3">
                @if($closeShiftStep === 2)
                    <button type="button" wire:click="$set('closeShiftStep', 1)"
                            class="rounded-xl bg-slate-100 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700">
                        Kembali
                    </button>
                @endif
                
                <button type="submit" form="close-shift-form" wire:loading.attr="disabled"
                        class="rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 disabled:opacity-75">
                    <span wire:loading.remove wire:target="submitCloseShift">
                        {{ $closeShiftStep === 1 ? 'Lanjut ke Kasir' : 'Konfirmasi Tutup Shift' }}
                    </span>
                    <span wire:loading wire:target="submitCloseShift">Memproses...</span>
                </button>
            </div>
        </div>
    </div>
</div>
</div>
