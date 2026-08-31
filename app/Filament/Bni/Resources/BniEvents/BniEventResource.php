<?php

namespace App\Filament\Bni\Resources\BniEvents;

use App\Filament\Bni\Resources\BniEvents\Pages\ManageBniEvents;
use App\Models\BniEvent;
use App\Support\Bni\BniPanelAccess;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BniEventResource extends Resource
{
    protected static ?string $model = BniEvent::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Sự kiện';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return 'Lễ chuyển giao';
    }

    public static function getModelLabel(): string
    {
        return 'sự kiện';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Sự kiện';
    }

    public static function canViewAny(): bool
    {
        return BniPanelAccess::canManageEverything();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Sự kiện BNI')->tabs([
                Tab::make('Thông tin chung')->schema([
                    Section::make('Nội dung sự kiện')->icon('heroicon-o-document-text')->schema([
                        TextInput::make('title')->label('Tên sự kiện')->required()->maxLength(255)->columnSpanFull(),
                        TextInput::make('slug')->label('Slug')->required()->maxLength(255)->columnSpanFull(),
                        Select::make('type')->label('Loại')->options(['handover' => 'Lễ chuyển giao', 'pickleball' => 'Pickleball'])->required()->live()->columnSpanFull(),
                        TextInput::make('kicker')->label('Dòng nhãn')->maxLength(255)->columnSpanFull(),
                        Select::make('status')->label('Trạng thái')->options(['draft' => 'Bản nháp', 'published' => 'Đã xuất bản'])->required()->columnSpanFull(),
                        CuratorPicker::make('hero_media_id')->label('Ảnh banner')->relationship('heroMedia', 'id')->disk('public')->constrained()->acceptedFileTypes(['image/*'])->columnSpanFull(),
                        Textarea::make('summary')->label('Mô tả ngắn')->rows(3)->columnSpanFull(),
                        RichEditor::make('content')->label('Nội dung')->columnSpanFull(),
                    ])->columns(2),
                    Section::make('Thời gian & liên hệ')->icon('heroicon-o-clock')->description('Đầu mối ở đây chỉ dành cho trang sự kiện. Mỗi thư mời sẽ dùng đầu mối riêng của chapter phụ trách.')->schema([
                        DateTimePicker::make('starts_at')->label('Bắt đầu')->columnSpanFull(),
                        DateTimePicker::make('ends_at')->label('Kết thúc')->columnSpanFull(),
                        TextInput::make('venue')->label('Địa điểm')->columnSpanFull(),
                        TextInput::make('address')->label('Địa chỉ')->columnSpanFull(),
                        TextInput::make('directions_url')->label('Link chỉ đường')->url()->maxLength(2048)->helperText('Dán link Google Maps hoặc bản đồ của địa điểm sự kiện.')->columnSpanFull(),
                        TextInput::make('contact_name')->label('Đầu mối trang sự kiện')->columnSpanFull(),
                        TextInput::make('contact_phone')->label('Số điện thoại')->tel()->columnSpanFull(),
                        TextInput::make('contact_email')->label('Email')->email()->columnSpanFull(),
                        Toggle::make('is_featured')->label('Sự kiện nổi bật')->columnSpanFull(),
                    ])->columns(2),
                ]),
                Tab::make('Slide')
                    ->visible(fn ($get): bool => $get('type') === 'handover')
                    ->schema([
                        Section::make('Slide đầu trang BNI')
                            ->icon('heroicon-o-photo')
                            ->description('Mỗi slide bắt buộc có ảnh. Phần chữ là tùy chọn và luôn nằm tách khỏi ảnh, không phủ overlay lên ảnh.')
                            ->schema([
                                Repeater::make('slides')
                                    ->relationship('slides')
                                    ->label('Danh sách slide')
                                    ->schema([
                                        CuratorPicker::make('media_id')
                                            ->label('Ảnh slide')
                                            ->relationship('media', 'id')
                                            ->disk('public')
                                            ->constrained()
                                            ->acceptedFileTypes(['image/*'])
                                            ->required()
                                            ->columnSpanFull(),
                                        TextInput::make('title')
                                            ->label('Tiêu đề (không bắt buộc)')
                                            ->maxLength(255)
                                            ->helperText('Tiêu đề được hiển thị bằng H2, không dùng H1 trong slide.')
                                            ->columnSpanFull(),
                                        Textarea::make('description')
                                            ->label('Mô tả (không bắt buộc)')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                        TextInput::make('button_label')
                                            ->label('Nhãn nút')
                                            ->maxLength(255)
                                            ->columnSpanFull(),
                                        TextInput::make('button_url')
                                            ->label('Liên kết nút')
                                            ->maxLength(2048)
                                            ->helperText('Có thể dùng URL đầy đủ hoặc liên kết trong trang như #lich-trinh.')
                                            ->columnSpanFull(),
                                        TextInput::make('alt_text')
                                            ->label('Mô tả ảnh cho SEO và trợ năng')
                                            ->maxLength(255)
                                            ->columnSpanFull(),
                                        Toggle::make('is_active')
                                            ->label('Hiển thị')
                                            ->default(true)
                                            ->columnSpanFull(),
                                        TextInput::make('sort_order')
                                            ->label('Thứ tự')
                                            ->numeric()
                                            ->default(0)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2)
                                    ->defaultItems(0)
                                    ->reorderable()
                                    ->orderColumn('sort_order')
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): string => filled($state['title'] ?? null) ? $state['title'] : 'Slide chỉ có ảnh')
                                    ->columnSpanFull(),
                            ]),
                    ]),
                Tab::make('Video & 4 chapter')->schema([
                    Section::make('Video sự kiện')
                        ->icon('heroicon-o-video-camera')
                        ->description('Ảnh poster luôn được dùng làm hình mặc định; khi có video, người xem có thể phát trực tiếp trên trang Lễ chuyển giao.')
                        ->schema([
                            CuratorPicker::make('video_poster_media_id')
                                ->label('Ảnh mặc định / poster video')
                                ->relationship('videoPosterMedia', 'id')
                                ->disk('public')
                                ->constrained()
                                ->acceptedFileTypes(['image/*'])
                                ->helperText('Nếu bỏ trống, hệ thống dùng ảnh banner của sự kiện.')
                                ->columnSpanFull(),
                            CuratorPicker::make('video_media_id')
                                ->label('Video tải lên')
                                ->relationship('videoMedia', 'id')
                                ->disk('public')
                                ->constrained()
                                ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/quicktime'])
                                ->helperText('Ưu tiên video tải lên. Nên dùng MP4/WebM tối ưu cho website.')
                                ->columnSpanFull(),
                            TextInput::make('video_url')
                                ->label('Hoặc URL video ngoài')
                                ->url()
                                ->maxLength(2048)
                                ->helperText('Dùng YouTube/Vimeo khi không chọn video tải lên.')
                                ->columnSpanFull(),
                            TextInput::make('registration_label')
                                ->label('Nhãn nút đăng ký')
                                ->default('Đăng ký ngay')
                                ->maxLength(255)
                                ->columnSpanFull(),
                            TextInput::make('registration_url')
                                ->label('Liên kết đăng ký')
                                ->default('#dang-ky')
                                ->maxLength(2048)
                                ->helperText('Có thể dùng URL biểu mẫu bên ngoài hoặc #dang-ky để cuộn xuống khu liên hệ.')
                                ->columnSpanFull(),
                        ])
                        ->columns(2),
                    Section::make('Bốn chapter')
                        ->icon('heroicon-o-user-group')
                        ->description('Mỗi chapter là một khối độc lập gồm nhận diện, ảnh poster và video tương ứng.')
                        ->schema([
                            Repeater::make('chapters')
                                ->relationship('chapters')
                                ->label('Chapter tham gia')
                                ->schema([
                                    TextInput::make('name')->label('Tên chapter')->required()->maxLength(255)->columnSpanFull(),
                                    TextInput::make('short_name')->label('Tên ngắn')->maxLength(48)->columnSpanFull(),
                                    TextInput::make('slug')->label('Slug')->required()->maxLength(255)->columnSpanFull(),
                                    Textarea::make('description')->label('Giới thiệu')->rows(3)->columnSpanFull(),
                                    CuratorPicker::make('logo_media_id')
                                        ->label('Logo')
                                        ->relationship('logoMedia', 'id')
                                        ->disk('public')
                                        ->constrained()
                                        ->acceptedFileTypes(['image/*'])
                                        ->columnSpanFull(),
                                    CuratorPicker::make('cover_media_id')
                                        ->label('Ảnh mặc định / poster video')
                                        ->relationship('coverMedia', 'id')
                                        ->disk('public')
                                        ->constrained()
                                        ->acceptedFileTypes(['image/*'])
                                        ->columnSpanFull(),
                                    CuratorPicker::make('video_media_id')
                                        ->label('Video chapter tải lên')
                                        ->relationship('videoMedia', 'id')
                                        ->disk('public')
                                        ->constrained()
                                        ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/quicktime'])
                                        ->columnSpanFull(),
                                    TextInput::make('video_url')
                                        ->label('Hoặc URL video ngoài')
                                        ->url()
                                        ->maxLength(2048)
                                        ->columnSpanFull(),
                                    Toggle::make('is_active')->label('Hiển thị')->default(true)->columnSpanFull(),
                                    TextInput::make('sort_order')->label('Thứ tự')->numeric()->default(0)->columnSpanFull(),
                                ])
                                ->columns(2)
                                ->defaultItems(0)
                                ->maxItems(4)
                                ->reorderable()
                                ->collapsible()
                                ->itemLabel(fn (array $state): ?string => $state['short_name'] ?? $state['name'] ?? 'Chapter')
                                ->columnSpanFull(),
                        ]),
                ]),
                Tab::make('Mục đích')->schema([
                    Repeater::make('purposes')->relationship('purposes')->label('4 mục đích của sự kiện')->schema([
                        TextInput::make('title')->label('Tiêu đề')->required()->columnSpanFull(),
                        Textarea::make('description')->label('Mô tả')->rows(2)->columnSpanFull(),
                        TextInput::make('icon')->label('Icon')->maxLength(80)->columnSpanFull(),
                        TextInput::make('sort_order')->label('Thứ tự')->numeric()->default(0)->columnSpanFull(),
                    ])->columns(2)->defaultItems(0)->columnSpanFull(),
                ]),
                Tab::make('Lịch trình')->schema([
                    Repeater::make('scheduleItems')->relationship('scheduleItems')->label('Lịch trình theo ngày')->schema([
                        TextInput::make('day_number')->label('Ngày số')->numeric()->required()->default(1)->columnSpanFull(),
                        TextInput::make('sort_order')->label('Thứ tự')->numeric()->default(0)->columnSpanFull(),
                        TextInput::make('starts_at')->label('Bắt đầu')->type('time')->columnSpanFull(),
                        TextInput::make('ends_at')->label('Kết thúc')->type('time')->columnSpanFull(),
                        TextInput::make('title')->label('Nội dung')->required()->columnSpanFull(),
                        TextInput::make('stage')->label('Vòng / chặng')->columnSpanFull(),
                        Textarea::make('description')->label('Mô tả')->rows(2)->columnSpanFull(),
                        Textarea::make('result')->label('Kết quả')->rows(2)->columnSpanFull(),
                        TextInput::make('location')->label('Khu vực')->columnSpanFull(),
                    ])->columns(2)->defaultItems(0)->columnSpanFull(),
                ]),
                Tab::make('Pickleball landing')
                    ->visible(fn ($get): bool => $get('type') === 'pickleball')
                    ->schema([
                        Section::make('Đếm ngược & RSVP')
                            ->icon('heroicon-o-clock')
                            ->description('Các nội dung hiển thị riêng trên landing page Pickleball.')
                            ->schema([
                                TextInput::make('settings.countdown_label')
                                    ->label('Nhãn đếm ngược')
                                    ->default('Đếm ngược đến giải đấu')
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                TextInput::make('settings.registration_title')
                                    ->label('Tiêu đề RSVP')
                                    ->default('Đăng ký tham gia')
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                Textarea::make('settings.registration_description')
                                    ->label('Mô tả RSVP')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                        Section::make('Cơ cấu giải thưởng')
                            ->icon('heroicon-o-trophy')
                            ->description('Chỉ nhập thông tin đã được Ban tổ chức xác nhận; có thể kéo thả để sắp xếp.')
                            ->schema([
                                TextInput::make('settings.prizes_title')
                                    ->label('Tiêu đề khu giải thưởng')
                                    ->default('Cơ cấu giải thưởng')
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                Textarea::make('settings.prizes_description')
                                    ->label('Mô tả chung')
                                    ->rows(2)
                                    ->columnSpanFull(),
                                Repeater::make('settings.prizes')
                                    ->label('Các hạng mục giải thưởng')
                                    ->schema([
                                        TextInput::make('title')->label('Tên hạng mục')->required()->maxLength(255)->columnSpanFull(),
                                        TextInput::make('value')->label('Giá trị / phần thưởng')->maxLength(255)->columnSpanFull(),
                                        Toggle::make('highlight')->label('Nhấn mạnh')->columnSpanFull(),
                                        Textarea::make('description')->label('Mô tả')->rows(2)->columnSpanFull(),
                                    ])
                                    ->columns(2)
                                    ->defaultItems(0)
                                    ->maxItems(8)
                                    ->reorderable()
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Hạng mục giải thưởng')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                        Section::make('Thể lệ giải đấu')
                            ->icon('heroicon-o-clipboard-document-check')
                            ->description('Nội dung chính thức về đối tượng, thể thức, luật thi đấu và lưu ý.')
                            ->schema([
                                TextInput::make('settings.rules_title')
                                    ->label('Tiêu đề thể lệ')
                                    ->default('Thể lệ giải đấu')
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                RichEditor::make('settings.rules')
                                    ->label('Nội dung thể lệ')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                    ]),
                Tab::make('Hoạt động & album ảnh')->schema([
                    Section::make('Hoạt động trong sự kiện')
                        ->icon('heroicon-o-rectangle-stack')
                        ->description('Mỗi hoạt động là một album ảnh động. Anh có thể thêm, đổi tên và sắp xếp tự do như “Trước lễ chuyển giao”, “Trong Gala Dinner” hoặc “After Party”.')
                        ->schema([
                            Repeater::make('activities')
                                ->relationship('activities')
                                ->label('Danh sách hoạt động / album')
                                ->schema([
                                    TextInput::make('title')->label('Tên hoạt động / album ảnh')->required()->maxLength(255)->columnSpanFull(),
                                    Select::make('type')
                                        ->label('Kiểu trình bày')
                                        ->options([
                                            'general' => 'Mặc định',
                                            'handover' => 'Lễ chuyển giao',
                                            'gala' => 'Gala',
                                            'pickleball' => 'Pickleball',
                                        ])
                                        ->default('general')
                                        ->required()
                                        ->columnSpanFull(),
                                    Textarea::make('description')->label('Mô tả')->rows(2)->columnSpanFull(),
                                    CuratorPicker::make('media_id')->label('Hình ảnh')->relationship('media', 'id')->disk('public')->constrained()->acceptedFileTypes(['image/*'])->columnSpanFull(),
                                    TextInput::make('link_url')->label('Liên kết')->url()->maxLength(2048)->columnSpanFull(),
                                    Toggle::make('is_active')->label('Hiển thị')->default(true)->columnSpanFull(),
                                ])
                                ->columns(2)
                                ->defaultItems(0)
                                ->reorderable()
                                ->orderColumn('sort_order')
                                ->collapsible()
                                ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Hoạt động mới')
                                ->columnSpanFull(),
                        ]),
                ]),
            ])->contained(false)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->label('Sự kiện')->searchable()->sortable()->wrap(),
            TextColumn::make('type')->label('Loại')->badge()->formatStateUsing(fn (string $state): string => $state === 'pickleball' ? 'Pickleball' : 'Lễ chuyển giao'),
            TextColumn::make('starts_at')->label('Bắt đầu')->dateTime('d/m/Y H:i')->sortable(),
            TextColumn::make('status')->label('Trạng thái')->badge(),
        ])->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageBniEvents::route('/')];
    }
}
