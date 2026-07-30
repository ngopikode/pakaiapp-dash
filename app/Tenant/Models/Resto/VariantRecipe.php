<?php

namespace App\Tenant\Models\Resto;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable(['recipeable_type', 'recipeable_id', 'raw_material_id', 'quantity_used'])]
class VariantRecipe extends Model
{
    use HasFactory;

    protected $table = 'recipes';

    public function recipeable(): MorphTo
    {
        return $this->morphTo();
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }
}
