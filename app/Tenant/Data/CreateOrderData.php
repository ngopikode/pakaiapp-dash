<?php
namespace App\Tenant\Data;

use Spatie\LaravelData\Data;

class CreateOrderData extends Data
{
    public function __construct(
        public string $customerName,
        public ?string $tableNumber,
        public string $orderType,
        public bool $isTaxActive = true,
        public bool $isServiceActive = true,
    ) {}
}
