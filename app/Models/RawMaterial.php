<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RawMaterial extends Model
{

    protected $fillable = [
        'id',
        'name',
        'unit',
        'stock',
        'cost_per_unit',
        'min_stock_alert'
    ];

    public function recipes(): HasMany
    {
        return $this->hasMany(VariantRecipe::class);
    }
}
