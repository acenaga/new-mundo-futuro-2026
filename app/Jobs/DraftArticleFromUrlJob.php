<?php

namespace App\Jobs;

use App\Actions\Articles\DraftArticleFromUrl;
use App\Support\Sources\ArticleDraftStore;
use App\Support\Sources\SourceUnavailableException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class DraftArticleFromUrlJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public int $timeout = DraftArticleFromUrl::TIME_LIMIT;

    public function __construct(
        public string $draftKey,
        public string $url,
        public bool $importImages = true,
        public bool $generateCover = true,
    ) {}

    public function handle(DraftArticleFromUrl $draftArticle, ArticleDraftStore $store): void
    {
        try {
            $draft = $draftArticle($this->url, $this->importImages, $this->generateCover);
        } catch (SourceUnavailableException $exception) {
            $store->fail($this->draftKey, $exception->getMessage());

            return;
        }

        $store->complete($this->draftKey, $draft);
    }

    public function failed(?Throwable $exception): void
    {
        app(ArticleDraftStore::class)->fail(
            $this->draftKey,
            'Ocurrió un error inesperado al generar el borrador. Inténtalo de nuevo.',
        );
    }
}
