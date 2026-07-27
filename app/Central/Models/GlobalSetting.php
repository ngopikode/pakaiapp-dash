<?php

namespace App\Central\Models;

use Illuminate\Database\Eloquent\Attributes\Connection;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

#[Connection('mysql')]
#[Table(key: 'key', keyType: 'string', incrementing: false)]
#[Fillable([
    'key',
    'value',
    'type',
    'description',
])]
class GlobalSetting extends Model
{
    /**
     * Helper to cast value based on its type.
     */
    protected function castValue(): Attribute
    {
        return Attribute::make(
            get: fn () => match ($this->type) {
                'integer' => (int) $this->value,
                'float' => (float) $this->value,
                'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
                'json' => json_decode($this->value, true),
                default => $this->value,
            },
        );
    }
}
