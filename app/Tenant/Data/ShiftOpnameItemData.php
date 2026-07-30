<?php

namespace App\Tenant\Data;

use Spatie\LaravelData\Data;

class ShiftOpnameItemData extends Data
{
    public function __construct(
        public int $rawMaterialId,
        public float $physicalStock,
        public ?string $note = null,
    ) {}
}
