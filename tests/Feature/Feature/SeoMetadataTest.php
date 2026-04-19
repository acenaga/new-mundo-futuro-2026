<?php

use App\Models\Category;
use App\Models\Post;

it('renders valid json-ld and absolute og image in publicaciones show', function () {
    $category = Category::factory()->create([
        'name' => 'Noticias',
        'slug' => 'noticias',
    ]);

    $post = Post::factory()->published()->create([
        'category_id' => $category->id,
        'title' => 'SEO "robusto" para crawlers',
        'excerpt' => 'Resumen con "comillas" y saltos\nde linea',
        'cover_image_path' => 'covers/publicacion.jpg',
    ]);

    $response = $this->get(route('publicaciones.show', $post));
    $response->assertSuccessful();

    $content = $response->getContent();

    preg_match('/<script type="application\/ld\+json">\s*(\{.*?\})\s*<\/script>/s', $content, $matches);
    expect($matches)->toHaveCount(2);

    $decoded = json_decode($matches[1], true, 512, JSON_THROW_ON_ERROR);

    expect($decoded['@type'])->toBe('Article');
    expect($decoded['headline'])->toBe('SEO "robusto" para crawlers');
    expect($decoded['description'])->toContain('comillas');
    expect($decoded['image'])->toBe(url('/storage/covers/publicacion.jpg'));

    $response->assertSee('property="og:image" content="'.e(url('/storage/covers/publicacion.jpg')).'"', false);
});

it('renders valid json-ld and absolute og image in tutoriales show', function () {
    $category = Category::factory()->create([
        'name' => 'Tutoriales',
        'slug' => 'tutorials',
    ]);

    $post = Post::factory()->published()->create([
        'category_id' => $category->id,
        'title' => 'Guia "Tech" paso a paso',
        'excerpt' => 'Descripcion con "escape" seguro',
        'cover_image_path' => 'covers/tutorial.jpg',
    ]);

    $response = $this->get(route('tutoriales.show', $post));
    $response->assertSuccessful();

    $content = $response->getContent();

    preg_match('/<script type="application\/ld\+json">\s*(\{.*?\})\s*<\/script>/s', $content, $matches);
    expect($matches)->toHaveCount(2);

    $decoded = json_decode($matches[1], true, 512, JSON_THROW_ON_ERROR);

    expect($decoded['@type'])->toBe('TechArticle');
    expect($decoded['headline'])->toBe('Guia "Tech" paso a paso');
    expect($decoded['image'])->toBe(url('/storage/covers/tutorial.jpg'));

    $response->assertSee('property="og:image" content="'.e(url('/storage/covers/tutorial.jpg')).'"', false);
});

it('declares sitemap in robots file', function () {
    $robots = file_get_contents(public_path('robots.txt'));

    expect($robots)->not->toBeFalse();
    expect($robots)->toContain('Sitemap: http://localhost/sitemap.xml');
});
