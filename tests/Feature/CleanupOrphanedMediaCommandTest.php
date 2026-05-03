<?php

use App\Models\Course;
use App\Models\Post;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

beforeEach(function () {
    Storage::fake('public');
});

test('it deletes orphaned post attachments not referenced in body', function () {
    $post = Post::factory()->create(['body' => '<p>Contenido sin imagen.</p>']);

    $orphan = $post->addMedia(UploadedFile::fake()->image('foto.jpg'))
        ->toMediaCollection('post-body-attachments');

    $this->artisan('media:cleanup-orphans')
        ->expectsOutputToContain('1 adjuntos huérfanos eliminados')
        ->assertExitCode(0);

    expect(Media::find($orphan->id))->toBeNull();
});

test('it deletes orphaned course attachments not referenced in description', function () {
    $course = Course::factory()->create(['description' => '<p>Contenido sin imagen.</p>']);

    $orphan = $course->addMedia(UploadedFile::fake()->image('foto.jpg'))
        ->toMediaCollection('course-description-attachments');

    $this->artisan('media:cleanup-orphans')
        ->expectsOutputToContain('1 adjuntos huérfanos eliminados')
        ->assertExitCode(0);

    expect(Media::find($orphan->id))->toBeNull();
});

test('it preserves attachments referenced in rich content', function () {
    $post = Post::factory()->create();

    $media = $post->addMedia(UploadedFile::fake()->image('foto.jpg'))
        ->toMediaCollection('post-body-attachments');

    $post->update(['body' => '<p><img id="'.$media->uuid.'" src="/storage/foto.jpg"></p>']);

    $this->artisan('media:cleanup-orphans')
        ->expectsOutputToContain('0 adjuntos huérfanos eliminados')
        ->assertExitCode(0);

    expect(Media::find($media->id))->not->toBeNull();
});

test('dry run reports orphans without deleting them', function () {
    $post = Post::factory()->create(['body' => '<p>Sin imagen.</p>']);

    $orphan = $post->addMedia(UploadedFile::fake()->image('foto.jpg'))
        ->toMediaCollection('post-body-attachments');

    $this->artisan('media:cleanup-orphans', ['--dry-run' => true])
        ->expectsOutputToContain('1 adjuntos huérfanos encontrados')
        ->assertExitCode(0);

    expect(Media::find($orphan->id))->not->toBeNull();
});

test('it reports zero when no orphans exist', function () {
    $this->artisan('media:cleanup-orphans')
        ->expectsOutput('0 adjuntos huérfanos eliminados.')
        ->assertExitCode(0);
});
