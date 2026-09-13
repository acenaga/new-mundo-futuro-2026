<?php

namespace App\Filament\Resources\Articles\Pages;

use App\Filament\Resources\Articles\Actions\ImportFromUrlAction;
use App\Filament\Resources\Articles\ArticleResource;
use App\Filament\Resources\Articles\Concerns\ImportsArticleFromUrl;
use Filament\Resources\Pages\CreateRecord;

class CreateArticle extends CreateRecord
{
    use ImportsArticleFromUrl;

    protected static string $resource = ArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportFromUrlAction::make(),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! auth()->user()?->hasRole('admin')) {
            $data['user_id'] = auth()->id();
        }

        return $data;
    }
}
