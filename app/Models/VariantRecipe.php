<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariantRecipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'variant_id',
        'raw_material_id',
        'quantity_used'
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }
}
