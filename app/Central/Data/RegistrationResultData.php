<?php

namespace App\Central\Data;

use Spatie\LaravelData\Attributes\Hidden;
use Spatie\LaravelData\Data;

class RegistrationResultData extends Data
{
    public function __construct(
        public string $type,

        #[Hidden] // Don't serialize message in 'data' response, we pass it to parent response
        public string $message,

        public ?string $redirect_url = null,
        public ?string $payment_url = null,
        public ?string $snap_token = null,
        public ?string $invoice_code = null,
    ) {}
}
