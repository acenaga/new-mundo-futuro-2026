<?php

namespace App\Rules;

use App\Support\RichContent\YouTubeEmbed;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidYouTubeVideo implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (YouTubeEmbed::extractVideoId(is_string($value) ? $value : null) === null) {
            $fail('Ingresa una URL valida de YouTube.');
        }
    }
}
