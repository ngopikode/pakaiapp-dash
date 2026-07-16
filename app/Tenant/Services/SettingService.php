<?php

namespace App\Tenant\Services;

use App\Central\Models\GlobalSetting;
use App\Central\Models\Tenant;
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
            callback: fn() => GlobalSetting::find("default_$key")?->cast_value
        );

        return $value ?? $default;
    }

    /**
     * Ambil banyak setting sekaligus dalam satu round-trip cache/database.
     * Jauh lebih efisien daripada memanggil get() berkali-kali.
     *
     * @param array<string, mixed> $keysWithDefaults ['key' => defaultValue]
     * @param Tenant|null $tenant
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
            $globalKeys = array_map(fn($k) => 'default_' . $k, $sortedKeys);
            return GlobalSetting::whereIn('key', $globalKeys)->get()
                ->mapWithKeys(fn($s) => [substr($s->key, 8) => $s->cast_value])
                ->all();
        });

        foreach (
            $globalKeysNeeded as $key => $default
        ) $results[$key] = $cached[$key] ?? $default;

        return $results;
    }
}
