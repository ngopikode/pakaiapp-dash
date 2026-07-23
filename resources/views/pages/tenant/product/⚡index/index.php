<?php

use App\Tenant\Models\Core\Category;
use App\Tenant\Models\Core\Product;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

new class extends Component
{
    #[Url]
    public string $search = '';

    #[Url]
    public string $filterCategory = '';

    #[Url]
    public string $filterStatus = '';

    #[Url]
    public string $filterPrice = '';

    #[Url]
    public string $sortField = 'newest'; // newest, price_asc, price_desc, stock_asc, stock_desc

    public int $perPage = 20;

    public function updating($property): void
    {
        if (in_array($property, ['search', 'filterCategory', 'filterStatus', 'filterPrice', 'sortField'])) {
            $this->perPage = 20;
        }
    }

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
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Produk berhasil dihapus.']);
        $this->dispatch('product-deleted', ['id' => $product->id]);
    }

    public function bulkDelete(array $ids): void
    {
        if (empty($ids)) return;
        Product::whereIn('id', $ids)->delete();
        $this->dispatch('notify', ['type' => 'success', 'message' => count($ids) . ' Produk terpilih berhasil dihapus.']);
        $this->dispatch('clear-selection');
    }

    public function bulkToggleStatus(array $ids, bool $status): void
    {
        if (empty($ids)) return;
        Product::whereIn('id', $ids)->update(['is_active' => $status]);
        $this->dispatch('notify', ['type' => 'success', 'message' => count($ids) . ' Status produk terpilih berhasil diubah.']);
        $this->dispatch('clear-selection');
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
    public function refreshTable(): void
    {
        $this->perPage = 20;
    }

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
                $query->whereHas('variants', fn ($q) => $q->whereBetween('price', [(int) $range[0], (int) $range[1]]));
            } elseif ($this->filterPrice === 'above-100k') {
                $query->whereHas('variants', fn ($q) => $q->where('price', '>=', 100000));
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
                $query->orderBy('products.id', 'asc');
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
