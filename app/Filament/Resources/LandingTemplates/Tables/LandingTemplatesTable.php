<?php

namespace App\Filament\Resources\LandingTemplates\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class LandingTemplatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Template')->searchable()->sortable()->wrap(),
                TextColumn::make('key')->label('Khóa')->badge()->copyable(),
                TextColumn::make('use_case')->label('Cấu trúc')->wrap()->toggleable(),
                TextColumn::make('source_name')->label('Nguồn')->wrap()->toggleable(),
                TextColumn::make('landing_pages_count')->counts('landingPages')->label('Landing đang dùng')->sortable(),
                IconColumn::make('is_active')->label('Cho phép chọn')->boolean(),
                TextColumn::make('updated_at')->label('Cập nhật')->dateTime('d/m/Y H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')->label('Cho phép chọn'),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
