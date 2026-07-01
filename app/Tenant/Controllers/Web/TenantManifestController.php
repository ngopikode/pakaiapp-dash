<?php

declare(strict_types=1);

namespace App\Tenant\Controllers\Web;

use App\Http\Controllers\Controller;

use App\Tenant\Models\Core\StoreSetting;
use Illuminate\Http\JsonResponse;

class TenantManifestController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $setting = StoreSetting::first();
        $storeName = $setting->name ?? tenant('id');

        return response()->json([
            "name" => $storeName . " Dashboard",
            "short_name" => substr($storeName, 0, 12),
            "start_url" => "/dashboard",
            "display" => "standalone",
            "background_color" => "#ffffff",
            "theme_color" => $setting->theme_color ?? "#22c55e",
            "icons" => [
                [
                    "src" => "/android-chrome-192x192.png",
                    "sizes" => "192x192",
                    "type" => "image/png"
                ],
                [
                    "src" => "/android-chrome-512x512.png",
                    "sizes" => "512x512",
                    "type" => "image/png"
                ]
            ]
        ]);
    }
}
