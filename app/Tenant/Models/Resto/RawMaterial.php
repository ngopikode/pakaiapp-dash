<?php

namespace App\Tenant\Models\Resto;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'id',
    'name',
    'unit',
    'stock',
    'cost_per_unit',
    'min_stock_alert',
])]
class RawMaterial extends Model
{
    public function recipes(): HasMany
    {
        return $this->hasMany(VariantRecipe::class);
    }
}
