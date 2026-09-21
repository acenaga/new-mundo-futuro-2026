<?php

use App\Enums\DeveloperResourcePricing;
use App\Enums\DeveloperResourceStatus;
use App\Filament\Resources\Developers\Pages\CreateDeveloper;
use App\Filament\Resources\Developers\Pages\ListDevelopers;
use App\Models\DeveloperResource;
use App\Models\DeveloperResourceCategory;
use App\Models\DeveloperResourceTechnology;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::findOrCreate('admin', 'web');
    Role::findOrCreate('editor', 'web');
    Role::findOrCreate('tutor', 'web');
});

test('an editor can create a developer resource from Filament', function () {
    $editor = User::factory()->create();
    $editor->assignRole('editor');
    $category = DeveloperResourceCategory::factory()->create();
    $technology = DeveloperResourceTechnology::factory()->create();

    $this->actingAs($editor);

    Livewire::test(CreateDeveloper::class)
        ->fillForm([
            'name' => 'Cloudflare Pages',
            'slug' => 'cloudflare-pages',
            'external_url' => 'https://pages.cloudflare.com',
            'summary' => 'Despliegues rápidos para proyectos web.',
            'why_use_it' => 'Sirve para publicar sitios estáticos.',
            'when_not_to_use_it' => 'No es una base de datos gestionada.',
            'pricing' => DeveloperResourcePricing::Freemium,
            'free_tier_details' => 'Incluye un plan gratuito para proyectos pequeños.',
            'no_card_required' => true,
            'developer_resource_category_id' => $category->id,
            'technologies' => [$technology->id],
            'status' => DeveloperResourceStatus::Draft,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $resource = DeveloperResource::query()->where('slug', 'cloudflare-pages')->firstOrFail();

    expect($resource->user_id)->toBe($editor->id)
        ->and($resource->technologies)->toHaveCount(1);
});

test('a tutor cannot access the developer resources panel', function () {
    $tutor = User::factory()->create();
    $tutor->assignRole('tutor');

    $this->actingAs($tutor);

    Livewire::test(ListDevelopers::class)->assertForbidden();
});

test('an editor can mark a resource as reviewed', function () {
    $editor = User::factory()->create();
    $editor->assignRole('editor');
    $resource = DeveloperResource::factory()->create(['last_verified_at' => now()->subDays(91)]);

    $this->actingAs($editor);

    Livewire::test(ListDevelopers::class)->callTableAction('markAsVerified', $resource);

    expect($resource->fresh()->is_stale)->toBeFalse();
});
