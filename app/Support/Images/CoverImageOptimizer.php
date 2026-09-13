<?php

namespace App\Support\Images;

use Spatie\Image\Enums\Fit;
use Spatie\Image\Image;

/**
 * Normalises uploaded cover images so every post ships the same lightweight asset:
 * the image is scaled down to fit within the maximum dimensions (never upscaled)
 * and re-encoded as WebP with a balanced quality setting.
 */
class CoverImageOptimizer
{
    public const int MAX_WIDTH = 1600;

    public const int MAX_HEIGHT = 1600;

    public const int QUALITY = 82;

    public const string EXTENSION = 'webp';

    /**
     * Optimise the image at the given path and write the result to a new temporary file.
     *
     * @return string Absolute path to the optimised WebP file. The caller must delete it.
     */
    public function optimize(string $sourcePath): string
    {
        $targetPath = tempnam(sys_get_temp_dir(), 'cover-').'.'.self::EXTENSION;

        Image::load($sourcePath)
            ->fit(Fit::Max, self::MAX_WIDTH, self::MAX_HEIGHT)
            ->quality(self::QUALITY)
            ->save($targetPath);

        return $targetPath;
    }
}
