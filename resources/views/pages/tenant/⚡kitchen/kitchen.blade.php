@if($this->kitchenDisabled)
    <div class="fixed inset-0 z-50 flex flex-col items-center justify-center bg-slate-50 dark:bg-[#0B1120] p-6 text-slate-800 dark:text-slate-200">
        <div class="mx-auto max-w-md text-center">
            <div class="mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800 mx-auto">
                <svg class="h-10 w-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-black text-slate-900 dark:text-white mb-2">Kitchen Display Tidak Aktif</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">Fitur layar dapur (KDS) sedang dinonaktifkan. Aktifkan melalui Pengaturan Toko untuk mengelola pesanan dapur.</p>
            @if(auth()->user()->role !== 'kitchen')
                <a href="{{ route('store-setting') }}" wire:navigate.hover class="inline-flex items-center gap-2 rounded-xl bg-amber-500 px-5 py-3 text-sm font-bold text-white shadow-lg transition-colors hover:bg-amber-600">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Buka Pengaturan Toko
                </a>
            @else
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-xl bg-slate-800 px-5 py-3 text-sm font-bold text-white shadow-lg transition-colors hover:bg-slate-700">
                    Kembali ke Login
                </a>
            @endif
        </div>
    </div>
@else
<div class="fixed inset-0 z-50 flex flex-col bg-slate-50 dark:bg-[#0B1120] p-3 text-slate-800 dark:text-slate-200 md:p-4 gap-3 overflow-hidden" x-data="{ stats: @js($this->kitchenStats), activeTab: 'waiting' }">

    {{-- Header --}}
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between shrink-0">
        <div>
            <h2 class="flex items-center gap-2 text-lg font-black text-slate-900 dark:text-white">
                <svg class="h-5 w-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                Kitchen Queue
            </h2>
            <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Real-time operations dashboard</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <span class="inline-flex items-center gap-2 rounded-full border border-emerald-500/30 bg-emerald-500/10 px-3 py-1 text-xs font-bold text-emerald-600 dark:text-emerald-400">
                <span class="relative flex h-2 w-2">
                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
                </span>
                Dapur Buka
            </span>
            <button x-data="{ theme: localStorage.getItem('theme') || 'light' }"
                    @click="theme = theme === 'dark' ? 'light' : 'dark'; localStorage.setItem('theme', theme); document.documentElement.classList.toggle('dark', theme === 'dark')"
                    class="flex items-center justify-center p-1.5 rounded-lg bg-white border border-slate-200 dark:border-slate-800 dark:bg-slate-800 text-slate-600 dark:text-slate-300 transition-colors hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-slate-900 dark:hover:text-white shadow-sm"
                    title="Ganti Tema">
                <i x-show="theme === 'dark'" class="ph-fill ph-sun text-[18px] text-orange-400" x-cloak></i>
                <i x-show="theme === 'light'" class="ph-fill ph-moon text-[18px] text-slate-500" x-cloak></i>
            </button>
            <button wire:click="$refresh" wire:loading.attr="disabled" class="flex items-center gap-2 rounded-lg bg-white border border-slate-200 dark:border-slate-800 dark:bg-slate-800 px-3 py-1.5 text-xs font-medium text-slate-600 dark:text-slate-300 transition-colors hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-slate-900 dark:hover:text-white shadow-sm" title="Refresh Data">
                <svg wire:loading.class="animate-spin" wire:target="$refresh" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Refresh
            </button>
            @if(auth()->user()->role === 'kitchen')
                <button wire:click="logout" class="flex items-center gap-2 rounded-lg border border-red-500/30 bg-red-500/10 px-3 py-1.5 text-xs font-medium text-red-500 transition-colors hover:bg-red-50 hover:text-white">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    Keluar
                </button>
            @else
                <a href="{{ route('dashboard') }}" wire:navigate.hover class="flex items-center gap-2 rounded-lg bg-white border border-slate-200 dark:border-slate-800 dark:bg-slate-800 px-3 py-1.5 text-xs font-medium text-slate-600 dark:text-slate-300 transition-colors hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-slate-900 dark:hover:text-white shadow-sm">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
            @endif
        </div>
    </div>

    {{-- Metrics Summary Cards --}}
    <div class="grid grid-cols-2 gap-3 md:grid-cols-4 shrink-0">
        {{-- Active Orders --}}
        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/50 p-3 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-2xl font-black text-slate-900 dark:text-white">{{ $this->kitchenStats['active'] }}</span>
                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-800 text-amber-500">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </span>
            </div>
            <div class="mt-2 flex items-center gap-2">
                <span class="text-xs font-bold text-slate-500">Active Orders</span>
                <span class="rounded-full bg-slate-100 dark:bg-slate-800 px-2 py-0.5 text-[10px] font-bold text-slate-600 dark:text-slate-400">Today</span>
            </div>
        </div>

        {{-- Avg Prep Time --}}
        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/50 p-3 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-2xl font-black text-slate-900 dark:text-white">{{ $this->kitchenStats['avg_prep'] }}m</span>
                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-800 text-sky-500">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </span>
            </div>
            <div class="mt-2 flex items-center gap-2">
                <span class="text-xs font-bold text-slate-500">Prep Time</span>
                <span class="rounded-full bg-slate-100 dark:bg-slate-800 px-2 py-0.5 text-[10px] font-bold text-slate-600 dark:text-slate-400">Avg</span>
            </div>
        </div>

        {{-- Pending --}}
        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/50 p-3 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-2xl font-black text-red-500">{{ $this->kitchenStats['pending'] }}</span>
                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-red-500/10 text-red-500">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </span>
            </div>
            <div class="mt-2 flex items-center gap-2">
                <span class="text-xs font-bold text-slate-500">Pending</span>
                <span class="rounded-full bg-slate-100 dark:bg-slate-800 px-2 py-0.5 text-[10px] font-bold text-slate-600 dark:text-slate-400">Waiting</span>
            </div>
        </div>

        {{-- Ready --}}
        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/50 p-3 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-2xl font-black text-emerald-500">{{ $this->kitchenStats['ready'] }}</span>
                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-500">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                </span>
            </div>
            <div class="mt-2 flex items-center gap-2">
                <span class="text-xs font-bold text-slate-500">Ready</span>
                <span class="rounded-full bg-slate-100 dark:bg-slate-800 px-2 py-0.5 text-[10px] font-bold text-slate-600 dark:text-slate-400">Done</span>
            </div>
        </div>
    </div>

    {{-- Mobile Tab Bar --}}
    <div class="mb-3 flex gap-2 md:hidden">
        <button type="button"
                x-on:click="activeTab = 'waiting'"
                :class="activeTab === 'waiting' ? 'border-red-500 bg-red-500/10 text-red-600 dark:text-red-500' : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-500'"
                class="flex flex-1 items-center justify-center gap-1.5 rounded-xl border-2 px-2 py-2 text-xs font-black transition-colors">
            <span>Pending</span>
            <span class="flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500/15 px-1.5 text-[10px] font-black text-red-600 dark:text-red-400">{{ collect($this->kitchenBatches)->where('status', 'waiting')->count() }}</span>
        </button>

        <button type="button"
                x-on:click="activeTab = 'processing'"
                :class="activeTab === 'processing' ? 'border-amber-500 bg-amber-500/10 text-amber-600 dark:text-amber-400' : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-500'"
                class="flex flex-1 items-center justify-center gap-1.5 rounded-xl border-2 px-2 py-2 text-xs font-black transition-colors">
            <span>Cooking</span>
            <span class="flex h-5 min-w-5 items-center justify-center rounded-full bg-amber-500/15 px-1.5 text-[10px] font-black text-amber-600 dark:text-amber-400">{{ collect($this->kitchenBatches)->where('status', 'processing')->count() }}</span>
        </button>

        <button type="button"
                x-on:click="activeTab = 'ready'"
                :class="activeTab === 'ready' ? 'border-emerald-500 bg-emerald-500/10 text-emerald-600 dark:text-emerald-500' : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-500'"
                class="flex flex-1 items-center justify-center gap-1.5 rounded-xl border-2 px-2 py-2 text-xs font-black transition-colors">
            <span>Ready</span>
            <span class="flex h-5 min-w-5 items-center justify-center rounded-full bg-emerald-500/15 px-1.5 text-[10px] font-black text-emerald-600 dark:text-emerald-400">{{ collect($this->kitchenBatches)->where('status', 'ready')->count() }}</span>
        </button>
    </div>

    {{-- Kanban Board --}}
    <div class="flex-1 min-h-0 flex gap-4">
        @php
            $waitingBatches = collect($this->kitchenBatches)->where('status', 'waiting');
            $processingBatches = collect($this->kitchenBatches)->where('status', 'processing');
            $readyBatches = collect($this->kitchenBatches)->where('status', 'ready');
        @endphp

        {{-- Kolom Pending --}}
        <div :class="activeTab === 'waiting' ? 'flex' : 'hidden md:flex'" class="w-full md:w-1/3 min-h-0 flex-col rounded-2xl border border-red-500/10 bg-red-50/50 dark:border-red-500/20 dark:bg-red-950/10 p-3 shadow-sm overflow-hidden">
            <div class="mb-3 flex items-center justify-between shrink-0">
                <h3 class="flex items-center gap-2 text-sm font-black text-red-600 dark:text-red-400">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-red-500/10 dark:bg-red-500/20 text-xs">{{ $waitingBatches->count() }}</span>
                    Pending
                </h3>
                <span class="text-[10px] font-bold uppercase tracking-wider text-red-500/70 dark:text-red-400/70">Menunggu</span>
            </div>

            <div class="flex-1 min-h-0 overflow-y-auto pr-1 pb-3 flex flex-col gap-3">
                @forelse($waitingBatches as $batch)
                    @include('pages.tenant.⚡kitchen.partials.kitchen-card', ['batch' => $batch])
                @empty
                    <div class="flex flex-1 flex-col items-center justify-center py-12 text-center">
                        <svg class="mb-3 h-10 w-10 text-red-500/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-sm font-medium text-slate-400 dark:text-slate-500">Antrean kosong</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Kolom Cooking --}}
        <div :class="activeTab === 'processing' ? 'flex' : 'hidden md:flex'" class="w-full md:w-1/3 min-h-0 flex-col rounded-2xl border border-amber-500/20 bg-amber-50/50 dark:border-amber-500/30 dark:bg-amber-950/10 p-3 shadow-lg shadow-amber-500/5 dark:shadow-amber-500/10 overflow-hidden">
            <div class="mb-3 flex items-center justify-between shrink-0">
                <h3 class="flex items-center gap-2 text-sm font-black text-amber-600 dark:text-amber-400">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-amber-500/10 dark:bg-amber-500/20 text-xs">{{ $processingBatches->count() }}</span>
                    Cooking
                </h3>
                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-500/70 dark:text-amber-400/70">Memasak</span>
            </div>

            <div class="flex-1 min-h-0 overflow-y-auto pr-1 pb-3 flex flex-col gap-3">
                @forelse($processingBatches as $batch)
                    @include('pages.tenant.⚡kitchen.partials.kitchen-card', ['batch' => $batch])
                @empty
                    <div class="flex flex-1 flex-col items-center justify-center py-12 text-center">
                        <svg class="mb-3 h-10 w-10 text-amber-500/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zM1.5 9a2.25 2.25 0 113 0 2.25 2.25 0 01-3 0z"></path></svg>
                        <p class="text-sm font-medium text-slate-400 dark:text-slate-500">Tidak ada yang dimasak</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Kolom Ready --}}
        <div :class="activeTab === 'ready' ? 'flex' : 'hidden md:flex'" class="w-full md:w-1/3 min-h-0 flex-col rounded-2xl border border-emerald-500/10 bg-emerald-50/50 dark:border-emerald-500/20 dark:bg-emerald-950/10 p-3 shadow-sm overflow-hidden">
            <div class="mb-3 flex items-center justify-between shrink-0">
                <h3 class="flex items-center gap-2 text-sm font-black text-emerald-600 dark:text-emerald-400">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-500/10 dark:bg-emerald-500/20 text-xs">{{ $readyBatches->count() }}</span>
                    Ready
                </h3>
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-500/70 dark:text-emerald-400/70">Selesai</span>
            </div>

            <div class="flex-1 min-h-0 overflow-y-auto pr-1 pb-3 flex flex-col gap-3">
                @forelse($readyBatches as $batch)
                    @include('pages.tenant.⚡kitchen.partials.kitchen-card', ['batch' => $batch])
                @empty
                    <div class="flex flex-1 flex-col items-center justify-center py-12 text-center">
                        <svg class="mb-3 h-10 w-10 text-emerald-500/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"></path></svg>
                        <p class="text-sm font-medium text-slate-400 dark:text-slate-500">Belum ada yang siap</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>

@endif

@script
{{-- Audio System Notification --}}
<script>
    document.addEventListener('livewire:initialized', () => {
        let previousCount = 0;
        Livewire.hook('commit', ({component, commit, respond, succeed, fail}) => {
            succeed(({snapshot, effect}) => {
                // Adjust to the new computed property reference ($this->kitchenBatches)
                if (snapshot.data.kitchenBatches) {
                    const currentCount = Object.keys(snapshot.data.kitchenBatches).length;
                    if (currentCount > previousCount && previousCount !== 0) {
                        try {
                            const ctx = new (window.AudioContext || window.webkitAudioContext)();
                            const osc = ctx.createOscillator();
                            const gain = ctx.createGain();
                            osc.type = 'triangle';
                            osc.frequency.setValueAtTime(600, ctx.currentTime);
                            osc.frequency.exponentialRampToValueAtTime(800, ctx.currentTime + 0.1);
                            gain.gain.setValueAtTime(0.2, ctx.currentTime);
                            gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.4);
                            osc.connect(gain);
                            gain.connect(ctx.destination);
                            osc.start();
                            osc.stop(ctx.currentTime + 0.4);
                        } catch (e) {
                            console.error('Audio playback failed', e);
                        }
                    }
                    previousCount = currentCount;
                }
            });
        });
    });
</script>
@endscript
