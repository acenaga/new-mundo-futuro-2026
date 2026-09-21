<?php

namespace App\Jobs;

use App\Actions\Articles\DraftArticleFromUrl;
use App\Support\Sources\ArticleDraftStore;
use App\Support\Sources\SourceUnavailableException;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class DraftArticleFromUrlJob implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public int $uniqueFor = 600;

    public int $timeout = DraftArticleFromUrl::TIME_LIMIT;

    public function __construct(
        public string $draftKey,
        public string $url,
        public bool $importImages = true,
        public bool $generateCover = true,
    ) {}

    public function handle(DraftArticleFromUrl $draftArticle, ArticleDraftStore $store): void
    {
        if (($store->get($this->draftKey)['status'] ?? null) !== ArticleDraftStore::STATUS_PENDING) {
            return;
        }

        $startedAt = microtime(true);
        $store->advance($this->draftKey, 'extracting');

        try {
            $draft = $draftArticle($this->url, $this->importImages, $this->generateCover, function (string $stage, array $context = []) use ($store): void {
                $store->advance($this->draftKey, $stage, $context);
            });
        } catch (SourceUnavailableException $exception) {
            $store->fail($this->draftKey, $exception->getMessage());
            Log::warning('article_draft_failed', ['draft_key' => $this->draftKey, 'stage' => $store->get($this->draftKey)['stage'] ?? 'unknown', 'exception' => $exception::class]);

            return;
        }

        $store->complete($this->draftKey, $draft);
        Log::info('article_draft_completed', ['draft_key' => $this->draftKey, 'duration_ms' => (int) ((microtime(true) - $startedAt) * 1000)]);
    }

    public function uniqueId(): string
    {
        return $this->draftKey;
    }

    public function failed(?Throwable $exception): void
    {
        app(ArticleDraftStore::class)->fail(
            $this->draftKey,
            'Ocurrió un error inesperado al generar el borrador. Inténtalo de nuevo.',
        );
    }
}
