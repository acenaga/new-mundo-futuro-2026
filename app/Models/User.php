<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'bio',
        'social_links',
        'current_points',
        'current_level',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'social_links' => 'array',
        ];
    }

    // Lógica de acceso al Panel de Filament
    public function canAccessPanel(Panel $panel): bool
    {
        // Si el usuario tiene rol Admin, Tutor o Editor, puede entrar.
        // Los estudiantes (usuarios normales) NO pueden entrar aquí.
        return $this->hasAnyRole(['super_admin', 'admin', 'tutor', 'editor']) && $this->hasVerifiedEmail();
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    // Relación: Un usuario (profesor) crea muchos cursos
    public function createdCourses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    // Relación: Un usuario (estudiante) completa muchas lecciones
    public function completedLessons()
    {
        return $this->belongsToMany(Lesson::class, 'lesson_user')
            ->withPivot('completed_at')
            ->withTimestamps();
    }

    // Relación: Gamificación - Medallas ganadas
    public function achievements()
    {
        return $this->belongsToMany(Achievement::class, 'achievement_user')
            ->withPivot('awarded_at');
    }
}
