<?php

namespace App\Support\Sources;

use App\Support\Images\CoverImageOptimizer;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

/**
 * Downloads an image from an external article, optimises it and stores it on the public disk.
 */
class SourceImageImporter
{
    public const string DISK = 'public';

    public const string DIRECTORY = 'post-images';

    public const int MAX_BYTES = 8_000_000;

    public function __construct(
        private readonly CoverImageOptimizer $optimizer,
        private readonly SafeExternalHttpClient $http,
    ) {}

    /**
     * @return string|null Root-relative public URL of the stored image, or null when it could not be imported.
     */
    public function import(string $url): ?string
    {
        if (! UrlSafety::isAllowed($url)) {
            return null;
        }

        try {
            $response = $this->http->get($url, 'image/*');
        } catch (SourceUnavailableException) {
            return null;
        }

        if ($response->failed() || ! Str::startsWith(strtolower($response->header('Content-Type')), 'image/')) {
            return null;
        }

        $content = $response->body();

        if ($content === '' || strlen($content) > self::MAX_BYTES) {
            return null;
        }

        if (@getimagesizefromstring($content) === false) {
            return null;
        }

        $sourcePath = tempnam(sys_get_temp_dir(), 'source-img-');
        file_put_contents($sourcePath, $content);
        $optimizedPath = null;

        try {
            $optimizedPath = $this->optimizer->optimize($sourcePath);

            $storedPath = Storage::disk(self::DISK)->putFileAs(
                self::DIRECTORY,
                new File($optimizedPath),
                Str::ulid()->toBase32().'.'.CoverImageOptimizer::EXTENSION,
                'public',
            );
        } catch (Throwable) {
            return null;
        } finally {
            @unlink($sourcePath);

            if ($optimizedPath !== null) {
                @unlink($optimizedPath);
            }
        }

        return $storedPath ? Storage::disk(self::DISK)->url($storedPath) : null;
    }
}
