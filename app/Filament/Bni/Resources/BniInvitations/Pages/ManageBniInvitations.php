<?php

namespace App\Filament\Bni\Resources\BniInvitations\Pages;

use App\Filament\Bni\Resources\BniInvitations\BniInvitationResource;
use App\Support\Bni\BniPanelAccess;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageBniInvitations extends ManageRecords
{
    protected static string $resource = BniInvitationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Thêm khách mời')
                ->mutateDataUsing(fn (array $data): array => BniPanelAccess::prepareInvitationData($data)),
        ];
    }
}
