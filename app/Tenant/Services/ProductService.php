<?php

namespace App\Tenant\Services;

use App\Tenant\Data\ProductFilterData;
use App\Tenant\Models\Core\Product;
use Illuminate\Database\Eloquent\Builder;

class ProductService
{
    public function getFilteredQuery(ProductFilterData $filter): Builder
    {
        return Product::query()
            ->with(['category', 'variants'])
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->select('products.*', 'categories.order_column as category_order')
            ->when(
                value: $filter->search,
                callback: fn(Builder $q, string $search) => $q->where(
                    fn(Builder $sq) => $sq->where('products.name', 'like', "%$search%")
                        ->orWhere('products.description', 'like', "%$search%")
                )
            )
            ->when(
                value: $filter->filterCategory,
                callback: fn(Builder $q, $categoryId) => $q->where('products.category_id', $categoryId)
            )
            ->when(
                value: in_array($filter->filterStatus, ['active', 'inactive']),
                callback: fn(Builder $q) => $q->where('products.is_active', $filter->filterStatus === 'active')
            )
            ->when(
                value: $filter->filterPrice === 'above-100k',
                callback: fn(Builder $q) => $q->whereHas('variants', fn(Builder $v) => $v->where('price', '>=', 100000))
            )
            ->when(
                value: $filter->filterPrice && str_contains($filter->filterPrice, '-'),
                callback: fn(Builder $q) => $q->whereHas('variants', fn(Builder $v) => $v->whereBetween('price', array_map('intval', explode('-', $filter->filterPrice))))
            )
            ->when(
                value: $filter->sortField ?? true, // Selalu ada default sort
                callback: match ($filter->sortField) {
                    'price_asc' => fn(Builder $q) => $q->withMin('variants as min_price', 'price')->orderBy('min_price'),
                    'price_desc' => fn(Builder $q) => $q->withMin('variants as min_price', 'price')->orderBy('min_price', 'desc'),
                    'stock_asc' => fn(Builder $q) => $q->withSum('variants as total_stock', 'stock')->orderBy('total_stock'),
                    'stock_desc' => fn(Builder $q) => $q->withSum('variants as total_stock', 'stock')->orderBy('total_stock', 'desc'),
                    'name_asc' => fn(Builder $q) => $q->orderBy('products.name'),
                    'name_desc' => fn(Builder $q) => $q->orderBy('products.name', 'desc'),
                    'category' => fn(Builder $q) => $q->orderByRaw('ISNULL(categories.order_column), categories.order_column ASC')->orderBy('products.id', 'desc'),
                    default => fn(Builder $q) => $q->orderBy('products.id'),
                }
            );
    }

    public function toggleAvailability(Product $product): void
    {
        $product->update(['is_active' => !$product->is_active]);
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }

    public function bulkDelete(array $ids): void
    {
        Product::whereIn('id', $ids)->delete();
    }

    public function bulkToggleStatus(array $ids, bool $status): void
    {
        Product::whereIn('id', $ids)->update(['is_active' => $status]);
    }
}
