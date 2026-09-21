<?php

namespace App\Support\Sources;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class DeveloperResourceDraftStore
{
    public const string STATUS_PENDING = 'pending';

    public const string STATUS_COMPLETED = 'completed';

    public const string STATUS_FAILED = 'failed';

    public const int TTL_MINUTES = 60;

    public function start(string $url, int $userId): string
    {
        $key = (string) Str::uuid();
        $this->put($key, ['status' => self::STATUS_PENDING, 'stage' => 'queued', 'url' => $url, 'user_id' => $userId]);

        return $key;
    }

    public function complete(string $key, DeveloperResourceDraft $draft): void
    {
        $this->put($key, [...($this->get($key) ?? []), 'status' => self::STATUS_COMPLETED, 'stage' => 'completed', 'fields' => $draft->fields, 'technology_suggestions' => $draft->technologySuggestions, 'evidence' => $draft->evidence]);
    }

    public function fail(string $key, string $message): void
    {
        $this->put($key, [...($this->get($key) ?? []), 'status' => self::STATUS_FAILED, 'stage' => 'failed', 'error' => $message]);
    }

    /** @param array<string, mixed> $context */
    public function advance(string $key, string $stage, array $context = []): void
    {
        $this->put($key, [...($this->get($key) ?? []), 'stage' => $stage, ...$context]);
    }

    /** @return array<string, mixed>|null */
    public function get(string $key): ?array
    {
        $value = Cache::get('developer-resource-draft:'.$key);

        return is_array($value) ? $value : null;
    }

    public function forget(string $key): void
    {
        Cache::forget('developer-resource-draft:'.$key);
    }

    /** @param array<string, mixed> $value */
    private function put(string $key, array $value): void
    {
        Cache::put('developer-resource-draft:'.$key, $value, now()->addMinutes(self::TTL_MINUTES));
    }
}
