<?php

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;

new class extends Component {
    public string $search = '';
    public string $categoryFilter = 'all';
    public int $limit = 12; // Awal load 12 produk

    public function updatedSearch(): void
    {
        $this->limit = 12; // Reset limit kalau lagi ngetik
    }

    public function updatedCategoryFilter(): void
    {
        $this->limit = 12; // Reset limit kalau pindah kategori
    }

    public function loadMore(): void
    {
        $this->limit += 8; // Nambah 8 produk tiap kali di-scroll ke bawah
    }

    public function with(): array
    {
        $query = Product::with('variants')
            ->where('is_active', true)
            ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->when($this->categoryFilter !== 'all', fn($q) => $q->where('category_id', $this->categoryFilter));

        $totalCount = (clone $query)->count();

        $products = $query->take($this->limit)->get()->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'category_id' => $p->category_id,
                'price' => (float)$p->base_price,
                'stock' => (int)$p->stock,
                'image_url' => $p->image ? asset('storage/' . $p->image) : null,
                'has_variants' => (bool)$p->has_variants,
                'variants' => $p->variants->map(function ($v) {
                    return [
                        'id' => $v->id,
                        'name' => $v->name,
                        'price' => (float)$v->price,
                        'stock' => (int)$v->stock,
                    ];
                })->toArray()
            ];
        });

        return [
            'categories' => Category::orderBy('order_column')->get(),
            'products' => $products,
            'hasMore' => $this->limit < $totalCount // Flag buat nampilin spinner di bawah
        ];
    }
};
