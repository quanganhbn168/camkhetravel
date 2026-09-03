<?php

namespace App\Filament\Bni\Resources\BniArticles\Pages;

use App\Filament\Bni\Resources\BniArticles\BniArticleResource;
use App\Filament\Bni\Resources\Pages\ListBniRecords;
use Filament\Actions\CreateAction;

class ListBniArticles extends ListBniRecords
{
    protected static string $resource = BniArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Viết bài')];
    }
}
