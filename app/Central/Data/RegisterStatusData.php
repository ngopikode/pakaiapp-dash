<?php

namespace App\Central\Data;

use Spatie\LaravelData\Data;

class RegisterStatusData extends Data
{
    public function __construct(
        public string $payment_status,
        public string $redirect_url,
        public ?string $payment_url = null,
    ) {}
}
