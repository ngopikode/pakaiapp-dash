<?php

namespace App\Tenant\Services;

use App\Tenant\Data\ProductFilterData;
use App\Tenant\Data\ProductFormData;
use App\Tenant\Models\Core\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function saveFromForm(?Product $product, ProductFormData $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            $attrs = [
                'category_id' => $data->categoryId,
                'name' => $data->name,
                'description' => $data->description,
                'image' => $data->image,
                'tax_included' => $data->taxIncluded,
                'has_variants' => $data->hasVariants,
                'is_active' => $data->isActive,
                'selection_type' => $data->selectionType,
                'max_selections' => $data->maxSelections,
            ];

            $product = Product::updateOrCreate(
                ['id' => $product?->id],
                $attrs
            );

            $this->syncVariants($product, $data);
            $this->syncExtras($product, $data);

            return $product;
        });
    }

    private function syncVariants(Product $product, ProductFormData $data): void
    {
        $variantIdsToKeep = [];

        if ($data->hasVariants) {
            foreach ($data->variants as $variantData) {
                if (!empty($variantData['name'])) {
                    $variant = $product->variants()->updateOrCreate(
                        ['id' => $variantData['id'] ?? null],
                        [
                            'name' => $variantData['name'],
                            'sku' => $variantData['sku'] ?? null,
                            'cost' => $variantData['cost'] ?: 0,
                            'price' => $variantData['price'] ?: 0,
                            'stock' => $variantData['stock'] ?: 0,
                            'min_stock' => $variantData['minStock'] ?: 0,
                        ]
                    );
                    $variantIdsToKeep[] = $variant->id;
                    $this->syncVariantRecipes($variant, $variantData['recipes'] ?? []);
                }
            }
        } else {
            $defaultVariant = $product->variants()->updateOrCreate(
                ['name' => 'Default'],
                [
                    'sku' => $data->baseSku ?: null,
                    'cost' => $data->baseCost ?: 0,
                    'price' => $data->basePrice ?: 0,
                    'stock' => $data->baseStock ?: 0,
                    'min_stock' => $data->baseMinStock ?: 0,
                ]
            );
            $variantIdsToKeep[] = $defaultVariant->id;
            $this->syncVariantRecipes($defaultVariant, $data->baseRecipes);
        }

        $product->variants()->whereNotIn('id', $variantIdsToKeep)->delete();
    }

    private function syncVariantRecipes($variant, array $recipes): void
    {
        if (tenant('store_type') !== 'resto') return;

        $recipeIdsToKeep = [];
        foreach ($recipes as $recipeData) {
            if (!empty($recipeData['raw_material_id'])) {
                $recipe = $variant->recipes()->updateOrCreate(
                    ['id' => $recipeData['id'] ?? null],
                    [
                        'raw_material_id' => $recipeData['raw_material_id'],
                        'quantity_used' => $recipeData['quantity_used'] ?: 0,
                    ]
                );
                $recipeIdsToKeep[] = $recipe->id;
            }
        }
        $variant->recipes()->whereNotIn('id', $recipeIdsToKeep)->delete();
    }

    private function syncExtras(Product $product, ProductFormData $data): void
    {
        if (tenant('store_type') !== 'resto') return;

        $extraIdsToKeep = [];
        foreach ($data->extras as $extraData) {
            if (!empty($extraData['name'])) {
                $extra = $product->extras()->updateOrCreate(
                    ['id' => $extraData['id'] ?? null],
                    [
                        'name' => $extraData['name'],
                        'cost' => $extraData['cost'] ?: 0,
                        'price' => $extraData['price'] ?: 0,
                        'is_active' => true,
                    ]
                );
                $extraIdsToKeep[] = $extra->id;
            }
        }
        $product->extras()->whereNotIn('id', $extraIdsToKeep)->delete();
    }

    public function getFilteredQuery(ProductFilterData $filter): Builder
    {
        return Product::query()
            ->with(['category', 'variants'])
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->select('products.*', 'categories.order_column as category_order')
            ->when(
                value: $filter->search,
                callback: fn (Builder $q, string $search) => $q->where(
                    fn (Builder $sq) => $sq->where('products.name', 'like', "%$search%")
                        ->orWhere('products.description', 'like', "%$search%")
                )
            )
            ->when(
                value: $filter->filterCategory,
                callback: fn (Builder $q, $categoryId) => $q->where('products.category_id', $categoryId)
            )
            ->when(
                value: in_array($filter->filterStatus, ['active', 'inactive']),
                callback: fn (Builder $q) => $q->where('products.is_active', $filter->filterStatus === 'active')
            )
            ->when(
                value: $filter->filterPrice === 'above-100k',
                callback: fn (Builder $q) => $q->whereHas('variants', fn (Builder $v) => $v->where('price', '>=', 100000))
            )
            ->when(
                value: $filter->filterPrice && str_contains($filter->filterPrice, '-'),
                callback: fn (Builder $q) => $q->whereHas('variants', fn (Builder $v) => $v->whereBetween('price', array_map('intval', explode('-', $filter->filterPrice))))
            )
            ->when(
                value: $filter->sortField ?? true,
                callback: match ($filter->sortField) {
                    'price_asc' => fn (Builder $q) => $q->withMin('variants as min_price', 'price')->orderBy('min_price'),
                    'price_desc' => fn (Builder $q) => $q->withMin('variants as min_price', 'price')->orderBy('min_price', 'desc'),
                    'stock_asc' => fn (Builder $q) => $q->withSum('variants as total_stock', 'stock')->orderBy('total_stock'),
                    'stock_desc' => fn (Builder $q) => $q->withSum('variants as total_stock', 'stock')->orderBy('total_stock', 'desc'),
                    'name_asc' => fn (Builder $q) => $q->orderBy('products.name'),
                    'name_desc' => fn (Builder $q) => $q->orderBy('products.name', 'desc'),
                    'category' => fn (Builder $q) => $q->orderByRaw('ISNULL(categories.order_column), categories.order_column ASC')->orderBy('products.id', 'desc'),
                    default => fn (Builder $q) => $q->orderBy('products.id'),
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
