<?php

namespace App\Filament\Resources\Tags\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

final class TagsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->copyable()->copyMessage('Đã sao chép')->label('Tên thẻ')->searchable()->sortable(),
                ToggleColumn::make('is_active')->label('Đang dùng'),
                TextColumn::make('updated_at')->label('Cập nhật')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->defaultSort('name')
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}
