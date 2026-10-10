<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Models\Post;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PostForm
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
                    ->helperText('Aparece no link da notícia.')
                    ->required()
                    ->maxLength(255)
                    ->alphaDash()
                    ->unique(),
                DateTimePicker::make('published_at')
                    ->label('Data de publicação')
                    ->helperText('Uma data no futuro agenda a notícia: ela só aparece no site a partir desse momento.')
                    ->seconds(false)
                    ->default(now())
                    ->required(),
                Toggle::make('is_published')
                    ->label('Publicado no site'),
                SpatieMediaLibraryFileUpload::make('cover')
                    ->label('Imagem de capa')
                    ->collection(Post::COVER)
                    ->visibility('public')
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(15 * 1024)
                    ->columnSpanFull(),
                Textarea::make('excerpt')
                    ->label('Resumo')
                    ->helperText('Aparece na lista de notícias. Se ficar vazio, o site usa o começo do texto.')
                    ->rows(2)
                    ->maxLength(300)
                    ->columnSpanFull(),
                RichEditor::make('body')
                    ->label('Texto')
                    ->toolbarButtons([
                        ['bold', 'italic', 'link'],
                        ['h2', 'h3'],
                        ['blockquote', 'bulletList', 'orderedList'],
                        ['undo', 'redo'],
                    ])
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
