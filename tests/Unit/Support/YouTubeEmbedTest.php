<?php

use App\Support\RichContent\YouTubeEmbed;

it('builds insert markup with preview and token', function () {
    $html = YouTubeEmbed::insertMarkup('Cn8HBj8QAbk');

    expect($html)->toContain('nmf-youtube-preview');
    expect($html)->toContain('[[youtube:Cn8HBj8QAbk]]');
});

it('replaces youtube token and preview id in content', function () {
    $content = '<figure class="nmf-youtube-preview"><img src="https://img.youtube.com/vi/Cn8HBj8QAbk/hqdefault.jpg"></figure><p>[[youtube:Cn8HBj8QAbk]]</p>';

    $updated = YouTubeEmbed::replaceInContent($content, 'Cn8HBj8QAbk', 'dQw4w9WgXcQ');

    expect($updated)->toContain('[[youtube:dQw4w9WgXcQ]]');
    expect($updated)->toContain('/vi/dQw4w9WgXcQ/');
    expect($updated)->not->toContain('Cn8HBj8QAbk');
});

it('removes youtube token and preview block from content', function () {
    $content = '<p>Antes</p><figure class="nmf-youtube-preview"><img src="https://img.youtube.com/vi/Cn8HBj8QAbk/hqdefault.jpg"></figure><p>[[youtube:Cn8HBj8QAbk]]</p><p>Despues</p>';

    $updated = YouTubeEmbed::removeFromContent($content, 'Cn8HBj8QAbk');

    expect($updated)->toContain('<p>Antes</p>');
    expect($updated)->toContain('<p>Despues</p>');
    expect($updated)->not->toContain('nmf-youtube-preview');
    expect($updated)->not->toContain('[[youtube:Cn8HBj8QAbk]]');
});
