<?php

namespace App\Support\Sources;

use Carbon\CarbonImmutable;

final readonly class ExtractedSource
{
    /**
     * @param  list<SourceImage>  $images
     */
    public function __construct(
        public string $url,
        public ?string $title,
        public ?string $author,
        public ?string $site,
        public ?CarbonImmutable $publishedAt,
        public string $text,
        public array $images = [],
    ) {}
}
