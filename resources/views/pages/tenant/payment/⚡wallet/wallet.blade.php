<div class="space-y-8 pb-12 m-4 md:m-6">

    {{-- BARIS 1: TOP ACTION AREA ─────────────────────────────────────────── --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white">Dompet Toko</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola saldo dompet dan pantau seluruh transaksi Anda dengan mudah.</p>
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
            {{-- Tombol Sekunder: Tambah Saldo --}}
            <button class="flex-1 md:flex-none justify-center px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold text-sm transition-all shadow-sm flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path>
                </svg>
                Tambah Saldo
            </button>
            {{-- Tombol Primer: Tarik Saldo --}}
            <button class="flex-1 md:flex-none justify-center px-5 py-2.5 rounded-xl text-white font-bold text-sm transition-all shadow-md flex items-center gap-2 cursor-pointer hover:opacity-90 bg-emerald-500 dark:bg-emerald-600">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Tarik Saldo
            </button>
        </div>
    </div>

    {{-- BARIS 2: WALLET OVERVIEW BANNER ──────────────────────────────────── --}}
    @island(name: 'wallet-overview', defer: true)
        @placeholder
            <div class="flex flex-col md:flex-row gap-6">
                <div class="w-full md:w-3/5 rounded-3xl p-6 md:p-8 min-h-[170px] relative overflow-hidden border border-slate-800 shadow-xl bg-gradient-to-br from-slate-900 via-slate-950 to-slate-900 animate-pulse">
                    <div class="absolute inset-0 bg-[linear-gradient(rgba(255,255,255,0.01)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.01)_1px,transparent_1px)] bg-[size:32px_32px] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_100%)]"></div>
                    <div class="absolute -right-20 -bottom-20 w-72 h-72 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="absolute -left-20 -top-20 w-72 h-72 bg-emerald-500/5 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="relative z-10 flex flex-col justify-between h-full min-h-[170px]">
                        <div class="flex justify-between items-start">
                            <div class="space-y-2">
                                <div class="h-2.5 w-28 bg-emerald-400/40 rounded"></div>
                                <div class="h-10 md:h-12 w-48 md:w-56 bg-slate-700 rounded-xl"></div>
                            </div>
                            <div class="w-12 h-9 rounded-lg bg-gradient-to-br from-amber-200 via-yellow-400 to-amber-500 p-[1.5px] shrink-0">
                                <div class="w-full h-full rounded-[6px] bg-slate-950/30"></div>
                            </div>
                        </div>
                        <div class="flex justify-between items-end mt-8 pt-4 border-t border-white/5">
                            <div class="space-y-1.5">
                                <div class="h-2.5 w-20 bg-slate-700 rounded"></div>
                                <div class="h-4 w-32 bg-slate-700 rounded"></div>
                            </div>
                            <div class="space-y-1.5 text-right">
                                <div class="h-2.5 w-20 bg-slate-700 rounded ml-auto"></div>
                                <div class="h-4 w-24 bg-slate-700 rounded ml-auto"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-full md:w-2/5 flex flex-col sm:flex-row gap-4">
                    @for($i = 0; $i < 2; $i++)
                        <div class="flex-1 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 animate-pulse flex flex-col justify-between gap-6">
                            <div class="flex justify-between items-start">
                                <div class="w-12 h-12 rounded-2xl bg-slate-200 dark:bg-slate-700 shadow-sm"></div>
                                <div class="h-6 w-16 rounded-lg bg-slate-200 dark:bg-slate-700"></div>
                            </div>
                            <div class="space-y-2">
                                <div class="h-3 w-20 bg-slate-200 dark:bg-slate-700 rounded"></div>
                                <div class="h-7 w-32 bg-slate-200 dark:bg-slate-700 rounded"></div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        @endplaceholder

        {{-- Actual content with count-up animation --}}
        <div class="flex flex-col md:flex-row gap-6"
             x-data="walletOverview({ balance: {{ (int) $wallet->balance }}, debit: {{ (int) $totalDebit }}, credit: {{ (int) $totalCredit }} })">

            {{-- Bagian Kiri: Virtual Premium Wallet Card --}}
            <div class="w-full md:w-3/5 bg-gradient-to-br from-slate-900 via-slate-950 to-slate-900 text-white rounded-3xl p-6 md:p-8 shadow-xl relative overflow-hidden border border-slate-800 group/vcard transition-all duration-500 hover:shadow-2xl hover:shadow-emerald-500/5 hover:-translate-y-1">
                {{-- Decorative Tech/Grid Mesh --}}
                <div class="absolute inset-0 bg-[linear-gradient(rgba(255,255,255,0.01)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.01)_1px,transparent_1px)] bg-[size:32px_32px] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_100%)]"></div>
                <div class="absolute -right-20 -bottom-20 w-72 h-72 bg-emerald-500/10 rounded-full blur-3xl group-hover/vcard:bg-emerald-500/15 transition-all duration-700 pointer-events-none"></div>
                <div class="absolute -left-20 -top-20 w-72 h-72 bg-emerald-500/5 rounded-full blur-3xl group-hover/vcard:bg-emerald-500/10 transition-all duration-700 pointer-events-none"></div>

                <div class="relative z-10 flex flex-col justify-between h-full min-h-[170px]">
                    {{-- Top Card Row --}}
                    <div class="flex justify-between items-start">
                        <div class="space-y-1">
                            <span class="text-[10px] font-extrabold uppercase tracking-widest text-emerald-400 flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                Current Balance
                            </span>
                            <span class="block text-4xl md:text-5xl font-black tracking-tight font-mono text-white drop-shadow-sm mt-1">
                                Rp <span x-text="displayBalance"></span>
                            </span>
                        </div>
                        {{-- Minimal Premium Gold Chip --}}
                        <div class="w-12 h-9 rounded-lg bg-gradient-to-br from-amber-200 via-yellow-400 to-amber-500 p-[1.5px] shadow-md border border-amber-300/20 shrink-0">
                            <div class="w-full h-full rounded-[6px] bg-slate-950/20 relative overflow-hidden flex items-center justify-center">
                                <div class="absolute inset-1 border border-amber-600/30 rounded grid grid-cols-3 grid-rows-3 gap-0.5 opacity-60">
                                    <div class="border-r border-b border-amber-600/30"></div>
                                    <div class="border-r border-b border-amber-600/30"></div>
                                    <div class="border-b border-amber-600/30"></div>
                                    <div class="border-r border-b border-amber-600/30"></div>
                                    <div class="border-r border-b border-amber-600/30"></div>
                                    <div class="border-b border-amber-600/30"></div>
                                    <div class="border-r border-amber-600/30"></div>
                                    <div class="border-r border-amber-600/30"></div>
                                    <div></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Bottom Card Row --}}
                    <div class="flex justify-between items-end mt-8 pt-4 border-t border-white/5">
                        <div>
                            <p class="text-[9px] font-bold uppercase tracking-wider text-slate-500">Pemilik Akun</p>
                            <p class="text-sm font-semibold text-slate-200 mt-0.5 truncate max-w-[200px]">{{ \Illuminate\Support\Str::title(str_replace('-', ' ', tenant('id'))) }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[9px] font-bold uppercase tracking-wider text-slate-500">Nomor Akun</p>
                            <p class="text-sm font-mono font-bold text-slate-200 mt-0.5">{{ tenant('id') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bagian Kanan: Ringkasan Arus Kas --}}
            <div class="w-full md:w-2/5 flex flex-col sm:flex-row gap-4">

                {{-- Total Keluar --}}
                <div class="group flex-1 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-950 flex flex-col justify-between gap-6 hover:shadow-lg hover:shadow-red-500/5 hover:-translate-y-1 hover:border-red-200 dark:hover:border-red-900/30 transition-all duration-300 cursor-default">
                    <div class="flex justify-between items-start">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 group-hover:scale-110 group-hover:rotate-[-10deg] transition-transform duration-300 shadow-sm" style="background-color: rgba(239, 68, 68, 0.08); color: var(--brand-red, #EF4444);">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25" /></svg>
                        </div>
                        <span class="text-[10px] font-extrabold tracking-widest uppercase text-red-500 bg-red-50 dark:bg-red-500/10 px-2.5 py-1 rounded-lg">Keluar</span>
                    </div>
                    <div>
                        <div class="text-xs font-bold uppercase tracking-wider text-muted-foreground mb-1 group-hover:text-slate-600 dark:group-hover:text-slate-400 transition-colors">Total Keluar</div>
                        <div class="font-extrabold text-2xl font-mono text-foreground">Rp <span x-text="displayDebit"></span></div>
                    </div>
                </div>

                {{-- Total Masuk --}}
                <div class="group flex-1 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-950 flex flex-col justify-between gap-6 hover:shadow-lg hover:shadow-emerald-500/5 hover:-translate-y-1 hover:border-emerald-200 dark:hover:border-emerald-900/30 transition-all duration-300 cursor-default">
                    <div class="flex justify-between items-start">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 group-hover:scale-110 group-hover:rotate-[10deg] transition-transform duration-300 shadow-sm" style="background-color: rgba(16, 185, 129, 0.08); color: var(--brand-accent);">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 4.5l-15 15m0 0h11.25m-11.25 0V8.25" /></svg>
                        </div>
                        <span class="text-[10px] font-extrabold tracking-widest uppercase text-emerald-500 bg-emerald-50 dark:bg-emerald-500/10 px-2.5 py-1 rounded-lg">Masuk</span>
                    </div>
                    <div>
                        <div class="text-xs font-bold uppercase tracking-wider text-muted-foreground mb-1 group-hover:text-slate-600 dark:group-hover:text-slate-400 transition-colors">Total Masuk</div>
                        <div class="font-extrabold text-2xl font-mono" style="color: var(--brand-accent);">Rp <span x-text="displayCredit"></span></div>
                    </div>
                </div>
            </div>
        </div>
    @endisland

    {{-- BARIS 3: SPLIT GRID 65:35 ──────────────────────────────────────── --}}
    <div class="flex flex-col-reverse md:flex-row gap-6 items-start">
        
        {{-- ================= KOLOM KIRI (65%) ================= --}}
        <div class="w-full md:w-[65%] dash-card p-0 flex flex-col overflow-hidden">
            
            {{-- Header Riwayat Transaksi + Filter Buttons --}}
            <div class="p-4 md:p-6 border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 flex flex-wrap items-center gap-3">
                <h3 class="text-lg font-bold text-foreground flex items-center gap-2 mr-auto">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Riwayat Transaksi
                </h3>

                {{-- Filter: Semua / Masuk / Keluar --}}
                <div class="flex items-center gap-1 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-1">
                    <button wire:click="$set('filter', 'all')"
                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer
                               {{ $filter === 'all' ? 'bg-white dark:bg-slate-700 text-emerald-600 dark:text-emerald-400 shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                        Semua
                    </button>
                    <button wire:click="$set('filter', 'credit')"
                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer
                               {{ $filter === 'credit' ? 'bg-white dark:bg-slate-700 text-emerald-600 dark:text-emerald-400 shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                        Masuk
                    </button>
                    <button wire:click="$set('filter', 'debit')"
                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer
                               {{ $filter === 'debit' ? 'bg-white dark:bg-slate-700 text-emerald-600 dark:text-emerald-400 shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                        Keluar
                    </button>
                </div>

                {{-- Tombol Urutkan --}}
                <button wire:click="toggleSort"
                    class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all border cursor-pointer flex items-center gap-1.5
                           {{ $sortOrder === 'asc'
                              ? 'border-emerald-400 dark:border-emerald-600 text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-500/10'
                              : 'border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 bg-white dark:bg-slate-900' }}">
                    <svg class="w-3.5 h-3.5 {{ $sortOrder === 'asc' ? 'rotate-180' : '' }} transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path>
                    </svg>
                    {{ $sortOrder === 'asc' ? 'Terlama' : 'Terbaru' }}
                </button>
            </div>

            {{-- Search Bar --}}
            <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search"
                           class="w-full border border-slate-200 dark:border-slate-700 rounded-xl pl-9 pr-4 py-2.5 text-sm text-foreground focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all bg-white dark:bg-slate-950"
                           placeholder="Cari transaksi...">
                </div>
            </div>

            {{-- Table Container with Max Height, Sticky Header + wire:loading skeleton --}}
            <div class="overflow-x-hidden md:overflow-x-auto overflow-y-auto max-h-[480px] w-full relative">

                <div wire:loading wire:target="filter,sortOrder,toggleSort,search"
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
                            @php $currentMonth = null; @endphp
                            @forelse($this->transactions as $tx)
                                @php
                                    $txMonth = $tx->created_at->translatedFormat('F Y');
                                    $parsed = $this->parseTransaction($tx);
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

                                <tr wire:key="tx-{{ $tx->id }}" class="flex items-center justify-between p-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group border-b border-slate-100 dark:border-slate-800/80">
                                    <td class="p-0 flex items-start gap-3.5 w-full min-w-0">
                                        <div class="w-12 h-12 rounded-full flex items-center justify-center shrink-0 bg-[#f15a24] shadow-sm">
                                            {!! $iconSvg !!}
                                        </div>

                                        <div class="flex-grow flex justify-between items-start min-w-0 gap-3">
                                            <div class="min-w-0 pr-2 flex flex-col gap-0.5 flex-1">
                                                <p class="text-sm font-bold text-slate-800 dark:text-slate-200 break-words leading-snug md:truncate">
                                                    {{ $title }}
                                                </p>
                                                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium break-words leading-snug md:truncate">
                                                    {{ $subtitle }}
                                                </p>
                                                <div class="flex items-center gap-1.5 mt-1">
                                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase whitespace-nowrap {{ $tx->wallet->type === 'gateway' ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/20' : 'bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-500/20' }}">
                                                        {{ $tx->wallet->type }}
                                                    </span>
                                                    <p class="text-[11px] text-slate-400 dark:text-slate-500 font-medium whitespace-nowrap">
                                                        {{ $tx->created_at->translatedFormat('d F Y H:i') }}
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="shrink-0 ml-auto text-right flex flex-col items-end gap-1 min-w-fit">
                                                <p class="text-xs sm:text-sm font-bold font-mono whitespace-nowrap {{ $tx->type === 'CREDIT' ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-800 dark:text-slate-200' }}">
                                                    {{ $tx->type === 'CREDIT' ? '+' : '-' }}Rp{{ number_format($tx->amount, 0, ',', '.') }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                {{-- Hanya tampil di page 1 tanpa data --}}
                                @if($this->transactions->currentPage() === 1)
                                    <tr class="flex md:table-row w-full">
                                        <td colspan="6" class="w-full py-16 text-center block md:table-cell">
                                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full mb-4 bg-slate-50 dark:bg-slate-800 text-muted-foreground">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            </div>
                                            <h4 class="text-base font-bold text-foreground">Belum Ada Transaksi</h4>
                                            <p class="text-sm text-muted-foreground mt-1">Belum ada transaksi yang sesuai dengan kriteria pencarian Anda.</p>
                                        </td>
                                    </tr>
                                @endif
                            @endforelse

                            {{-- Infinite Scroll Trigger --}}
                            @if($this->transactions->hasMorePages())
                                <tr class="flex md:table-row w-full">
                                    <td colspan="6" class="w-full py-4 block md:table-cell">
                                        <div x-intersect.margin.200px="$wire.nextPage()" class="flex flex-col items-center justify-center gap-2 py-2">
                                            <div class="animate-spin rounded-full h-5 w-5 border-b-2" style="border-color: var(--brand-accent);"></div>
                                            <span class="text-xs font-bold text-muted-foreground uppercase tracking-wider">Memuat lebih banyak...</span>
                                        </div>
                                    </td>
                                </tr>
                            @else
                                @if($this->transactions->total() > 0)
                                    <tr class="flex md:table-row w-full">
                                        <td colspan="6" class="w-full py-3 text-center block md:table-cell">
                                            <span class="text-xs font-bold text-muted-foreground uppercase tracking-widest">— Semua transaksi telah dimuat —</span>
                                        </td>
                                    </tr>
                                @endif
                            @endif

                    </tbody>
                </table>
            </div>
        </div>

        {{-- ================= KOLOM KANAN (35%) ================= --}}
        <div class="w-full md:w-[35%] dash-card p-6 flex flex-col bg-card relative overflow-hidden group/chart">
            {{-- Decorative glow --}}
            <div class="absolute inset-0 bg-gradient-to-b from-indigo-500/5 to-transparent opacity-0 group-hover/chart:opacity-100 transition-opacity duration-700 pointer-events-none"></div>

            {{-- Header Analytics --}}
            <div class="flex items-center justify-between mb-8 relative z-10">
                <h3 class="text-lg font-bold text-foreground flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                    Pengeluaran Terkini
                </h3>
                <button class="p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 text-muted-foreground hover:text-foreground transition-colors cursor-pointer focus:ring-2 focus:ring-emerald-500/20 outline-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                </button>
            </div>

            {{-- Visual Chart (Donut Chart) --}}
            <div class="relative w-56 h-56 mx-auto mb-10 flex items-center justify-center">
                {{-- Donut SVG --}}
                <svg viewBox="0 0 100 100" class="w-full h-full transform -rotate-90 drop-shadow-sm">
                    {{-- Base Track --}}
                    <circle cx="50" cy="50" r="40" stroke-width="14" fill="none" class="stroke-slate-100 dark:stroke-slate-800"></circle>

                    {{-- Segment 1 (Sent) --}}
                    <circle cx="50" cy="50" r="40" stroke-width="14" fill="none" stroke-dasharray="251.2" stroke-dashoffset="80" stroke-linecap="round" class="transition-all duration-1000" style="stroke: var(--brand-red, #EF4444);"></circle>

                    {{-- Segment 2 (Received) --}}
                    <circle cx="50" cy="50" r="40" stroke-width="14" fill="none" stroke-dasharray="251.2" stroke-dashoffset="180" stroke-linecap="round" class="transition-all duration-1000 delay-300" style="stroke: var(--brand-accent);"></circle>
                </svg>

                {{-- Center Text Summary --}}
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-[10px] uppercase font-bold text-muted-foreground tracking-widest mb-1">Pekan Ini</span>
                    <span class="text-xl font-black text-foreground font-mono">Rp {{ number_format($totalDebit, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- Chart Legend List --}}
            <div class="flex flex-col gap-3 mt-auto relative z-10">

                {{-- Sent --}}
                <div class="group flex items-center justify-between p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 hover:bg-slate-50 dark:hover:bg-slate-800 hover:border-slate-300 dark:hover:border-slate-700 hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 cursor-pointer">
                    <div class="flex items-center gap-3">
                        <span class="w-3.5 h-3.5 rounded-full shadow-sm group-hover:scale-125 transition-transform duration-300" style="background-color: var(--brand-red, #EF4444);"></span>
                        <span class="text-sm font-bold text-slate-700 dark:text-slate-200 group-hover:text-foreground transition-colors">Terkirim</span>
                    </div>
                    <span class="text-sm font-bold font-mono text-foreground">Rp {{ number_format($totalDebit, 0, ',', '.') }}</span>
                </div>

                {{-- Pending --}}
                <div class="group flex items-center justify-between p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 hover:bg-slate-50 dark:hover:bg-slate-800 hover:border-slate-300 dark:hover:border-slate-700 hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 cursor-pointer">
                    <div class="flex items-center gap-3">
                        <span class="w-3.5 h-3.5 rounded-full shadow-sm bg-amber-400 dark:bg-amber-500 group-hover:scale-125 transition-transform duration-300"></span>
                        <span class="text-sm font-bold text-slate-700 dark:text-slate-200 group-hover:text-foreground transition-colors">Tertunda</span>
                    </div>
                    <span class="text-sm font-bold font-mono text-muted-foreground group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">Rp 0</span>
                </div>

                {{-- Received --}}
                <div class="group flex items-center justify-between p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 hover:bg-slate-50 dark:hover:bg-slate-800 hover:border-slate-300 dark:hover:border-slate-700 hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 cursor-pointer">
                    <div class="flex items-center gap-3">
                        <span class="w-3.5 h-3.5 rounded-full shadow-sm group-hover:scale-125 transition-transform duration-300" style="background-color: var(--brand-accent);"></span>
                        <span class="text-sm font-bold text-slate-700 dark:text-slate-200 group-hover:text-foreground transition-colors">Diterima</span>
                    </div>
                    <span class="text-sm font-bold font-mono text-foreground">Rp {{ number_format($totalCredit, 0, ',', '.') }}</span>
                </div>

            </div>
        </div>

    </div>
    @endisland

</div>

@script
<script>
    Alpine.data('walletOverview', ({ balance, debit, credit }) => ({
        // Raw target values
        _balance: balance,
        _debit:   debit,
        _credit:  credit,

        // Display values (formatted strings)
        displayBalance: '0',
        displayDebit:   '0',
        displayCredit:  '0',

        init() {
            // Small delay so the island transition is visible before counting starts
            setTimeout(() => {
                this._animateCount('displayBalance', this._balance)
                this._animateCount('displayDebit',   this._debit,   100)
                this._animateCount('displayCredit',  this._credit,  200)
            }, 80)
        },

        /**
         * Animate a numeric count-up from 0 → target over ~800ms.
         * @param {string} prop     - reactive property name on this
         * @param {number} target   - final integer value
         * @param {number} delay    - optional stagger delay in ms
         */
        _animateCount(prop, target, delay = 0) {
            if (target === 0) {
                this[prop] = '0'
                return
            }

            const duration   = 850      // ms
            const startTime  = performance.now() + delay
            const self       = this

            const ease = t => t < 0.5
                ? 4 * t * t * t
                : 1 - Math.pow(-2 * t + 2, 3) / 2   // ease-in-out cubic

            const fmt = n => Math.round(n)
                .toString()
                .replace(/\B(?=(\d{3})+(?!\d))/g, '.')

            function tick(now) {
                const elapsed = now - startTime
                if (elapsed < 0) { requestAnimationFrame(tick); return }

                const progress = Math.min(elapsed / duration, 1)
                self[prop] = fmt(ease(progress) * target)

                if (progress < 1) requestAnimationFrame(tick)
                else self[prop] = fmt(target)   // snap to exact on finish
            }

            requestAnimationFrame(tick)
        },
    }))
</script>
@endscript
