<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublicacionesController;
use App\Http\Controllers\TutorialesController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/publicaciones', [PublicacionesController::class, 'index'])->name('publicaciones');
Route::get('/tutoriales', [TutorialesController::class, 'index'])->name('tutoriales');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__.'/settings.php';
