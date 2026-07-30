<?php

namespace App\Tenant\Data;

use Spatie\LaravelData\Data;

class ShiftExpenseData extends Data
{
    public function __construct(
        public float $amount,
        public string $description,
    ) {}
}
