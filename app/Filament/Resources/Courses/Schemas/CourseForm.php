<?php

namespace App\Filament\Resources\Courses\Schemas;

use App\Enums\CourseStatus;
use App\Filament\RichEditor\Plugins\YouTubeEmbedRichContentPlugin;
use App\Rules\OnlyYouTubeEmbeds;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
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
                    ->afterStateUpdated(function ($state, Set $set) {
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
                        ['youtubeEmbed', 'youtubeReplace', 'youtubeRemove'],
                    ])
                    ->preventFileAttachmentPathTampering()
                    ->rule(new OnlyYouTubeEmbeds)
                    ->columnSpanFull(),
                FileUpload::make('image_path')
                    ->imageEditor()
                    ->image(),
                Select::make('status')
                    ->options(CourseStatus::class)
                    ->default(CourseStatus::Draft)
                    ->required(),
                Toggle::make('is_premium')
                    ->required(),
            ]);
    }
}
