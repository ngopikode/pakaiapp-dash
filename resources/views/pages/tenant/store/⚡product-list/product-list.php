<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\Product;
use App\Models\Category;

new class extends Component
{
    public string $category = 'all';
    public string $viewMode = 'grid';
    public int $perPage = 10;
    public bool $hasMore = true;

    public function setCategory(string $categoryName): void
    {
        $this->category = $categoryName;
        $this->perPage = 10;
        $this->hasMore = true;
    }

    public function setViewMode(string $mode): void
    {
        $this->viewMode = $mode;
    }

    public function loadMore(): void
    {
        $this->perPage += 10;
    }

    #[Computed]
    public function categories(): array
    {
        return Category::orderBy('name')->pluck('name')->toArray();
    }

    #[Computed]
    public function products()
    {
        $query = Product::query()->with(['category', 'variants']);

        if ($this->category !== 'all') {
            $query->whereHas('category', fn($q) => $q->where('name', $this->category));
        }

        $total = $query->count();
        $items = $query->take($this->perPage)->get();

        $this->hasMore = count($items) < $total;

        return $items->map(function ($p) {
            return [
                'id'              => $p->id,
                'name'            => $p->name,
                'description'     => $p->description,
                'image'           => $p->image ? \Illuminate\Support\Facades\Storage::url($p->image) : null,
                'price'           => $p->price,
                'formatted_price' => $p->formatted_price,
                'category'        => $p->category?->name ?? '',
                'is_active'       => $p->is_active,
                'has_variants'    => $p->has_variants,
                'selection_type'  => $p->selection_type ?? 'single',
                'max_selections'  => $p->max_selections ?? 1,
                'variants'        => $p->variants->map(fn($v) => [
                    'id'    => $v->id,
                    'name'  => $v->name,
                    'price' => $v->price,
                ])->toArray(),
            ];
        })->toArray();
    }
};