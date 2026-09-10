<?php

namespace App\Filament\Bni\Resources\BniSponsors\Pages;

use App\Filament\Bni\Resources\BniSponsors\BniSponsorResource;
use App\Filament\Bni\Resources\Pages\ListBniRecords;
use Filament\Actions\CreateAction;

class ListBniSponsors extends ListBniRecords
{
    protected static string $resource = BniSponsorResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Thêm nhà tài trợ')];
    }
}
