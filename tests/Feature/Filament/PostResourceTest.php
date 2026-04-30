<?php

use App\Enums\PostStatus;
use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Filament\Resources\Posts\Pages\ListPosts;
use App\Models\Category;
use App\Models\Post;
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

test('admin can list posts', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $posts = Post::factory()->count(3)->create();

    $this->actingAs($admin);

    Livewire::test(ListPosts::class)
        ->assertCanSeeTableRecords($posts);
});

test('admin can create a post', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $category = Category::factory()->create();

    $this->actingAs($admin);

    Livewire::test(CreatePost::class)
        ->fillForm([
            'user_id' => $admin->id,
            'title' => 'My Test Post',
            'slug' => 'my-test-post',
            'body' => 'This is the body of the post.',
            'status' => PostStatus::Draft,
            'category_id' => $category->id,
            'allow_comments' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas(Post::class, [
        'title' => 'My Test Post',
        'slug' => 'my-test-post',
        'status' => PostStatus::Draft->value,
    ]);
});

test('admin can change author when creating a post', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $anotherUser = User::factory()->create();
    $anotherUser->assignRole('tutor');

    $this->actingAs($admin);

    Livewire::test(CreatePost::class)
        ->fillForm([
            'user_id' => $anotherUser->id,
            'title' => 'Post by another author',
            'slug' => 'post-by-another-author',
            'body' => 'Body content here.',
            'status' => PostStatus::Draft,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas(Post::class, [
        'title' => 'Post by another author',
        'user_id' => $anotherUser->id,
    ]);
});

test('tutor can create post and author is auto-set to themselves', function () {
    $tutor = User::factory()->create();
    $tutor->assignRole('tutor');

    $this->actingAs($tutor);

    Livewire::test(CreatePost::class)
        ->fillForm([
            'title' => 'Tutor Post Title',
            'slug' => 'tutor-post-title',
            'body' => 'Body content written by tutor.',
            'status' => PostStatus::Draft,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas(Post::class, [
        'title' => 'Tutor Post Title',
        'user_id' => $tutor->id,
    ]);
});

test('tutor cannot see author field in form', function () {
    $tutor = User::factory()->create();
    $tutor->assignRole('tutor');

    $this->actingAs($tutor);

    Livewire::test(CreatePost::class)
        ->assertFormFieldIsHidden('user_id');
});

test('title is required to create a post', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin);

    Livewire::test(CreatePost::class)
        ->fillForm([
            'title' => null,
            'body' => 'Some body content.',
            'status' => PostStatus::Draft,
        ])
        ->call('create')
        ->assertHasFormErrors(['title' => 'required']);
});

test('body is required to create a post', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin);

    Livewire::test(CreatePost::class)
        ->fillForm([
            'title' => 'Title Without Body',
            'slug' => 'title-without-body',
            'body' => '',
            'status' => PostStatus::Draft,
        ])
        ->call('create')
        ->assertHasFormErrors(['body']);
});

test('body strips non-youtube iframe embeds before persisting', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin);

    Livewire::test(CreatePost::class)
        ->fillForm([
            'title' => 'Body With Invalid Iframe',
            'slug' => 'body-with-invalid-iframe',
            'body' => '<p>Contenido</p><iframe src="https://example.com/embed/123"></iframe>',
            'status' => PostStatus::Draft,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $post = Post::query()
        ->where('slug', 'body-with-invalid-iframe')
        ->firstOrFail();

    expect($post->body)->not->toContain('<iframe');
    expect($post->body)->not->toContain('example.com');
});
