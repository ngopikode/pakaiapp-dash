<?php

namespace App\Tenant\Models\Core;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable([
    'wallet_id',
    'type',
    'amount',
    'opening_balance',
    'closing_balance',
    'reference_id',
    'reference_type',
    'description',
    'created_by',
])]
class WalletTransaction extends Model
{

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'opening_balance' => 'decimal:2',
            'closing_balance' => 'decimal:2',
        ];
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}
