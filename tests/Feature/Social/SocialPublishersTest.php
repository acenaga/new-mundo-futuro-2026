<?php

use App\Events\PostPublished;
use App\Jobs\PublishPostToSocialMedia;
use App\Models\Post;
use App\Models\SocialPost;
use App\Services\Social\Publishers\DiscordPublisher;
use App\Services\Social\Publishers\FacebookPagePublisher;
use App\Services\Social\Publishers\InstagramPublisher;
use App\Services\Social\Publishers\ThreadsPublisher;
use App\Services\Social\Publishers\XPublisher;
use App\Services\Social\SocialPublisherRegistry;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    Http::preventStrayRequests();
});

it('the registry resolves all five networks', function () {
    $registry = app(SocialPublisherRegistry::class);

    expect($registry->for('discord'))->toBeInstanceOf(DiscordPublisher::class)
        ->and($registry->for('x'))->toBeInstanceOf(XPublisher::class)
        ->and($registry->for('facebook'))->toBeInstanceOf(FacebookPagePublisher::class)
        ->and($registry->for('threads'))->toBeInstanceOf(ThreadsPublisher::class)
        ->and($registry->for('instagram'))->toBeInstanceOf(InstagramPublisher::class);
});
it('posts a tweet: title + link truncated to 280 and records the id', function () {
    config(['services.x.access_token' => 'test-token', 'services.x.account' => 'mundofuturo']);

    Http::fake([
        'api.x.com/2/tweets' => Http::response(['data' => ['id' => 'tweet-1']], 201),
    ]);

    $post = Post::factory()->published()->create([
        'title' => 'Laravel 13: novedades',
        'excerpt' => 'Todo lo nuevo del framework.',
    ]);

    $result = app(XPublisher::class)->publish($post);

    expect($result)->external_id->toBe('tweet-1')
        ->external_url->toBe('https://x.com/mundofuturo/status/tweet-1');

    Http::assertSent(function ($request) use ($post) {
        $text = $request->data()['text'];

        return $request->url() === 'https://api.x.com/2/tweets'
            && str_starts_with($text, 'Laravel 13: novedades')
            && str_ends_with($text, ' — '.$post->publicUrl())
            && mb_strlen($text) <= 280;
    });
});

it('skips x when the token is missing', function () {
    config(['services.x.access_token' => null]);

    Http::fake();

    $result = app(XPublisher::class)->publish(Post::factory()->published()->create());

    expect($result)->external_id->toBeNull()->external_url->toBeNull();

    Http::assertNothingSent();
});

it('publishes to the facebook page feed and records the permalink', function () {
    config([
        'services.facebook.page_id' => '123-page',
        'services.facebook.page_access_token' => 'fb-token',
    ]);

    Http::fake([
        'graph.facebook.com/v23.0/123-page/feed' => Http::response(['id' => '123-page_995'], 200),
        'graph.facebook.com/v23.0/123-page_995*' => Http::response(['permalink_url' => '/mundofuturo/posts/995'], 200),
    ]);

    $post = Post::factory()->published()->create();

    $result = app(FacebookPagePublisher::class)->publish($post);

    expect($result)->external_id->toBe('123-page_995')
        ->external_url->toBe('https://www.facebook.com/mundofuturo/posts/995');

    Http::assertSent(function ($request) use ($post) {
        return str_contains($request->url(), '/feed')
            && $request->data()['link'] === $post->publicUrl()
            && blank($request->data()['message']) === false;
    });
});

it('auto-publishes a text thread and records the container id', function () {
    config([
        'services.threads.user_id' => '456-user',
        'services.threads.access_token' => 'th-token',
    ]);

    Http::fake([
        'graph.threads.net/v1.0/456-user/threads' => Http::response(['id' => 'container-1'], 200),
        'graph.threads.net/v1.0/container-1*' => Http::response(['permalink' => '/@mundofuturo/post/abc'], 200),
    ]);

    $post = Post::factory()->published()->create();

    $result = app(ThreadsPublisher::class)->publish($post);

    expect($result)->external_id->toBe('container-1')
        ->external_url->toBe('https://www.threads.net/@mundofuturo/post/abc');

    Http::assertSent(function ($request) {
        $data = $request->data();

        return str_contains($request->url(), 'graph.threads.net')
            && isset($data['media_type'])
            && $data['media_type'] === 'TEXT'
            && isset($data['auto_publish_text'])
            && $data['auto_publish_text'] === 'true';
    });
});

it('creates and publishes an instagram container when a cover exists', function () {
    config([
        'services.instagram.user_id' => '789-ig',
        'services.instagram.access_token' => 'ig-token',
    ]);

    Storage::fake('public');
    $path = UploadedFile::fake()->image('cover.jpg')->store('posts', 'public');

    Http::fake([
        'graph.instagram.com/v23.0/789-ig/media' => Http::response(['id' => 'container-2'], 200),
        'graph.instagram.com/v23.0/789-ig/media_publish' => Http::response(['id' => 'ig-post-1'], 200),
    ]);

    $post = Post::factory()->published()->create(['cover_image_path' => $path]);

    $result = app(InstagramPublisher::class)->publish($post);

    expect($result)->external_id->toBe('ig-post-1');

    Http::assertSent(function ($request) use ($post) {
        if (str_contains($request->url(), '/media_publish')) {
            return $request->data()['creation_id'] === 'container-2';
        }

        return str_contains($request->url(), '/media')
            && $request->data()['image_url'] === $post->coverImageUrl
            && $request->data()['caption'] !== '';
    });
});

it('skips instagram when the post has no cover image', function () {
    config([
        'services.instagram.user_id' => '789-ig',
        'services.instagram.access_token' => 'ig-token',
    ]);

    Http::fake();

    $result = app(InstagramPublisher::class)->publish(Post::factory()->published()->create());

    expect($result)->external_id->toBeNull()->external_url->toBeNull();

    Http::assertNothingSent();
});

it('the job fans out to every enabled network with credentials', function () {
    config([
        'services.discord.webhook_url' => 'https://discord.com/api/webhooks/123/token',
        'services.x.access_token' => 'x-token',
        'services.x.account' => 'mundofuturo',
        'services.facebook.page_id' => '123-page',
        'services.facebook.page_access_token' => 'fb-token',
        'services.threads.user_id' => '456-user',
        'services.threads.access_token' => 'th-token',
    ]);
    config(['services.instagram.user_id' => null]);
    config(['services.instagram.access_token' => null]);

    Http::fake([
        'discord.com/*' => Http::response(['id' => 'dc-1', 'channel_id' => 'ch-1'], 200),
        'api.x.com/*' => Http::response(['data' => ['id' => 'tw-1']], 201),
        'graph.facebook.com/*' => Http::response(['id' => 'fb-1'], 200),
        'graph.threads.net/*' => Http::response(['id' => 'th-1'], 200),
        '*' => Http::response([], 200),
    ]);

    $post = Post::factory()->published()->create();

    (new PublishPostToSocialMedia(new PostPublished($post)))
        ->handle(app(SocialPublisherRegistry::class));

    assertDatabaseHas(SocialPost::class, ['post_id' => $post->id, 'network' => 'discord', 'external_id' => 'dc-1']);
    assertDatabaseHas(SocialPost::class, ['post_id' => $post->id, 'network' => 'x', 'external_id' => 'tw-1']);
    assertDatabaseHas(SocialPost::class, ['post_id' => $post->id, 'network' => 'facebook', 'external_id' => 'fb-1']);
    assertDatabaseHas(SocialPost::class, ['post_id' => $post->id, 'network' => 'threads', 'external_id' => 'th-1']);
});

it('the job leaves empty records for unconfigured networks and publishes the rest', function () {
    config([
        'services.discord.webhook_url' => null,
        'services.x.access_token' => 'x-token',
        'services.facebook.page_id' => null,
        'services.facebook.page_access_token' => null,
        'services.threads.user_id' => null,
        'services.threads.access_token' => null,
        'services.instagram.user_id' => null,
        'services.instagram.access_token' => null,
    ]);

    Http::fake([
        'api.x.com/*' => Http::response(['data' => ['id' => 'tw-2']], 201),
    ]);

    $post = Post::factory()->published()->create();

    (new PublishPostToSocialMedia(new PostPublished($post)))
        ->handle(app(SocialPublisherRegistry::class));

    assertDatabaseHas(SocialPost::class, ['post_id' => $post->id, 'network' => 'x', 'external_id' => 'tw-2']);

    $skipped = SocialPost::query()->where('post_id', $post->id)
        ->whereIn('network', ['discord', 'facebook', 'threads', 'instagram'])
        ->count();

    expect($skipped)->toBe(4);
});
