<?php

namespace App\Services\Social\Publishers;

use App\Contracts\SocialPublisher;
use App\Models\Post;
use App\Services\Social\Concerns\BuildsSocialPostText;
use App\Services\Social\Concerns\SkipsWhenUnconfigured;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class XPublisher implements SocialPublisher
{
    use BuildsSocialPostText, SkipsWhenUnconfigured;

    private const MAX_TEXT = 280;

    /**
     * X cobra $0.20 por post con link vs $0.015 sin link; el link ocupa 23
     * caracteres según el contador de X. Dejarlo configurable vía texto del post.
     */
    public function publish(Post $post): array
    {
        $token = config('services.x.access_token');

        if (! $token) {
            return self::skipped();
        }

        $text = $this->tweetText($post);

        $response = Http::asJson()
            ->withToken($token)
            ->post('https://api.x.com/2/tweets', ['text' => $text])
            ->throw()
            ->json();

        $id = data_get($response, 'data.id');

        return [
            'external_id' => $id,
            'external_url' => $id ? $this->postUrl($id) : null,
        ];
    }

    private function tweetText(Post $post): string
    {
        $title = trim($post->title);

        if (blank($post->publicUrl())) {
            return Str::limit($title, self::MAX_TEXT, '…');
        }

        // 23 chars = longitud con la que X contabiliza un link
        $available = self::MAX_TEXT - 23 - 3; // espacio para " — "

        return Str::limit($title, max($available, 40), '…').' — '.$post->publicUrl();
    }

    private function postUrl(string $id): ?string
    {
        $account = config('services.x.account');

        return $account ? "https://x.com/{$account}/status/{$id}" : null;
    }
}
