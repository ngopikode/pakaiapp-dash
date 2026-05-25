<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalSetting extends Model
{
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'key';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];

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
