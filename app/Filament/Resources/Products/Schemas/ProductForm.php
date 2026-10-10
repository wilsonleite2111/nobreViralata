<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Product;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nome')
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
                    ->helperText('Aparece no link do produto.')
                    ->required()
                    ->maxLength(255)
                    ->alphaDash()
                    ->unique(),
                Select::make('product_category_id')
                    ->label('Categoria')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('price_cents')
                    ->label('Preço')
                    ->prefix('R$')
                    ->numeric()
                    ->step(0.01)
                    ->minValue(0.01)
                    ->maxValue(99999.99)
                    ->required()
                    ->formatStateUsing(fn (?int $state): ?string => $state === null ? null : number_format($state / 100, 2, '.', ''))
                    ->dehydrateStateUsing(fn (string|float|int $state): int => (int) round(((float) $state) * 100)),
                SpatieMediaLibraryFileUpload::make('photos')
                    ->label('Fotos')
                    ->helperText('Arraste para reordenar. A primeira foto é a principal.')
                    ->collection(Product::PHOTOS)
                    ->visibility('public')
                    ->multiple()
                    ->reorderable()
                    ->appendFiles()
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(15 * 1024)
                    ->panelLayout('grid')
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->label('Descrição')
                    ->rows(5)
                    ->columnSpanFull(),
                Repeater::make('variants')
                    ->label('Variações (tamanhos, cores...)')
                    ->helperText('Deixe sem variações se o produto tem uma opção só; nesse caso vale o estoque abaixo.')
                    ->relationship()
                    ->orderColumn('sort_order')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome')
                            ->placeholder('Ex.: M')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('stock')
                            ->label('Estoque')
                            ->integer()
                            ->minValue(0)
                            ->default(0)
                            ->required(),
                    ])
                    ->columns(2)
                    ->defaultItems(0)
                    ->addActionLabel('Adicionar variação')
                    ->columnSpanFull(),
                TextInput::make('stock')
                    ->label('Estoque (produto sem variações)')
                    ->integer()
                    ->minValue(0)
                    ->default(0)
                    ->required(),
                TextInput::make('external_url')
                    ->label('Link de compra externo')
                    ->helperText('Opcional. Se preenchido, o botão Comprar leva a este link em vez do WhatsApp.')
                    ->url()
                    ->startsWith(['https://', 'http://'])
                    ->maxLength(255),
                Toggle::make('is_active')
                    ->label('Ativo na loja'),
                Toggle::make('is_featured')
                    ->label('Destaque na página inicial'),
            ]);
    }
}
