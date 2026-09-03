<?php

namespace App\Filament\Bni\Resources\BniInvitations\Pages;

use App\Filament\Bni\Resources\BniInvitations\BniInvitationResource;
use App\Filament\Bni\Resources\Pages\EditBniRecord;
use App\Support\Bni\BniPanelAccess;

class EditBniInvitation extends EditBniRecord
{
    protected static string $resource = BniInvitationResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return BniPanelAccess::prepareInvitationData($data);
    }
}
