<?php

namespace App\Filament\Resources\Redirects\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RedirectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('source')
                    ->label('Nguồn')
                    ->searchable(),
                TextColumn::make('from_path')
                    ->label('Đường dẫn cũ')
                    ->searchable(),
                TextColumn::make('to_url')
                    ->label('URL đích')
                    ->searchable()
                    ->limit(60),
                TextColumn::make('status_code')
                    ->label('Mã')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Bật')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
