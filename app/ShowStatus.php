<?php

namespace App;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ShowStatus: string implements HasColor, HasLabel
{
    case Scheduled = 'scheduled';
    case SoldOut = 'sold_out';
    case Cancelled = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::Scheduled => 'Confirmado',
            self::SoldOut => 'Esgotado',
            self::Cancelled => 'Cancelado',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Scheduled => 'success',
            self::SoldOut => 'warning',
            self::Cancelled => 'danger',
        };
    }
}
