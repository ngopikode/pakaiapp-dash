<?php

namespace App\Tenant\Data;

use Spatie\LaravelData\Data;

class DeliveryZoneData extends Data
{
    public function __construct(
        public string $name,
        public float $shippingCost,
        public float $minFreeShipping,
        public bool $isActive,
    ) {}
}
