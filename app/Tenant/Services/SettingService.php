<?php

namespace App\Tenant\Services;

use App\Central\Models\GlobalSetting;
use App\Central\Models\Tenant;
use App\Tenant\Data\StoreSettingFormData;
use App\Tenant\Models\Core\StoreSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class SettingService
{
    /**
     * Get a setting value, falling back from Tenant overrides to Global defaults.
     *
     * Prioritas:
     * 1. $tenant->data['key']
     * 2. GlobalSetting (di-cache)
     * 3. Parameter $default (hardcoded fallback)
     *
     * @param  string  $key  Contoh: 'trx_fee', otomatis cek 'default_trx_fee' di global
     */
    public function get(string $key, ?Tenant $tenant = null, mixed $default = null): mixed
    {
        if ($tenant && isset($tenant->$key)) return $tenant->$key;

        // Menggunakan cache key 'global_setting_val_' agar bypass cache model lama.
        // Kita hanya meng-cache $value akhirnya (scalar/array) bukan Object Eloquent Model
        // untuk mencegah error "__PHP_Incomplete_Class" saat unserialize.
        $value = Cache::remember(
            key: "global_setting_val_default_$key",
            ttl: 3600,
            callback: fn () => GlobalSetting::find("default_$key")?->cast_value
        );

        return $value ?? $default;
    }

    /**
     * Ambil banyak setting sekaligus dalam satu round-trip cache/database.
     * Jauh lebih efisien daripada memanggil get() berkali-kali.
     *
     * @param  array<string, mixed>  $keysWithDefaults  ['key' => defaultValue]
     * @return array<string, mixed> ['key' => resolvedValue]
     */
    public function getMany(array $keysWithDefaults, ?Tenant $tenant = null): array
    {
        $results = [];
        $globalKeysNeeded = [];

        foreach ($keysWithDefaults as $key => $default) {
            if ($tenant && isset($tenant->$key)) {
                $results[$key] = $tenant->$key;
            } else {
                $globalKeysNeeded[$key] = $default;
            }
        }

        if (empty($globalKeysNeeded)) return $results;

        // Sort untuk memastikan cache key konsisten terlepas dari urutan input
        $sortedKeys = array_keys($globalKeysNeeded);
        sort($sortedKeys);

        $cacheKey = 'global_settings_batch_' . md5(implode(',', $sortedKeys));
        $cached = Cache::remember($cacheKey, 3600, function () use ($sortedKeys) {
            $globalKeys = array_map(fn ($k) => 'default_' . $k, $sortedKeys);

            return GlobalSetting::whereIn('key', $globalKeys)->get()
                ->mapWithKeys(fn ($s) => [substr($s->key, 8) => $s->cast_value])
                ->all();
        });

        foreach (
            $globalKeysNeeded as $key => $default
        ) $results[$key] = $cached[$key] ?? $default;

        return $results;
    }

    /**
     * @throws Throwable
     */
    public function saveFromForm(?StoreSetting $setting, StoreSettingFormData $data, $newLogo, $newOgImage, $newQrisImage = null): void
    {
        $attrs = [
            'name' => $data->name,
            'theme_color' => $data->themeColor,
            'whatsapp_number' => $data->whatsappNumber,
            'address' => $data->address,
            'is_active' => $data->isActive,
            'store_type' => $data->storeType,
            'is_dinein_active' => $data->isDineinActive,
            'is_takeaway_active' => $data->isTakeawayActive,
            'is_delivery_active' => $data->isDeliveryActive,
            'is_tax_active' => $data->isTaxActive,
            'tax_rate' => $data->taxRate,
            'is_service_charge_active' => $data->isServiceChargeActive,
            'service_charge_rate' => $data->serviceChargeRate,
            'is_application_fee_passed' => $data->isApplicationFeePassed,
            'is_kitchen_active' => $data->isKitchenActive,
            'is_shift_active' => $data->isShiftActive,
            'is_wa_checkout_active' => $data->isWaCheckoutActive,
            'is_preorder_active' => $data->isPreorderActive,
            'cutoff_time' => $data->cutoffTime,
            'hero_promo_text' => $data->heroPromoText,
            'hero_status_text' => $data->heroStatusText,
            'hero_headline' => $data->heroHeadline,
            'hero_tagline' => $data->heroTagline,
            'hero_instagram_url' => $data->heroInstagramUrl,
            'navbar_brand_text' => $data->navbarBrandText,
            'navbar_title' => $data->navbarTitle,
            'navbar_subtitle' => $data->navbarSubtitle,
            'seo_title' => $data->seoTitle,
            'seo_description' => $data->seoDescription,
            'seo_keywords' => $data->seoKeywords,
            'og_title' => $data->ogTitle,
            'og_description' => $data->ogDescription,
            'use_same_hours' => $data->useSameHours,
            'operating_hours' => $data->operatingHours,
        ];

        try {
            DB::beginTransaction();

            if ($newLogo) {
                if ($setting?->logo) Storage::disk('public')->delete($setting->logo);
                $attrs['logo'] = $newLogo->store('settings', 'public');
            }

            if ($newOgImage) {
                if ($setting?->og_image) Storage::disk('public')->delete($setting->og_image);
                $attrs['og_image'] = $newOgImage->store('settings', 'public');
            }

            if ($newQrisImage) {
                if ($setting?->qris_image) Storage::disk('public')->delete($setting->qris_image);
                $attrs['qris_image'] = $newQrisImage->store('settings', 'public');
            }

            StoreSetting::updateOrCreate(
                ['id' => $setting?->id ?? 1],
                $attrs
            );

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @throws Throwable
     */
    public function deleteQrisImage(?StoreSetting $setting): void
    {
        if (!$setting?->qris_image) return;

        try {
            DB::beginTransaction();
            Storage::disk('public')->delete($setting->qris_image);
            StoreSetting::where('id', $setting->id ?? 1)->update(['qris_image' => null]);
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
