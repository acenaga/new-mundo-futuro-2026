<?php

use App\Enums\PostStatus;
use App\Enums\PostType;
use App\Filament\Resources\Articles\Pages\CreateArticle;
use App\Filament\Resources\Articles\Pages\ListArticles;
use App\Filament\Resources\Tutorials\Pages\ListTutorials;
use App\Models\Article;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tutorial;
use App\Models\User;
use App\Rules\ScheduledAtHalfHour;
use Illuminate\Support\Facades\Date;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    $permissions = ['ViewAny:Post', 'Create:Post', 'Update:Post'];

    foreach ($permissions as $permission) {
        Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
    }

    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web'])
        ->syncPermissions($permissions);
});

it('publish action sets status and date from the articles table', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $this->actingAs($admin);

    $draft = Article::factory()->create([
        'status' => PostStatus::Draft,
        'published_at' => null,
    ]);

    Livewire::test(ListArticles::class)
        ->callTableAction('publish', $draft);

    assertDatabaseHas(Post::class, [
        'id' => $draft->id,
        'status' => PostStatus::Published->value,
    ]);

    expect($draft->fresh()->published_at)->not()->toBeNull()
        ->and($draft->fresh()->isPublished())->toBeTrue();
});

it('unpublish action keeps the original publication date', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $this->actingAs($admin);

    $scheduledAt = Date::tomorrow();
    $post = Article::factory()->create([
        'status' => PostStatus::Published,
        'published_at' => $scheduledAt,
    ]);

    Livewire::test(ListArticles::class)
        ->callTableAction('unpublish', $post);

    assertDatabaseHas(Post::class, [
        'id' => $post->id,
        'status' => PostStatus::Draft->value,
        'published_at' => $scheduledAt->toDateTimeString(),
    ]);
});

it('unpublish action returns a post to draft', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $this->actingAs($admin);

    $post = Article::factory()->published()->create();

    Livewire::test(ListArticles::class)
        ->callTableAction('unpublish', $post);

    assertDatabaseHas(Post::class, [
        'id' => $post->id,
        'status' => PostStatus::Draft->value,
    ]);

    expect($post->fresh()->isPublished())->toBeFalse();
});

it('unpublish action works for a scheduled post', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $this->actingAs($admin);

    $post = Article::factory()->scheduled()->create();

    Livewire::test(ListArticles::class)
        ->callTableAction('unpublish', $post);

    assertDatabaseHas(Post::class, [
        'id' => $post->id,
        'status' => PostStatus::Draft->value,
    ]);
});

it('schedule action sets the scheduled status and date from the articles table', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $this->actingAs($admin);

    $draft = Article::factory()->create([
        'status' => PostStatus::Draft,
        'published_at' => null,
    ]);

    $slot = ScheduledAtHalfHour::nextSlot();

    Livewire::test(ListArticles::class)
        ->callTableAction('schedule', $draft, data: [
            'scheduled_at' => $slot->format('Y-m-d H:i:s'),
        ]);

    assertDatabaseHas(Post::class, [
        'id' => $draft->id,
        'status' => PostStatus::Scheduled->value,
        'published_at' => $slot->toDateTimeString(),
    ]);
});

it('publish action works from the tutorials table', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $this->actingAs($admin);

    $draft = Tutorial::factory()->create([
        'status' => PostStatus::Draft,
        'published_at' => null,
    ]);

    expect($draft->type)->toBe(PostType::Tutorial);

    Livewire::test(ListTutorials::class)
        ->callTableAction('publish', $draft);

    assertDatabaseHas(Post::class, [
        'id' => $draft->id,
        'status' => PostStatus::Published->value,
    ]);
});

it('article form auto-fills the publication date when switching to published', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $category = Category::factory()->create(['slug' => 'noticias']);
    $this->actingAs($admin);

    Livewire::test(CreateArticle::class)
        ->fillForm([
            'user_id' => $admin->id,
            'title' => 'Publicado con fecha automatica',
            'slug' => 'publicado-con-fecha-automatica',
            'body' => '<p>Contenido</p>',
            'status' => PostStatus::Published->value,
            'category_id' => $category->id,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $post = Post::query()->where('slug', 'publicado-con-fecha-automatica')->firstOrFail();

    expect($post->published_at)->not()->toBeNull()
        ->and($post->isPublished())->toBeTrue();
});
