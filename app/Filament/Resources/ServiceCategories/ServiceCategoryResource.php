<?php

namespace App\Filament\Resources\ServiceCategories;

use App\Filament\Resources\Concerns\CategoryResource;
use App\Filament\Resources\ServiceCategories\Pages\CreateServiceCategory;
use App\Filament\Resources\ServiceCategories\Pages\EditServiceCategory;
use App\Filament\Resources\ServiceCategories\Pages\ListServiceCategories;
use App\Models\ServiceCategory;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

class ServiceCategoryResource extends CategoryResource
{
    protected static ?string $model = ServiceCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $navigationLabel = 'Nhóm dịch vụ';

    protected static ?int $navigationSort = 1;

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
