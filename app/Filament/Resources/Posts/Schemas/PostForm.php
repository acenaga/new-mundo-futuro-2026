<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Enums\PostStatus;
use App\Filament\RichEditor\Plugins\YouTubeEmbedRichContentPlugin;
use App\Rules\OnlyYouTubeEmbeds;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
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
                        Select::make('category_id')
                            ->label('Categoría')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
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
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
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
