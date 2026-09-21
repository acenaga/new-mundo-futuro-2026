<?php

namespace App\Jobs;

use App\Actions\DeveloperResources\DraftDeveloperResourceFromUrl;
use App\Support\Sources\DeveloperResourceDraftStore;
use App\Support\Sources\SourceUnavailableException;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class DraftDeveloperResourceFromUrlJob implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public int $uniqueFor = 600;

    public int $timeout = DraftDeveloperResourceFromUrl::TIME_LIMIT;

    public function __construct(public string $draftKey, public string $url) {}

    public function handle(DraftDeveloperResourceFromUrl $draftResource, DeveloperResourceDraftStore $store): void
    {
        if (($store->get($this->draftKey)['status'] ?? null) !== DeveloperResourceDraftStore::STATUS_PENDING) {
            return;
        }

        $startedAt = microtime(true);
        $store->advance($this->draftKey, 'extracting');
        try {
            $draft = $draftResource($this->url, fn (string $stage, array $context = []) => $store->advance($this->draftKey, $stage, $context));
        } catch (SourceUnavailableException $exception) {
            $store->fail($this->draftKey, $exception->getMessage());
            Log::warning('developer_resource_draft_failed', ['draft_key' => $this->draftKey, 'stage' => $store->get($this->draftKey)['stage'] ?? 'unknown', 'host' => parse_url($this->url, PHP_URL_HOST), 'exception' => $exception::class]);

            return;
        }

        $store->complete($this->draftKey, $draft);
        Log::info('developer_resource_draft_completed', ['draft_key' => $this->draftKey, 'host' => parse_url($this->url, PHP_URL_HOST), 'duration_ms' => (int) ((microtime(true) - $startedAt) * 1000)]);
    }

    public function uniqueId(): string
    {
        return $this->draftKey;
    }

    public function failed(?Throwable $exception): void
    {
        app(DeveloperResourceDraftStore::class)->fail($this->draftKey, 'Ocurrió un error inesperado al generar el recurso. Inténtalo de nuevo.');
    }
}
