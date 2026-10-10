<?php

namespace App\Filament\Resources\PhotoAlbums\Schemas;

use App\Models\PhotoAlbum;
use App\Models\Show;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PhotoAlbumForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Título')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, ?string $state, string $operation): void {
                        if ($operation === 'create') {
                            $set('slug', Str::slug((string) $state));
                        }
                    }),
                TextInput::make('slug')
                    ->label('Endereço da página')
                    ->helperText('Aparece no link do álbum.')
                    ->required()
                    ->maxLength(255)
                    ->alphaDash()
                    ->unique(),
                DatePicker::make('taken_at')
                    ->label('Data das fotos')
                    ->default(today())
                    ->required(),
                Select::make('show_id')
                    ->label('Show relacionado')
                    ->relationship('show', 'title')
                    ->getOptionLabelFromRecordUsing(fn (Show $record): string => "{$record->starts_at->format('d/m/Y')} — {$record->title}")
                    ->searchable()
                    ->preload(),
                SpatieMediaLibraryFileUpload::make('photos')
                    ->label('Fotos')
                    ->helperText('Arraste para reordenar. A primeira foto é a capa do álbum.')
                    ->collection(PhotoAlbum::PHOTOS)
                    ->visibility('public')
                    ->multiple()
                    ->reorderable()
                    ->appendFiles()
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(15 * 1024)
                    ->panelLayout('grid')
                    ->columnSpanFull(),
                Toggle::make('is_published')
                    ->label('Publicado no site'),
            ]);
    }
}
