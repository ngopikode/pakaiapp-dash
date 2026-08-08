<?php

namespace App\Tenant\Data;

use Spatie\LaravelData\Data;

class DeliverySlotData extends Data
{
    public function __construct(
        public string $name,
        public string $startTime,
        public string $endTime,
        public int $maxOrders,
        public bool $isActive,
    ) {}
}
