<?php

namespace App\Models;

use App\Enums\PostStatus;
use App\Enums\PostType;
use App\Filament\RichEditor\Plugins\YouTubeEmbedRichContentPlugin;
use App\Support\RichContent\YouTubeEmbed;
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

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::saving(function (Post $post) {
            $isTutorial = $post instanceof Tutorial || $post->type === PostType::Tutorial;
            $isArticle = $post instanceof Article || $post->type === PostType::Article;

            // Enforce category invariance for Tutorial type
            if ($isTutorial) {
                $post->type = PostType::Tutorial;

                $tutorialCategory = Category::where('slug', 'tutoriales')->first()
                    ?? Category::create(['slug' => 'tutoriales', 'name' => 'Tutoriales']);

                if ($post->category_id !== null && (int) $post->category_id !== (int) $tutorialCategory->id) {
                    throw new \InvalidArgumentException('Un tutorial solo puede tener la categoría "Tutoriales".');
                }

                $post->category_id = $tutorialCategory->id;
            }

            // Enforce category invariance for Article type
            if ($isArticle) {
                $post->type = PostType::Article;

                if ($post->category_id !== null) {
                    $tutorialCategory = Category::where('slug', 'tutoriales')->first();
                    if ($tutorialCategory && (int) $post->category_id === (int) $tutorialCategory->id) {
                        throw new \InvalidArgumentException('Un artículo no puede tener la categoría "Tutoriales".');
                    }
                }
            }
        });
    }

    protected $table = 'posts';

    protected $fillable = [
        'user_id',
        'category_id',
        'type',
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
            'type' => PostType::class,
            'status' => PostStatus::class,
            'published_at' => 'datetime',
            'allow_comments' => 'boolean',
        ];
    }

    /**
     * Resolve the correct subclass when hydrating from the database (STI).
     *
     * @param  array<string, mixed>  $attributes
     */
    public function newFromBuilder($attributes = [], $connection = null): static
    {
        $attributes = (array) $attributes;

        $type = $attributes['type'] ?? null;

        $class = match ($type) {
            PostType::Article->value, PostType::Article => Article::class,
            PostType::Tutorial->value, PostType::Tutorial => Tutorial::class,
            default => static::class,
        };

        if ($class !== static::class && is_a($class, static::class, true)) {
            $model = (new $class)->newInstance([], true);
            $model->setRawAttributes($attributes, true);
            $model->setConnection($connection ?: $this->getConnectionName());
            $model->fireModelEvent('retrieved', false);

            return $model;
        }

        return parent::newFromBuilder($attributes, $connection);
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
                ? url(Storage::disk('public')->url($this->cover_image_path))
                : null,
        );
    }

    protected function readingTime(): Attribute
    {
        return Attribute::get(function (): int {
            $words = str_word_count(strip_tags($this->body ?? ''));

            return (int) max(1, ceil($words / 200));
        });
    }

    protected function youtubeThumbnailUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            $videoId = YouTubeEmbed::extractVideoId($this->video_url);

            return $videoId ? "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg" : null;
        });
    }

    protected function youtubeEmbedUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            $videoId = YouTubeEmbed::extractVideoId($this->video_url);

            return $videoId ? "https://www.youtube.com/embed/{$videoId}" : null;
        });
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
        return $this->belongsToMany(Tag::class, 'post_tag', 'post_id', 'tag_id');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function getMorphClass(): string
    {
        return self::class;
    }
}
