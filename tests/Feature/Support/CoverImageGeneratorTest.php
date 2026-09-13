<?php

use App\Support\Images\CoverImageGenerator;
use Illuminate\Support\Facades\Storage;
use Laravel\Ai\Image;

function fakeGeneratedPng(): string
{
    $image = imagecreatetruecolor(320, 180);
    imagefill($image, 0, 0, imagecolorallocate($image, 90, 40, 160));

    ob_start();
    imagepng($image);
    $binary = ob_get_clean();
    imagedestroy($image);

    return base64_encode($binary);
}

it('generates a cover with the ai provider and stores it optimised on the public disk', function () {
    Storage::fake('public');
    Image::fake([fakeGeneratedPng()]);

    $path = app(CoverImageGenerator::class)->generate('Laravel integra Vite+', 'Los kits de inicio ahora usan Vite+.');

    expect($path)->toStartWith('covers/')
        ->and($path)->toEndWith('.webp');

    Storage::disk('public')->assertExists($path);

    Image::assertGenerated(fn ($prompt) => $prompt->contains('Laravel integra Vite+')
        && $prompt->contains('Sin texto'));
});
