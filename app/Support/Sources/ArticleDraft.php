<?php

namespace App\Support\Sources;

final readonly class ArticleDraft
{
    /**
     * @param  array<string, mixed>  $fields  Form state ready to be filled into the article form.
     * @param  list<string>  $warnings  Non-fatal problems worth showing to the editor.
     */
    public function __construct(
        public array $fields,
        public array $warnings = [],
    ) {}
}
