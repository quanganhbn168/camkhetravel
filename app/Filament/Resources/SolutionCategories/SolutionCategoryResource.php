<?php

namespace App\Filament\Resources\SolutionCategories;

use App\Filament\Resources\Concerns\UsesPrimaryKeyForRecordRoutes;
use App\Filament\Resources\SolutionCategories\Pages\CreateSolutionCategory;
use App\Filament\Resources\SolutionCategories\Pages\EditSolutionCategory;
use App\Filament\Resources\SolutionCategories\Pages\ListSolutionCategories;
use App\Filament\Resources\SolutionCategories\Schemas\SolutionCategoryForm;
use App\Filament\Resources\SolutionCategories\Tables\SolutionCategoriesTable;
use App\Models\SolutionCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SolutionCategoryResource extends Resource
{
    use UsesPrimaryKeyForRecordRoutes;

    protected static ?string $model = SolutionCategory::class;

    protected static ?string $recordRouteKeyName = 'id';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $navigationLabel = 'Danh mục giải pháp';

    protected static ?string $recordTitleAttribute = 'name';

    // Keep the category and solution resources adjacent without reordering other modules.
    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return 'Nội dung website';
    }

    public static function getModelLabel(): string
    {
        return 'danh mục giải pháp';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Danh mục giải pháp';
    }

    public static function form(Schema $schema): Schema
    {
        return SolutionCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SolutionCategoriesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSolutionCategories::route('/'),
            'create' => CreateSolutionCategory::route('/create'),
            'edit' => EditSolutionCategory::route('/{record}/edit'),
        ];
    }
}
