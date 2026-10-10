<?php

namespace App\Filament\Resources\Shows\Schemas;

use App\ShowStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ShowForm
{
    private const array STATES = [
        'AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA',
        'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO',
    ];

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
                    ->helperText('Aparece no link do show. Se o título se repetir, acrescente a data (ex.: bar-do-ze-2026-11-20).')
                    ->required()
                    ->maxLength(255)
                    ->alphaDash()
                    ->unique(),
                DateTimePicker::make('starts_at')
                    ->label('Data e hora')
                    ->seconds(false)
                    ->required(),
                Select::make('status')
                    ->label('Situação')
                    ->options(ShowStatus::class)
                    ->default(ShowStatus::Scheduled)
                    ->required(),
                TextInput::make('venue')
                    ->label('Local')
                    ->required()
                    ->maxLength(255),
                TextInput::make('address')
                    ->label('Endereço')
                    ->maxLength(255),
                TextInput::make('city')
                    ->label('Cidade')
                    ->required()
                    ->maxLength(255),
                Select::make('state')
                    ->label('Estado')
                    ->options(array_combine(self::STATES, self::STATES))
                    ->searchable()
                    ->required(),
                TextInput::make('ticket_url')
                    ->label('Link de ingressos')
                    ->url()
                    ->startsWith(['https://', 'http://'])
                    ->maxLength(255)
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->label('Descrição')
                    ->rows(5)
                    ->columnSpanFull(),
                Toggle::make('is_published')
                    ->label('Publicado no site'),
            ]);
    }
}
