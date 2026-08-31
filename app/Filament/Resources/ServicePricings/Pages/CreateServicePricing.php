<?php

namespace App\Filament\Resources\ServicePricings\Pages;

use App\Filament\Resources\ServicePricings\ServicePricingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateServicePricing extends CreateRecord
{
    protected static string $resource = ServicePricingResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }
}
