<?php

use App\Models\Category;
use App\Models\StoreSetting;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    public ?int $categoryId = null;
    public string $name = '';
    public string $type = 'retail';
    public bool $isEditing = false;

    // Ambil tipe toko saat komponen dimuat
    public function mount(): void
    {
        $setting = StoreSetting::first();
        if ($setting) $this->type = $setting->store_type;
    }

    #[On('openModal')]
    public function handleOpenModal($type, $mode, $id = null): void
    {
        if ($type !== 'category') return;

        $this->resetValidation();
        $this->reset(['categoryId', 'name']);

        // Pastikan tipe tetap ke-lock setiap kali modal dibuka
        $setting = StoreSetting::first();
        $this->type = $setting ? $setting->store_type : 'retail';

        $this->isEditing = ($mode === 'edit');

        if ($this->isEditing && $id) {
            $category = Category::find($id);
            if ($category) {
                $this->categoryId = $category->id;
                $this->name = $category->name;
                // $this->type sengaja tidak di-override dari kategori lama untuk keamanan
            }
        }

        $this->dispatch('show-category-modal');
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'Nama kategori wajib diisi, Bro.',
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
                'type' => $this->type, // Otomatis pakai tipe toko yang di-lock
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
