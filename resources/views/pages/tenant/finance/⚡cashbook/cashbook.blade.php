<div class="m-4 md:m-6">
    <div class="space-y-8 pb-12">
    
    {{-- BARIS 1: TOP ACTION AREA ─────────────────────────────────────────── --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">Buku Kas Operasional</h1>
            <p class="mt-1 text-sm font-medium text-slate-500 dark:text-slate-400">
                Catat pemasukan dan pengeluaran manual di luar transaksi POS.
            </p>
        </div>
        <button x-data @click="$dispatch('open-cashbook-modal')"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition-all hover:bg-slate-800 active:scale-95 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path></svg>
            Catat Transaksi Kas
        </button>
    </div>

    <!-- Metrik Saldo -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($this->wallets as $wallet)
            <div class="rounded-3xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-sm relative overflow-hidden group hover:shadow-md transition-shadow">
                <!-- Decorative Glow Background -->
                <div class="absolute -right-20 -bottom-20 w-48 h-48 {{ $wallet->type === 'cash' ? 'bg-emerald-500/10' : 'bg-blue-500/10' }} rounded-full blur-3xl pointer-events-none transition-all group-hover:scale-150 duration-500"></div>
                
                <div class="relative z-10 flex flex-col justify-between h-full min-h-[120px]">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-2xl {{ $wallet->type === 'cash' ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400' : 'bg-blue-100 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400' }}">
                                @if($wallet->type === 'cash')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                @endif
                            </div>
                            <h3 class="text-sm font-bold text-slate-500 dark:text-slate-400">Saldo {{ $wallet->type === 'cash' ? 'Tunai (Laci)' : 'Rekening Bank' }}</h3>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <span class="text-sm font-bold text-slate-400">Rp</span>
                        <span class="text-3xl font-black tracking-tight text-slate-900 dark:text-white">{{ number_format($wallet->balance, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- BARIS 3: RIWAYAT TRANSAKSI ────────────────────────────────────────── --}}
    <div class="mt-8 rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 overflow-hidden">
        <div class="flex flex-col gap-4 border-b border-slate-200 p-5 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                Riwayat Transaksi
            </h2>
            
            <div class="flex gap-2 p-1 bg-slate-100 dark:bg-slate-800 rounded-xl overflow-x-auto hide-scrollbar w-full md:w-auto">
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

        <div class="overflow-x-hidden md:overflow-x-auto overflow-y-auto max-h-[600px] w-full relative">
            <div wire:loading wire:target="filterWallet"
                 class="absolute inset-0 z-20 bg-white dark:bg-slate-900 flex flex-col p-0 divide-y divide-slate-100 dark:divide-slate-800/80">
                @for($i = 0; $i < 6; $i++)
                    <div class="flex items-center justify-between p-4 border-b border-slate-100 dark:border-slate-800/80 last:border-b-0">
                        <div class="p-0 flex items-start gap-3.5 w-full min-w-0 animate-pulse">
                            <div class="w-12 h-12 rounded-full bg-slate-200 dark:bg-slate-700 shrink-0"></div>
                            <div class="flex-grow flex justify-between items-start min-w-0 gap-3">
                                <div class="min-w-0 pr-2 flex flex-col gap-0.5 flex-1">
                                    <div class="h-4 w-32 max-w-full bg-slate-200 dark:bg-slate-700 rounded"></div>
                                    <div class="h-3.5 w-24 max-w-full bg-slate-100 dark:bg-slate-800/50 rounded"></div>
                                    <div class="h-3 w-28 max-w-full bg-slate-100 dark:bg-slate-800/30 rounded mt-0.5"></div>
                                </div>
                                <div class="shrink-0 ml-auto text-right flex flex-col items-end gap-1 min-w-fit">
                                    <div class="h-4 w-16 sm:w-20 bg-slate-200 dark:bg-slate-700 rounded ml-auto"></div>
                                    <div class="h-4 w-12 bg-slate-100 dark:bg-slate-800 rounded ml-auto"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>

            <table class="w-full text-left border-collapse md:whitespace-nowrap">
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 bg-white dark:bg-slate-900">
                    @island(name: 'cashbook-tx-list', always: true)
                        @placeholder
                            @for($i = 0; $i < 5; $i++)
                                <tr class="flex items-center justify-between p-4 border-b border-slate-100 dark:border-slate-800/80">
                                    <td class="p-0 flex items-start gap-3.5 w-full min-w-0">
                                        <div class="w-12 h-12 rounded-full bg-slate-200 dark:bg-slate-800 animate-pulse shrink-0"></div>
                                        <div class="flex-grow flex justify-between items-start min-w-0 gap-3">
                                            <div class="min-w-0 pr-2 flex flex-col gap-0.5 flex-1">
                                                <div class="h-4 w-32 max-w-full bg-slate-200 dark:bg-slate-800 rounded animate-pulse"></div>
                                                <div class="h-3.5 w-24 max-w-full bg-slate-100 dark:bg-slate-800/50 rounded animate-pulse"></div>
                                                <div class="h-3 w-28 max-w-full bg-slate-100 dark:bg-slate-800/30 rounded animate-pulse mt-0.5"></div>
                                            </div>
                                            <div class="shrink-0 ml-auto text-right flex flex-col items-end gap-1 min-w-fit">
                                                <div class="h-4 w-16 sm:w-20 bg-slate-200 dark:bg-slate-800 rounded animate-pulse ml-auto"></div>
                                                <div class="h-4 w-12 bg-slate-100 dark:bg-slate-800 rounded animate-pulse ml-auto"></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endfor
                        @endplaceholder

                        @php $currentMonth = null; @endphp
                        @forelse($this->histories as $history)
                            @php
                                $txMonth = $history->created_at->translatedFormat('F Y');
                                $parsed = $this->parseTransaction($history);
                                $title = $parsed['title'];
                                $subtitle = $parsed['subtitle'];
                                $iconSvg = $parsed['iconSvg'];
                                $iconBg = $parsed['iconBg'];
                            @endphp

                            @if($txMonth !== $currentMonth)
                                @php $currentMonth = $txMonth; @endphp
                                <tr class="bg-slate-50 dark:bg-slate-800/20 flex w-full">
                                    <td class="px-4 py-2.5 text-sm font-extrabold text-slate-700 dark:text-slate-300 block w-full">
                                        {{ $txMonth }}
                                    </td>
                                </tr>
                            @endif

                            <tr wire:key="history-{{ $history->id }}" class="flex items-center justify-between p-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group border-b border-slate-100 dark:border-slate-800/80">
                                <td class="p-0 flex items-start gap-3.5 w-full min-w-0">
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center shrink-0 bg-[#f15a24] shadow-sm">
                                        {!! $iconSvg !!}
                                    </div>

                                    <div class="flex-grow flex justify-between items-start min-w-0 gap-3">
                                        <div class="min-w-0 pr-2 flex flex-col gap-0.5 flex-1">
                                            <p class="text-sm font-bold text-slate-800 dark:text-slate-200 break-words leading-snug md:truncate">
                                                {{ $title }}
                                            </p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium break-words leading-snug md:truncate" title="{{ $subtitle }}">
                                                {{ $subtitle }}
                                            </p>
                                            <div class="flex items-center gap-1.5 mt-1">
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase whitespace-nowrap {{ $history->wallet->type === 'bank' ? 'bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-500/20' : 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20' }}">
                                                    {{ $history->wallet->type }}
                                                </span>
                                                <p class="text-[11px] text-slate-400 dark:text-slate-500 font-medium whitespace-nowrap">
                                                    {{ $history->created_at->translatedFormat('d F Y H:i') }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="shrink-0 ml-auto text-right flex flex-col items-end gap-1 min-w-fit">
                                            <p class="text-xs sm:text-sm font-bold font-mono whitespace-nowrap {{ $history->type === 'CREDIT' ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-800 dark:text-slate-200' }}">
                                                {{ $history->type === 'CREDIT' ? '+' : '-' }}Rp{{ number_format($history->amount, 0, ',', '.') }}
                                            </p>
                                            <div>
                                                @if($history->type === 'DEBIT')
                                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase whitespace-nowrap bg-orange-50 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400 border border-orange-200 dark:border-orange-500/20">
                                                        Keluar
                                                    </span>
                                                @else
                                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase whitespace-nowrap bg-sky-50 dark:bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-200 dark:border-sky-500/20">
                                                        Masuk
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            {{-- Hanya tampil di page 1 tanpa data --}}
                            @if($this->histories->currentPage() === 1)
                                <tr class="flex md:table-row w-full">
                                    <td colspan="6" class="w-full py-16 text-center block md:table-cell">
                                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full mb-4 bg-slate-50 dark:bg-slate-800 text-slate-400">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </div>
                                        <h4 class="text-base font-bold text-slate-900 dark:text-white">Belum Ada Transaksi</h4>
                                        <p class="text-sm text-slate-500 mt-1">Belum ada transaksi kas untuk kriteria ini.</p>
                                    </td>
                                </tr>
                            @endif
                        @endforelse

                        {{-- Infinite Scroll Trigger --}}
                        @if($this->histories->hasMorePages())
                            <tr class="flex md:table-row w-full"
                                x-data="{ fired: false }"
                                x-show="!fired">
                                <td colspan="6" class="w-full py-4 block md:table-cell">
                                    <div x-intersect.margin.200px="fired = true; $wire.$island('cashbook-tx-list', { mode: 'append' }).nextPage()" class="flex flex-col items-center justify-center gap-2 py-2">
                                        <div class="animate-spin rounded-full h-5 w-5 border-b-2" style="border-color: var(--brand-accent, #10B981);"></div>
                                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Memuat lebih banyak...</span>
                                    </div>
                                </td>
                            </tr>
                        @else
                            @if($this->histories->total() > 0)
                                <tr class="flex md:table-row w-full">
                                    <td colspan="6" class="w-full py-3 text-center block md:table-cell">
                                        <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">— Semua transaksi telah dimuat —</span>
                                    </td>
                                </tr>
                            @endif
                        @endif

                    @endisland
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form Cashbook -->
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
