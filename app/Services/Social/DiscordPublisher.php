<?php

namespace App\Services\Social;

use App\Contracts\SocialPublisher;
use App\Models\Post;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DiscordPublisher implements SocialPublisher
{
    private const DISCORD_URL = 'https://discord.com';

    private const EMBED_TOTAL_LIMIT = 6000;

    public function publish(Post $post): array
    {
        $webhookUrl = config('services.discord.webhook_url');

        if (! $webhookUrl) {
            return ['external_id' => null, 'external_url' => null];
        }

        $response = Http::asJson()
            ->post($this->executeUrl($webhookUrl), $this->payload($post))
            ->throw()
            ->json();

        return $this->fromMessage($response);
    }

    private function executeUrl(string $webhookUrl): string
    {
        return Str::of($webhookUrl)
            ->before('?')
            ->append('?wait=true')
            ->toString();
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(Post $post): array
    {
        $description = $post->excerpt ?: trim(strip_tags((string) $post->body));
        $description = Str::limit($description, 3900, '…');

        $embed = array_filter([
            'title' => Str::limit($post->title, 255),
            'description' => $description,
            'url' => $post->publicUrl(),
            'timestamp' => $post->published_at?->toIso8601String(),
            'image' => ['url' => $post->coverImageUrl],
        ], fn ($value) => filled($value));

        $totalLength = mb_strlen($embed['title'] ?? '')
            + mb_strlen($embed['description'] ?? '');

        if ($totalLength > self::EMBED_TOTAL_LIMIT) {
            $embed['description'] = Str::limit($description, self::EMBED_TOTAL_LIMIT - mb_strlen($embed['title']) - 50, '…');
        }

        return [
            'embeds' => [$embed],
            'allowed_mentions' => ['parse' => []],
        ];
    }

    /**
     * @return array{external_id: ?string, external_url: ?string}
     */
    private function fromMessage(?array $message): array
    {
        if (! $message || ! isset($message['id'])) {
            return ['external_id' => null, 'external_url' => null];
        }

        $externalUrl = isset($message['channel_id'])
            ? sprintf('%s/channels/%s/%s/%s', self::DISCORD_URL, $message['guild_id'] ?? '@me', $message['channel_id'], $message['id'])
            : null;

        return [
            'external_id' => $message['id'],
            'external_url' => $externalUrl,
        ];
    }
}
