<?php

namespace App\Filament\Bni\Resources\BniArticleCategories\Pages;

use App\Filament\Bni\Resources\BniArticleCategories\BniArticleCategoryResource;
use App\Filament\Bni\Resources\Pages\ListBniRecords;
use App\Support\Bni\BniPanelAccess;
use Filament\Actions\CreateAction;

class ListBniArticleCategories extends ListBniRecords
{
    protected static string $resource = BniArticleCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Thêm danh mục')->visible(fn (): bool => BniPanelAccess::canManageEverything())];
    }
}
