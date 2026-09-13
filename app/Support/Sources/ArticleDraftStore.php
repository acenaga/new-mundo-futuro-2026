<?php

namespace App\Support\Sources;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Keeps the state of article drafts generated in the background until the editor picks them up.
 */
class ArticleDraftStore
{
    public const string STATUS_PENDING = 'pending';

    public const string STATUS_COMPLETED = 'completed';

    public const string STATUS_FAILED = 'failed';

    public const int TTL_MINUTES = 60;

    /**
     * Register a new pending draft and return its key.
     */
    public function start(string $url, int $userId): string
    {
        $key = (string) Str::uuid();

        $this->put($key, [
            'status' => self::STATUS_PENDING,
            'url' => $url,
            'user_id' => $userId,
        ]);

        return $key;
    }

    public function complete(string $key, ArticleDraft $draft): void
    {
        $this->put($key, [
            ...($this->get($key) ?? []),
            'status' => self::STATUS_COMPLETED,
            'fields' => $draft->fields,
            'warnings' => $draft->warnings,
        ]);
    }

    public function fail(string $key, string $message): void
    {
        $this->put($key, [
            ...($this->get($key) ?? []),
            'status' => self::STATUS_FAILED,
            'error' => $message,
        ]);
    }

    /**
     * @return array{status: string, url?: string, user_id?: int, fields?: array<string, mixed>, warnings?: list<string>, error?: string}|null
     */
    public function get(string $key): ?array
    {
        $value = Cache::get($this->cacheKey($key));

        return is_array($value) ? $value : null;
    }

    public function forget(string $key): void
    {
        Cache::forget($this->cacheKey($key));
    }

    /**
     * @param  array<string, mixed>  $value
     */
    private function put(string $key, array $value): void
    {
        Cache::put($this->cacheKey($key), $value, now()->addMinutes(self::TTL_MINUTES));
    }

    private function cacheKey(string $key): string
    {
        return 'article-draft:'.$key;
    }
}
