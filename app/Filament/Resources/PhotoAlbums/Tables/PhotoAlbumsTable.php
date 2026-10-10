<?php

namespace App\Filament\Resources\PhotoAlbums\Tables;

use App\Models\PhotoAlbum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PhotoAlbumsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('photos')
                    ->label('Capa')
                    ->collection(PhotoAlbum::PHOTOS)
                    ->conversion('thumb')
                    ->limit(1),
                TextColumn::make('title')
                    ->label('Título')
                    ->searchable(),
                TextColumn::make('taken_at')
                    ->label('Data')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('media_count')
                    ->label('Fotos')
                    ->counts('media'),
                TextColumn::make('show.title')
                    ->label('Show'),
                IconColumn::make('is_published')
                    ->label('Publicado')
                    ->boolean(),
            ])
            ->defaultSort('taken_at', 'desc')
            ->filters([
                TernaryFilter::make('is_published')
                    ->label('Publicado'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
