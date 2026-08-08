<?php

namespace App\Tenant\Data;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
class DeliveryZoneData extends Data
{
    public function __construct(
        public string $name,
        public float $shippingCost = 0,
        public float $minFreeShipping = 0,
        public bool $isActive = true,
    ) {}
}
