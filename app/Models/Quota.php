<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quota extends Model
{
    protected $fillable = [
        'type',
        'total_slots',
        'used_slots',
    ];

    protected function casts(): array
    {
        return [
            'total_slots' => 'integer',
            'used_slots' => 'integer',
        ];
    }
}
