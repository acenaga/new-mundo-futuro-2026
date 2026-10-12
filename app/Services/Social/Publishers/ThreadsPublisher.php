<?php

namespace App\Services\Social\Publishers;

use App\Contracts\SocialPublisher;
use App\Models\Post;
use App\Services\Social\Concerns\BuildsSocialPostText;
use App\Services\Social\Concerns\SkipsWhenUnconfigured;
use Illuminate\Support\Facades\Http;

class ThreadsPublisher implements SocialPublisher
{
    use BuildsSocialPostText, SkipsWhenUnconfigured;

    private const GRAPH = 'https://graph.threads.net/v1.0';

    private const MAX_TEXT = 400;

    public function publish(Post $post): array
    {
        $userId = config('services.threads.user_id');
        $token = config('services.threads.access_token');

        if (! $userId || ! $token) {
            return self::skipped();
        }

        // Texto con link, auto-publicado (solo válido para posts de texto)
        $response = Http::asForm()
            ->post("{$this->graphUrl()}/{$userId}/threads", [
                'media_type' => 'TEXT',
                'text' => $this->message($post, self::MAX_TEXT - 60 - 3).' — '.$post->publicUrl(),
                'auto_publish_text' => 'true',
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

    private function permalink(?string $id, string $token): ?string
    {
        if (! $id) {
            return null;
        }

        $permalink = Http::get("{$this->graphUrl()}/{$id}", [
            'fields' => 'permalink',
            'access_token' => $token,
        ])->json('permalink');

        return $permalink ? "https://www.threads.net{$permalink}" : null;
    }
}
