<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'id',
        'invoice_code',
        'table_number',
        'customer_name',
        'customer_phone',
        'order_type',
        'payment_method',
        'subtotal',
        'discount',
        'total_price',
        'amount_paid',
        'change_amount',
        'status',
        'user_id',
        'cancellation_note',
        'created_at',
        'updated_at'
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Relasi ke kasir yang login
    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
