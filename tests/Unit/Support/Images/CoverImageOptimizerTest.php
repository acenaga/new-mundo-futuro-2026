<?php

use App\Support\Images\CoverImageOptimizer;

function makeTestPng(int $width, int $height): string
{
    $path = tempnam(sys_get_temp_dir(), 'test-png-').'.png';
    $image = imagecreatetruecolor($width, $height);
    imagefill($image, 0, 0, imagecolorallocate($image, 30, 120, 200));
    imagepng($image, $path);
    imagedestroy($image);

    return $path;
}

afterEach(function () {
    foreach (glob(sys_get_temp_dir().'/test-png-*') ?: [] as $file) {
        @unlink($file);
    }
});

it('scales oversized images down to the maximum width and encodes them as webp', function () {
    $source = makeTestPng(2400, 1350);

    $optimized = (new CoverImageOptimizer)->optimize($source);

    [$width, $height] = getimagesize($optimized);
    $mime = mime_content_type($optimized);
    @unlink($optimized);

    expect($mime)->toBe('image/webp')
        ->and($width)->toBe(CoverImageOptimizer::MAX_WIDTH)
        ->and($height)->toBe(900);
});

it('does not upscale images smaller than the maximum dimensions', function () {
    $source = makeTestPng(800, 450);

    $optimized = (new CoverImageOptimizer)->optimize($source);

    [$width, $height] = getimagesize($optimized);
    @unlink($optimized);

    expect($width)->toBe(800)
        ->and($height)->toBe(450);
});
