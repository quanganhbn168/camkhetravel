<?php

namespace App\Filament\Resources\Partners\Schemas;

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Đối tác chỉ cần logo.
 * - Thêm mới: chọn/tải nhiều logo một lần, mỗi logo thành một đối tác (xem CreatePartner).
 * - Sửa: một logo; tên và website không bắt buộc.
 */
final class PartnerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Tải logo đối tác')
                ->description('Kéo thả hoặc chọn nhiều logo cùng lúc. Mỗi logo sẽ trở thành một đối tác. Tên và website không bắt buộc, có thể bổ sung sau khi sửa từng đối tác.')
                ->visible(fn (string $operation): bool => $operation === 'create')
                ->schema([
                    CuratorPicker::make('logos')
                        ->label('Logo')
                        ->multiple()
                        ->required()
                        ->disk('public')
                        ->constrained()
                        ->acceptedFileTypes(['image/*'])
                        ->columnSpanFull(),
                ]),
            Section::make('Đối tác')
                ->visible(fn (string $operation): bool => $operation !== 'create')
                ->schema([
                    CuratorPicker::make('curator_media_id')
                        ->label('Logo')
                        ->relationship('curatorMedia', 'id')
                        ->disk('public')
                        ->constrained()
                        ->acceptedFileTypes(['image/*']),
                    TextInput::make('name')->label('Tên đối tác (tuỳ chọn)')->maxLength(255)
                        ->helperText('Không bắt buộc. Nếu nhập, tên được dùng làm chữ thay thế (alt) và gợi ý khi rê chuột vào logo.'),
                    TextInput::make('website_url')->label('Website (tuỳ chọn)')->url()->maxLength(255),
                    Toggle::make('is_active')->label('Hiển thị trên trang chủ')->default(true),
                ])
                ->columns(2),
        ]);
    }
}
