<?php

use App\Tenant\Models\Core\Category;
use App\Tenant\Models\Core\Product;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    #[Url] public string $search = '';
    #[Url] public string $filterCategory = '';
    #[Url] public string $filterStatus = '';

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingFilterCategory(): void { $this->resetPage(); }
    public function updatingFilterStatus(): void { $this->resetPage(); }

    public function toggleAvailability(Product $product): void
    {
        $product->update(['is_active' => !$product->is_active]);
        $this->dispatch('notify', ['type' => 'success', 'message' => "Status {$product->name} berhasil diubah."]);
    }

    public function deleteProduct(Product $product): void
    {
        $product->delete();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Produk berhasil dihapus.']);
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
    public function refreshTable(): void { $this->resetPage(); }

    public function with(): array
    {
        $query = Product::with(['category', 'variants'])->orderBy('id', 'desc');

        if ($this->search) $query->where(fn ($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('description', 'like', "%{$this->search}%"));
        if ($this->filterCategory) $query->where('category_id', $this->filterCategory);
        if ($this->filterStatus === 'active') $query->where('is_active', true);
        if ($this->filterStatus === 'inactive') $query->where('is_active', false);

        return [
            'categories' => Category::orderBy('order_column')->get(),
            'products' => $query->paginate(12),
        ];
    }
};
