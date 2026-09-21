<?php

namespace App\Filament\Resources\Developers\Tables;

use App\Enums\DeveloperResourcePricing;
use App\Enums\DeveloperResourceStatus;
use App\Models\DeveloperResource;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DevelopersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Recurso')->searchable()->sortable()
                    ->description(fn (DeveloperResource $record): string => $record->external_url),
                TextColumn::make('category.name')->label('Categoría')->badge()->sortable(),
                TextColumn::make('pricing')->label('Plan')->badge(),
                TextColumn::make('technologies.name')->label('Tecnologías')->badge()->separator(',')->limitList(3),
                TextColumn::make('status')->label('Estado')->badge()->sortable(),
                TextColumn::make('last_verified_at')->label('Verificado')->date()->placeholder('Pendiente')->sortable(),
                TextColumn::make('review_status')->label('Revisión')
                    ->state(fn (DeveloperResource $record): string => $record->is_stale ? 'Pendiente' : 'Al día')
                    ->badge()
                    ->color(fn (DeveloperResource $record): string => $record->is_stale ? 'warning' : 'success'),
            ])
            ->filters([
                SelectFilter::make('status')->label('Estado')->options(DeveloperResourceStatus::class),
                SelectFilter::make('category')->label('Categoría')->relationship('category', 'name'),
                SelectFilter::make('technologies')->label('Tecnología')->relationship('technologies', 'name'),
                SelectFilter::make('pricing')->label('Modalidad')->options(DeveloperResourcePricing::class),
                Filter::make('no_card_required')->label('Sin tarjeta')->query(fn ($query) => $query->where('no_card_required', true)),
                Filter::make('requires_review')->label('Requiere revisión')->query(fn ($query) => $query->requiresReview()),
            ])
            ->recordActions([
                Action::make('markAsVerified')->label('Marcar revisado')->icon('heroicon-o-check-circle')->color('success')
                    ->action(fn (DeveloperResource $record) => $record->markAsVerified()),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
