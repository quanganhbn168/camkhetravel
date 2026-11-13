<?php

namespace App\Filament\Forms;

use App\Support\Seo\ContentSeoFallbacks;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;

final class SeoFields
{
    /** @return array<int, mixed> */
    public static function make(string $titleSource = 'title', string $descriptionSource = 'excerpt', bool $prefill = true): array
    {
        return [
            TextInput::make('seo_title')
                ->label('SEO title')
                ->maxLength(255)
                ->formatStateUsing($prefill ? fn (?string $state, ?Model $record): ?string => $state ?: ContentSeoFallbacks::title($record?->getAttribute($titleSource)) : null)
                ->helperText('Tiêu đề dùng cho kết quả tìm kiếm và khi chia sẻ liên kết.'.($prefill ? '' : ' Để trống thì khi lưu sẽ tự lấy từ tiêu đề.'))
                ->columnSpanFull(),
            Textarea::make('seo_description')
                ->label('Meta description')
                ->rows(4)
                ->maxLength(160)
                ->formatStateUsing($prefill ? fn (?string $state, ?Model $record): ?string => $state ?: ContentSeoFallbacks::description($record?->getAttribute($descriptionSource)) : null)
                ->helperText('Mô tả ngắn tối đa khoảng 160 ký tự.'.($prefill ? '' : ' Để trống thì khi lưu sẽ tự lấy từ mô tả ngắn.'))
                ->columnSpanFull(),
            SeoImageField::make(),
        ];
    }
}
