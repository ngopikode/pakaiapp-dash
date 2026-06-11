<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class DetectDevice
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userAgent = $request->userAgent() ?? '';

        // Check explicitly for tablets:
        // 1. iPad (its user agent often contains "Mobile", so we must catch it first)
        // 2. Android tablets (contains "Android" but usually lacks "Mobile")
        $isTablet = preg_match('/iPad/i', $userAgent) || (preg_match('/Android/i', $userAgent) && !preg_match('/Mobile/i', $userAgent));

        if ($isTablet) {
            $isMobile = false;
        } else {
            // Check for strictly mobile phones
            $isMobile = preg_match('/Mobile|iPhone|iPod|BlackBerry|Opera Mini|IEMobile|WPDesktop/i', $userAgent);
        }

        // Share a global variable to all Blade views
        View::share('isMobile', (bool)$isMobile);

        // Optionally, register it into the container if needed by controllers/services
        app()->instance('isMobile', (bool)$isMobile);

        return $next($request);
    }
}
