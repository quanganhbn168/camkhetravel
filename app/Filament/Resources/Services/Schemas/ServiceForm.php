<?php

namespace App\Filament\Resources\Services\Schemas;

use App\Filament\Forms\SeoFields;
use App\Filament\RichEditor\ScopedAttachCuratorMediaPlugin;
use App\Models\Service;
use App\Support\Seo\ContentSeoFallbacks;
use App\Support\Services\ServiceIcons;
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

/**
 * Form dịch vụ: các nhóm xếp đúng thứ tự xuất hiện trên trang chi tiết dịch vụ.
 * Nhóm có ghi "dùng mẫu" thì để trống sẽ lấy từ Dịch vụ & giải pháp → Mẫu trang dịch vụ.
 */
final class ServiceForm
{
    private const TEMPLATE_NOTE = ' Để trống cả phần thì trang dùng "Mẫu trang dịch vụ" (menu Dịch vụ & giải pháp).';

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(['lg' => 3])
            ->components([
                Group::make([
                    self::bannerSection(),
                    self::packageSection(),
                    self::pricingSection(),
                    self::benefitSection(),
                    self::usecaseSection(),
                    self::vehicleSection(),
                    self::areaSection(),
                    self::processSection(),
                    self::contentSection(),
                    Section::make('10. Câu hỏi thường gặp')
                        ->description('Quản lý ở menu Nội dung → Câu hỏi thường gặp: tạo câu hỏi và gắn vào dịch vụ này.')
                        ->collapsed(),
                    Section::make('SEO')
                        ->schema([...SeoFields::make(prefill: false)])
                        ->columns(2)
                        ->collapsed(),
                ])->columnSpan(['lg' => 2]),
                self::sidebarSection(),
            ]);
    }

    private static function bannerSection(): Section
    {
        return Section::make('1. Banner đầu trang')
            ->description('Chữ bên trái, ảnh (hoặc video) làm nền bên phải.')
            ->schema([
                TextInput::make('title')
                    ->label('Tên dịch vụ (H1)')
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
                TextInput::make('hero_badge')->label('Nhãn nhỏ phía trên tiêu đề')->placeholder('Dịch vụ vận chuyển tại Phú Thọ')->helperText('Để trống: dùng tên danh mục dịch vụ.')->maxLength(120),
                TextInput::make('hero_subtitle')->label('Câu phụ dưới tiêu đề')->placeholder('Đặt xe nhanh – Đón tận nơi – Đi mọi hành trình')->helperText('Để trống: dùng mô tả ngắn.')->maxLength(255),
                Repeater::make('hero_points')
                    ->label('Các ý chính (dấu tích)')
                    ->helperText('Nên 3–4 ý. Để trống: lấy 3 lý do đầu trong "Vì sao chọn".')
                    ->simple(TextInput::make('text')->required()->maxLength(160))
                    ->addActionLabel('Thêm ý')
                    ->reorderable()
                    ->columnSpanFull(),
                CuratorPicker::make('curator_media_id')->label('Ảnh banner / ảnh đại diện')->helperText('Ảnh ngang, chủ thể (xe) nằm bên phải. Cũng dùng làm ảnh đại diện của dịch vụ.')->relationship('curatorMedia', 'id')->disk('public')->constrained()->acceptedFileTypes(['image/*'])->columnSpanFull(),
                CuratorPicker::make('banner_video_media_id')->label('Video banner (không bắt buộc)')->relationship('bannerVideoMedia', 'id')->disk('public')->constrained()->acceptedFileTypes(['video/*'])->helperText('Có video thì phát nền thay ảnh; ảnh banner làm poster.')->columnSpanFull(),
            ])
            ->columns(2);
    }

    private static function packageSection(): Section
    {
        return Section::make('2. Gói dịch vụ & giá "Từ ..."')
            ->description('Các thẻ gói ngay dưới banner, ví dụ: Ghép xe tiết kiệm – Từ 150K/người. Chỉ ghi giá đã xác nhận.')
            ->schema([
                TextInput::make('package_title')->label('Tiêu đề')->placeholder('Tổng quan dịch vụ & giá')->maxLength(255)->columnSpanFull(),
                Textarea::make('package_description')->label('Mô tả')->rows(2)->columnSpanFull(),
                Repeater::make('package_items')
                    ->label('Gói')
                    ->schema([
                        TextInput::make('title')->label('Tên gói')->required()->maxLength(120),
                        Select::make('icon')->label('Icon')->options(ServiceIcons::options())->searchable(),
                        TextInput::make('price')->label('Giá')->placeholder('Từ 150K')->maxLength(60),
                        TextInput::make('unit')->label('Đơn vị')->placeholder('/người/lượt')->maxLength(60),
                        Textarea::make('description')->label('Mô tả')->rows(2)->columnSpanFull(),
                        TextInput::make('button_label')->label('Chữ trên nút')->placeholder('Nhận báo giá')->maxLength(60)->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->addActionLabel('Thêm gói')
                    ->reorderable()
                    ->collapsible()
                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Gói mới')
                    ->columnSpanFull(),
            ])
            ->columns(2)
            ->collapsible();
    }

    private static function pricingSection(): Section
    {
        return Section::make('3. Bảng giá theo điểm đến')
            ->description('Mỗi điểm đến là một thẻ (Đi Việt Trì, Đi Hà Nội...), bên trong chia dòng theo bao xe / ghép xe. Giá để trống sẽ hiện "Liên hệ".')
            ->schema([
                TextInput::make('pricing_title')->label('Tiêu đề')->placeholder('Giá đặt xe đi các địa điểm')->maxLength(255)->columnSpanFull(),
                Textarea::make('pricing_description')->label('Mô tả')->rows(2)->columnSpanFull(),
                Repeater::make('pricing_groups')
                    ->label('Điểm đến')
                    ->schema([
                        TextInput::make('destination')->label('Điểm đến')->placeholder('Đi Việt Trì')->required()->maxLength(120)->columnSpanFull(),
                        Repeater::make('rows')
                            ->label('Các dòng giá')
                            ->schema([
                                Select::make('kind')->label('Hình thức')->options(ServiceIcons::pricingKinds())->default('private')->required(),
                                TextInput::make('label')->label('Nội dung')->placeholder('Xe 4 chỗ – 1 chiều')->required()->maxLength(160),
                                TextInput::make('price')->label('Giá')->placeholder('350.000đ – 400.000đ')->maxLength(120),
                            ])
                            ->columns(3)
                            ->addActionLabel('Thêm dòng giá')
                            ->reorderable()
                            ->columnSpanFull(),
                    ])
                    ->addActionLabel('Thêm điểm đến')
                    ->reorderable()
                    ->collapsible()
                    ->itemLabel(fn (array $state): ?string => $state['destination'] ?? 'Điểm đến mới')
                    ->columnSpanFull(),
                Textarea::make('pricing_note')->label('Ghi chú dưới bảng giá')->rows(2)->placeholder('Chi phí thực tế được tư vấn dựa trên quãng đường, loại xe và lịch trình.')->columnSpanFull(),
            ])
            ->columns(2)
            ->collapsible();
    }

    private static function benefitSection(): Section
    {
        return Section::make('4. Vì sao chọn (dùng mẫu)')
            ->description('Các lý do khách nên chọn dịch vụ.'.self::TEMPLATE_NOTE)
            ->schema([
                TextInput::make('benefit_title')->label('Tiêu đề')->placeholder('Để trống: dùng tiêu đề trong mẫu')->maxLength(255)->columnSpanFull(),
                Textarea::make('benefit_description')->label('Mô tả')->rows(2)->columnSpanFull(),
                Repeater::make('benefit_items')
                    ->label('Các lý do')
                    ->schema([
                        TextInput::make('title')->label('Tiêu đề')->required()->maxLength(255),
                        Select::make('icon')->label('Icon')->options(ServiceIcons::options())->searchable()->placeholder('Tự chọn theo thứ tự'),
                        Textarea::make('description')->label('Mô tả')->rows(2)->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->addActionLabel('Thêm lý do')
                    ->reorderable()
                    ->collapsible()
                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Lý do mới')
                    ->columnSpanFull(),
            ])
            ->columns(2)
            ->collapsible();
    }

    private static function usecaseSection(): Section
    {
        return Section::make('5. Các nhu cầu đặt xe')
            ->description('Thẻ có ảnh: đi bệnh viện, sân bay, công tác, du lịch... Nên 6 thẻ cho đẹp hàng.')
            ->schema([
                TextInput::make('usecase_title')->label('Tiêu đề')->placeholder('Các nhu cầu đặt xe')->maxLength(255)->columnSpanFull(),
                Textarea::make('usecase_description')->label('Mô tả')->rows(2)->columnSpanFull(),
                Repeater::make('usecase_items')
                    ->label('Nhu cầu')
                    ->schema([
                        TextInput::make('title')->label('Tiêu đề')->required()->maxLength(255),
                        Select::make('icon')->label('Icon')->options(ServiceIcons::options())->searchable(),
                        CuratorPicker::make('media_id')->label('Ảnh minh họa')->disk('public')->constrained()->acceptedFileTypes(['image/*'])->columnSpanFull(),
                        Textarea::make('description')->label('Mô tả')->rows(2)->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->addActionLabel('Thêm nhu cầu')
                    ->reorderable()
                    ->collapsible()
                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Nhu cầu mới')
                    ->columnSpanFull(),
            ])
            ->columns(2)
            ->collapsible();
    }

    private static function vehicleSection(): Section
    {
        return Section::make('6. Các dòng xe phục vụ (dùng mẫu)')
            ->description('Chỉ liệt kê loại xe thực sự cung cấp.'.self::TEMPLATE_NOTE)
            ->schema([
                TextInput::make('vehicle_title')->label('Tiêu đề')->placeholder('Để trống: dùng tiêu đề trong mẫu')->maxLength(255)->columnSpanFull(),
                Textarea::make('vehicle_description')->label('Mô tả')->rows(2)->columnSpanFull(),
                Repeater::make('vehicle_items')
                    ->label('Dòng xe')
                    ->schema([
                        TextInput::make('title')->label('Tên xe')->required()->maxLength(255),
                        TextInput::make('seats')->label('Số chỗ')->placeholder('4 chỗ')->maxLength(64),
                        CuratorPicker::make('media_id')->label('Ảnh xe')->helperText('Ảnh xe nền trắng/trong suốt là đẹp nhất.')->disk('public')->constrained()->acceptedFileTypes(['image/*'])->columnSpanFull(),
                        Textarea::make('description')->label('Mô tả ngắn')->rows(2)->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->addActionLabel('Thêm dòng xe')
                    ->reorderable()
                    ->collapsible()
                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Dòng xe mới')
                    ->columnSpanFull(),
            ])
            ->columns(2)
            ->collapsible();
    }

    private static function areaSection(): Section
    {
        return Section::make('7. Khu vực nhận đón khách')
            ->description('Nêu rõ địa bàn đón khách, ví dụ xã Cẩm Khê, Phú Thọ.')
            ->schema([
                TextInput::make('area_title')->label('Tiêu đề')->placeholder('Khu vực nhận đón khách')->maxLength(255)->columnSpanFull(),
                Textarea::make('area_description')->label('Mô tả')->rows(3)->columnSpanFull(),
                CuratorPicker::make('area_media_id')->label('Ảnh khu vực (không bắt buộc)')->disk('public')->constrained()->acceptedFileTypes(['image/*'])->columnSpanFull(),
                Repeater::make('area_items')
                    ->label('Điểm đón / khu vực')
                    ->schema([
                        TextInput::make('title')->label('Nội dung')->placeholder('Đón tại chợ, trường học, cơ quan')->required()->maxLength(255)->columnSpanFull(),
                    ])
                    ->addActionLabel('Thêm khu vực')
                    ->reorderable()
                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Khu vực mới')
                    ->columnSpanFull(),
            ])
            ->columns(2)
            ->collapsible();
    }

    private static function processSection(): Section
    {
        return Section::make('8. Quy trình đặt xe (dùng mẫu)')
            ->description('Các bước đặt xe, tự đánh số theo thứ tự.'.self::TEMPLATE_NOTE)
            ->schema([
                TextInput::make('process_title')->label('Tiêu đề')->placeholder('Để trống: dùng tiêu đề trong mẫu')->maxLength(255)->columnSpanFull(),
                Textarea::make('process_description')->label('Mô tả')->rows(2)->columnSpanFull(),
                Repeater::make('process_items')
                    ->label('Các bước')
                    ->schema([
                        TextInput::make('title')->label('Tiêu đề')->required()->maxLength(255),
                        Select::make('icon')->label('Icon')->options(ServiceIcons::options())->searchable()->placeholder('Tự chọn theo thứ tự'),
                        Textarea::make('description')->label('Mô tả')->rows(2)->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->addActionLabel('Thêm bước')
                    ->reorderable()
                    ->collapsible()
                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Bước mới')
                    ->columnSpanFull(),
            ])
            ->columns(2)
            ->collapsible();
    }

    private static function contentSection(): Section
    {
        return Section::make('9. Giới thiệu & nội dung chi tiết (SEO)')
            ->description('Hiển thị sau phần Quy trình & FAQ. Chia bài bằng H2/H3, trang tự tạo mục lục.')
            ->schema([
                Textarea::make('excerpt')
                    ->label('Mô tả ngắn')
                    ->helperText('Đoạn mở đầu phần giới thiệu; cũng dùng cho thẻ dịch vụ và meta description khi để trống SEO.')
                    ->rows(3)
                    ->columnSpanFull(),
                RichEditor::make('body')
                    ->label('Nội dung chi tiết')
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
            ->columns(2)
            ->collapsible();
    }

    private static function sidebarSection(): Section
    {
        return Section::make('Phân loại & hiển thị')
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
            ->columnSpan(['lg' => 1]);
    }
}
