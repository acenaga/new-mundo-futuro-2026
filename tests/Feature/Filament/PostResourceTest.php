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
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

use function Pest\Laravel\assertDatabaseHas;

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
