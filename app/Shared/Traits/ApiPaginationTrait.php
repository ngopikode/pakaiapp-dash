<?php

namespace App\Shared\Traits;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

trait ApiPaginationTrait
{
    protected int $page = 1;

    protected int $limit = 5;

    protected string $order = 'ASC';

    protected function manualPaginateWrapper(int $page, int $limit, int $total, mixed $data = []): array
    {
        return [
            '_metadata' => [
                'page' => $page,
                'per_page' => $limit,
                'total' => $total,
            ],
            'records' => $data,
        ];
    }

    protected static function autoPaginateWrapper(mixed $data): array
    {
        return [
            '_metadata' => [
                'page' => (int)$data->currentPage(),
                'per_page' => (int)$data->perPage(),
                'total' => (int)$data->total(),
            ],
            'records' => $data->items(),
        ];
    }

    protected static function autoPaginateWrapperV2(
        LengthAwarePaginator $paginator,
        Collection|array|null $transformedData = null
    ): array {
        if (is_null($transformedData)) {
            $transformedData = $paginator->items();
        }

        return [
            'wrapper-v2' => true,
            'headers' => [
                'Total-Count' => $paginator->total(),
                'Per-Page' => $paginator->perPage(),
                'Current-Page' => $paginator->currentPage(),
                'Total-Pages' => $paginator->lastPage(),
            ],
            'records' => $transformedData,
        ];
    }
}
