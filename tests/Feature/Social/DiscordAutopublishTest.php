<?php

use App\Enums\PostStatus;
use App\Events\PostPublished;
use App\Jobs\PublishPostToSocialMedia;
use App\Models\Post;
use App\Models\SocialPost;
use App\Services\Social\Publishers\DiscordPublisher;
use App\Services\Social\SocialPublisherRegistry;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;

it('dispatches the job when a post transitions to published', function () {
    Queue::fake();

    Post::factory()->create();

    Queue::assertNotPushed(PublishPostToSocialMedia::class);

    $draft = Post::factory()->create();
    $draft->forceFill(['status' => PostStatus::Published])->save();

    Queue::assertPushed(PublishPostToSocialMedia::class, 1);
});

it('does not dispatch again for an already published post', function () {
    Queue::fake();

    $post = Post::factory()->published()->create();

    Queue::assertPushed(PublishPostToSocialMedia::class, 1);

    $post->excerpt = 'Edición sin cambios de estado';
    $post->save();

    Queue::assertPushed(PublishPostToSocialMedia::class, 1);
});

it('the discord publisher posts an embed and returns the message data', function () {
    config(['services.discord.webhook_url' => 'https://discord.com/api/webhooks/123/token']);

    Http::fake([
        'discord.com/api/webhooks/123/token*' => Http::response([
            'id' => 'msg-42',
            'channel_id' => 'ch-42',
            'guild_id' => 'guild-42',
        ]),
    ]);

    $post = Post::factory()->published()->create([
        'title' => 'Tutorial de Laravel',
        'excerpt' => 'Aprende Laravel paso a paso.',
    ]);

    $result = app(DiscordPublisher::class)->publish($post);

    expect($result)->external_id->toBe('msg-42')
        ->external_url->toBe('https://discord.com/channels/guild-42/ch-42/msg-42');

    Http::assertSent(function (Request $request) use ($post) {
        $embed = $request->data()['embeds'][0] ?? [];

        return str_contains($request->url(), 'wait=true')
            && $embed['title'] === 'Tutorial de Laravel'
            && $embed['url'] === $post->publicUrl()
            && $embed['description'] === 'Aprende Laravel paso a paso.';
    });
});

it('the job records a social post row with the discord message id', function () {
    config(['services.discord.webhook_url' => 'https://discord.com/api/webhooks/123/token']);

    Http::fake([
        'discord.com/api/webhooks/123/token*' => Http::response([
            'id' => 'msg-7',
            'channel_id' => 'ch-7',
        ]),
    ]);

    $post = Post::factory()->published()->create();

    (new PublishPostToSocialMedia(new PostPublished($post)))
        ->handle(app(SocialPublisherRegistry::class));

    $socialPost = SocialPost::query()
        ->where('post_id', $post->id)
        ->where('network', 'discord')
        ->firstOrFail();

    expect($socialPost->external_id)->toBe('msg-7')
        ->and($socialPost->external_url)->toBe('https://discord.com/channels/@me/ch-7/msg-7');
});

it('the registry resolves only networks that have a publisher', function () {
    config(['services.social.networks' => ['discord', 'unknown-network']]);

    $registry = app(SocialPublisherRegistry::class);

    expect($registry->enabledNetworks())->toBe(['discord'])
        ->and($registry->for('discord'))->toBeInstanceOf(DiscordPublisher::class);
});

it('the job does not publish twice to the same network', function () {
    config(['services.discord.webhook_url' => 'https://discord.com/api/webhooks/123/token']);

    Http::fake();

    $post = Post::factory()->create();

    SocialPost::create([
        'post_id' => $post->id,
        'network' => 'discord',
        'external_id' => 'already',
        'external_url' => 'https://discord.example',
        'published_at' => now(),
    ]);

    $post->forceFill(['published_at' => now()->subDay()])->saveQuietly();

    (new PublishPostToSocialMedia(new PostPublished($post)))
        ->handle(app(SocialPublisherRegistry::class));

    Http::assertNothingSent();
});

it('the scheduler command triggers the social dispatch publish', function () {
    Queue::fake();

    $post = Post::factory()->scheduled()->create();
    $post->forceFill(['published_at' => now()->subMinute()])->saveQuietly();

    $this->artisan('posts:publish-scheduled')->assertSuccessful();

    Queue::assertPushed(PublishPostToSocialMedia::class, 1);
});

it('the job publishes nothing when the discord webhook is not configured', function () {
    config(['services.discord.webhook_url' => null]);

    Http::fake();

    $post = Post::factory()->published()->create();

    (new PublishPostToSocialMedia(new PostPublished($post)))
        ->handle(app(SocialPublisherRegistry::class));

    Http::assertNothingSent();

    expect($post->socialPosts()->where('network', 'discord')->exists())->toBeTrue();
});
