<?php

namespace App\Tenant\Models\Resto;

use App\Shared\Traits\ClearsAiMenuCache;
use App\Tenant\Models\Core\Product;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'id',
    'product_id',
    'name',
    'cost',
    'price',
    'is_active',
    'created_at',
    'updated_at',
])]
class ProductExtra extends Model
{
    use ClearsAiMenuCache;

    protected function casts(): array
    {
        return [
            'price' => 'float',
            'cost' => 'float',
            'is_active' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
