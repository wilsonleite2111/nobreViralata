<?php

namespace App\Models;

use App\ShowStatus;
use Carbon\CarbonImmutable;
use Database\Factories\ShowFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property CarbonImmutable $starts_at
 * @property ShowStatus $status
 */
#[Fillable(['title', 'slug', 'starts_at', 'venue', 'city', 'state', 'address', 'ticket_url', 'status', 'description', 'is_published'])]
class Show extends Model
{
    /** @use HasFactory<ShowFactory> */
    use HasFactory;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => ShowStatus::Scheduled,
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
            'starts_at' => 'immutable_datetime',
            'status' => ShowStatus::class,
            'is_published' => 'boolean',
        ];
    }

    /**
     * @return HasMany<PhotoAlbum, $this>
     */
    public function photoAlbums(): HasMany
    {
        return $this->hasMany(PhotoAlbum::class);
    }

    /**
     * @return HasMany<Video, $this>
     */
    public function videos(): HasMany
    {
        return $this->hasMany(Video::class);
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
     * A show stays upcoming until the end of the day it happens on.
     *
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function upcoming(Builder $query): void
    {
        $query->where('starts_at', '>=', today());
    }

    /**
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function past(Builder $query): void
    {
        $query->where('starts_at', '<', today());
    }

    public function isUpcoming(): bool
    {
        return $this->starts_at->greaterThanOrEqualTo(today());
    }

    public function hasTicketsOnSale(): bool
    {
        return $this->ticket_url !== null
            && $this->status === ShowStatus::Scheduled
            && $this->isUpcoming();
    }

    public function location(): string
    {
        return "{$this->city}/{$this->state}";
    }

    public function mapUrl(): string
    {
        $query = implode(', ', array_filter([$this->venue, $this->address, $this->city, $this->state]));

        return 'https://www.google.com/maps/search/?api=1&query='.urlencode($query);
    }

    /**
     * Schema.org MusicEvent data, so search engines list the show as an event.
     *
     * @return array<string, mixed>
     */
    public function toStructuredData(): array
    {
        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'MusicEvent',
            'name' => $this->title,
            'startDate' => $this->starts_at->toIso8601String(),
            'eventStatus' => $this->status === ShowStatus::Cancelled
                ? 'https://schema.org/EventCancelled'
                : 'https://schema.org/EventScheduled',
            'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
            'url' => route('shows.show', $this),
            'location' => [
                '@type' => 'Place',
                'name' => $this->venue,
                'address' => array_filter([
                    '@type' => 'PostalAddress',
                    'streetAddress' => $this->address,
                    'addressLocality' => $this->city,
                    'addressRegion' => $this->state,
                    'addressCountry' => 'BR',
                ]),
            ],
            'performer' => [
                '@type' => 'MusicGroup',
                'name' => config('app.name'),
            ],
        ];

        if ($this->description !== null) {
            $data['description'] = $this->description;
        }

        if ($this->ticket_url !== null) {
            $data['offers'] = [
                '@type' => 'Offer',
                'url' => $this->ticket_url,
                'availability' => $this->status === ShowStatus::SoldOut
                    ? 'https://schema.org/SoldOut'
                    : 'https://schema.org/InStock',
            ];
        }

        return $data;
    }
}
