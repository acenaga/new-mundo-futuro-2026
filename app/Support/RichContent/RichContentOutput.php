<?php

namespace App\Support\RichContent;

class RichContentOutput
{
    public static function render(string $html): string
    {
        $withoutEditorPreviewBlocks = preg_replace(
            '/<figure[^>]*class=["\'][^"\']*nmf-youtube-preview[^"\']*["\'][^>]*>.*?<\/figure>/si',
            '',
            $html,
        );

        if (! is_string($withoutEditorPreviewBlocks)) {
            $withoutEditorPreviewBlocks = $html;
        }

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
}
