<?php

namespace App\Filament\Bni\Resources\BniRegistrations\Pages;

use App\Filament\Bni\Resources\BniRegistrations\BniRegistrationResource;
use App\Filament\Bni\Resources\Pages\CreateBniRecord;
use App\Support\Bni\BniPanelAccess;

class CreateBniRegistration extends CreateBniRecord
{
    protected static string $resource = BniRegistrationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return BniPanelAccess::prepareRegistrationData($data);
    }
}
