<?php

namespace App\Tenant\Models\Core;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'id',
    'name',
    'type',
    'balance',
    'current_billing_period',
    'monthly_transaction_count',
    'monthly_fee_paid',
    'monthly_void_count',
])]
class Wallet extends Model
{
    public const string TYPE_BILLING = 'billing';

    public const string TYPE_CASH = 'cash';

    public const string TYPE_BANK = 'bank';

    public const string TYPE_GATEWAY = 'gateway';

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
