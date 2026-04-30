<?php

use App\Models\Category;
use App\Models\Post;

it('serves core public routes', function () {
    $newsCategory = Category::factory()->create([
        'name' => 'Noticias',
        'slug' => 'noticias',
    ]);

    $tutorialsCategory = Category::factory()->create([
        'name' => 'Tutoriales',
        'slug' => 'tutoriales',
    ]);

    $publicacion = Post::factory()->published()->create([
        'category_id' => $newsCategory->id,
    ]);

    $tutorial = Post::factory()->published()->create([
        'category_id' => $tutorialsCategory->id,
    ]);

    $this->get(route('home'))->assertSuccessful();
    $this->get(route('publicaciones'))->assertSuccessful();
    $this->get(route('tutoriales'))->assertSuccessful();
    $this->get(route('publicaciones.show', $publicacion))->assertSuccessful();
    $this->get(route('tutoriales.show', $tutorial))->assertSuccessful();
});

it('serves sitemap xml response', function () {
    $response = $this->get(route('sitemap'));

    $response->assertSuccessful();
    $response->assertHeader('Content-Type', 'application/xml');
});
