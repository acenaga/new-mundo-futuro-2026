<?php

use App\Ai\Agents\ArticleFromUrlAgent;
use App\Filament\Resources\Articles\Pages\CreateArticle;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Laravel\Ai\Image;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    foreach (['ViewAny:Post', 'View:Post', 'Create:Post', 'Update:Post', 'Delete:Post'] as $permission) {
        Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
    }

    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web'])
        ->syncPermissions(['ViewAny:Post', 'View:Post', 'Create:Post', 'Update:Post', 'Delete:Post']);

    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');
    $this->actingAs($this->admin);

    Storage::fake('public');

    $paragraphs = str_repeat('<p>Every Laravel starter kit now builds with Vite+, the unified toolchain released in beta.</p>', 6);

    Http::fake([
        'https://cdn.laravel-news.com/*' => Http::response(testPngBinary(), 200, ['Content-Type' => 'image/png']),
        'https://laravel-news.com/*' => Http::response(<<<HTML
        <html><head>
            <title>Laravel Starter Kits Now Ship with Vite+</title>
            <meta name="author" content="Paul Redmond">
            <meta property="og:site_name" content="Laravel News">
            <meta property="article:published_time" content="2026-08-28T12:50:00-04:00">
        </head><body><article><img src="https://cdn.laravel-news.com/vite-plus.png" alt="Vite+ logo">{$paragraphs}</article></body></html>
        HTML, 200, ['Content-Type' => 'text/html']),
        'https://down.example.com/*' => Http::response('', 503),
    ]);
});

function testPngBinary(): string
{
    $image = imagecreatetruecolor(200, 120);
    imagefill($image, 0, 0, imagecolorallocate($image, 20, 20, 120));

    ob_start();
    imagepng($image);
    $binary = ob_get_clean();
    imagedestroy($image);

    return $binary;
}

function fakeDraftResponse(array $overrides = []): array
{
    return [
        'title' => 'Los starter kits de Laravel ahora usan Vite+',
        'excerpt' => 'Todos los kits de inicio de Laravel se construyen ahora con Vite+.',
        'body_html' => '<h2>Qué cambia</h2><p>Según informa <a href="https://laravel-news.com/laravel-starter-kits-vite-plus">Laravel News</a>, los kits usan <code>vp</code>.</p>',
        'source_title' => 'Laravel Starter Kits Now Ship with Vite+',
        'source_author' => 'Paul Redmond',
        'source_site' => 'Laravel News',
        'source_published_at' => '2026-08-28T12:50:00-04:00',
        ...$overrides,
    ];
}

it('fills the article form with the generated draft', function () {
    ArticleFromUrlAgent::fake([fakeDraftResponse([
        'body_html' => '<h2>Qué cambia</h2><p>[[imagen:1]]</p><p>Según informa <a href="https://laravel-news.com/laravel-starter-kits-vite-plus">Laravel News</a>, los kits usan <code>vp</code>.</p><p>[[imagen:7]]</p>',
    ])]);
    Image::fake([base64_encode(testPngBinary())]);

    Livewire::test(CreateArticle::class)
        ->callAction('importFromUrl', data: ['url' => 'https://laravel-news.com/laravel-starter-kits-vite-plus'])
        ->assertHasNoActionErrors()
        ->assertFormSet([
            'title' => 'Los starter kits de Laravel ahora usan Vite+',
            'slug' => 'los-starter-kits-de-laravel-ahora-usan-vite',
            'excerpt' => 'Todos los kits de inicio de Laravel se construyen ahora con Vite+.',
            'source_url' => 'https://laravel-news.com/laravel-starter-kits-vite-plus',
            'source_title' => 'Laravel Starter Kits Now Ship with Vite+',
            'source_author' => 'Paul Redmond',
            'source_site' => 'Laravel News',
        ])
        ->assertFormSet(function (array $state) {
            $body = json_encode($state['body'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            expect($body)->toContain('Qué cambia')
                ->and($body)->toContain('https://laravel-news.com/laravel-starter-kits-vite-plus')
                ->and($body)->toContain('/storage/post-images/')
                ->and($body)->not->toContain('[[imagen')
                ->and($state['source_published_at'])->toStartWith('2026-08-28');

            $coverPath = is_array($state['cover_image_path']) ? reset($state['cover_image_path']) : $state['cover_image_path'];

            expect($coverPath)->toStartWith('covers/');

            Storage::disk('public')->assertExists($coverPath);
        });

    expect(Storage::disk('public')->files('post-images'))->toHaveCount(1);

    Image::assertGenerated(fn ($prompt) => $prompt->contains('Los starter kits de Laravel ahora usan Vite+'));

    ArticleFromUrlAgent::assertPrompted(fn ($prompt) => str_contains($prompt->prompt, 'Every Laravel starter kit now builds with Vite+')
        && str_contains($prompt->prompt, 'Paul Redmond'));
});

it('skips image import and cover generation when the toggles are off', function () {
    ArticleFromUrlAgent::fake([fakeDraftResponse([
        'body_html' => '<p>[[imagen:1]]</p><p>Texto sin imágenes.</p>',
    ])]);
    Image::fake()->preventStrayImages();

    Livewire::test(CreateArticle::class)
        ->callAction('importFromUrl', data: [
            'url' => 'https://laravel-news.com/laravel-starter-kits-vite-plus',
            'import_images' => false,
            'generate_cover' => false,
        ])
        ->assertHasNoActionErrors()
        ->assertFormSet(function (array $state) {
            $body = json_encode($state['body'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            expect($body)->not->toContain('[[imagen')
                ->and($body)->not->toContain('/storage/post-images/')
                ->and($body)->toContain('Texto sin imágenes')
                ->and($state['cover_image_path'])->toBeEmpty();
        });

    Image::assertNothingGenerated();
    expect(Storage::disk('public')->files('post-images'))->toBeEmpty();
});

it('still fills the form when the cover generation fails', function () {
    ArticleFromUrlAgent::fake([fakeDraftResponse()]);
    Image::fake([fn () => throw new RuntimeException('Image provider down')]);

    Livewire::test(CreateArticle::class)
        ->callAction('importFromUrl', data: [
            'url' => 'https://laravel-news.com/laravel-starter-kits-vite-plus',
            'import_images' => false,
            'generate_cover' => true,
        ])
        ->assertHasNoActionErrors()
        ->assertFormSet([
            'title' => 'Los starter kits de Laravel ahora usan Vite+',
        ])
        ->assertNotified('Aviso');
});

it('sanitizes the generated html', function () {
    Image::fake()->preventStrayImages();
    ArticleFromUrlAgent::fake([fakeDraftResponse([
        'body_html' => '<h1>No</h1><p onclick="x()">Hola <script>alert(1)</script><img src="x.png"><a href="javascript:alert(1)">mal</a></p>',
    ])]);

    Livewire::test(CreateArticle::class)
        ->callAction('importFromUrl', data: ['url' => 'https://laravel-news.com/laravel-starter-kits-vite-plus', 'generate_cover' => false])
        ->assertHasNoActionErrors()
        ->assertFormSet(function (array $state) {
            $body = json_encode($state['body'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            expect($body)->not->toContain('heading')
                ->and($body)->not->toContain('alert(1)')
                ->and($body)->not->toContain('image')
                ->and($body)->not->toContain('onclick')
                ->and($body)->not->toContain('javascript:')
                ->and($body)->toContain('Hola');
        });
});

it('shows an error on the url field when the source cannot be downloaded', function () {
    ArticleFromUrlAgent::fake()->preventStrayPrompts();

    Livewire::test(CreateArticle::class)
        ->callAction('importFromUrl', data: ['url' => 'https://down.example.com/post'])
        ->assertHasActionErrors(['url']);

    ArticleFromUrlAgent::assertNeverPrompted();
});

it('requires a valid url in the import modal', function () {
    ArticleFromUrlAgent::fake()->preventStrayPrompts();

    Livewire::test(CreateArticle::class)
        ->callAction('importFromUrl', data: ['url' => 'not-a-url'])
        ->assertHasActionErrors(['url']);
});

it('persists the source fields when the article is created', function () {
    $category = Category::factory()->create(['slug' => 'noticias']);

    Livewire::test(CreateArticle::class)
        ->fillForm([
            'title' => 'Artículo con fuente',
            'slug' => 'articulo-con-fuente',
            'body' => '<p>Contenido</p>',
            'category_id' => $category->id,
            'user_id' => $this->admin->id,
            'source_url' => 'https://laravel-news.com/laravel-starter-kits-vite-plus',
            'source_title' => 'Laravel Starter Kits Now Ship with Vite+',
            'source_author' => 'Paul Redmond',
            'source_site' => 'Laravel News',
            'source_published_at' => '2026-08-28 12:50:00',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas('posts', [
        'slug' => 'articulo-con-fuente',
        'source_url' => 'https://laravel-news.com/laravel-starter-kits-vite-plus',
        'source_author' => 'Paul Redmond',
        'source_site' => 'Laravel News',
    ]);

    expect(Article::where('slug', 'articulo-con-fuente')->first()->hasSource())->toBeTrue();
});
