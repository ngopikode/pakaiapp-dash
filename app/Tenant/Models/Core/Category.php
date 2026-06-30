<?php

namespace App\Tenant\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'id',
        'name',
        'slug',
        'type',
        'order_column',
        'created_at',
        'updated_at'
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
