<?php

namespace App\Filament\Resources\DeveloperResourceTechnologies\Pages;

use App\Filament\Resources\DeveloperResourceTechnologies\DeveloperResourceTechnologyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageDeveloperResourceTechnologies extends ManageRecords
{
    protected static string $resource = DeveloperResourceTechnologyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
