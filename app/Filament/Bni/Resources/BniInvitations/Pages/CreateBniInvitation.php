<?php

namespace App\Filament\Bni\Resources\BniInvitations\Pages;

use App\Filament\Bni\Resources\BniInvitations\BniInvitationResource;
use App\Filament\Bni\Resources\Pages\CreateBniRecord;
use App\Support\Bni\BniPanelAccess;

class CreateBniInvitation extends CreateBniRecord
{
    protected static string $resource = BniInvitationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return BniPanelAccess::prepareInvitationData($data);
    }
}
