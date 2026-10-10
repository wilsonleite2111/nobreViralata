<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\PhotoAlbumFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @property CarbonImmutable $taken_at
 */
#[Fillable(['title', 'slug', 'taken_at', 'show_id', 'is_published'])]
class PhotoAlbum extends Model implements HasMedia
{
    /** @use HasFactory<PhotoAlbumFactory> */
    use HasFactory, InteractsWithMedia;

    public const string PHOTOS = 'photos';

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
            'taken_at' => 'immutable_date',
            'is_published' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Show, $this>
     */
    public function show(): BelongsTo
    {
        return $this->belongsTo(Show::class);
    }

    /**
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where('is_published', true);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::PHOTOS)
            ->useDisk('public')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->performOnCollections(self::PHOTOS)
            ->fit(Fit::Crop, 600, 600)
            ->format('webp');

        $this->addMediaConversion('large')
            ->performOnCollections(self::PHOTOS)
            ->fit(Fit::Max, 1920, 1920)
            ->format('webp');
    }

    public function cover(): ?Media
    {
        return $this->getFirstMedia(self::PHOTOS);
    }
}
