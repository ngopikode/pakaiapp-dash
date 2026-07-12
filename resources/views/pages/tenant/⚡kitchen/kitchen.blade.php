<div class="min-h-screen bg-slate-950 p-4 font-sans text-slate-200 md:p-6 lg:p-8">

    {{-- Header --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="flex items-center gap-2 text-2xl font-bold text-white">
                <svg class="h-6 w-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                Kitchen Display
            </h2>
            <p class="mt-1 text-sm text-slate-400">Monitor Pesanan Dapur secara Real-time</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <button wire:click="$refresh" wire:loading.attr="disabled" class="flex items-center gap-2 rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-slate-300 transition-colors hover:bg-slate-700 hover:text-white" title="Refresh Data">
                <svg wire:loading.class="animate-spin" wire:target="$refresh" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Refresh
            </button>
            <div class="flex items-center gap-2 rounded-lg border border-slate-700 bg-slate-800/50 px-4 py-2">
                <span class="relative flex h-2.5 w-2.5">
                  <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                  <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                </span>
                <span class="text-sm font-medium text-slate-300">Live Sync</span>
            </div>
            @if(auth()->user()->role === 'kitchen')
                <button wire:click="logout" class="flex items-center gap-2 rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-2 text-sm font-medium text-red-500 transition-colors hover:bg-red-500 hover:text-white">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    Keluar
                </button>
            @else
                <a href="{{ route('dashboard') }}" wire:navigate.hover class="flex items-center gap-2 rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-slate-300 transition-colors hover:bg-slate-700 hover:text-white">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
            @endif
        </div>
    </div>

    {{-- Grid Pesanan --}}
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @forelse($this->kitchenBatches as $batch)
            @php $order = $batch['order']; @endphp
            <div class="relative flex flex-col overflow-hidden rounded-2xl border {{ $batch['status'] === 'processing' ? 'border-emerald-500 shadow-[0_0_15px_rgba(16,185,129,0.15)]' : 'border-amber-500 shadow-[0_0_15px_rgba(245,158,11,0.15)]' }} bg-slate-900 transition-all">
                
                {{-- Card Header Solid --}}
                <div class="flex items-center justify-between p-4 {{ $batch['status'] === 'processing' ? 'bg-emerald-600 text-white' : 'bg-amber-500 text-slate-900' }}">
                    <div>
                        <h5 class="text-2xl font-black">#{{ str_replace('INV-', '', $order->invoice_code) }}{{ $batch['status'] === 'waiting' && $order->items->where('kitchen_status', '!=', 'waiting')->isNotEmpty() ? ' (Tambahan)' : '' }}</h5>
                        <small class="mt-1 flex items-center gap-1 font-bold {{ $batch['status'] === 'processing' ? 'text-emerald-100' : 'text-amber-900/80' }}"
                               x-data="{ 
                                   timeAgo: '', 
                                   start: new Date('{{ \Carbon\Carbon::parse($batch['created_at'])->toIso8601String() }}'),
                                   updateTime() {
                                       let diff = Math.floor((new Date() - this.start) / 1000);
                                       if (diff < 60) {
                                           this.timeAgo = diff + ' detik lalu';
                                       } else if (diff < 3600) {
                                           let m = Math.floor(diff / 60);
                                           let s = diff % 60;
                                           this.timeAgo = m + 'm ' + s + 's lalu';
                                       } else {
                                           let h = Math.floor(diff / 3600);
                                           let m = Math.floor((diff % 3600) / 60);
                                           this.timeAgo = h + 'j ' + m + 'm lalu';
                                       }
                                   }
                               }"
                               x-init="updateTime(); setInterval(() => updateTime(), 1000)">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ \Carbon\Carbon::parse($batch['created_at'])->format('H:i') }}
                            (<span class="tabular-nums" x-text="timeAgo">{{ \Carbon\Carbon::parse($batch['created_at'])->diffForHumans() }}</span>)
                        </small>
                    </div>
                    <div class="text-right">
                        @if($order->order_type === 'dinein')
                            <span class="inline-flex items-center gap-1 rounded-full bg-black/20 px-3 py-1.5 text-xs font-bold {{ $batch['status'] === 'processing' ? 'text-white' : 'text-slate-900' }}">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                Meja {{ $order->table_number ?? '-' }}
                            </span>
                        @elseif($order->order_type === 'takeaway')
                            <span class="inline-flex items-center gap-1 rounded-full bg-black/20 px-3 py-1.5 text-xs font-bold {{ $batch['status'] === 'processing' ? 'text-white' : 'text-slate-900' }}">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                Bungkus
                            </span>
                        @elseif($order->order_type === 'online')
                            <span class="inline-flex items-center gap-1 rounded-full bg-black/20 px-3 py-1.5 text-xs font-bold {{ $batch['status'] === 'processing' ? 'text-white' : 'text-slate-900' }}">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Online
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-full bg-black/20 px-3 py-1.5 text-xs font-bold {{ $batch['status'] === 'processing' ? 'text-white' : 'text-slate-900' }}">
                                {{ ucfirst($order->order_type) }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Card Body --}}
                <div class="flex-1 p-4">
                    @if($order->notes)
                        <div class="mb-4 flex items-start gap-2 rounded-xl border border-red-500/20 bg-red-500/10 p-3 text-sm text-red-500">
                            <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            <div><strong class="font-bold">CATATAN:</strong> {{ $order->notes }}</div>
                        </div>
                    @endif

                    <ul class="flex flex-col gap-3">
                        @foreach($batch['items'] as $item)
                            <li class="flex items-start justify-between gap-3 rounded-xl border border-slate-800 bg-slate-800/30 p-3">
                                <div class="flex-1">
                                    <div class="text-xl font-bold text-white">{{ $item->product_name }}</div>
                                    @if($item->variant_name)
                                        <div class="mt-1 flex items-center gap-1 text-sm font-semibold text-slate-400">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                            {{ $item->variant_name }}
                                        </div>
                                    @endif
                                    @if($item->note)
                                        <div class="mt-1 flex items-start gap-1 text-sm font-semibold text-amber-400">
                                            <svg class="mt-0.5 h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                                            "{{ $item->note }}"
                                        </div>
                                    @endif
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-slate-700 text-3xl font-black text-white shadow-inner">x{{ $item->quantity }}</span>
                                    
                                    @if($batch['status'] === 'waiting')
                                        <button wire:click="markItemAsProcessing({{ $item->id }})" class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-amber-500 text-white shadow-lg transition-transform hover:scale-105 hover:bg-amber-600" title="Mulai Masak Item Ini">
                                            <svg class="h-8 w-8" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path></svg>
                                        </button>
                                    @elseif($batch['status'] === 'processing')
                                        <button wire:click="markItemAsReady({{ $item->id }})" class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-emerald-500 text-white shadow-lg transition-transform hover:scale-105 hover:bg-emerald-600" title="Item Ini Siap">
                                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        </button>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Card Footer --}}
                <div class="border-t border-slate-800 bg-slate-800/30 p-4">
                    @if($batch['status'] === 'waiting')
                        <button wire:click="markAsProcessing({{ $order->id }})" class="flex w-full items-center justify-center gap-2 rounded-xl border-2 border-amber-500/30 bg-amber-500/10 py-5 text-xl font-bold text-amber-500 transition-colors hover:bg-amber-500 hover:text-white">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            MULAI MASAK SEMUA
                        </button>
                    @elseif($batch['status'] === 'processing')
                        <button wire:click="markAsReady({{ $order->id }})" class="flex w-full items-center justify-center gap-2 rounded-xl border-2 border-emerald-500/30 bg-emerald-500/10 py-5 text-xl font-bold text-emerald-500 transition-colors hover:bg-emerald-500 hover:text-white">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            SEMUA SIAP
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full flex flex-col items-center justify-center py-24 text-center">
                <div class="mb-6 flex h-24 w-24 items-center justify-center rounded-full bg-slate-800/50 text-slate-600">
                    <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 8c0 2.761-2.239 5-5 5H8c-2.761 0-5-2.239-5-5V7c0-2.761 2.239-5 5-5h8c2.761 0 5 2.239 5 5v1zM8 3v4M16 3v4m3 11H5m11 0v4H8v-4"></path></svg>
                </div>
                <h3 class="mb-2 text-2xl font-bold text-white">Dapur Kosong</h3>
                <p class="text-slate-400">Belum ada pesanan masuk. Koki bisa istirahat sejenak!</p>
            </div>
        @endforelse
    </div>

</div>

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
