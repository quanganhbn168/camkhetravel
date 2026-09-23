<?php

namespace App\Filament\Resources\SolutionCategories\Pages;

use App\Filament\Resources\SolutionCategories\SolutionCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSolutionCategory extends CreateRecord
{
    protected static string $resource = SolutionCategoryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
