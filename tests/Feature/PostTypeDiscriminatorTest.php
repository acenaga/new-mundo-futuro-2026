<?php

use App\Enums\PostType;
use App\Models\Article;
use App\Models\Post;
use App\Models\Tutorial;
use App\Models\User;

it('creates articles with type article by default', function () {
    $post = Post::factory()->published()->create();

    expect($post->type)->toBe(PostType::Article);
});

it('creates tutorials with type tutorial via factory state', function () {
    $post = Post::factory()->tutorial()->published()->create();

    expect($post->type)->toBe(PostType::Tutorial);
});

it('Article global scope only returns articles', function () {
    Post::factory()->published()->create();
    Post::factory()->tutorial()->published()->create();

    $articles = Article::all();

    expect($articles)->toHaveCount(1);
    expect($articles->first()->type)->toBe(PostType::Article);
});

it('Tutorial global scope only returns tutorials', function () {
    Post::factory()->published()->create();
    Post::factory()->tutorial()->published()->create();

    $tutorials = Tutorial::all();

    expect($tutorials)->toHaveCount(1);
    expect($tutorials->first()->type)->toBe(PostType::Tutorial);
});

it('Post::all returns all types', function () {
    Post::factory()->published()->create();
    Post::factory()->tutorial()->published()->create();

    $all = Post::all();

    expect($all)->toHaveCount(2);
});

it('hydrates correct subclass instances via newFromBuilder', function () {
    $article = Post::factory()->published()->create();
    $tutorial = Post::factory()->tutorial()->published()->create();

    $all = Post::orderBy('id')->get();

    expect($all[0])->toBeInstanceOf(Article::class);
    expect($all[1])->toBeInstanceOf(Tutorial::class);
});

it('auto-sets type when creating via Article model', function () {
    $article = Article::create([
        'user_id' => User::factory()->create()->id,
        'title' => 'Article via model',
        'slug' => 'article-via-model',
        'body' => 'Test body content',
    ]);

    expect($article->type)->toBe(PostType::Article);
});

it('auto-sets type when creating via Tutorial model', function () {
    $tutorial = Tutorial::create([
        'user_id' => User::factory()->create()->id,
        'title' => 'Tutorial via model',
        'slug' => 'tutorial-via-model',
        'body' => 'Test body content',
    ]);

    expect($tutorial->type)->toBe(PostType::Tutorial);
});

it('resolves route model binding correctly for Article', function () {
    $article = Post::factory()->published()->create();
    $tutorial = Post::factory()->tutorial()->published()->create();

    $this->get(route('publicaciones.show', $article))
        ->assertSuccessful();

    $this->get(route('publicaciones.show', $tutorial))
        ->assertNotFound();
});

it('resolves route model binding correctly for Tutorial', function () {
    $article = Post::factory()->published()->create();
    $tutorial = Post::factory()->tutorial()->published()->create();

    $this->get(route('tutoriales.show', $tutorial))
        ->assertSuccessful();

    $this->get(route('tutoriales.show', $article))
        ->assertNotFound();
});
