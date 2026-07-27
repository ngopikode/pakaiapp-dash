<?php

namespace App\Tenant\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Shared\Traits\ApiResponserTrait;
use App\Tenant\Models\Core\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryApiController extends Controller
{
    use ApiResponserTrait;

    public function __invoke(Request $request): JsonResponse
    {
        // Dalam konteks tenancy, kita tidak perlu cari tenant manual
        // karena sudah terkoneksi ke database tenant secara otomatis

        $categories = Category::pluck('name')->toArray();

        return $this->successResponse($categories);
    }
}
