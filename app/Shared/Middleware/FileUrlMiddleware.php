<?php

namespace App\Shared\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FileUrlMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        config()->set(
            'filesystems.disks.public.url',
            url('/' . config('tenancy.filesystem.suffix_base') . tenant('id'))
        );

        return $next($request);
    }
}
