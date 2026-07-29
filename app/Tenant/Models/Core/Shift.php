<?php

namespace App\Tenant\Models\Core;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'id',
    'user_id',
    'started_at',
    'ended_at',
    'starting_cash',
    'cash_sales',
    'cash_expenses',
    'expected_cash',
    'actual_cash',
    'difference',
    'status',
    'note',
])]
class Shift extends Model
{
    public const string STATUS_ACTIVE = 'active';

    public const string STATUS_CLOSED = 'closed';

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
            'starting_cash' => 'decimal:2',
            'cash_sales' => 'decimal:2',
            'cash_expenses' => 'decimal:2',
            'expected_cash' => 'decimal:2',
            'actual_cash' => 'decimal:2',
            'difference' => 'decimal:2',
        ];
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(TenantUser::class, 'user_id');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(ShiftExpense::class);
    }
}
