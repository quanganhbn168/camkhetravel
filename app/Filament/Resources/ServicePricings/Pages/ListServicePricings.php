<?php

namespace App\Filament\Resources\ServicePricings\Pages;

use App\Filament\Resources\ServicePricings\ServicePricingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListServicePricings extends ListRecords
{
    protected static string $resource = ServicePricingResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Thêm bảng giá')];
    }
}
