<?php

use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    public ?int $activeCategoryId = null;

    public function loadProducts($categoryId): void
    {
        if ($this->activeCategoryId == $categoryId) {
            $this->activeCategoryId = null;
        } else {
            $this->activeCategoryId = $categoryId;
        }
    }

    public function toggleAvailability($productId): void
    {
        $product = Product::findOrFail($productId);
        $product->update(['is_active' => !$product->is_active]);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Status ' . $product->name . ' berhasil diubah.'
        ]);
    }

    public function deleteProduct($productId): void
    {
        Product::findOrFail($productId)->delete();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Produk berhasil dihapus.']);
    }

    public function deleteCategory($categoryId): void
    {
        $category = Category::withCount('products')->findOrFail($categoryId);

        if ($category->products_count > 0) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal! Hapus semua produk di kategori ini dulu.']);
            return;
        }

        $category->delete();
        $this->activeCategoryId = null;
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Kategori dihapus.']);
    }

    #[On('category-saved')]
    public function refreshTable(): void
    {
    }

    public function with(): array
    {
        $categories = Category::withCount('products')
            ->orderBy('order_column')
            ->get();

        $loadedProducts = [];
        if ($this->activeCategoryId) {
            $loadedProducts = Product::with('variants')
                ->where('category_id', $this->activeCategoryId)
                ->get();
        }

        return [
            'categories' => $categories,
            'loadedProducts' => $loadedProducts
        ];
    }
};
