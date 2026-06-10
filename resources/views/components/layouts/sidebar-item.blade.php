@props([
    'route',        // route name
    'icon',         // icon class
    'label',        // text label
    'activeRoute'   // current active route
])

@php
    $isActive = ($activeRoute === $route) || (str_starts_with($activeRoute, $route . '.'));
@endphp

<a href="{{ route($route) }}" wire:navigate onclick="if(!event.ctrlKey && !event.metaKey && !event.shiftKey && event.button !== 1) window.showLoader()"
   class="list-group-item list-group-item-action {{ $isActive ? 'active' : '' }} d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-3">
        <i class="{{ $icon }}"></i>
        <span>{!! $label !!}</span>
    </div>
    @if($isActive)
        <span class="active-indicator-dot"></span>
    @endif
</a>
