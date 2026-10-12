<?php

use App\Enums\PostStatus;
use App\Models\Article;
use App\Models\DeveloperResource;
use App\Models\Post;
use App\Models\Tutorial;
use App\Rules\ScheduledAtHalfHour;
use Illuminate\Support\Facades\Date;

use function Pest\Laravel\get;

it('only returns posts published with a past date', function () {
    $published = Post::factory()->published()->create(['published_at' => now()->subDay()]);
    $draft = Post::factory()->create();
    $scheduled = Post::factory()->scheduled()->article()->create();
    $nullable = Post::factory()->published()->article()->create(['published_at' => null]);

    $slugs = Post::published()->pluck('slug');

    expect($slugs)->toContain($published->slug, $nullable->slug)
        ->not->toContain($draft->slug, $scheduled->slug)
        ->toHaveCount(2);
});

it('returns 404 for a future-dated article detail', function () {
    $futurePost = Post::factory()->scheduled()->article()->create();

    get(route('publicaciones.show', ['article' => $futurePost->slug]))->assertNotFound();
});

it('returns 404 for a future-dated tutorial detail', function () {
    $futureTutorial = Post::factory()->scheduled()->tutorial()->create();

    get(route('tutoriales.show', ['tutorial' => $futureTutorial->slug]))->assertNotFound();
});

it('excludes not-yet-published posts from the sitemap', function () {
    $visible = Post::factory()->published()->article()->create();
    $scheduled = Post::factory()->scheduled()->article()->create();

    $content = get(route('sitemap'))->getContent();

    expect($content)
        ->toContain($visible->slug)
        ->not->toContain($scheduled->slug);
});

it('isPublished reflects status and date coherently', function () {
    $saved = Post::factory()->create();

    expect($saved->isPublished())->toBeFalse();

    $live = Post::factory()->published()->create();

    expect($live->isPublished())->toBeTrue();

    $future = Post::factory()->scheduled()->create();

    expect($future->isPublished())->toBeFalse();
});

it('saving a published post without date self-heals with now()', function () {
    $post = Post::factory()->create([
        'status' => PostStatus::Published,
        'published_at' => null,
    ]);

    expect($post->refresh()->published_at)->not()->toBeNull()
        ->and($post->isPublished())->toBeTrue();
});

it('a draft post keeps its saved future date for later republishing', function () {
    $post = Post::factory()->create([
        'status' => PostStatus::Draft,
        'published_at' => Date::tomorrow(),
    ]);

    expect($post->refresh()->published_at->toDateString())->toBe(Date::tomorrow()->toDateString());
});

it('a scheduled post rejects a non half-hour aligned date', function () {
    Post::factory()->create([
        'status' => PostStatus::Scheduled,
        'published_at' => ScheduledAtHalfHour::nextSlot()->setMinute(15),
    ]);
})->throws(InvalidArgumentException::class);

it('a scheduled post rejects a past date', function () {
    Post::factory()->create([
        'status' => PostStatus::Scheduled,
        'published_at' => now()->subDay(),
    ]);
})->throws(InvalidArgumentException::class);

it('the scheduler publishes posts whose time has arrived', function () {
    $post = Post::factory()->scheduled()->create();

    $post->forceFill(['published_at' => now()->subMinute()])->saveQuietly();
    $post->refresh();

    expect($post->isPublished())->toBeFalse();

    $this->artisan('posts:publish-scheduled')->assertSuccessful();

    expect($post->refresh()->status)->toBe(PostStatus::Published)
        ->and($post->isPublished())->toBeTrue();
});

it('the scheduler leaves future scheduled posts untouched', function () {
    $post = Post::factory()->scheduled()->create();

    $this->artisan('posts:publish-scheduled')->assertSuccessful();

    expect($post->refresh()->status)->toBe(PostStatus::Scheduled);
});

it('isPublished on developer resource reflects status and date', function () {
    $draft = DeveloperResource::factory()->create(['published_at' => now()->subDay()]);
    $future = DeveloperResource::factory()->published()->create(['published_at' => Date::tomorrow()]);
    $live = DeveloperResource::factory()->published()->create();

    expect($draft->isPublished())->toBeFalse()
        ->and($future->isPublished())->toBeFalse()
        ->and($live->isPublished())->toBeTrue();
});

it('scopes work on article and tutorial subclasses', function () {
    $article = Article::factory()->published()->create();
    $tutorial = Tutorial::factory()->published()->create();
    Post::factory()->published()->article()->create(['published_at' => Date::tomorrow()]);

    expect(Article::published()->pluck('slug'))->toContain($article->slug)->toHaveCount(1)
        ->and(Tutorial::published()->pluck('slug'))->toContain($tutorial->slug)->toHaveCount(1);
});
