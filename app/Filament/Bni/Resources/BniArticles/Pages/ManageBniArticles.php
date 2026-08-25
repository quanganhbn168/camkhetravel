<?php

namespace App\Filament\Bni\Resources\BniArticles\Pages;

use App\Filament\Bni\Resources\BniArticles\BniArticleResource;
use App\Support\Bni\BniPanelAccess;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageBniArticles extends ManageRecords
{
    protected static string $resource = BniArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Viết bài')
                ->mutateDataUsing(fn (array $data): array => BniPanelAccess::forceChapter($data)),
        ];
    }
}
