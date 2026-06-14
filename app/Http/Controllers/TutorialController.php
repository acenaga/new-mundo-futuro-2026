<?php

namespace App\Http\Controllers;

use App\Enums\PostStatus;
use App\Models\Tag;
use App\Models\Tutorial;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class TutorialController extends Controller
{
    public function index(Request $request): View
    {
        $tagSlug = $request->query('tag');

        $query = Tutorial::where('status', PostStatus::Published)
            ->with(['tags'])
            ->latest('published_at');

        if ($tagSlug) {
            $query->whereHas('tags', fn ($q) => $q->where('slug', $tagSlug));
        }

        $tutorials = $query->paginate(12)->withQueryString();

        $tags = Tag::whereHas(
            'posts',
            fn ($q) => $q
                ->where('status', PostStatus::Published)
                ->where('type', 'tutorial')
        )->orderBy('name')->get();

        return view('tutoriales.index', compact('tutorials', 'tags', 'tagSlug'));
    }

    public function show(Tutorial $tutorial): View
    {
        $tutorial->loadMissing(['author', 'category', 'tags']);

        abort_unless($tutorial->status === PostStatus::Published, 404);

        $related = Tutorial::where('id', '!=', $tutorial->id)
            ->where('status', PostStatus::Published)
            ->with(['tags'])
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('tutoriales.show', [
            'tutorial' => $tutorial,
            'related' => $related,
        ]);
    }
}
