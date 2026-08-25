<?php

namespace App\Filament\Bni\Resources\BniEvents\Pages;

use App\Filament\Bni\Resources\BniEvents\BniEventResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageBniEvents extends ManageRecords
{
    protected static string $resource = BniEventResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Thêm sự kiện')];
    }
}
