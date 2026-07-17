<!-- Mobile Header Layout (< lg) -->
<div class="lg:hidden flex items-center gap-2 mb-6">
    <!-- Search Bar -->
    <div class="relative flex-1">
        <i class="ph-bold ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground"></i>
        <input type="text" wire:model.live.debounce.300ms="queueSearch" wire:island="queue" placeholder="Cari no. invoice / nama..." 
               class="w-full pl-9 pr-4 py-2.5 rounded-full border border-border bg-card text-sm focus:ring-1 focus:ring-primary outline-none text-foreground shadow-sm transition-shadow">
    </div>

    <!-- Filter Dropdown -->
    <div x-data="{ openFilter: false }" class="relative shrink-0">
        <button type="button" @click="openFilter = !openFilter"
            class="w-10 h-10 flex items-center justify-center rounded-full bg-card border border-border text-muted-foreground hover:text-foreground shadow-sm transition-colors cursor-pointer"
            :class="openFilter ? 'ring-2 ring-primary border-transparent' : ''">
            <i class="ph-bold ph-funnel text-lg"></i>
        </button>

        <!-- Dropdown Menu -->
        <div x-show="openFilter" @click.outside="openFilter = false" style="display: none;"
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="absolute right-0 mt-2 w-48 bg-card border border-border rounded-2xl shadow-xl z-50 p-2 space-y-1">
            
            @foreach($filters as $filter)
                <button wire:click="setQueueFilter('{{ $filter['id'] }}')" wire:island="queue" @click="openFilter = false"
                    class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-xl transition-colors"
                    :class="'{{ $queueFilter }}' === '{{ $filter['id'] }}' ? 'bg-primary/10 text-primary' : 'text-foreground hover:bg-accent hover:text-accent-foreground'">
                    <span>{{ $filter['label'] }}</span>
                    <span class="rounded-full min-w-[20px] h-5 px-1.5 flex items-center justify-center text-[10px] font-black"
                          :class="'{{ $queueFilter }}' === '{{ $filter['id'] }}' ? 'bg-primary text-primary-foreground' : 'bg-muted text-muted-foreground'">{{ $counts[$filter['id']] }}</span>
                </button>
            @endforeach
        </div>
    </div>
    
    <!-- Refresh Button -->
    <button type="button" wire:click="$refresh" wire:island="queue"
            class="w-10 h-10 shrink-0 flex items-center justify-center rounded-full bg-primary/10 text-primary hover:bg-primary/20 transition-colors shadow-sm cursor-pointer"
            title="Refresh">
        <i class="ph-bold ph-arrows-clockwise text-lg" wire:loading.class="spin-icon"></i>
    </button>
</div>

<!-- Desktop Header Layout (>= lg) -->
<div class="hidden lg:flex items-center justify-between gap-4 mb-6">

    <!-- Horizontal Filters -->
    <div class="flex items-center gap-2 overflow-x-auto hide-scrollbar pb-2 lg:pb-0 w-full lg:w-auto">
        @foreach($filters as $filter)
            <button type="button" wire:click="setQueueFilter('{{ $filter['id'] }}')" wire:island="queue"
                class="flex items-center gap-2 rounded-full px-4 py-2 text-sm font-medium shadow-sm shrink-0 transition-colors cursor-pointer {{ $queueFilter === $filter['id'] ? 'bg-primary text-primary-foreground' : 'bg-card border border-border text-muted-foreground hover:bg-accent hover:text-foreground' }}">
                {{ $filter['label'] }} <span
                    class="rounded-full min-w-[24px] h-6 px-1.5 flex items-center justify-center text-[11px] font-bold transition-colors {{ $queueFilter === $filter['id'] ? 'bg-primary-foreground text-primary' : 'bg-accent text-foreground' }}">{{ $counts[$filter['id']] }}</span>
            </button>
        @endforeach
    </div>

    <!-- Actions Right -->
    <div class="flex items-center gap-2 shrink-0">
        <!-- Search Input Desktop -->
        <div x-data="{ searchOpen: false }" class="relative flex items-center justify-end h-10" x-init="if ('{{ $queueSearch }}' !== '') searchOpen = true">
            <!-- Search Icon Button -->
            <button type="button" @click="searchOpen = true; $nextTick(() => $refs.searchInput.focus())"
                    x-show="!searchOpen && '{{ $queueSearch }}' === ''"
                    class="w-10 h-10 flex items-center justify-center rounded-full bg-card border border-border text-muted-foreground hover:text-foreground transition-colors shadow-sm cursor-pointer z-10"
                    title="Search">
                <i class="ph-bold ph-magnifying-glass text-lg"></i>
            </button>

            <!-- Search Input Field -->
            <div x-show="searchOpen || '{{ $queueSearch }}' !== ''" 
                 @click.outside="if('{{ $queueSearch }}' === '') searchOpen = false" 
                 style="display: none;"
                 x-transition:enter="transition-all ease-out duration-300 origin-right"
                 x-transition:enter-start="opacity-0 scale-95 w-10"
                 x-transition:enter-end="opacity-100 scale-100 w-64"
                 x-transition:leave="transition-all ease-in duration-200 origin-right"
                 x-transition:leave-start="opacity-100 scale-100 w-64"
                 x-transition:leave-end="opacity-0 scale-95 w-10"
                 class="relative w-64 h-10">
                <i class="ph-bold ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground"></i>
                <input type="text" wire:model.live.debounce.300ms="queueSearch" wire:island="queue" x-ref="searchInput" placeholder="Cari invoice / nama..." 
                       class="w-full h-full pl-9 pr-8 py-2 rounded-full border border-border bg-card text-sm focus:ring-1 focus:ring-primary outline-none text-foreground shadow-sm transition-shadow">
                <button type="button" wire:click="$set('queueSearch', '')" wire:island="queue" @click="searchOpen = false" 
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-destructive">
                    <i class="ph-bold ph-x text-sm"></i>
                </button>
            </div>
        </div>
        
        <!-- Refresh -->
        <button type="button" wire:click="$refresh" wire:island="queue"
                class="w-10 h-10 flex items-center justify-center rounded-full bg-primary/10 text-primary hover:bg-primary/20 transition-colors shadow-sm cursor-pointer"
                title="Refresh">
            <i class="ph-bold ph-arrows-clockwise text-lg" wire:loading.class="spin-icon"></i>
        </button>
    </div>
</div>
