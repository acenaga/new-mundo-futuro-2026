<?php

namespace App\Http\Controllers;

use App\Enums\CourseStatus;
use App\Enums\PostType;
use App\Models\Course;
use App\Models\Post;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featuredCourses = Course::where('status', CourseStatus::Published)
            ->with('teacher')
            ->latest()
            ->limit(3)
            ->get();

        $popularTutorials = Post::query()
            ->where('type', PostType::Tutorial)
            ->published()
            ->with(['category', 'tags'])
            ->latest('published_at')
            ->limit(3)
            ->get();

        $recentPosts = Post::query()
            ->where('type', PostType::Article)
            ->published()
            ->with(['author', 'category', 'tags'])
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('welcome', compact('featuredCourses', 'popularTutorials', 'recentPosts'));
    }
}
