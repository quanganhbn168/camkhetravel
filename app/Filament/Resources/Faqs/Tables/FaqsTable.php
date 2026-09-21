<?php

namespace App\Filament\Resources\Faqs\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

final class FaqsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('question')->label('Câu hỏi')->searchable()->wrap(),
                TextColumn::make('faqable_type')->label('Phạm vi')->formatStateUsing(fn (?string $state): string => match ($state) {
                    null, '' => 'Trang chủ',
                    'service' => 'Dịch vụ',
                    'project' => 'Dự án',
                    'product' => 'Sản phẩm',
                    'post' => 'Bài viết',
                    default => $state,
                }),
                TextColumn::make('group')->label('Nhóm')->badge(),
                TextColumn::make('sort_order')->label('Thứ tự')->sortable(),
                IconColumn::make('is_active')->label('Hiển thị')->boolean(),
            ])
            ->filters([
                SelectFilter::make('group')->label('Nhóm')->options(['homepage' => 'Trang chủ', 'detail' => 'Chi tiết nội dung']),
                SelectFilter::make('is_active')->label('Hiển thị')->options([1 => 'Đang hiển thị', 0 => 'Đang ẩn']),
            ])
            ->defaultSort('sort_order')
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}
