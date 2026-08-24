<?php

namespace App\Filament\Resources\ContentItems\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ContentItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Tiêu đề')
                    ->searchable()
                    ->wrap()
                    ->limit(70),
                TextColumn::make('type')
                    ->label('Loại')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'post' => 'Bài viết',
                        'page' => 'Trang',
                        'landing' => 'Landing',
                        'service' => 'Dịch vụ',
                        'us_portfolio' => 'Dự án',
                        'partner' => 'Đối tác',
                        'us_testimonial' => 'Đánh giá',
                        'video' => 'Video',
                        default => $state,
                    })
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'published' => 'Đã xuất bản',
                        'draft' => 'Bản nháp',
                        'pending' => 'Chờ duyệt',
                        'private' => 'Riêng tư',
                        default => $state,
                    }),
                TextColumn::make('canonical_path')
                    ->label('URL')
                    ->searchable()
                    ->copyable()
                    ->limit(55),
                IconColumn::make('needs_seo_review')
                    ->label('Rà SEO')
                    ->boolean(),
                TextColumn::make('published_at')
                    ->label('Xuất bản')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('seo_score')
                    ->label('SEO')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('source_id')
                    ->label('WP ID')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Loại nội dung')
                    ->options([
                        'post' => 'Bài viết',
                        'page' => 'Trang',
                        'landing' => 'Landing page',
                        'service' => 'Dịch vụ',
                        'us_portfolio' => 'Dự án',
                        'partner' => 'Đối tác',
                        'us_testimonial' => 'Đánh giá',
                        'video' => 'Video',
                    ]),
                SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'published' => 'Đã xuất bản',
                        'draft' => 'Bản nháp',
                        'pending' => 'Chờ duyệt',
                        'private' => 'Riêng tư',
                    ]),
                TernaryFilter::make('needs_seo_review')
                    ->label('Cần rà soát SEO'),
                TrashedFilter::make(),
            ])
            ->defaultSort('published_at', 'desc')
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
