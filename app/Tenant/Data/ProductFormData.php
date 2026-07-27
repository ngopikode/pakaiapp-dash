<?php

namespace App\Tenant\Data;

use Spatie\LaravelData\Data;

class ProductFormData extends Data
{
    public function __construct(
        public string $name,
        public int $categoryId,
        public ?string $description,
        public ?string $image,
        public bool $taxIncluded,
        public bool $isActive,
        public bool $hasVariants,
        public string $selectionType,
        public int $maxSelections,
        public float $baseCost,
        public float $basePrice,
        public int $baseStock,
        public int $baseMinStock,
        public string $baseSku,
        public array $variants,
        public array $extras,
        public array $baseRecipes,
    ) {}
}
