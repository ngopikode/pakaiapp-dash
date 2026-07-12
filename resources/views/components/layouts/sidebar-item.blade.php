@props([
    'route',        // route name
    'icon',         // icon class
    'label',        // text label
    'activeRoute'   // current active route
])

@php
    $isActive = ($activeRoute === $route) || (str_starts_with($activeRoute, $route . '.'));

    $activeClasses = 'bg-orange-500/10 text-orange-600 dark:bg-orange-500/15 dark:text-orange-400 font-bold';
    $inactiveClasses = 'text-slate-500 dark:text-slate-400 font-medium hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-100/80 dark:hover:bg-slate-800/50';
@endphp

<a href="{{ route($route) }}" wire:navigate.hover
   class="group flex items-center justify-between px-3.5 py-2.5 mb-1 rounded-xl transition-all duration-200 focus:outline-none {{ $isActive ? $activeClasses : $inactiveClasses }}">
    <div class="flex items-center gap-3.5 min-w-0">
        <i class="{{ $icon }} text-[20px] flex-shrink-0 transition-transform duration-200 group-hover:scale-110"></i>
        <span class="truncate text-[13px] tracking-tight">{!! $label !!}</span>
    </div>
    @if($isActive)
        <div class="w-1.5 h-1.5 rounded-full bg-orange-500 shrink-0 shadow-[0_0_8px_rgba(249,115,22,0.6)]"></div>
    @endif
</a>
