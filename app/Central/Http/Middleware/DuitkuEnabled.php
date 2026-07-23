<?php

namespace App\Central\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class DuitkuEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (!config('duitku.enabled')) abort(
            code: ResponseAlias::HTTP_FORBIDDEN,
            message: 'Duitku payment gateway is disabled.'
        );

        return $next($request);
    }
}
