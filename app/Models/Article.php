<?php

namespace App\Models;

use App\Enums\PostType;
use Illuminate\Database\Eloquent\Builder;

class Article extends Post
{
    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('article', function (Builder $builder) {
            $builder->where('type', PostType::Article);
        });

        static::creating(function (Article $article) {
            $article->type = PostType::Article;
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
