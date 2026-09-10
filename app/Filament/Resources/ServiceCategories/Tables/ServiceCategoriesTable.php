<?php

namespace App\Filament\Resources\ServiceCategories\Tables;

use App\Filament\Resources\ServiceCategories\ServiceCategoryResource;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

final class ServiceCategoriesTable
{
    public static function configure(Table $table): Table
    {
        $table = ServiceCategoryResource::baseTable($table);

        return $table->columns([
            ...$table->getColumns(),
            ToggleColumn::make('is_featured')->label('Nổi bật'),
            ToggleColumn::make('is_home')->label('Trang chủ'),
        ]);
    }
}
