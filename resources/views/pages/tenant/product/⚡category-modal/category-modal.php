<?php

namespace App\Livewire\Tenant\Product;

use App\Models\Category;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    public $categoryId = null;
    public $name = '';
    public $type = 'retail'; // Default retail
    public $isEditing = false;

    // Menangkap event dispatch dari tombol "Tambah Kategori" atau "Edit Kategori"
    #[On('openModal')]
    public function handleOpenModal($type, $mode, $id = null)
    {
        // Pastikan modal ini hanya merespon untuk 'category'
        if ($type !== 'category') return;

        $this->resetValidation();
        $this->reset(['categoryId', 'name']);
        $this->type = 'retail';
        $this->isEditing = ($mode === 'edit');

        if ($this->isEditing && $id) {
            $category = Category::find($id);
            if ($category) {
                $this->categoryId = $category->id;
                $this->name = $category->name;
                $this->type = $category->type;
            }
        }

        // Buka modal Bootstrap via Alpine.js
        $this->dispatch('show-category-modal');
        $this->dispatch('show-bootstrap-modal');
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:retail,fnb',
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
        ]);

        // Auto generate slug
        $slug = Str::slug($this->name);

        // Mencegah slug duplikat sederhana
        if (!$this->isEditing && Category::where('slug', $slug)->exists()) {
            $slug = $slug . '-' . time();
        }

        Category::updateOrCreate(
            ['id' => $this->categoryId],
            [
                'name' => $this->name,
                'slug' => $slug,
                'type' => $this->type,
            ]
        );

        // Tutup modal
        $this->dispatch('hide-category-modal');

        // Beri tahu halaman Index untuk me-refresh data
        $this->dispatch('category-saved');

        // Tampilkan notifikasi
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Kategori berhasil ' . ($this->isEditing ? 'diperbarui' : 'ditambahkan') . ' ☕'
        ]);
    }
};
