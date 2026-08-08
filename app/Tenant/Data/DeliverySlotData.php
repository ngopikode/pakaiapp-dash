<?php

namespace App\Tenant\Data;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
class DeliverySlotData extends Data
{
    public function __construct(
        public string $name,
        public string $startTime,
        public string $endTime,
        public int $maxOrders = 0,
        public bool $isActive = true,
    ) {}
}
