<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Lesson extends Model
{
    protected $fillable = [
        'module_id',
        'title',
        'slug',
        'video_url',
        'description',
        'duration_minutes',
        'is_free_preview',
        'sort_order',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(CourseModule::class, 'module_id');
    }

    // Relación: Usuarios que han completado esta lección
    public function studentsCompleted(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'lesson_user');
    }

    // Relación Polimórfica: Una lección puede tener comentarios (dudas)
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
