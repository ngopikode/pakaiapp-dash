<?php

namespace App\Central\Data;

use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;

class GetPaymentMethodsInputData extends Data
{
    public function __construct(
        #[Min(1)]
        public int|float $amount,
    ) {}
}
