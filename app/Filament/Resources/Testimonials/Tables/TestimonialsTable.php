<?php

namespace App\Filament\Resources\Testimonials\Tables;

use App\Models\Testimonial;
use Awcodes\Curator\Components\Tables\CuratorColumn;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class TestimonialsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                CuratorColumn::make('curatorMedia')->label('Ảnh')->square(),
                TextColumn::make('client_name')->label('Khách hàng')->searchable()->sortable(),
                TextColumn::make('company_name')->label('Doanh nghiệp')->toggleable(),
                TextColumn::make('rating')
                    ->label('Sao')
                    ->formatStateUsing(fn (?int $state): string => $state ? "{$state}/5" : 'Chưa có')
                    ->sortable(),
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
