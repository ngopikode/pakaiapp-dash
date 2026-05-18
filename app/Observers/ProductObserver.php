<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\Quota;
use Exception;

class ProductObserver
{
    /**
     * Handle the Product "creating" event.
     * @throws Exception
     */
    public function creating(Product $product): void
    {
        $quota = Quota::firstOrCreate(
            ['type' => 'PRODUCT_SLOT'],
            ['total_slots' => 50, 'used_slots' => 0]
        );

        if ($quota->used_slots >= $quota->total_slots) {
            throw new Exception("Wah, menu jualanmu makin banyak nih! 🚀 Sayangnya kuota slot ($quota->total_slots menu) sudah penuh. Yuk, tambah slot baru pakai Saldo biar makin cuan!");
        }
    }

    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        $quota = Quota::firstOrCreate(
            ['type' => 'PRODUCT_SLOT'],
            ['total_slots' => 50, 'used_slots' => 0]
        );

        $quota->increment('used_slots');
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        $quota = Quota::where('type', 'PRODUCT_SLOT')->first();

        if ($quota && $quota->used_slots > 0) {
            $quota->decrement('used_slots');
        }
    }
}
