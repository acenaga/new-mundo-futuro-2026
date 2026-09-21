<?php

namespace App\Filament\Resources\Articles\Actions;

use App\Filament\Resources\Articles\Pages\CreateArticle;
use App\Filament\Resources\Articles\Pages\EditArticle;
use App\Jobs\DraftArticleFromUrlJob;
use App\Support\Sources\ArticleDraftStore;
use App\Support\Sources\SourceUnavailableException;
use App\Support\Sources\UrlSafety;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Validation\ValidationException;

class ImportFromUrlAction
{
    public static function make(): Action
    {
        return Action::make('importFromUrl')
            ->label('Importar desde URL')
            ->icon(Heroicon::OutlinedLink)
            ->color('gray')
            ->disabled(fn (CreateArticle|EditArticle $livewire): bool => $livewire->pendingDraftKey !== null)
            ->modalHeading('Importar artículo desde una URL')
            ->modalDescription('Se descargará el artículo, se redactará una versión en español citando la fuente y se rellenarán el título, el extracto, el contenido y los datos de la fuente. La generación se hace en segundo plano y los valores actuales de esos campos se sobrescribirán al terminar. Revisa el resultado antes de publicar.')
            ->modalSubmitActionLabel('Generar borrador')
            ->schema([
                TextInput::make('url')
                    ->label('URL del artículo')
                    ->placeholder('https://ejemplo.com/articulo')
                    ->url()
                    ->required()
                    ->maxLength(2048),
                Toggle::make('import_images')
                    ->label('Importar las imágenes del artículo original')
                    ->helperText('Se descargan, validan, optimizan y colocan en el contenido. Una fuente pública no implica permiso de reutilización: verifica tus derechos.')
                    ->default(true),
                Toggle::make('generate_cover')
                    ->label('Generar imagen de portada con IA')
                    ->helperText('Crea una ilustración a partir del título y el extracto. Puedes reemplazarla después.')
                    ->default(true),
            ])
            ->action(function (array $data, Schema $schema, CreateArticle|EditArticle $livewire, ArticleDraftStore $store): void {
                try {
                    UrlSafety::assertAllowed($data['url']);
                } catch (SourceUnavailableException $exception) {
                    throw ValidationException::withMessages([
                        $schema->getStatePath().'.url' => $exception->getMessage(),
                    ]);
                }

                $key = $store->start($data['url'], (int) auth()->id());

                DraftArticleFromUrlJob::dispatch(
                    $key,
                    $data['url'],
                    importImages: (bool) ($data['import_images'] ?? true),
                    generateCover: (bool) ($data['generate_cover'] ?? true),
                );

                $livewire->pendingDraftKey = $key;

                Notification::make()
                    ->title('Generando borrador')
                    ->body('Te avisaremos aquí mismo cuando esté listo. Puedes seguir editando mientras tanto.')
                    ->info()
                    ->send();
            });
    }
}
