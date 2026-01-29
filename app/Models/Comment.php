<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['user_id', 'body', 'is_approved'];

    // Relación Inversa Polimórfica
    // Esto devuelve AUTOMÁTICAMENTE una instancia de Course o Lesson,
    // dependiendo de qué haya guardado en la BD.
    public function commentable()
    {
        return $this->morphTo();
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
