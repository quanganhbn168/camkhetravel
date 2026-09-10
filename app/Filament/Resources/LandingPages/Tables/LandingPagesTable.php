<?php

namespace App\Filament\Resources\LandingPages\Tables;

use App\Models\LandingPage;
use App\Support\Localization\LocalizedUrl;
use Awcodes\Curator\Components\Tables\CuratorColumn;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

final class LandingPagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                CuratorColumn::make('curatorMedia')->label('Ảnh')->square(),
                TextColumn::make('title')->label('Landing page')->searchable()->sortable()->wrap(),
                TextColumn::make('status')->label('Trạng thái')->badge(),
                TextColumn::make('projects_count')->counts('projects')->label('Dự án')->sortable(),
                TextColumn::make('posts_count')->counts('posts')->label('Blog')->sortable(),
                IconColumn::make('is_featured')->label('Nổi bật')->boolean(),
                TextColumn::make('updated_at')->label('Cập nhật')->dateTime('d/m/Y H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')->label('Trạng thái')->options(['draft' => 'Bản nháp', 'published' => 'Đã xuất bản', 'pending' => 'Chờ duyệt', 'private' => 'Riêng tư']),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                Action::make('preview')->label('Xem trang')->icon(Heroicon::OutlinedArrowTopRightOnSquare)->url(fn (LandingPage $record): string => LocalizedUrl::landingPage($record))->openUrlInNewTab(),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
