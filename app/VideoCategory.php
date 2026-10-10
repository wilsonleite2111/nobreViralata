<?php

namespace App;

use Filament\Support\Contracts\HasLabel;

enum VideoCategory: string implements HasLabel
{
    case Clip = 'clip';
    case Live = 'live';
    case Backstage = 'backstage';

    public function getLabel(): string
    {
        return match ($this) {
            self::Clip => 'Clipes',
            self::Live => 'Ao vivo',
            self::Backstage => 'Bastidores',
        };
    }
}
