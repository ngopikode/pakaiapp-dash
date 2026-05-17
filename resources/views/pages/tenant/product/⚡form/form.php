<?php

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public ?Product $product = null;

    public array $categories = [];
    public string $selectedCategoryType = 'retail';

    public string $name = '';
    public string $categoryId = '';
    public string $description = '';
    public $image;
    public bool $taxIncluded = false;
    public bool $isActive = true;

    public bool $hasVariants = false;
    public string $selectionType = 'single';
    public int $maxSelections = 1;
    public float $baseCost = 0;
    public float $basePrice = 0;
    public int $baseStock = 0;
    public int $baseMinStock = 0;

    public array $variants = [];
    public array $extras = [];

    public function mount(?Product $product = null): void
    {
        $this->categories = Category::select('id', 'name', 'type')->orderBy('name')->get()->toArray();

        if ($product && $product->exists) {
            $this->product = $product;
            $this->name = $product->name;
            $this->categoryId = $product->category_id;
            $this->description = $product->description ?? '';
            $this->taxIncluded = $product->tax_included;
            $this->isActive = $product->is_active;
            $this->hasVariants = $product->has_variants;
            $this->selectionType = $product->selection_type ?? 'single';
            $this->maxSelections = $product->max_selections ?? 1;

            $this->updatedCategoryId($this->categoryId);

            if ($this->hasVariants) {
                foreach ($product->variants as $variant) {
                    $this->variants[] = [
                        'id' => $variant->id,
                        'name' => $variant->name,
                        'cost' => (float)$variant->cost,
                        'price' => (float)$variant->price,
                        'stock' => $variant->stock,
                        'minStock' => $variant->min_stock,
                    ];
                }
            } else {
                $defaultVariant = $product->variants->first();
                if ($defaultVariant) {
                    $this->baseCost = (float)$defaultVariant->cost;
                    $this->basePrice = (float)$defaultVariant->price;
                    $this->baseStock = $defaultVariant->stock;
                    $this->baseMinStock = $defaultVariant->min_stock;
                }
            }

            foreach ($product->extras as $extra) {
                $this->extras[] = [
                    'id' => $extra->id,
                    'name' => $extra->name,
                    'cost' => (float)$extra->cost,
                    'price' => (float)$extra->price,
                ];
            }
        } else {
            $this->addVariant();
            $this->addExtra();
        }
    }

    public function updatedCategoryId($value): void
    {
        $cat = collect($this->categories)->firstWhere('id', $value);
        $this->selectedCategoryType = $cat['type'] ?? 'retail';
    }

    public function addVariant(): void
    {
        $this->variants[] = ['id' => null, 'name' => '', 'cost' => '', 'price' => '', 'stock' => '', 'minStock' => ''];
    }

    public function removeVariant(int $index): void
    {
        unset($this->variants[$index]);
        $this->variants = array_values($this->variants);
    }

    public function addExtra(): void
    {
        $this->extras[] = ['id' => null, 'name' => '', 'cost' => '', 'price' => ''];
    }

    public function removeExtra(int $index): void
    {
        unset($this->extras[$index]);
        $this->extras = array_values($this->extras);
    }

    public function save(): void
    {
        try {
            $this->validate([
                'name' => 'required|string|max:255',
                'categoryId' => 'required|exists:categories,id',
                'image' => 'nullable|image|max:2048',
            ]);
        } catch (ValidationException $exception) {
            $this->dispatch('notify', ['type' => 'error', 'message' => $exception->getMessage()]);
            return;
        }

        DB::beginTransaction();
        try {
            $imagePath = $this->product?->image;
            if ($this->image) {
                $imagePath = $this->image->store('products', 'public');
            }

            $productData = [
                'category_id' => $this->categoryId,
                'name' => $this->name,
                'description' => $this->description,
                'image' => $imagePath,
                'tax_included' => $this->taxIncluded,
                'has_variants' => $this->hasVariants,
                'is_active' => $this->isActive,
            ];

            if (tenant('store_type') === 'resto') {
                $productData['selection_type'] = $this->hasVariants ? $this->selectionType : 'single';
                $productData['max_selections'] = $this->hasVariants ? $this->maxSelections : 1;
            }

            $product = Product::updateOrCreate(
                ['id' => $this->product?->id],
                $productData
            );

            $variantIdsToKeep = [];
            if ($this->hasVariants) {
                foreach ($this->variants as $variantData) {
                    if (!empty($variantData['name'])) {
                        $variant = $product->variants()->updateOrCreate(
                            ['id' => $variantData['id'] ?? null],
                            [
                                'name' => $variantData['name'],
                                'cost' => $variantData['cost'] ?: 0,
                                'price' => $variantData['price'] ?: 0,
                                'stock' => $variantData['stock'] ?: 0,
                                'min_stock' => $variantData['minStock'] ?: 0,
                            ]
                        );
                        $variantIdsToKeep[] = $variant->id;
                    }
                }
            } else {
                $defaultVariant = $product->variants()->updateOrCreate(
                    ['name' => 'Default'],
                    [
                        'cost' => $this->baseCost ?: 0,
                        'price' => $this->basePrice ?: 0,
                        'stock' => $this->baseStock ?: 0,
                        'min_stock' => $this->baseMinStock ?: 0,
                    ]
                );
                $variantIdsToKeep[] = $defaultVariant->id;
            }
            $product->variants()->whereNotIn('id', $variantIdsToKeep)->delete();

            $extraIdsToKeep = [];
            if ($this->selectedCategoryType === 'fnb') {
                foreach ($this->extras as $extraData) {
                    if (!empty($extraData['name'])) {
                        $extra = $product->extras()->updateOrCreate(
                            ['id' => $extraData['id'] ?? null],
                            [
                                'name' => $extraData['name'],
                                'cost' => $extraData['cost'] ?: 0,
                                'price' => $extraData['price'] ?: 0,
                                'is_active' => true,
                            ]
                        );
                        $extraIdsToKeep[] = $extra->id;
                    }
                }
            }
            $product->extras()->whereNotIn('id', $extraIdsToKeep)->delete();

            DB::commit();
            session()->flash('success', 'Produk berhasil disimpan.');
            $this->redirectRoute('product', navigate: true);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'SYSTEM ERROR: ' . $e->getMessage());
        }
    }
};
