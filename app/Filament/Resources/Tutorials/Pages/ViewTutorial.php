<?php

namespace App\Filament\Resources\Tutorials\Pages;

use App\Filament\Resources\Tutorials\TutorialResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTutorial extends ViewRecord
{
    protected static string $resource = TutorialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
