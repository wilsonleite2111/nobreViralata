<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Forms\Components\RichEditor;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * @property-read Schema $form
 */
class ManageBand extends Page
{
    protected string $view = 'filament.pages.manage-band';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static ?string $navigationLabel = 'História da banda';

    protected static ?string $title = 'História da banda';

    protected static ?string $slug = 'band';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'history' => Setting::read(Setting::BAND_HISTORY),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                RichEditor::make('history')
                    ->label('Texto da página "A Banda"')
                    ->toolbarButtons([
                        ['bold', 'italic', 'link'],
                        ['h2', 'h3'],
                        ['blockquote', 'bulletList', 'orderedList'],
                        ['undo', 'redo'],
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        Setting::write(Setting::BAND_HISTORY, $this->form->getState()['history'] ?? null);

        Notification::make()
            ->success()
            ->title('História salva')
            ->send();
    }
}
