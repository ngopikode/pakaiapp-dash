<?php

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    public bool $lazy = false;
    public string $category = 'all';
    public int $perPage = 10;
    public array $categories = [];
    public string $sort = 'popular';
    public ?int $minPrice = null;
    public ?int $maxPrice = null;
    public string $search = '';

    public function mount(): void
    {
        $this->categories = Category::orderBy('order_column')
            ->pluck('name')
            ->toArray();
    }

    public function setCategory(string $category): void
    {
        $this->category = $category;
        $this->perPage = 10;
    }

    public function setSort(string $sort): void
    {
        $this->sort = $sort;
        $this->perPage = 10;
    }

    public function resetFilters(): void
    {
        $this->sort = 'popular';
        $this->category = 'all';
        $this->minPrice = null;
        $this->maxPrice = null;
        $this->search = '';
        $this->perPage = 10;
    }

    public function applyFilters(): void
    {
        $this->perPage = 10;
        // Optional logic you might want when 'Apply Filters' is clicked
    }

    public function loadMore(): void
    {
        $this->perPage += 10;
    }

    #[Computed]
    public function hasMore(): bool
    {
        $query = Product::query()
            ->when(
                $this->category !== 'all',
                fn($q) => $q->whereHas('category', fn($q2) => $q2->where('name', $this->category))
            )
            ->when(
                $this->search !== '',
                fn($q) => $q->where('name', 'like', '%' . $this->search . '%')
            );

        if ($this->minPrice !== null || $this->maxPrice !== null) {
            $query->whereHas('variants', function ($q) {
                if ($this->minPrice !== null) $q->where('price', '>=', $this->minPrice);
                if ($this->maxPrice !== null) $q->where('price', '<=', $this->maxPrice);
            });
        }

        return $query->count() > $this->perPage;
    }

    #[Computed]
    public function products(): array
    {
        if ($this->lazy) return [];
        $query = Product::query()
            ->with(['category', 'variants', 'extras'])
            ->when(
                $this->category !== 'all',
                fn($q) => $q->whereHas('category', fn($q2) => $q2->where('name', $this->category))
            )
            ->when(
                $this->search !== '',
                fn($q) => $q->where('name', 'like', '%' . $this->search . '%')
            );

        if ($this->minPrice !== null || $this->maxPrice !== null) {
            $query->whereHas('variants', function ($q) {
                if ($this->minPrice !== null) $q->where('price', '>=', $this->minPrice);
                if ($this->maxPrice !== null) $q->where('price', '<=', $this->maxPrice);
            });
        }

        if ($this->sort === 'newest') {
            $query->orderBy('created_at', 'desc');
        } elseif ($this->sort === 'lowest_price') {
            $query->withMin('variants', 'price')->orderBy('variants_min_price', 'asc');
        } elseif ($this->sort === 'highest_price') {
            $query->withMin('variants', 'price')->orderBy('variants_min_price', 'desc');
        } else {
            $query->orderByRaw('is_active DESC');
        }

        return $query->take($this->perPage)
            ->get()
            ->map(fn(Product $p) => [
                'id' => $p->id,
                'name' => $p->name,
                'description' => $p->description,
                'image' => $p->image ? Storage::url($p->image) : null,
                'price' => $p->price,
                'formatted_price' => $p->formatted_price,
                'category' => $p->category?->name ?? '',
                'is_active' => $p->is_active,
                'has_variants' => $p->has_variants,
                'selection_type' => $p->selection_type ?? 'single',
                'max_selections' => $p->max_selections ?? 1,
                'variants' => $p->variants->map(fn($v) => [
                    'id' => $v->id,
                    'name' => $v->name,
                    'price' => $v->price,
                ])->toArray(),
                'extras' => $p->extras->where('is_active', true)->map(fn($e) => [
                    'id' => $e->id,
                    'name' => $e->name,
                    'price' => $e->price,
                ])->values()->toArray(),
            ])
            ->toArray();
    }
};
