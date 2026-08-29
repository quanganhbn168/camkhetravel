<?php

namespace App\Filament\Resources\LandingTemplates\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class LandingTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Nhận diện template')
                ->icon(Heroicon::OutlinedSwatch)
                ->description('Tên, mô tả và nguồn tham chiếu. Blade/CSS là mã nguồn được triển khai cùng website nên chỉ hiển thị để đối chiếu.')
                ->schema([
                    TextInput::make('name')
                        ->label('Tên template')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Textarea::make('description')
                        ->label('Mô tả')
                        ->rows(3)
                        ->maxLength(2000)
                        ->columnSpanFull(),
                    TextInput::make('use_case')
                        ->label('Cấu trúc / mục đích sử dụng')
                        ->maxLength(255)
                        ->columnSpanFull(),
                    TextInput::make('key')
                        ->label('Khóa template')
                        ->disabled()
                        ->dehydrated(false),
                    TextInput::make('source_name')
                        ->label('Nguồn thiết kế')
                        ->maxLength(255),
                    TextInput::make('source_path')
                        ->label('Đường dẫn nguồn tham chiếu')
                        ->disabled()
                        ->dehydrated(false)
                        ->columnSpanFull(),
                    TextInput::make('view_name')
                        ->label('Blade view')
                        ->disabled()
                        ->dehydrated(false),
                    TextInput::make('css_source')
                        ->label('CSS riêng')
                        ->disabled()
                        ->dehydrated(false),
                ])
                ->columns(2),
            Section::make('Bảng màu mặc định')
                ->icon(Heroicon::OutlinedPaintBrush)
                ->description('Khi chọn template, landing nhận bảng màu này và vẫn có thể đổi riêng từng trang.')
                ->schema([
                    ColorPicker::make('palette.primary')->label('Màu chủ đạo')->required(),
                    ColorPicker::make('palette.accent')->label('Màu nhấn')->required(),
                    ColorPicker::make('palette.surface')->label('Màu nền nhẹ')->required(),
                    ColorPicker::make('palette.ink')->label('Màu chữ chính')->required(),
                ])
                ->columns(4),
            Section::make('Schema quản trị của template')
                ->icon(Heroicon::OutlinedAdjustmentsHorizontal)
                ->description('Các trường dưới đây tự sinh thành một nhóm thiết lập riêng khi quản trị landing page.')
                ->schema([
                    TextInput::make('settings_schema.title')
                        ->label('Tiêu đề nhóm thiết lập')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Textarea::make('settings_schema.description')
                        ->label('Mô tả nhóm thiết lập')
                        ->rows(2)
                        ->maxLength(1000)
                        ->columnSpanFull(),
                    Select::make('settings_schema.icon')
                        ->label('Biểu tượng')
                        ->options([
                            'heroicon-o-sparkles' => 'Lấp lánh',
                            'heroicon-o-cursor-arrow-rays' => 'Chuyển đổi',
                            'heroicon-o-photo' => 'Hình ảnh',
                            'heroicon-o-video-camera' => 'Video',
                            'heroicon-o-camera' => 'Máy ảnh',
                            'heroicon-o-adjustments-horizontal' => 'Thiết lập',
                        ])
                        ->required()
                        ->native(false),
                    TextInput::make('settings_schema.columns')
                        ->label('Số cột')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(4)
                        ->default(2)
                        ->required(),
                    Repeater::make('settings_schema.fields')
                        ->label('Các trường tùy chỉnh')
                        ->schema([
                            TextInput::make('label')
                                ->label('Nhãn trường')
                                ->required()
                                ->maxLength(255)
                                ->columnSpanFull(),
                            TextInput::make('key')
                                ->label('Khóa dữ liệu')
                                ->required()
                                ->alphaDash()
                                ->maxLength(120),
                            Select::make('type')
                                ->label('Kiểu trường')
                                ->options([
                                    'text' => 'Một dòng',
                                    'textarea' => 'Nhiều dòng',
                                    'url' => 'Liên kết',
                                    'tags' => 'Danh sách thẻ',
                                    'media' => 'Media / video',
                                ])
                                ->required()
                                ->native(false)
                                ->live(),
                            TextInput::make('max_length')
                                ->label('Số ký tự tối đa')
                                ->numeric()
                                ->minValue(1),
                            TextInput::make('rows')
                                ->label('Số dòng')
                                ->numeric()
                                ->minValue(2)
                                ->maxValue(12)
                                ->visible(fn ($get): bool => $get('type') === 'textarea'),
                            TextInput::make('media_role')
                                ->label('Vai trò media')
                                ->maxLength(120)
                                ->visible(fn ($get): bool => $get('type') === 'media'),
                            Textarea::make('helper_text')
                                ->label('Hướng dẫn nhập')
                                ->rows(2)
                                ->maxLength(1000)
                                ->columnSpanFull(),
                            Toggle::make('full_width')
                                ->label('Hiển thị toàn chiều ngang')
                                ->columnSpanFull(),
                        ])
                        ->columns(2)
                        ->addActionLabel('Thêm trường')
                        ->reorderable()
                        ->cloneable()
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['label'] ?? 'Trường mới')
                        ->columnSpanFull(),
                    Placeholder::make('default_content_note')
                        ->label('Dữ liệu mặc định')
                        ->content('Nội dung mặc định và blueprint block được quản lý bằng seeder để đồng bộ an toàn với Blade/CSS. Nội dung từng landing vẫn chỉnh trực tiếp trong mục Landing page.')
                        ->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('Phạm vi sử dụng')
                ->icon(Heroicon::OutlinedEye)
                ->description('Tắt template sẽ ẩn khỏi danh sách chọn mới; các landing đã dùng vẫn tiếp tục render đúng giao diện.')
                ->schema([
                    TextInput::make('sort_order')
                        ->label('Thứ tự')
                        ->numeric()
                        ->minValue(0)
                        ->required(),
                    Toggle::make('is_active')
                        ->label('Cho phép chọn template')
                        ->default(true)
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }
}
