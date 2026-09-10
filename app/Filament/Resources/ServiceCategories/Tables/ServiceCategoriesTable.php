<?php

namespace App\Filament\Resources\ServiceCategories\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

final class ServiceCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Danh mục')->searchable()->sortable(),
                TextColumn::make('slug')->label('Slug')->copyable(),
                TextColumn::make('sort_order')->label('Thứ tự')->sortable(),
                IconColumn::make('is_active')->label('Hiển thị')->boolean(),
                ToggleColumn::make('is_featured')->label('Nổi bật'),
                ToggleColumn::make('is_home')->label('Trang chủ'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}
