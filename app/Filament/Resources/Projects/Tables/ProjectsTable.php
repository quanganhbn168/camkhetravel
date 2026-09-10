<?php

namespace App\Filament\Resources\Projects\Tables;

use Awcodes\Curator\Components\Tables\CuratorColumn;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

final class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                CuratorColumn::make('curatorMedia')->label('Ảnh')->square(),
                TextColumn::make('title')->label('Dự án')->searchable()->sortable()->wrap(),
                TextColumn::make('category.name')->label('Danh mục')->badge()->toggleable(),
                TextColumn::make('client_name')->label('Khách hàng')->toggleable(),
                TextColumn::make('status')->label('Trạng thái')->badge(),
                IconColumn::make('is_featured')->label('Nổi bật')->boolean(),
            ])
            ->filters([
                SelectFilter::make('project_category_id')->label('Danh mục')->relationship('category', 'name'),
                SelectFilter::make('status')->label('Trạng thái')->options(['draft' => 'Bản nháp', 'published' => 'Đã xuất bản', 'pending' => 'Chờ duyệt', 'private' => 'Riêng tư']),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
