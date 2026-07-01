<?php

namespace App\Tenant\Models\Ai;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Tenant\Models\Core\ProductVariant;

class AiPricingRule extends Model
{
    protected $fillable = [
        'rule_name',
        'rule_type',
        'start_time',
        'end_time',
        'active_days',
        'is_active',
    ];

    protected $casts = [
        'active_days' => 'array',
        'is_active' => 'boolean',
    ];

    public function productVariants(): BelongsToMany
    {
        return $this->belongsToMany(ProductVariant::class, 'ai_rule_variants')
            ->withPivot('id', 'discount_value');
    }
}
