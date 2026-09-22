<?php

namespace App\Filament\Resources\Products\Tables;

use Awcodes\Curator\Components\Tables\CuratorColumn;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

final class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                CuratorColumn::make('curatorMedia')->label('Ảnh')->square(),
                TextColumn::make('title')->copyable()->copyMessage('Đã sao chép')->label('Sản phẩm')->searchable()->sortable()->wrap(),
                TextColumn::make('category.name')->copyable()->copyMessage('Đã sao chép')->label('Danh mục')->badge()->toggleable(),
                TextColumn::make('sku')->label('Mã')->toggleable(),
                TextColumn::make('status')->label('Trạng thái')->badge(),
                ToggleColumn::make('is_featured')->label('Nổi bật'),
            ])
            ->filters([
                SelectFilter::make('product_category_id')->label('Danh mục')->relationship('category', 'name'),
                SelectFilter::make('status')->label('Trạng thái')->options(['draft' => 'Bản nháp', 'published' => 'Đã xuất bản', 'pending' => 'Chờ duyệt', 'private' => 'Riêng tư']),
            ])
            ->defaultSort('sort_order')
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}
