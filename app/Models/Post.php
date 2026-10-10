<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @property CarbonImmutable $published_at
 */
#[Fillable(['title', 'slug', 'excerpt', 'body', 'published_at', 'is_published'])]
class Post extends Model implements HasMedia
{
    /** @use HasFactory<PostFactory> */
    use HasFactory, InteractsWithMedia;

    public const string COVER = 'cover';

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'is_published' => false,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'immutable_datetime',
            'is_published' => 'boolean',
        ];
    }

    /**
     * A post dated in the future stays hidden until that moment, which lets the band schedule news.
     *
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where('is_published', true)->where('published_at', '<=', now());
    }

    public function isVisible(): bool
    {
        return $this->is_published && $this->published_at->lessThanOrEqualTo(now());
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::COVER)
            ->useDisk('public')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('card')
            ->performOnCollections(self::COVER)
            ->fit(Fit::Crop, 800, 450)
            ->format('webp');

        $this->addMediaConversion('large')
            ->performOnCollections(self::COVER)
            ->fit(Fit::Max, 1600, 1600)
            ->format('webp');
    }

    public function coverUrl(string $conversion): ?string
    {
        return $this->getFirstMedia(self::COVER)?->getAvailableUrl([$conversion]);
    }

    public function summary(): string
    {
        return $this->excerpt ?? Str::limit(trim(html_entity_decode(strip_tags($this->body))), 160);
    }
}
