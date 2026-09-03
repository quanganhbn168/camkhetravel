<?php

namespace App\Filament\Resources\AboutDepartments\Pages;

use App\Filament\Resources\AboutDepartments\AboutDepartmentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAboutDepartment extends EditRecord
{
    protected static string $resource = AboutDepartmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
