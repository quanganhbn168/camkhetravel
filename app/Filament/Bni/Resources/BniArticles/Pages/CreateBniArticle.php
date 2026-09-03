<?php

namespace App\Filament\Bni\Resources\BniArticles\Pages;

use App\Filament\Bni\Resources\BniArticles\BniArticleResource;
use App\Filament\Bni\Resources\Pages\CreateBniRecord;
use App\Support\Bni\BniPanelAccess;

class CreateBniArticle extends CreateBniRecord
{
    protected static string $resource = BniArticleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return BniPanelAccess::prepareArticleData($data);
    }
}
