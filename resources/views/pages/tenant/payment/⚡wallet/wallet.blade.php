<div class="space-y-8 pb-12 m-4 md:m-6">
    
    {{-- BARIS 1: TOP ACTION AREA ─────────────────────────────────────────── --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white">Dashboard Keuangan</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Pantau seluruh saldo deposit, laci kasir, dan pendapatan gateway Anda.</p>
        </div>
        
        <div class="flex items-center gap-3 w-full md:w-auto">
            {{-- Tombol Tambah Saldo Billing --}}
            <button class="flex-1 md:flex-none justify-center px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold text-sm transition-all shadow-sm flex items-center gap-2 cursor-pointer">
                <i class="ph-bold ph-plus text-emerald-500 text-lg"></i>
                Top Up Deposit
            </button>
            {{-- Tombol Pindah ke Buku Kas --}}
            <a href="{{ route('buku-kas') }}" wire:navigate class="flex-1 md:flex-none justify-center px-5 py-2.5 rounded-xl text-white font-bold text-sm transition-all shadow-md flex items-center gap-2 cursor-pointer hover:opacity-90 bg-emerald-600 dark:bg-emerald-600">
                <i class="ph-bold ph-book-open-text text-lg text-white"></i>
                Buka Buku Kas
            </a>
        </div>
    </div>

    {{-- BARIS 2: WALLET CARDS OVERVIEW ───────────────────────────────────── --}}
    @island(name: 'wallet-overview', defer: true)
        @placeholder
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                <div class="h-32 bg-slate-100 dark:bg-slate-800 rounded-3xl animate-pulse"></div>
                <div class="h-32 bg-slate-100 dark:bg-slate-800 rounded-3xl animate-pulse"></div>
                <div class="h-32 bg-slate-100 dark:bg-slate-800 rounded-3xl animate-pulse"></div>
                <div class="h-32 bg-slate-100 dark:bg-slate-800 rounded-3xl animate-pulse"></div>
            </div>
        @endplaceholder
        
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">
            <!-- Billing Wallet -->
            <div class="rounded-[24px] border border-slate-200 bg-gradient-to-br from-white to-slate-50 p-6 shadow-sm dark:border-slate-800 dark:from-slate-900 dark:to-slate-900/50 relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 opacity-5 transition-transform duration-500 group-hover:scale-110">
                    <i class="ph-fill ph-wallet text-[120px]"></i>
                </div>
                <div class="relative z-10 flex items-center gap-4 mb-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-100 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400">
                        <i class="ph-bold ph-wallet text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Deposit Billing</h3>
                        <div class="text-[10px] text-slate-400">Potongan Fee Pakaiapp</div>
                    </div>
                </div>
                <div class="relative z-10">
                    <span class="text-sm font-bold {{ $billingWallet->balance <= 0 ? 'text-rose-400' : 'text-slate-400' }}">Rp</span>
                    <span class="text-3xl font-black tracking-tight {{ $billingWallet->balance <= 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-900 dark:text-white' }}">{{ number_format($billingWallet->balance, 0, ',', '.') }}</span>
                </div>
                @if($billingWallet->balance <= 0)
                <div class="absolute top-0 right-0 w-24 h-24 overflow-hidden">
                    <div class="absolute top-0 right-0 w-full h-full bg-rose-500 transform translate-x-12 -translate-y-12 rotate-45"></div>
                    <i class="ph-bold ph-warning absolute top-3 right-3 text-white"></i>
                </div>
                @endif
            </div>

            <!-- Cash Wallet -->
            <div class="rounded-[24px] border border-slate-200 bg-gradient-to-br from-white to-slate-50 p-6 shadow-sm dark:border-slate-800 dark:from-slate-900 dark:to-slate-900/50 relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 opacity-5 transition-transform duration-500 group-hover:scale-110">
                    <i class="ph-fill ph-money text-[120px]"></i>
                </div>
                <div class="relative z-10 flex items-center gap-4 mb-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
                        <i class="ph-bold ph-money text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Laci Kasir</h3>
                        <div class="text-[10px] text-slate-400">Uang Fisik Toko</div>
                    </div>
                </div>
                <div class="relative z-10">
                    <span class="text-sm font-bold text-slate-400">Rp</span>
                    <span class="text-3xl font-black tracking-tight text-slate-900 dark:text-white">{{ number_format($cashWallet->balance, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Bank Wallet -->
            <div class="rounded-[24px] border border-slate-200 bg-gradient-to-br from-white to-slate-50 p-6 shadow-sm dark:border-slate-800 dark:from-slate-900 dark:to-slate-900/50 relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 opacity-5 transition-transform duration-500 group-hover:scale-110">
                    <i class="ph-fill ph-bank text-[120px]"></i>
                </div>
                <div class="relative z-10 flex items-center gap-4 mb-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                        <i class="ph-bold ph-bank text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Rekening Bank</h3>
                        <div class="text-[10px] text-slate-400">Transfer & QRIS Statis</div>
                    </div>
                </div>
                <div class="relative z-10">
                    <span class="text-sm font-bold text-slate-400">Rp</span>
                    <span class="text-3xl font-black tracking-tight text-slate-900 dark:text-white">{{ number_format($bankWallet->balance, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Gateway Wallet -->
            <div class="rounded-[24px] border border-slate-200 bg-gradient-to-br from-white to-slate-50 p-6 shadow-sm dark:border-slate-800 dark:from-slate-900 dark:to-slate-900/50 relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 opacity-5 transition-transform duration-500 group-hover:scale-110">
                    <i class="ph-fill ph-globe text-[120px]"></i>
                </div>
                <div class="relative z-10 flex items-center gap-4 mb-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400">
                        <i class="ph-bold ph-globe text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Payment Gateway</h3>
                        <div class="text-[10px] text-slate-400">Belum di-settlement</div>
                    </div>
                </div>
                <div class="relative z-10">
                    <span class="text-sm font-bold text-slate-400">Rp</span>
                    <span class="text-3xl font-black tracking-tight text-slate-900 dark:text-white">{{ number_format($gatewayWallet->balance, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    @endisland

    {{-- BARIS 3: RIWAYAT TRANSAKSI ───────────────────────────────────────── --}}
    @island(name: 'wallet-history')
        <div class="rounded-[24px] bg-white border border-slate-200 dark:bg-[#0B1120] dark:border-slate-800 shadow-sm relative overflow-hidden">
            {{-- Toolbar Filter & Search --}}
            <div class="p-4 md:p-6 border-b border-slate-200 dark:border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex gap-2 p-1 bg-slate-100 dark:bg-slate-800 rounded-xl overflow-x-auto hide-scrollbar w-full md:w-auto">
                    <button wire:click="$set('filter', 'all')"
                            class="px-4 py-1.5 rounded-lg text-sm font-bold whitespace-nowrap transition-colors {{ $filter === 'all' ? 'bg-white text-slate-900 shadow-sm dark:bg-slate-700 dark:text-white' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300' }}">
                        Semua
                    </button>
                    <button wire:click="$set('filter', 'billing')"
                            class="px-4 py-1.5 rounded-lg text-sm font-bold whitespace-nowrap transition-colors {{ $filter === 'billing' ? 'bg-white text-orange-600 shadow-sm dark:bg-slate-700 dark:text-orange-400' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300' }}">
                        Billing
                    </button>
                    <button wire:click="$set('filter', 'cash')"
                            class="px-4 py-1.5 rounded-lg text-sm font-bold whitespace-nowrap transition-colors {{ $filter === 'cash' ? 'bg-white text-emerald-600 shadow-sm dark:bg-slate-700 dark:text-emerald-400' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300' }}">
                        Laci Kasir
                    </button>
                    <button wire:click="$set('filter', 'bank')"
                            class="px-4 py-1.5 rounded-lg text-sm font-bold whitespace-nowrap transition-colors {{ $filter === 'bank' ? 'bg-white text-blue-600 shadow-sm dark:bg-slate-700 dark:text-blue-400' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300' }}">
                        Bank
                    </button>
                    <button wire:click="$set('filter', 'gateway')"
                            class="px-4 py-1.5 rounded-lg text-sm font-bold whitespace-nowrap transition-colors {{ $filter === 'gateway' ? 'bg-white text-indigo-600 shadow-sm dark:bg-slate-700 dark:text-indigo-400' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300' }}">
                        Gateway
                    </button>
                </div>
                
                <div class="relative w-full md:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="ph-bold ph-magnifying-glass text-slate-400"></i>
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari transaksi..." class="block w-full pl-10 pr-3 py-2 border border-slate-200 rounded-xl text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:bg-slate-900 dark:border-slate-700 dark:text-white dark:placeholder-slate-500">
                </div>
            </div>

            {{-- Table View (Desktop) & List View (Mobile) --}}
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50/50 dark:border-slate-800 dark:bg-slate-900/50 text-slate-500 dark:text-slate-400">
                            <th class="px-6 py-4 font-bold">Waktu</th>
                            <th class="px-6 py-4 font-bold">Detail Transaksi</th>
                            <th class="px-6 py-4 font-bold text-right cursor-pointer hover:text-slate-700 dark:hover:text-slate-200" wire:click="toggleSort">
                                Nominal & Dompet 
                                <i class="ph-bold {{ $sortOrder === 'asc' ? 'ph-caret-up' : 'ph-caret-down' }} align-middle"></i>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($transactions as $tx)
                            @php 
                                $meta = App\Livewire\Pages\Tenant\Payment\Wallet::parseTransaction($tx); 
                                // Override bg based on wallet type for visual distinction
                                $walletBadgeColor = match($tx->wallet->type) {
                                    'billing' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
                                    'cash' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                    'bank' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                    'gateway' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400',
                                    default => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-400'
                                };
                            @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900 dark:text-white">{{ $tx->created_at->format('d M Y') }}</div>
                                    <div class="text-xs text-slate-500">{{ $tx->created_at->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl {{ $meta['iconBg'] }} flex items-center justify-center shrink-0 shadow-sm opacity-90 group-hover:opacity-100 transition-opacity">
                                            {!! $meta['iconSvg'] !!}
                                        </div>
                                        <div class="max-w-[300px] md:max-w-md lg:max-w-xl">
                                            <div class="font-bold text-slate-900 dark:text-white">{{ $meta['title'] }}</div>
                                            <div class="text-xs text-slate-500 truncate" title="{{ $meta['subtitle'] }}">{{ $meta['subtitle'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="font-black {{ $tx->type === 'CREDIT' ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-900 dark:text-white' }} text-base mb-1">
                                        {{ $tx->type === 'CREDIT' ? '+' : '-' }}Rp {{ number_format($tx->amount, 0, ',', '.') }}
                                    </div>
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold uppercase {{ $walletBadgeColor }}">
                                        {{ $tx->wallet->type }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-16 text-center">
                                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 mb-4">
                                        <i class="ph-bold ph-receipt text-2xl text-slate-400"></i>
                                    </div>
                                    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-1">Tidak ada transaksi</h3>
                                    <p class="text-sm text-slate-500">Belum ada aktivitas di dompet ini untuk filter yang dipilih.</p>
                                    @if($search !== '' || $filter !== 'all')
                                        <button wire:click="clearFilters" class="mt-4 text-emerald-600 font-bold text-sm hover:underline">Hapus Filter</button>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($transactions->hasPages())
                <div class="border-t border-slate-200 dark:border-slate-800 p-4 bg-slate-50/50 dark:bg-slate-900/50">
                    {{ $transactions->links(data: ['scrollTo' => false]) }}
                </div>
            @endif
        </div>
    @endisland

</div>