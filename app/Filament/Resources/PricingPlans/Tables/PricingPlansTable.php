<?php

namespace App\Filament\Resources\PricingPlans\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PricingPlansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Gói giá')->searchable()->sortable()->wrap(),
                TextColumn::make('landingPage.title')->label('Landing page')->badge()->toggleable(),
                TextColumn::make('price')
                    ->label('Mức giá')
                    ->formatStateUsing(fn ($state, $record): string => filled($state)
                        ? 'Từ '.number_format((int) $state).'đ'
                        : ($record->price_label ?: 'Liên hệ')),
                IconColumn::make('is_featured')->label('Nổi bật')->boolean(),
                IconColumn::make('is_active')->label('Hiển thị')->boolean(),
            ])
            ->filters([
                SelectFilter::make('landing_page_id')->label('Landing page')->relationship('landingPage', 'title'),
                TernaryFilter::make('is_active')->label('Hiển thị'),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
