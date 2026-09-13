<?php

namespace App\Filament\Resources\Articles\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ArticleInfolist
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
                TextEntry::make('source_url')
                    ->label('Fuente (URL)')
                    ->url(fn (?string $state) => $state, shouldOpenInNewTab: true)
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('source_title')
                    ->label('Título original')
                    ->placeholder('-'),
                TextEntry::make('source_author')
                    ->label('Autor original')
                    ->placeholder('-'),
                TextEntry::make('source_site')
                    ->label('Sitio de origen')
                    ->placeholder('-'),
                TextEntry::make('source_published_at')
                    ->label('Publicación original')
                    ->dateTime()
                    ->placeholder('-'),
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
