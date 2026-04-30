<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PostInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('author.name')
                    ->label('Autor'),
                TextEntry::make('category.name')
                    ->label('Categoría')
                    ->placeholder('-'),
                TextEntry::make('title')
                    ->label('Título'),
                TextEntry::make('slug'),
                TextEntry::make('status')
                    ->label('Estado')
                    ->badge(),
                TextEntry::make('published_at')
                    ->label('Publicado el')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('excerpt')
                    ->label('Extracto')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('body')
                    ->label('Contenido')
                    ->html()
                    ->columnSpanFull(),
                ImageEntry::make('cover_image_path')
                    ->label('Portada')
                    ->disk('public')
                    ->visibility('public')
                    ->placeholder('-'),
                IconEntry::make('allow_comments')
                    ->label('Comentarios')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->label('Creado')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime(),
            ]);
    }
}
