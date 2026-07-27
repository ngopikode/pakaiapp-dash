<?php

namespace App\Observers;

use App\Tenant\Models\Core\Product;
use App\Tenant\Services\ProductQuotaService;
use Exception;

class ProductObserver
{
    protected ?ProductQuotaService $productQuotaService = null;

    protected function productQuotaService(): ProductQuotaService
    {
        return $this->productQuotaService ??= app(ProductQuotaService::class);
    }

    /**
     * @throws Exception
     */
    public function creating(Product $product): void
    {
        $this->productQuotaService()->ensureCanCreate();
    }

    public function created(Product $product): void
    {
        $this->productQuotaService()->incrementUsedSlots();
    }

    public function deleted(Product $product): void
    {
        $this->productQuotaService()->decrementUsedSlots();
    }
}
