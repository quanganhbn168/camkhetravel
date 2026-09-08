<?php

namespace App\Filament\Forms;

use App\Support\Bni\BniMediaService;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

final class BniSeoImageField
{
    public static function make(): SpatieMediaLibraryFileUpload
    {
        return SpatieMediaLibraryFileUpload::make('seo_image')
            ->label('Ảnh chia sẻ riêng (Open Graph)')
            ->collection('seo_image')
            ->conversion(BniMediaService::WEBP_CONVERSION)
            ->disk('public')
            ->image()
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->helperText('Dùng khi chia sẻ liên kết. Để trống để dùng ảnh đại diện hoặc ảnh Open Graph mặc định. Gợi ý ảnh ngang 1200 × 630; không thay đổi banner trên trang.')
            ->columnSpanFull();
    }
}
