<?php

namespace App\Central\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'title',
    'slug',
    'excerpt',
    'content',
    'meta_title',
    'meta_description',
    'published_at',
])]
class Article extends Model
{
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }
}
