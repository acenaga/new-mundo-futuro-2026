<?php

namespace App\Models;

use App\Filament\RichEditor\Plugins\YouTubeEmbedRichContentPlugin;
use Filament\Forms\Components\RichEditor\FileAttachmentProviders\SpatieMediaLibraryFileAttachmentProvider;
use Filament\Forms\Components\RichEditor\Models\Concerns\InteractsWithRichContent;
use Filament\Forms\Components\RichEditor\Models\Contracts\HasRichContent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Course extends Model implements HasMedia, HasRichContent
{
    use InteractsWithMedia;
    use InteractsWithRichContent;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'image_path',
        'status',
        'is_premium',
    ];

    public function setUpRichContent(): void
    {
        $this->registerRichContent('description')
            ->fileAttachmentsDisk('public')
            ->fileAttachmentsVisibility('public')
            ->fileAttachmentProvider(
                SpatieMediaLibraryFileAttachmentProvider::make()
                    ->collection('course-description-attachments'),
            )
            ->plugins([
                YouTubeEmbedRichContentPlugin::make(),
            ]);
    }

    // Relación: El curso pertenece a un Profesor
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relación: Un curso tiene muchos módulos
    public function modules(): HasMany
    {
        return $this->hasMany(CourseModule::class)->orderBy('sort_order');
    }

    // Relación: A través de los módulos, tiene muchas lecciones (HasManyThrough)
    // Útil para calcular el progreso total del curso rápido.
    public function lessons(): HasManyThrough
    {
        return $this->hasManyThrough(Lesson::class, CourseModule::class);
    }

    // Relación Polimórfica: Un curso tiene comentarios
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
