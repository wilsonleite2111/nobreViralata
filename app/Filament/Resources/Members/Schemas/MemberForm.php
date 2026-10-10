<?php

namespace App\Filament\Resources\Members\Schemas;

use App\Models\Member;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),
                TextInput::make('role')
                    ->label('Função na banda')
                    ->helperText('Ex.: Vocal, Guitarra, Bateria.')
                    ->required()
                    ->maxLength(255),
                SpatieMediaLibraryFileUpload::make('photo')
                    ->label('Foto')
                    ->helperText('De preferência na vertical. O site recorta em formato retrato.')
                    ->collection(Member::PHOTO)
                    ->visibility('public')
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(15 * 1024)
                    ->columnSpanFull(),
                Textarea::make('bio')
                    ->label('Sobre')
                    ->rows(5)
                    ->columnSpanFull(),
            ]);
    }
}
