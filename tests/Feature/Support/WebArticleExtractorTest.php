<?php

use App\Support\Sources\SourceUnavailableException;
use App\Support\Sources\WebArticleExtractor;
use Illuminate\Support\Facades\Http;

function sampleArticleHtml(string $wrapper = 'article'): string
{
    $paragraphs = str_repeat('<p>Vite+ is a single CLI called vp that wraps Vite, Rolldown and Vitest into one toolchain.</p>', 6);

    return <<<HTML
    <!doctype html>
    <html lang="en">
    <head>
        <title>Fallback Title | Site</title>
        <meta property="og:title" content="Laravel Starter Kits Now Ship with Vite+">
        <meta name="author" content="Paul Redmond">
        <meta property="og:site_name" content="Laravel News">
        <meta property="article:published_time" content="2026-08-28T12:50:00-04:00">
        <link rel="canonical" href="https://laravel-news.com/laravel-starter-kits-vite-plus">
        <script>window.tracking = true;</script>
    </head>
    <body>
        <nav><a href="/">Home</a> Navigation noise</nav>
        <{$wrapper}>
            <h1>Laravel Starter Kits Now Ship with Vite+</h1>
            {$paragraphs}
            <aside>Sponsored noise</aside>
        </{$wrapper}>
        <footer>Footer noise</footer>
    </body>
    </html>
    HTML;
}

it('extracts metadata and article text from an html page', function () {
    Http::fake([
        'https://laravel-news.com/*' => Http::response(sampleArticleHtml(), 200, ['Content-Type' => 'text/html; charset=UTF-8']),
    ]);

    $source = (new WebArticleExtractor)->extract('https://laravel-news.com/laravel-starter-kits-vite-plus?utm=x');

    expect($source->url)->toBe('https://laravel-news.com/laravel-starter-kits-vite-plus')
        ->and($source->title)->toBe('Laravel Starter Kits Now Ship with Vite+')
        ->and($source->author)->toBe('Paul Redmond')
        ->and($source->site)->toBe('Laravel News')
        ->and($source->publishedAt?->toIso8601String())->toBe('2026-08-28T12:50:00-04:00')
        ->and($source->text)->toContain('Vite+ is a single CLI called vp')
        ->and($source->text)->not->toContain('Navigation noise')
        ->and($source->text)->not->toContain('Footer noise')
        ->and($source->text)->not->toContain('Sponsored noise')
        ->and($source->text)->not->toContain('window.tracking');
});

it('prefers the json-ld author name over an author url in meta tags', function () {
    $paragraphs = str_repeat('<p>Vite+ is a single CLI called vp that wraps Vite, Rolldown and Vitest into one toolchain.</p>', 6);

    Http::fake([
        'https://laravel-news.com/*' => Http::response(<<<HTML
        <html><head>
            <title>Laravel Starter Kits Now Ship with Vite+</title>
            <meta property="article:author" content="https://laravel-news.com/@paulredmond"/>
            <script type="application/ld+json">{"@context":"https://schema.org","@type":"NewsArticle","headline":"Laravel Starter Kits Now Ship with Vite+","author":{"@type":"Person","name":"Paul Redmond","url":"https://laravel-news.com/@paulredmond"},"datePublished":"2026-08-28T12:50:00-04:00","publisher":{"@type":"Organization","name":"Laravel News"}}</script>
        </head><body><article>{$paragraphs}</article></body></html>
        HTML, 200, ['Content-Type' => 'text/html']),
    ]);

    $source = (new WebArticleExtractor)->extract('https://laravel-news.com/laravel-starter-kits-vite-plus');

    expect($source->author)->toBe('Paul Redmond')
        ->and($source->site)->toBe('Laravel News')
        ->and($source->publishedAt?->toIso8601String())->toBe('2026-08-28T12:50:00-04:00');
});

it('collects the content images of the article as absolute urls', function () {
    $paragraphs = str_repeat('<p>Vite+ is a single CLI called vp that wraps Vite, Rolldown and Vitest into one toolchain.</p>', 6);

    Http::fake([
        'https://laravel-news.com/*' => Http::response(<<<HTML
        <html><head><title>Post</title></head><body>
        <article>
            <img src="https://picperf.io/https://laravelnews.s3.amazonaws.com/images/vite-plus.png" alt="Vite+ announcement">
            <figure><img src="/images/inline.jpg" alt="Inline screenshot"></figure>
            <img data-src="https://cdn.example.com/lazy.png" src="data:image/gif;base64,R0lGOD" alt="Lazy loaded">
            <img src="/icons/avatar.png" width="48" height="48" alt="Avatar">
            <img src="/logo.svg" alt="Logo">
            <img src="https://picperf.io/https://laravelnews.s3.amazonaws.com/images/vite-plus.png" alt="Duplicate">
            {$paragraphs}
        </article>
        </body></html>
        HTML, 200, ['Content-Type' => 'text/html']),
    ]);

    $source = (new WebArticleExtractor)->extract('https://laravel-news.com/posts/laravel-starter-kits-vite-plus');

    expect(array_map(fn ($image) => $image->url, $source->images))->toBe([
        'https://picperf.io/https://laravelnews.s3.amazonaws.com/images/vite-plus.png',
        'https://laravel-news.com/images/inline.jpg',
        'https://cdn.example.com/lazy.png',
    ])
        ->and($source->images[1]->alt)->toBe('Inline screenshot');
});

it('falls back to the main element when there is no article element', function () {
    Http::fake([
        'https://example.com/*' => Http::response(sampleArticleHtml('main'), 200, ['Content-Type' => 'text/html']),
    ]);

    $source = (new WebArticleExtractor)->extract('https://example.com/post');

    expect($source->text)->toContain('Vite+ is a single CLI called vp');
});

it('throws when the page cannot be downloaded', function () {
    Http::fake([
        'https://example.com/*' => Http::response('Not found', 404),
    ]);

    (new WebArticleExtractor)->extract('https://example.com/missing');
})->throws(SourceUnavailableException::class, 'No se pudo descargar la página');

it('throws when the response is not html', function () {
    Http::fake([
        'https://example.com/*' => Http::response('{"ok":true}', 200, ['Content-Type' => 'application/json']),
    ]);

    (new WebArticleExtractor)->extract('https://example.com/api');
})->throws(SourceUnavailableException::class, 'no devolvió una página HTML');

it('throws when there is no readable content', function () {
    Http::fake([
        'https://example.com/*' => Http::response('<html><body><article><p>Too short</p></article></body></html>', 200, ['Content-Type' => 'text/html']),
    ]);

    (new WebArticleExtractor)->extract('https://example.com/empty');
})->throws(SourceUnavailableException::class, 'No se encontró contenido legible');

it('rejects urls that point to internal hosts', function (string $url) {
    Http::fake();

    try {
        (new WebArticleExtractor)->extract($url);
    } catch (SourceUnavailableException $exception) {
        Http::assertNothingSent();

        expect($exception->getMessage())->toContain('internas o privadas');

        return;
    }

    $this->fail('Expected SourceUnavailableException to be thrown.');
})->with([
    'http://localhost/admin',
    'http://127.0.0.1:8000/',
    'http://10.0.0.5/secret',
    'http://192.168.1.1/',
    'http://app.internal/',
]);

it('rejects urls with unsupported schemes', function () {
    Http::fake();

    (new WebArticleExtractor)->extract('ftp://example.com/file');
})->throws(SourceUnavailableException::class, 'La URL no es válida');
