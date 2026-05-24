<?php

use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    public string $search = '';
    public string $categoryFilter = 'all';
    public int $limit = 12;

    /**
     * Re-render when stock is updated (after checkout/order).
     */
    #[On('stock-updated')]
    public function stockUpdated(): void
    {
        // No-op: just triggers a re-render so product data refreshes from DB
    }


    public function updatedSearch(): void
    {
        $this->limit = 12;
    }

    public function updatedCategoryFilter(): void
    {
        $this->limit = 12;
    }

    public function loadMore(): void
    {
        $this->limit += 8;
    }

    public function with(): array
    {
        $query = Product::with(['variants:id,product_id,name,cost,price,stock'])
            ->when(tenant('store_type') === 'resto')->with(['extras' => fn($q) => $q->where('is_active', true)])
            ->where('is_active', true)
            ->when($this->search, fn($q) => $q->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhereHas('variants', fn($v) => $v->where('sku', 'like', '%' . $this->search . '%'));
            }))
            ->when($this->categoryFilter !== 'all', fn($q) => $q->where('category_id', $this->categoryFilter));

        $totalCount = (clone $query)->count();

        $products = $query->take($this->limit)->get()->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'category_id' => $p->category_id,
                'image_url' => $p->image ? Storage::url($p->image) : null,
                'has_variants' => (bool)$p->has_variants,
                'selection_type' => $p->selection_type ?? 'single',
                'max_selections' => (int)($p->max_selections ?? 1),

                // Ambil harga terendah & total stok untuk tampilan grid
                'price' => (float)$p->variants->min('price'),
                'stock' => (int)$p->variants->sum('stock'),

                // Kirim semua varian ke Frontend (Alpine)
                'variants' => $p->variants->map(function ($v) {
                    return [
                        'id' => $v->id,
                        'name' => $v->name,
                        'cost' => (float)$v->cost,
                        'price' => (float)$v->price,
                        'stock' => (int)$v->stock,
                    ];
                })->toArray(),

                // Kirim semua extra/add-on ke Frontend (Alpine)
                'extras' => tenant('store_type') === 'resto'
                    ? $p->extras->map(function ($e) {
                        return [
                            'id' => $e->id,
                            'name' => $e->name,
                            'price' => (float)$e->price,
                            'is_active' => (bool)$e->is_active,
                        ];
                    })->toArray()
                    : []
            ];
        });

        return [
            'categories' => Category::orderBy('order_column')->get(),
            'products' => $products,
            'hasMore' => $this->limit < $totalCount
        ];
    }
};
