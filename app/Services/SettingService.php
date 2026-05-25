<?php

namespace App\Services;

use App\Models\GlobalSetting;
use App\Models\Tenant;
use Illuminate\Support\Facades\Cache;

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
     * @param string $key Contoh: 'trx_fee', otomatis cek 'default_trx_fee' di global
     * @param Tenant|null $tenant
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, ?Tenant $tenant = null, mixed $default = null): mixed
    {
        // 1. Cek properti pada Tenant (di kolom JSON 'data')
        if ($tenant && isset($tenant->$key)) {
            return $tenant->$key;
        }

        // 2. Cek Global Setting dari Database (di-cache untuk performance)
        $globalKey = 'default_' . $key;
        
        $globalSetting = Cache::remember('global_setting_' . $globalKey, 3600, function () use ($globalKey) {
            return GlobalSetting::find($globalKey);
        });

        if ($globalSetting) {
            return $globalSetting->cast_value;
        }

        // 3. Fallback terakhir
        return $default;
    }
}
