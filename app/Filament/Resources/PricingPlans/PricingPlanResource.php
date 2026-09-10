<?php

namespace App\Filament\Resources\PricingPlans;

use App\Filament\Resources\PricingPlans\Pages\CreatePricingPlan;
use App\Filament\Resources\PricingPlans\Pages\EditPricingPlan;
use App\Filament\Resources\PricingPlans\Pages\ListPricingPlans;
use App\Filament\Resources\PricingPlans\Schemas\PricingPlanForm;
use App\Filament\Resources\PricingPlans\Tables\PricingPlansTable;
use App\Models\PricingPlan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PricingPlanResource extends Resource
{
    protected static ?string $model = PricingPlan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $navigationLabel = 'Gói giá landing page';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return 'Landingpage';
    }

    public static function getModelLabel(): string
    {
        return 'gói giá';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Bảng giá landing page';
    }

    public static function form(Schema $schema): Schema
    {
        return PricingPlanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PricingPlansTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPricingPlans::route('/'),
            'create' => CreatePricingPlan::route('/create'),
            'edit' => EditPricingPlan::route('/{record}/edit'),
        ];
    }
}
