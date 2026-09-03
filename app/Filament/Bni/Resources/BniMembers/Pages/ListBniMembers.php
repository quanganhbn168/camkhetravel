<?php

namespace App\Filament\Bni\Resources\BniMembers\Pages;

use App\Filament\Bni\Resources\BniMembers\BniMemberResource;
use App\Filament\Bni\Resources\Pages\ListBniRecords;
use Filament\Actions\CreateAction;

class ListBniMembers extends ListBniRecords
{
    protected static string $resource = BniMemberResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Tạo tài khoản hội viên')];
    }
}
