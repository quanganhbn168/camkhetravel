<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class TestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(['default' => 1, 'lg' => 3])->components([
            Section::make('Thông tin khách hàng')
                ->schema([
                    CuratorPicker::make('curator_media_id')
                        ->label('Ảnh đại diện / logo')
                        ->relationship('curatorMedia', 'id')
                        ->disk('public')
                        ->constrained()
                        ->acceptedFileTypes(['image/*']),
                    TextInput::make('client_name')->label('Họ tên khách hàng')->required()->maxLength(255),
                    TextInput::make('client_role')->label('Chức danh')->maxLength(255),
                    TextInput::make('company_name')->label('Doanh nghiệp')->maxLength(255),
                    TextInput::make('rating')->label('Số sao')->numeric()->minValue(1)->maxValue(5)->default(5),
                    Textarea::make('quote')->label('Nội dung phản hồi')->required()->rows(5)->columnSpanFull(),
                    Toggle::make('is_illustrative')->label('Nội dung minh họa')->helperText('Bật khi đây chưa phải phản hồi thực tế đã được xác nhận.'),
                ])
                ->columns(2)->columnSpan(['default' => 1, 'lg' => 2]),
            Section::make('Hiển thị')
                ->schema([
                    Toggle::make('is_active')->label('Hiển thị trên trang chủ')->default(true),
                ])->columnSpan(1),
        ]);
    }
}
