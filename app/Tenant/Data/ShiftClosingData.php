<?php

namespace App\Tenant\Data;

use Spatie\LaravelData\Data;

class ShiftClosingData extends Data
{
    public function __construct(
        public float $actualCash,
        /** @var ShiftOpnameItemData[] */
        public array $opnameItems,
    ) {}
}
