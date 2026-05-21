<?php

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
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
        // Hitung total data aktual biar spinner/skeleton otomatis hilang kalau data abis
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
        return Product::query()
            ->with(['category', 'variants', 'extras'])
            ->when(
                $this->category !== 'all',
                fn($q) => $q->whereHas('category', fn($q2) => $q2->where('name', $this->category))
            )
            ->orderByRaw('is_active DESC') // produk aktif di atas, habis/nonaktif di bawah
            ->take($this->perPage)
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
