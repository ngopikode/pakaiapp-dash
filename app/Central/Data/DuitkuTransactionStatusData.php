<?php

namespace App\Central\Data;

use Spatie\LaravelData\Data;

class DuitkuTransactionStatusData extends Data
{
    public function __construct(
        public ?string $statusCode,
        public ?string $statusMessage,
    ) {}
}
