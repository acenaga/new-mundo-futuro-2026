<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class Tutorial extends Post
{
    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('tutorial', function (Builder $builder) {
            $builder->whereHas('category', function (Builder $query) {
                $query->where('slug', 'tutoriales');
            });
        });
    }
}
