<?php

namespace App\Filament\Resources\HeroSlides\Schemas;

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class HeroSlideForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(['default' => 1, 'lg' => 3])->components([
            Section::make('Nội dung')

                ->schema([
                    CuratorPicker::make('curator_media_id')
                        ->label('Ảnh nền')
                        ->relationship('curatorMedia', 'id')
                        ->disk('public')
                        ->constrained()
                        ->acceptedFileTypes(['image/*'])
                        ->columnSpanFull(),
                    TextInput::make('title')
                        ->label('Tiêu đề')
                        ->helperText('Để trống toàn bộ phần nội dung và nút nếu slide chỉ hiển thị ảnh.')
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Textarea::make('description')->label('Mô tả')->rows(3)->columnSpanFull(),
                    TextInput::make('primary_label')->label('Nhãn nút chính')->maxLength(255),
                    TextInput::make('primary_url')->label('URL nút chính')->maxLength(255)->rules(['nullable', 'regex:/^(?:\/(?!\/)[^\s]*|https?:\/\/[^\s]+)$/']),
                    TextInput::make('secondary_label')->label('Nhãn nút phụ')->maxLength(255),
                    TextInput::make('secondary_url')->label('URL nút phụ')->maxLength(255)->rules(['nullable', 'regex:/^(?:\/(?!\/)[^\s]*|https?:\/\/[^\s]+)$/']),
                ])
                ->columns(2)->columnSpan(['default' => 1, 'lg' => 2]),
            Section::make('Hiển thị')

                ->schema([
                    Toggle::make('is_active')->label('Hiển thị trên trang chủ')->default(true),
                ])->columnSpan(1),
        ]);
    }
}
