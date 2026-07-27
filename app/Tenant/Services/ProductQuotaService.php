<?php

namespace App\Tenant\Services;

use App\Central\Models\Quota;
use Exception;

class ProductQuotaService
{
    protected ?SettingService $settingService = null;

    protected function settingService(): SettingService
    {
        return $this->settingService ??= app(SettingService::class);
    }

    private function resolveQuota(): Quota
    {
        $limit = (int) $this->settingService()->get('product_slots', tenant(), 12);

        return Quota::firstOrCreate(
            ['type' => 'PRODUCT_SLOT'],
            ['total_slots' => $limit, 'used_slots' => 0]
        );
    }

    /**
     * @throws Exception
     */
    public function ensureCanCreate(): void
    {
        $quota = $this->resolveQuota();

        if ($quota->used_slots >= $quota->total_slots) {
            throw new Exception("Kuota slot ($quota->total_slots menu) sudah penuh. Tambah slot baru pakai Saldo.");
        }
    }

    public function incrementUsedSlots(): void
    {
        $this->resolveQuota()->increment('used_slots');
    }

    public function decrementUsedSlots(): void
    {
        Quota::where('type', 'PRODUCT_SLOT')
            ->where('used_slots', '>', 0)
            ->decrement('used_slots');
    }
}
