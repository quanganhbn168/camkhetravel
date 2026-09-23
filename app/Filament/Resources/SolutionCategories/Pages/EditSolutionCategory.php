<?php

namespace App\Filament\Resources\SolutionCategories\Pages;

use App\Filament\Resources\SolutionCategories\SolutionCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSolutionCategory extends EditRecord
{
    protected static string $resource = SolutionCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->disabled(fn (): bool => $this->record->solutions()->exists())
                ->tooltip('Danh mục chỉ được xóa khi không còn giải pháp.'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
