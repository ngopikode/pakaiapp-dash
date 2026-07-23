<?php

use App\Tenant\Models\Core\Category;
use App\Tenant\Models\Core\Product;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

new class extends Component {

    #[Url] public string $search = '';
    #[Url] public string $filterCategory = '';
    #[Url] public string $filterStatus = '';
    #[Url] public string $filterPrice = '';
    #[Url] public string $sortField = 'newest'; // newest, price_asc, price_desc, stock_asc, stock_desc
    
    public int $perPage = 20;
    public array $selected = [];
    public bool $selectAll = false;

    public function updatedSelectAll($value): void
    {
        if ($value) {
            $this->selected = $this->getFilteredQuery()->pluck('products.id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function updatingSearch(): void { $this->perPage = 20; $this->selected = []; $this->selectAll = false; }
    public function updatingFilterCategory(): void { $this->perPage = 20; $this->selected = []; $this->selectAll = false; }
    public function updatingFilterStatus(): void { $this->perPage = 20; $this->selected = []; $this->selectAll = false; }
    public function updatingFilterPrice(): void { $this->perPage = 20; $this->selected = []; $this->selectAll = false; }
    public function updatingSortField(): void { $this->perPage = 20; $this->selected = []; $this->selectAll = false; }

    public function loadMore(): void
    {
        $this->perPage += 20;
    }

    public function toggleAvailability(Product $product): void
    {
        $product->update(['is_active' => !$product->is_active]);
        $this->dispatch('notify', ['type' => 'success', 'message' => "Status {$product->name} berhasil diubah."]);
    }

    public function deleteProduct(Product $product): void
    {
        $product->delete();
        $this->selected = array_diff($this->selected, [(string)$product->id]);
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Produk berhasil dihapus.']);
    }

    public function bulkDelete(): void
    {
        if (empty($this->selected)) return;
        Product::whereIn('id', $this->selected)->delete();
        $this->selected = [];
        $this->selectAll = false;
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Produk terpilih berhasil dihapus.']);
    }

    public function bulkToggleStatus(bool $status): void
    {
        if (empty($this->selected)) return;
        Product::whereIn('id', $this->selected)->update(['is_active' => $status]);
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Status produk terpilih berhasil diubah.']);
    }

    public function deleteCategory(Category $category): void
    {
        $category->loadCount('products');
        if ($category->products_count > 0) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal! Hapus semua produk di kategori ini dulu.']);
            return;
        }
        $category->delete();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Kategori dihapus.']);
    }

    #[On('category-saved')]
    #[On('product-saved')]
    public function refreshTable(): void { $this->perPage = 20; }

    private function getFilteredQuery()
    {
        $query = Product::with(['category', 'variants'])
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->select('products.*', 'categories.order_column as category_order');

        // Text Search
        if ($this->search) {
            $query->where(fn ($q) => $q->where('products.name', 'like', "%{$this->search}%")->orWhere('products.description', 'like', "%{$this->search}%"));
        }

        // Category & Status Filter
        if ($this->filterCategory) $query->where('products.category_id', $this->filterCategory);
        if ($this->filterStatus === 'active') $query->where('products.is_active', true);
        if ($this->filterStatus === 'inactive') $query->where('products.is_active', false);

        // Price Filter
        if ($this->filterPrice) {
            $range = explode('-', $this->filterPrice);
            if (count($range) === 2) {
                $query->whereHas('variants', fn($q) => $q->whereBetween('price', [(int)$range[0], (int)$range[1]]));
            } elseif ($this->filterPrice === 'above-100k') {
                $query->whereHas('variants', fn($q) => $q->where('price', '>=', 100000));
            }
        }

        // Sorting
        switch ($this->sortField) {
            case 'price_asc':
                $query->selectRaw('(SELECT MIN(price) FROM product_variants WHERE product_variants.product_id = products.id) as min_price')
                      ->orderBy('min_price', 'asc');
                break;
            case 'price_desc':
                $query->selectRaw('(SELECT MIN(price) FROM product_variants WHERE product_variants.product_id = products.id) as min_price')
                      ->orderBy('min_price', 'desc');
                break;
            case 'stock_asc':
                $query->selectRaw('(SELECT SUM(stock) FROM product_variants WHERE product_variants.product_id = products.id) as total_stock')
                      ->orderBy('total_stock', 'asc');
                break;
            case 'stock_desc':
                $query->selectRaw('(SELECT SUM(stock) FROM product_variants WHERE product_variants.product_id = products.id) as total_stock')
                      ->orderBy('total_stock', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('products.name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('products.name', 'desc');
                break;
            case 'category':
                $query->orderByRaw('ISNULL(categories.order_column), categories.order_column ASC')->orderBy('products.id', 'desc');
                break;
            default: // newest
                $query->orderBy('products.id', 'desc');
                break;
        }

        return $query;
    }

    public function with(): array
    {
        $paginator = $this->getFilteredQuery()->paginate($this->perPage);

        return [
            'categories' => Category::orderBy('order_column')->get(),
            'products' => collect($paginator->items()),
            'hasMore' => $paginator->hasMorePages(),
        ];
    }
};
