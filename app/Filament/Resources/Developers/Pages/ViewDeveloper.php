<?php

namespace App\Filament\Resources\Developers\Pages;

use App\Filament\Resources\Developers\DeveloperResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDeveloper extends ViewRecord
{
    protected static string $resource = DeveloperResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
