<?php

namespace App\Filament\Forms;

use App\Support\Seo\ContentSeoFallbacks;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;

final class SeoFields
{
    /** @return array<int, mixed> */
    public static function make(string $titleSource = 'title', string $descriptionSource = 'excerpt'): array
    {
        return [
            TextInput::make('seo_title')
                ->label('SEO title')
                ->maxLength(255)
                ->formatStateUsing(fn (?string $state, ?Model $record): ?string => $state ?: ContentSeoFallbacks::title($record?->getAttribute($titleSource)))
                ->helperText('Tiêu đề dùng cho kết quả tìm kiếm và khi chia sẻ liên kết.')
                ->columnSpanFull(),
            Textarea::make('seo_description')
                ->label('Meta description')
                ->rows(4)
                ->maxLength(160)
                ->formatStateUsing(fn (?string $state, ?Model $record): ?string => $state ?: ContentSeoFallbacks::description($record?->getAttribute($descriptionSource)))
                ->helperText('Mô tả ngắn tối đa khoảng 160 ký tự.')
                ->columnSpanFull(),
            SeoImageField::make(),
        ];
    }
}
