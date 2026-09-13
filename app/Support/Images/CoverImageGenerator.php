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

    public function __construct(private readonly CoverImageOptimizer $optimizer) {}

    /**
     * @return string Path of the stored cover relative to the public disk root.
     */
    public function generate(string $title, ?string $excerpt = null): string
    {
        $image = Image::of($this->buildPrompt($title, $excerpt))
            ->landscape()
            ->timeout(120)
            ->generate()
            ->firstImage();

        $sourcePath = tempnam(sys_get_temp_dir(), 'cover-ai-');
        file_put_contents($sourcePath, $image->content());
        $optimizedPath = null;

        try {
            $optimizedPath = $this->optimizer->optimize($sourcePath);

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

    public function buildPrompt(string $title, ?string $excerpt = null): string
    {
        return implode(' ', array_filter([
            'Ilustración editorial moderna y limpia para un artículo de tecnología titulado:',
            '"'.$title.'".',
            filled($excerpt) ? 'Resumen del artículo: '.$excerpt : null,
            'Estilo: digital, colores vivos con predominio de azules y violetas, composición horizontal 16:9,',
            'formas abstractas o metáforas visuales relacionadas con el tema.',
            'Sin texto, sin letras, sin logotipos, sin marcas de agua ni personas reales.',
        ]));
    }
}
