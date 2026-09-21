<?php

namespace App\Support\Sources;

final readonly class DeveloperResourceSource
{
    /**
     * @param  list<array{url: string, title: ?string, text: string}>  $pages
     */
    public function __construct(
        public string $canonicalUrl,
        public string $title,
        public array $pages,
    ) {}

    public function text(): string
    {
        return implode("\n\n", array_map(
            fn (array $page): string => "URL: {$page['url']}\nTítulo: ".($page['title'] ?? 'Sin título')."\n{$page['text']}",
            $this->pages,
        ));
    }
}
