<?php

namespace App\Support\RichContent;

use Illuminate\Support\Str;

class RichContentOutput
{
    public static function render(string $html): string
    {
        $withoutEditorPreviewBlocks = self::stripEditorPreviewBlocks($html);

        $withParagraphTokens = preg_replace_callback(
            '/<p>\s*\[\[youtube:([a-zA-Z0-9_-]{11})\]\]\s*<\/p>/',
            fn (array $matches): string => YouTubeEmbed::iframeHtml($matches[1]),
            $withoutEditorPreviewBlocks,
        );

        if (! is_string($withParagraphTokens)) {
            $withParagraphTokens = $html;
        }

        $withInlineTokens = preg_replace_callback(
            '/\[\[youtube:([a-zA-Z0-9_-]{11})\]\]/',
            fn (array $matches): string => YouTubeEmbed::iframeHtml($matches[1]),
            $withParagraphTokens,
        );

        $renderedHtml = is_string($withInlineTokens) ? $withInlineTokens : $withParagraphTokens;

        return self::normalizePublicStorageUrls($renderedHtml);
    }

    private static function stripEditorPreviewBlocks(string $html): string
    {
        $cleaned = preg_replace(
            '/<figure[^>]*class=["\'][^"\']*nmf-youtube-preview[^"\']*["\'][^>]*>.*?<\/figure>/si',
            '',
            $html,
        );

        if (! is_string($cleaned)) {
            $cleaned = $html;
        }

        return $cleaned;
    }

    private static function normalizePublicStorageUrls(string $html): string
    {
        if (! app()->bound('request')) {
            return $html;
        }

        $rootUrl = rtrim(url('/'), '/');

        return preg_replace_callback(
            '/https?:\/\/[^"\']+\/storage\/[^"\']+/i',
            function (array $matches) use ($rootUrl): string {
                $path = parse_url($matches[0], PHP_URL_PATH);

                if (! is_string($path) || ! Str::startsWith($path, '/storage/')) {
                    return $matches[0];
                }

                return $rootUrl.$path;
            },
            $html,
        ) ?? $html;
    }
}
