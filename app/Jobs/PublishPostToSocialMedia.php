<?php

namespace App\Jobs;

use App\Events\PostPublished;
use App\Models\SocialPost;
use App\Services\Social\SocialPublisherRegistry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class PublishPostToSocialMedia implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    /**
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [30, 120];
    }

    public function __construct(public PostPublished $event)
    {
        //
    }

    public function handle(SocialPublisherRegistry $registry): void
    {
        $post = $this->event->post->refresh();

        foreach ($registry->enabledNetworks() as $network) {
            $this->publishTo($post, $network, $registry);
        }
    }

    private function publishTo($post, string $network, SocialPublisherRegistry $registry): void
    {
        $existing = SocialPost::query()
            ->where('post_id', $post->id)
            ->where('network', $network)
            ->first();

        if ($existing) {
            return;
        }

        try {
            $result = $registry->for($network)->publish($post);

            SocialPost::create([
                'post_id' => $post->id,
                'network' => $network,
                'external_id' => $result['external_id'],
                'external_url' => $result['external_url'],
                'published_at' => $post->published_at,
            ]);
        } catch (Throwable $e) {
            Log::error("No se pudo publicar el post [{$post->slug}] en [{$network}].", [
                'exception' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
