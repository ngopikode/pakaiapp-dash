<?php

namespace App\Central\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Table(connection: 'mysql', key: 'key', incrementing: false, keyType: 'string')]
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
    public function getCastValueAttribute()
    {
        switch ($this->type) {
            case 'integer':
                return (int) $this->value;
            case 'float':
                return (float) $this->value;
            case 'boolean':
                return filter_var($this->value, FILTER_VALIDATE_BOOLEAN);
            case 'json':
                return json_decode($this->value, true);
            default:
                return $this->value;
        }
    }
}
