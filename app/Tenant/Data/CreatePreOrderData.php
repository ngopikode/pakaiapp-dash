<?php

namespace App\Tenant\Data;

use Spatie\LaravelData\Data;

/**
 * Kontrak data untuk PreOrderService::createPreOrder().
 * Dipakai oleh PreOrderApiController saat checkout mode = direct_wa.
 */
class CreatePreOrderData extends Data
{
    public function __construct(
        public string $customerName,
        public string $customerAddress,
        public string $deliveryDate,
        public int $deliverySlotId,
        public int $deliveryZoneId,
        public string $paymentMethod,
        public ?string $customerPhone = null,
        public ?string $notes = null,
    ) {}
}
