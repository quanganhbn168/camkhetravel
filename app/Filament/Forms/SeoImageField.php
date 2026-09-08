<?php

namespace App\Filament\Forms;

use Awcodes\Curator\Components\Forms\CuratorPicker;

final class SeoImageField
{
    public static function make(): CuratorPicker
    {
        return CuratorPicker::make('seo_image_media_id')
            ->label('Ảnh chia sẻ riêng (Open Graph)')
            ->relationship('seoImageMedia', 'id')
            ->disk('public')
            ->constrained()
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->helperText('Dùng khi chia sẻ liên kết Facebook, Zalo… Để trống để dùng ảnh đại diện hoặc ảnh Open Graph mặc định của website. Gợi ý ảnh ngang 1200 × 630; không thay đổi ảnh hiển thị trên trang.')
            ->columnSpanFull();
    }
}
