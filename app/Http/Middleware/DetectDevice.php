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
        $userAgent = $request->userAgent();

        // Simple regex to detect common mobile/tablet user agents
        $isMobile = preg_match('/Mobile|Android|BlackBerry|iPhone|iPad|iPod|Opera Mini|IEMobile|WPDesktop/i', $userAgent);

        // Fallback for iPadOS 13+ which requests desktop site by default and spoofs as Mac
        if ($request->hasCookie('is_ipad') && $request->cookie('is_ipad') == '1') {
            $isMobile = true;
        }

        // Share a global variable to all Blade views
        View::share('isMobile', (bool)$isMobile);

        // Optionally, register it into the container if needed by controllers/services
        app()->instance('isMobile', (bool)$isMobile);

        return $next($request);
    }
}
