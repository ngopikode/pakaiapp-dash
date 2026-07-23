<?php

namespace App\Tenant\Models\Core;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'id',
    'name',
    'slug',
    'type',
    'order_column',
    'created_at',
    'updated_at',
])]
class Category extends Model
{

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
