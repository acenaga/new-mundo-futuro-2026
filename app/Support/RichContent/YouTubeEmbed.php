<?php

namespace App\Support\RichContent;

class YouTubeEmbed
{
    private const ID_REGEX = '/^[a-zA-Z0-9_-]{11}$/';

    private const URL_REGEX = '/(?:youtube\.com\/(?:watch\?(?:.*&)?v=|embed\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/';

    public static function extractVideoId(?string $value): ?string
    {
        if (! filled($value)) {
            return null;
        }

        $trimmed = trim($value);

        if (preg_match(self::ID_REGEX, $trimmed) === 1) {
            return $trimmed;
        }

        preg_match(self::URL_REGEX, $trimmed, $matches);

        return $matches[1] ?? null;
    }

    public static function isAllowedEmbedUrl(string $url): bool
    {
        $host = parse_url($url, PHP_URL_HOST);

        if (! is_string($host)) {
            return false;
        }

        $host = strtolower($host);

        return in_array($host, [
            'youtube.com',
            'www.youtube.com',
            'm.youtube.com',
            'youtube-nocookie.com',
            'www.youtube-nocookie.com',
        ], true);
    }

    public static function iframeHtml(string $videoId): string
    {
        return '<div class="my-6 aspect-video w-full overflow-hidden rounded-lg">'
            .'<iframe src="https://www.youtube.com/embed/'.$videoId.'" class="h-full w-full" frameborder="0" '
            .'allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" '
            .'referrerpolicy="strict-origin-when-cross-origin" loading="lazy" allowfullscreen></iframe>'
            .'</div>';
    }

    public static function token(string $videoId): string
    {
        return '[[youtube:'.$videoId.']]';
    }

    public static function insertMarkup(string $videoId): string
    {
        return self::editorPreviewHtml($videoId).'<p>'.self::token($videoId).'</p>';
    }

    public static function editorPreviewHtml(string $videoId): string
    {
        return '<figure class="nmf-youtube-preview my-4 overflow-hidden rounded-lg border border-gray-300/60">'
            .'<img src="https://img.youtube.com/vi/'.$videoId.'/hqdefault.jpg" '
            .'alt="Vista previa de YouTube" class="h-auto w-full object-cover" loading="lazy">'
            .'<figcaption class="px-3 py-2 text-xs text-gray-600">Vista previa del video (solo editor)</figcaption>'
            .'</figure>';
    }

    public static function replaceInContent(string $content, string $fromVideoId, string $toVideoId): string
    {
        $updated = str_replace(self::token($fromVideoId), self::token($toVideoId), $content);
        $updated = str_replace('/vi/'.$fromVideoId.'/', '/vi/'.$toVideoId.'/', $updated);

        return $updated;
    }

    public static function removeFromContent(string $content, string $videoId): string
    {
        $figurePattern = '/<figure[^>]*class=["\'][^"\']*nmf-youtube-preview[^"\']*["\'][^>]*>.*?\/vi\/'
            .preg_quote($videoId, '/')
            .'.*?<\/figure>/si';

        $withoutPreview = preg_replace($figurePattern, '', $content);

        if (! is_string($withoutPreview)) {
            $withoutPreview = $content;
        }

        $withoutTokenParagraph = preg_replace(
            '/<p>\s*\[\[youtube:'.preg_quote($videoId, '/').'\]\]\s*<\/p>/i',
            '',
            $withoutPreview,
        );

        if (! is_string($withoutTokenParagraph)) {
            $withoutTokenParagraph = $withoutPreview;
        }

        return str_replace(self::token($videoId), '', $withoutTokenParagraph);
    }
}
