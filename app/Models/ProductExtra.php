<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductExtra extends Model
{
    protected $fillable = [
        'id',
        'product_id',
        'name',
        'cost',
        'price',
        'is_active',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'price'     => 'float',
        'cost'      => 'float',
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
