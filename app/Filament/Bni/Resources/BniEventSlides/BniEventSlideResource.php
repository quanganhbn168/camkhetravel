<?php

namespace App\Filament\Bni\Resources\BniEventSlides;

use App\Filament\Bni\Resources\BniEventSlides\Pages\CreateBniEventSlide;
use App\Filament\Bni\Resources\BniEventSlides\Pages\EditBniEventSlide;
use App\Filament\Bni\Resources\BniEventSlides\Pages\ListBniEventSlides;
use App\Models\BniEvent;
use App\Models\BniEventSlide;
use App\Support\Bni\BniMediaService;
use App\Support\Bni\BniPanelAccess;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Validation\ValidationException;

class BniEventSlideResource extends Resource
{
    protected static ?string $model = BniEventSlide::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationLabel = 'Slide đầu trang';

    protected static ?string $modelLabel = 'slide đầu trang';

    protected static ?string $pluralModelLabel = 'Slide đầu trang';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return 'Sự kiện BNI';
    }

    public static function canViewAny(): bool
    {
        return BniPanelAccess::canManageEverything();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Slide đầu trang')
                ->icon('heroicon-o-photo')
                ->description('Mỗi bản ghi là một slide toàn chiều ngang. Có thể chỉ dùng ảnh, tải video lên hoặc gắn URL YouTube/Vimeo; ảnh luôn là cover dự phòng.')
                ->schema([
                    SpatieMediaLibraryFileUpload::make('image')
                        ->label('Ảnh slide / ảnh cover video')
                        ->collection('image')
                        ->conversion(BniMediaService::WEBP_CONVERSION)
                        ->disk('public')
                        ->image()
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->required()
                        ->columnSpanFull(),
                    SpatieMediaLibraryFileUpload::make('video')
                        ->label('Video tải lên (không bắt buộc)')
                        ->collection('video')
                        ->disk('public')
                        ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/quicktime'])
                        ->helperText('Nếu có video tải lên, slide sẽ tự phát không tiếng. Video tải lên được ưu tiên trước URL bên dưới.')
                        ->columnSpanFull(),
                    TextInput::make('video_url')
                        ->label('Hoặc URL YouTube/Vimeo')
                        ->url()
                        ->maxLength(2048)
                        ->helperText('Để trống cả hai trường video nếu slide này chỉ hiển thị ảnh.')
                        ->columnSpanFull(),
                    TextInput::make('title')
                        ->label('Tên gợi nhớ')
                        ->helperText('Chỉ dùng để nhận biết trong quản trị, không hiển thị trên ảnh slide.')
                        ->maxLength(255)
                        ->columnSpanFull(),
                    TextInput::make('alt_text')->label('Mô tả ảnh')->maxLength(255)->columnSpanFull(),
                    Toggle::make('is_active')->label('Hiển thị')->default(true)->columnSpanFull(),
                ])
                ->columns(2)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('image')->label('Ảnh')->collection('image')->conversion(BniMediaService::WEBP_CONVERSION)->square(),
                TextColumn::make('title')->label('Tên gợi nhớ')->placeholder('Chưa đặt tên')->searchable()->wrap(),
                TextColumn::make('content_type')
                    ->label('Kiểu nội dung')
                    ->getStateUsing(fn (BniEventSlide $record): string => $record->hasMedia('video') ? 'Video tải lên' : ($record->video_url ? 'YouTube / Vimeo' : 'Ảnh'))
                    ->badge(),
                ToggleColumn::make('is_active')->label('Hiển thị'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()->slideOver(),
            ]);
    }

    /** @param array<string, mixed> $data */
    public static function prepareCreateData(array $data): array
    {
        $event = BniEvent::query()
            ->where('type', 'handover')
            ->orderByRaw("case when status = 'published' then 0 else 1 end")
            ->orderByDesc('is_featured')
            ->orderByDesc('starts_at')
            ->first();

        if (! $event) {
            throw ValidationException::withMessages([
                'data.image' => 'Cần tạo thông tin Lễ chuyển giao trước khi thêm slide.',
            ]);
        }

        $data['bni_event_id'] = $event->getKey();

        return $data;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBniEventSlides::route('/'),
            'create' => CreateBniEventSlide::route('/create'),
            'edit' => EditBniEventSlide::route('/{record}/edit'),
        ];
    }
}
