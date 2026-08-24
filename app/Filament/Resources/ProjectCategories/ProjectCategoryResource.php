<?php

namespace App\Filament\Resources\ProjectCategories;

use App\Filament\Resources\Concerns\CategoryResource;
use App\Filament\Resources\ProjectCategories\Pages\CreateProjectCategory;
use App\Filament\Resources\ProjectCategories\Pages\EditProjectCategory;
use App\Filament\Resources\ProjectCategories\Pages\ListProjectCategories;
use App\Models\ProjectCategory;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

class ProjectCategoryResource extends CategoryResource
{
    protected static ?string $model = ProjectCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $navigationLabel = 'Danh mục dự án';

    protected static ?int $navigationSort = 3;

    public static function getModelLabel(): string
    {
        return 'danh mục dự án';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Danh mục dự án';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProjectCategories::route('/'),
            'create' => CreateProjectCategory::route('/create'),
            'edit' => EditProjectCategory::route('/{record}/edit'),
        ];
    }
}
