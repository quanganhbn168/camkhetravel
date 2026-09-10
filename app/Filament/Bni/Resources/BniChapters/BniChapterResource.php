<?php

namespace App\Filament\Bni\Resources\BniChapters;

use App\Filament\Bni\Resources\BniChapters\Pages\CreateBniChapter;
use App\Filament\Bni\Resources\BniChapters\Pages\EditBniChapter;
use App\Filament\Bni\Resources\BniChapters\Pages\ListBniChapters;
use App\Filament\Forms\BniSeoImageField;
use App\Models\BniChapter;
use App\Support\Bni\BniMediaService;
use App\Support\Bni\BniPanelAccess;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BniChapterResource extends Resource
{
    protected static ?string $model = BniChapter::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Chapter';

    protected static ?string $modelLabel = 'chapter';

    protected static ?string $pluralModelLabel = 'Chapter';

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return 'Sự kiện BNI';
    }

    public static function canViewAny(): bool
    {
        return BniPanelAccess::canManageChapterContent();
    }

    public static function canCreate(): bool
    {
        return BniPanelAccess::canManageEverything();
    }

    public static function canDelete(Model $record): bool
    {
        return BniPanelAccess::canManageEverything();
    }

    public static function getEloquentQuery(): Builder
    {
        return BniPanelAccess::scopeChapter(parent::getEloquentQuery(), 'id')->withCount('contacts');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Thông tin chapter')
                ->icon('heroicon-o-user-group')
                ->description('Thêm, sửa, xóa Chapter tại đây. Danh sách nhiều đầu mối của từng Chapter được quản lý riêng trong mục Liên hệ Chapter.')
                ->schema([
                    TextInput::make('name')->label('Tên chapter')->required()->maxLength(255)->disabled(fn (): bool => ! BniPanelAccess::canManageEverything())->dehydrated()->columnSpanFull(),
                    TextInput::make('short_name')->label('Tên ngắn')->maxLength(48)->disabled(fn (): bool => ! BniPanelAccess::canManageEverything())->dehydrated()->columnSpanFull(),
                    Select::make('bni_event_id')->label('Thuộc sự kiện')->relationship('event', 'title')->searchable()->preload()->disabled(fn (): bool => ! BniPanelAccess::canManageEverything())->dehydrated()->columnSpanFull(),
                    TextInput::make('sort_order')->label('Thứ tự')->numeric()->default(0)->disabled(fn (): bool => ! BniPanelAccess::canManageEverything())->dehydrated()->columnSpanFull(),
                    BniSeoImageField::make(),
                    SpatieMediaLibraryFileUpload::make('logo')->label('Logo')->collection('logo')->conversion(BniMediaService::WEBP_CONVERSION)->disk('public')->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])->disabled(fn (): bool => ! BniPanelAccess::canManageEverything())->columnSpanFull(),
                    SpatieMediaLibraryFileUpload::make('cover')->label('Ảnh cover')->collection('cover')->conversion(BniMediaService::WEBP_CONVERSION)->disk('public')->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])->helperText('Ảnh dùng cho thẻ và nội dung riêng của Chapter. Video sự kiện có ảnh cover riêng trong mục Video sự kiện.')->disabled(fn (): bool => ! BniPanelAccess::canManageEverything())->columnSpanFull(),
                    SpatieMediaLibraryFileUpload::make('video')->label('Video chapter')->collection('video')->disk('public')->acceptedFileTypes(['video/mp4', 'video/webm', 'video/quicktime'])->disabled(fn (): bool => ! BniPanelAccess::canManageEverything())->columnSpanFull(),
                    TextInput::make('video_url')->label('Hoặc URL video ngoài')->url()->maxLength(2048)->disabled(fn (): bool => ! BniPanelAccess::canManageEverything())->dehydrated()->columnSpanFull(),
                    Textarea::make('description')->label('Giới thiệu')->rows(3)->disabled(fn (): bool => ! BniPanelAccess::canManageEverything())->dehydrated()->columnSpanFull(),
                    Toggle::make('is_active')->label('Hiển thị')->default(true)->disabled(fn (): bool => ! BniPanelAccess::canManageEverything())->dehydrated()->columnSpanFull(),
                ])->columns(2)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('Chapter')->searchable()->sortable(),
            TextColumn::make('event.title')->label('Sự kiện')->toggleable(),
            TextColumn::make('contacts_count')->label('Đầu mối')->sortable(),
            ToggleColumn::make('is_active')->label('Hiển thị')->disabled(fn (): bool => ! BniPanelAccess::canManageEverything()),
        ])->defaultSort('sort_order')->reorderable('sort_order')->recordActions([
            EditAction::make(),
            DeleteAction::make()->slideOver()->visible(fn (): bool => BniPanelAccess::canManageEverything()),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBniChapters::route('/'),
            'create' => CreateBniChapter::route('/create'),
            'edit' => EditBniChapter::route('/{record}/edit'),
        ];
    }
}
