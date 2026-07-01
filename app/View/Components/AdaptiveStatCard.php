<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AdaptiveStatCard extends Component
{
    public string $title;
    public string $value;

    /**
     * Create a new component instance.
     */
    public function __construct(string $title, string $value)
    {
        $this->title = $title;
        $this->value = $value;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        // View::shared('isMobile') returns the value we shared in the middleware
        if (app()->bound('isMobile') && app('isMobile') === true) {
            return view('components.mobile.stat-card');
        }
        
        // Fallback to View facade if needed, though bound should work
        // if (\Illuminate\Support\Facades\View::shared('isMobile')) { ... }

        return view('components.desktop.stat-card');
    }
}
