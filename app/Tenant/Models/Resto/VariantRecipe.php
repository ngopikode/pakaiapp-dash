<?php

namespace App\Tenant\Models\Resto;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Tenant\Models\Core\ProductVariant;

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
