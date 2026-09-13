<?php

namespace App\Filament\Resources\Articles\Actions;

use App\Actions\Articles\DraftArticleFromUrl;
use App\Support\Sources\SourceUnavailableException;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
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
            ->modalHeading('Importar artículo desde una URL')
            ->modalDescription('Se descargará el artículo, se redactará una versión en español citando la fuente y se rellenarán el título, el extracto, el contenido y los datos de la fuente. Los valores actuales de esos campos se sobrescribirán. Revisa el resultado antes de publicar.')
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
                    ->helperText('Se descargan, optimizan y colocan en el contenido. Verifica que tienes derecho a reutilizarlas.')
                    ->default(true),
                Toggle::make('generate_cover')
                    ->label('Generar imagen de portada con IA')
                    ->helperText('Crea una ilustración a partir del título y el extracto. Puedes reemplazarla después.')
                    ->default(true),
            ])
            ->action(function (array $data, Schema $schema, CreateRecord|EditRecord $livewire, DraftArticleFromUrl $draftArticle): void {
                try {
                    $draft = $draftArticle(
                        $data['url'],
                        importImages: (bool) ($data['import_images'] ?? true),
                        generateCover: (bool) ($data['generate_cover'] ?? true),
                    );
                } catch (SourceUnavailableException $exception) {
                    throw ValidationException::withMessages([
                        $schema->getStatePath().'.url' => $exception->getMessage(),
                    ]);
                }

                $livewire->form->fill([
                    ...$livewire->form->getRawState(),
                    ...$draft->fields,
                ]);

                Notification::make()
                    ->title('Borrador generado')
                    ->body('Revisa el contenido, las imágenes y la cita de la fuente antes de guardar.')
                    ->success()
                    ->send();

                foreach ($draft->warnings as $warning) {
                    Notification::make()
                        ->title('Aviso')
                        ->body($warning)
                        ->warning()
                        ->send();
                }
            });
    }
}
