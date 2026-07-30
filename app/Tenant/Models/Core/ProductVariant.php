<?php

namespace App\Tenant\Models\Core;

use App\Shared\Traits\ClearsAiMenuCache;
use App\Tenant\Models\Ai\AiPricingRule;
use App\Tenant\Models\Resto\VariantRecipe;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

#[Fillable([
    'id',
    'product_id',
    'sku',
    'name',
    'cost',
    'price',
    'active_discount_price',
    'active_discount_name',
    'is_critical',
    'stock',
    'min_stock',
    'created_at',
    'updated_at',
])]
class ProductVariant extends Model
{
    use ClearsAiMenuCache;

    protected function casts(): array
    {
        return [
            'is_critical' => 'boolean',
        ];
    }

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

    public function recipes(): MorphMany
    {
        return $this->morphMany(VariantRecipe::class, 'recipeable');
    }

    public function aiPricingRules(): BelongsToMany
    {
        return $this->belongsToMany(AiPricingRule::class, 'ai_rule_variants')
            ->withPivot('id', 'discount_value');
    }
}
