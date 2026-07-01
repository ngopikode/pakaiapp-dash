<?php

namespace App\Central\Data;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class CentralLoginResultData extends Data
{
    public function __construct(
        public string $type,
        /** @var array<int, array{store_name: string, tenant_id: string, url: string}>|null */
        public ?Collection $stores = null,
        public ?string $redirect_url = null,
    ) {}
}
