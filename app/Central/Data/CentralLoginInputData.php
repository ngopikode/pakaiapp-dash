<?php

namespace App\Central\Data;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class CentralLoginInputData extends Data
{
    public function __construct(
        #[Required, StringType, Max(255)]
        public string $login_input,
    ) {}
}
