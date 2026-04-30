<?php

namespace App\Http\Controllers;

use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PostController extends Controller
{
    private const TUTORIAL_CATEGORY_SLUG = 'tutoriales';

    public function index(Request $request): View
    {
        $categorySlug = $request->query('categoria');
        $tagSlug = $request->query('tag');

        $query = Post::whereHas('category', fn($q) => $q->where('slug', '!=', self::TUTORIAL_CATEGORY_SLUG))
            ->where('status', 'published')
            ->with(['author', 'category', 'tags'])
            ->latest('published_at');

        if ($categorySlug) {
            $query->whereHas('category', fn($q) => $q->where('slug', $categorySlug));
        }

        if ($tagSlug) {
            $query->whereHas('tags', fn($q) => $q->where('slug', $tagSlug));
        }

        $posts = $query->paginate(10)->withQueryString();

        $categories = Category::where('slug', '!=', self::TUTORIAL_CATEGORY_SLUG)
            ->orderBy('name')
            ->get();

        $tags = Tag::whereHas(
            'posts',
            fn($q) => $q
                ->where('status', 'published')
                ->whereHas('category', fn($q2) => $q2->where('slug', '!=', self::TUTORIAL_CATEGORY_SLUG))
        )->orderBy('name')->get();

        return view('publicaciones.index', compact('posts', 'categories', 'categorySlug', 'tags', 'tagSlug'));
    }

    public function show(Post $post): View
    {
        abort_unless($post->status === PostStatus::Published, 404);

        $post->load(['author', 'category', 'tags']);

        $related = Post::where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->where('status', PostStatus::Published)
            ->with(['author', 'category', 'tags'])
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('publicaciones.show', compact('post', 'related'));
    }
}
