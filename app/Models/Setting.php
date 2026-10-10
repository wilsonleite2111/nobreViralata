<?php

namespace App\Models;

use Database\Factories\SettingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Site-wide content edited in the admin panel, stored as key/value pairs.
 */
#[Fillable(['key', 'value'])]
class Setting extends Model
{
    /** @use HasFactory<SettingFactory> */
    use HasFactory;

    public const string BAND_HISTORY = 'band.history';

    public const string STORE_WHATSAPP = 'store.whatsapp';

    /**
     * Reduces a phone number typed in any format to the digits wa.me expects, adding Brazil's country code when missing.
     */
    public static function normalizeWhatsapp(string $value): ?string
    {
        $digits = preg_replace('/\D+/', '', $value) ?? '';

        if (in_array(strlen($digits), [10, 11], true)) {
            $digits = '55'.$digits;
        }

        return in_array(strlen($digits), [12, 13], true) && str_starts_with($digits, '55') ? $digits : null;
    }

    public static function read(string $key): ?string
    {
        return self::query()->where('key', $key)->value('value');
    }

    public static function write(string $key, ?string $value): void
    {
        self::query()->updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
