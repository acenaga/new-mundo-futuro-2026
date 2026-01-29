<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon_path',
        'points_reward'
    ];

    // Relación: Qué usuarios han ganado esta medalla
    public function winners()
    {
        return $this->belongsToMany(User::class, 'achievement_user')
            ->withPivot('awarded_at');
    }
}
