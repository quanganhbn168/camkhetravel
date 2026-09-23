<?php

namespace App\Filament\Resources\SolutionCategories\Pages;

use App\Filament\Resources\SolutionCategories\SolutionCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSolutionCategories extends ListRecords
{
    protected static string $resource = SolutionCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Thêm danh mục giải pháp')];
    }
}
