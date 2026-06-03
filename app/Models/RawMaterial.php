<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'unit',
        'stock',
        'cost_per_unit',
        'min_stock_alert'
    ];

    public function recipes()
    {
        return $this->hasMany(VariantRecipe::class);
    }
}
