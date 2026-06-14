<?php

namespace App\Models;

use App\Enums\PostType;
use Illuminate\Database\Eloquent\Builder;

class Tutorial extends Post
{
    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('tutorial', function (Builder $builder) {
            $builder->where('type', PostType::Tutorial);
        });

        static::creating(function (Tutorial $tutorial) {
            $tutorial->type = PostType::Tutorial;
        });
    }

    /**
     * Ensure the correct morph class is always the parent.
     */
    public function getMorphClass(): string
    {
        return Post::class;
    }
}
