<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Post;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    private const TUTORIAL_CATEGORY_SLUG = 'tutoriales';

    public function index(): View
    {
        $featuredCourses = Course::where('status', 'published')
            ->with('teacher')
            ->latest()
            ->limit(3)
            ->get();

        $popularTutorials = Post::whereHas('category', fn($q) => $q->where('slug', self::TUTORIAL_CATEGORY_SLUG))
            ->where('status', 'published')
            ->with(['category', 'tags'])
            ->latest('published_at')
            ->limit(3)
            ->get();

        $recentPosts = Post::whereHas('category', fn($q) => $q->where('slug', '!=', self::TUTORIAL_CATEGORY_SLUG))
            ->where('status', 'published')
            ->with(['author', 'category', 'tags'])
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('welcome', compact('featuredCourses', 'popularTutorials', 'recentPosts'));
    }
}
