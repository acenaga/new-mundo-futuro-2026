<?php

namespace App\Support\Sources;

final readonly class DeveloperResourceDraft
{
    /**
     * @param  array<string, mixed>  $fields
     * @param  list<string>  $technologySuggestions
     * @param  array<string, string>  $evidence
     */
    public function __construct(
        public array $fields,
        public array $technologySuggestions,
        public array $evidence,
    ) {}
}
