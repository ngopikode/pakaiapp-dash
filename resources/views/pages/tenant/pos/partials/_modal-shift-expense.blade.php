<div class="fixed inset-0 z-[1050] flex items-center justify-center p-4 sm:p-0"
     x-show="isShiftExpenseModalOpen" x-cloak
     @open-shift-expense-modal.window="isShiftExpenseModalOpen = true; setTimeout(() => $refs.expenseAmountInput.focus(), 100)"
     @close-shift-expense-modal.window="isShiftExpenseModalOpen = false"
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"
         @click="isShiftExpenseModalOpen = false"></div>

    <div class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-white text-left align-middle shadow-xl transition-all dark:bg-slate-900 sm:my-8"
         x-show="isShiftExpenseModalOpen"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

        <form wire:submit="saveExpense">
            <div class="bg-white px-4 pb-4 pt-5 dark:bg-slate-900 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-orange-100 dark:bg-orange-900/50 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="ph ph-receipt text-xl text-orange-600 dark:text-orange-400"></i>
                    </div>
                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                        <h3 class="text-base font-semibold leading-6 text-slate-900 dark:text-white" id="modal-title">Catat Pengeluaran Laci</h3>
                        <div class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                            Gunakan uang dari laci kasir untuk pengeluaran operasional (petty cash) seperti beli galon, parkir, dll.
                        </div>

                        <div class="mt-4 space-y-4 text-left">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Nominal (Rp)</label>
                                <input type="number" wire:model="expenseAmount" x-ref="expenseAmountInput"
                                       class="block w-full rounded-xl border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-orange-500 focus:ring-orange-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:border-orange-500"
                                       placeholder="Contoh: 15000" min="1" required>
                                @error('expenseAmount') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Keterangan</label>
                                <input type="text" wire:model="expenseDescription"
                                       class="block w-full rounded-xl border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-orange-500 focus:ring-orange-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:border-orange-500"
                                       placeholder="Contoh: Beli air galon" required>
                                @error('expenseDescription') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-slate-50 px-4 py-3 dark:bg-slate-800/50 sm:flex sm:flex-row-reverse sm:px-6">
                <button type="submit" wire:loading.attr="disabled"
                        class="inline-flex w-full justify-center rounded-xl bg-orange-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-orange-500 sm:ml-3 sm:w-auto disabled:opacity-75">
                    <span wire:loading.remove wire:target="saveExpense">Simpan Pengeluaran</span>
                    <span wire:loading wire:target="saveExpense">Memproses...</span>
                </button>
                <button type="button" @click="isShiftExpenseModalOpen = false"
                        class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-3 py-2 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-300 dark:ring-slate-700 dark:hover:bg-slate-700 sm:mt-0 sm:w-auto">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>
