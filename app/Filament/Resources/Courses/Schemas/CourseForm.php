<?php

namespace App\Filament\Resources\Courses\Schemas;

use App\Filament\RichEditor\Plugins\YouTubeEmbedRichContentPlugin;
use App\Rules\OnlyYouTubeEmbeds;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('teacher', 'name')
                    ->required(),
                TextInput::make('title')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('slug', Str::slug($state));
                    })
                    ->maxLength(255),
                TextInput::make('slug')
                    ->required()
                    ->disabled()
                    ->dehydrated()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                RichEditor::make('description')
                    ->required()
                    ->maxLength(65535)
                    ->plugins([
                        YouTubeEmbedRichContentPlugin::make(),
                    ])
                    ->enableToolbarButtons([
                        ['youtubeEmbed'],
                    ])
                    ->preventFileAttachmentPathTampering()
                    ->rule(new OnlyYouTubeEmbeds)
                    ->columnSpanFull(),
                FileUpload::make('image_path')
                    ->imageEditor()
                    ->image(),
                TextInput::make('status')
                    ->required()
                    ->default('draft'),
                Toggle::make('is_premium')
                    ->required(),
            ]);
    }
}
