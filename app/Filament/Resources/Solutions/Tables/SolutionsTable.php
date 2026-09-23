<?php

namespace App\Filament\Resources\Solutions\Tables;

use App\Models\Solution;
use Awcodes\Curator\Components\Tables\CuratorColumn;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

final class SolutionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['category', 'curatorMedia', 'slugs']))
            ->columns([
                CuratorColumn::make('curatorMedia')->label('Ảnh')->square(),
                TextColumn::make('title')->label('Giải pháp')->copyable()->copyMessage('Đã sao chép')->searchable()->sortable()->wrap(),
                TextColumn::make('category.name')->label('Danh mục')->searchable()->sortable()
                    ->description(fn (Solution $record): ?string => $record->category?->is_active ? null : 'Danh mục đang ẩn'),
                ToggleColumn::make('is_active')->label('Công khai')->disabled(fn (Solution $record) => ! auth()->user()->can('update', $record)),
                ToggleColumn::make('is_home')->label('Trang chủ')->disabled(fn (Solution $record) => ! auth()->user()->can('update', $record)),
                TextColumn::make('sort_order')->label('Thứ tự')->sortable(),
            ])
            ->filters([
                SelectFilter::make('solution_category_id')->label('Danh mục giải pháp')
                    ->relationship('category', 'name')->searchable()->preload(),
                TernaryFilter::make('is_active')->label('Công khai'),
                TernaryFilter::make('is_home')->label('Trang chủ'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->recordActions([
                Action::make('preview')->label('Xem giải pháp')
                    ->url(fn (Solution $record) => route('solutions.show', ['solution' => $record->slug]))
                    ->openUrlInNewTab()->visible(fn (Solution $record): bool => $record->is_active && (bool) $record->category?->is_active),
                EditAction::make(), DeleteAction::make(),
            ]);
    }
}
