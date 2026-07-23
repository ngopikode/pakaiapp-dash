<?php

namespace App\Tenant\Models\Core;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'id',
    'order_id',
    'product_id',
    'variant_id',
    'product_name',
    'variant_name',
    'quantity',
    'cost',
    'price',
    'subtotal',
    'note',
    'kitchen_status',
    'discount',
    'selected_variants',
    'selected_extras',
    'created_at',
    'updated_at',
])]
class OrderItem extends Model
{

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
