<?php

namespace App\Filament\Resources\ProductCategories\Tables;

use App\Support\Categories\CategoryTree;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

final class ProductCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['curatorMedia', 'parent'])->withCount('children'))
            ->columns([
                ImageColumn::make('image_url')->label('Ảnh'),
                TextColumn::make('parent.name')->label('Danh mục cha')->placeholder('Danh mục gốc')->searchable(),
                TextColumn::make('name')->copyable()->copyMessage('Đã sao chép')->label('Danh mục')->description(fn ($record) => CategoryTree::tablePath($record))->searchable()->sortable(),
                TextColumn::make('products_count')->counts('products')->label('Sản phẩm')->sortable(),
                TextColumn::make('sort_order')->label('Thứ tự')->sortable(),
                ToggleColumn::make('is_active')->label('Hiển thị'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->recordActions([EditAction::make(), DeleteAction::make()->hidden(fn ($record) => $record->children_count > 0)]);
    }
}
