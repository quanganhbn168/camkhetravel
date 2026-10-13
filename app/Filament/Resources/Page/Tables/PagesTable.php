<?php

namespace App\Filament\Resources\Page\Tables;

use App\Models\Page;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('slugs'))
            ->columns([
                TextColumn::make('type')
                    ->label('Loại')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === Page::TYPE_SYSTEM ? 'Hệ thống' : 'Tự tạo')
                    ->color(fn (string $state): string => $state === Page::TYPE_SYSTEM ? 'gray' : 'primary'),
                TextColumn::make('title')->label('Tiêu đề')->searchable()->sortable()->weight('medium')->limit(60),
                TextColumn::make('path')
                    ->label('Đường dẫn')
                    ->state(fn (Page $record): string => $record->isSystem()
                        ? '/'.ltrim((string) parse_url($record->url, PHP_URL_PATH), '/')
                        : (filled($record->slug) ? '/'.$record->slug : '—'))
                    ->copyable()
                    ->copyMessage('Đã sao chép'),
                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->state(fn (Page $record): string => match (true) {
                        $record->isSystem() => 'Đang hiển thị',
                        $record->status !== 'published' => 'Bản nháp',
                        $record->published_at?->isFuture() => 'Hẹn giờ',
                        default => 'Đang hiển thị',
                    })
                    ->color(fn (string $state): string => $state === 'Đang hiển thị' ? 'success' : 'warning'),
                TextColumn::make('seo')
                    ->label('SEO')
                    ->badge()
                    ->state(fn (Page $record): string => filled($record->seo_title) && filled($record->seo_description) ? 'Đủ' : 'Thiếu')
                    ->color(fn (string $state): string => $state === 'Đủ' ? 'success' : 'danger'),
                TextColumn::make('updated_at')->label('Cập nhật')->dateTime('d/m/Y H:i')->sortable()->toggleable(),
            ])
            ->defaultSort(fn ($query) => $query->orderByDesc('type')->orderBy('sort_order')->orderByDesc('updated_at'))
            ->filters([
                SelectFilter::make('type')->label('Loại')->options([Page::TYPE_SYSTEM => 'Hệ thống', Page::TYPE_CUSTOM => 'Tự tạo']),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('view')
                    ->label('Xem')
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->color('gray')
                    ->url(fn (Page $record): ?string => $record->isSystem() || $record->status === 'published' ? $record->url : null, shouldOpenInNewTab: true)
                    ->visible(fn (Page $record): bool => $record->isSystem() || ($record->status === 'published' && filled($record->slug))),
            ])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->checkIfRecordIsSelectableUsing(fn (Page $record): bool => ! $record->isSystem());
    }
}
