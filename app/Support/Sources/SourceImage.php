<?php

namespace App\Support\Sources;

final readonly class SourceImage
{
    public function __construct(
        public string $url,
        public ?string $alt,
    ) {}
}
