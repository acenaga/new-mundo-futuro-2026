<?php

use App\Enums\PostStatus;
use App\Enums\PostType;
use App\Filament\Resources\Articles\Pages\CreateArticle;
use App\Filament\Resources\Articles\Pages\ListArticles;
use App\Filament\Resources\Tutorials\Pages\CreateTutorial;
use App\Filament\Resources\Tutorials\Pages\ListTutorials;
use App\Models\Article;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tutorial;
use App\Models\User;
use App\Support\Images\CoverImageOptimizer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

beforeEach(function () {
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    $permissions = [
        'ViewAny:Post',
        'View:Post',
        'Create:Post',
        'Update:Post',
        'Delete:Post',
        'Restore:Post',
        'ForceDelete:Post',
        'ForceDeleteAny:Post',
        'RestoreAny:Post',
        'Replicate:Post',
        'Reorder:Post',
    ];

    foreach ($permissions as $permission) {
        Permission::firstOrCreate([
            'name' => $permission,
            'guard_name' => 'web',
        ]);
    }

    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'tutor', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'web']);

    Role::findByName('admin', 'web')->syncPermissions($permissions);
    Role::findByName('tutor', 'web')->syncPermissions([
        'ViewAny:Post',
        'View:Post',
        'Create:Post',
    ]);
});

test('admin can list articles', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $articles = Article::factory()->count(3)->create();

    $this->actingAs($admin);

    Livewire::test(ListArticles::class)
        ->assertCanSeeTableRecords($articles);
});

test('admin can list tutorials', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $tutorials = Tutorial::factory()->count(3)->create();

    $this->actingAs($admin);

    Livewire::test(ListTutorials::class)
        ->assertCanSeeTableRecords($tutorials);
});

test('admin can create an article', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $category = Category::factory()->create(['slug' => 'noticias']);

    $this->actingAs($admin);

    Livewire::test(CreateArticle::class)
        ->fillForm([
            'user_id' => $admin->id,
            'title' => 'My Test Article',
            'slug' => 'my-test-article',
            'body' => 'This is the body of the article.',
            'status' => PostStatus::Draft,
            'category_id' => $category->id,
            'allow_comments' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas(Post::class, [
        'title' => 'My Test Article',
        'slug' => 'my-test-article',
        'type' => PostType::Article->value,
        'status' => PostStatus::Draft->value,
        'category_id' => $category->id,
    ]);
});

test('admin can create a tutorial', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $tutorialCategory = Category::firstOrCreate(['slug' => 'tutoriales'], ['name' => 'Tutoriales']);

    $this->actingAs($admin);

    Livewire::test(CreateTutorial::class)
        ->fillForm([
            'user_id' => $admin->id,
            'title' => 'My Test Tutorial',
            'slug' => 'my-test-tutorial',
            'body' => 'This is the body of the tutorial.',
            'status' => PostStatus::Draft,
            'category_id' => $tutorialCategory->id,
            'allow_comments' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas(Post::class, [
        'title' => 'My Test Tutorial',
        'slug' => 'my-test-tutorial',
        'type' => PostType::Tutorial->value,
        'status' => PostStatus::Draft->value,
        'category_id' => $tutorialCategory->id,
    ]);
});

test('admin cannot create a tutorial with null category', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin);

    Livewire::test(CreateTutorial::class)
        ->fillForm([
            'user_id' => $admin->id,
            'title' => 'My Null Category Tutorial',
            'slug' => 'my-null-category-tutorial',
            'body' => 'Body content.',
            'status' => PostStatus::Draft,
            'category_id' => null,
        ])
        ->call('create')
        ->assertHasFormErrors(['category_id' => 'required']);
});

test('admin cannot create a tutorial with non-tutorial category', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $regularCategory = Category::factory()->create(['slug' => 'noticias']);

    $this->actingAs($admin);

    Livewire::test(CreateTutorial::class)
        ->fillForm([
            'user_id' => $admin->id,
            'title' => 'My Wrong Category Tutorial',
            'slug' => 'my-wrong-category-tutorial',
            'body' => 'Body content.',
            'status' => PostStatus::Draft,
            'category_id' => $regularCategory->id,
        ])
        ->call('create')
        ->assertHasFormErrors(['category_id']);
});

test('admin cannot create an article with tutoriales category', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $tutorialCategory = Category::firstOrCreate(['slug' => 'tutoriales'], ['name' => 'Tutoriales']);

    $this->actingAs($admin);

    Livewire::test(CreateArticle::class)
        ->fillForm([
            'user_id' => $admin->id,
            'title' => 'My Wrong Category Article',
            'slug' => 'my-wrong-category-article',
            'body' => 'Body content.',
            'status' => PostStatus::Draft,
            'category_id' => $tutorialCategory->id,
        ])
        ->call('create')
        ->assertHasFormErrors(['category_id']);
});

test('tutor can create article and author is auto-set to themselves', function () {
    $tutor = User::factory()->create();
    $tutor->assignRole('tutor');

    $this->actingAs($tutor);

    Livewire::test(CreateArticle::class)
        ->fillForm([
            'title' => 'Tutor Article Title',
            'slug' => 'tutor-article-title',
            'body' => 'Body content written by tutor.',
            'status' => PostStatus::Draft,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas(Post::class, [
        'title' => 'Tutor Article Title',
        'user_id' => $tutor->id,
    ]);
});

test('tutor cannot see author field in article form', function () {
    $tutor = User::factory()->create();
    $tutor->assignRole('tutor');

    $this->actingAs($tutor);

    Livewire::test(CreateArticle::class)
        ->assertFormFieldIsHidden('user_id');
});

test('body strips non-youtube iframe embeds before persisting in article', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin);

    Livewire::test(CreateArticle::class)
        ->fillForm([
            'title' => 'Article With Invalid Iframe',
            'slug' => 'article-with-invalid-iframe',
            'body' => '<p>Contenido</p><iframe src="https://example.com/embed/123"></iframe>',
            'status' => PostStatus::Draft,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $post = Post::query()
        ->where('slug', 'article-with-invalid-iframe')
        ->firstOrFail();

    expect($post->body)->not->toContain('<iframe');
    expect($post->body)->not->toContain('example.com');
});

test('cover image is optimised and stored as webp when creating an article', function () {
    Storage::fake('public');

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $category = Category::factory()->create(['slug' => 'noticias']);

    $this->actingAs($admin);

    Livewire::test(CreateArticle::class)
        ->fillForm([
            'title' => 'Artículo con portada',
            'excerpt' => 'Extracto',
            'body' => '<p>Contenido</p>',
            'category_id' => $category->id,
            'status' => PostStatus::Draft,
            'cover_image_path' => UploadedFile::fake()->image('portada.png', 2400, 1350),
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $article = Article::query()->where('title', 'Artículo con portada')->firstOrFail();

    expect($article->cover_image_path)->toEndWith('.webp');
    Storage::disk('public')->assertExists($article->cover_image_path);

    [$width] = getimagesize(Storage::disk('public')->path($article->cover_image_path));
    expect($width)->toBe(CoverImageOptimizer::MAX_WIDTH);
});

test('article slug is generated from the title', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin);

    Livewire::test(CreateArticle::class)
        ->fillForm(['title' => 'Nuevo artículo de Laravel'])
        ->assertFormSet(['slug' => 'nuevo-articulo-de-laravel']);
});

test('admin cannot create an article with invalid slug format', function (string $slug) {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin);

    Livewire::test(CreateArticle::class)
        ->fillForm([
            'user_id' => $admin->id,
            'title' => 'Article With Invalid Slug',
            'slug' => $slug,
            'body' => '<p>Contenido</p>',
            'status' => PostStatus::Draft,
        ])
        ->call('create')
        ->assertHasFormErrors(['slug'])
        ->assertSee(__('validation.slug_format', ['attribute' => 'slug']));

    assertDatabaseMissing(Post::class, ['slug' => $slug]);
})->with([
    'slash' => 'foo/bar',
    'spaces' => 'foo bar',
    'uppercase' => 'Foo-Bar',
    'underscore' => 'foo_bar',
]);

test('admin cannot create a tutorial with invalid slug format', function (string $slug) {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $tutorialCategory = Category::firstOrCreate(['slug' => 'tutoriales'], ['name' => 'Tutoriales']);

    $this->actingAs($admin);

    Livewire::test(CreateTutorial::class)
        ->fillForm([
            'user_id' => $admin->id,
            'title' => 'Tutorial With Invalid Slug',
            'slug' => $slug,
            'body' => '<p>Contenido</p>',
            'status' => PostStatus::Draft,
            'category_id' => $tutorialCategory->id,
        ])
        ->call('create')
        ->assertHasFormErrors(['slug'])
        ->assertSee(__('validation.slug_format', ['attribute' => 'slug']));

    assertDatabaseMissing(Post::class, ['slug' => $slug]);
})->with([
    'slash' => 'foo/bar',
    'spaces' => 'foo bar',
    'uppercase' => 'Foo-Bar',
    'underscore' => 'foo_bar',
]);

test('article and tutorial slugs share the same uniqueness constraint', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Article::factory()->create(['slug' => 'slug-compartido']);
    $tutorialCategory = Category::firstOrCreate(['slug' => 'tutoriales'], ['name' => 'Tutoriales']);

    $this->actingAs($admin);

    Livewire::test(CreateTutorial::class)
        ->fillForm([
            'user_id' => $admin->id,
            'title' => 'Tutorial With Duplicated Slug',
            'slug' => 'slug-compartido',
            'body' => '<p>Contenido</p>',
            'status' => PostStatus::Draft,
            'category_id' => $tutorialCategory->id,
        ])
        ->call('create')
        ->assertHasFormErrors(['slug' => 'unique']);

    assertDatabaseMissing(Post::class, ['title' => 'Tutorial With Duplicated Slug']);
});

test('cover image rejects a tampered existing path when creating an article', function () {
    Storage::fake('public');

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin);

    Livewire::test(CreateArticle::class)
        ->fillForm([
            'user_id' => $admin->id,
            'title' => 'Article With Tampered Cover',
            'slug' => 'article-with-tampered-cover',
            'body' => '<p>Contenido</p>',
            'status' => PostStatus::Draft,
            'cover_image_path' => ['tampered-path' => 'covers/another-users-image.webp'],
        ])
        ->call('create')
        ->assertHasFormErrors(['cover_image_path']);

    assertDatabaseMissing(Post::class, ['title' => 'Article With Tampered Cover']);
});

test('user without create permission cannot open post creation pages', function (string $page) {
    $editor = User::factory()->create();
    $editor->assignRole('editor');

    $this->actingAs($editor);

    Livewire::test($page)->assertForbidden();
})->with([
    'articles' => CreateArticle::class,
    'tutorials' => CreateTutorial::class,
]);
