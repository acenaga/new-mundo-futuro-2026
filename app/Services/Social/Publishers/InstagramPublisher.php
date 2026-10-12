<?php

namespace App\Services\Social\Publishers;

use App\Contracts\SocialPublisher;
use App\Models\Post;
use App\Services\Social\Concerns\BuildsSocialPostText;
use App\Services\Social\Concerns\SkipsWhenUnconfigured;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class InstagramPublisher implements SocialPublisher
{
    use BuildsSocialPostText, SkipsWhenUnconfigured;

    private const GRAPH = 'https://graph.instagram.com/v23.0';

    private const MAX_CAPTION = 2200;

    public function publish(Post $post): array
    {
        $userId = config('services.instagram.user_id');
        $token = config('services.instagram.access_token');

        if (! $userId || ! $token) {
            return self::skipped();
        }

        if (blank($post->coverImageUrl)) {
            return self::skipped();
        }

        $container = Http::asForm()
            ->post("{$this->graphUrl()}/{$userId}/media", [
                'image_url' => $post->coverImageUrl,
                'caption' => $this->caption($post),
                'access_token' => $token,
            ])
            ->throw()
            ->json();

        $containerId = data_get($container, 'id');

        if (! $containerId) {
            return self::skipped();
        }

        $published = Http::asForm()
            ->post("{$this->graphUrl()}/{$userId}/media_publish", [
                'creation_id' => $containerId,
                'access_token' => $token,
            ])
            ->throw()
            ->json();

        $id = data_get($published, 'id');

        return [
            'external_id' => $id,
            'external_url' => null,
        ];
    }

    private function graphUrl(): string
    {
        return self::GRAPH;
    }

    private function caption(Post $post): string
    {
        $caption = $this->message($post, self::MAX_CAPTION - 40 - 3).' — '.$post->publicUrl();

        return Str::limit($caption, self::MAX_CAPTION, '…');
    }
}
