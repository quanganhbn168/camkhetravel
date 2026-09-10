<?php

namespace App\Filament\Resources\Languages\Tables;

use App\Models\Language;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class LanguagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Ngôn ngữ')->searchable()->sortable(),
                TextColumn::make('code')->label('Mã')->badge()->searchable(),
                TextColumn::make('native_name')->label('Nhãn'),
                TextColumn::make('og_locale')->label('OG locale')->toggleable(),
                IconColumn::make('is_default')->label('Mặc định')->boolean(),
                IconColumn::make('is_active')->label('Hiển thị')->boolean(),
                IconColumn::make('is_indexable')->label('Index')->boolean(),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()->visible(fn (Language $record): bool => ! $record->is_default),
            ]);
    }
}
