<?php

namespace App\Filament\Resources\Developers\Actions;

use App\Filament\Resources\Developers\Pages\CreateDeveloper;
use App\Filament\Resources\Developers\Pages\EditDeveloper;
use App\Jobs\DraftDeveloperResourceFromUrlJob;
use App\Support\Sources\DeveloperResourceDraftStore;
use App\Support\Sources\SourceUnavailableException;
use App\Support\Sources\UrlSafety;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Validation\ValidationException;

class CompleteDeveloperResourceFromUrlAction
{
    public static function make(): Action
    {
        return Action::make('completeDeveloperResourceFromUrl')
            ->label('Completar desde URL')
            ->icon(Heroicon::OutlinedLink)
            ->color('gray')
            ->disabled(fn (CreateDeveloper|EditDeveloper $livewire): bool => $livewire->pendingDeveloperResourceDraftKey !== null)
            ->modalHeading('Completar recurso desde su URL oficial')
            ->modalDescription('Se consultará el sitio oficial y hasta dos páginas internas de precios o documentación. El resultado reemplazará solo los campos generados, no se guardará automáticamente y no modificará publicación, destacado, logo ni tecnologías.')
            ->modalSubmitActionLabel('Investigar recurso')
            ->schema([
                TextInput::make('url')->label('URL oficial')->placeholder('https://ejemplo.com')->url()->required()->maxLength(2048),
            ])
            ->action(function (array $data, Schema $schema, CreateDeveloper|EditDeveloper $livewire, DeveloperResourceDraftStore $store): void {
                try {
                    UrlSafety::assertAllowed($data['url']);
                } catch (SourceUnavailableException $exception) {
                    throw ValidationException::withMessages([$schema->getStatePath().'.url' => $exception->getMessage()]);
                }

                $key = $store->start($data['url'], (int) auth()->id());
                DraftDeveloperResourceFromUrlJob::dispatch($key, $data['url']);
                $livewire->pendingDeveloperResourceDraftKey = $key;

                Notification::make()->title('Investigando recurso')->body('Se completará el formulario solo si la revisión editorial lo aprueba.')->info()->send();
            });
    }
}
