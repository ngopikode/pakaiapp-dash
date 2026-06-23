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

    public function loadMore(): void
    {
        $this->perPage += 10;
    }

    #[Computed]
    public function hasMore(): bool
    {
        $total = Product::query()
            ->when(
                $this->category !== 'all',
                fn($q) => $q->whereHas('category', fn($q2) => $q2->where('name', $this->category))
            )
            ->count();

        return $total > $this->perPage;
    }

    #[Computed]
    public function products(): array
    {
        if ($this->lazy) return [];
        return Product::query()
            ->with(['category', 'variants'])
            ->when(
                $this->category !== 'all',
                fn($q) => $q->whereHas('category', fn($q2) => $q2->where('name', $this->category))
            )
            ->orderByRaw('is_active DESC') 
            ->take($this->perPage)
            ->get()
            ->map(fn(Product $p) => [
                'id' => $p->id,
                'name' => $p->name,
                'description' => $p->description,
                'image' => $p->image ? Storage::url($p->image) : null,
                'price' => $p->price,
                'formatted_price' => $p->formatted_price,
                'active_discount_price' => $p->variants->min('active_discount_price'),
                'active_discount_name' => $p->variants->firstWhere('active_discount_name', '!=', null)?->active_discount_name,
                'category' => $p->category?->name ?? '',
                'is_active' => $p->is_active,
                'has_variants' => $p->has_variants,
                'selection_type' => $p->selection_type ?? 'single',
                'max_selections' => $p->max_selections ?? 1,
                'variants' => $p->variants->map(fn($v) => [
                    'id' => $v->id,
                    'name' => $v->name,
                    'price' => $v->price,
                    'active_discount_price' => $v->active_discount_price,
                    'active_discount_name' => $v->active_discount_name,
                ])->toArray(),
            ])
            ->toArray();
    }
};
