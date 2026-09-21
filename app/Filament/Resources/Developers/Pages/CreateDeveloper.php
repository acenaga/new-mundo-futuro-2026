<?php

namespace App\Filament\Resources\Developers\Pages;

use App\Filament\Resources\Developers\Actions\CompleteDeveloperResourceFromUrlAction;
use App\Filament\Resources\Developers\Concerns\ImportsDeveloperResourceFromUrl;
use App\Filament\Resources\Developers\DeveloperResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDeveloper extends CreateRecord
{
    use ImportsDeveloperResourceFromUrl;

    protected static string $resource = DeveloperResource::class;

    protected function getHeaderActions(): array
    {
        return [CompleteDeveloperResourceFromUrlAction::make()];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] ??= auth()->id();

        return $data;
    }
}
