<div class="fixed inset-0 z-[1050] flex items-center justify-center p-4 sm:p-0"
     x-show="isShiftExpenseModalOpen" x-cloak
     @open-shift-expense-modal.window="isShiftExpenseModalOpen = true"
     @close-shift-expense-modal.window="isShiftExpenseModalOpen = false"
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"
         @click="isShiftExpenseModalOpen = false"></div>

    <div class="relative w-full max-w-md transform overflow-hidden rounded-[24px] bg-white text-left align-middle shadow-2xl transition-all dark:bg-slate-900 sm:my-8 border border-slate-200 dark:border-slate-800"
         x-show="isShiftExpenseModalOpen"
         x-data="{ 
            displayValue: '', 
            formatValue(val) { 
                let num = val.toString().replace(/\D/g, ''); 
                this.displayValue = num ? new Intl.NumberFormat('id-ID').format(num) : '';
                $wire.set('expenseAmount', num);
            }
         }"
         @open-shift-expense-modal.window="displayValue = ''; $wire.set('expenseAmount', 0); $wire.set('expenseDescription', ''); setTimeout(() => $refs.expenseAmountInput.focus(), 100)"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

        <form wire:submit="saveExpense">
            <div class="bg-white px-6 pb-6 pt-6 dark:bg-slate-900 sm:p-8 sm:pb-6">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-orange-100 dark:bg-orange-900/50 sm:mx-0 sm:h-12 sm:w-12 ring-8 ring-orange-50 dark:ring-orange-900/20">
                        <i class="ph-bold ph-receipt text-2xl text-orange-600 dark:text-orange-400"></i>
                    </div>
                    <div class="mt-4 text-center sm:ml-5 sm:mt-0 sm:text-left w-full">
                        <h3 class="text-xl font-bold leading-6 text-slate-900 dark:text-white" id="modal-title">Catat Pengeluaran</h3>
                        <div class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                            Catat uang yang diambil dari laci kasir (kasbon, beli es batu, bayar sampah, dll).
                        </div>

                        <div class="mt-6 space-y-4 text-left">
                            <div>
                                <label class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-300">Nominal Pengeluaran</label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                        <span class="text-xl font-bold text-slate-400 dark:text-slate-500">Rp</span>
                                    </div>
                                    <input type="text" inputmode="numeric" 
                                           x-ref="expenseAmountInput"
                                           x-model="displayValue" 
                                           @input="formatValue($event.target.value)"
                                           class="block w-full rounded-2xl border-2 border-slate-200 bg-slate-50 py-4 pl-12 pr-4 text-right text-3xl font-black text-slate-900 tracking-tight transition-all focus:border-orange-500 focus:bg-white focus:ring-4 focus:ring-orange-500/10 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:border-orange-500 dark:focus:bg-slate-900 placeholder:text-slate-300 dark:placeholder:text-slate-600"
                                           placeholder="0" required autocomplete="off">
                                </div>
                                @error('expenseAmount') <span class="mt-1 block text-sm font-medium text-rose-500">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-300">Keterangan / Tujuan</label>
                                <input type="text" wire:model="expenseDescription"
                                       class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 transition-colors focus:border-orange-500 focus:ring-orange-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:border-orange-500 placeholder:text-slate-400"
                                       placeholder="Contoh: Beli token listrik, Kasbon Budi" required autocomplete="off">
                                @error('expenseDescription') <span class="mt-1 block text-sm font-medium text-rose-500">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-slate-50 px-6 py-4 dark:bg-slate-800/50 sm:flex sm:flex-row-reverse sm:px-8">
                <button type="submit" wire:loading.attr="disabled"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-orange-600 px-6 py-3 text-sm font-bold text-white shadow-sm transition-all hover:bg-orange-500 focus:ring-4 focus:ring-orange-500/20 active:scale-[0.98] sm:ml-3 sm:w-auto disabled:opacity-75">
                    <span wire:loading.remove wire:target="saveExpense"><i class="ph-bold ph-receipt text-lg"></i> Simpan</span>
                    <span wire:loading wire:target="saveExpense"><i class="ph-bold ph-spinner animate-spin text-lg"></i> Memproses...</span>
                </button>
                <button type="button" @click="isShiftExpenseModalOpen = false"
                        class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-6 py-3 text-sm font-bold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-300 transition-all hover:bg-slate-50 active:scale-[0.98] dark:bg-slate-800 dark:text-slate-300 dark:ring-slate-700 dark:hover:bg-slate-700 sm:mt-0 sm:w-auto">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>
