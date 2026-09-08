<?php

namespace App\Filament\Resources\Services\Pages;

use App\Filament\Resources\ServicePricings\ServicePricingResource;
use App\Filament\Resources\Services\ServiceResource;
use App\Support\Localization\LocalizedUrl;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditService extends EditRecord
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('pricing')
                ->label('Bảng giá dịch vụ')
                ->icon(Heroicon::OutlinedCurrencyDollar)
                ->url(fn (): string => $this->record->pricingCatalog
                    ? ServicePricingResource::getUrl('edit', ['record' => $this->record->pricingCatalog])
                    : ServicePricingResource::getUrl('create', ['service_id' => $this->record->id]))
                ->openUrlInNewTab(),
            Action::make('preview')
                ->label('Xem dịch vụ')
                ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                ->url(fn (): string => LocalizedUrl::slug($this->record->slug))
                ->openUrlInNewTab(),
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
