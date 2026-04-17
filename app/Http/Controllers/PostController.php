<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request): View
    {
        $categorySlug = $request->query('categoria');
        $tagSlug = $request->query('tag');

        $query = Post::whereHas('category', fn ($q) => $q->where('slug', '!=', 'tutorials'))
            ->where('status', 'published')
            ->with(['author', 'category', 'tags'])
            ->latest('published_at');

        if ($categorySlug) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
        }

        if ($tagSlug) {
            $query->whereHas('tags', fn ($q) => $q->where('slug', $tagSlug));
        }

        $posts = $query->paginate(10)->withQueryString();

        $categories = Category::where('slug', '!=', 'tutorials')
            ->orderBy('name')
            ->get();

        $tags = Tag::whereHas('posts', fn ($q) => $q
            ->where('status', 'published')
            ->whereHas('category', fn ($q2) => $q2->where('slug', '!=', 'tutorials'))
        )->orderBy('name')->get();

        return view('publicaciones.index', compact('posts', 'categories', 'categorySlug', 'tags', 'tagSlug'));
    }
}
