<?php

namespace App\Filament\Bni\Resources\BniMembers\Pages;

use App\Filament\Bni\Resources\BniMembers\BniMemberResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageBniMembers extends ManageRecords
{
    protected static string $resource = BniMemberResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Tạo tài khoản hội viên')];
    }
}
