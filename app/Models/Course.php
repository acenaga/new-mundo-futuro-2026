<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'image_path',
        'status',
        'is_premium'
    ];

    // Relación: El curso pertenece a un Profesor
    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relación: Un curso tiene muchos módulos
    public function modules()
    {
        return $this->hasMany(CourseModule::class)->orderBy('sort_order');
    }

    // Relación: A través de los módulos, tiene muchas lecciones (HasManyThrough)
    // Útil para calcular el progreso total del curso rápido.
    public function lessons()
    {
        return $this->hasManyThrough(Lesson::class, CourseModule::class);
    }

    // Relación Polimórfica: Un curso tiene comentarios
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
