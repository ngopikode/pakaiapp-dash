<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait ClearsAiMenuCache
{
    /**
     * Boot the trait and register model events.
     */
    protected static function bootClearsAiMenuCache(): void
    {
        $clearCache = function () {
            if (function_exists('tenant') && tenant('id')) {
                Cache::forget('ai_menu_tenant_' . tenant('id'));
            }
        };

        static::saved($clearCache);
        static::deleted($clearCache);
    }
}
