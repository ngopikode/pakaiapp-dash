<?php

namespace App\Tenant\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Tenant\Models\Core\Product;
use App\Shared\Traits\ApiPaginationTrait;
use App\Shared\Traits\ApiResponserTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductApiController extends Controller
{
    use ApiResponserTrait, ApiPaginationTrait;

    public function index(Request $request): JsonResponse
    {
        $limit = (int)$request->input('limit', 20);
        $categoryName = $request->input('category', 'all');

        $query = Product::with(['category', 'variants']);

        if ($categoryName !== 'all') {
            $query->whereHas('category', function ($q) use ($categoryName) {
                $q->where('name', $categoryName);
            });
        }

        $products = $query->paginate($limit);

        $transformedData = $products->getCollection()->transform(function (Product $product) {
            return [
                'id' => "product-{$product->id}",
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => (float)$product->price, // Uses accessor
                'description' => $product->description,
                'category' => $product->category ? $product->category->name : 'Uncategorized',
                'image' => $product->image ? Storage::url($product->image) : null,
                'type' => $product->has_variants ? 'multi' : 'single',
                'is_available' => (bool)$product->is_active,
                'options' => $product->variants->pluck('name'),
            ];
        });

        $data = self::autoPaginateWrapperV2($products, $transformedData);

        return $this->successResponse($data);
    }

    public function show(Request $request, string $productId): JsonResponse
    {
        $id = str_contains($productId, 'product-') ? explode('-', $productId)[1] : $productId;
        $product = Product::with(['category', 'variants'])->findOrFail($id);

        $transformedData = [
            'id' => "product-{$product->id}",
            'product_id' => $product->id,
            'name' => $product->name,
            'price' => (float)$product->price,
            'description' => $product->description,
            'category' => $product->category ? $product->category->name : 'Uncategorized',
            'image' => $product->image ? Storage::url($product->image) : null,
            'type' => $product->has_variants ? 'multi' : 'single',
            'is_available' => (bool)$product->is_active,
            'options' => $product->variants->pluck('name'),
        ];

        return $this->successResponse($transformedData);
    }
}
