<?php

namespace App\Tenant\Models\Core;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Tenant\Models\Ai\AiPricingRule;
use App\Tenant\Models\Resto\VariantRecipe;

#[Fillable([
    'id',
    'product_id',
    'sku',
    'name',
    'cost',
    'price',
    'active_discount_price',
    'active_discount_name',
    'stock',
    'min_stock',
    'created_at',
    'updated_at',
])]
class ProductVariant extends Model
{
    use \App\Shared\Traits\ClearsAiMenuCache;

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    protected function profitMargin(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->price - $this->cost,
        );
    }

    public function recipes()
    {
        return $this->hasMany(VariantRecipe::class, 'variant_id');
    }

    public function aiPricingRules(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(AiPricingRule::class, 'ai_rule_variants')
            ->withPivot('id', 'discount_value');
    }
}
