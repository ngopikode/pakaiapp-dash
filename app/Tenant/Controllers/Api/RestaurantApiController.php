<?php

namespace App\Tenant\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Shared\Traits\ApiResponserTrait;
use App\Tenant\Models\Core\StoreSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class RestaurantApiController extends Controller
{
    use ApiResponserTrait;

    public function __invoke(Request $request): JsonResponse
    {
        $setting = StoreSetting::first();

        if (!$setting) return $this->errorResponse(message: 'Store settings not found', code: ResponseAlias::HTTP_NOT_FOUND);

        $data = [
            'id' => tenant('id'),
            'name' => $setting->name,
            'logo' => $setting->logo ? Storage::url($setting->logo) : null,
            'theme_color' => $setting->theme_color,
            'whatsapp_number' => $setting->whatsapp_number,
            'address' => $setting->address,
            'is_tax_active' => (bool)$setting->is_tax_active,
            'tax_rate' => (float)$setting->tax_rate,
            'is_service_charge_active' => (bool)$setting->is_service_charge_active,
            'service_charge_rate' => (float)$setting->service_charge_rate,
            'hero' => [
                'promo_text' => $setting->hero_promo_text,
                'status_text' => $setting->hero_status_text,
                'headline' => $setting->hero_headline,
                'tagline' => $setting->hero_tagline,
                'instagram_url' => $setting->hero_instagram_url,
            ],
            'navbar' => [
                'brand_text' => $setting->navbar_brand_text,
                'title' => $setting->navbar_title,
                'subtitle' => $setting->navbar_subtitle,
            ],
            'seo' => [
                'title' => $setting->seo_title,
                'description' => $setting->seo_description,
                'keywords' => $setting->seo_keywords,
                'og_title' => $setting->og_title,
                'og_description' => $setting->og_description,
                'og_image' => $setting->og_image ? Storage::url($setting->og_image) : null
            ],
            'operating_hours' => $setting->operating_hours,
            'use_same_hours'  => (bool) $setting->use_same_hours,
            'is_open_now'     => $setting->isOpenNow(),
            'today_hours'     => $setting->getTodayHours(),
        ];

        return $this->successResponse($data);
    }
}
