<?php

namespace App\Filament\Bni\Resources\BniRegistrations\Pages;

use App\Filament\Bni\Resources\BniRegistrations\BniRegistrationResource;
use App\Filament\Bni\Resources\Pages\ListBniRecords;
use Filament\Actions\CreateAction;

class ListBniRegistrations extends ListBniRecords
{
    protected static string $resource = BniRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Thêm đăng ký')];
    }
}
