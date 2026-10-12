<?php

namespace App\Services\Social\Publishers;

use App\Contracts\SocialPublisher;
use App\Models\Post;
use App\Services\Social\Concerns\BuildsSocialPostText;
use App\Services\Social\Concerns\SkipsWhenUnconfigured;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class FacebookPagePublisher implements SocialPublisher
{
    use BuildsSocialPostText, SkipsWhenUnconfigured;

    private const GRAPH = 'https://graph.facebook.com/v23.0';

    public function publish(Post $post): array
    {
        $pageId = config('services.facebook.page_id');
        $token = config('services.facebook.page_access_token');

        if (! $pageId || ! $token) {
            return self::skipped();
        }

        $response = Http::asForm()
            ->post("{$this->graphUrl()}/{$pageId}/feed", [
                'link' => $post->publicUrl(),
                'message' => $this->message($post, 5000),
                'access_token' => $token,
            ])
            ->throw()
            ->json();

        $id = data_get($response, 'id');

        return [
            'external_id' => $id,
            'external_url' => $this->permalink($id, $token),
        ];
    }

    private function graphUrl(): string
    {
        return self::GRAPH;
    }

    private function permalink(?string $postId, string $token): ?string
    {
        if (! $postId) {
            return null;
        }

        try {
            $link = Http::get("{$this->graphUrl()}/{$postId}", [
                'fields' => 'permalink_url',
                'access_token' => $token,
            ])->json('permalink_url');

            return $link ? "https://www.facebook.com{$link}" : null;
        } catch (ConnectionException) {
            return null;
        }
    }
}
