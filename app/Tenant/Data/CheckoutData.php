<?php

namespace App\Tenant\Data;

use Spatie\LaravelData\Data;

class CheckoutData extends Data
{
    public function __construct(
        public string $customerName,
        public ?string $tableNumber,
        public string $orderType,
        public string $paymentMethod,
        public float $discount = 0,
        public float $amountPaid = 0,
        public bool $isTaxActive = true,
        public bool $isServiceActive = true,
    ) {}
}
