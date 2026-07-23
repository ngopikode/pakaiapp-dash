<?php

namespace App\Tenant\Services;

use App\Tenant\Data\ProductFilterData;
use App\Tenant\Models\Core\Product;
use Illuminate\Database\Eloquent\Builder;

class ProductService
{
    public function getFilteredQuery(ProductFilterData $filter): Builder
    {
        $query = Product::with(['category', 'variants'])
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->select('products.*', 'categories.order_column as category_order');

        if ($filter->search) {
            $query->where(fn ($q) => $q->where('products.name', 'like', "%{$filter->search}%")
                ->orWhere('products.description', 'like', "%{$filter->search}%"));
        }

        if ($filter->filterCategory) {
            $query->where('products.category_id', $filter->filterCategory);
        }
        if ($filter->filterStatus === 'active') {
            $query->where('products.is_active', true);
        } elseif ($filter->filterStatus === 'inactive') {
            $query->where('products.is_active', false);
        }

        if ($filter->filterPrice) {
            $range = explode('-', $filter->filterPrice);
            if (count($range) === 2) {
                $query->whereHas('variants', fn ($q) => $q->whereBetween('price', [(int) $range[0], (int) $range[1]]));
            } elseif ($filter->filterPrice === 'above-100k') {
                $query->whereHas('variants', fn ($q) => $q->where('price', '>=', 100000));
            }
        }

        match ($filter->sortField) {
            'price_asc' => $query->selectRaw('(SELECT MIN(price) FROM product_variants WHERE product_variants.product_id = products.id) as min_price')
                ->orderBy('min_price', 'asc'),
            'price_desc' => $query->selectRaw('(SELECT MIN(price) FROM product_variants WHERE product_variants.product_id = products.id) as min_price')
                ->orderBy('min_price', 'desc'),
            'stock_asc' => $query->selectRaw('(SELECT SUM(stock) FROM product_variants WHERE product_variants.product_id = products.id) as total_stock')
                ->orderBy('total_stock', 'asc'),
            'stock_desc' => $query->selectRaw('(SELECT SUM(stock) FROM product_variants WHERE product_variants.product_id = products.id) as total_stock')
                ->orderBy('total_stock', 'desc'),
            'name_asc' => $query->orderBy('products.name', 'asc'),
            'name_desc' => $query->orderBy('products.name', 'desc'),
            'category' => $query->orderByRaw('ISNULL(categories.order_column), categories.order_column ASC')->orderBy('products.id', 'desc'),
            default => $query->orderBy('products.id', 'asc'),
        };

        return $query;
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
