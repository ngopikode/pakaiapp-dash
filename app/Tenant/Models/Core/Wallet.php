<?php

namespace App\Tenant\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    protected $fillable = [
        'id',
        'balance',
        'current_billing_period',
        'monthly_transaction_count',
        'monthly_fee_paid',
        'monthly_void_count',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
            'monthly_fee_paid' => 'decimal:2',
            'monthly_transaction_count' => 'integer',
            'monthly_void_count' => 'integer',
        ];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }
}
