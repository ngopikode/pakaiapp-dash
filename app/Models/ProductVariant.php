<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    protected $fillable = [
        'id',
        'name',
        'cost',
        'price',
        'stock',
        'min_stock',
    ];
    protected $guarded = [];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getProfitMarginAttribute()
    {
        return $this->price - $this->cost;
    }
}
