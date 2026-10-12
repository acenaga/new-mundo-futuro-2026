<?php

namespace App\Console\Commands;

use App\Enums\PostStatus;
use App\Models\Post;
use Illuminate\Console\Command;

class PublishScheduledPosts extends Command
{
    protected $signature = 'posts:publish-scheduled';

    protected $description = 'Publica las entradas programadas cuya fecha de publicación ya llegó.';

    public function handle(): int
    {
        Post::query()
            ->where('status', PostStatus::Scheduled)
            ->where('published_at', '<=', now())
            ->each(function (Post $post): void {
                $post->forceFill(['status' => PostStatus::Published])->save();

                $this->info("Publicado: {$post->title} ({$post->slug}).");
            });

        return self::SUCCESS;
    }
}
