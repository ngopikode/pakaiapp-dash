<?php

namespace App\Tenant\Data;

use Spatie\LaravelData\Data;

class ProductFilterData extends Data
{
    public function __construct(
        public string $search = '',
        public string $filterCategory = '',
        public string $filterStatus = '',
        public string $filterPrice = '',
        public string $sortField = 'newest',
        public int $perPage = 20,
    ) {}
}
