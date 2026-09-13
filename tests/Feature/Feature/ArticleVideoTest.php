<?php

use App\Models\Category;
use App\Models\Post;

function createPublishedArticle(array $attributes = []): Post
{
    $category = Category::factory()->create(['name' => 'Noticias', 'slug' => 'noticias']);

    return Post::factory()->published()->create([
        'category_id' => $category->id,
        ...$attributes,
    ]);
}

it('embeds the youtube player on the article page when a video url is set', function () {
    $article = createPublishedArticle(['video_url' => 'https://www.youtube.com/watch?v=Cn8HBj8QAbk']);

    $this->get(route('publicaciones.show', $article))
        ->assertSuccessful()
        ->assertSee('https://www.youtube.com/embed/Cn8HBj8QAbk', false);
});

it('does not render a video player when the article has no video url', function () {
    $article = createPublishedArticle(['video_url' => null]);

    $this->get(route('publicaciones.show', $article))
        ->assertSuccessful()
        ->assertDontSee('youtube.com/embed', false);
});
