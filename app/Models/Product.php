<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @property-read Collection<int, ProductVariant> $variants
 */
#[Fillable(['product_category_id', 'name', 'slug', 'description', 'price_cents', 'stock', 'external_url', 'is_active', 'is_featured'])]
class Product extends Model implements HasMedia
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory, InteractsWithMedia;

    public const string PHOTOS = 'photos';

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'stock' => 0,
        'is_active' => false,
        'is_featured' => false,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price_cents' => 'integer',
            'stock' => 'integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<ProductCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    /**
     * @return HasMany<ProductVariant, $this>
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort_order')->orderBy('id');
    }

    /**
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
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
            ->fit(Fit::Crop, 800, 800)
            ->format('webp');

        $this->addMediaConversion('large')
            ->performOnCollections(self::PHOTOS)
            ->fit(Fit::Max, 1600, 1600)
            ->format('webp');
    }

    public function cover(): ?Media
    {
        return $this->getFirstMedia(self::PHOTOS);
    }

    public function formattedPrice(): string
    {
        return 'R$ '.number_format($this->price_cents / 100, 2, ',', '.');
    }

    /**
     * A product with variants is in stock while any variant is; its own stock only counts when it has none.
     */
    public function isInStock(): bool
    {
        if ($this->variants->isNotEmpty()) {
            return $this->variants->contains(fn (ProductVariant $variant): bool => $variant->isInStock());
        }

        return $this->stock > 0;
    }

    /**
     * Where the buy button leads: the external store when set, otherwise a WhatsApp chat with the order filled in.
     */
    public function purchaseUrl(?string $whatsappNumber, ?ProductVariant $variant = null): ?string
    {
        if ($this->external_url !== null) {
            return $this->external_url;
        }

        if (blank($whatsappNumber)) {
            return null;
        }

        $item = $variant === null ? $this->name : "{$this->name} ({$variant->name})";
        $message = "Olá! Quero comprar: {$item} — {$this->formattedPrice()}\n".route('store.show', $this);

        return "https://wa.me/{$whatsappNumber}?text=".rawurlencode($message);
    }
}
