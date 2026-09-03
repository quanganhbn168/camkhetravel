<?php

namespace App\Filament\Bni\Resources\BniEvents\Pages;

use App\Filament\Bni\Resources\BniEvents\BniEventResource;
use App\Filament\Bni\Resources\Pages\ListBniRecords;
use Filament\Actions\CreateAction;

class ListBniEvents extends ListBniRecords
{
    protected static string $resource = BniEventResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Thêm sự kiện')];
    }
}
