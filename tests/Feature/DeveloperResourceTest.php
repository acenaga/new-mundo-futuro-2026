<?php

use App\Enums\DeveloperResourcePricing;
use App\Enums\DeveloperResourceStatus;
use App\Models\DeveloperResource;
use App\Models\DeveloperResourceCategory;
use App\Models\DeveloperResourceTechnology;

test('the public catalog only lists published developer resources', function () {
    $published = DeveloperResource::factory()->published()->create(['name' => 'Visible Resource']);
    DeveloperResource::factory()->create(['name' => 'Hidden Draft']);

    $this->get(route('recursos'))
        ->assertSuccessful()
        ->assertSee('Visible Resource')
        ->assertDontSee('Hidden Draft');

    $this->get(route('recursos.show', $published))->assertSuccessful();
});

test('the catalog filters by category technology and no card requirement', function () {
    $category = DeveloperResourceCategory::factory()->create(['slug' => 'hosting-deploy']);
    $technology = DeveloperResourceTechnology::factory()->create(['slug' => 'docker']);
    $matching = DeveloperResource::factory()->published()->create([
        'developer_resource_category_id' => $category->id,
        'name' => 'No Card Deploy',
        'no_card_required' => true,
    ]);
    $matching->technologies()->attach($technology);
    DeveloperResource::factory()->published()->create(['name' => 'Other Resource']);

    $this->get(route('recursos', ['categoria' => 'hosting-deploy', 'tecnologia' => 'docker', 'sin_tarjeta' => 1]))
        ->assertSuccessful()
        ->assertSee('No Card Deploy')
        ->assertDontSee('Other Resource');
});

test('draft developer resources are not accessible through public detail pages', function () {
    $draft = DeveloperResource::factory()->create();

    $this->get(route('recursos.show', $draft))->assertNotFound();
});

test('a resource is stale after ninety days without a review', function () {
    $resource = DeveloperResource::factory()->create(['last_verified_at' => now()->subDays(91)]);

    expect($resource->is_stale)->toBeTrue();

    $resource->markAsVerified(now());

    expect($resource->fresh()->is_stale)->toBeFalse();
});

test('resource pricing and publication state are typed', function () {
    $resource = DeveloperResource::factory()->published()->create([
        'pricing' => DeveloperResourcePricing::Free,
        'status' => DeveloperResourceStatus::Published,
    ]);

    expect($resource->pricing)->toBe(DeveloperResourcePricing::Free)
        ->and($resource->status)->toBe(DeveloperResourceStatus::Published);
});
