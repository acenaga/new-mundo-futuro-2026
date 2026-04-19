<?php

namespace App\Http\Controllers;

use App\Enums\PostStatus;
use App\Models\Post;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $posts = Post::where('status', PostStatus::Published)
            ->with('category')
            ->latest('published_at')
            ->get(['slug', 'published_at', 'updated_at', 'category_id']);

        $tutorials = $posts->filter(fn ($p) => $p->category?->slug === 'tutorials');
        $publicaciones = $posts->filter(fn ($p) => $p->category?->slug !== 'tutorials');

        return response()
            ->view('sitemap', compact('tutorials', 'publicaciones'))
            ->header('Content-Type', 'application/xml');
    }
}
