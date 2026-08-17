<?php

namespace App\Http\Controllers;

use App\Enums\PostStatus;
use App\Enums\PostType;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request): View
    {
        $categorySlug = $request->query('categoria');
        $tagSlug = $request->query('tag');

        $query = Article::where('status', PostStatus::Published)
            ->with(['author', 'category', 'tags'])
            ->latest('published_at');

        if ($categorySlug) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
        }

        if ($tagSlug) {
            $query->whereHas('tags', fn ($q) => $q->where('slug', $tagSlug));
        }

        $posts = $query->paginate(10)->withQueryString();

        $categories = Category::whereHas('posts', fn ($q) => $q->where('type', PostType::Article))
            ->orderBy('name')
            ->get();

        $tags = Tag::whereHas(
            'posts',
            fn ($q) => $q
                ->where('status', PostStatus::Published)
                ->where('type', PostType::Article)
        )->orderBy('name')->get();

        return view('publicaciones.index', compact('posts', 'categories', 'categorySlug', 'tags', 'tagSlug'));
    }

    public function show(Article $article): View
    {
        abort_unless($article->status === PostStatus::Published, 404);

        $article->load(['author', 'category', 'tags']);

        $related = Article::where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->where('status', PostStatus::Published)
            ->with(['author', 'category', 'tags'])
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('publicaciones.show', [
            'post' => $article,
            'related' => $related,
        ]);
    }
}
