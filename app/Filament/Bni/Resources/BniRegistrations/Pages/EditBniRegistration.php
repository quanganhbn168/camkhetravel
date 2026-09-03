<?php

namespace App\Filament\Bni\Resources\BniRegistrations\Pages;

use App\Filament\Bni\Resources\BniRegistrations\BniRegistrationResource;
use App\Filament\Bni\Resources\Pages\EditBniRecord;
use App\Support\Bni\BniPanelAccess;

class EditBniRegistration extends EditBniRecord
{
    protected static string $resource = BniRegistrationResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return BniPanelAccess::prepareRegistrationData($data);
    }
}
