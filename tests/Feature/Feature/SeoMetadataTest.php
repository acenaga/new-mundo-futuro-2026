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

    $response->assertSee('property="og:image" content="' . e(url('/storage/covers/publicacion.jpg')) . '"', false);
});

it('renders valid json-ld and absolute og image in tutoriales show', function () {
    $category = Category::factory()->create([
        'name' => 'Tutoriales',
        'slug' => 'tutoriales',
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

    $response->assertSee('property="og:image" content="' . e(url('/storage/covers/tutorial.jpg')) . '"', false);
});

it('renders recent post cover image and detail link on home', function () {
    $category = Category::factory()->create([
        'name' => 'Noticias',
        'slug' => 'noticias',
    ]);

    $post = Post::factory()->published()->create([
        'category_id' => $category->id,
        'title' => 'Post visible en home',
        'cover_image_path' => 'covers/home-post.jpg',
    ]);

    $response = $this->get(route('home'));

    $response->assertSuccessful();
    $response->assertSee('src="' . e(url('/storage/covers/home-post.jpg')) . '"', false);
    $response->assertSee('href="' . e(route('publicaciones.show', $post)) . '"', false);
});

it('renders sanitized rich html in publicaciones show', function () {
    $category = Category::factory()->create([
        'name' => 'Noticias',
        'slug' => 'noticias',
    ]);

    $post = Post::factory()->published()->create([
        'category_id' => $category->id,
        'body' => '<h2>Subtitulo</h2><p><strong>Contenido</strong> renderizado.</p><script>alert("xss")</script>',
    ]);

    $response = $this->get(route('publicaciones.show', $post));

    $response->assertSuccessful();
    $response->assertSee('<h2>Subtitulo</h2>', false);
    $response->assertSee('<strong>Contenido</strong>', false);
    $response->assertDontSee('<script>alert("xss")</script>', false);
    $response->assertDontSee('&lt;h2&gt;Subtitulo&lt;/h2&gt;', false);
});

it('declares sitemap in robots file', function () {
    $robots = file_get_contents(public_path('robots.txt'));

    expect($robots)->not->toBeFalse();
    expect($robots)->toContain('Sitemap: http://localhost/sitemap.xml');
});
