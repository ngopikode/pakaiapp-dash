@php
    $order = $batch['order'];
    $status = $batch['status']; // waiting | processing | ready
    $isReady = $status === 'ready';
    $isProcessing = $status === 'processing';
    $isWaiting = $status === 'waiting';

    // Color config per status
    $accent = $isReady ? 'emerald' : ($isProcessing ? 'amber' : 'red');
    $headerBg = $isReady ? 'bg-emerald-600' : ($isProcessing ? 'bg-amber-500' : 'bg-red-600');
    $headerText = $isReady ? 'text-white' : ($isProcessing ? 'text-slate-900' : 'text-white');
    $cardBorder = $isReady ? 'border-emerald-500/30' : ($isProcessing ? 'border-amber-500/30' : 'border-red-500/30');
    $badgeBg = 'bg-black/20';
    $badgeText = $isReady ? 'text-white' : ($isProcessing ? 'text-slate-900' : 'text-white');
@endphp

<div class="relative flex flex-col overflow-hidden rounded-xl border {{ $cardBorder }} bg-white dark:bg-slate-900 shadow-sm border-slate-200 dark:border-slate-800 transition-all">
    {{-- Card Header --}}
    <div class="flex items-center justify-between p-3 {{ $headerBg }} {{ $headerText }}">
        <div>
            <h5 class="text-base font-black">#{{ str_replace('INV-', '', $order->invoice_code) }}{{ $isWaiting && $order->items->where('kitchen_status', '!=', 'waiting')->isNotEmpty() ? ' (Tambahan)' : '' }}</h5>
            <small class="mt-0.5 flex items-center gap-1 text-xs font-bold {{ $isReady ? 'text-emerald-100' : ($isProcessing ? 'text-amber-900/80' : 'text-red-100') }}"
                   x-data="{
                       timeAgo: '',
                       start: new Date('{{ \Carbon\Carbon::parse($batch['created_at'])->toIso8601String() }}'),
                       updateTime() {
                           let diff = Math.floor((new Date() - this.start) / 1000);
                           if (diff < 60) {
                               this.timeAgo = diff + 'd lalu';
                           } else if (diff < 3600) {
                               let m = Math.floor(diff / 60);
                               this.timeAgo = m + 'm lalu';
                           } else {
                               let h = Math.floor(diff / 3600);
                               let m = Math.floor((diff % 3600) / 60);
                               this.timeAgo = h + 'j ' + m + 'm lalu';
                           }
                       }
                   }"
                   x-init="updateTime(); setInterval(() => updateTime(), 60000)">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ \Carbon\Carbon::parse($batch['created_at'])->format('H:i') }}
                (<span class="tabular-nums" x-text="timeAgo">{{ \Carbon\Carbon::parse($batch['created_at'])->diffForHumans() }}</span>)
            </small>
        </div>
        <div class="text-right">
            @if($order->order_type === 'dinein')
                <span class="inline-flex items-center gap-1 rounded-full {{ $badgeBg }} px-2 py-0.5 text-[11px] font-bold {{ $badgeText }}">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    Meja {{ $order->table_number ?? '-' }}
                </span>
            @elseif($order->order_type === 'takeaway')
                <span class="inline-flex items-center gap-1 rounded-full {{ $badgeBg }} px-2 py-0.5 text-[11px] font-bold {{ $badgeText }}">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    Bungkus
                </span>
            @elseif($order->order_type === 'online')
                <span class="inline-flex items-center gap-1 rounded-full {{ $badgeBg }} px-2 py-0.5 text-[11px] font-bold {{ $badgeText }}">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Online
                </span>
            @else
                <span class="inline-flex items-center gap-1 rounded-full {{ $badgeBg }} px-2 py-0.5 text-[11px] font-bold {{ $badgeText }}">
                    {{ ucfirst($order->order_type) }}
                </span>
            @endif
        </div>
    </div>

    {{-- Progress Bar (only for cooking) --}}
    @if($isProcessing)
        <div class="h-1.5 w-full bg-slate-200 dark:bg-slate-800"
             x-data="{ progress: 0, start: new Date('{{ \Carbon\Carbon::parse($batch['created_at'])->toIso8601String() }}') }"
             x-init="setInterval(() => {
                 let elapsed = Math.floor((new Date() - this.start) / 1000 / 60);
                 this.progress = Math.min(Math.floor((elapsed / 20) * 100), 100);
             }, 1000)">
            <div class="h-full bg-amber-500 transition-all duration-1000"
                 :style="'width: ' + progress + '%'"></div>
        </div>
    @endif

    {{-- Card Body --}}
    <div class="flex-1 p-3">
        @if($order->notes)
            <div class="mb-2 flex items-start gap-1.5 rounded-lg border border-red-500/20 bg-red-50/50 dark:bg-red-500/10 p-2 text-xs text-red-600 dark:text-red-500">
                <svg class="mt-0.5 h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <div><strong class="font-bold">CATATAN:</strong> {{ $order->notes }}</div>
            </div>
        @endif

        <ul class="flex flex-col gap-1.5">
            @foreach($batch['items'] as $item)
                <li class="flex items-center justify-between gap-2 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/30 p-2">
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ $item->product_name }}</div>
                        @if($item->variant_name)
                            <div class="flex items-center gap-1 text-[11px] font-semibold text-slate-500 dark:text-slate-400">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                {{ $item->variant_name }}
                            </div>
                        @endif
                        @if($item->note)
                            <div class="flex items-start gap-1 text-[11px] font-semibold text-amber-600 dark:text-amber-400">
                                <svg class="mt-0.5 h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                                "{{ $item->note }}"
                            </div>
                        @endif
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-200 dark:bg-slate-700 text-sm font-black text-slate-900 dark:text-white">x{{ $item->quantity }}</span>

                        @if($isWaiting)
                            <button wire:click="markItemAsProcessing({{ $item->id }})" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-red-500 text-white shadow-sm transition-transform hover:scale-105 hover:bg-red-600" title="Mulai Masak">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path></svg>
                            </button>
                        @elseif($isProcessing)
                            <button wire:click="markItemAsReady({{ $item->id }})" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-500 text-white shadow-sm transition-transform hover:scale-105 hover:bg-emerald-600" title="Siap">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </button>
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>
    </div>

    {{-- Card Footer --}}
    @if(!$isReady)
        <div class="border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/30 p-3">
            @if($isWaiting)
                <button wire:click="markAsProcessing({{ $order->id }})" class="flex w-full items-center justify-center gap-2 rounded-lg border-2 border-red-500/30 bg-red-50 dark:bg-red-500/10 py-2.5 text-sm font-bold text-red-600 dark:text-red-500 transition-colors hover:bg-red-500 hover:text-white">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    MULAI MASAK
                </button>
            @elseif($isProcessing)
                <button wire:click="markAsReady({{ $order->id }})" class="flex w-full items-center justify-center gap-2 rounded-lg border-2 border-emerald-500/30 bg-emerald-50 dark:bg-emerald-500/10 py-2.5 text-sm font-bold text-emerald-600 dark:text-emerald-500 transition-colors hover:bg-emerald-500 hover:text-white">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    SEMUA SIAP
                </button>
            @endif
        </div>
    @else
        <div class="border-t border-slate-200 dark:border-slate-800 bg-emerald-50 dark:bg-emerald-500/5 p-3">
            <div class="flex items-center justify-center gap-1.5 rounded-lg py-1.5 text-xs font-bold text-emerald-600 dark:text-emerald-500">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                Selesai — Siap Disajikan
            </div>
        </div>
    @endif
</div>
