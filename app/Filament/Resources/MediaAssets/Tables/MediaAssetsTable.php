<?php

namespace App\Filament\Resources\MediaAssets\Tables;

use App\Models\MediaAsset;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MediaAssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('public_url')
                    ->label('Ảnh')
                    ->getStateUsing(fn (MediaAsset $record): ?string => str_starts_with((string) $record->mime_type, 'image/')
                        ? $record->public_url
                        : null)
                    ->square()
                    ->imageSize(56),
                TextColumn::make('title')
                    ->label('Tên media')
                    ->searchable()
                    ->limit(60),
                TextColumn::make('effective_alt_text')
                    ->label('Alt SEO')
                    ->searchable()
                    ->limit(60)
                    ->toggleable(),
                TextColumn::make('source')
                    ->label('Nguồn')
                    ->searchable(),
                TextColumn::make('source_id')
                    ->label('WP ID')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('mime_type')
                    ->label('Định dạng')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('localization_status')
                    ->label('Trạng thái')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'localized' => 'Đã lưu nội bộ',
                        'pending' => 'Chờ đồng bộ',
                        'missing' => 'Thiếu tệp',
                        'failed' => 'Đồng bộ lỗi',
                        default => $state ?: 'Chưa rõ',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'localized' => 'success',
                        'pending' => 'warning',
                        'missing', 'failed' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('width')
                    ->label('Rộng')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('height')
                    ->label('Cao')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('published_at')
                    ->label('Ngày tải lên')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
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
                SelectFilter::make('localization_status')
                    ->label('Trạng thái tệp')
                    ->options([
                        'localized' => 'Đã lưu nội bộ',
                        'pending' => 'Chờ đồng bộ',
                        'missing' => 'Thiếu tệp nguồn',
                        'failed' => 'Đồng bộ lỗi',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }
}
