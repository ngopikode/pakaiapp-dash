<?php

use App\Tenant\Data\ProductFilterData;
use App\Tenant\Models\Core\Category;
use App\Tenant\Models\Core\Product;
use App\Tenant\Services\CategoryService;
use App\Tenant\Services\ProductService;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

new class extends Component {
    protected ?ProductService $productService = null;

    protected ?CategoryService $categoryService = null;

    #[Url]
    public string $search = '';

    #[Url]
    public string $filterCategory = '';

    #[Url]
    public string $filterStatus = '';

    #[Url]
    public string $filterPrice = '';

    #[Url]
    public string $sortField = 'newest';

    public int $perPage = 20;

    protected function productService(): ProductService
    {
        return $this->productService ??= app(ProductService::class);
    }

    protected function categoryService(): CategoryService
    {
        return $this->categoryService ??= app(CategoryService::class);
    }

    public function updating($property): void
    {
        if (
            in_array($property, ['search', 'filterCategory', 'filterStatus', 'filterPrice', 'sortField'])
        ) $this->perPage = 20;
    }

    public function loadMore(): void
    {
        $this->perPage += 20;
    }

    public function toggleAvailability(Product $product): void
    {
        $this->productService()->toggleAvailability($product);
        $this->dispatch('notify', ['type' => 'success', 'message' => "Status $product->name berhasil diubah."]);
    }

    public function deleteProduct(Product $product): void
    {
        $this->productService()->delete($product);
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Produk berhasil dihapus.']);
        $this->dispatch('product-deleted', ['id' => $product->id]);
    }

    public function bulkDelete(array $ids): void
    {
        if (empty($ids)) return;
        $this->productService()->bulkDelete($ids);
        $this->dispatch('notify', ['type' => 'success', 'message' => count($ids) . ' Produk terpilih berhasil dihapus.']);
        $this->dispatch('clear-selection');
    }

    public function bulkToggleStatus(array $ids, bool $status): void
    {
        if (empty($ids)) return;
        $this->productService()->bulkToggleStatus($ids, $status);
        $this->dispatch('notify', ['type' => 'success', 'message' => count($ids) . ' Status produk terpilih berhasil diubah.']);
        $this->dispatch('clear-selection');
    }

    public function deleteCategory(Category $category): void
    {
        try {
            $this->categoryService()->delete($category);
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Kategori dihapus.']);
        } catch (Exception $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    #[On('category-saved')]
    #[On('product-saved')]
    public function refreshTable(): void
    {
        $this->perPage = 20;
    }

    public function with(): array
    {
        $filter = new ProductFilterData(
            search: $this->search,
            filterCategory: $this->filterCategory,
            filterStatus: $this->filterStatus,
            filterPrice: $this->filterPrice,
            sortField: $this->sortField,
            perPage: $this->perPage,
        );

        $paginator = $this->productService()->getFilteredQuery($filter)->paginate($this->perPage);

        return [
            'categories' => Category::orderBy('order_column')->get(),
            'products' => collect($paginator->items()),
            'hasMore' => $paginator->hasMorePages(),
        ];
    }
};
