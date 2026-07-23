<?php

use App\Tenant\Data\CategoryData;
use App\Tenant\Models\Core\Category;
use App\Tenant\Models\Core\StoreSetting;
use App\Tenant\Services\CategoryService;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    protected ?CategoryService $categoryService = null;

    public ?int $categoryId = null;

    public string $name = '';

    public string $type = 'retail';

    public bool $isEditing = false;

    protected function categoryService(): CategoryService
    {
        return $this->categoryService ??= app(CategoryService::class);
    }

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

        $setting = StoreSetting::first();
        $this->type = $setting ? $setting->store_type : 'retail';

        $this->isEditing = ($mode === 'edit');

        if ($this->isEditing && $id) {
            $category = Category::find($id);
            if ($category) {
                $this->categoryId = $category->id;
                $this->name = $category->name;
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

        $this->categoryService()->save(
            new CategoryData(
                name: $this->name,
                type: $this->type,
                id: $this->categoryId,
            )
        );

        $this->dispatch('hide-category-modal');
        $this->dispatch('category-saved');
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Kategori berhasil ' . ($this->isEditing ? 'diperbarui' : 'ditambahkan') . ' ☕',
        ]);
    }
};
