<?php

namespace App\Filament\Resources\DeveloperResourceCategories\Pages;

use App\Filament\Resources\DeveloperResourceCategories\DeveloperResourceCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageDeveloperResourceCategories extends ManageRecords
{
    protected static string $resource = DeveloperResourceCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
