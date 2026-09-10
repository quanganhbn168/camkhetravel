<?php

namespace App\Filament\Resources\Posts\Tables;

use Awcodes\Curator\Components\Tables\CuratorColumn;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

final class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                CuratorColumn::make('curatorMedia')->label('Ảnh')->square(),
                TextColumn::make('title')->label('Tiêu đề')->searchable()->sortable()->wrap(),
                TextColumn::make('categories.name')->label('Chuyên mục')->badge()->separator(', ')->toggleable(),
                TextColumn::make('status')->label('Trạng thái')->badge(),
                IconColumn::make('is_featured')->label('Nổi bật')->boolean(),
                TextColumn::make('published_at')->label('Xuất bản')->dateTime('d/m/Y')->sortable(),
            ])
            ->filters([
                SelectFilter::make('categories')->label('Chuyên mục')->relationship('categories', 'name'),
                SelectFilter::make('status')->label('Trạng thái')->options(['draft' => 'Bản nháp', 'published' => 'Đã xuất bản', 'pending' => 'Chờ duyệt', 'private' => 'Riêng tư']),
            ])
            ->defaultSort('published_at', 'desc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
