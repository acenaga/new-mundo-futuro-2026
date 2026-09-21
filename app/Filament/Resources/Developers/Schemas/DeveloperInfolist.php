<?php

namespace App\Filament\Resources\Developers\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DeveloperInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Recurso')->schema([
                TextEntry::make('name')->label('Nombre'),
                TextEntry::make('external_url')->label('URL oficial')->url(fn ($record): string => $record->external_url)->openUrlInNewTab(),
                TextEntry::make('category.name')->label('Categoría'),
                TextEntry::make('technologies.name')->label('Tecnologías')->badge(),
                TextEntry::make('pricing')->label('Modalidad')->badge(),
                IconEntry::make('no_card_required')->label('Sin tarjeta')->boolean(),
                TextEntry::make('last_verified_at')->label('Última revisión')->dateTime()->placeholder('Pendiente'),
                TextEntry::make('summary')->label('Resumen')->columnSpanFull(),
                TextEntry::make('why_use_it')->label('¿Para qué sirve?')->columnSpanFull(),
                TextEntry::make('when_not_to_use_it')->label('¿Cuándo no conviene?')->columnSpanFull(),
                TextEntry::make('free_tier_details')->label('Plan gratuito')->columnSpanFull(),
            ])->columns(2),
        ]);
    }
}
