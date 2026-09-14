<?php

namespace App\Support\Images;

use GdImage;
use RuntimeException;

/**
 * Stamps the Mundo Futuro logo and a short headline on a generated cover.
 *
 * The illustration prompt keeps the left third of the image empty, so the branding
 * is laid out there: logo lock-up at the top, headline anchored to the bottom.
 * Colours adapt to the luminance of that area so the text stays legible on light
 * or dark backgrounds.
 */
class CoverBranding
{
    public const string FONT_BOLD = 'fonts/SpaceGrotesk-Bold.ttf';

    public const string FONT_MEDIUM = 'fonts/SpaceGrotesk-Medium.ttf';

    public const string LOGO_INDIGO = 'images/cover/logo-icon-indigo.png';

    public const string LOGO_LIGHT = 'images/cover/logo-icon-light.png';

    public const int MAX_HEADLINE_LENGTH = 70;

    /**
     * Draw the branding onto the WebP file at the given path, replacing it in place.
     */
    public function apply(string $imagePath, string $headline): void
    {
        $headline = $this->normalizeHeadline($headline);

        if ($headline === '') {
            return;
        }

        $image = @imagecreatefromwebp($imagePath);

        if (! $image instanceof GdImage) {
            throw new RuntimeException('No se pudo abrir la portada para rotularla.');
        }

        imagealphablending($image, true);

        $width = imagesx($image);
        $height = imagesy($image);
        $padding = (int) round($width * 0.055);
        $columnWidth = (int) round($width * 0.34);
        $isDark = $this->isDarkRegion($image, 0, 0, $columnWidth + $padding, $height);

        [$textRgb, $mutedRgb] = $isDark
            ? [[244, 244, 251], [153, 153, 179]]
            : [[17, 0, 144], [74, 74, 106]];

        $textColor = imagecolorallocate($image, ...$textRgb);
        $mutedColor = imagecolorallocate($image, ...$mutedRgb);
        $accentColor = imagecolorallocate($image, 244, 191, 39);

        $this->drawLogo($image, $isDark ? self::LOGO_LIGHT : self::LOGO_INDIGO, $padding, $padding, (int) round($height * 0.11), $mutedColor);
        $this->drawHeadline($image, $headline, $padding, $columnWidth, $height - $padding, (int) round($height * 0.058), $textColor, $accentColor);

        if (! imagewebp($image, $imagePath, CoverImageOptimizer::QUALITY)) {
            throw new RuntimeException('No se pudo guardar la portada rotulada.');
        }

        imagedestroy($image);
    }

    public function normalizeHeadline(string $headline): string
    {
        $headline = trim(preg_replace('/\s+/u', ' ', strip_tags($headline)) ?? '');
        $headline = rtrim($headline, ' .');

        if (mb_strlen($headline) <= self::MAX_HEADLINE_LENGTH) {
            return $headline;
        }

        $cut = mb_substr($headline, 0, self::MAX_HEADLINE_LENGTH);
        $lastSpace = mb_strrpos($cut, ' ');

        return rtrim($lastSpace !== false ? mb_substr($cut, 0, $lastSpace) : $cut, ' ,;:').'…';
    }

    private function drawLogo(GdImage $image, string $logoResource, int $x, int $y, int $iconHeight, int $wordmarkColor): void
    {
        $logo = @imagecreatefrompng(resource_path($logoResource));

        if (! $logo instanceof GdImage) {
            return;
        }

        $iconWidth = (int) round(imagesx($logo) * $iconHeight / imagesy($logo));

        imagecopyresampled($image, $logo, $x, $y, 0, 0, $iconWidth, $iconHeight, imagesx($logo), imagesy($logo));
        imagedestroy($logo);

        $fontSize = max(10, (int) round($iconHeight * 0.26));
        $font = resource_path(self::FONT_MEDIUM);
        $box = imagettfbbox($fontSize, 0, $font, 'MUNDO FUTURO');
        $textHeight = abs($box[7] - $box[1]);

        imagettftext(
            $image,
            $fontSize,
            0,
            $x + $iconWidth + (int) round($iconHeight * 0.22),
            $y + (int) round(($iconHeight + $textHeight) / 2),
            $wordmarkColor,
            $font,
            'MUNDO FUTURO',
        );
    }

    private function drawHeadline(GdImage $image, string $headline, int $x, int $maxWidth, int $bottom, int $fontSize, int $color, int $accentColor): void
    {
        $font = resource_path(self::FONT_BOLD);

        do {
            $lines = $this->wrap($headline, $font, $fontSize, $maxWidth);
            $fontSize -= 2;
        } while (count($lines) > 4 && $fontSize > 12);

        $fontSize += 2;
        $lineHeight = (int) round($fontSize * 1.65);
        $y = $bottom;

        foreach (array_reverse($lines) as $line) {
            imagettftext($image, $fontSize, 0, $x, $y, $color, $font, $line);
            $y -= $lineHeight;
        }

        $barY = $y - (int) round($lineHeight * 0.15);
        imagefilledrectangle($image, $x, $barY, $x + (int) round($fontSize * 1.6), $barY + max(3, (int) round($fontSize * 0.14)), $accentColor);
    }

    /**
     * @return list<string>
     */
    private function wrap(string $text, string $font, int $fontSize, int $maxWidth): array
    {
        $lines = [];
        $current = '';

        foreach (preg_split('/\s+/u', $text) ?: [] as $word) {
            $candidate = $current === '' ? $word : $current.' '.$word;
            $box = imagettfbbox($fontSize, 0, $font, $candidate);

            if ($current !== '' && ($box[2] - $box[0]) > $maxWidth) {
                $lines[] = $current;
                $current = $word;
            } else {
                $current = $candidate;
            }
        }

        if ($current !== '') {
            $lines[] = $current;
        }

        return $lines;
    }

    private function isDarkRegion(GdImage $image, int $x0, int $y0, int $x1, int $y1): bool
    {
        $total = 0;
        $samples = 0;
        $step = max(4, (int) (($x1 - $x0) / 40));

        for ($y = $y0; $y < $y1; $y += $step) {
            for ($x = $x0; $x < $x1; $x += $step) {
                $rgb = imagecolorat($image, $x, $y);
                $total += 0.2126 * (($rgb >> 16) & 0xFF) + 0.7152 * (($rgb >> 8) & 0xFF) + 0.0722 * ($rgb & 0xFF);
                $samples++;
            }
        }

        return $samples > 0 && ($total / $samples) < 128;
    }
}
