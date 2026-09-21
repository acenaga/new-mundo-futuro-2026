<?php

namespace App\Models;

use App\Enums\DeveloperResourcePricing;
use App\Enums\DeveloperResourceStatus;
use Carbon\CarbonInterface;
use Database\Factories\DeveloperResourceFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class DeveloperResource extends Model
{
    /** @use HasFactory<DeveloperResourceFactory> */
    use HasFactory;

    public const int REVIEW_AFTER_DAYS = 90;

    protected $fillable = [
        'user_id', 'developer_resource_category_id', 'name', 'slug', 'external_url', 'summary',
        'why_use_it', 'when_not_to_use_it', 'pricing', 'free_tier_details', 'no_card_required',
        'logo_path', 'is_featured', 'status', 'last_verified_at', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'pricing' => DeveloperResourcePricing::class,
            'status' => DeveloperResourceStatus::class,
            'no_card_required' => 'boolean',
            'is_featured' => 'boolean',
            'last_verified_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    #[Scope]
    protected function published(Builder $query): Builder
    {
        return $query->where('status', DeveloperResourceStatus::Published)->whereNotNull('published_at');
    }

    #[Scope]
    protected function requiresReview(Builder $query): Builder
    {
        return $query->where(function (Builder $query): void {
            $query->whereNull('last_verified_at')
                ->orWhere('last_verified_at', '<', now()->subDays(self::REVIEW_AFTER_DAYS));
        });
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(DeveloperResourceCategory::class, 'developer_resource_category_id');
    }

    public function technologies(): BelongsToMany
    {
        return $this->belongsToMany(DeveloperResourceTechnology::class);
    }

    public function markAsVerified(?CarbonInterface $verifiedAt = null): void
    {
        $this->forceFill(['last_verified_at' => $verifiedAt ?? now()])->save();
    }

    protected function isStale(): Attribute
    {
        return Attribute::get(fn (): bool => $this->last_verified_at === null
            || $this->last_verified_at->lt(now()->subDays(self::REVIEW_AFTER_DAYS)));
    }

    protected function logoUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => filled($this->logo_path)
            ? url(Storage::disk('public')->url($this->logo_path))
            : null);
    }
}
