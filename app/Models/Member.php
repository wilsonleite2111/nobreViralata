<?php

namespace App\Models;

use Database\Factories\MemberFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[Fillable(['name', 'role', 'bio', 'sort_order'])]
class Member extends Model implements HasMedia
{
    /** @use HasFactory<MemberFactory> */
    use HasFactory, InteractsWithMedia;

    public const string PHOTO = 'photo';

    protected static function booted(): void
    {
        // New members go to the end of the line-up until reordered in the admin.
        static::creating(function (Member $member): void {
            $member->sort_order ??= (int) self::query()->max('sort_order') + 1;
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::PHOTO)
            ->useDisk('public')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('portrait')
            ->performOnCollections(self::PHOTO)
            ->fit(Fit::Crop, 640, 800)
            ->format('webp');
    }

    public function photoUrl(): ?string
    {
        return $this->getFirstMedia(self::PHOTO)?->getAvailableUrl(['portrait']);
    }
}
