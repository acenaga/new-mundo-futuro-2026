<?php

namespace App\Models;

use App\Enums\PostStatus;
use App\Filament\RichEditor\Plugins\YouTubeEmbedRichContentPlugin;
use Database\Factories\PostFactory;
use Filament\Forms\Components\RichEditor\FileAttachmentProviders\SpatieMediaLibraryFileAttachmentProvider;
use Filament\Forms\Components\RichEditor\Models\Concerns\InteractsWithRichContent;
use Filament\Forms\Components\RichEditor\Models\Contracts\HasRichContent;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Post extends Model implements HasMedia, HasRichContent
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    use InteractsWithMedia;
    use InteractsWithRichContent;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'excerpt',
        'body',
        'cover_image_path',
        'video_url',
        'status',
        'published_at',
        'allow_comments',
    ];

    protected function casts(): array
    {
        return [
            'status' => PostStatus::class,
            'published_at' => 'datetime',
            'allow_comments' => 'boolean',
        ];
    }

    public function setUpRichContent(): void
    {
        $this->registerRichContent('body')
            ->fileAttachmentsDisk('public')
            ->fileAttachmentsVisibility('public')
            ->fileAttachmentProvider(
                SpatieMediaLibraryFileAttachmentProvider::make()
                    ->collection('post-body-attachments'),
            )
            ->plugins([
                YouTubeEmbedRichContentPlugin::make(),
            ]);
    }

    protected function coverImageUrl(): Attribute
    {
        return Attribute::get(
            fn (): ?string => filled($this->cover_image_path)
                ? Storage::disk('public')->url($this->cover_image_path)
                : null,
        );
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
