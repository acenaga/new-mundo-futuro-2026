<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TutorialController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/publicaciones', [PostController::class, 'index'])->name('publicaciones');
Route::get('/publicaciones/{post:slug}', [PostController::class, 'show'])->name('publicaciones.show');
Route::get('/tutoriales', [TutorialController::class, 'index'])->name('tutoriales');
Route::get('/tutoriales/{post:slug}', [TutorialController::class, 'show'])->name('tutoriales.show');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__.'/settings.php';
