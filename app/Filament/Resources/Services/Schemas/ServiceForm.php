<?php

namespace App\Filament\Resources\Services\Schemas;

use App\Filament\Forms\SeoFields;
use App\Filament\RichEditor\ScopedAttachCuratorMediaPlugin;
use App\Models\Service;
use App\Support\Seo\ContentSeoFallbacks;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(['lg' => 3])
            ->components([
                Group::make([
                    Section::make('1-2. Banner & giới thiệu dịch vụ')

                        ->schema([
                            TextInput::make('title')
                                ->label('Tên dịch vụ')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (?string $state, $get, $set): void {
                                    if (blank($get('slug')) && ($slug = ContentSeoFallbacks::slug($state))) {
                                        $set('slug', $slug);
                                    }
                                })
                                ->columnSpanFull(),
                            TextInput::make('slug')
                                ->label('Đường dẫn (slug)')
                                ->maxLength(255)
                                ->formatStateUsing(fn (?string $state, ?Service $record): ?string => $state ?: $record?->slug)
                                ->columnSpanFull(),
                            CuratorPicker::make('curator_media_id')->label('Ảnh đại diện')->relationship('curatorMedia', 'id')->disk('public')->constrained()->acceptedFileTypes(['image/*'])->columnSpanFull(),
                            CuratorPicker::make('banner_video_media_id')->label('Video banner dịch vụ')->relationship('bannerVideoMedia', 'id')->disk('public')->constrained()->acceptedFileTypes(['video/*'])->helperText('Video được phát nền ở banner; ảnh đại diện sẽ làm poster khi cần.')->columnSpanFull(),
                            CuratorPicker::make('gallery')->label('Ảnh thực tế (ảnh xe, chuyến đi)')->multiple()->disk('public')->constrained()->acceptedFileTypes(['image/*'])->helperText('Hai ảnh đầu hiện cạnh phần Giới thiệu. Ưu tiên ảnh xe thực tế.')->columnSpanFull(),
                            Textarea::make('excerpt')
                                ->label('Mô tả ngắn (hiện ở phần Giới thiệu, cạnh ảnh)')
                                ->rows(3)
                                ->columnSpanFull(),
                            RichEditor::make('body')
                                ->label('Nội dung giới thiệu')
                                ->helperText('Nội dung chi tiết, hiển thị full width ngay dưới phần Giới thiệu.')
                                ->plugins([ScopedAttachCuratorMediaPlugin::make()])
                                ->toolbarButtons([
                                    ['bold', 'italic', 'underline', 'strike', 'link'],
                                    ['attachCuratorMedia', 'h2', 'h3'],
                                    ['alignStart', 'alignCenter', 'alignEnd'],
                                    ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                                    ['table'],
                                    ['undo', 'redo'],
                                ])
                                ->resizableImages()
                                ->columnSpanFull(),
                        ])
                        ->columns(2),
                    Section::make('3. Vì sao chọn dịch vụ')
                        ->description('Các lý do khách nên chọn dịch vụ.'.' Để trống cả phần thì trang dùng "Mẫu trang dịch vụ" (menu Dịch vụ & giải pháp).')
                        ->schema([
                            TextInput::make('benefit_title')->label('Tiêu đề')->placeholder('Để trống: dùng tiêu đề trong mẫu')->maxLength(255)->columnSpanFull(),
                            Textarea::make('benefit_description')->label('Mô tả')->rows(2)->columnSpanFull(),
                            Repeater::make('benefit_items')
                                ->label('Các lý do')
                                ->schema([
                                    TextInput::make('title')->label('Tiêu đề')->required()->maxLength(255)->columnSpanFull(),
                                    Textarea::make('description')->label('Mô tả')->rows(3)->columnSpanFull(),
                                ])
                                ->addActionLabel('Thêm lý do')
                                ->reorderable()
                                ->collapsible()
                                ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Lý do mới')
                                ->columnSpanFull(),
                        ])
                        ->columns(2),
                    Section::make('4. Các nhu cầu đặt xe')
                        ->description('Ví dụ: đi bệnh viện, sân bay, công tác, du lịch.')
                        ->schema([
                            TextInput::make('usecase_title')->label('Tiêu đề')->placeholder('Các nhu cầu đặt xe')->maxLength(255)->columnSpanFull(),
                            Textarea::make('usecase_description')->label('Mô tả')->rows(2)->columnSpanFull(),
                            Repeater::make('usecase_items')
                                ->label('Nhu cầu')
                                ->schema([
                                    TextInput::make('title')->label('Tiêu đề')->required()->maxLength(255)->columnSpanFull(),
                                    CuratorPicker::make('media_id')->label('Ảnh minh họa (không bắt buộc)')->disk('public')->constrained()->acceptedFileTypes(['image/*'])->columnSpanFull(),
                                    Textarea::make('description')->label('Mô tả')->rows(3)->columnSpanFull(),
                                ])
                                ->columns(2)
                                ->addActionLabel('Thêm nhu cầu')
                                ->reorderable()
                                ->collapsible()
                                ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Nhu cầu mới')
                                ->columnSpanFull(),
                        ])
                        ->columns(2),
                    Section::make('5. Các dòng xe phục vụ')
                        ->description('Chỉ liệt kê loại xe thực sự cung cấp.'.' Để trống cả phần thì trang dùng "Mẫu trang dịch vụ" (menu Dịch vụ & giải pháp).')
                        ->schema([
                            TextInput::make('vehicle_title')->label('Tiêu đề')->placeholder('Để trống: dùng tiêu đề trong mẫu')->maxLength(255)->columnSpanFull(),
                            Textarea::make('vehicle_description')->label('Mô tả')->rows(2)->columnSpanFull(),
                            Repeater::make('vehicle_items')
                                ->label('Dòng xe')
                                ->schema([
                                    TextInput::make('title')->label('Tên xe')->required()->maxLength(255),
                                    TextInput::make('seats')->label('Số chỗ')->placeholder('4 chỗ')->maxLength(64),
                                    CuratorPicker::make('media_id')->label('Ảnh xe')->helperText('Ưu tiên ảnh xe thực tế.')->disk('public')->constrained()->acceptedFileTypes(['image/*'])->columnSpanFull(),
                                    Textarea::make('description')->label('Mô tả')->rows(3)->columnSpanFull(),
                                ])
                                ->columns(2)
                                ->addActionLabel('Thêm dòng xe')
                                ->reorderable()
                                ->collapsible()
                                ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Dòng xe mới')
                                ->columnSpanFull(),
                        ])
                        ->columns(2),
                    Section::make('6. Khu vực nhận đón khách')
                        ->description('Nêu rõ địa bàn đón khách, ví dụ xã Cẩm Khê, Phú Thọ.')
                        ->schema([
                            TextInput::make('area_title')->label('Tiêu đề')->placeholder('Khu vực nhận đón khách')->maxLength(255)->columnSpanFull(),
                            Textarea::make('area_description')->label('Mô tả')->rows(3)->columnSpanFull(),
                            Repeater::make('area_items')
                                ->label('Địa điểm / khu vực')
                                ->schema([
                                    TextInput::make('title')->label('Tên khu vực')->required()->maxLength(255),
                                    TextInput::make('description')->label('Ghi chú ngắn')->maxLength(255),
                                ])
                                ->columns(2)
                                ->addActionLabel('Thêm khu vực')
                                ->reorderable()
                                ->collapsible()
                                ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Khu vực mới')
                                ->columnSpanFull(),
                        ])
                        ->columns(2),
                    Section::make('7. Quy trình đặt xe')
                        ->description('Các bước đặt xe. Để trống cả phần thì trang dùng "Mẫu trang dịch vụ" (menu Dịch vụ & giải pháp).')
                        ->schema([
                            TextInput::make('process_title')->label('Tiêu đề')->placeholder('Để trống: dùng tiêu đề trong mẫu')->maxLength(255)->columnSpanFull(),
                            Textarea::make('process_description')->label('Mô tả')->rows(2)->columnSpanFull(),
                            CuratorPicker::make('process_background_media_id')->label('Ảnh nền quy trình')->relationship('processBackgroundMedia', 'id')->disk('public')->constrained()->acceptedFileTypes(['image/*'])->helperText('Ảnh nền cho toàn bộ section quy trình.')->columnSpanFull(),
                            Repeater::make('process_items')
                                ->label('Các bước')
                                ->schema([
                                    TextInput::make('step')->label('Số bước')->numeric()->minValue(1),
                                    TextInput::make('title')->label('Tiêu đề')->required()->maxLength(255),
                                    Textarea::make('description')->label('Mô tả')->rows(3)->columnSpanFull(),
                                ])
                                ->columns(2)
                                ->addActionLabel('Thêm bước')
                                ->reorderable()
                                ->collapsible()
                                ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Bước mới')
                                ->columnSpanFull(),
                        ])
                        ->columns(2),
                    Section::make('8. Bảng giá / báo giá')
                        ->description('Giá tham khảo. Chỉ điền giá đã được xác nhận; hành trình chưa có giá để trống cột giá sẽ hiện "Liên hệ".')
                        ->schema([
                            TextInput::make('pricing_title')->label('Tiêu đề')->placeholder('Bảng giá tham khảo')->maxLength(255)->columnSpanFull(),
                            Textarea::make('pricing_description')->label('Mô tả')->rows(2)->columnSpanFull(),
                            Repeater::make('pricing_items')
                                ->label('Hành trình')
                                ->schema([
                                    TextInput::make('route')->label('Hành trình')->placeholder('Cẩm Khê → Sân bay Nội Bài')->required()->maxLength(255),
                                    TextInput::make('vehicle')->label('Loại xe')->placeholder('4 chỗ')->maxLength(255),
                                    TextInput::make('price')->label('Giá tham khảo')->placeholder('từ 900.000đ')->maxLength(255),
                                ])
                                ->columns(3)
                                ->addActionLabel('Thêm hành trình')
                                ->reorderable()
                                ->collapsible()
                                ->itemLabel(fn (array $state): ?string => $state['route'] ?? 'Hành trình mới')
                                ->columnSpanFull(),
                            Textarea::make('pricing_note')->label('Ghi chú dưới bảng giá')->rows(2)->placeholder('Giá có thể thay đổi theo thời điểm, điểm đón và số khách.')->columnSpanFull(),
                        ])
                        ->columns(2),
                    Section::make('SEO')

                        ->schema([
                            ...SeoFields::make(prefill: false),
                        ])
                        ->columns(2),
                ])->columnSpan(['lg' => 2]),
                Section::make('Phân loại & hiển thị')

                    ->schema([
                        Select::make('service_category_id')->label('Danh mục dịch vụ')->relationship('category', 'name')->searchable()->preload(),
                        Select::make('tags')
                            ->label('Thẻ')
                            ->relationship('tags', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')->label('Tên thẻ')->required(),
                            ]),
                        Select::make('status')->label('Trạng thái')->options(['draft' => 'Bản nháp', 'published' => 'Đã xuất bản', 'pending' => 'Chờ duyệt', 'private' => 'Riêng tư'])->required()->default('draft'),
                        TextInput::make('sort_order')->label('Thứ tự')->numeric()->default(fn (): int => ((int) Service::query()->max('sort_order')) + 1),
                        Toggle::make('is_featured')->label('Dịch vụ nổi bật'),
                        Toggle::make('is_home')->label('Hiển thị trang chủ'),
                        Select::make('quote_type')
                            ->label('Loại yêu cầu khi bấm tư vấn')
                            ->options(['trip' => 'Chuyến đi riêng', 'partner' => 'Hợp tác doanh nghiệp', 'wedding' => 'Xe cưới', 'shared' => 'Xe ghép'])
                            ->default('trip')
                            ->required()
                            ->helperText('Form báo giá mở sẵn đúng loại này khi khách bấm nút tư vấn trên thẻ dịch vụ ở trang chủ.'),
                    ])
                    ->columns(1)
                    ->columnSpan(['lg' => 1]),
            ]);
    }
}
