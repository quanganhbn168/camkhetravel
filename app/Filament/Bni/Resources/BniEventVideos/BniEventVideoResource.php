<?php

namespace App\Filament\Bni\Resources\BniEventVideos;

use App\Filament\Bni\Resources\BniEventVideos\Pages\CreateBniEventVideo;
use App\Filament\Bni\Resources\BniEventVideos\Pages\EditBniEventVideo;
use App\Filament\Bni\Resources\BniEventVideos\Pages\ListBniEventVideos;
use App\Models\BniEventVideo;
use App\Support\Bni\BniMediaService;
use App\Support\Bni\BniPanelAccess;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BniEventVideoResource extends Resource
{
    protected static ?string $model = BniEventVideo::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-video-camera';

    protected static ?string $navigationLabel = 'Video sự kiện';

    protected static ?string $modelLabel = 'video sự kiện';

    protected static ?string $pluralModelLabel = 'Video sự kiện';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return 'Lễ chuyển giao';
    }

    public static function canViewAny(): bool
    {
        return BniPanelAccess::canManageEverything();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Video giới thiệu sự kiện')
                ->icon('heroicon-o-video-camera')
                ->description('Mỗi sự kiện có một cấu hình video riêng. Video tải lên được ưu tiên trước URL YouTube hoặc Vimeo.')
                ->schema([
                    Select::make('bni_event_id')
                        ->label('Sự kiện')
                        ->relationship('event', 'title')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->searchable()
                        ->preload()
                        ->columnSpanFull(),
                    SpatieMediaLibraryFileUpload::make('poster')
                        ->label('Ảnh poster')
                        ->collection('poster')
                        ->conversion(BniMediaService::WEBP_CONVERSION)
                        ->disk('public')
                        ->image()
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->helperText('Nếu để trống, trang sẽ dùng ảnh banner của sự kiện.')
                        ->columnSpanFull(),
                    SpatieMediaLibraryFileUpload::make('video')
                        ->label('Video tải lên')
                        ->collection('video')
                        ->disk('public')
                        ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/quicktime'])
                        ->columnSpanFull(),
                    TextInput::make('external_url')
                        ->label('Hoặc URL YouTube/Vimeo')
                        ->url()
                        ->maxLength(2048)
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->columnSpanFull(),
            Section::make('Nút đăng ký')
                ->icon('heroicon-o-cursor-arrow-rays')
                ->schema([
                    TextInput::make('registration_label')->label('Nhãn nút')->default('Đăng ký ngay')->maxLength(255)->columnSpanFull(),
                    TextInput::make('registration_url')
                        ->label('Liên kết đăng ký')
                        ->default('/le-chuyen-giao/dang-ky')
                        ->maxLength(2048)
                        ->helperText('Có thể dùng đường dẫn nội bộ hoặc URL biểu mẫu bên ngoài.')
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('event.title')->label('Sự kiện')->searchable()->sortable(),
                TextColumn::make('external_url')->label('Video ngoài')->limit(45)->placeholder('Dùng video tải lên'),
                TextColumn::make('registration_label')->label('Nút đăng ký')->placeholder('Đăng ký ngay'),
                TextColumn::make('updated_at')->label('Cập nhật')->since()->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()->slideOver(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBniEventVideos::route('/'),
            'create' => CreateBniEventVideo::route('/create'),
            'edit' => EditBniEventVideo::route('/{record}/edit'),
        ];
    }
}
