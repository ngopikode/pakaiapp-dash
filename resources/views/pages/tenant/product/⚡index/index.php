<?php

namespace App\Livewire\Tenant\Product;

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;

new class extends Component {
    public $activeCategoryId = null;

    // Buka/Tutup Accordion Kategori
    public function loadProducts($categoryId)
    {
        if ($this->activeCategoryId == $categoryId) {
            $this->activeCategoryId = null; // Tutup jika diklik lagi
        } else {
            $this->activeCategoryId = $categoryId; // Buka dan load
        }
    }

    // Ubah status Aktif/Nonaktif (Dulu is_available, sekarang is_active)
    public function toggleAvailability($productId)
    {
        $product = Product::findOrFail($productId);
        $product->update(['is_active' => !$product->is_active]);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Status ' . $product->name . ' berhasil diubah ☕'
        ]);
    }

    public function deleteProduct($productId)
    {
        Product::findOrFail($productId)->delete();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Produk berhasil dihapus.']);
    }

    public function deleteCategory($categoryId)
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

    public function with(): array
    {
        // 1. Load kategori beserta jumlah produknya
        // Catatan: Jika ada logic multi-tenant (auth user), tambahkan ->where('tenant_id', ...)
        $categories = Category::withCount('products')
            ->orderBy('order_column', 'asc')
            ->get();

        // 2. Load produk HANYA untuk kategori yang sedang diklik (Eager Load Variants!)
        $loadedProducts = [];
        if ($this->activeCategoryId) {
            $loadedProducts = Product::with('variants') // <-- KUNCI OPTIMASI SERVER MINI
            ->where('category_id', $this->activeCategoryId)
                // ->orderBy('order_column', 'asc') // Aktifkan jika nanti ada fitur urut produk
                ->get();
        }

        return [
            'categories' => $categories,
            'loadedProducts' => $loadedProducts
        ];
    }
};
