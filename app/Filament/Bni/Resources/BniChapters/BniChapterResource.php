<?php

namespace App\Filament\Bni\Resources\BniChapters;

use App\Filament\Bni\Resources\BniChapters\Pages\ManageBniChapters;
use App\Models\BniChapter;
use App\Support\Bni\BniPanelAccess;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BniChapterResource extends Resource
{
    protected static ?string $model = BniChapter::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Chapter';

    protected static ?int $navigationSort = 2;

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
            Section::make('Thông tin chapter')->icon('heroicon-o-user-group')->schema([
                TextInput::make('name')->label('Tên chapter')->required()->maxLength(255)->columnSpanFull(),
                TextInput::make('short_name')->label('Tên ngắn')->maxLength(48),
                TextInput::make('slug')->label('Slug')->required()->maxLength(255),
                Select::make('bni_event_id')->label('Thuộc sự kiện')->relationship('event', 'title')->searchable()->preload(),
                TextInput::make('sort_order')->label('Thứ tự')->numeric()->default(0),
                CuratorPicker::make('logo_media_id')->label('Logo')->relationship('logoMedia', 'id')->disk('public')->constrained()->acceptedFileTypes(['image/*'])->columnSpanFull(),
                CuratorPicker::make('cover_media_id')->label('Ảnh cover')->relationship('coverMedia', 'id')->disk('public')->constrained()->acceptedFileTypes(['image/*'])->columnSpanFull(),
                Textarea::make('description')->label('Giới thiệu')->rows(3)->columnSpanFull(),
                TextInput::make('contact_name')->label('Đầu mối'),
                TextInput::make('contact_email')->label('Email')->email(),
                TextInput::make('contact_phone')->label('Số điện thoại')->tel(),
                Toggle::make('is_active')->label('Hiển thị')->default(true)->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('Chapter')->searchable()->sortable(),
            TextColumn::make('event.title')->label('Sự kiện')->toggleable(),
            TextColumn::make('contact_name')->label('Đầu mối')->toggleable(),
            TextColumn::make('is_active')->label('Hiển thị')->badge()->formatStateUsing(fn (bool $state): string => $state ? 'Có' : 'Ẩn'),
        ])->defaultSort('sort_order')->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageBniChapters::route('/')];
    }
}
