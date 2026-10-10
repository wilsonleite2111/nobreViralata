<?php

namespace App\Filament\Resources\PhotoAlbums;

use App\Filament\Resources\PhotoAlbums\Pages\CreatePhotoAlbum;
use App\Filament\Resources\PhotoAlbums\Pages\EditPhotoAlbum;
use App\Filament\Resources\PhotoAlbums\Pages\ListPhotoAlbums;
use App\Filament\Resources\PhotoAlbums\Schemas\PhotoAlbumForm;
use App\Filament\Resources\PhotoAlbums\Tables\PhotoAlbumsTable;
use App\Models\PhotoAlbum;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PhotoAlbumResource extends Resource
{
    protected static ?string $model = PhotoAlbum::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static ?string $modelLabel = 'álbum de fotos';

    protected static ?string $pluralModelLabel = 'álbuns de fotos';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return PhotoAlbumForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PhotoAlbumsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPhotoAlbums::route('/'),
            'create' => CreatePhotoAlbum::route('/create'),
            'edit' => EditPhotoAlbum::route('/{record}/edit'),
        ];
    }
}
