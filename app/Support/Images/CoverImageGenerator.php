<?php

namespace App\Support\Images;

use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Ai\Image;

/**
 * Generates an illustrative cover image for a post with the configured AI image provider
 * and stores it, optimised, on the public disk alongside uploaded covers.
 */
class CoverImageGenerator
{
    public const string DISK = 'public';

    public const string DIRECTORY = 'covers';

    public function __construct(
        private readonly CoverImageOptimizer $optimizer,
        private readonly CoverBranding $branding,
    ) {}

    /**
     * @return string Path of the stored cover relative to the public disk root.
     */
    public function generate(string $title, ?string $excerpt = null, ?string $concept = null, ?string $headline = null): string
    {
        $image = Image::of($this->buildPrompt($title, $excerpt, $concept))
            ->landscape()
            ->timeout(120)
            ->generate()
            ->firstImage();

        $sourcePath = tempnam(sys_get_temp_dir(), 'cover-ai-');
        file_put_contents($sourcePath, $image->content());
        $optimizedPath = null;

        try {
            $optimizedPath = $this->optimizer->optimize($sourcePath);

            $this->branding->apply($optimizedPath, $headline ?: $title);

            $storedPath = Storage::disk(self::DISK)->putFileAs(
                self::DIRECTORY,
                new File($optimizedPath),
                Str::ulid()->toBase32().'.'.CoverImageOptimizer::EXTENSION,
                'public',
            );
        } finally {
            @unlink($sourcePath);

            if ($optimizedPath !== null) {
                @unlink($optimizedPath);
            }
        }

        if (! is_string($storedPath)) {
            throw new \RuntimeException('No se pudo guardar la portada generada.');
        }

        return $storedPath;
    }

    /**
     * Compose the image prompt: a concrete scene for this article wrapped in the
     * site's fixed editorial art direction so every cover feels like the same publication.
     */
    public function buildPrompt(string $title, ?string $excerpt = null, ?string $concept = null): string
    {
        $subject = filled($concept)
            ? $concept
            : 'A single, concrete, recognisable object or small scene that represents this article: "'.$title.'".'
                .(filled($excerpt) ? ' Context: '.$excerpt : '');

        return implode("\n", [
            'Editorial cover illustration for a Spanish-language technology magazine.',
            '',
            'Subject: '.$subject,
            '',
            'Art direction:',
            '- Flat vector illustration with clean geometric shapes, crisp edges and subtle paper grain. Think modern editorial magazine spot illustration, not 3D render, not photo, not concept art.',
            '- One clear focal subject placed in the right two thirds of the frame. The left third of the image must stay completely empty: flat, plain background colour with no objects, shapes or details, reserved for a headline that will be added later.',
            '- Uncluttered composition, few elements, strong silhouette.',
            '- Limited palette: deep indigo #110090 and violet #4c2e84 as dominant tones, off-white #f4f4fb for light areas, one small accent in warm yellow #f4bf27. Matte colours, soft flat shading, no gradients glow.',
            '- Even, calm lighting. No lens flares, no neon glow, no light rays, no sparkles, no bokeh, no motion blur.',
            '- Horizontal 16:9 composition.',
            '',
            'Strictly avoid: text, letters, numbers, logos, watermarks, user interface screenshots, rockets, light bulbs, brains, lightning bolts, circuit boards, binary code, glowing padlocks, holograms, robots, humanoid figures, faces, hands.',
        ]);
    }
}
