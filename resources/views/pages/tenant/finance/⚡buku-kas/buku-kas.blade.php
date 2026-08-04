<div>
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">Buku Kas Operasional</h1>
            <p class="mt-1 text-sm font-medium text-slate-500 dark:text-slate-400">
                Catat pemasukan dan pengeluaran manual di luar transaksi POS.
            </p>
        </div>
        <button x-data @click="$dispatch('open-cashbook-modal')"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition-all hover:bg-slate-800 active:scale-95 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-200">
            <i class="ph-bold ph-plus"></i>
            Catat Transaksi Kas
        </button>
    </div>

    <!-- Metrik Saldo -->
    <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2">
        @foreach($this->wallets as $wallet)
            <div class="rounded-[24px] border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 relative overflow-hidden group">
                <!-- Decorative Icon Background -->
                <div class="absolute -right-4 -top-4 opacity-5 transition-transform duration-500 group-hover:scale-110">
                    <i class="ph-fill {{ $wallet->type === 'cash' ? 'ph-cash-register' : 'ph-bank' }} text-[120px]"></i>
                </div>
                
                <div class="relative z-10 flex items-center gap-4 mb-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl {{ $wallet->type === 'cash' ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/50 dark:text-emerald-400' : 'bg-blue-100 text-blue-600 dark:bg-blue-900/50 dark:text-blue-400' }}">
                        <i class="ph-bold {{ $wallet->type === 'cash' ? 'ph-money' : 'ph-bank' }} text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Saldo {{ $wallet->type === 'cash' ? 'Tunai (Laci Kasir)' : 'Rekening Bank' }}</h3>
                    </div>
                </div>
                <div class="relative z-10">
                    <span class="text-sm font-bold text-slate-400">Rp</span>
                    <span class="text-3xl font-black tracking-tight text-slate-900 dark:text-white">{{ number_format($wallet->balance, 0, ',', '.') }}</span>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Riwayat Transaksi -->
    <div class="rounded-[24px] border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="flex flex-col gap-4 border-b border-slate-200 p-5 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Riwayat Mutasi Kas</h2>
            
            <div class="flex gap-2 p-1 bg-slate-100 dark:bg-slate-800 rounded-xl overflow-x-auto hide-scrollbar">
                <button wire:click="$set('filterWallet', 'all')"
                        class="px-4 py-1.5 rounded-lg text-sm font-bold whitespace-nowrap transition-colors {{ $filterWallet === 'all' ? 'bg-white text-slate-900 shadow-sm dark:bg-slate-700 dark:text-white' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300' }}">
                    Semua
                </button>
                <button wire:click="$set('filterWallet', 'cash')"
                        class="px-4 py-1.5 rounded-lg text-sm font-bold whitespace-nowrap transition-colors {{ $filterWallet === 'cash' ? 'bg-white text-emerald-600 shadow-sm dark:bg-slate-700 dark:text-emerald-400' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300' }}">
                    Tunai (Laci)
                </button>
                <button wire:click="$set('filterWallet', 'bank')"
                        class="px-4 py-1.5 rounded-lg text-sm font-bold whitespace-nowrap transition-colors {{ $filterWallet === 'bank' ? 'bg-white text-blue-600 shadow-sm dark:bg-slate-700 dark:text-blue-400' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300' }}">
                    Rekening Bank
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50/50 dark:border-slate-800 dark:bg-slate-900/50 text-slate-500 dark:text-slate-400">
                        <th class="px-6 py-4 font-bold">Waktu</th>
                        <th class="px-6 py-4 font-bold">Tipe Kas</th>
                        <th class="px-6 py-4 font-bold">Deskripsi</th>
                        <th class="px-6 py-4 font-bold text-right">Nominal</th>
                        <th class="px-6 py-4 font-bold text-right">Sisa Saldo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($this->histories as $history)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-900 dark:text-white">{{ $history->created_at->format('d M Y') }}</div>
                                <div class="text-xs text-slate-500">{{ $history->created_at->format('H:i:s') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-bold {{ $history->wallet->type === 'cash' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' }}">
                                    <i class="ph-fill {{ $history->wallet->type === 'cash' ? 'ph-money' : 'ph-bank' }}"></i>
                                    {{ ucfirst($history->wallet->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="max-w-[300px] truncate text-slate-700 dark:text-slate-300 font-medium" title="{{ $history->description }}">
                                    {{ $history->description }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-black {{ $history->type === 'credit' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                    {{ $history->type === 'credit' ? '+' : '-' }} Rp {{ number_format($history->amount, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-bold text-slate-600 dark:text-slate-400">Rp {{ number_format($history->balance_after, 0, ',', '.') }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                <i class="ph-bold ph-wallet text-4xl mb-3 opacity-50 block"></i>
                                Belum ada riwayat transaksi kas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($this->histories->hasPages())
            <div class="border-t border-slate-200 px-6 py-4 dark:border-slate-800">
                {{ $this->histories->links(data: ['scrollTo' => false]) }}
            </div>
        @endif
    </div>

    <!-- Modal Form Buku Kas -->
    <div x-data="{ 
            isModalOpen: false,
            raw: $wire.entangle('amount'),
            get displayValue() {
                return this.raw ? new Intl.NumberFormat('id-ID').format(this.raw) : '';
            },
            updateValue(val) { 
                this.raw = val.toString().replace(/\D/g, ''); 
            }
         }"
         @open-cashbook-modal.window="isModalOpen = true; this.raw = ''; $wire.set('description', ''); setTimeout(() => $refs.amountInput.focus(), 100)"
         @close-cashbook-modal.window="isModalOpen = false"
         x-show="isModalOpen" x-cloak
         class="fixed inset-0 z-[1050] flex items-center justify-center p-4 sm:p-0">

        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"
             @click="isModalOpen = false"
             x-show="isModalOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"></div>

        <div class="relative w-full max-w-lg transform overflow-hidden rounded-[24px] bg-white text-left align-middle shadow-2xl transition-all dark:bg-slate-900 border border-slate-200 dark:border-slate-800"
             x-show="isModalOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

            <form wire:submit="saveEntry">
                <div class="bg-white px-6 pb-6 pt-6 dark:bg-slate-900 sm:p-8 sm:pb-6">
                    <h3 class="text-xl font-black tracking-tight text-slate-900 dark:text-white mb-6 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                            <i class="ph-bold ph-pencil-simple text-xl"></i>
                        </div>
                        Catat Transaksi Kas
                    </h3>

                    <div class="space-y-5">
                        <!-- Jenis Transaksi (Radio Group) -->
                        <div>
                            <label class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-300">Jenis Transaksi</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="flex cursor-pointer items-center justify-center gap-2 rounded-xl border-2 px-4 py-3 font-bold transition-all"
                                       :class="$wire.type === 'in' ? 'border-emerald-500 bg-emerald-50 text-emerald-700 dark:border-emerald-400 dark:bg-emerald-900/20 dark:text-emerald-400' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-400'">
                                    <input type="radio" wire:model.live="type" value="in" class="sr-only">
                                    <i class="ph-bold ph-arrow-down-left text-lg"></i> Pemasukan
                                </label>
                                <label class="flex cursor-pointer items-center justify-center gap-2 rounded-xl border-2 px-4 py-3 font-bold transition-all"
                                       :class="$wire.type === 'out' ? 'border-rose-500 bg-rose-50 text-rose-700 dark:border-rose-400 dark:bg-rose-900/20 dark:text-rose-400' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-400'">
                                    <input type="radio" wire:model.live="type" value="out" class="sr-only">
                                    <i class="ph-bold ph-arrow-up-right text-lg"></i> Pengeluaran
                                </label>
                            </div>
                        </div>

                        <!-- Sumber / Tujuan Kas -->
                        <div>
                            <label class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-300">Sumber / Tujuan Kas</label>
                            <select wire:model="walletType" 
                                    class="block w-full rounded-xl border-2 border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-900 focus:border-slate-500 focus:ring-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                                <option value="cash">Tunai (Laci Kasir)</option>
                                <option value="bank">Rekening Bank</option>
                            </select>
                        </div>

                        <!-- Nominal Jumbo Input -->
                        <div>
                            <label class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-300">Nominal Uang</label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                    <span class="text-xl font-bold text-slate-400 dark:text-slate-500">Rp</span>
                                </div>
                                <input type="text" inputmode="numeric" 
                                       x-ref="amountInput"
                                       :value="displayValue" 
                                       @input="updateValue($event.target.value)"
                                       class="block w-full rounded-2xl border-2 border-slate-200 bg-slate-50 py-4 pl-12 pr-4 text-right text-3xl font-black text-slate-900 tracking-tight transition-all focus:border-slate-500 focus:bg-white focus:ring-4 focus:ring-slate-500/10 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:border-slate-500 dark:focus:bg-slate-900 placeholder:text-slate-300 dark:placeholder:text-slate-600"
                                       placeholder="0" required autocomplete="off">
                            </div>
                            @error('amount') <span class="mt-1 block text-sm font-medium text-rose-500">{{ $message }}</span> @enderror
                        </div>

                        <!-- Deskripsi -->
                        <div>
                            <label class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-300">Keterangan</label>
                            <input type="text" wire:model="description"
                                   class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 transition-colors focus:border-slate-500 focus:ring-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:border-slate-500 placeholder:text-slate-400"
                                   placeholder="Contoh: Beli sabun pel, Setoran modal..." required autocomplete="off">
                            @error('description') <span class="mt-1 block text-sm font-medium text-rose-500">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="bg-slate-50 px-6 py-5 dark:bg-slate-800/50 sm:flex sm:flex-row-reverse sm:px-8 border-t border-slate-100 dark:border-slate-800">
                    <button type="submit" wire:loading.attr="disabled"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-slate-900 px-6 py-3 text-sm font-bold text-white shadow-sm transition-all hover:bg-slate-800 focus:ring-4 focus:ring-slate-500/20 active:scale-[0.98] dark:bg-white dark:text-slate-900 dark:hover:bg-slate-200 sm:ml-3 sm:w-auto disabled:opacity-75">
                        <span wire:loading.remove wire:target="saveEntry">Simpan Transaksi</span>
                        <span wire:loading wire:target="saveEntry"><i class="ph-bold ph-spinner animate-spin text-lg"></i> Memproses...</span>
                    </button>
                    <button type="button" @click="isModalOpen = false"
                            class="mt-3 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-white px-6 py-3 text-sm font-bold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-300 transition-all hover:bg-slate-50 active:scale-[0.98] dark:bg-slate-800 dark:text-slate-300 dark:ring-slate-700 dark:hover:bg-slate-700 sm:mt-0 sm:w-auto">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
