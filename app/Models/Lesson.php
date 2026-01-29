<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'sort_order'
    ];

    public function module()
    {
        return $this->belongsTo(CourseModule::class, 'module_id');
    }

    // Relación: Usuarios que han completado esta lección
    public function studentsCompleted()
    {
        return $this->belongsToMany(User::class, 'lesson_user');
    }

    // Relación Polimórfica: Una lección puede tener comentarios (dudas)
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
