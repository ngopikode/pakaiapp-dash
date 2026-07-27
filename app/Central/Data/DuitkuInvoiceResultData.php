<?php

namespace App\Central\Data;

use Spatie\LaravelData\Data;

class DuitkuInvoiceResultData extends Data
{
    public function __construct(
        public string $paymentUrl,
        public string $reference,
        public ?string $vaNumber = null,
    ) {}
}
