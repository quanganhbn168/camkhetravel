<?php

namespace App\Filament\Resources\AboutDepartments\Pages;

use App\Filament\Resources\AboutDepartments\AboutDepartmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAboutDepartments extends ListRecords
{
    protected static string $resource = AboutDepartmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Thêm phòng ban'),
        ];
    }
}
