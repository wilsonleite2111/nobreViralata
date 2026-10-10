<?php

namespace App\Filament\Resources\Subscribers;

use App\Filament\Resources\Subscribers\Pages\ManageSubscribers;
use App\Models\Subscriber;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SubscriberResource extends Resource
{
    protected static ?string $model = Subscriber::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAtSymbol;

    protected static ?string $modelLabel = 'inscrito na newsletter';

    protected static ?string $pluralModelLabel = 'inscritos na newsletter';

    protected static ?string $recordTitleAttribute = 'email';

    /**
     * People subscribe themselves on the site, with e-mail confirmation.
     */
    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('email')
            ->columns([
                TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('status')
                    ->label('Situação')
                    ->badge()
                    ->state(fn (Subscriber $record): string => match (true) {
                        $record->unsubscribed_at !== null => 'Saiu da lista',
                        $record->confirmed_at !== null => 'Confirmado',
                        default => 'Aguardando confirmação',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Confirmado' => 'success',
                        'Saiu da lista' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Inscrito em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Situação')
                    ->options([
                        'confirmed' => 'Confirmado',
                        'pending' => 'Aguardando confirmação',
                        'unsubscribed' => 'Saiu da lista',
                    ])
                    ->query(fn (Builder $query, array $data): Builder => match ($data['value'] ?? null) {
                        'confirmed' => $query->whereNotNull('confirmed_at')->whereNull('unsubscribed_at'),
                        'pending' => $query->whereNull('confirmed_at')->whereNull('unsubscribed_at'),
                        'unsubscribed' => $query->whereNotNull('unsubscribed_at'),
                        default => $query,
                    }),
            ])
            ->recordActions([
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSubscribers::route('/'),
        ];
    }
}
