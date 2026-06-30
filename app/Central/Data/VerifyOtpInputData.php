<?php

namespace App\Central\Data;

use Spatie\LaravelData\Attributes\Validation\Digits;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class VerifyOtpInputData extends Data
{
    public function __construct(
        #[Required, Email]
        public string $email,
        #[Required, Digits(6)]
        public string $otp,
    ) {}
}
