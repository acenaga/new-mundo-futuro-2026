<?php

namespace App\Http\Controllers;

use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class TutorialController extends Controller
{
    public function index(Request $request): View
    {
        $tagSlug = $request->query('tag');

        $query = Post::whereHas('category', fn ($q) => $q->where('slug', 'tutorials'))
            ->where('status', 'published')
            ->with(['tags'])
            ->latest('published_at');

        if ($tagSlug) {
            $query->whereHas('tags', fn ($q) => $q->where('slug', $tagSlug));
        }

        $tutorials = $query->paginate(12)->withQueryString();

        $tags = Tag::whereHas('posts', fn ($q) => $q
            ->where('status', 'published')
            ->whereHas('category', fn ($q2) => $q2->where('slug', 'tutorials'))
        )->orderBy('name')->get();

        return view('tutoriales.index', compact('tutorials', 'tags', 'tagSlug'));
    }

    public function show(Post $post): View
    {
        abort_unless(
            $post->status === PostStatus::Published &&
            $post->category?->slug === 'tutorials',
            404
        );

        $post->load(['author', 'category', 'tags']);

        $related = Post::whereHas('category', fn ($q) => $q->where('slug', 'tutorials'))
            ->where('id', '!=', $post->id)
            ->where('status', PostStatus::Published)
            ->with(['tags'])
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('tutoriales.show', compact('post', 'related'));
    }
}
