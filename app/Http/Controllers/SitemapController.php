<?php

namespace App\Http\Controllers;

use App\Enums\PostType;
use App\Models\DeveloperResource;
use App\Models\Post;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $posts = Post::published()
            ->latest('published_at')
            ->get(['id', 'slug', 'type', 'published_at', 'updated_at']);

        $tutorials = $posts->filter(fn (Post $p): bool => $p->type === PostType::Tutorial);
        $publicaciones = $posts->filter(fn (Post $p): bool => $p->type === PostType::Article);

        $resources = DeveloperResource::published()
            ->latest('updated_at')
            ->get(['slug', 'updated_at']);

        return response()
            ->view('sitemap', compact('tutorials', 'publicaciones', 'resources'))
            ->header('Content-Type', 'application/xml');
    }
}
