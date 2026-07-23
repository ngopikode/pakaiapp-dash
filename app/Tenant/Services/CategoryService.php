<?php

namespace App\Tenant\Services;

use App\Tenant\Data\CategoryData;
use App\Tenant\Models\Core\Category;
use Illuminate\Support\Str;

class CategoryService
{
    public function save(CategoryData $data): Category
    {
        $slug = Str::slug($data->name);

        if (!$data->id && Category::where('slug', $slug)->exists()) {
            $slug = $slug . '-' . time();
        }

        return Category::updateOrCreate(
            ['id' => $data->id],
            [
                'name' => $data->name,
                'slug' => $slug,
                'type' => $data->type,
            ]
        );
    }

    public function delete(Category $category): void
    {
        $category->loadCount('products');
        if ($category->products_count > 0) {
            throw new \Exception('Hapus semua produk di kategori ini dulu.');
        }
        $category->delete();
    }
}
