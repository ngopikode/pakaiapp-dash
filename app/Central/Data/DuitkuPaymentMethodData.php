<?php

namespace App\Central\Data;

use Spatie\LaravelData\Data;

class DuitkuPaymentMethodData extends Data
{
    public function __construct(
        public string $paymentMethod,
        public string $paymentName,
        public string $paymentImage,
        public int $totalFee,
    ) {
    }
}
