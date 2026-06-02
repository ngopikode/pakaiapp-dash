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
        
        // Menggunakan cache key baru 'global_setting_val_' agar bypass cache model lama
        // Kita hanya meng-cache $value akhirnya (scalar/array) bukan Object Eloquent Model
        // untuk mencegah error "__PHP_Incomplete_Class" saat unserialize
        $value = Cache::remember('global_setting_val_' . $globalKey, 3600, function () use ($globalKey) {
            $setting = GlobalSetting::find($globalKey);
            return $setting ? $setting->cast_value : null;
        });

        if ($value !== null) {
            return $value;
        }

        // 3. Fallback terakhir
        return $default;
    }
}
