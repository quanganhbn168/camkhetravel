<?php

namespace App\Filament\Resources\Menus\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

final class MenusTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Menu')->searchable()->sortable(),
                TextColumn::make('location')->label('Vị trí')->badge()->searchable()->sortable(),
                TextColumn::make('items_count')->label('Menu item')->counts('items')->sortable(),
                ToggleColumn::make('is_active')->label('Kích hoạt'),
                TextColumn::make('updated_at')->label('Cập nhật')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->defaultSort('name')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
