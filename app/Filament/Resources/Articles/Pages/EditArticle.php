<?php

namespace App\Filament\Resources\Articles\Pages;

use App\Filament\Resources\Articles\Actions\ImportFromUrlAction;
use App\Filament\Resources\Articles\ArticleResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditArticle extends EditRecord
{
    protected static string $resource = ArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportFromUrlAction::make(),
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
