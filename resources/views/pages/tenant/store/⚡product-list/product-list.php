<?php

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * ProductList — the single Livewire component on the storefront.
 *
 * Responsibilities:
 *  - Category filtering  (backend, re-renders product grid)
 *  - Infinite scroll     (backend, appends more rows)
 *
 * viewMode (grid ↔ list) intentionally lives in Alpine.store('ui')
 * so toggling the layout never costs a Livewire network round-trip.
 */
new class extends Component
{
    public string $category   = 'all';
    public int    $perPage    = 10;
    public bool   $hasMore    = true;
    public array  $categories = [];

    public function mount(): void
    {
        // Categories are small and static-ish — load once, fine as serialized state.
        $this->categories = Category::orderBy('order_column')
            ->pluck('name')
            ->toArray();
    }

    public function setCategory(string $category): void
    {
        $this->category = $category;
        $this->perPage  = 10;
        $this->hasMore  = true;
    }

    public function loadMore(): void
    {
        $this->perPage += 10;
    }

    /**
     * Products computed property.
     *
     * Uses the "+1 trick": fetch perPage+1 rows in ONE query — no separate COUNT().
     * Result is NOT stored in the Livewire state snapshot, keeping payload lean.
     */
    #[Computed]
    public function products(): array
    {
        $items = Product::query()
            ->with(['category', 'variants'])
            ->when(
                $this->category !== 'all',
                fn ($q) => $q->whereHas(
                    'category',
                    fn ($q2) => $q2->where('name', $this->category)
                )
            )
            ->take($this->perPage + 1)
            ->get();

        // Determine hasMore via the extra row — zero extra queries.
        $this->hasMore = $items->count() > $this->perPage;

        return $items
            ->take($this->perPage)
            ->map(fn (Product $p) => [
                'id'              => $p->id,
                'name'            => $p->name,
                'description'     => $p->description,
                'image'           => $p->image ? Storage::url($p->image) : null,
                'price'           => $p->price,
                'formatted_price' => $p->formatted_price,
                'category'        => $p->category?->name ?? '',
                'is_active'       => $p->is_active,
                'has_variants'    => $p->has_variants,
                'selection_type'  => $p->selection_type ?? 'single',
                'max_selections'  => $p->max_selections ?? 1,
                'variants'        => $p->variants->map(fn ($v) => [
                    'id'    => $v->id,
                    'name'  => $v->name,
                    'price' => $v->price,
                ])->toArray(),
            ])
            ->toArray();
    }
};
