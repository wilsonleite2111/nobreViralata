<?php

namespace App\Filament\Resources\PhotoAlbums\Pages;

use App\Filament\Resources\PhotoAlbums\PhotoAlbumResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPhotoAlbum extends EditRecord
{
    protected static string $resource = PhotoAlbumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
