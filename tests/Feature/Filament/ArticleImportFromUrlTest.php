<?php

use App\Actions\Articles\DraftArticleFromUrl;
use App\Ai\Agents\ArticleDraftReviewerAgent;
use App\Ai\Agents\ArticleFromUrlAgent;
use App\Filament\Resources\Articles\Pages\CreateArticle;
use App\Jobs\DraftArticleFromUrlJob;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use App\Support\Sources\ArticleDraft;
use App\Support\Sources\ArticleDraftStore;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
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
    ArticleDraftReviewerAgent::fake([[
        'approved' => true,
        'reasons' => [],
        'attribution_verified' => true,
        'faithful_to_source' => true,
        'possible_excessive_copying' => false,
        'unsupported_facts' => [],
        'html_compliant' => true,
    ]]);

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
        'https://example.com/down*' => Http::response('', 503),
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
        'cover_concept' => 'An open toolbox on a workbench with neatly arranged wrenches replaced by a single multi-tool.',
        'cover_headline' => 'Vite+ llega a los starter kits',
        'source_title' => 'Laravel Starter Kits Now Ship with Vite+',
        'source_author' => 'Paul Redmond',
        'source_site' => 'Laravel News',
        'source_published_at' => '2026-08-28T12:50:00-04:00',
        ...$overrides,
    ];
}

function runDraftJob(string $url = 'https://laravel-news.com/laravel-starter-kits-vite-plus', bool $importImages = true, bool $generateCover = true): array
{
    $store = app(ArticleDraftStore::class);
    $key = $store->start($url, 1);

    (new DraftArticleFromUrlJob($key, $url, $importImages, $generateCover))
        ->handle(app(DraftArticleFromUrl::class), $store);

    return [$key, $store->get($key)];
}

describe('import action', function () {
    it('queues the draft generation and marks the page as pending', function () {
        Queue::fake();

        $component = Livewire::test(CreateArticle::class)
            ->callAction('importFromUrl', data: [
                'url' => 'https://laravel-news.com/laravel-starter-kits-vite-plus',
                'import_images' => false,
                'generate_cover' => true,
            ])
            ->assertHasNoActionErrors()
            ->assertNotified('Generando borrador');

        $key = $component->get('pendingDraftKey');

        expect($key)->not->toBeNull()
            ->and(app(ArticleDraftStore::class)->get($key)['status'])->toBe(ArticleDraftStore::STATUS_PENDING);

        Queue::assertPushed(DraftArticleFromUrlJob::class, fn (DraftArticleFromUrlJob $job) => $job->draftKey === $key
            && $job->url === 'https://laravel-news.com/laravel-starter-kits-vite-plus'
            && $job->importImages === false
            && $job->generateCover === true);
    });

    it('rejects internal urls before queueing anything', function () {
        Queue::fake();

        Livewire::test(CreateArticle::class)
            ->callAction('importFromUrl', data: ['url' => 'http://127.0.0.1/admin'])
            ->assertHasActionErrors(['url']);

        Queue::assertNothingPushed();
    });

    it('requires a valid url in the import modal', function () {
        Queue::fake();

        Livewire::test(CreateArticle::class)
            ->callAction('importFromUrl', data: ['url' => 'not-a-url'])
            ->assertHasActionErrors(['url']);

        Queue::assertNothingPushed();
    });
});

describe('draft job', function () {
    it('blocks a draft rejected by the editorial reviewer', function () {
        ArticleFromUrlAgent::fake([fakeDraftResponse()]);
        ArticleDraftReviewerAgent::fake([[
            'approved' => false,
            'reasons' => ['Falta contexto verificable.'],
            'attribution_verified' => true,
            'faithful_to_source' => false,
            'possible_excessive_copying' => false,
            'unsupported_facts' => [],
            'html_compliant' => true,
        ]]);
        Image::fake()->preventStrayImages();

        [, $draft] = runDraftJob(importImages: false, generateCover: false);

        expect($draft['status'])->toBe(ArticleDraftStore::STATUS_FAILED)
            ->and($draft['error'])->toContain('revisión editorial')
            ->and($draft['source_hash'])->toBeString();
    });

    it('stores the generated draft with imported images and an ai cover', function () {
        ArticleFromUrlAgent::fake([fakeDraftResponse([
            'body_html' => '<h2>Qué cambia</h2><p>[[imagen:1]]</p><p>Según informa <a href="https://laravel-news.com/laravel-starter-kits-vite-plus">Laravel News</a>, los kits usan <code>vp</code>.</p>',
        ])]);
        Image::fake([base64_encode(testPngBinary())]);

        [, $draft] = runDraftJob();

        expect($draft['status'])->toBe(ArticleDraftStore::STATUS_COMPLETED)
            ->and($draft['warnings'])->toBe([])
            ->and($draft['fields'])->toMatchArray([
                'title' => 'Los starter kits de Laravel ahora usan Vite+',
                'slug' => 'los-starter-kits-de-laravel-ahora-usan-vite',
                'excerpt' => 'Todos los kits de inicio de Laravel se construyen ahora con Vite+.',
                'source_url' => 'https://laravel-news.com/laravel-starter-kits-vite-plus',
                'source_title' => 'Laravel Starter Kits Now Ship with Vite+',
                'source_author' => 'Paul Redmond',
                'source_site' => 'Laravel News',
                'source_published_at' => '2026-08-28 12:50:00',
            ])
            ->and($draft['fields']['body'])->toContain('<img src="/storage/post-images/')
            ->and($draft['fields']['body'])->not->toContain('[[imagen')
            ->and($draft['fields']['cover_image_path'])->toStartWith('covers/');

        Storage::disk('public')->assertExists($draft['fields']['cover_image_path']);
        expect(Storage::disk('public')->files('post-images'))->toHaveCount(1);

        ArticleFromUrlAgent::assertPrompted(fn ($prompt) => str_contains($prompt->prompt, 'Every Laravel starter kit now builds with Vite+')
            && str_contains($prompt->prompt, 'Paul Redmond')
            && str_contains($prompt->prompt, 'https://cdn.laravel-news.com/vite-plus.png')
            && $prompt->model === 'gemini-3.8-flash');
        ArticleDraftReviewerAgent::assertPrompted(fn ($prompt) => $prompt->model === 'gemini-3.8-flash');
        Image::assertGenerated(fn ($prompt) => $prompt->contains('An open toolbox on a workbench')
            && $prompt->contains('Flat vector illustration'));
    });

    it('skips image import and cover generation when disabled', function () {
        ArticleFromUrlAgent::fake([fakeDraftResponse(['body_html' => '<p>[[imagen:1]]</p><p>Texto sin imágenes. <a href="https://laravel-news.com/laravel-starter-kits-vite-plus">Fuente</a></p>'])]);
        Image::fake()->preventStrayImages();

        [, $draft] = runDraftJob(importImages: false, generateCover: false);

        expect($draft['status'])->toBe(ArticleDraftStore::STATUS_COMPLETED)
            ->and($draft['fields']['body'])->toBe('<p>Texto sin imágenes. <a href="https://laravel-news.com/laravel-starter-kits-vite-plus" target="_blank" rel="noopener noreferrer nofollow">Fuente</a></p>')
            ->and($draft['fields'])->not->toHaveKey('cover_image_path');

        Image::assertNothingGenerated();
        expect(Storage::disk('public')->files('post-images'))->toBeEmpty();
    });

    it('completes with a warning when the cover generation fails', function () {
        ArticleFromUrlAgent::fake([fakeDraftResponse()]);
        Image::fake([fn () => throw new RuntimeException('Image provider down')]);

        [, $draft] = runDraftJob(importImages: false);

        expect($draft['status'])->toBe(ArticleDraftStore::STATUS_COMPLETED)
            ->and($draft['fields']['title'])->toBe('Los starter kits de Laravel ahora usan Vite+')
            ->and($draft['warnings'])->toHaveCount(1)
            ->and($draft['warnings'][0])->toContain('portada');
    });

    it('sanitizes the generated html', function () {
        Image::fake()->preventStrayImages();
        ArticleFromUrlAgent::fake([fakeDraftResponse([
            'body_html' => '<h1>No</h1><p onclick="x()">Hola <script>alert(1)</script><img src="x.png"><a href="javascript:alert(1)">mal</a> <a href="https://laravel-news.com/laravel-starter-kits-vite-plus">Fuente</a></p>',
        ])]);

        [, $draft] = runDraftJob(importImages: false, generateCover: false);

        expect($draft['fields']['body'])->toBe('<h2>No</h2><p>Hola <a>mal</a> <a href="https://laravel-news.com/laravel-starter-kits-vite-plus" target="_blank" rel="noopener noreferrer nofollow">Fuente</a></p>');
    });

    it('marks the draft as failed when the source cannot be downloaded', function () {
        ArticleFromUrlAgent::fake()->preventStrayPrompts();

        [, $draft] = runDraftJob(url: 'https://example.com/down');

        expect($draft['status'])->toBe(ArticleDraftStore::STATUS_FAILED)
            ->and($draft['error'])->toContain('No se pudo descargar la página');

        ArticleFromUrlAgent::assertNeverPrompted();
    });
});

describe('pending draft polling', function () {
    it('does not let another user consume a draft', function () {
        $store = app(ArticleDraftStore::class);
        $key = $store->start('https://laravel-news.com/x', $this->admin->id);
        $store->complete($key, new ArticleDraft(['title' => 'Privado', 'slug' => 'privado', 'body' => '<p>Cuerpo</p>']));
        $otherUser = User::factory()->create();
        $otherUser->assignRole('admin');

        $this->actingAs($otherUser);

        Livewire::test(CreateArticle::class)
            ->set('pendingDraftKey', $key)
            ->call('checkPendingDraft')
            ->assertSet('pendingDraftKey', null)
            ->assertNotified('No tienes acceso a este borrador')
            ->assertFormSet(['title' => null]);

        expect($store->get($key))->not->toBeNull();
    });

    it('fills the form once the draft is completed', function () {
        $store = app(ArticleDraftStore::class);
        $key = $store->start('https://laravel-news.com/laravel-starter-kits-vite-plus', $this->admin->id);
        $store->complete($key, new ArticleDraft([
            'title' => 'Título generado',
            'slug' => 'titulo-generado',
            'excerpt' => 'Extracto generado',
            'body' => '<p>Cuerpo generado</p>',
            'source_url' => 'https://laravel-news.com/laravel-starter-kits-vite-plus',
            'source_title' => 'Original',
            'source_author' => 'Paul Redmond',
            'source_site' => 'Laravel News',
            'source_published_at' => '2026-08-28 12:50:00',
        ], ['Una imagen de la fuente no se pudo descargar y se omitió.']));

        Livewire::test(CreateArticle::class)
            ->set('pendingDraftKey', $key)
            ->call('checkPendingDraft')
            ->assertSet('pendingDraftKey', null)
            ->assertNotified('Borrador generado')
            ->assertFormSet([
                'title' => 'Título generado',
                'slug' => 'titulo-generado',
                'source_author' => 'Paul Redmond',
            ])
            ->assertFormSet(function (array $state) {
                expect(json_encode($state['body'], JSON_UNESCAPED_UNICODE))->toContain('Cuerpo generado');
            });

        expect($store->get($key))->toBeNull();
    });

    it('shows the draft warnings as notifications', function () {
        $store = app(ArticleDraftStore::class);
        $key = $store->start('https://laravel-news.com/x', $this->admin->id);
        $store->complete($key, new ArticleDraft(
            ['title' => 'Título', 'slug' => 'titulo', 'body' => '<p>Cuerpo</p>'],
            ['No se pudo generar la imagen de portada. Puedes subir una manualmente.'],
        ));

        Livewire::test(CreateArticle::class)
            ->set('pendingDraftKey', $key)
            ->call('checkPendingDraft')
            ->assertNotified('Aviso');
    });

    it('keeps waiting while the draft is pending', function () {
        $key = app(ArticleDraftStore::class)->start('https://laravel-news.com/x', $this->admin->id);

        Livewire::test(CreateArticle::class)
            ->set('pendingDraftKey', $key)
            ->call('checkPendingDraft')
            ->assertSet('pendingDraftKey', $key)
            ->assertSee('El borrador está en cola para generarse')
            ->assertFormSet(['title' => null]);
    });

    it('shows the current generation stage while waiting', function () {
        $store = app(ArticleDraftStore::class);
        $key = $store->start('https://laravel-news.com/x', $this->admin->id);
        $store->advance($key, 'reviewing');

        Livewire::test(CreateArticle::class)
            ->set('pendingDraftKey', $key)
            ->assertSee('Revisando la calidad editorial del borrador')
            ->assertSee('aria-live="polite"', false);
    });

    it('notifies the error when the draft failed', function () {
        $store = app(ArticleDraftStore::class);
        $key = $store->start('https://laravel-news.com/x', $this->admin->id);
        $store->fail($key, 'No se pudo descargar la página.');

        Livewire::test(CreateArticle::class)
            ->set('pendingDraftKey', $key)
            ->call('checkPendingDraft')
            ->assertSet('pendingDraftKey', null)
            ->assertNotified('No se pudo generar el borrador')
            ->assertFormSet(['title' => null]);
    });
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
