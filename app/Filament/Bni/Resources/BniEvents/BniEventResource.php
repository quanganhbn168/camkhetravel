<?php

namespace App\Filament\Bni\Resources\BniEvents;

use App\Filament\Bni\Resources\BniEvents\Pages\CreateBniEvent;
use App\Filament\Bni\Resources\BniEvents\Pages\EditBniEvent;
use App\Filament\Bni\Resources\BniEvents\Pages\ListBniEvents;
use App\Models\BniEvent;
use App\Support\Bni\BniMediaService;
use App\Support\Bni\BniPanelAccess;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
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
            Section::make('Thông tin sự kiện')
                ->icon('heroicon-o-calendar-days')
                ->description('Chỉ quản lý thông tin chung của sự kiện. Slide, video, chapter và lịch trình có khu quản trị riêng ở menu bên trái.')
                ->schema([
                    TextInput::make('title')->label('Tên sự kiện')->required()->maxLength(255)->columnSpanFull(),
                    Select::make('type')->label('Loại sự kiện')->options([
                        'handover' => 'Lễ chuyển giao',
                        'pickleball' => 'Pickleball',
                    ])->required()->columnSpanFull(),
                    TextInput::make('kicker')->label('Dòng nhãn')->maxLength(255)->columnSpanFull(),
                    SpatieMediaLibraryFileUpload::make('hero')
                        ->label('Ảnh banner')
                        ->collection('hero')
                        ->conversion(BniMediaService::WEBP_CONVERSION)
                        ->disk('public')
                        ->image()
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->columnSpanFull(),
                    Textarea::make('summary')->label('Mô tả ngắn')->rows(3)->columnSpanFull(),
                    RichEditor::make('content')->label('Nội dung giới thiệu')->columnSpanFull(),
                ])
                ->columns(2)
                ->columnSpanFull(),
            Section::make('Thời gian, địa điểm & liên hệ')
                ->icon('heroicon-o-clock')
                ->description('Thông tin chung hiển thị trên trang sự kiện. Đầu mối của từng thư mời vẫn được quản lý theo chapter.')
                ->schema([
                    DateTimePicker::make('starts_at')->label('Bắt đầu')->columnSpanFull(),
                    DateTimePicker::make('ends_at')->label('Kết thúc')->columnSpanFull(),
                    TextInput::make('venue')->label('Tên địa điểm')->columnSpanFull(),
                    TextInput::make('address')->label('Địa chỉ')->columnSpanFull(),
                    TextInput::make('directions_url')->label('Link chỉ đường')->url()->maxLength(2048)->columnSpanFull(),
                    TextInput::make('contact_name')->label('Đầu mối sự kiện')->columnSpanFull(),
                    TextInput::make('contact_phone')->label('Số điện thoại')->tel()->columnSpanFull(),
                    TextInput::make('contact_email')->label('Email')->email()->columnSpanFull(),
                    Select::make('status')->label('Trạng thái')->options([
                        'draft' => 'Bản nháp',
                        'published' => 'Đã xuất bản',
                    ])->required()->default('draft')->columnSpanFull(),
                    Toggle::make('is_featured')->label('Sự kiện nổi bật')->columnSpanFull(),
                ])
                ->columns(2)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->label('Sự kiện')->searchable()->sortable()->wrap(),
            TextColumn::make('type')->label('Loại')->badge()->formatStateUsing(fn (string $state): string => $state === 'pickleball' ? 'Pickleball' : 'Lễ chuyển giao'),
            TextColumn::make('starts_at')->label('Bắt đầu')->dateTime('d/m/Y H:i')->sortable(),
            TextColumn::make('status')->label('Trạng thái')->badge(),
        ])->recordActions([
            EditAction::make(),
            DeleteAction::make()->slideOver(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBniEvents::route('/'),
            'create' => CreateBniEvent::route('/create'),
            'edit' => EditBniEvent::route('/{record}/edit'),
        ];
    }
}
