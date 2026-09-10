<?php

namespace App\Filament\Resources\ServiceCategories;

use App\Filament\Resources\Concerns\CategoryResource;
use App\Filament\Resources\ServiceCategories\Pages\CreateServiceCategory;
use App\Filament\Resources\ServiceCategories\Pages\EditServiceCategory;
use App\Filament\Resources\ServiceCategories\Pages\ListServiceCategories;
use App\Filament\Resources\ServiceCategories\Schemas\ServiceCategoryForm;
use App\Filament\Resources\ServiceCategories\Tables\ServiceCategoriesTable;
use App\Models\ServiceCategory;
use BackedEnum;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ServiceCategoryResource extends CategoryResource
{
    protected static ?string $model = ServiceCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $navigationLabel = 'Nhóm dịch vụ';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return ServiceCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ServiceCategoriesTable::configure($table);
    }

    public static function getModelLabel(): string
    {
        return 'nhóm dịch vụ';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Nhóm dịch vụ';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServiceCategories::route('/'),
            'create' => CreateServiceCategory::route('/create'),
            'edit' => EditServiceCategory::route('/{record}/edit'),
        ];
    }
}
