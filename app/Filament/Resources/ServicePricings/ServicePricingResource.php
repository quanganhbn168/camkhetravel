<?php

namespace App\Filament\Resources\ServicePricings;

use App\Filament\Resources\ServicePricings\Pages\CreateServicePricing;
use App\Filament\Resources\ServicePricings\Pages\EditServicePricing;
use App\Filament\Resources\ServicePricings\Pages\ListServicePricings;
use App\Filament\Resources\ServicePricings\Schemas\ServicePricingForm;
use App\Filament\Resources\ServicePricings\Tables\ServicePricingsTable;
use App\Models\ServicePricing;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ServicePricingResource extends Resource
{
    protected static ?string $model = ServicePricing::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $navigationLabel = 'Bảng giá dịch vụ';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return 'Nội dung website';
    }

    public static function getModelLabel(): string
    {
        return 'bảng giá dịch vụ';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Bảng giá dịch vụ';
    }

    public static function form(Schema $schema): Schema
    {
        return ServicePricingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ServicePricingsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServicePricings::route('/'),
            'create' => CreateServicePricing::route('/create'),
            'edit' => EditServicePricing::route('/{record}/edit'),
        ];
    }
}
