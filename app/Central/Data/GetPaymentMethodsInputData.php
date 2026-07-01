<?php

namespace App\Central\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Min;

class GetPaymentMethodsInputData extends Data
{
    public function __construct(
        #[Min(1)]
        public int|float $amount,
    ) {
    }
}
