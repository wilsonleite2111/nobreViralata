<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Closure;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * @property-read Schema $form
 */
class ManageStore extends Page
{
    protected string $view = 'filament.pages.manage-store';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static string|UnitEnum|null $navigationGroup = 'Loja';

    protected static ?string $navigationLabel = 'WhatsApp de vendas';

    protected static ?string $title = 'WhatsApp de vendas';

    protected static ?string $slug = 'store';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'whatsapp' => Setting::read(Setting::STORE_WHATSAPP),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('whatsapp')
                    ->label('Número que recebe os pedidos')
                    ->helperText('Com DDD, ex.: (41) 99999-8888. O botão Comprar abre uma conversa com este número. Sem número, só produtos com link externo podem ser comprados.')
                    ->tel()
                    ->maxLength(30)
                    ->rule(fn (): Closure => function (string $attribute, mixed $value, Closure $fail): void {
                        if (filled($value) && Setting::normalizeWhatsapp((string) $value) === null) {
                            $fail('Informe um número de celular brasileiro com DDD.');
                        }
                    }),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $whatsapp = $this->form->getState()['whatsapp'] ?? null;

        Setting::write(Setting::STORE_WHATSAPP, filled($whatsapp) ? Setting::normalizeWhatsapp($whatsapp) : null);

        Notification::make()
            ->success()
            ->title('WhatsApp salvo')
            ->send();
    }
}
