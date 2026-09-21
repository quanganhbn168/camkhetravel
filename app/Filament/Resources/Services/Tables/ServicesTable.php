<?php

namespace App\Filament\Resources\Services\Tables;

use App\Models\Service;
use App\Support\Localization\LocalizedUrl;
use Awcodes\Curator\Components\Tables\CuratorColumn;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

final class ServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                CuratorColumn::make('curatorMedia')->label('Ảnh')->square(),
                TextColumn::make('title')->label('Dịch vụ')->searchable()->sortable()->wrap(),
                TextColumn::make('category.name')->label('Danh mục')->badge()->toggleable(),
                TextColumn::make('status')->label('Trạng thái')->badge(),
                TextColumn::make('backstage_projects_count')->counts('backstageProjects')->label('Dự án')->sortable(),
                IconColumn::make('is_featured')->label('Nổi bật')->boolean(),
                IconColumn::make('is_home')->label('Trang chủ')->boolean(),
                TextColumn::make('updated_at')->label('Cập nhật')->dateTime('d/m/Y H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('service_category_id')->label('Danh mục dịch vụ')->relationship('category', 'name'),
                SelectFilter::make('status')->label('Trạng thái')->options(['draft' => 'Bản nháp', 'published' => 'Đã xuất bản', 'pending' => 'Chờ duyệt', 'private' => 'Riêng tư']),
                TernaryFilter::make('is_home')->label('Trang chủ'),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                Action::make('preview')->label('Xem dịch vụ')->icon(Heroicon::OutlinedArrowTopRightOnSquare)->url(fn (Service $record): string => LocalizedUrl::service($record))->openUrlInNewTab(),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
