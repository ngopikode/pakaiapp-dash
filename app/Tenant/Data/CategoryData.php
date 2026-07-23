<?php

namespace App\Tenant\Data;

use Spatie\LaravelData\Data;

class CategoryData extends Data
{
    public function __construct(
        public string $name,
        public string $type = 'retail',
        public ?int $id = null,
    ) {}
}
