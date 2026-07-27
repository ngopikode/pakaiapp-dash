<?php

namespace App\Shared\Middleware;

use App\Tenant\Models\Core\StoreSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckStoreOpen
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Hanya berlaku untuk tenant
        if (!tenant()) {
            return $next($request);
        }

        $setting = StoreSetting::cached();

        // Jika operating_hours null, anggap selalu buka
        // Jika setting->is_active false, anggap tutup (di-handle juga oleh logic isOpenNow atau kita tambah sini)
        if ($setting && $setting->operating_hours && !$setting->isOpenNow()) {
            return response()->json([
                'success' => false,
                'message' => 'Toko sedang tutup. Silakan order kembali saat jam operasional.',
                'today_hours' => $setting->getTodayHours(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $next($request);
    }
}
