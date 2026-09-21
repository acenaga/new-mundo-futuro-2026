<?php

namespace App\Filament\Resources\Developers\Pages;

use App\Filament\Resources\Developers\Actions\CompleteDeveloperResourceFromUrlAction;
use App\Filament\Resources\Developers\Concerns\ImportsDeveloperResourceFromUrl;
use App\Filament\Resources\Developers\DeveloperResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditDeveloper extends EditRecord
{
    use ImportsDeveloperResourceFromUrl;

    protected static string $resource = DeveloperResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CompleteDeveloperResourceFromUrlAction::make(),
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
