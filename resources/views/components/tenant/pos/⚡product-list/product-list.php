<?php

use App\Tenant\Models\Core\Category;
use App\Tenant\Models\Core\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public string $search = '';

    public string $categoryFilter = 'all';

    public int $limit = 12;

    #[On('stock-updated')]
    public function stockUpdated(): void
    {
        // No-op: just triggers a re-render so product data refreshes from DB
    }

    public function updatedSearch(): void
    {
        $this->resetLimit();
    }

    public function updatedCategoryFilter(): void
    {
        $this->resetLimit();
    }

    public function loadMore(): void
    {
        $this->limit += 8;
    }

    public function handleEnter($value = null): void
    {
        $searchTerm = trim($value ?? $this->search);
        if ($searchTerm === '') return;

        $this->search = $searchTerm;

        $matchedVariantId = null;
        $product = $this->baseProductQuery()->whereHas('variants', fn ($q) => $q->where('sku', $searchTerm))->first();

        if ($product) {
            $matchedVariantId = $product->variants->firstWhere('sku', $searchTerm)?->id;
        }

        if (!$product) {
            $product = $this->singleSearchMatch($searchTerm);
        }

        if (!$product) return;

        $this->dispatch('add-product', product: $this->formatProduct($product), variantId: $matchedVariantId);
        $this->search = '';
    }

    public function with(): array
    {
        $query = $this->baseProductQuery()
            ->when($this->categoryFilter === 'promo', fn ($q) => $q->whereHas('variants', fn ($q2) => $q2->whereNotNull('active_discount_price')))
            ->when($this->search !== '', fn ($q) => $this->applySearchFilter($q, $this->search))
            ->when($this->categoryFilter !== 'all' && $this->categoryFilter !== 'promo' && $this->search === '', fn ($q) => $q->where('category_id', $this->categoryFilter));

        $totalCount = (clone $query)->count();
        $products = $query->take($this->limit)->get()->map(fn ($product) => $this->formatProduct($product));

        return [
            'categories' => $this->categories(),
            'products' => $products,
            'hasMore' => $this->limit < $totalCount,
            'hasPromoItems' => $this->hasPromoItems(),
        ];
    }

    private function resetLimit(): void
    {
        $this->limit = 12;
    }

    private function baseProductQuery(): Builder
    {
        return Product::with($this->productRelations())
            ->where('is_active', true);
    }

    private function productRelations(): array
    {
        $relations = ['variants:id,product_id,sku,name,cost,price,stock,active_discount_price,active_discount_name'];

        if (tenant('store_type') === 'resto') {
            $relations['extras'] = fn ($q) => $q->where('is_active', true);
        }

        return $relations;
    }

    private function applySearchFilter($query, string $searchTerm): void
    {
        $words = array_values(array_filter(explode(' ', $searchTerm)));

        $query->where(function ($builder) use ($words, $searchTerm) {
            $this->applyPhraseSearch($builder, $searchTerm);

            if (count($words) > 1) {
                $builder->orWhere(function ($multiWordQuery) use ($words) {
                    foreach ($words as $word) {
                        $multiWordQuery->where(function ($wordQuery) use ($word) {
                            $this->applyPhraseSearch($wordQuery, $word);
                        });
                    }
                });
            }
        });
    }

    private function applyPhraseSearch($query, string $term): void
    {
        $query->where('name', 'like', '%' . $term . '%')
            ->orWhere('description', 'like', '%' . $term . '%')
            ->orWhereHas('variants', fn ($v) => $v->where('sku', 'like', '%' . $term . '%')
                ->orWhere('name', 'like', '%' . $term . '%')
            );
    }

    private function singleSearchMatch(string $searchTerm): ?Product
    {
        $query = $this->baseProductQuery()
            ->where(fn ($q) => $this->applySearchFilter($q, $searchTerm));

        return $query->count() === 1 ? $query->first() : null;
    }

    private function formatProduct(Product $product): array
    {
        $product->loadMissing($this->productRelations());
        $variants = $product->variants;
        $activeDiscountName = $variants->firstWhere('active_discount_name', '!=')?->active_discount_name;
        $activeDiscountPrice = $variants->min('active_discount_price');

        return [
            'id' => $product->id,
            'name' => $product->name,
            'category_id' => $product->category_id,
            'image_url' => $product->image ? Storage::url($product->image) : null,
            'has_variants' => (bool) $product->has_variants,
            'selection_type' => $product->selection_type ?? 'single',
            'max_selections' => (int) ($product->max_selections ?? 1),
            'price' => (float) $variants->min('price'),
            'active_discount_price' => $activeDiscountPrice ? (float) $activeDiscountPrice : null,
            'active_discount_name' => $activeDiscountName,
            'stock' => (int) $variants->sum('stock'),
            'variants' => $variants->map(fn ($variant) => [
                'id' => $variant->id,
                'name' => $variant->name,
                'sku' => $variant->sku,
                'cost' => (float) $variant->cost,
                'price' => (float) $variant->price,
                'active_discount_price' => $variant->active_discount_price ? (float) $variant->active_discount_price : null,
                'active_discount_name' => $variant->active_discount_name,
                'stock' => (int) $variant->stock,
            ])->toArray(),
            'extras' => tenant('store_type') === 'resto'
                ? $product->extras->map(fn ($extra) => [
                    'id' => $extra->id,
                    'name' => $extra->name,
                    'price' => (float) $extra->price,
                    'is_active' => (bool) $extra->is_active,
                ])->toArray()
                : [],
        ];
    }

    private function categories(): Collection
    {
        return Category::orderBy('order_column')->get();
    }

    private function hasPromoItems(): bool
    {
        return Product::whereHas('variants', fn ($q) => $q->whereNotNull('active_discount_price'))
            ->where('is_active', true)
            ->exists();
    }
};
