<?php

namespace App\Filament\Resources\MediaAssets\Schemas;

use App\Models\MediaAsset;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class MediaAssetInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ImageEntry::make('public_url')
                    ->label('Xem trước')
                    ->getStateUsing(fn (MediaAsset $record): ?string => str_starts_with((string) $record->mime_type, 'image/')
                        ? $record->public_url
                        : null)
                    ->imageHeight(320)
                    ->columnSpanFull(),
                TextEntry::make('source'),
                TextEntry::make('source_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('parent_source_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('slug')
                    ->placeholder('-'),
                TextEntry::make('title')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('effective_alt_text')
                    ->label('Alt SEO')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('alt_text')
                    ->label('Alt gốc WordPress')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('caption')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('mime_type')
                    ->placeholder('-'),
                TextEntry::make('source_url')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('source_path')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('disk')
                    ->placeholder('-'),
                TextEntry::make('file_path')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('localization_status')
                    ->label('Trạng thái tệp')
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
                TextEntry::make('width')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('height')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('file_size')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('published_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
