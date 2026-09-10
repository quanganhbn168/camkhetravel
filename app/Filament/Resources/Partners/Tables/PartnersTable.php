<?php

namespace App\Filament\Resources\Partners\Tables;

use App\Models\Partner;
use Awcodes\Curator\Components\Tables\CuratorColumn;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class PartnersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                CuratorColumn::make('curatorMedia')->label('Logo')->square(),
                TextColumn::make('name')->label('Đối tác')->searchable()->sortable(),
                TextColumn::make('website_url')->label('Website')->toggleable()->limit(36),
                IconColumn::make('is_active')->label('Hiển thị')->boolean(),
                TextColumn::make('updated_at')->label('Cập nhật')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
