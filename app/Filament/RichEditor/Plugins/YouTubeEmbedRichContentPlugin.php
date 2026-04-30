<?php

namespace App\Filament\RichEditor\Plugins;

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
                        ->rule(function (string $attribute, mixed $value, \Closure $fail): void {
                            if (YouTubeEmbed::extractVideoId(is_string($value) ? $value : null) === null) {
                                $fail('Ingresa una URL valida de YouTube.');
                            }
                        }),
                ])
                ->action(function (array $arguments, array $data, RichEditor $component): void {
                    $videoId = YouTubeEmbed::extractVideoId($data['url'] ?? null);

                    if ($videoId === null) {
                        throw ValidationException::withMessages([
                            'url' => 'Ingresa una URL valida de YouTube.',
                        ]);
                    }

                    $component->runCommands(
                        [
                            EditorCommand::make('insertContent', [
                                YouTubeEmbed::editorPreviewHtml($videoId).'<p>[[youtube:'.$videoId.']]</p>',
                            ]),
                        ],
                        editorSelection: $arguments['editorSelection'] ?? null,
                    );
                }),
        ];
    }
}
