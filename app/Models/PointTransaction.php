<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'description',
        'reference_type', // Polimórfico
        'reference_id'    // Polimórfico
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación Polimórfica: ¿Qué generó estos puntos?
    // Puede ser el completion de una Lesson, un Comment aprobado, etc.
    public function reference()
    {
        return $this->morphTo();
    }
}
