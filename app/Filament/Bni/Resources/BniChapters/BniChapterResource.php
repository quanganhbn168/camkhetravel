<?php

namespace App\Filament\Bni\Resources\BniChapters;

use App\Filament\Bni\Resources\BniChapters\Pages\CreateBniChapter;
use App\Filament\Bni\Resources\BniChapters\Pages\EditBniChapter;
use App\Filament\Bni\Resources\BniChapters\Pages\ListBniChapters;
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
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BniChapterResource extends Resource
{
    protected static ?string $model = BniChapter::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Chapter';

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return 'Lễ chuyển giao';
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
        return BniPanelAccess::scopeChapter(parent::getEloquentQuery(), 'id');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Thông tin chapter')->icon('heroicon-o-user-group')->schema([
                TextInput::make('name')->label('Tên chapter')->required()->maxLength(255)->disabled(fn (): bool => ! BniPanelAccess::canManageEverything())->dehydrated()->columnSpanFull(),
                TextInput::make('short_name')->label('Tên ngắn')->maxLength(48)->disabled(fn (): bool => ! BniPanelAccess::canManageEverything())->dehydrated()->columnSpanFull(),
                Select::make('bni_event_id')->label('Thuộc sự kiện')->relationship('event', 'title')->searchable()->preload()->disabled(fn (): bool => ! BniPanelAccess::canManageEverything())->dehydrated()->columnSpanFull(),
                TextInput::make('sort_order')->label('Thứ tự')->numeric()->default(0)->disabled(fn (): bool => ! BniPanelAccess::canManageEverything())->dehydrated()->columnSpanFull(),
                SpatieMediaLibraryFileUpload::make('logo')->label('Logo')->collection('logo')->conversion(BniMediaService::WEBP_CONVERSION)->disk('public')->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])->disabled(fn (): bool => ! BniPanelAccess::canManageEverything())->columnSpanFull(),
                SpatieMediaLibraryFileUpload::make('cover')->label('Ảnh cover')->collection('cover')->conversion(BniMediaService::WEBP_CONVERSION)->disk('public')->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])->disabled(fn (): bool => ! BniPanelAccess::canManageEverything())->columnSpanFull(),
                SpatieMediaLibraryFileUpload::make('video')->label('Video chapter')->collection('video')->disk('public')->acceptedFileTypes(['video/mp4', 'video/webm', 'video/quicktime'])->disabled(fn (): bool => ! BniPanelAccess::canManageEverything())->columnSpanFull(),
                TextInput::make('video_url')->label('Hoặc URL video ngoài')->url()->maxLength(2048)->disabled(fn (): bool => ! BniPanelAccess::canManageEverything())->dehydrated()->columnSpanFull(),
                Textarea::make('description')->label('Giới thiệu')->rows(3)->disabled(fn (): bool => ! BniPanelAccess::canManageEverything())->dehydrated()->columnSpanFull(),
                TextInput::make('contact_name')->label('Đầu mối liên hệ trên thư mời')->helperText('Dữ liệu riêng của chapter; đây là người khách mời liên hệ khi cần hỗ trợ.')->columnSpanFull(),
                TextInput::make('contact_email')->label('Email liên hệ trên thư mời')->email()->columnSpanFull(),
                TextInput::make('contact_phone')->label('Số điện thoại liên hệ trên thư mời')->tel()->columnSpanFull(),
                Toggle::make('is_active')->label('Hiển thị')->default(true)->disabled(fn (): bool => ! BniPanelAccess::canManageEverything())->dehydrated()->columnSpanFull(),
            ])->columns(2)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('Chapter')->searchable()->sortable(),
            TextColumn::make('event.title')->label('Sự kiện')->toggleable(),
            TextColumn::make('contact_name')->label('Đầu mối')->toggleable(),
            TextColumn::make('is_active')->label('Hiển thị')->badge()->formatStateUsing(fn (bool $state): string => $state ? 'Có' : 'Ẩn'),
        ])->defaultSort('sort_order')->recordActions([
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
