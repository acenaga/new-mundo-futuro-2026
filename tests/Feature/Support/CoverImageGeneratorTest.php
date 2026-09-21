<?php

use App\Support\Images\CoverBranding;
use App\Support\Images\CoverImageGenerator;
use Illuminate\Support\Facades\Storage;
use Laravel\Ai\Image;

function fakeGeneratedPng(): string
{
    $image = imagecreatetruecolor(640, 360);
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

    $path = app(CoverImageGenerator::class)->generate('Laravel integra Vite+', 'Los kits de inicio ahora usan Vite+.', 'A single multi-tool resting on a clean workbench.', 'Vite+ llega a los starter kits');

    expect($path)->toStartWith('covers/')
        ->and($path)->toEndWith('.webp');

    Storage::disk('public')->assertExists($path);

    $stored = imagecreatefromwebp(Storage::disk('public')->path($path));
    $plain = imagecreatefromstring(base64_decode(fakeGeneratedPng()));

    expect(imagecolorat($stored, 40, 40))->not->toBe(imagecolorat($plain, 40, 40));

    Image::assertGenerated(fn ($prompt) => $prompt->contains('A single multi-tool resting on a clean workbench.')
        && $prompt->contains('Flat vector illustration')
        && $prompt->contains('left third of the image must stay completely empty')
        && $prompt->contains('Strictly avoid: text')
        && $prompt->model === CoverImageGenerator::MODEL);
});

it('brands the cover with the logo and a wrapped headline in light and dark variants', function () {
    $branding = app(CoverBranding::class);

    foreach ([[244, 244, 251], [18, 18, 29]] as $background) {
        $image = imagecreatetruecolor(1264, 848);
        imagefill($image, 0, 0, imagecolorallocate($image, ...$background));
        $path = tempnam(sys_get_temp_dir(), 'brand-').'.webp';
        imagewebp($image, $path, 90);
        $original = imagecreatefromwebp($path);

        $branding->apply($path, 'Un titular suficientemente largo como para necesitar varias líneas en la portada');

        $branded = imagecreatefromwebp($path);
        $differs = function (int $x, int $y) use ($branded, $original): bool {
            $a = imagecolorat($branded, $x, $y);
            $b = imagecolorat($original, $x, $y);

            return abs((($a >> 16) & 0xFF) - (($b >> 16) & 0xFF)) > 24
                || abs((($a >> 8) & 0xFF) - (($b >> 8) & 0xFF)) > 24
                || abs(($a & 0xFF) - ($b & 0xFF)) > 24;
        };

        $changedLeft = 0;
        $changedRight = 0;

        for ($y = 0; $y < 848; $y += 8) {
            for ($x = 0; $x < 1264; $x += 8) {
                if ($differs($x, $y)) {
                    $x < 600 ? $changedLeft++ : $changedRight++;
                }
            }
        }

        // Branding lands on the left column; the right two thirds belong to the illustration.
        expect($changedLeft)->toBeGreaterThan(200)
            ->and($changedRight)->toBe(0);

        @unlink($path);
    }
});

it('normalises headlines to a single tidy line of text', function () {
    $branding = app(CoverBranding::class);

    expect($branding->normalizeHeadline("  Despliegues   automáticos\ncon Ansible. "))->toBe('Despliegues automáticos con Ansible')
        ->and(mb_strlen($branding->normalizeHeadline(str_repeat('palabra ', 20))))->toBeLessThanOrEqual(CoverBranding::MAX_HEADLINE_LENGTH + 1)
        ->and($branding->normalizeHeadline(str_repeat('palabra ', 20)))->toEndWith('…');
});

it('falls back to the title and excerpt when no concept is provided', function () {
    Storage::fake('public');
    Image::fake([fakeGeneratedPng()]);

    app(CoverImageGenerator::class)->generate('Laravel integra Vite+', 'Los kits de inicio ahora usan Vite+.');

    Image::assertGenerated(fn ($prompt) => $prompt->contains('"Laravel integra Vite+"')
        && $prompt->contains('Los kits de inicio ahora usan Vite+.'));
});
