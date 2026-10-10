<?php

namespace App\Filament\Resources\Videos\Schemas;

use App\Models\Show;
use App\Models\Video;
use App\VideoCategory;
use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class VideoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Título')
                    ->required()
                    ->maxLength(255),
                TextInput::make('youtube_id')
                    ->label('Link do YouTube')
                    ->helperText('Cole o link do vídeo (ex.: https://www.youtube.com/watch?v=...) ou só o código dele.')
                    ->required()
                    ->rule(fn (): Closure => function (string $attribute, mixed $value, Closure $fail): void {
                        if (! is_string($value) || Video::extractYoutubeId($value) === null) {
                            $fail('Informe um link válido de vídeo do YouTube.');
                        }
                    })
                    ->dehydrateStateUsing(fn (string $state): ?string => Video::extractYoutubeId($state)),
                Select::make('category')
                    ->label('Categoria')
                    ->options(VideoCategory::class)
                    ->required(),
                DatePicker::make('published_at')
                    ->label('Data do vídeo')
                    ->default(today())
                    ->required(),
                Select::make('show_id')
                    ->label('Show relacionado')
                    ->relationship('show', 'title')
                    ->getOptionLabelFromRecordUsing(fn (Show $record): string => "{$record->starts_at->format('d/m/Y')} — {$record->title}")
                    ->searchable()
                    ->preload(),
                Toggle::make('is_featured')
                    ->label('Destaque na página inicial'),
                Toggle::make('is_published')
                    ->label('Publicado no site'),
            ]);
    }
}
