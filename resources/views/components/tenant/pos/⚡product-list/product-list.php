<?php

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;

new class extends Component {
    public string $search = '';
    public string $categoryFilter = 'all';
    public int $limit = 12;

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
        $query = Product::with('variants:id,product_id,name,cost,price,stock')
            ->where('is_active', true)
            ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->when($this->categoryFilter !== 'all', fn($q) => $q->where('category_id', $this->categoryFilter));

        $totalCount = (clone $query)->count();

        $products = $query->take($this->limit)->get()->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'category_id' => $p->category_id,
                'image_url' => $p->image ? asset('storage/' . $p->image) : null,
                'has_variants' => (bool)$p->has_variants,

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
                })->toArray()
            ];
        });

        return [
            'categories' => Category::orderBy('order_column')->get(),
            'products' => $products,
            'hasMore' => $this->limit < $totalCount
        ];
    }
};
