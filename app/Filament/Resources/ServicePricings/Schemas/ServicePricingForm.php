<?php

namespace App\Filament\Resources\ServicePricings\Schemas;

use App\Models\ServicePricing;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ServicePricingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(['lg' => 3])
            ->components([
                Section::make('Bảng giá của dịch vụ')
                    ->icon('heroicon-o-document-currency-dollar')
                    ->schema([
                        Select::make('service_id')
                            ->label('Dịch vụ')
                            ->relationship('service', 'title')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->unique(ignoreRecord: true)
                            ->default(fn (): ?int => request()->integer('service_id') ?: null)
                            ->columnSpanFull(),
                        TextInput::make('title')
                            ->label('Tên bảng giá')
                            ->required()
                            ->default('Bảng giá dịch vụ')
                            ->maxLength(255),
                        Textarea::make('description')
                            ->label('Mô tả bảng giá')
                            ->rows(3)
                            ->columnSpanFull(),
                        Repeater::make('packages')
                            ->label('Các gói trong bảng giá')
                            ->relationship('packages')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Tên gói')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                TextInput::make('badge')
                                    ->label('Nhãn nổi bật')
                                    ->maxLength(100)
                                    ->placeholder('Ví dụ: Phổ biến'),
                                TextInput::make('price_unit')
                                    ->label('Đơn vị giá')
                                    ->maxLength(100)
                                    ->placeholder('Ví dụ: / dự án'),
                                TextInput::make('list_price')
                                    ->label('Giá niêm yết')
                                    ->numeric()
                                    ->minValue(0)
                                    ->prefix('₫')
                                    ->helperText('Để trống nếu gói cần báo giá theo yêu cầu.'),
                                TextInput::make('price_label')
                                    ->label('Nhãn giá thay thế')
                                    ->maxLength(255)
                                    ->placeholder('Ví dụ: Liên hệ để nhận báo giá'),
                                Select::make('promotion_type')
                                    ->label('Khuyến mại')
                                    ->options([
                                        'fixed_price' => 'Giá khuyến mại cố định',
                                        'percent' => 'Giảm theo phần trăm',
                                    ])
                                    ->placeholder('Không áp dụng'),
                                TextInput::make('promotion_value')
                                    ->label(fn ($get): string => $get('promotion_type') === 'percent' ? 'Mức giảm (%)' : 'Giá khuyến mại')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(fn ($get): ?int => $get('promotion_type') === 'percent' ? 100 : null)
                                    ->prefix(fn ($get): string => $get('promotion_type') === 'percent' ? '%' : '₫')
                                    ->visible(fn ($get): bool => filled($get('promotion_type')))
                                    ->required(fn ($get): bool => filled($get('promotion_type'))),
                                Textarea::make('description')
                                    ->label('Mô tả gói')
                                    ->rows(2)
                                    ->columnSpanFull(),
                                Repeater::make('items')
                                    ->label('Các đầu mục của gói')
                                    ->relationship('items')
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Tên đầu mục')
                                            ->required()
                                            ->maxLength(255),
                                        Toggle::make('is_active')
                                            ->label('Hiển thị')
                                            ->default(true),
                                        Textarea::make('description')
                                            ->label('Mô tả đầu mục')
                                            ->rows(2)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2)
                                    ->addActionLabel('Thêm đầu mục')
                                    ->reorderable()
                                    ->cloneable()
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'Đầu mục mới')
                                    ->columnSpanFull(),
                                Toggle::make('is_featured')
                                    ->label('Gói nổi bật'),
                                Toggle::make('is_active')
                                    ->label('Hiển thị trên website')
                                    ->default(true),
                                TextInput::make('sort_order')
                                    ->label('Thứ tự')
                                    ->numeric()
                                    ->default(1),
                            ])
                            ->columns(2)
                            ->addActionLabel('Thêm gói giá')
                            ->reorderable()
                            ->cloneable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'Gói giá mới')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpan(['lg' => 2]),
                Section::make('Nguồn bảng giá')
                    ->icon('heroicon-o-paper-clip')
                    ->description('Có thể lưu ảnh/PDF hoặc một đường dẫn tham khảo. JSON dùng nút Nhập JSON ở đầu trang để tạo lại các gói và đầu mục.')
                    ->schema([
                        CuratorPicker::make('source_media_id')
                            ->label('Ảnh hoặc PDF bảng giá')
                            ->relationship('sourceMedia', 'id')
                            ->disk('public')
                            ->constrained()
                            ->acceptedFileTypes(['image/*', 'application/pdf'])
                            ->columnSpanFull(),
                        TextInput::make('source_url')
                            ->label('Link bảng giá')
                            ->url()
                            ->maxLength(1000)
                            ->columnSpanFull(),
                        Textarea::make('source_json')
                            ->label('JSON đã nhập gần nhất')
                            ->rows(12)
                            ->readOnly()
                            ->dehydrated(false)
                            ->placeholder('Chưa có JSON được nhập.')
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->columnSpan(['lg' => 1]),
            ]);
    }
}
