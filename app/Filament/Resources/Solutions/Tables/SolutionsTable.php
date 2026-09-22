<?php

namespace App\Filament\Resources\Solutions\Tables;

use App\Models\Solution;
use Awcodes\Curator\Components\Tables\CuratorColumn;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

final class SolutionsTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            CuratorColumn::make('curatorMedia')->label('Ảnh')->square(),
            TextColumn::make('title')->label('Giải pháp')->copyable()->copyMessage('Đã sao chép')->searchable()->sortable()->wrap(),
            TextColumn::make('short_title')->label('Loại công trình')->copyable(),
            ToggleColumn::make('is_active')->label('Công khai')->disabled(fn (Solution $record) => ! auth()->user()->can('update', $record)),
            ToggleColumn::make('is_home')->label('Trang chủ')->disabled(fn (Solution $record) => ! auth()->user()->can('update', $record)),
            TextColumn::make('sort_order')->label('Thứ tự')->sortable(),
        ])->defaultSort('sort_order')->recordActions([
            Action::make('preview')->label('Xem giải pháp')->url(fn (Solution $record) => route('solutions.show', ['solution' => $record->slug]))->openUrlInNewTab()->visible(fn (Solution $record) => $record->is_active),
            EditAction::make(), DeleteAction::make(),
        ]);
    }
}
