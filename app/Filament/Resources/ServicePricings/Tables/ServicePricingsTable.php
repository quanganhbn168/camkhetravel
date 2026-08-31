<?php

namespace App\Filament\Resources\ServicePricings\Tables;

use App\Support\Localization\LocalizedUrl;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ServicePricingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('service.title')->label('Dịch vụ')->searchable()->sortable()->wrap(),
                TextColumn::make('title')->label('Bảng giá')->searchable()->wrap(),
                TextColumn::make('packages_count')->counts('packages')->label('Số gói')->sortable(),
                TextColumn::make('source_url')->label('Link nguồn')->limit(35)->toggleable(),
                IconColumn::make('source_media_id')->label('Media')->boolean(),
                TextColumn::make('updated_at')->label('Cập nhật')->dateTime('d/m/Y H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('service_id')->label('Dịch vụ')->relationship('service', 'title'),
            ])
            ->defaultSort('updated_at', 'desc')
            ->recordActions([
                Action::make('preview')
                    ->label('Xem dịch vụ')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn ($record): string => LocalizedUrl::service($record->service))
                    ->openUrlInNewTab(),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
