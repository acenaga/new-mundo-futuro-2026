<?php

use App\Support\Sources\SourceImageImporter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

function fakePngBinary(int $width = 200, int $height = 150): string
{
    $image = imagecreatetruecolor($width, $height);
    imagefill($image, 0, 0, imagecolorallocate($image, 30, 60, 200));

    ob_start();
    imagepng($image);
    $binary = ob_get_clean();
    imagedestroy($image);

    return $binary;
}

beforeEach(function () {
    Storage::fake('public');
});

it('downloads, optimises and stores an image on the public disk', function () {
    Http::fake([
        'https://example.com/*' => Http::response(fakePngBinary(), 200, ['Content-Type' => 'image/png']),
    ]);

    $url = app(SourceImageImporter::class)->import('https://example.com/photo.png');

    expect($url)->toStartWith('/storage/post-images/')
        ->and($url)->toEndWith('.webp');

    $files = Storage::disk('public')->files('post-images');

    expect($files)->toHaveCount(1)
        ->and(Storage::disk('public')->size($files[0]))->toBeGreaterThan(0);
});

it('returns null when the response is not an image', function () {
    Http::fake([
        'https://example.com/*' => Http::response('<html></html>', 200, ['Content-Type' => 'text/html']),
    ]);

    expect(app(SourceImageImporter::class)->import('https://example.com/photo.png'))->toBeNull()
        ->and(Storage::disk('public')->files('post-images'))->toBeEmpty();
});

it('returns null when the image content is invalid despite its declared type', function () {
    Http::fake([
        'https://example.com/*' => Http::response('not an image', 200, ['Content-Type' => 'image/png']),
    ]);

    expect(app(SourceImageImporter::class)->import('https://example.com/photo.png'))->toBeNull()
        ->and(Storage::disk('public')->files('post-images'))->toBeEmpty();
});

it('returns null for internal hosts without sending a request', function () {
    Http::fake();

    expect(app(SourceImageImporter::class)->import('http://127.0.0.1/secret.png'))->toBeNull();

    Http::assertNothingSent();
});

it('returns null when the download fails', function () {
    Http::fake([
        'https://example.com/*' => Http::response('', 404),
    ]);

    expect(app(SourceImageImporter::class)->import('https://example.com/missing.png'))->toBeNull();
});
