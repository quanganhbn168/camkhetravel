<?php

namespace App\Filament\Bni\Resources\BniArticles\Pages;

use App\Filament\Bni\Resources\BniArticles\BniArticleResource;
use App\Filament\Bni\Resources\Pages\EditBniRecord;
use App\Support\Bni\BniPanelAccess;

class EditBniArticle extends EditBniRecord
{
    protected static string $resource = BniArticleResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return BniPanelAccess::prepareArticleData($data);
    }
}
