<?php

namespace App\Rules;

use App\Support\RichContent\YouTubeEmbed;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class OnlyYouTubeEmbeds implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || blank($value)) {
            return;
        }

        preg_match_all('/<iframe[^>]*src=["\']([^"\']+)["\'][^>]*>/i', $value, $iframeMatches);

        foreach ($iframeMatches[1] ?? [] as $src) {
            if (! YouTubeEmbed::isAllowedEmbedUrl($src)) {
                $fail(__('validation.only_youtube_embeds'));

                return;
            }
        }

        preg_match_all('/\[\[youtube:([^\]]+)\]\]/', $value, $tokenMatches);

        foreach ($tokenMatches[1] ?? [] as $tokenValue) {
            if (YouTubeEmbed::extractVideoId($tokenValue) === null) {
                $fail(__('validation.youtube_embed_invalid'));

                return;
            }
        }
    }
}
