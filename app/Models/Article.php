<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class Article extends Post
{
    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('article', function (Builder $builder) {
            $builder->whereHas('category', function (Builder $query) {
                $query->where('slug', '!=', 'tutoriales');
            });
        });
    }
}
