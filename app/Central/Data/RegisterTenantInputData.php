<?php

namespace App\Central\Data;

use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class RegisterTenantInputData extends Data
{
    public function __construct(
        #[Required, StringType, Max(100)]
        public string $jenisBisnis,
        #[Required, StringType, Max(100)]
        public string $namaToko,
        #[Required, StringType, Max(100)]
        public string $namaOwner,
        #[Required, StringType, Max(50)]
        public string $noWa,
        #[Required, Email, Max(150)]
        public string $email,
        #[Required, In(['free', 'santai', 'premium'])]
        public string $paket,
        #[Required, StringType, Max(50)]
        public string $payment_method,
    ) {}
}
