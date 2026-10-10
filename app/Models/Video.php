<?php

namespace App\Models;

use App\VideoCategory;
use Carbon\CarbonImmutable;
use Database\Factories\VideoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property CarbonImmutable $published_at
 * @property VideoCategory $category
 */
#[Fillable(['title', 'youtube_id', 'category', 'published_at', 'show_id', 'is_featured', 'is_published'])]
class Video extends Model
{
    /** @use HasFactory<VideoFactory> */
    use HasFactory;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'is_featured' => false,
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
            'category' => VideoCategory::class,
            'published_at' => 'immutable_date',
            'is_featured' => 'boolean',
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

    /**
     * Accepts a bare video ID or any common YouTube link (watch, youtu.be, shorts, embed, live).
     */
    public static function extractYoutubeId(string $value): ?string
    {
        $value = trim($value);

        if (preg_match('/^[\w-]{11}$/', $value) === 1) {
            return $value;
        }

        $pattern = '~^(?:https?://)?(?:www\.|m\.)?(?:youtube\.com/(?:watch\?(?:.*&)?v=|shorts/|embed/|live/)|youtu\.be/)([\w-]{11})(?:[?&#/].*)?$~';

        return preg_match($pattern, $value, $matches) === 1 ? $matches[1] : null;
    }

    public function thumbnailUrl(): string
    {
        return "https://i.ytimg.com/vi/{$this->youtube_id}/hqdefault.jpg";
    }

    public function embedUrl(): string
    {
        return "https://www.youtube-nocookie.com/embed/{$this->youtube_id}?autoplay=1";
    }
}
