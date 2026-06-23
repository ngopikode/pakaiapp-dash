<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    use \App\Traits\ClearsAiMenuCache;

    protected $fillable = [
        'id',
        'product_id',
        'sku',
        'name',
        'cost',
        'price',
        'stock',
        'min_stock',
        'created_at',
        'updated_at'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getProfitMarginAttribute()
    {
        return $this->price - $this->cost;
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
