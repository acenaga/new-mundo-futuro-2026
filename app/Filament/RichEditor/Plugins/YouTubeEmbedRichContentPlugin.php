<?php

namespace App\Filament\RichEditor\Plugins;

use App\Rules\ValidYouTubeVideo;
use App\Support\RichContent\YouTubeEmbed;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\EditorCommand;
use Filament\Forms\Components\RichEditor\Plugins\Contracts\RichContentPlugin;
use Filament\Forms\Components\RichEditor\RichEditorTool;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Validation\ValidationException;
use Tiptap\Core\Extension;

class YouTubeEmbedRichContentPlugin implements RichContentPlugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    /**
     * @return array<Extension>
     */
    public function getTipTapPhpExtensions(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    public function getTipTapJsExtensions(): array
    {
        return [];
    }

    /**
     * @return array<RichEditorTool>
     */
    public function getEditorTools(): array
    {
        return [
            RichEditorTool::make('youtubeEmbed')
                ->label('Insertar YouTube')
                ->action()
                ->icon(Heroicon::Play),
            RichEditorTool::make('youtubeReplace')
                ->label('Actualizar YouTube')
                ->action()
                ->icon(Heroicon::PencilSquare),
            RichEditorTool::make('youtubeRemove')
                ->label('Quitar YouTube')
                ->action()
                ->icon(Heroicon::Trash),
        ];
    }

    /**
     * @return array<Action>
     */
    public function getEditorActions(): array
    {
        return [
            Action::make('youtubeEmbed')
                ->label('Insertar video de YouTube')
                ->schema([
                    TextInput::make('url')
                        ->label('URL de YouTube')
                        ->placeholder('https://www.youtube.com/watch?v=Cn8HBj8QAbk')
                        ->required()
                        ->rule(new ValidYouTubeVideo),
                ])
                ->action(function (array $arguments, array $data, RichEditor $component): void {
                    $videoId = YouTubeEmbed::extractVideoId($data['url'] ?? null);

                    $component->runCommands(
                        [
                            EditorCommand::make('insertContent', [
                                YouTubeEmbed::insertMarkup($videoId),
                            ]),
                        ],
                        editorSelection: $arguments['editorSelection'] ?? null,
                    );
                }),
            Action::make('youtubeReplace')
                ->label('Actualizar video de YouTube')
                ->schema([
                    TextInput::make('current_url')
                        ->label('Video actual (URL o ID)')
                        ->placeholder('https://www.youtube.com/watch?v=Cn8HBj8QAbk')
                        ->required()
                        ->rule(new ValidYouTubeVideo),
                    TextInput::make('new_url')
                        ->label('Nuevo video (URL o ID)')
                        ->placeholder('https://www.youtube.com/watch?v=dQw4w9WgXcQ')
                        ->required()
                        ->rule(new ValidYouTubeVideo),
                ])
                ->action(function (array $data, RichEditor $component): void {
                    $fromVideoId = YouTubeEmbed::extractVideoId($data['current_url'] ?? null);
                    $toVideoId = YouTubeEmbed::extractVideoId($data['new_url'] ?? null);

                    $currentContent = (string) ($component->getState() ?? '');
                    $updatedContent = YouTubeEmbed::replaceInContent($currentContent, $fromVideoId, $toVideoId);

                    if ($updatedContent === $currentContent) {
                        throw ValidationException::withMessages([
                            'current_url' => 'No se encontro ese video dentro del contenido.',
                        ]);
                    }

                    $component->state($updatedContent);
                }),
            Action::make('youtubeRemove')
                ->label('Quitar video de YouTube')
                ->schema([
                    TextInput::make('url')
                        ->label('Video a quitar (URL o ID)')
                        ->placeholder('https://www.youtube.com/watch?v=Cn8HBj8QAbk')
                        ->required()
                        ->rule(new ValidYouTubeVideo),
                ])
                ->action(function (array $data, RichEditor $component): void {
                    $videoId = YouTubeEmbed::extractVideoId($data['url'] ?? null);

                    $currentContent = (string) ($component->getState() ?? '');
                    $updatedContent = YouTubeEmbed::removeFromContent($currentContent, $videoId);

                    if ($updatedContent === $currentContent) {
                        throw ValidationException::withMessages([
                            'url' => 'No se encontro ese video dentro del contenido.',
                        ]);
                    }

                    $component->state($updatedContent);
                }),
        ];
    }
}
