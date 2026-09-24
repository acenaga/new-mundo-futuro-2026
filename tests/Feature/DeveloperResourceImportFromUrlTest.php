<?php

use App\Actions\DeveloperResources\DraftDeveloperResourceFromUrl;
use App\Ai\Agents\DeveloperResourceDraftReviewerAgent;
use App\Ai\Agents\DeveloperResourceFromUrlAgent;
use App\Filament\Resources\Developers\Pages\CreateDeveloper;
use App\Jobs\DraftDeveloperResourceFromUrlJob;
use App\Models\DeveloperResourceCategory;
use App\Models\User;
use App\Support\Sources\DeveloperResourceDraft;
use App\Support\Sources\DeveloperResourceDraftStore;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    DeveloperResourceCategory::factory()->create(['slug' => 'herramientas-ci', 'name' => 'Herramientas/CI']);
    Http::fake([
        'https://example.com' => Http::response('<html><head><title>Acme Dev</title><link rel="canonical" href="https://example.com"></head><body><h1>Acme Dev</h1><p>Build, test and deploy developer projects.</p><a href="/pricing">Pricing and free plan</a><a href="https://outside.example/pricing">External pricing</a></body></html>', 200, ['Content-Type' => 'text/html']),
        'https://example.com/pricing' => Http::response('<html><head><title>Pricing</title></head><body><h1>Free plan</h1><p>The free tier includes 100 builds each month and does not require a credit card.</p></body></html>', 200, ['Content-Type' => 'text/html']),
    ]);
});

function developerResourceAgentResponse(array $overrides = []): array
{
    return [
        'name' => 'Acme Dev',
        'external_url' => 'https://example.com',
        'summary' => 'Herramienta para construir, probar y desplegar proyectos de desarrollo.',
        'why_use_it' => 'Conviene para automatizar compilaciones y despliegues de proyectos pequeños.',
        'when_not_to_use_it' => 'No conviene si se necesitan más de 100 compilaciones mensuales en el plan gratuito.',
        'pricing' => 'freemium',
        'free_tier_details' => 'El plan gratuito incluye 100 compilaciones por mes.',
        'no_card_required' => true,
        'category_slug' => 'herramientas-ci',
        'technology_suggestions' => ['CI/CD', 'Deploy'],
        'pricing_evidence_url' => 'https://example.com/pricing',
        'pricing_evidence_excerpt' => 'The free tier includes 100 builds each month.',
        'card_evidence_url' => 'https://example.com/pricing',
        'card_evidence_excerpt' => 'does not require a credit card.',
        ...$overrides,
    ];
}

function approvedDeveloperResourceReview(array $overrides = []): array
{
    return [
        'approved' => true,
        'pricing_verified' => true,
        'free_tier_verified' => true,
        'card_requirement_verified' => true,
        'category_valid' => true,
        'source_consistent' => true,
        'reasons' => [],
        'unsupported_claims' => [],
        ...$overrides,
    ];
}

test('it generates a reviewed resource draft from official pages only', function () {
    DeveloperResourceFromUrlAgent::fake([developerResourceAgentResponse()]);
    DeveloperResourceDraftReviewerAgent::fake([approvedDeveloperResourceReview()]);
    $store = app(DeveloperResourceDraftStore::class);
    $key = $store->start('https://example.com', 1);

    (new DraftDeveloperResourceFromUrlJob($key, 'https://example.com'))->handle(app(DraftDeveloperResourceFromUrl::class), $store);

    $draft = $store->get($key);
    expect($draft['status'])->toBe(DeveloperResourceDraftStore::STATUS_COMPLETED)
        ->and($draft['canonical_url'])->toBe('https://example.com')
        ->and($draft['source_hash'])->toBeString()
        ->and($draft['fields'])->toMatchArray(['name' => 'Acme Dev', 'pricing' => 'freemium', 'no_card_required' => true])
        ->and($draft['technology_suggestions'])->toBe(['CI/CD', 'Deploy']);

    DeveloperResourceFromUrlAgent::assertPrompted(fn ($prompt) => str_contains($prompt->prompt, 'https://example.com/pricing') && ! str_contains($prompt->prompt, 'outside.example'));
});

test('it blocks a draft when the reviewer cannot verify card requirements', function () {
    DeveloperResourceFromUrlAgent::fake([developerResourceAgentResponse()]);
    DeveloperResourceDraftReviewerAgent::fake([approvedDeveloperResourceReview(['approved' => false, 'card_requirement_verified' => false, 'reasons' => ['La tarjeta no está confirmada.']])]);
    $store = app(DeveloperResourceDraftStore::class);
    $key = $store->start('https://example.com', 1);

    (new DraftDeveloperResourceFromUrlJob($key, 'https://example.com'))->handle(app(DraftDeveloperResourceFromUrl::class), $store);

    expect($store->get($key))->toMatchArray(['status' => DeveloperResourceDraftStore::STATUS_FAILED])
        ->and($store->get($key)['error'])->toContain('revisión editorial');
});

test('a completed draft only fills the generated fields and remains private to its creator', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    foreach (['ViewAny:DeveloperResource', 'Create:DeveloperResource'] as $permission) {
        Permission::findOrCreate($permission, 'web');
    }
    Role::findOrCreate('editor', 'web');
    $owner->givePermissionTo(['ViewAny:DeveloperResource', 'Create:DeveloperResource'])->assignRole('editor');
    $other->givePermissionTo(['ViewAny:DeveloperResource', 'Create:DeveloperResource'])->assignRole('editor');
    $store = app(DeveloperResourceDraftStore::class);
    $key = $store->start('https://example.com', $owner->id);
    $store->complete($key, new DeveloperResourceDraft([
        'name' => 'Completado', 'slug' => 'completado', 'external_url' => 'https://example.com', 'summary' => 'Resumen',
    ], ['CI/CD'], ['Plan y límites' => 'https://example.com/pricing — Free tier']));

    $this->actingAs($other);
    Livewire::test(CreateDeveloper::class)->set('pendingDeveloperResourceDraftKey', $key)->call('checkPendingDeveloperResourceDraft')
        ->assertNotified('No tienes acceso a este borrador')->assertFormSet(['name' => null]);

    $this->actingAs($owner);
    Livewire::test(CreateDeveloper::class)->fillForm(['is_featured' => true])->set('pendingDeveloperResourceDraftKey', $key)->call('checkPendingDeveloperResourceDraft')
        ->assertNotified('Campos completados, sin guardar')->assertFormSet(['name' => 'Completado', 'is_featured' => true]);
});

test('the Filament action queues one private resource draft after validating its URL', function () {
    $user = User::factory()->create();
    foreach (['ViewAny:DeveloperResource', 'Create:DeveloperResource'] as $permission) {
        Permission::findOrCreate($permission, 'web');
    }
    Role::findOrCreate('editor', 'web');
    $user->givePermissionTo(['ViewAny:DeveloperResource', 'Create:DeveloperResource'])->assignRole('editor');
    $this->actingAs($user);
    Queue::fake();

    $component = Livewire::test(CreateDeveloper::class)
        ->callAction('completeDeveloperResourceFromUrl', data: ['url' => 'https://example.com'])
        ->assertHasNoActionErrors()
        ->assertNotified('Investigando recurso');

    $key = $component->get('pendingDeveloperResourceDraftKey');
    expect($key)->not->toBeNull()
        ->and(app(DeveloperResourceDraftStore::class)->get($key)['user_id'])->toBe($user->id);
    Queue::assertPushed(DraftDeveloperResourceFromUrlJob::class, fn (DraftDeveloperResourceFromUrlJob $job): bool => $job->draftKey === $key && $job->url === 'https://example.com');
});
