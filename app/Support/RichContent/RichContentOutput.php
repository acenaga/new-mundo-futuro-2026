<?php

namespace App\Support\RichContent;

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

        return is_string($withInlineTokens) ? $withInlineTokens : $withParagraphTokens;
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

        $cleaned = preg_replace(
            '/<figure[^>]*>.*?<img[^>]*src=["\']https?:\/\/img\.youtube\.com\/vi\/[a-zA-Z0-9_-]{11}\/hqdefault\.jpg[^"\']*["\'][^>]*>.*?(?:<figcaption[^>]*>.*?solo editor.*?<\/figcaption>)?.*?<\/figure>/si',
            '',
            $cleaned,
        );

        if (! is_string($cleaned)) {
            $cleaned = $html;
        }

        $cleaned = preg_replace(
            '/<p>\s*<img[^>]*src=["\']https?:\/\/img\.youtube\.com\/vi\/[a-zA-Z0-9_-]{11}\/hqdefault\.jpg[^"\']*["\'][^>]*>\s*<\/p>/si',
            '',
            $cleaned,
        );

        if (! is_string($cleaned)) {
            return $html;
        }

        $cleaned = preg_replace(
            '/<img[^>]*src=["\']https?:\/\/img\.youtube\.com\/vi\/[a-zA-Z0-9_-]{11}\/hqdefault\.jpg[^"\']*["\'][^>]*>/si',
            '',
            $cleaned,
        );

        if (! is_string($cleaned)) {
            return $html;
        }

        $cleaned = str_replace('Vista previa del video (solo editor)', '', $cleaned);

        return $cleaned;
    }
}
