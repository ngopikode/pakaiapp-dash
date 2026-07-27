<?php

namespace App\Shared\Traits;

use Illuminate\Support\Facades\Cache;

trait ClearsStoreSettingCache
{
    protected static function bootClearsStoreSettingCache()
    {
        static::saved(function () {
            Cache::forget('store_setting_' . tenant('id'));
        });

        static::deleted(function () {
            Cache::forget('store_setting_' . tenant('id'));
        });
    }
}
