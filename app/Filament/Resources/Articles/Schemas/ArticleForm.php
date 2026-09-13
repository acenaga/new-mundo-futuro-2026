<?php

namespace App\Filament\Resources\Articles\Schemas;

use App\Enums\PostStatus;
use App\Filament\Forms\Components\CoverImageUpload;
use App\Filament\RichEditor\Plugins\YouTubeEmbedRichContentPlugin;
use App\Models\Category;
use App\Rules\OnlyYouTubeEmbeds;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'lg' => 3])
            ->components([
                Section::make('Contenido')
                    ->columnSpan(['default' => 1, 'lg' => 2])
                    ->schema([
                        TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, Set $set) => $set('slug', Str::slug($state))),
                        TextInput::make('slug')
                            ->required()
                            ->disabled()
                            ->dehydrated()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Textarea::make('excerpt')
                            ->label('Extracto')
                            ->rows(3)
                            ->maxLength(500),
                        RichEditor::make('body')
                            ->label('Contenido')
                            ->required()
                            ->plugins([
                                YouTubeEmbedRichContentPlugin::make(),
                            ])
                            ->enableToolbarButtons([
                                ['youtubeEmbed', 'youtubeReplace', 'youtubeRemove'],
                            ])
                            ->preventFileAttachmentPathTampering()
                            ->rule(new OnlyYouTubeEmbeds),
                    ]),
                Section::make('Configuración')
                    ->columns(1)
                    ->columnSpan(['default' => 1, 'lg' => 1])
                    ->schema([
                        Select::make('user_id')
                            ->label('Autor')
                            ->relationship('author', 'name')
                            ->searchable()
                            ->preload()
                            ->default(fn () => auth()->id())
                            ->visible(fn () => auth()->user()?->hasRole('admin'))
                            ->required(),
                        Select::make('category_id')
                            ->label('Categoría')
                            ->relationship(
                                name: 'category',
                                titleAttribute: 'name',
                                modifyQueryUsing: function (Builder $query) {
                                    return $query->where('slug', '!=', 'tutoriales');
                                }
                            )
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->rules([
                                function () {
                                    return function (string $attribute, $value, \Closure $fail) {
                                        if (! $value) {
                                            return;
                                        }

                                        $category = Category::find($value);
                                        if (! $category) {
                                            return;
                                        }

                                        if ($category->slug === 'tutoriales') {
                                            $fail('Un artículo no puede tener la categoría "Tutoriales".');
                                        }
                                    };
                                },
                            ]),
                        Select::make('status')
                            ->label('Estado')
                            ->options(PostStatus::class)
                            ->default(PostStatus::Draft)
                            ->required(),
                        DateTimePicker::make('published_at')
                            ->label('Fecha de publicación')
                            ->nullable(),
                        Select::make('tags')
                            ->label('Etiquetas')
                            ->relationship('tags', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload(),
                        CoverImageUpload::make('cover_image_path')
                            ->label('Imagen de portada'),
                        TextInput::make('video_url')
                            ->label('URL del video (YouTube)')
                            ->url()
                            ->placeholder('https://www.youtube.com/watch?v=...'),
                        Toggle::make('allow_comments')
                            ->label('Permitir comentarios')
                            ->default(true),
                    ]),
            ]);
    }
}
