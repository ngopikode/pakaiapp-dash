<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    protected $fillable = [
        'navbar_brand_text',
        'hero_headline',
        'is_active',
        'name',
        'store_type',
    ];
    protected $guarded = [];
}
