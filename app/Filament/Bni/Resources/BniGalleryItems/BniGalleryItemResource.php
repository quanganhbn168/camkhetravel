<?php

namespace App\Filament\Bni\Resources\BniGalleryItems;

use App\Filament\Bni\Resources\BniGalleryItems\Pages\ManageBniGalleryItems;
use App\Models\BniGalleryItem;
use App\Support\Bni\BniPanelAccess;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Curator\Components\Tables\CuratorColumn;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BniGalleryItemResource extends Resource
{
    protected static ?string $model = BniGalleryItem::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationLabel = 'Thư viện ảnh';

    protected static ?int $navigationSort = 6;

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
            Section::make('Ảnh sự kiện')->icon('heroicon-o-photo')->schema([
                Select::make('bni_event_id')->label('Sự kiện')->relationship('event', 'title')->searchable()->preload(),
                Select::make('bni_chapter_id')->label('Chapter')->relationship('chapter', 'name')->searchable()->preload(),
                Select::make('group')->label('Nhóm hiển thị')->options(['event' => 'Theo sự kiện', 'chapter' => 'Theo chapter'])->required()->default('event'),
                TextInput::make('sort_order')->label('Thứ tự')->numeric()->default(0),
                TextInput::make('title')->label('Tiêu đề')->maxLength(255)->columnSpanFull(),
                TextInput::make('caption')->label('Chú thích')->maxLength(255)->columnSpanFull(),
                CuratorPicker::make('media_id')->label('Hình ảnh')->relationship('media', 'id')->disk('public')->constrained()->acceptedFileTypes(['image/*'])->required()->columnSpanFull(),
                Toggle::make('is_active')->label('Hiển thị')->default(true)->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            CuratorColumn::make('media')->label('Ảnh')->square(),
            TextColumn::make('title')->label('Tiêu đề')->searchable()->wrap(),
            TextColumn::make('group')->label('Nhóm')->badge(),
            TextColumn::make('event.title')->label('Sự kiện')->toggleable(),
            TextColumn::make('chapter.short_name')->label('Chapter')->badge()->toggleable(),
        ])->defaultSort('sort_order')->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageBniGalleryItems::route('/')];
    }
}
