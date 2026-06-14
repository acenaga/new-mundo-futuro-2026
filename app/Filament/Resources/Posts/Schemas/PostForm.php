<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Enums\PostStatus;
use App\Enums\PostType;
use App\Filament\RichEditor\Plugins\YouTubeEmbedRichContentPlugin;
use App\Models\Category;
use App\Rules\OnlyYouTubeEmbeds;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Configuración')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Select::make('user_id')
                            ->label('Autor')
                            ->relationship('author', 'name')
                            ->searchable()
                            ->preload()
                            ->default(fn () => auth()->id())
                            ->visible(fn () => auth()->user()?->hasRole('admin'))
                            ->required(),
                        Select::make('type')
                            ->label('Tipo')
                            ->options(PostType::class)
                            ->default(PostType::Article)
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                $stateVal = $state instanceof PostType ? $state->value : $state;
                                if ($stateVal === PostType::Tutorial->value) {
                                    $tutorialCategory = Category::where('slug', 'tutoriales')->first();
                                    if ($tutorialCategory) {
                                        $set('category_id', $tutorialCategory->id);
                                    }
                                } else {
                                    $currentCategory = Category::find($get('category_id'));
                                    if ($currentCategory && $currentCategory->slug === 'tutoriales') {
                                        $set('category_id', null);
                                    }
                                }
                            }),
                        Select::make('category_id')
                            ->label('Categoría')
                            ->relationship(
                                name: 'category',
                                titleAttribute: 'name',
                                modifyQueryUsing: function (Builder $query, Get $get) {
                                    $type = $get('type');
                                    $isTutorial = ($type instanceof PostType ? $type->value : $type) === PostType::Tutorial->value;

                                    return $query->when(
                                        $isTutorial,
                                        fn ($q) => $q->where('slug', 'tutoriales'),
                                        fn ($q) => $q->where('slug', '!=', 'tutoriales')
                                    );
                                }
                            )
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->rules([
                                function (Get $get) {
                                    return function (string $attribute, $value, \Closure $fail) use ($get) {
                                        if (! $value) {
                                            return;
                                        }

                                        $type = $get('type');
                                        $isTutorial = ($type instanceof PostType ? $type->value : $type) === PostType::Tutorial->value;

                                        $category = Category::find($value);
                                        if (! $category) {
                                            return;
                                        }

                                        if ($isTutorial && $category->slug !== 'tutoriales') {
                                            $fail('Un tutorial solo puede tener la categoría "Tutoriales".');
                                        }

                                        if (! $isTutorial && $category->slug === 'tutoriales') {
                                            $fail('Una publicación no puede tener la categoría "Tutoriales".');
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
                            ->preload()
                            ->columnSpanFull(),
                        FileUpload::make('cover_image_path')
                            ->label('Imagen de portada')
                            ->disk('public')
                            ->image()
                            ->imageEditor()
                            ->visibility('public')
                            ->columnSpanFull(),
                        TextInput::make('video_url')
                            ->label('URL del video (YouTube)')
                            ->url()
                            ->placeholder('https://www.youtube.com/watch?v=...')
                            ->columnSpanFull(),
                        Toggle::make('allow_comments')
                            ->label('Permitir comentarios')
                            ->default(true),
                    ]),
                Section::make('Contenido')
                    ->columnSpanFull()
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
            ]);
    }
}
