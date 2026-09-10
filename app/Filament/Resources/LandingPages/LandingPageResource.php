<?php

namespace App\Filament\Resources\LandingPages;

use App\Filament\Resources\Concerns\UsesPrimaryKeyForRecordRoutes;
use App\Filament\Resources\LandingPages\Pages\CreateLandingPage;
use App\Filament\Resources\LandingPages\Pages\EditLandingPage;
use App\Filament\Resources\LandingPages\Pages\ListLandingPages;
use App\Filament\Resources\LandingPages\Schemas\LandingPageForm;
use App\Filament\Resources\LandingPages\Tables\LandingPagesTable;
use App\Models\LandingPage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LandingPageResource extends Resource
{
    use UsesPrimaryKeyForRecordRoutes;

    protected static ?string $model = LandingPage::class;

    protected static ?string $recordRouteKeyName = 'id';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleGroup;

    protected static ?string $navigationLabel = 'Landing pages';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return 'Landingpage';
    }

    public static function getModelLabel(): string
    {
        return 'landing page';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Landing pages';
    }

    public static function form(Schema $schema): Schema
    {
        return LandingPageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LandingPagesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLandingPages::route('/'),
            'create' => CreateLandingPage::route('/create'),
            'edit' => EditLandingPage::route('/{record}/edit'),
        ];
    }
}
