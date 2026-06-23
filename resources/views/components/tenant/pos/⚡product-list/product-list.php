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

    public function handleEnter($value = null): void
    {
        $searchTerm = trim($value ?? $this->search);
        if (empty($searchTerm)) return;

        $this->search = $searchTerm;

        $matchedVariantId = null;

        // 1. Cek Exact SKU Match
        $product = Product::with(['variants:id,product_id,sku,name,cost,price,stock'])
            ->when(tenant('store_type') === 'resto')->with(['extras' => fn($q) => $q->where('is_active', true)])
            ->where('is_active', true)
            ->whereHas('variants', fn($q) => $q->where('sku', $searchTerm))
            ->first();

        if ($product) {
            $matchedVariant = $product->variants->where('sku', $searchTerm)->first();
            if ($matchedVariant) {
                $matchedVariantId = $matchedVariant->id;
            }
        }

        // 2. Jika bukan SKU exact, cek apakah query memfilter daftar menjadi TEPAT 1 produk
        if (!$product) {
            $words = array_filter(explode(' ', $searchTerm));
            $query = Product::with(['variants:id,product_id,sku,name,cost,price,stock'])
                ->when(tenant('store_type') === 'resto')->with(['extras' => fn($q) => $q->where('is_active', true)])
                ->where('is_active', true)
                ->where(function ($q) use ($words, $searchTerm) {
                    $q->where('name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('description', 'like', '%' . $searchTerm . '%')
                        ->orWhereHas('variants', fn($v) => 
                            $v->where('sku', 'like', '%' . $searchTerm . '%')
                              ->orWhere('name', 'like', '%' . $searchTerm . '%')
                        );

                    if (count($words) > 1) {
                        $q->orWhere(function ($multiWordQuery) use ($words) {
                            foreach ($words as $word) {
                                $multiWordQuery->where(function ($wordQuery) use ($word) {
                                    $wordQuery->where('name', 'like', '%' . $word . '%')
                                        ->orWhere('description', 'like', '%' . $word . '%')
                                        ->orWhereHas('variants', fn($v) => 
                                            $v->where('sku', 'like', '%' . $word . '%')
                                              ->orWhere('name', 'like', '%' . $word . '%')
                                        );
                                });
                            }
                        });
                    }
                });

            if ($query->count() === 1) {
                $product = $query->first();
            }
        }

        if ($product) {
            $formattedProduct = [
                'id' => $product->id,
                'name' => $product->name,
                'category_id' => $product->category_id,
                'image_url' => $product->image ? \Storage::url($product->image) : null,
                'has_variants' => (bool)$product->has_variants,
                'selection_type' => $product->selection_type ?? 'single',
                'max_selections' => (int)($product->max_selections ?? 1),
                'price' => (float)$product->variants->min('price'),
                'active_discount_price' => $product->variants->min('active_discount_price') ? (float)$product->variants->min('active_discount_price') : null,
                'active_discount_name' => $product->variants->firstWhere('active_discount_name', '!=', null)?->active_discount_name,
                'stock' => (int)$product->variants->sum('stock'),
                'variants' => $product->variants->map(function ($v) {
                    return [
                        'id' => $v->id,
                        'name' => $v->name,
                        'sku' => $v->sku,
                        'cost' => (float)$v->cost,
                        'price' => (float)$v->price,
                        'active_discount_price' => $v->active_discount_price ? (float)$v->active_discount_price : null,
                        'active_discount_name' => $v->active_discount_name,
                        'stock' => (int)$v->stock,
                    ];
                })->toArray(),
                'extras' => tenant('store_type') === 'resto'
                    ? $product->extras->map(function ($e) {
                        return [
                            'id' => $e->id,
                            'name' => $e->name,
                            'price' => (float)$e->price,
                            'is_active' => (bool)$e->is_active,
                        ];
                    })->toArray()
                    : []
            ];

            $this->dispatch('add-product', product: $formattedProduct, variantId: $matchedVariantId);
            $this->search = ''; 
        }
    }

    public function with(): array
    {
        $query = Product::with(['variants:id,product_id,sku,name,cost,price,stock'])
            ->when(tenant('store_type') === 'resto')->with(['extras' => fn($q) => $q->where('is_active', true)])
            ->where('is_active', true)
            ->when($this->search, function ($q) {
                $searchTerm = trim($this->search);
                $words = array_filter(explode(' ', $searchTerm));

                $q->where(function ($query) use ($words, $searchTerm) {
                    // 1. Exact Phrase Match (Fastest for Barcode/SKU or exact name)
                    $query->where('name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('description', 'like', '%' . $searchTerm . '%')
                        ->orWhereHas('variants', fn($v) => 
                            $v->where('sku', 'like', '%' . $searchTerm . '%')
                              ->orWhere('name', 'like', '%' . $searchTerm . '%')
                        );

                    // 2. Multi-word Match (e.g. "Milo Jumbo" -> searches name & variant name)
                    if (count($words) > 1) {
                        $query->orWhere(function ($multiWordQuery) use ($words) {
                            foreach ($words as $word) {
                                $multiWordQuery->where(function ($wordQuery) use ($word) {
                                    $wordQuery->where('name', 'like', '%' . $word . '%')
                                        ->orWhere('description', 'like', '%' . $word . '%')
                                        ->orWhereHas('variants', fn($v) => 
                                            $v->where('sku', 'like', '%' . $word . '%')
                                              ->orWhere('name', 'like', '%' . $word . '%')
                                        );
                                });
                            }
                        });
                    }
                });
            })
            ->when($this->categoryFilter !== 'all' && empty($this->search), fn($q) => $q->where('category_id', $this->categoryFilter));

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
                'active_discount_price' => $p->variants->min('active_discount_price') ? (float)$p->variants->min('active_discount_price') : null,
                'active_discount_name' => $p->variants->firstWhere('active_discount_name', '!=', null)?->active_discount_name,
                'stock' => (int)$p->variants->sum('stock'),

                // Kirim semua varian ke Frontend (Alpine)
                'variants' => $p->variants->map(function ($v) {
                    return [
                        'id' => $v->id,
                        'name' => $v->name,
                        'sku' => $v->sku,
                        'cost' => (float)$v->cost,
                        'price' => (float)$v->price,
                        'active_discount_price' => $v->active_discount_price ? (float)$v->active_discount_price : null,
                        'active_discount_name' => $v->active_discount_name,
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
