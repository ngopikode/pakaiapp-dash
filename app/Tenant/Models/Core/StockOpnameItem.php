<?php

namespace App\Tenant\Models\Core;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable([
    'id',
    'stock_opname_id',
    'opnameable_type',
    'opnameable_id',
    'system_stock',
    'physical_stock',
    'difference',
    'note',
])]
class StockOpnameItem extends Model
{
    protected function casts(): array
    {
        return [
            'system_stock' => 'decimal:2',
            'physical_stock' => 'decimal:2',
            'difference' => 'decimal:2',
        ];
    }

    public function stockOpname(): BelongsTo
    {
        return $this->belongsTo(StockOpname::class);
    }

    public function opnameable(): MorphTo
    {
        return $this->morphTo();
    }
}
