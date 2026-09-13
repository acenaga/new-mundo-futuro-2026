<?php

namespace App\Filament\Forms\Components;

use App\Support\Images\CoverImageOptimizer;
use Filament\Forms\Components\FileUpload;
use Filament\Support\Icons\Heroicon;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/**
 * Cover image field shared by articles and tutorials: guides the editor on the
 * recommended size, lets them crop freely or to preset ratios, and optimises
 * the file on the server before storing it.
 */
class CoverImageUpload extends FileUpload
{
    public const string RECOMMENDED_SIZE = '1200 × 675 px';

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->image()
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->maxSize(5120)
            ->disk('public')
            ->visibility('public')
            ->imageEditor()
            ->imageEditorAspectRatioOptions([null, '16:9', '4:3', '1:1'])
            ->imageEditorViewportWidth('1200')
            ->imageEditorViewportHeight('675')
            ->hint('Tamaño sugerido: '.self::RECOMMENDED_SIZE)
            ->hintIcon(
                Heroicon::OutlinedInformationCircle,
                tooltip: 'Usa una imagen de '.self::RECOMMENDED_SIZE.' (proporción 16:9). '
                    .'Si subes otra medida puedes recortarla con el editor. '
                    .'Al guardar se redimensiona y convierte a WebP automáticamente.',
            )
            ->helperText('JPG, PNG o WebP de hasta 5 MB. La imagen se optimiza al subirla.')
            ->saveUploadedFileUsing(function (TemporaryUploadedFile $file, CoverImageUpload $component): string {
                return $component->storeOptimized($file);
            });
    }

    /**
     * Optimise the temporary upload and persist it on the configured disk.
     *
     * @return string Path of the stored file relative to the disk root.
     */
    public function storeOptimized(TemporaryUploadedFile $file): string
    {
        $optimizer = app(CoverImageOptimizer::class);

        $sourcePath = tempnam(sys_get_temp_dir(), 'cover-src-');
        file_put_contents($sourcePath, $file->get());

        $optimizedPath = $optimizer->optimize($sourcePath);

        try {
            $fileName = $this->resolveFileName($file);

            $storedPath = Storage::disk($this->getDiskName())->putFileAs(
                $this->getDirectory() ?? '',
                new File($optimizedPath),
                $fileName,
                $this->getVisibility(),
            );
        } finally {
            @unlink($sourcePath);
            @unlink($optimizedPath);
        }

        return $storedPath;
    }

    protected function resolveFileName(TemporaryUploadedFile $file): string
    {
        $baseName = $this->shouldPreserveFilenames()
            ? Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
            : Str::ulid()->toBase32();

        return $baseName.'.'.CoverImageOptimizer::EXTENSION;
    }
}
