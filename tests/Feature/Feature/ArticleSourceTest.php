<?php

use App\Models\Category;
use App\Models\Post;

function createPublishedArticleWithCategory(array $attributes = [], bool $fromSource = false): Post
{
    $category = Category::factory()->create(['name' => 'Noticias', 'slug' => 'noticias']);

    $factory = Post::factory()->published();

    if ($fromSource) {
        $factory = $factory->fromSource();
    }

    return $factory->create(['category_id' => $category->id, ...$attributes]);
}

it('shows the source attribution block when the article is based on an external source', function () {
    $article = createPublishedArticleWithCategory(fromSource: true);

    $this->get(route('publicaciones.show', $article))
        ->assertSuccessful()
        ->assertSee('Este artículo está basado en')
        ->assertSee('Laravel Starter Kits Now Ship with Vite+')
        ->assertSee('Paul Redmond')
        ->assertSee('Laravel News')
        ->assertSee('href="https://laravel-news.com/laravel-starter-kits-vite-plus"', false)
        ->assertSee('rel="noopener noreferrer nofollow"', false)
        ->assertSee('"isBasedOn":"https://laravel-news.com/laravel-starter-kits-vite-plus"', false);
});

it('does not show the source block when the article has no source', function () {
    $article = createPublishedArticleWithCategory(['source_url' => null]);

    $this->get(route('publicaciones.show', $article))
        ->assertSuccessful()
        ->assertDontSee('Este artículo está basado en')
        ->assertDontSee('isBasedOn');
});
