<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\SubscriberFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property CarbonImmutable|null $confirmed_at
 * @property CarbonImmutable|null $unsubscribed_at
 */
#[Fillable(['email', 'confirmed_at', 'unsubscribed_at'])]
class Subscriber extends Model
{
    /** @use HasFactory<SubscriberFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        // The token is the secret in the confirmation and unsubscribe links.
        static::creating(function (Subscriber $subscriber): void {
            $subscriber->token ??= Str::random(64);
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'confirmed_at' => 'immutable_datetime',
            'unsubscribed_at' => 'immutable_datetime',
        ];
    }

    public function isActive(): bool
    {
        return $this->confirmed_at !== null && $this->unsubscribed_at === null;
    }

    public function confirm(): void
    {
        if ($this->isActive()) {
            return;
        }

        $this->update(['confirmed_at' => now(), 'unsubscribed_at' => null]);
    }

    public function unsubscribe(): void
    {
        if ($this->unsubscribed_at !== null) {
            return;
        }

        $this->update(['unsubscribed_at' => now()]);
    }
}
