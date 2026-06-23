@props([
    'route',        // route name
    'icon',         // icon class
    'label',        // text label
    'activeRoute'   // current active route
])

@php
    $isActive = ($activeRoute === $route) || (str_starts_with($activeRoute, $route . '.'));
@endphp

<a href="{{ route($route) }}" wire:navigate.hover
   class="list-group-item list-group-item-action {{ $isActive ? 'active' : '' }} d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-3 min-w-0">
        <i class="{{ $icon }} flex-shrink-0"></i>
        <span class="text-truncate">{!! $label !!}</span>
    </div>
    @if($isActive)
        <span class="active-indicator-dot"></span>
    @endif
</a>
