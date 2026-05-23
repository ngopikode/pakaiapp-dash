<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\IpUtils;

class DuitkuIpWhitelist
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Izinkan testing lokal jika environment adalah local
        if (app()->environment('local') && in_array($request->ip(), ['127.0.0.1', '::1'])) {
            return $next($request);
        }

        $clientIp = $request->ip();

        $allowedIps = config('duitku.sandbox', true)
            ? ['182.23.85.11', '182.23.85.12', '103.177.101.187', '103.177.101.188']
            : ['182.23.85.8', '182.23.85.9', '182.23.85.10', '182.23.85.13', '182.23.85.14',
               '103.177.101.184', '103.177.101.185', '103.177.101.186', '103.177.101.189', '103.177.101.190'];

        if (!IpUtils::checkIp($clientIp, $allowedIps)) {
            Log::warning('[Duitku Central] Diblokir: Request webhook dari IP yang tidak diizinkan', [
                'ip' => $clientIp,
                'user_agent' => $request->userAgent()
            ]);

            return response('INVALID_IP', 403);
        }

        return $next($request);
    }
}
