<?php

namespace App\Filament\Bni\Resources\BniArticleCategories\Pages;

use App\Filament\Bni\Resources\BniArticleCategories\BniArticleCategoryResource;
use App\Support\Bni\BniPanelAccess;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageBniArticleCategories extends ManageRecords
{
    protected static string $resource = BniArticleCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Thêm danh mục')
                ->visible(fn (): bool => BniPanelAccess::canManageEverything()),
        ];
    }
}
