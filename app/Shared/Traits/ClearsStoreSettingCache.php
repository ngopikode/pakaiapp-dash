<?php

namespace App\Shared\Traits;

use App\Tenant\Models\Core\StoreSetting;

trait ClearsStoreSettingCache
{
    protected static function bootClearsStoreSettingCache(): void
    {
        static::saved(function () {
            StoreSetting::forgetCache();
        });

        static::deleted(function () {
            StoreSetting::forgetCache();
        });
    }
}
