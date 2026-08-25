<?php

namespace App\Filament\Bni\Resources\BniEvents;

use App\Filament\Bni\Resources\BniEvents\Pages\ManageBniEvents;
use App\Models\BniEvent;
use App\Support\Bni\BniPanelAccess;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
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
                        TextInput::make('slug')->label('Slug')->required()->maxLength(255),
                        Select::make('type')->label('Loại')->options(['handover' => 'Lễ chuyển giao', 'pickleball' => 'Pickleball'])->required(),
                        TextInput::make('kicker')->label('Dòng nhãn')->maxLength(255),
                        Select::make('status')->label('Trạng thái')->options(['draft' => 'Bản nháp', 'published' => 'Đã xuất bản'])->required(),
                        CuratorPicker::make('hero_media_id')->label('Ảnh banner')->relationship('heroMedia', 'id')->disk('public')->constrained()->acceptedFileTypes(['image/*'])->columnSpanFull(),
                        TextInput::make('video_url')->label('Video giới thiệu')->url()->maxLength(2048)->columnSpanFull(),
                        Textarea::make('summary')->label('Mô tả ngắn')->rows(3)->columnSpanFull(),
                        RichEditor::make('content')->label('Nội dung')->columnSpanFull(),
                    ])->columns(2),
                    Section::make('Thời gian & liên hệ')->icon('heroicon-o-clock')->schema([
                        DateTimePicker::make('starts_at')->label('Bắt đầu'),
                        DateTimePicker::make('ends_at')->label('Kết thúc'),
                        TextInput::make('venue')->label('Địa điểm'),
                        TextInput::make('address')->label('Địa chỉ'),
                        TextInput::make('contact_name')->label('Đầu mối liên hệ'),
                        TextInput::make('contact_phone')->label('Số điện thoại')->tel(),
                        TextInput::make('contact_email')->label('Email')->email(),
                        Toggle::make('is_featured')->label('Sự kiện nổi bật')->columnSpanFull(),
                    ])->columns(2),
                ]),
                Tab::make('Mục đích')->schema([
                    Repeater::make('purposes')->relationship('purposes')->label('4 mục đích của sự kiện')->schema([
                        TextInput::make('title')->label('Tiêu đề')->required()->columnSpanFull(),
                        Textarea::make('description')->label('Mô tả')->rows(2)->columnSpanFull(),
                        TextInput::make('icon')->label('Icon')->maxLength(80),
                        TextInput::make('sort_order')->label('Thứ tự')->numeric()->default(0),
                    ])->columns(2)->defaultItems(0)->columnSpanFull(),
                ]),
                Tab::make('Lịch trình')->schema([
                    Repeater::make('scheduleItems')->relationship('scheduleItems')->label('Lịch trình theo ngày')->schema([
                        TextInput::make('day_number')->label('Ngày số')->numeric()->required()->default(1),
                        TextInput::make('sort_order')->label('Thứ tự')->numeric()->default(0),
                        TextInput::make('starts_at')->label('Bắt đầu')->type('time'),
                        TextInput::make('ends_at')->label('Kết thúc')->type('time'),
                        TextInput::make('title')->label('Nội dung')->required()->columnSpanFull(),
                        TextInput::make('stage')->label('Vòng / chặng')->columnSpanFull(),
                        Textarea::make('description')->label('Mô tả')->rows(2)->columnSpanFull(),
                        Textarea::make('result')->label('Kết quả')->rows(2)->columnSpanFull(),
                        TextInput::make('location')->label('Khu vực')->columnSpanFull(),
                    ])->columns(2)->defaultItems(0)->columnSpanFull(),
                ]),
                Tab::make('Hoạt động')->schema([
                    Repeater::make('activities')->relationship('activities')->label('Hoạt động đặc biệt')->schema([
                        Select::make('type')->label('Loại')->options(['handover' => 'Lễ chuyển giao', 'gala' => 'Gala & sinh nhật', 'pickleball' => 'Pickleball'])->required(),
                        TextInput::make('sort_order')->label('Thứ tự')->numeric()->default(0),
                        TextInput::make('title')->label('Tiêu đề')->required()->columnSpanFull(),
                        Textarea::make('description')->label('Mô tả')->rows(2)->columnSpanFull(),
                        CuratorPicker::make('media_id')->label('Hình ảnh')->relationship('media', 'id')->disk('public')->constrained()->acceptedFileTypes(['image/*'])->columnSpanFull(),
                        TextInput::make('link_url')->label('Liên kết')->url()->maxLength(2048)->columnSpanFull(),
                        Toggle::make('is_active')->label('Hiển thị')->default(true)->columnSpanFull(),
                    ])->columns(2)->defaultItems(0)->columnSpanFull(),
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
