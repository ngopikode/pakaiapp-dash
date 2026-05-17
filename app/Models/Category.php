<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'type',
        'id',
    ];
    protected $guarded = []; // Biar gampang mass-assignment

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

}
