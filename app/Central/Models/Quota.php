<?php

namespace App\Central\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['type', 'total_slots', 'used_slots'])]
class Quota extends Model
{
    protected function casts(): array
    {
        return [
            'total_slots' => 'integer',
            'used_slots' => 'integer',
        ];
    }
}
