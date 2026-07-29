<?php

namespace App\Tenant\Models\Core;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'id',
    'shift_id',
    'user_id',
    'status',
    'note',
])]
class StockOpname extends Model
{
    public const string STATUS_DRAFT = 'draft';

    public const string STATUS_COMPLETED = 'completed';

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(TenantUser::class, 'user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockOpnameItem::class);
    }
}
