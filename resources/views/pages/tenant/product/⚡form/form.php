<?php

use App\Tenant\Data\ProductFormData;
use App\Tenant\Models\Core\Category;
use App\Tenant\Models\Core\Product;
use App\Tenant\Models\Core\StoreSetting;
use App\Tenant\Models\Resto\RawMaterial;
use App\Tenant\Services\ProductService;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Form Produk')]
class extends Component
{
    use WithFileUploads;

    protected ?ProductService $productService = null;

    public ?Product $product = null;

    public array $categories = [];

    public array $rawMaterials = [];

    public string $selectedCategoryType = 'retail';

    public string $name = '';

    public string $categoryId = '';

    public string $description = '';

    public mixed $image = null;

    public bool $taxIncluded = false;

    public bool $isActive = true;

    public bool $hasVariants = false;

    public string $selectionType = 'single';

    public int $maxSelections = 1;

    public float $baseCost = 0;

    public float $basePrice = 0;

    public int $baseStock = 0;

    public int $baseMinStock = 0;

    public string $baseSku = '';

    public array $baseRecipes = [];

    public array $variants = [];

    public array $extras = [];

    protected function productService(): ProductService
    {
        return $this->productService ??= app(ProductService::class);
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'categoryId' => ['required', 'exists:categories,id'],
            'image' => ['nullable', 'image', 'max:2048'],
            'hasVariants' => ['boolean'],
            'baseCost' => ['nullable', 'numeric', 'min:0'],
            'basePrice' => ['nullable', 'numeric', 'min:0'],
            'baseStock' => ['nullable', 'integer', 'min:0'],
        ];
    }

    private function loadCategoriesOnce(): void
    {
        if (!empty($this->categories)) return;

        $this->selectedCategoryType = StoreSetting::cached()?->store_type ?? 'retail';
        $this->categories = Category::select('id', 'name', 'type')->orderBy('name')->get()->toArray();
        if ($this->selectedCategoryType === 'resto') {
            $this->rawMaterials = RawMaterial::select('id', 'name', 'unit')->orderBy('name')->get()->toArray();
        }
    }

    private function emptyVariant(): array
    {
        return ['id' => null, 'name' => '', 'sku' => '', 'cost' => '', 'price' => '', 'stock' => '', 'minStock' => '', 'recipes' => []];
    }

    private function emptyExtra(): array
    {
        return ['id' => null, 'name' => '', 'cost' => '', 'price' => ''];
    }

    #[On('load-product')]
    public function loadProduct(int $productId): void
    {
        $this->loadCategoriesOnce();

        $product = Product::with(['variants.recipes', 'extras'])->find($productId);
        if (!$product) return;

        $this->product = $product;
        $this->name = $product->name;
        $this->categoryId = (string) $product->category_id;
        $this->description = $product->description ?? '';
        $this->taxIncluded = $product->tax_included;
        $this->isActive = $product->is_active;
        $this->hasVariants = $product->has_variants;
        $this->selectionType = $product->selection_type ?? 'single';
        $this->maxSelections = $product->max_selections ?? 1;

        $this->variants = [];
        if ($this->hasVariants) {
            foreach ($product->variants as $variant) {
                $variantRecipes = [];
                if (tenant('store_type') === 'resto') {
                    foreach ($variant->recipes as $recipe) {
                        $variantRecipes[] = [
                            'id' => $recipe->id,
                            'raw_material_id' => $recipe->raw_material_id,
                            'quantity_used' => (float) $recipe->quantity_used,
                        ];
                    }
                }
                $this->variants[] = [
                    'id' => $variant->id,
                    'name' => $variant->name,
                    'sku' => $variant->sku ?? '',
                    'cost' => (float) $variant->cost,
                    'price' => (float) $variant->price,
                    'stock' => $variant->stock,
                    'minStock' => $variant->min_stock,
                    'recipes' => $variantRecipes,
                ];
            }
        } else {
            $defaultVariant = $product->variants->first();
            if ($defaultVariant) {
                $this->baseCost = (float) $defaultVariant->cost;
                $this->basePrice = (float) $defaultVariant->price;
                $this->baseStock = $defaultVariant->stock;
                $this->baseMinStock = $defaultVariant->min_stock;
                $this->baseSku = $defaultVariant->sku ?? '';
                if (tenant('store_type') === 'resto') {
                    foreach ($defaultVariant->recipes as $recipe) {
                        $this->baseRecipes[] = [
                            'id' => $recipe->id,
                            'raw_material_id' => $recipe->raw_material_id,
                            'quantity_used' => (float) $recipe->quantity_used,
                        ];
                    }
                }
            }
            $this->variants = [$this->emptyVariant()];
        }

        $this->extras = [];
        if (tenant('store_type') === 'resto') {
            foreach ($product->extras as $extra) {
                $this->extras[] = [
                    'id' => $extra->id,
                    'name' => $extra->name,
                    'cost' => (float) $extra->cost,
                    'price' => (float) $extra->price,
                ];
            }
        }
        if (empty($this->extras)) {
            $this->extras = [$this->emptyExtra()];
        }

        $this->dispatch('form-initialized');
    }

    #[On('init-new-form')]
    public function initNewForm(): void
    {
        $this->product = null;
        $this->name = '';
        $this->categoryId = '';
        $this->description = '';
        $this->image = null;
        $this->taxIncluded = false;
        $this->isActive = true;
        $this->hasVariants = false;
        $this->selectionType = 'single';
        $this->maxSelections = 1;
        $this->baseCost = 0;
        $this->basePrice = 0;
        $this->baseStock = 0;
        $this->baseMinStock = 0;
        $this->baseSku = '';
        $this->baseRecipes = [];
        $this->variants = [$this->emptyVariant()];
        $this->extras = [$this->emptyExtra()];

        $this->loadCategoriesOnce();
        $this->dispatch('form-initialized');
    }

    public function addVariant(): void
    {
        $this->variants[] = $this->emptyVariant();
    }

    public function removeVariant(int $index): void
    {
        unset($this->variants[$index]);
        $this->variants = array_values($this->variants);
    }

    public function addExtra(): void
    {
        $this->extras[] = $this->emptyExtra();
    }

    public function removeExtra(int $index): void
    {
        unset($this->extras[$index]);
        $this->extras = array_values($this->extras);
    }

    public function addBaseRecipe(): void
    {
        $this->baseRecipes[] = ['id' => null, 'raw_material_id' => '', 'quantity_used' => ''];
    }

    public function removeBaseRecipe(int $index): void
    {
        unset($this->baseRecipes[$index]);
        $this->baseRecipes = array_values($this->baseRecipes);
    }

    public function addVariantRecipe(int $variantIndex): void
    {
        $this->variants[$variantIndex]['recipes'][] = ['id' => null, 'raw_material_id' => '', 'quantity_used' => ''];
    }

    public function removeVariantRecipe(int $variantIndex, int $recipeIndex): void
    {
        unset($this->variants[$variantIndex]['recipes'][$recipeIndex]);
        $this->variants[$variantIndex]['recipes'] = array_values($this->variants[$variantIndex]['recipes']);
    }

    public function save(): void
    {
        $this->validate();

        $imagePath = $this->product?->image;
        if ($this->image) {
            $imagePath = $this->image->store('products', 'public');
        }

        $dto = new ProductFormData(
            name: $this->name,
            categoryId: (int) $this->categoryId,
            description: $this->description ?: null,
            image: $imagePath,
            taxIncluded: $this->taxIncluded,
            isActive: $this->isActive,
            hasVariants: $this->hasVariants,
            selectionType: $this->hasVariants ? $this->selectionType : 'single',
            maxSelections: $this->hasVariants ? $this->maxSelections : 1,
            baseCost: $this->baseCost,
            basePrice: $this->basePrice,
            baseStock: $this->baseStock,
            baseMinStock: $this->baseMinStock,
            baseSku: $this->baseSku,
            variants: $this->variants,
            extras: $this->extras,
            baseRecipes: $this->baseRecipes,
        );

        try {
            $this->productService()->saveFromForm($this->product, $dto);
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Produk berhasil disimpan.']);
            $this->dispatch('close-product-form');
            $this->dispatch('product-saved');
        } catch (Throwable $e) {
            report($e);
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal menyimpan produk.']);
        }
    }
};
